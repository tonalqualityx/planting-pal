<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security

function planting_pal_home($lat=NULL, $lon=NULL, $radius=NULL, $zip=null){
    ob_start(); ?>
    <div class='white-background'>
        <div class='container'>
            <img src="<?php echo INDPPL_ROOT_URL; ?>assets/img/logo-1.png" id='logo-header'>
            
        </div>
    </div>
    <div class="location-body light-blue-bg store-locate-container">
        <!-- <div class="desktopWarning">
            <p class="desktopWarning-p">This site is optimized for mobile phones in portrait layout.</p><i class="material-icons d-block portrait-only">screen_lock_portrait</i></div> -->
        <div class="container store-locate-inside-container indppl-light-green-bg">
            <div class='zip-search-container'>
                <div class="row wizard-start">
                    <div class="col lets-get-started-img"><img src="<?php echo INDPPL_ROOT_URL ?>assets/img/lets-get-started.png"></div>
                </div>
                <div class="row search-form">
                    <div class="col" id='app-location-submitter'>
                        <form action="<?php site_url(); ?>" method="post">
                        <h4 class='find-garden-center'>Find a Garden Center</h4>
                        <div class='side-by-side'>
                            <!-- <select class='form-control' id='geo-radius'>
                                <option value='5' selected>5 Miles</option>
                                <option value='10'>10 Miles</option>
                                <option value='15'>15 Miles</option>
                                <option value='25'>25 Miles</option>
                                <option value='custom'>Custom</option>
                            </select> -->
                            <input type='number' class='hide' min='0' max='30' id='geo-radius-custom' value='5'>
                        </div>
                        <div class='fix-position-geo'>
                            <input class="form-control rounded-input4" id='zip-for-location' type="text" name="zip" placeholder="Zip or Store Name">
                            <img class='hide' src="
                            <?php 
                            echo home_url() . '/wp-content/plugins/planting-pal/assets/img/gps.png'; 
                            ?>
                            " id="location-icon">
                            <a href="#" class="geo-submit orange-bg">FIND</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
       
    <?php
    $top = ob_get_clean();
    ob_start();
    $is_zip = isValidZipCode($zip);
    ?><div class='store-list-container'> <?php
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : '1';
    $args = array(
        'post_type' => 'store',
        'meta_query' => array(
            array(
                'key' => 'wpcf-issetup',
                'value' => '1',
                'compare' => '=',
            ),
        ),
    );
    $store_name_search = false;
    // var_dump($is_zip);
    if($zip && $is_zip == false){
        $store_name_search = true;
        $args['meta_query'] = array(
            array(
                'key' => 'wpcf-issetup',
                'value' => '1',
                'compare' => '=',
            ),
        );
        $args['post_title_like'] = $zip;
        if($lat != NULL){
            $zip_array = geofind($lat, $lon, $radius);
            foreach($zip_array as $key => $value){
                $zips_only[] = $value['zip'];
                $distance_array[] = round($value['distance'], 2);
            }
            // $args['meta_query'] = array(
            //     'relation' => 'AND',
            //     array(
            //         'key' => 'wpcf-zip',
            //         'value'   => $zips_only,
            //         'compare' => 'IN',
            //     ),
            //     array(
            //         'key' => 'wpcf-issetup',
            //         'value' => '1',
            //         'compare' => '=',
            //     ),
            // );
        }
    }else if($zip == null && $lat){
        $distance_array = array();
        $zips_only = array();
        if($lat != NULL){
            $zip_array = geofind($lat, $lon, $radius);
            foreach($zip_array as $key => $value){
                $zips_only[] = $value['zip'];
                $distance_array[] = round($value['distance'], 2);
            }
        }
        $args['meta_query'] = array(
            'relation' => 'AND',
            array(
                'key' => 'wpcf-zip',
                'value'   => $zips_only,
                'compare' => 'IN',
            ),
            array(
                'key' => 'wpcf-issetup',
                'value' => '1',
                'compare' => '=',
            ),
        );
    }else if($zip){
        $zip_array = geozip($zip, $radius);
        foreach($zip_array as $key => $value){
            $zips_only[] = $value['zip'];
            $distance_array[] = round($value['distance'], 2);
        }
        $args['meta_query'] = array(
            'relation' => 'AND',
            array(
                'key' => 'wpcf-zip',
                'value'   => $zips_only,
                'compare' => 'IN',
            ),
            array(
                'key' => 'wpcf-issetup',
                'value' => '1',
                'compare' => '=',
            ),
        );
    }
    // var_dump($store_name_search);
    $pagination = 5;
    if(isset($_POST['pagination'])){
        $pagination = intval($_POST['pagination']);
    }

    $the_query = new WP_Query( $args );
    // The Loop
    if ( $the_query->have_posts() ) {
        ?>
        <div class='flex-left-justify min-width-90'><?php
        $i =0;
        $store_array = array();
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $id = get_the_ID();
            $post_zip = get_post_meta($id, 'wpcf-zip', true);
            if(isset($zip_array) && count($zip_array) > 0){
                foreach($zip_array as $key => $value){
                    if($value['zip'] == $post_zip){
                        $distance = $value['distance'];
                    }
                }
            }
            $store_array[$id] = $distance;
        }
        // var_dump($the_query);
        // var_dump('<br /><br />');
        // var_dump($store_array);
        asort($store_array);
        foreach($store_array as $key => $value){
            $id = $key;
            $add = get_post_meta($id, 'wpcf-address1', true);
            $city = get_post_meta($id, 'wpcf-city', true);
            $state = get_post_meta($id, 'wpcf-state', true);
            $store_zip = get_post_meta($id, 'wpcf-zip', true);
            $distance = $value;
            $phone = get_post_meta($id, 'wpcf-phone', true);
            $url = get_post_meta($id, 'wpcf-weburl', true);
            $author = get_post_field( 'post_author', $id );
            // var_dump($url);
            $pro_array = indppl_user_status($author);
            if(in_array('paidaccountpro', $pro_array)){
                $is_pro = true;
            }else{
                $is_pro = false;
            }
            $title = get_the_title($id);
            $img = get_post_meta($id, 'wpcf-logo', true);
            ?>
            <div class='single-store-app-container'>
                <div class='app-store-img'>
                    <img src=<?php echo $img; ?>>
                </div>
                <div class='app-store-info'>
                    <h3 class='results-store'><a href='<?php echo get_permalink($id); ?>'><?php echo $title; ?></a></h3>
                    <p class='store-list-text'><?php echo $add; ?></p>
                    <p class='store-list-text'><?php echo $city . ", " . $state . " " . $store_zip; ?></p>
                    <?php

                    if($is_pro == true){
                        ?>
                        <p class='store-list-text'>
                            <a class='orange-text' href=tel:<?php echo $phone; ?>><?php echo phone_number_format($phone); ?></a>
                            <a class='orange-text' href='<?php echo $url; ?>' target='_blank'><?php echo $url; ?></a></p>
                        <?php
                    }
                    ?>
                </div>
                <div class='app-store-distance'>
                    <?php if($distance > 0){
                        ?>
                        <p class='store-distance store-list-text'><?php echo round($distance, 2); ?> mi</p>
                        <?php
                    }else if($store_zip == $zip || $zip_array[0]['zip'] == $store_zip){
                        ?>
                        <p class='store-distance store-list-text'>In Town</p>
                        <?php
                    }else if($zip != null || $lat != 0){
                        ?>
                        <p class='store-distance store-list-text'>Greater than 30mi</p>
                        <?php
                    }
                    ?>
                </div>
                    
            </div>
            
            <?php
            if($i >= $pagination-1){
                break;
            }
            $i++;
        }
        ?></div>
        <?php
        if($the_query->found_posts > $pagination){
        ?>
            <div class='indppl-pagination-container'>
                <a href='#' id='indppl-app-pagination' class='indppl-button' data-page="<?php echo $pagination + 5; ?>">Load More</a>
            </div>

        <?php
        }
        
        /* Restore original Post Data */
        wp_reset_postdata();
    } else {
        ?><p>No Stores in your area</p><?php
    }
    ?></div><?php
    if(isset($_POST['radius'])){
        $get_store = ob_get_clean();
        return $get_store;
    }
    
    ?>
        <script src="<?php echo INDPPL_ROOT_URL ?>assets/bootstrap/js/bootstrap.min.js"></script>
    </div>
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
    $status = indppl_user_status($user_id);
    ob_start();
    if(in_array('showaccount',$status)){
        FLBuilder::render_query( array(
            'page_id' => 48306
        ));
    } else {

        if(isset($_GET['store-id'])){
            $safe_store_id = intval($_GET['store-id']);
            $author_id = get_post_field('post_author', $safe_store_id);
            
            if($user_id == $author_id || current_user_can('administrator') || indppl_user_is_auth($user_id, $safe_store_id)){
                $store_id = intval(htmlspecialchars($_GET['store-id']));
                echo "<script>monitorProgress({$store_id});</script>";
            }else{
                ?>
                <h3 class='color-red'>Sorry, but you must be logged in and authorized to access this store in order to make edits.</h3>
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
                    <?php 
                        $user = get_current_user_id();
                        $args = array(
                            'author' => $user,
                            'post_type' => 'store',
                            'posts_per_page' => -1,
                            // 'meta_query' => array(
                                'meta_key' => 'wpcf-issetup',
                                'meta_value' => 1
                            // ),
                        );
                        
                        $user_stores = get_posts($args);
                        $user_stores_count = count($user_stores);
                        if($user_stores_count > 0){
                            // echo "<p>You will be billed for an additional store when this store is brought online.</p>";
                            // indppl_notify_new_store($store_id, $user);
                        }
                    ?>
                    <a href='#' class='store-go-live-btn button button-primary' data-id='<?php echo $store_id; ?>'>Make Public</a>
    
                <?php } else {
                    echo "<h2>Store Setup Progress</h2>";
                    echo $progress['bar'];
                }
            
            } ?>
    
            <ul class='indppl-nav indppl-nav-tabs'>
                <li class="indppl-active"><a href='#indppl-tab-1'>1. Store Info</a></li>
                <li><a href='#indppl-tab-2'>2. Container Size Selection</a></li>
                <li><a href='#indppl-tab-3'>3. Products Recommendations</a></li>
                <li><a href='#indppl-tab-4'>4. Planting Guides</a></li>
            </ul>
            
            <div class='indppl-tab-content'>
                <div id='indppl-tab-1' class='indppl-tab-pane indppl-active'>
                    <div class='indppl-store-management-container'>
                        <div class="indppl-instructions">
                            <div class="indppl-instructions-text">
                                <h2>Store Information</h2>
                                <p>Enter your Garden Center's store information here. This info will be used to help customers find you in the Planting Pal app and customize your planting guide with contact information and store logo.</p>
                            </div>
                            <div class="indppl-video">
                                <iframe width="266" height="150" src="https://www.youtube.com/embed/_u9CgVPHU6A" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                <p class="indppl-watch-video">Watch: How to use this page.</p>
                            </div>
                        </div>
                    </div>
                    <?php
                    $store_info  = indppl_store_info($store_id);
                    echo $store_info;
                    ?>
                </div>
                <div id='indppl-tab-2' class='indppl-tab-pane'>
                    
                    <div class="indppl-instructions">
                        <div class="indppl-instructions-text">
                            <h2>Container Size Selection</h2>
                            <p>Select the plant container sizes you stock at your Garden Center. Only the sizes you select here will be shown to your customer in the app. Also, since some sizes are seasonal, select which plant container sizes you want showing up in the different seasons.</p>
                        </div>
                        <div class="indppl-video">
                            <iframe width="266" height="150" src="https://www.youtube.com/embed/_u9CgVPHU6A" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <p class="indppl-watch-video">Watch: How to use this page.</p>
                        </div>
                    </div>
                            
                    <div class="margin-top-20">
                        <?php $containers = do_shortcode('[pp-store-containers]');
                        echo $containers; ?>
                    </div>
    
                </div>
                <div id='indppl-tab-3' class='indppl-tab-pane'>
                    <div class="indppl-instructions">
                        <div class="indppl-instructions-text">
                            <h2>Product Recommendation 'Recipes'</h2>
                            <p>Your planting recommendations are like a gourmet recipe where each product is an ingredient. The product recommendation 'recipes' you build on this page will show up on the app's Shopping List and Planting Guide to tell your customers what products to use and in what quantities.</p>
                        </div>
                        <div class="indppl-video">
                            <iframe width="266" height="150" src="https://www.youtube.com/embed/_u9CgVPHU6A" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <p class="indppl-watch-video">Watch: How to use this page.</p>
                        </div>
                    </div>
                    <div id="pp-store-products">
                        <?php echo do_shortcode('[pp-store-products]'); ?>
                    </div>
                </div>
                <div id='indppl-tab-4' class='indppl-tab-pane'>
                    
                    <div class="indppl-instructions">
                        <div class="indppl-instructions-text">
                            <h2>Planting Guides</h2>
                            <p>We're not big fans of one-size-fits-all. That applies to planting guides as well. From here, you'll be able to manage customized planting guides for each planting situation your customer faces.</p>
                        </div>
                        <div class="indppl-video">
                            <iframe width="266" height="150" src="https://www.youtube.com/embed/_u9CgVPHU6A" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <p class="indppl-watch-video">Watch: How to use this page.</p>
                        </div>
                    </div>
    
                    <?php 
                    $guides = do_shortcode('[pp-store-guides]');
                    echo $guides; 
                    ?>
                </div>
            </div>
            <?php
            // $return = ob_get_clean();
        }else if($_GET['new'] == true){
            // ob_start();
            ?>
            <div class="indppl-instructions margin-top-20">
                <div class="indppl-instructions-text">
                    <h2>Store Management</h2>
                    <p>Enter your Garden Center's store information here. This info will be used to help customers find you in the Planting Pal app and customize your planting guide with contact information and store logo.</p>
                </div>
                <div class="indppl-video">
                    <iframe width="266" height="150" src="https://www.youtube.com/embed/cVknBmohzGA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>                
                    <p class="indppl-watch-video">Watch: How to use this page.</p>
                </div>
            </div>
            <?php
            $store_info = indppl_store_info($store_id);
            echo $store_info;
            // $return = ob_get_clean();
        }else{
            // ob_start();
            ?>
            <div class='indppl-store-management-container'>
                <!-- <h2>My Stores</h2>
                <p>This is a place for instructions</p> -->
                <?php
                $store_info  = do_shortcode('[pp-my-stores]');
                echo $store_info;
                echo do_shortcode('[pp-my-dups]');
                ?>
            </div>
            <?php
        }
        
        indppl_membr_modal_init();
    }

    
    $return = ob_get_clean();
    
	return $return;
}
add_shortcode('pp-store-management', 'pp_store_management');

