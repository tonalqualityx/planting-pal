<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security

function planting_pal_home($lat=NULL, $lon=NULL){
	ob_start(); ?>
    <body class="location-body ppl-green-bg">
        <!-- <div class="desktopWarning">
            <p class="desktopWarning-p">This site is optimized for mobile phones in portrait layout.</p><i class="material-icons d-block portrait-only">screen_lock_portrait</i></div> -->
        <div class="container">
            <div class='zip-search-container'>
                <div class="row wizard-start">
                    <div class="col"><img src="<?php echo INDPPL_ROOT_URL ?>assets/img/wizard-location.png"></div>
                </div>
                <div class="row search-form">
                    <div class="col" id='app-location-submitter'>
                        <form action="<?php site_url(); ?>" method="post">
                        <div class='fix-position-geo'>
                            <input class="form-control rounded-input4" id='zip-for-location' type="text" name="zip" placeholder="Zipcode">
                            <img src="<?php echo home_url() . '/wp-content/plugins/planting-pal/assets/img/gps.png'; ?>" id="location-icon">
                        </div>
                        <input type="image" src="<?php echo INDPPL_ROOT_URL ?>assets/img/enter-geo.png" alt="Submit" border="0" class="geo-submit">
                    </form>
                </div>
            </div>
        </div>
       
    <?php
    if(isset($_POST['lat'])){
		$lat = $_POST['lat'];
        $lon = $_POST['lon'];
        $zip_array = geofind($lat, $lon);
    }else if(isset($_POST['zip'])){
		$zip_array = geozip($_POST['zip']);
    }
    $top = ob_get_clean();
    ob_start();
    // var_dump($zip_array);
    ?><div class='store-list-container'> <?php
  
    if($zip_array){
		
		$args = array(
			'post_type' => 'store',
            'meta_query' => array(
				array(
					'key' => 'wpcf-zip',
                    'value'   => $zip_array,
                    'compare' => 'IN',
					
                )
            )
        );
        $the_query = new WP_Query( $args );
        // The Loop
        if ( $the_query->have_posts() ) {
            ?>
        <div class='flex-left-justify'><?php
        $i =0;
        while ( $the_query->have_posts() ) {
            // var_dump('special');
            $the_query->the_post();
            $id = get_the_ID();
            // var_dump(get_post_meta($id));

            $add = get_post_meta($id, 'wpcf-address1');
            $city = get_post_meta($id, 'wpcf-city');
            $state = get_post_meta($id, 'wpcf-state');
            $zip = get_post_meta($id, 'wpcf-zip');
            $phone = get_post_meta($id, 'wpcf-phone');
            $url = get_post_meta($id, 'wpcf-weburl');
            $is_pro = get_post_meta($id, 'wpcf-ispro');
            $title = get_the_title();
            ?>
            <h3 class='results-store'><a href='<?php echo get_permalink($id); ?>'><?php echo $title; ?></a></h3>
            <p class='store-list-text'><?php echo $add[0]; ?></p>
            <p class='store-list-text'><?php echo $city[0] . ", " . $state[0] . " " . $zip[0]; ?></p>
            <?php

            if($is_pro[0] == 1){
                ?>
                <p class='store-list-text'><a href=tel:<?php echo $phone[0]; ?>><?php echo phone_number_format($phone[0]); ?></a> <a href='<?php echo $url[0]; ?>' target='_blank'>Website</a></p>
                <?php
            }
        }
        ?></div><?php
        
        /* Restore original Post Data */
        wp_reset_postdata();
    } else {
        ?><p>No Stores in your area</p><?php
    }
        
    }
    ?></div><?php
    if(isset($_POST['lat'])){
		$get_store = ob_get_clean();
        return $get_store;
    }
	
    ?>
        <script src="<?php echo INDPPL_ROOT_URL ?>assets/bootstrap/js/bootstrap.min.js"></script>
    </body>
    <!-- </html> -->
    <?php
      
$return = ob_get_clean();
    return $top.$return;
}
add_shortcode('planting-pal-home', 'planting_pal_home');

