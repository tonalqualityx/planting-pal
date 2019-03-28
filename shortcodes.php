<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security
function planting_pal_home(){

    ob_start();
    ?>
    <!-- <!DOCTYPE html>
    <html> -->

    <!-- <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>Planting Pal</title>
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#488A40">
        <meta name="msapplication-TileColor" content="#488a40">
        <meta name="theme-color" content="#488a40">
    </head> -->
    <script>
    function getLocation() {
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;

        document.location = "?lat=" + lat + "&lon=" + lon;
        });
    }
    }
    </script>
    <body class="location-body ppl-green-bg">
        <!-- <div class="desktopWarning">
            <p class="desktopWarning-p">This site is optimized for mobile phones in portrait layout.</p><i class="material-icons d-block portrait-only">screen_lock_portrait</i></div> -->
        <div class="container">
            <div class="row wizard-start">
                <div class="col"><img src="<?php echo INDPPL_ROOT_URL ?>assets/img/wizard-location.png"></div>
            </div>
                <div class="row search-form">
                    <div class="col">
                    <form action="<?php site_url(); ?>" method="post">
                    <input class="form-control rounded-input4" type="text" name="zip" placeholder="Zipcode"><i class="material-icons" onclick="getLocation()" id="location-icon">my_location</i>
                    <input type="image" src="<?php echo INDPPL_ROOT_URL ?>assets/img/enter-geo.png" alt="Submit" border="0" class="geo-submit">
                    </form>
                </div>
            </div>
        </div>

  
    <?php
    if(isset($_GET['lat'])){
        $lat = $_GET['lat'];
        $lon = $_get['lon'];
        $zip_array = geofind($lat, $lon);
    }else if(isset($_POST['zip'])){
        $zip_array = geozip($_POST['zip']);
    }
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
        // var_dump($zip_array);
        $the_query = new WP_Query( $args );
        // The Loop
        if ( $the_query->have_posts() ) {
            echo '<ul>';
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                echo '<li>' . get_the_title() . '</li>';
            }
            echo '</ul>';
            /* Restore original Post Data */
            wp_reset_postdata();
        } else {
            // no posts found
        }
    }

    ?>
        <script src="<?php echo INDPPL_ROOT_URL ?>assets/js/jquery.min.js"></script>
        <script src="<?php echo INDPPL_ROOT_URL ?>assets/bootstrap/js/bootstrap.min.js"></script>
    </body>
    <!-- </html> -->
    <?php
    $return = ob_get_clean();
    return $return;
}
add_shortcode( 'planting-pal-home', 'planting_pal_home' );