function pp_my_stores(){
    ob_start();

    if(in_array('showaccount',$status)){
        FLBuilder::render_query( array(
            'page_id' => 48306
        ));
    } else { 
        
        $user_id = get_current_user_id();
        $args = array(
            'author' => $user_id,
            'post_type' => 'store',
            'orderby' => 'post-date',
        );
        $stores = new WP_Query($args);
        $status = indppl_user_status($user_id);
        global $wp;
        $curernt_url =  home_url( $wp->request );
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
                    $live = get_post_meta($id, 'wpcf-issetup', true);
                    ?>
                    <div class='indppl-single-store-container white-background indppl-space-between'>
                        <?php
                        if($title){
                            ?>
                        <div class="indppl-store-dash-left">
                            <div class='indppl-flex'>
                                <div class='indppl-store-thumb indppl-dash-thumb'>
                                        <img src='<?php echo $img; ?>'>
                                </div>
                                <div class="indppl-store-address">
                                    <h4 class=''><?php echo $title; ?></h4>
                                    <p class='indppl-small-store-text'><?php echo $address1; ?></p>
                                    <p class='indppl-small-store-text'><?php echo $city . ', ' . $state; ?></p>
                                </div>
                            </div>
                            <div class="dash-buttons">
                                <p><a class='indppl-button button-primary indppl-small-store-link' href='<?php echo $link; ?>'>Edit</a> Manage profile, products, & planting guide</p>
                                <p><a class='indppl-button button-primary indppl-small-store-perma-link' href='<?php echo $permalink; ?>' target="_blank">Test</a> Test store in the app</p>
                                <p style="display:none;"><a href='#' data-store='<?php echo $id; ?>' class='indppl-button button-primary indppl-duplicate-store'>Duplicate</a> Copy store settings to create a new store</p>
                                <p><a href='#' data-store='<?php echo $id; ?>' class='indppl-button button-primary indppl-delete-store'>Delete</a> Delete this store</p>
                                <?php if(in_array('paidaccountpro', $status)){ ?>
                                    <p><a href='#' data-store='<?php echo $id; ?>' class='indppl-button button-primary indppl-store-auth'>Authorize</a> Manage who can duplicate this store</p>
                                <?php } ?>
                            </div>
                        </div>
                            <?php
                        }
                        ?>
                        <div class=''>
                            
                            <div class=''>
                                <?php 
                                $status = "Offline";
                                $status_class = "grey-text";
                                $progress = indppl_store_progress_bar($id, false, false);

                                if($live){
                                    $status = "Online";
                                    $status_class = "green-text";
                                }
                                        ?>
                                <p><strong>Store Status:</strong> <span class='<?php echo $status_class; ?>' id='status-<?php echo $id; ?>'><?php echo $status; ?></span></p>
                                <?php 
                                $gauge_level = 360*($progress['complete']/100);
                                $p51 = '';
                                if($gauge_level > 180){
                                    $p51 = 'p51';
                                }
                                ?>
                                <div class="c100 <?php echo $p51; ?> center orange">
                                    <span><span class="gauge-small">store setup</span><?php echo $progress['complete']; ?>%<span class="gauge-small">complete</span></span>
                                    <div class="slice">
                                        <div class="bar" style="transform: rotate(<?php echo $gauge_level; ?>deg);"></div>
                                        <div class="fill"></div>
                                    </div>
                                </div>
                                <?php if ($progress['complete'] < 100) { ?>
                                    <a href="<?php echo $link; ?>" class="orange-text text-center" style="display:block; margin-top:5px;">finish store setup</a>

                                <?php } 
                                if($progress['complete'] == 100 && !$live){ ?>
                                    <a href='#' data-store='<?php echo $id; ?>' class='orange-text text-center indppl-live-store' style="display:block; margin-top:5px;">Go Live</a>
                                <?php
                                }elseif($progress['complete'] == 100 && $live){
                                    ?>
                                    <a href='#' class='orange-text text-center indppl-store-deactivate' data-store='<?php echo $id; ?>' style="display:block; margin-top:5px;">Deactivate</a>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                wp_reset_postdata(); ?>
            </div>
            <?php
        }else if(home_url() . '/my-account' == $curernt_url){
            $store_form = indppl_store_info($id);
            echo $store_form;
        }else{
            // $add_button = get_add_store_button();
            // echo $add_button;
            $store_form = indppl_store_info($id);
            echo $store_form;
        }
        do_shortcode('[pp-my-dups]');

    }
    $return = ob_get_clean();
    return $return;
}
add_shortcode('pp-my-stores', 'pp_my_stores');