function indppl_import() {
    $handle = fopen(INDPPL_ROOT_PATH . "/products.csv", "r");
    $prods = array();

    $containers = get_posts(array('post_type' => 'container', 'order' => 'ASC', 'posts_per_page' => -1));

    // foreach($containers as $cont) {
    //     echo "<p>{$cont->ID} {$cont->post_title}</p>";
    // }

    while (($data = fgetcsv($handle, 0, ";")) !== false) {

        $prods[$data[0]][$data[1]]['packages'][] = array('size' => $data[4], 'unit' => $data[5]);
        $prods[$data[0]][$data[1]]['5cups'] = $data[6];
        $prods[$data[0]][$data[1]]['dry'] = $data[7];

        //setup the instructions
        $i = 1;
        $a = 11;
        while ($i <= 4) {
            // if($data[$a] != ''){
            $prods[$data[0]][$data[1]]['instructions']['ground']['step ' . $i] = $data[$a];
            // }
            $i++;
            if ($a == 14) {
                $$a = 11;
            } else {
                $a++;
            }
        }

        $prods[$data[0]][$data[1]]['instructions']['pots-beds'] = array('fill-text' => $data[15], 'blend-text' => $data[16], 'surface-text' => $data[19]);
        $prods[$data[0]][$data[1]]['instructions']['beds'] = array('fill' => $data[25], 'blend' => $data[26], 'surface' => $data[27], 'text' => $data[28]);

        $prods[$data[0]][$data[1]]['apprates']['pots-beds']['additive'] = array('dilution' => $data[17], 'unit' => $data[18]);
        $prods[$data[0]][$data[1]]['apprates']['pots-beds']['surface'] = array('dilution' => $data[20], 'unit' => $data[21]);
        $prods[$data[0]][$data[1]]['apprates']['pots-beds']['each'] = array($data[22], $data[23], $data[24]);

        $a = 29;

        foreach ($containers as $cont) {
            $prods[$data[0]][$data[1]]['apprates']['ground'][$cont->post_title] = array('id' => $cont->ID, 'qty' => $data[$a], 'unit' => $data[$a + 1]);
            $a += 2;
        }

    }

    $stores = get_posts(array('post_type' => 'product'));
    foreach ($stores as $store) {
        $meta = get_post_meta($store->ID);
        // var_dump($meta);
    }

    foreach ($prods as $key => $val) {
        echo "<h2>{$key}</h2>";
        foreach ($val as $k => $product) {
            if ($product['dry'] == 'y') {
                $dry = 'dry';
            } else {
                $dry = 'wet';
            }
            $args = array(
                'post_title' => $k,
                'post_status' => 'publish',
                'post_type' => 'product',
                // 'tax_input' => array('brand' => strtolower($key) ),
                'meta_input' => array(
                    'wpcf-default' => '1',
                    'wpcf-5cups' => $product['5cups'],
                    'wpcf-dryliquid' => $dry,
                    'wpcf-step-1-instructions' => $product['instructions']['ground']['step 1'],
                    'wpcf-step-2-instructions' => $product['instructions']['ground']['step 2'],
                    'wpcf-step-3-instructions' => $product['instructions']['ground']['step 3'],
                    'wpcf-step-4-instructions' => $product['instructions']['ground']['step 4'],
                    'wpcf-blended-filler-instructions' => $product['instructions']['pots-beds']['fill-text'],
                    'wpcf-blended-additive-instructions' => $product['instructions']['pots-beds']['blend-text'],
                    'wpcf-surface-text' => $product['instructions']['pots-beds']['surface-text'],
                    'wpcf-blended-additive-dilution' => $product['apprates']['pots-beds']['additive']['dilution'],
                    'wpcf-blended-additive-unit' => $product['apprates']['pots-beds']['additive']['unit'],
                    'wpcf-surface-dilution' => $product['apprates']['pots-beds']['surface']['dilution'],
                    'wpcf-surface-unit' => $product['apprates']['pots-beds']['surface']['unit'],
                    'wpcf-each-small' => $product['apprates']['pots-beds']['each'][0],
                    'wpcf-each-medium' => $product['apprates']['pots-beds']['each'][1],
                    'wpcf-each-large' => $product['apprates']['pots-beds']['each'][2],
                    'wpcf-raised-bed-tex' => $product['instructions']['beds']['text'],
                    'wpcf-use-blended-filler' => $product['instructions']['beds'][0],
                    'wpcf-use-blended-additive' => $product['instructions']['beds'][1],
                    'wpcf-use-surface' => $product['instructions']['beds'][2],
                ),

            );
            $inserted_product = wp_insert_post($args);
            wp_set_object_terms($inserted_product, $key, 'brand');
            // var_dump($inserted_product);
            echo "<h3>{$k}</h3>";
            // echo "<h4>Details</h4>";
            // echo "<ul><li>5 Cups: {$product['5cups']}</li><li>Dry: {$product['dry']}</li></ul>";
            // echo "<h4>Package Sizes</h4>";
            // echo "<ul>";
            foreach ($product['packages'] as $pack) {
                $packargs = array(
                    'post_title' => $key . " " . $k . " " . $pack['size'] . $pack['unit'],
                    'post_type' => 'package',
                    'post_status' => 'publish',
                    'meta_input' => array(
                        'wpcf-size' => $pack['size'],
                        'wpcf-unit' => $pack['unit'],
                        'wpcf-default-package' => '1',
                    ),
                );

                $inserted_pack = wp_insert_post($packargs);
                toolset_connect_posts('product-package', $inserted_product, $inserted_pack);

                echo "<p>" . $key . " " . $k . " " . $pack['size'] . $pack['unit'] . "</p>";

            }
            // echo "Before <br />";
            foreach ($containers as $cont) {
                // echo "<p>looping</p>";
                $cont_id = $cont->ID;
                // var_dump($cont_id);
                // var_dump($inserted_product);
                $connection = toolset_connect_posts('default-apprate', $inserted_product, $cont_id);
                // var_dump($product['apprates']['ground'][$cont->post_title]['qty']);
                $cpt_id = $connection['intermediary_post'];
                echo "cpt " . $cpt_id;
                // if($cpt_id != 0){
                $test = update_post_meta($cpt_id, 'wpcf-apprate-qty', $product['apprates']['ground'][$cont->post_title]['qty']);
                $test2 = update_post_meta($cpt_id, 'wpcf-apprate-unit-holdover', $product['apprates']['ground'][$cont->post_title]['unit']);

                
                // }

            }
            // echo "</ul>";
        }
    }
}

