<?php
defined('ABSPATH') or die('No script kiddies please!'); //For security

function planting_pal_home($lat = NULL, $lon = NULL) {
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
                    <form action="<?php site_url();?>" method="post">
                    <input class="form-control rounded-input4" id='zip-for-location' type="text" name="zip" placeholder="Zipcode"><i class="material-icons" id="location-icon">my_location</i>
                    <button class="gradient-button">ENTER</button>
                    <input type="image" src="<?php echo INDPPL_ROOT_URL ?>assets/img/enter-geo.png" alt="Submit" border="0" class="geo-submit">
                    </form>
                </div>
            </div>
        </div>



    <?php
if (isset($_POST['lat'])) {
        $lat = $_POST['lat'];
        $lon = $_POST['lon'];
        $zip_array = geofind($lat, $lon);
    } else if (isset($_POST['zip'])) {
        $zip_array = geozip($_POST['zip']);
    }
    $top = ob_get_clean();
    ob_start();
    // var_dump($zip_array);
    ?><div class='store-list-container'> <?php
if ($zip_array) {

        $args = array(
            'post_type' => 'store',
            'meta_query' => array(
                array(
                    'key' => 'wpcf-zip',
                    'value' => $zip_array,
                    'compare' => 'IN',

                ),
            ),
        );
        $the_query = new WP_Query($args);
        // The Loop
        if ($the_query->have_posts()) {
            ?>
            <div class='flex-left-justify'><?php
$i = 0;
            while ($the_query->have_posts()) {
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
if ($is_pro[0] == 1) {
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
if (isset($_POST['lat'])) {
        $get_store = ob_get_clean();
        return $get_store;
    }

    ?>
        <script src="<?php echo INDPPL_ROOT_URL ?>assets/bootstrap/js/bootstrap.min.js"></script>
    </body>
    <!-- </html> -->
    <?php
$return = ob_get_clean();
    return $top . $return;
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

                var_dump($test);
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