function pp_store_containers(){
    $store_id = $_GET['store-id'];
    
    $store_container_relations = toolset_get_related_posts(
        $store_id, // get posts related to this one
        'store-container', // relationship between the posts
        'parent',
        '100',
        '0',
        array('limit' => 999),
        'post_id',
        'intermediary'
    );

    $container_array = toolset_get_related_posts(
        $store_id, // get posts related to this one
        'store-container', // relationship between the posts
        'parent',
        '100',
        '0',
        array('limit' => 999),
        'post_id',
        'child'
    );

    $first_time_class = '';
    $product_array = toolset_get_related_posts(
        $store_id,
        'store-product',
        'parent',
        '100',
        '0',
        array('limit' => 999),
        'post_id',
        'child'
    );
    if(empty($product_array)){
        $first_time_class = 'ind-first-time';
    }

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
    <form  method="post" action='#' id='container-select-form' class="form-horizontal <?php echo $first_time_class; ?>" enctype="multipart/form-data">
        <input type='hidden' id='store-id' name='store-id' value='<?php echo $store_id; ?>'>
        <input type='hidden' id='user-status' name='user-status' value='<?php echo $user_status[0]; ?>'>
        <table class='indppl-containers-table'>
            <tr>
                <th>Select all plant sizes you carry</th>
                <?php
                if(get_post_meta($store_id, "wpcf-spring-start", true)){
                    $spring_start = get_post_meta($store_id, 'wpcf-spring-start', true);
                }else{
                    $spring_start = '3/20';
                }
                if(get_post_meta($store_id, "wpcf-spring-end", true)){
                    $spring_end = get_post_meta($store_id, 'wpcf-spring-end', true);
                }else{
                    $spring_end = '6/20';
                }
                // var_dump($spring_start . " : " . $spring_end);
                if(get_post_meta($store_id, "wpcf-summer-start", true)){
                    $summer_start = get_post_meta($store_id, 'wpcf-summer-start', true);
                }else{
                    $summer_start = '6/21';
                }
                if(get_post_meta($store_id, "wpcf-summer-end", true)){
                    $summer_end = get_post_meta($store_id, 'wpcf-summer-end', true);
                }else{
                    $summer_end = '9/22';
                }

                if(get_post_meta($store_id, "wpcf-fall-start", true)){
                    $fall_start = get_post_meta($store_id, 'wpcf-fall-start', true);
                }else{
                    $fall_start = '9/23';
                }
                if(get_post_meta($store_id, "wpcf-fall-end", true)){
                    $fall_end = get_post_meta($store_id, 'wpcf-fall-end', true);
                }else{
                    $fall_end = '12/20';
                }

                if(get_post_meta($store_id, "wpcf-winter-start", true)){
                    $winter_start = get_post_meta($store_id, 'wpcf-winter-start', true);
                }else{
                    $winter_start = '12/21';
                }
                if(get_post_meta($store_id, "wpcf-winter-end", true)){
                    $winter_end = get_post_meta($store_id, 'wpcf-winter-end', true);
                }else{
                    $winter_end = '3/19';
                }
                ?>
                <th class='contianer-date-col'>
                    <h4 class='container-season'>Spring</h4>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            starts
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='spring-start' class='container-date' value='<?php echo $spring_start; ?>'>
                    </div>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            ends
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='spring-end' class='container-date' value='<?php echo $spring_end; ?>'>
                    </div>
                </th>
                <th class='contianer-date-col'>
                <h4 class='container-season'>Summer</h4>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            starts
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='summer-start' class='container-date' value='<?php echo $summer_start; ?>'>
                    </div>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            ends
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='summer-end' class='container-date' value='<?php echo $summer_end; ?>'>
                    </div>
                </th>
                <th class='contianer-date-col'>
                <h4 class='container-season'>Fall</h4>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            starts
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='fall-start' class='container-date' value='<?php echo $fall_start; ?>'>
                    </div>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            ends
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='fall-end' class='container-date' value='<?php echo $fall_end; ?>'>
                    </div>
                </th>
                <th class='contianer-date-col'>
                <h4 class='container-season'>Winter</h4>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            starts
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='winter-start' class='container-date' value='<?php echo $winter_start; ?>'>
                    </div>
                    <div class='container-date-container'>
                        <span class='padding-right-5'>
                            ends
                        </span>
                        <div><img class='indppl-cal-img' src='<?php echo home_url(); ?>/wp-content/plugins/planting-pal/assets/img/calendar.png'></div>
                        <input type='text' name='winter-end' class='container-date' value='<?php echo $winter_end; ?>'>
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

            $data1 = get_posts($user_args);
            $data2 = get_posts($args);

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
        <div class='container-button-container'>
            <a href='#' class='add-container-btn indppl-button'>+ Add New Plant Container Size</a>
            <!-- <p class="container-submit"><input type="submit" name="container-submit" id="container-submit" class="button button-primary" value="Save Changes"/></p> -->
        </div>
    </form>
    <?php
    $return = ob_get_clean();
    return $return;
}
add_shortcode('pp-store-containers', 'pp_store_containers');