// Deactivated so this shortcode won't accidentally trigger and screw everything...
// If we need to import data from a CSV (matching the exact format of products.csv) reactivate
// and point it at the appropriate file, then simply load a page with this shortcode on it.
// This is a SUPER HEAVY function and generally times out if we try to run more than 7 rows at a time

// add_shortcode('import-products', 'indppl_import');

function pp_store_management(){
    $store_id = '';
    $user_id = get_current_user_id();
    if(isset($_GET['store-id'])){
        $author_id = get_post_field('post_author', intval($_GET['store-id']));
        if($user_id == $author_id || current_user_can('administrator')){
            $store_id = intval(htmlspecialchars($_GET['store-id']));
        }else{
            ?>
            <h3 class='color-red'>Sorry, but you must be logged in to access this store. Further Options below.</h3>
            <?php
        }
    }
    if(isset($_POST['submit'])){
        if($store_id == null){$store_id = 0;}
        if(isset($_POST['store-id'])){
            $store_id = intval($_POST['store-id']);
        }
        indppl_save_post($store_id);
    }
    
    
    if($store_id > 0){
        ob_start();
        $setup = get_post_meta($store_id, 'wpcf-issetup', true);
        if($setup){
            ?>
            <p>Your store is Live. To make your store private hit the button below.</p>
            <a href='#' class='store-go-live-btn button button-primary' data-id='<?php echo $store_id; ?>'>Make Private</a>
            <?php
        }else{
            $progress = indppl_store_progress_bar($store_id, TRUE);
            if($progress['complete'] == 100){ ?>
                <p>Excellent work! You've completed all the steps to setup your store, but it's not live yet. If you're ready, go ahead and hit the button below to make it public. Don't worry, if you still need to make some changes you don't have to go live until you're ready!</p>
                <a href='#' class='store-go-live-btn button button-primary' data-id='<?php echo $store_id; ?>'>Make Public</a>

            <?php } else {
                echo "<h2>Store Setup Progress</h2>";
                echo $progress['bar'];
            }
        
        } ?>

        <ul class='indppl-nav indppl-nav-tabs'>
            <li class="indppl-active"><a href='#indppl-tab-1'>Store Info</a></li>
            <li><a href='#indppl-tab-2'>Plant Containers</a></li>
            <li><a href='#indppl-tab-3'>Products</a></li>
            <li><a href='#indppl-tab-4'>Guides</a></li>
        </ul>
        
        <div class='indppl-tab-content'>
            <div id='indppl-tab-1' class='indppl-tab-pane indppl-active'>
                <div class='indppl-store-management-container'>
                <h2>Store Management</h2>
                <p>This is a place for instructions</p>
                    <?php
                    $store_info  = indppl_store_info($store_id);
                    echo $store_info;
                    ?>
                </div>
            </div>
            <div id='indppl-tab-2' class='indppl-tab-pane'>
                
                <h2>Sizes</h2>
                <p>This is a place for instructions</p>
                <?php
                $containers = do_shortcode('[pp-store-containers]');
                echo $containers;
                ?>
            </div>
            <div id='indppl-tab-3' class='indppl-tab-pane'>
                <h2>Products</h2>
                <p>This is a place for instructions</p>
                <?php echo do_shortcode('[pp-store-products]'); ?>
            </div>
            <div id='indppl-tab-4' class='indppl-tab-pane'>
                
                <h2>Guides</h2>
                <p>This is a place for instructions</p>
                <?php 
                $guides = do_shortcode('[pp-store-guides]');
                echo $guides; 
                ?>
            </div>
        </div>
        <?php
        $return = ob_get_clean();
    }else if($_GET['new'] == true){
        ob_start();
        ?>
        <h2>Store Management</h2>
        <p>This is a place for instructions</p>
        <?php
        $store_info = indppl_store_info($store_id);
        echo $store_info;
        $return = ob_get_clean();
    }else{
        ob_start();
        ?>
        <div class='indppl-store-management-container'>
            <!-- <h2>My Stores</h2>
            <p>This is a place for instructions</p> -->
            <?php
            $store_info  = do_shortcode('[pp-my-stores]');
            echo $store_info;
            ?>
        </div>
        <?php
        $return = ob_get_clean();
    }
    
    
	return $return;
}
add_shortcode('pp-store-management', 'pp_store_management');

