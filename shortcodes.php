<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security
function planting_pal_home($lat=NULL, $lon=NULL){
	ob_start();
    ?>
    <body class="location-body ppl-green-bg">
        <!-- <div class="desktopWarning">
            <p class="desktopWarning-p">This site is optimized for mobile phones in portrait layout.</p><i class="material-icons d-block portrait-only">screen_lock_portrait</i></div> -->
        <div class="container">
            <div class='zip-search-container'>
                <div class="row wizard-start">
                    <div class="col"><img src="<?php echo INDPPL_ROOT_URL ?>assets/img/wizard-location.png"></div>
                </div>
                <div class="row search-form">
                    <div class="col">
                    <form action="<?php site_url(); ?>" method="post">
                    <input class="form-control rounded-input4" id='zip-for-location' type="text" name="zip" placeholder="Zipcode"><i class="material-icons" id="location-icon">my_location</i>
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
add_shortcode( 'planting-pal-home', 'planting_pal_home' );

function pp_store_management(){
    $store_id = '';
    if(isset($_GET['store-id'])){
        $store_id = intval(htmlspecialchars($_GET['store-id']));
    }
    if(isset($_POST['submit'])){
        if($store_id == null){$store_id = 0;}
        if(!empty($_POST['store-id'])){
            $store_id = $_POST['store-id'];
        }
        $store_id = indppl_save_post($store_id);
    }
	// if(is_int($store_id)){
	// 	$store_name = get_the_title($store_id);
	// 	$address1 = get_post_meta($store_id, 'wpcf-address1', true);
	// 	$address2 = get_post_meta($store_id, 'wpcf-address2', true);
	// 	$city = get_post_meta($store_id, 'wpcf-city', true);
	// 	$state = get_post_meta($store_id, 'wpcf-state', true);
	// 	$zip = get_post_meta($store_id, 'wpcf-zip', true);
	// 	$weburl = get_post_meta($store_id, 'wpcf-weburl', true);
	// 	$phone = get_post_meta($store_id, 'wpcf-phone', true);
	// 	$email = get_post_meta($store_id, 'wpcf-email', true);
	// 	$logo = get_post_meta($store_id, 'wpcf-logo', true);
    // }
    
    
    if(is_int($store_id)){
        ob_start();
        $setup = get_post_meta($store_id, 'wpcf-issetup', true);
        if($setup){
            ?>
            <p>Your store is Live. To make your site private hit the button below.</p>
            <a href='#' class='store-go-live-btn button button-primary' data-id='<?php echo $store_id; ?>'>Make Private</a>
            <?php
        }else{
            ?>
            <p>Your store is not live. If you have filled out all the information below you can make your store live with this button.</p>
            <a href='#' class='store-go-live-btn button button-primary' data-id='<?php echo $store_id; ?>'>Make Public</a>
        <?php } ?>

        <ul class='indppl-nav indppl-nav-tabs'>
            <li class="indppl-active"><a href='#indppl-tab-1'>Store Info</a></li>
            <li><a href='#indppl-tab-2'>Sizes</a></li>
            <li><a href='#indppl-tab-3'>In-Ground</a></li>
            <li><a href='#indppl-tab-4'>Pots</a></li>
            <li><a href='#indppl-tab-5'>Raised Beds</a></li>
            <li><a href='#indppl-tab-6'>Guides</a></li>
        </ul>
        
        <div class='indppl-tab-content'>
            <div id='indppl-tab-1' class='indppl-tab-pane indppl-active'>
                <?php
                $store_info  = indppl_store_info($store_id);
                echo $store_info;
                ?>
            </div>
            <div id='indppl-tab-2' class='indppl-tab-pane'>
                
                <p>Sizes</p>
                <?php
                $containers = do_shortcode('[pp-store-containers]');
                echo $containers;
                ?>
            </div>
            <div id='indppl-tab-3' class='indppl-tab-pane'>
                
                <p>In-Ground</p>
            </div>
            <div id='indppl-tab-4' class='indppl-tab-pane'>
                
                <p>Pots</p>
            </div>
            <div id='indppl-tab-5' class='indppl-tab-pane'>
                
                <p>Raised Beds</p>
            </div>
            <div id='indppl-tab-6' class='indppl-tab-pane'>
                
                <p>Guides</p>
            </div>
        </div>
        <?php
        $return = ob_get_clean();
    }else{
        $return = indppl_store_info($store_id);
    }
    
    
	return $return;
}
add_shortcode('pp-store-management', 'pp_store_management');

function pp_my_stores(){
    ob_start();
    ?>
    <div class='indppl-my-stores-container'>
        <?php
            $user_id = get_current_user_id();
            $args = array(
                'author' => $user_id,
                'post_type' => 'store',
                'orderby' => 'post-date',
            );
            $stores = new WP_Query($args);
            if($stores->have_posts()){
                while($stores->have_posts()){
                    $stores->the_post();
                    
                    $id = get_the_ID();
                    $img = get_post_meta($id, 'wpcf-logo', true);
                    $title = get_the_title();
                    $address1 = get_post_meta($id, 'wpcf-address1', true);
                    $city = get_post_meta($id, 'wpcf-city', true);
                    $state = get_post_meta($id, 'wpcf-state', true);
                    $link = home_url() . '/test2?store-id=' . $id;
                    // var_dump($img);
                    ?>
                    <div class='indppl-single-store-container'>
                        <div class='flex-half'>
                            <div class='indppl-store-thumb'>
                                <img src='<?php echo $img; ?>'>
                            </div>
                        </div>
                        <div class='flex-half flex-half-text'>
                            <h4 class='indppl-small-title'><?php echo $title; ?></h4>
                            <p class='indppl-small-store-text'><?php echo $address1; ?></p>
                            <p class='indppl-small-store-text'><?php echo $city . ', ' . $state; ?></p>
                            <a id='indppl-small-store-link' class='button button-primary indppl-small-store-link' href='<?php echo $link; ?>'>Edit</a>
                        </div>
                    </div>
                    <?php
                }
                wp_reset_postdata();
                // remove else to allow the add store link to always be active.
            }else{
                ?>
                <div class='indppl-add-store-container'>
                    <a class='indppl-add-store-link' href='<?php echo home_url() . "/test2/"; ?>'>
                        <div class='indppl-add-store-centered'>
                            <svg id='path' class="icon  icon--plus" viewBox="-52.5 -52.5 100 100" xmlns="http://www.w3.org/2000/svg">
                                <path d="M-5 -25 h5 v20 h20 v5 h-20 v20 h-5 v-20 h-20 v-5 h20 z" />
                            </svg>
                        </div>
                        <h4 class='indppl-add-store-text'>Add Store</h4>
                    </a>
                </div>
                <?php 
            }
        ?>
    </div>
    <?php
    $return = ob_get_clean();
    return $return;
}
add_shortcode('pp-my-stores', 'pp_my_stores');

function pp_store_containers(){
    $store_id = $_GET['store-id'];
    
    $args2 = array('post_id' => $store_id);
    $cons = types_child_posts('container', $args2);
    $relation_array = array();
    foreach($cons as $key => $value){
        array_push($relation_array, $value->ID);
    }

    // var_dump($relation_array);
    $int_args = array(
        'post_type' => 'store-container'
    );
    $int = new WP_Query($int_args);
    $int_array = array();
    // int array has the relation for containers and season for this store.
    if($int->have_posts()){
        while($int->have_posts()){
            $int->the_post();
            $int_id = get_the_ID();
            $post = get_post($int_id);
            $slug = $post->post_name;
            $slug_array = explode('-', $slug);
            $cont_id = $slug_array[count($slug_array)-1];
            // var_dump($cont_id);
            $int_meta = get_post_meta($int_id);
            $int_array[$cont_id] = array();
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
    // var_dump($int);
    ?>
    <form  method="post" action='#' id='container-select-form' class="form-horizontal" enctype="multipart/form-data">
        <input type='hidden' id='store-id' name='store-id' value='<?php echo $store_id; ?>'>
        <input type='hidden' id='user-status' name='user-status' value='<?php echo $user_status[0]; ?>'>
        <table class='indppl-containers-table'>
            <fieldset>
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
            </fieldset>
            <?php
            $args = array(
                'post_type' => 'container',
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => 'wpcf-default-container',
                        'compare' => 'EXISTS',                    
                    ),
                    array(
                        'key' => 'wpcf-default-container',
                        'compare' => 'NOT EXISTS',
                    ),
                ),
                'orderby' => array('meta_value' => 'ASC', 'date' => 'DESC'),
            );
            $containers = new WP_Query($args);
            // var_dump($containers);
            if($containers->have_posts()){
                while($containers->have_posts()){
                    $containers->the_post();
                    $id = get_the_ID();
                    $title = get_the_title();
                    $meta = get_post_meta($id, 'wpcf-default-container', true);
                    // if()
                    // var_dump($meta);
                    echo indppl_build_container_relation_output($id, $title, $relation_array, $int_array, $meta);
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