function pp_store_products(){
    if(isset($_GET['store-id'])){
        $store_id = $_GET['store-id'];
    }else if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }

    $store_owner = get_the_author_meta('ID', $store_id);
    $stati       = indppl_user_status($store_owner);
    $pro         = in_array('paidaccountpro', $stati) ? true : false;
    // var_dump($store_id);
    $apprates = indppl_apprates($store_id);
    ?>
        <div class='indppl-products-main-container'>

            <h3 class='indppl-products-title indppl-dark-green'>In-Ground Plantings</h3>
            <p class="indppl-products-instructions">Add, edit & delete your 'ingredient list' for in-ground planting recommendations</p>
            <div class='indppl-product-list'>
                <?php echo indppl_get_current_products("ground"); ?>
            </div>
            <a href="#" class='indppl-add-product-btn indppl-btn' data-type='ground'>+ Add Product</a>

            <?php if($pro){ ?>

                <h3 class='indppl-products-title indppl-dark-green'>Pot Plantings</h3>
                <p class="indppl-products-instructions">Add, edit & delete your 'ingredient list' for pot planting recommendations. For this section, be sure to add all the products before adjusting application rates.</p>
                <div class='indppl-product-list'>
                    <?php echo indppl_get_current_products("pots"); ?>
                </div>
                <a href="#" class='indppl-add-product-pots-btn indppl-btn' data-type='pots'>+ Add Product</a>
                <?php
                // var_dump($apprates);
                if(count($apprates['pots']) > 0){
                    ?>
                    <a href="#" class='indppl-application-rates-pots-btn indppl-btn' data-type='pots'>Application rates</a>
                    <?php
                }
                ?>

                <h3 class='indppl-products-title indppl-dark-green'>Raised Bed Plantings</h3>
                <p class="indppl-products-instructions">Add, edit & delete your 'ingredient list' for raised bed planting recommendations. For this section, be sure to add all the products before adjusting application rates.</p>
                <div class='indppl-product-list'>
                    <?php echo indppl_get_current_products("beds"); ?>
                </div>
                <a href="#" class='indppl-add-product-pots-btn indppl-btn' data-type='beds'>+ Add Product</a>
                <?php
                // var_dump($apprates);
                if(count($apprates['beds']) > 0){
                    ?>
                    <a href="#" class='indppl-application-rates-pots-btn indppl-btn' data-type='beds'>Application rates</a>
                    <?php
                }
                ?>
                
            <?php } ?>
        </div>
    <?php
}
add_shortcode('pp-store-products', 'pp_store_products');