function pp_my_stores(){
    ob_start();
    ?>
    
        <?php
            $user_id = get_current_user_id();
            $args = array(
                'author' => $user_id,
                'post_type' => 'store',
                'orderby' => 'post-date',
            );
            $stores = new WP_Query($args);
            $status = indppl_user_status($user_id);
            if($stores->have_posts()){
                ?>
                <div class='indppl-my-stores-container'>
                <?php
                while($stores->have_posts()){
                    $stores->the_post();
                    
                    $id = get_the_ID();
                    $img = get_post_meta($id, 'wpcf-logo', true);
                    $title = get_the_title();
                    $address1 = get_post_meta($id, 'wpcf-address1', true);
                    $city = get_post_meta($id, 'wpcf-city', true);
                    $state = get_post_meta($id, 'wpcf-state', true);
                    $link = home_url() . '/store-profile?store-id=' . $id;
                    $permalink = get_the_permalink($id);
                    ?>
                    <div class='indppl-single-store-container'>
                        <?php
                        if($img){
                            ?>
                        <div class='flex-half'>
                            <div class='indppl-store-thumb'>
                                    <img src='<?php echo $img; ?>'>
                            </div>
                        </div>
                            <?php
                        }
                        ?>
                        <div class='flex-half flex-half-text'>
                            <h4 class='indppl-small-title'><?php echo $title; ?></h4>
                            <p class='indppl-small-store-text'><?php echo $address1; ?></p>
                            <p class='indppl-small-store-text'><?php echo $city . ', ' . $state; ?></p>
                            <a class='indppl-button button-primary indppl-small-store-perma-link' href='<?php echo $permalink; ?>' target="_blank">View</a>
                            <a class='indppl-button button-primary indppl-small-store-link' href='<?php echo $link; ?>'>Edit</a>
                        </div>
                    </div>
                    <?php
                }
                wp_reset_postdata();
                if(in_array('paidaccountpro', $status)){
                    $add_button = get_add_store_button();
                    echo $add_button;
                }
                ?>
                </div>
                <?php
            }else{
                $add_button = get_add_store_button();
                echo $add_button;
            }
        ?>
    <?php
    $return = ob_get_clean();
    return $return;
}
add_shortcode('pp-my-stores', 'pp_my_stores');

