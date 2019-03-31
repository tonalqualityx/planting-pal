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

    // if(isset($_POST)){
        var_dump($_POST);
    // }

    // wp_handle_upload( $file, $overrides, $time );

    ob_start();
    ?>
    <h1>Welcome to Planting Pal!</h1>
    <p>We just need to get a few quick details to configure your store then you can begin building out your products and rates.</p>
    <form method="post" action='#' id='store-management-form' class="form-horizontal">
    <fieldset>
    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="store-name">Store Name</label>
      <div class="col-md-4">
      <input id="store-name" name="store-name" type="text" placeholder="" class="form-control input-md" required="">
    
      </div>
    </div>
    
    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="address1">Address Line 1</label>
      <div class="col-md-4">
      <input id="address1" name="address1" type="text" placeholder="" class="form-control input-md" required="">
    
      </div>
    </div>
    
    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="address2">Address Line 2</label>
      <div class="col-md-4">
      <input id="address2" name="address2" type="text" placeholder="" class="form-control input-md">
    
      </div>
    </div>
    
    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="city">City</label>
      <div class="col-md-4">
      <input id="city" name="city" type="text" placeholder="" class="form-control input-md" required="">
    
      </div>
    </div>
    
    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="state">State</label>
      <div class="col-md-1">
      <input id="state" name="state" type="text" placeholder="" class="form-control input-md" required="">
    
      </div>
    </div>
    
    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="zip">Zipcode</label>
      <div class="col-md-2">
      <input id="zip" name="zip" type="text" placeholder="" class="form-control input-md" required="">
    
      </div>
    </div>
    <?php
    // if ($grabList == '19'){
    ?>
    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="weburl">Store Website</label>
      <div class="col-md-4">
      <input id="weburl" name="weburl" type="text" placeholder="" class="form-control input-md">
    
      </div>
    </div>
    
    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="phone">Phone Number</label>
      <div class="col-md-4">
      <input id="phone" name="phone" type="text" placeholder="" class="form-control input-md" required="">
    
      </div>
    </div>
    
    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="store-email">Email Address</label>
      <div class="col-md-4">
      <input id="store-email" name="store-email" type="text" placeholder="" class="form-control input-md" required="">
    
      </div>
    </div>
    <?php
    // };
    
    ?>
    <?php
    // if ($grabList == '19'){
    ?>
    <!-- Prepended text-->
    <!-- <div class="form-group">
      <label class="col-md-4 control-label" for="purl">Pretty URL</label>
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-addon">https://m.plantingpal.com/</span>
          <input id="purl" name="purl" class="form-control" placeholder="yourname" type="text">
        </div>
    
      </div>
    </div> -->
    
    
    <!-- File Button -->
    <div class="form-group">
      <label class="col-md-4 control-label" for="logo">Store Logo</label>
      <div class="col-md-4">
        <input id="logo" name="logo" class="input-file" type="file">
      </div>
    </div>
    <?php
    // }
    ?>
    <!-- Button -->
    <div class="form-group">
      <label class="col-md-4 control-label" for="submit"></label>
      <div class="col-md-4">
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>
        <!-- <button id="submit" name="submit" class="btn btn-primary">Save Information</button> -->
      </div>
    </div>
    <input type="hidden" name="setup" value="2">
    </fieldset>
    </form>

    <script type="text/javascript">

    $(function()
    {
        $('#logo').on('change',function ()
        {
            var filePath = $(this).val();
            console.log(filePath);
        });
    });

    </script>

    <?php
    $return = ob_get_clean();
    return $return;
}
add_shortcode('pp-store-management', 'pp_store_management');