function indppl_store_guides(){
    $user = get_current_user_id(  );
    $store = htmlspecialchars($_GET['store-id']);
    $store_owner = get_the_author_meta('ID', $store);
    $stati = indppl_user_status($store_owner);
    $pro = in_array('paidaccountpro', $stati) ? true : false;

    $pots_text = "Pot Planting Guide";
    $beds_text = "Raised Bed Planting Guide";
    
    $pots = "<span style='font-style:italic;font-weight: 100;'>{$pots_text} (Pro Required)</span>";
    $beds = "<span style='font-style:italic;font-weight: 100;'>{$beds_text} (Pro Required)</span>";


    if(in_array('paidaccountpro', $stati)){
        $pots = "<h3 class='indppl-dark-green'>{$pots_text}</h3><a href='#' class='edit-guides pots-guide indppl-btn' data-target='pots' data-storeid='{$store}'>Edit</a>";
        $beds = "<h3 class='indppl-dark-green'>{$beds_text}</h3><a href='#' class='edit-guides pots-guide indppl-btn' data-target='beds' data-storeid='{$store}'>Edit</a>";
    }
    
    ob_start(); ?>
    <ul class="style-free planting-guide-editor">
        <li>
            <h3 class="indppl-dark-green">In Ground Planting Guide</h3>
            <a href="#" class="edit-guides ground-guide indppl-btn" data-target="ground" data-storeid="<?php echo $store; ?>">Edit</a></li>
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

function indppl_authorized_dups(){
    $user = get_userdata( get_current_user_id() );
    $response = indppl_get_dup_auth($user->user_email, 'sub');

    $user_id = get_current_user_id();
    $args = array(
        'author' => $user_id,
        'post_type' => 'store',
        'orderby' => 'post-date',
    );
    $stores = new WP_Query($args);
    $status = indppl_user_status($user_id);
    global $wp;

    if(count($response) > 0){

        ob_start(); ?>

            <div class="indppl-dup-stores">
                <h2>Stores you are authorized to manage</h2>
                <!-- <ul class="style-free"> -->
                    <?php foreach($response as $store){
                        $store_name = get_the_title($store['store_id']);
                        if($store_name){

                            $address1   = get_post_meta($store['store_id'], 'wpcf-address1', TRUE);
                            $city = get_post_meta($store['store_id'], 'wpcf-city', TRUE);
                            $state = get_post_meta($store['store_id'], 'wpcf-state', TRUE);
                            $logo = get_post_meta($store['store_id'], 'wpcf-logo', TRUE);
                            $link = home_url() . "/store-profile?store-id=" . $store['store_id']; ?>
                            <div class="indppl-single-store-container white-background indppl-space-between">
                                <div class="indppl-store-dash-left">
                                    <div class="indppl-flex">
                                        <div class="indppl-store-thumb indppl-dash-thumb">
                                            <?php if($logo){ ?>
                                                <img src="<?php echo $logo; ?>" alt="Store Logo">
                                            <?php } ?>
                                        </div>
                                        <div class="indppl-store-address">
                                            <h4 class=""><?php echo $store_name; ?></h4>
                                            <p class="indppl-small-store-text"><?php echo $address1; ?></p>
                                            <p class="indppl-small-store-text"><?php echo $city . ", " . $state; ?></p>
                                        </div>
                                    </div>
                                    <div class="dash-buttons">
                                        <p><a class='indppl-button button-primary indppl-small-store-link' href='<?php echo $link; ?>'>Edit</a> Manage profile, products, & planting guide</p>
                                        <p><a class='indppl-button button-primary indppl-small-store-perma-link' href='<?php echo get_the_permalink($store['store_id']); ?>' target="_blank">Test</a> Test store in the app</p>
                                        
                                        <p><a href='#' data-store='<?php echo $id; ?>' class='indppl-button button-primary indppl-delete-store'>Delete</a> Delete this store</p>
                                        <?php if(in_array('paidaccountpro', $status)){ ?>
                                            <p><a href='#' data-store='<?php echo $store['store_id']; ?>' class='indppl-button button-primary indppl-store-auth'>Authorize</a> Manage who can duplicate this store</p>
                                        <?php } ?>
                                        <?php if(!$stores->have_posts()) { ?>
                                            <p><a class="indppl-button button-primary indppl-small-store-link indppl-duplicate-store" data-store="<?php echo $store['store-id']; ?>" href="#">Duplicate</a> Duplicate this store's containers, products, application rates, and planting guides</p>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="">
                                        
                                    <div class="">
                                        <?php 
                                        $status = "Offline";
                                        $status_class = "grey-text";
                                        $progress = indppl_store_progress_bar($store['store_id'], false, false);
                                        $live = get_post_meta($store['store_id'], 'wpcf-issetup', TRUE);

                                        if($live){
                                            $status = "Online";
                                            $status_class = "green-text";
                                        }
                                            ?>
                                        <p><strong>Store Status:</strong> <span class='<?php echo $status_class; ?>' id='status-<?php echo $store['store_id']; ?>'><?php echo $status; ?></span></p>
                                        <?php 
                                        $gauge_level = 360*($progress['complete']/100);
                                        $p51 = '';
                                        if($gauge_level > 180){
                                            $p51 = 'p51';
                                        }
                                        ?>
                                        <div class="c100 <?php echo $p51; ?> center orange">
                                            <span><span class="gauge-small">store setup</span><?php echo $progress['complete']; ?>%<span class="gauge-small">complete</span></span>
                                            <div class="slice">
                                                <div class="bar" style="transform: rotate(<?php echo $gauge_level; ?>deg);"></div>
                                                <div class="fill"></div>
                                            </div>
                                        </div>
                                        <?php if ($progress['complete'] < 100) { ?>
                                        <a href="<?php echo $link; ?>" class="orange-text text-center" style="display:block; margin-top:5px;">finish store setup</a>

                                        <?php } 
                                        if($progress['complete'] == 100 && !$live){ ?>
                                            <a href='#' data-store='<?php echo $id; ?>' class='orange-text text-center indppl-live-store' style="display:block; margin-top:5px;">Go Live</a>
                                        <?php
                                        }elseif($progress['complete'] == 100 && $live){
                                            ?>
                                            <a href='#' class='orange-text text-center indppl-store-deactivate' data-store='<?php echo $id; ?>' style="display:block; margin-top:5px;">Deactivate</a>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php } else{

                        } ?>

                    <?php } ?>
                <!-- </ul> -->
            </div>

        <?php $response = ob_get_clean();
        return $response;
    } else {
        // Nothing to see here...
    }
}
add_shortcode('pp-my-dups', 'indppl_authorized_dups');