function pp_store_containers(){
    $store_id = $_GET['store-id'];
    
    $args2 = array('post_id' => $store_id);
    $cons = types_child_posts('container', $args2);
    // var_dump($cons);
    $container_array = array();
    foreach($cons as $key => $value){
        array_push($container_array, $value->ID);
    }

    $store_container_relations = toolset_get_related_posts(
        $store_id, // get posts related to this one
        'store-container', // relationship between the posts
        'parent',
        '100',
        '0',
        array(),
        'post_id',
        'intermediary'
    );

    // var_dump($store_container_relations);
    // var_dump($container_array);
    $int_args = array(
        'post_type' => 'store-container',

        'author' => get_current_user_id(),
    );
    $int = new WP_Query($int_args);
    $int_array = [];
    // int array has the relation for containers and season for this store.
    if($int->have_posts()){
        while($int->have_posts()){
            $int->the_post();
            $int_id = get_the_ID();
            $post = get_post($int_id);
            $slug = $post->post_name;
            $slug_array = explode('-', $slug);
            $cont_id = $slug_array[count($slug_array)-1];
            $int_meta = get_post_meta($int_id);
            $int_array[$cont_id] = array();
            // var_dump($int_id);
            foreach($int_meta as $key => $value){
                array_push($int_array[$cont_id], $key);
            }
        }
        wp_reset_postdata();
        // var_dump('<br /><br />');
        // var_dump($int_array);
    }
    $user_status = indppl_user_status(get_current_user_id());
    ob_start();

    ?>
    <form  method="post" action='#' id='container-select-form' class="form-horizontal" enctype="multipart/form-data">
        <input type='hidden' id='store-id' name='store-id' value='<?php echo $store_id; ?>'>
        <input type='hidden' id='user-status' name='user-status' value='<?php echo $user_status[0]; ?>'>
        <table class='indppl-containers-table'>
            <tr>
                <th>Select all plant sizes you carry</th>
                <th class='contianer-date-col'>
                    Spring
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            starts
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='spring-start' class='container-date' value='<?php echo get_post_meta($store_id, "wpcf-spring-start", true); ?>'>
                    </div>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            ends
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='spring-end' class='container-date' value='<?php echo get_post_meta($store_id, "wpcf-spring-end", true); ?>'>
                    </div>
                </th>
                <th class='contianer-date-col'>
                    Summer
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            starts
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='summer-start' class='container-date' value='<?php echo get_post_meta($store_id, "wpcf-summer-start", true); ?>'>
                    </div>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            ends
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='summer-end' class='container-date' value='<?php echo get_post_meta($store_id, "wpcf-summer-end", true); ?>'>
                    </div>
                </th>
                <th class='contianer-date-col'>
                    Fall
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            starts
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='fall-start' class='container-date' value='<?php echo get_post_meta($store_id, "wpcf-fall-start", true); ?>'>
                    </div>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            ends
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='fall-end' class='container-date' value='<?php echo get_post_meta($store_id, "wpcf-fall-end", true); ?>'>
                    </div>
                </th>
                <th class='contianer-date-col'>
                    Winter
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            starts
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='winter-start' class='container-date' value='<?php echo get_post_meta($store_id, "wpcf-winter-start", true); ?>'>
                    </div>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            ends
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='winter-end' class='container-date' value='<?php echo get_post_meta($store_id, "wpcf-winter-end", true); ?>'>
                    </div>
                </th>
            </tr>
            <?php
            $args = array(
                'post_type' => 'container',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'wpcf-default-container',
                        'compare' => 'EXISTS',                    
                    ),
                ),
                'orderby' => array('title' => 'DESC'),
            );
            $user_id = get_current_user_id();
            $user_args = array(
                'post_type' => 'container',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array('key' => 'wpcf-default-container',
                    'compare' => "NOT EXISTS",
                    ),
                ),
                'author' => $user_id,

                'orderby' => array('title' => 'DESC'),
            );

            $data1 = get_posts($args);
            $data2 = get_posts($user_args);

            $obj_merge = array_merge($data1, $data2);

            $postIDs = array_unique(wp_list_pluck($obj_merge, "ID"));
            
            $args3 = array(
                'post_type' => 'container',
                'post__in' => $postIDs,
                'nopaging' => true,
                );
            $final = get_posts($args3);

            if(isset($final)){
                foreach($final as $post){
                    setup_postdata($post);
                    $id = $post->ID;

                    $title = $post->post_title;
                    $meta = get_post_meta($id, 'wpcf-default-container', true);

                    $relation = array();
                    if(in_array($id, $container_array)){
                        $key = array_search($id, $container_array);
                        $relation = get_post_meta($store_container_relations[$key]);
                    }
                    echo indppl_build_container_relation_output($id, $title, $container_array, $relation, $meta);
                        
                }
                wp_reset_postdata();
            }
            ?>
        </table>
        <a href='#' class='add-container-btn button button-primary'>Add Container</a>
        <p class="container-submit"><input type="submit" name="container-submit" id="container-submit" class="button button-primary" value="Save Changes"/></p>
    </form>
    <?php
    $return = ob_get_clean();
    return $return;
}
add_shortcode('pp-store-containers', 'pp_store_containers');

