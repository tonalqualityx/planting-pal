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
				var_dump(get_post_meta($id));
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