function pp_store_products(){
    ?>
        <div class='indppl-products-main-container'>
            <h3 class='indppl-products-title'>In-Ground</h3>
            <a href="#" class='indppl-add-product-btn' data-type='ground'>Add Product</a>
            <div class='indppl-product-list'>
                <?php echo indppl_get_current_products("ground"); ?>
            </div>
            <h3 class='indppl-products-title'>Pots</h3>
            <a href="#" class='indppl-add-product-pots-btn' data-type='pots'>Add Product</a>
            <a href="#" class='indppl-application-rates-pots-btn' data-type='pots'>Application rates</a>
            <div class='indppl-product-list'>
                <?php echo indppl_get_current_products("pots"); ?>
            </div>
            <h3 class='indppl-products-title'>Raised beds</h3>
            <a href="#" class='indppl-add-product-pots-btn' data-type='beds'>Add Product</a>
            <a href="#" class='indppl-application-rates-pots-btn' data-type='beds'>Application rates</a>
            <div class='indppl-product-list'>
                <?php echo indppl_get_current_products("beds"); ?>
            </div>
        </div>
    <?php
}
add_shortcode('pp-store-products', 'pp_store_products');

function indppl_store_guides(){
    $user = get_current_user_id(  );
    $stati = indppl_user_status($user);
    $store = htmlspecialchars($_GET['store-id']);
    $pots_text = "Manage Potted Plants Planting Guide";
    $beds_text = "Manage Raised Bed Planting Guide";
    
    $pots = "<span style='font-style:italic;font-weight: 100;'>{$pots_text} (Pro Required)</span>";
    $beds = "<span style='font-style:italic;font-weight: 100;'>{$beds_text} (Pro Required)</span>";


    if(in_array('paidaccountpro', $stati)){
        $pots = "<a href='#' class='edit-guides pots-guide' data-target='pots' data-storeid='{$store}'>{$pots_text}</a>";
        $beds = "<a href='#' class='edit-guides pots-guide' data-target='beds' data-storeid='{$store}'>{$beds_text}</a>";
    }
    
    ob_start(); ?>
    <h3 class="indppl-products-title">Your Planting Guides</h3>
    <ul class="style-free">
        <li><a href="#" class="edit-guides ground-guide" data-target="ground" data-storeid="<?php echo $store; ?>">Manage In Ground Planting Guide</a></li>
        <li><?php echo $pots; ?></li>
        <li><?php echo $beds; ?></li>
    </ul>
    <?php
    return ob_get_clean();

}

add_shortcode( 'pp-store-guides', 'indppl_store_guides' );

function pp_sponsor_management(){
    ob_start();
    $user_id = get_current_user_id();
    $sponsor_status = get_user_meta($user_id, 'is_sponsor', true);
    if($sponsor_status != 1){
        return null;
    }
    $sponsor_count = get_user_meta($user_id, 'sponsor_count', true);
    $args = array(
        'author' => $user_id,
        'post_type' => 'sponsorship',
        'meta_key' => 'wpcf-sponsorship-active',
        'meta_value' => 1,
    );
    $sponsors = get_posts($args);
    $count = count($sponsors);
    // var_dump($sponsors);
    ?>
    <div class='indppl-add-sponsor-main-container'>
        <p><?php echo $count; ?>/<?php echo $sponsor_count; ?></p>
        <div class='indppl-add-sponsor-container'>
        <?php
        foreach($sponsors as $key => $value){
            $id = $value->ID;
            $img = get_post_meta($id, 'wpcf-sponsorship-image', true);
            $active = get_post_meta($id, 'wpcf-sponsorship-active', true);
            $brand_id = get_post_meta($id, 'brand_id', true);
            $product_id = get_post_meta($id, 'product_id', true);
            $title = $value->post_title;

            // var_dump($value);
            // var_dump("<br /><br />");
            if($active){

                ?>
            
                <a class='indppl-edit-sponsor-link flex-25' href='#' data-id='<?php echo $id; ?>' data-brand='<?php echo $brand_id; ?>' data-product='<?php echo $product_id; ?>'>
                    <div class='add-sponsor-container'>
                        <div class='indppl-add-sponsor-centered'>
                            <img clas='indppl-add-sponsor-image' src='<?php echo $img; ?>'>
                        </div>
                        <h4 class='indppl-add-sponsor-text'><?php echo $title; ?></h4>
                    </div>
                </a>
                
                <?php
            }
        }
        if($sponsor_status == 1 && $count < $sponsor_count){
            ?>
                
                <a class='indppl-add-sponsor-link flex-25' href='#'>
                    <div class='add-sponsor-container'>

                        <div class='indppl-add-sponsor-centered'>
                            <svg id='path' class="icon  icon--plus" viewBox="-52.5 -52.5 100 100" xmlns="http://www.w3.org/2000/svg">
                            <path d="M-5 -25 h5 v20 h20 v5 h-20 v20 h-5 v-20 h-20 v-5 h20 z" />
                        </svg>
                        </div>
                        <h4 class='indppl-add-sponsor-text'>Add Sponsorship</h4>
                    </div>
                </a>

            <?php
        }
        ?>
        </div>
    </div>
    <?php
    $return = ob_get_clean();
    return $return;
}
add_shortcode('pp-sponsor-management', 'pp_sponsor_management');