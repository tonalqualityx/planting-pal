<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security
function indppl_planting_pal_home_ajax(){
    if(isset($_POST['lat'])){
        $lat = $_POST['lat'];
        $lon = $_POST['lon'];

        // do_shortcode('[planting_pal_home]');
        $return = planting_pal_home($lat, $lon);
        echo $return;
    }
    // var_dump($_POST);
    die();
}
add_action( 'wp_ajax_indppl_planting_pal_home_ajax', 'indppl_planting_pal_home_ajax' );
add_action('wp_ajax_nopriv_indppl_planting_pal_home_ajax', 'indppl_planting_pal_home_ajax');

function indppl_switch_live_ajax(){
    if(isset($_POST['id'])){
        $store_id = $_POST['id'];
        $status = get_post_meta($store_id, 'wpcf-issetup', true);
        if($status){
            update_post_meta($store_id, 'wpcf-issetup', 0);
            echo 0;
        }else {
            update_post_meta( $store_id, 'wpcf-issetup', 1);
            echo 1;
        }
    }
    die();
}
add_action( 'wp_ajax_indppl_switch_live_ajax', 'indppl_switch_live_ajax' );
add_action('wp_ajax_nopriv_indppl_switch_live_ajax', 'indppl_switch_live_ajax');

function indppl_save_container_data_ajax(){
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['date'])){
        $date = $_POST['date'];
        foreach($date as $key => $value){
            $name = 'wpcf-' . $value['name'];
            $val = $value['value'];
            update_post_meta($store_id, $name, $val);
        }
    }
    $non_default = [];
    $available = [];
    $not_available = [];
    $default = [];
    $new_array = [];
    $send_back_arrary = [];
    
    // var_dump(get_post_meta($store_container_relations[1]));
    // if(isset($_POST['default_container'])){
        if(isset($_POST['non_default'])){
            $non_default = $_POST['non_default'];
        }
        if(isset($_POST['available'])){
            $available = $_POST['available'];
        }
        if(isset($_POST['not_available'])){
            $not_available = $_POST['not_available'];
        }
        if(isset($_POST['default_container'])){
            $default = $_POST['default_container'];
        }
        // var_dump($_POST);
        if(isset($_POST['new_array'])){
            $new_array = $_POST['new_array'];
            // var_dump($new_array);
            foreach($new_array as $key => $value){
                $container_id = indppl_create_container($new_array[$key]);
                $send_back_arrary[$key] = $container_id;
                $cont_avail = toolset_connect_posts('store-container', $store_id, $container_id);
                // var_dump($cont_avail);
                $build_array = array(0 => array());
                $cont_array = array(0 => $cont_avail['intermediary_post']);
                foreach($new_array[$key] as $title => $season){
                    if($title == 'name'){
                    }else{
                        $build_array[0]['name'] = $container_id . "-" . $season;
                        // var_dump($build_array);
                        // var_dump($cont_array);
                        indppl_add_relation($build_array, $cont_array);

                    }
                }
            }
        }
        // var_dump($available);
        foreach($available as $key => $value){
            $cont_avail = toolset_connect_posts('store-container', $store_id, $value);
            // var_dump($cont_avail);
        }
        foreach($not_available as $key => $value){
            $not_avail = toolset_disconnect_posts('store-container', $store_id, $value);
            // var_dump($not_avail);
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

        
        // foreach ($default as $key => $value) {
        //     $name = explode("-", $value['name']);
        //     $id = $name[0];
        //     $season ='';
        //     // var_dump(get_post_meta($id));
        //     if($name[1] == 'spring'){
        //         $season = 'wpcf-available-in-spring';
        //     }
        //     else if ($name[1] == 'summer'){
        //         $season = 'wpcf-available-in-summer';
        //     }
        //     else if ($name[1] == 'fall'){
        //         $season = 'wpcf-available-in-fall';
        //     }
        //     else if ($name[1] == 'winter'){
        //         $season = 'wpcf-available-in-winter';
        //     }
        //     foreach($store_container_relations as $key2 => $rel_val){
        //         $container = get_post($rel_val);
        //         $name = $container->post_name;
        //         $name_array = explode('-', $name);
        //         $cont_id = $name_array[count($name_array)-1];
        //         if($id == $cont_id){
        //             $test = update_post_meta($rel_val, $season, '1');
        //             var_dump($test);
        //         }
        //     } 
        // }
        indppl_add_relation($default, $store_container_relations);
        indppl_add_relation($non_default, $store_container_relations);
        

        $remove_dot = $_POST['remove_dot'];
        // var_dump($remove_dot);
        foreach($remove_dot as $key => $value){
            // var_dump($value);
            $name = explode("-", $value);
            $id = $name[0];
            $season ='';
            // var_dump(get_post_meta($id));
            if($name[1] == 'spring'){
                $season = 'wpcf-available-in-spring';
            }
            else if ($name[1] == 'summer'){
                $season = 'wpcf-available-in-summer';
            }
            else if ($name[1] == 'fall'){
                $season = 'wpcf-available-in-fall';
            }
            else if ($name[1] == 'winter'){
                $season = 'wpcf-available-in-winter';
            }
            foreach($store_container_relations as $key2 => $rel_val){
                $container = get_post($rel_val);
                $name = $container->post_name;
                $name_array = explode('-', $name);
                $cont_id = $name_array[count($name_array)-1];
                if($id == $cont_id){
                    $test = delete_post_meta($rel_val, $season);
                    // var_dump($test);
                }
            } 
        }
        // $v;
        // foreach($available as $key => $value){
        //     $v = array_keys($default, $value);
        //     // if(in_array($value, $default[$key]['id']){
        //     //     echo $default[$key]['id'];
        //     // }
        //     var_dump($v);
        // }
        // echo json_encode($send_back_array);
    // }
    die();
}
add_action( 'wp_ajax_indppl_save_container_data_ajax', 'indppl_save_container_data_ajax' );
add_action('wp_ajax_nopriv_indppl_save_container_data_ajax', 'indppl_save_container_data_ajax');


function indppl_add_new_product_ajax(){
    $return = indppl_get_product_info();
    echo $return;
    die();
}
add_action( 'wp_ajax_indppl_add_new_product_ajax', 'indppl_add_new_product_ajax' );
add_action('wp_ajax_nopriv_indppl_add_new_product_ajax', 'indppl_add_new_product_ajax');

function indppl_get_products_by_brand_ajax(){
    if(isset($_POST['brand'])){
        $brand = $_POST['brand'];
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    $args = array(
        'post_type' => 'product',
        'tax_query' => array(
            array(
                'taxonomy' => 'brand',
                'field'    => 'slug',
                'terms'    => $brand,
            ),
        ),
        'relation' => 'OR',
        array(
            'author' => get_current_user_id(),
            'meta_query' => array(
                array(
                    'key' => 'wpcf-default',
                    'value' => 1,
                    'compare' => '=',
                ),
            ),
        ),
    );
    $products = new WP_Query($args);
    // var_dump($products);
    ob_start();

    ?> <option class='product-create-product-option' value='' disabled selected>Select Product</option> <?php
    if($products->have_posts()){
        while($products->have_posts()){
            $products->the_post();
            $title = get_the_title();
            $id = get_the_id();
            ?>
            <option value="<?php echo $id; ?>"><?php echo $title; ?></option>
            <?php
        }
    }
    echo ob_get_clean();
    die();
}
add_action( 'wp_ajax_indppl_get_products_by_brand_ajax', 'indppl_get_products_by_brand_ajax' );
add_action('wp_ajax_nopriv_indppl_get_products_by_brand_ajax', 'indppl_get_products_by_brand_ajax');

function indppl_get_product_info_ajax(){
    if(isset($_POST['product_id'])){
        $product_id = $_POST['product_id'];
    }
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    if(isset($_POST['edit'])){
        $container = indppl_get_product_info();
    }
    
    if($product_id == 'new'){
        $default = 0;
        $unit = 'oz';
        $dryliquid = 'dry';
    }else{
        $default = get_post_meta($product_id, 'wpcf-default', true);
        $unit = get_post_meta($product_id, 'wpcf-unit', true);
        $fivecups = get_post_meta($product_id, 'wpcf-5cups', true);
        $dryliquid = get_post_meta($product_id, 'wpcf-dryliquid', true);
    }

    $send_array = array();
    if($default){
        $standard_unit = "<div id='product-create-standard-unit' data-unit='" . $unit . "'></div>";
    }else{
        ob_start();
        ?>
        <h3 class='product-create-dry-wet-title'>Select Dry or Wet for this product</h3>
        <input type='radio' class='product-create-dry-wet' name='product-create-dry-wet' id='product-create-dry' <?php if($dryliquid == 'dry'){ ?>checked<?php }?> value='dry' >Dry
        <input type='radio' class='product-create-dry-wet' name='product-create-dry-wet' id='product-create-wet' <?php if($dryliquid == 'wet'){ ?>checked<?php }?> value='wet' >Wet
        <?php
        $dry_wet = ob_get_clean();
        ob_start();
        ?>
        <select class='product-create-standard-unit' id='product-create-standard-unit' name='product-create-standard-unit'>
            <option class='product-create-standard-unit-option' value='<?php echo $unit; ?>' selected><?php echo $unit; ?></option>
        </select>
        <?php
        $standard_unit = ob_get_clean();
    }
    $product_related = [];
    // getting sizes
    if($product_id != 'new'){
        $product_related = toolset_get_related_posts(
            $product_id,
            'product-package',
            'parent',
            '100',
            '0',
            array(),
            'post_id',
            'child'
        );
    }
    $store_related = toolset_get_related_posts(
        $store_id,
        'store-package',
        'parent',
        '100',
        '0',
        array(),
        'post_id',
        'child'
    );
    $sizes = '';
    $pack_units[] = [$fivecups, 'lb'];
    ob_start();
    ?>
    <h3>Select the sizes you stock:</h3>
    <?php
    if($product_related && $product_id != 'new'){
        foreach ($product_related as $key => $value) {
            $size_meta = get_post_meta($value, 'wpcf-size', true);
            $unit_meta = get_post_meta($value, 'wpcf-unit', true);
            // $pack_units[] = [$size_meta, $unit_meta];
            $author = get_post_field( 'post_author', $value );
            $default_package = get_post_meta($value, 'wpcf-default-package', true);
            if($author == get_current_user_id() || $default_package){
                
                // echo $store_id;
                $non_default = '';
                if(!$default_package){
                    $non_default = 'indppl-non-default-package';
                }
                $in_store = '';
                if(in_array($value, $store_related)){
                    $in_store = 'indppl-background-green';
                }
                // echo $author;
                // echo $default_package;
                
                ?>
                
                <a href='#' class='<?php echo $in_store . " " . $non_default; ?> indppl-product-create-size-btn' data-size='<?php echo $size_meta; ?>' data-unit='<?php echo $unit_meta;?>' data-id='<?php echo $value; ?>'><?php echo $size_meta . " " . $unit_meta; ?></a>
                <?php
                
            }


        }
    }
    $sizes .= ob_get_clean();

    ob_start();
    ?>
    <h3>Create a new size:</h3>
    <div class='indppl-product-create-size-num-inside-container'>
        <input type='number' class='indppl-product-create-size-num' id='indpll-product-create-size-num' min='0' name='indppl-product-create-size-num'>
        <select class='product-create-standard-unit-add' id='product-create-standard-unit-add' name='product-create-standard-unit-add'>
            <option class='product-create-standard-unit-add-option' value='' disabled selected>Select Unit</option>
        </select>
        <a href='#' id='indppl-product-create-new-size-btn' class='indppl-button'>Create</a>
    </div>
    <?php
    $new_size .= ob_get_clean();

    $cups_active = false;
    // $app_rates_active = false;
    if($dryliquid == 'dry' && !$default){
        
        foreach($pack_units as $key => $v){
            // if($v[1] == 'lb' || $v[1] == 'g' || $v[1] == 'kg' || $v[1] == 'oz'){
            //     $app_rates_active = true;
            // }
            // $console[] = intval($v[0]);
            if(($v[1] == 'lb' && intval($v[0]) > 2.2) || ($v[1] == 'g' && intval($v[0]) > 997.9) || ($v[1] == 'kg' && intval($v[0]) > 0.998) || ($v[1] == 'oz' && intval($v[0]) > 35.2)){
                $cups_active = true;
            }
        }
    }
    
    if($cups_active || $product_id == 'new'){
        ob_start();
        $weight_array = ['lb', 'g', 'kg', 'oz'];
        ?>
        <h3>How much does 5 level coups of this product weigh?</h3>
        <div class='product-create-5-cups-inside-container'>
            <input type='number' class='indppl-product-create-cups-num' id='indpll-product-create-cups-num' min='0' name='indppl-product-create-cups-num' value='<?php echo $fivecups; ?>'>
            <select class='product-create-5-cups' id='product-create-5-cups' name='product-create-5-cups'>
                <!-- <option class='product-create-5-cups-option' value='' disabled selected>Select Unit</option> -->
                <?php
                foreach($weight_array as $weight_unit){
                    if($weight_unit == $finecups[0][1]){
                        $selected_unit = 'selected';
                    }
                    ?>
                    <option class='product-create-5-cups-option' value='<?php echo $weight_unit; ?>' <?php echo $selected_unit; ?> >
                    <?php echo $weight_unit; ?>
                    </option>
                <?php
                }
                ?>
            </select>
        </div>
        <?php
        // $console = $unit;
        
        $cups = ob_get_clean();
    }

    // app rates chart container
    if($product_id != 'new'){
        $app_rates_chart = update_package_table($store_id, $product_id, $type);
    }

    ob_start();
    if($type == 'pots'){
        ?>
        <input type="submit" name="product-create-pots-next" data-exit="true" id="product-create-pots-next" class="product-create-pots-submit" value="Next">
        <?php
    }else{
        ?>
        <input type="submit" name="product-create-next" data-exit="true" id="product-create-next" class="product-create-submit" value="Next">
        <?php

    }
    $next_btn = ob_get_clean();

    if($product_id == 'new'){
        ob_start();
        ?>
        <input type='text' class='indppl-add-product-name' name='indppl-add-product-name' placeholder='Product Name'>
        <?php
        $add_product = ob_get_clean();
    }
    if($type == 'pots'){
        ob_start();
        $filler = get_post_meta($product_id, 'wpcf-use-blended-filler');
        $additive = get_post_meta($product_id, 'wpcf-use-blended-additive');
        $surface = get_post_meta($product_id, 'wpcf-use-surface');
        ?>
        <div class='indppl-add-product-usage-type'>
            <h3>Select Usage Type (check all that apply)</h3>
            <div>
                <input type='checkbox' name='indppl-add-product-bulk-filler' class='indppl-add-usage-type-check' id='indppl-add-product-bulk-filler' <?php if($filler){ ?>checked<?php }?>>
                <label for='indppl-add-product-bulk-filler'>Bulk Filler/Substrate(ie Potting Soil)</label>
            </div>
            <div>
                <input type='checkbox' name='indppl-add-product-additive-blend' class='indppl-add-usage-type-check' id='indppl-add-product-additive-blend' <?php if($additive){ ?>checked<?php }?>>
                <label for='indppl-add-product-additive-blend'>Additive Blended In thie Potting Soil</label>
            </div>
            <div>
                <input type='checkbox' name='indppl-add-product-additive-surface' class='indppl-add-usage-type-check' id='indppl-add-product-additive-surface' <?php if($surface){ ?>checked<?php }?>>
                <label for='indppl-add-product-additive-surface'>Additive Surface Applied after planting</label>
            </div>
        </div>
        <?php
        $usage_type = ob_get_clean();
    }
    ob_start();
    $fraction = get_post_meta($product_id, 'wpcf-fraction', true);
    ?>
    <div class='indppl-add-product-fraction-bag'>
    <h3 class='product-create-fraction-bag-title'>When you recommend apply this product, is it by:</h3>
        <input type='checkbox' class='product-create-fraction-bag' name='product-create-fraction-bag' id='product-create-fraction-bag' <?php if($fraction){ ?>checked<?php }?> value='1' >Fraction of a bag
    </div>
    <?php
    $fraction_bag = ob_get_clean();
    
    $send_array['standard_unit'] = $standard_unit;
    $send_array['dry_wet'] = array(0 => $dry_wet, 1 => $dryliquid, 2=> $unit);
    $send_array['size'] = $sizes;
    $send_array['new_size'] = $new_size;
    // $send_array['app_rate'] = $app_rate;
    $send_array['cups'] = $cups;
    $send_array['app_rates_chart'] = $app_rates_chart;
    $send_array['next_btn'] = $next_btn;
    $send_array['fraction'] = $fraction_bag;
    $send_array['default'] = $default;
    $send_array['console'] = $console;
    if($container){
        $send_array['container'] = $container;
        $brand = get_the_terms($product_id, 'brand', true);
        $send_array['brand'] = $brand[0]->name;
        $send_array['product'] = get_the_title($product_id);
    }
    if($product_id == 'new'){
        $send_array['new_product'] = $add_product;
    }
    if($type == 'pots'){
        $send_array['usage_type'] = $usage_type;
    }
    echo json_encode($send_array);
    die();
}
add_action( 'wp_ajax_indppl_get_product_info_ajax', 'indppl_get_product_info_ajax' );
add_action('wp_ajax_nopriv_indppl_get_product_info_ajax', 'indppl_get_product_info_ajax');

function indppl_save_product_ajax(){
    if(isset($_POST['product_id'])){
        $product_id = $_POST['product_id'];
    }
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    if(isset($_POST['brand'])){
        $brand = $_POST['brand'];
    }
    if(isset($_POST['product_input'])){
        $product_rate = $_POST['product_input'];
    }
    if(isset($_POST['product_select'])){
        $product_unit = $_POST['product_select'];
    }
    if(isset($_POST['package_array'])){
        $package_array = $_POST['package_array'];
    }
    if(isset($_POST['package_remove'])){
        $package_remove = $_POST['package_remove'];
    }
    if(isset($_POST['new_pack'])){
        $new_pack = $_POST['new_pack'];
    }
    if(isset($_POST['product_name'])){
        $product_name = $_POST['product_name'];
    }
    if(isset($_POST['product_dryliquid'])){
        $product_dryliquid = $_POST['product_dryliquid'];
    }
    if(isset($_POST['cups_num'])){
        $cups_num = $_POST['cups_num'];
    }
    if(isset($_POST['cups_unit'])){
        $cups_unit = $_POST['cups_unit'];
    }
    if(isset($_POST['fraction'])){
        $fraction = $_POST['fraction'];
    }
    $console = $cups_num;
    if($product_id == 'new'){
        $new_product_args = array(
            'post_type' => 'product',
            'post_author' => get_current_user_id(),
            'post_title' => $product_name,
            'post_status' => 'publish',
            'meta_input' => array(
                'wpcf-dryliquid' => $product_dryliquid,
                'wpcf-unit' => $new_pack[count($new_pack)-1]['unit'],
                'wpcf-5cups' => $cups_num,
                'wpcf-5cups-unit' => $cups_unit,
            ),
        );
        $product_id = wp_insert_post($new_product_args);
        wp_set_object_terms($product_id, $brand, 'brand');
    }


    $send_array = array($product_id => array());
    foreach($product_rate as $key => $value){
        $temp = array(
                'unit' => $product_unit[$key]['value'],
                'amount' => $value['value'],
        );
        $send_array[$product_id]['containers'][$value['name']] = $temp;
    }
    if($product_rate){
        // $console = $send_array;
        $save = indppl_apprates($store_id, $type, $send_array);
        
    }
    // var_dump($package_array);
    // var_dump($new_pack);
    foreach($new_pack as $key => $value){
        $new_id = indppl_create_package($value);
        $new_package = toolset_connect_posts('store-package', $store_id, $new_id);
        $prod_pack = toolset_connect_posts('product-package', $product_id, $new_id);
        // var_dump($new_package);
    }
    foreach($package_array as $package_id){
        if(!$package_id == 0){
            $new_package = toolset_connect_posts('store-package', $store_id, $package_id);
        }
        // var_dump($new_package);
    }
    foreach($package_remove as $pack_id){
        if(is_array($pack_id)){
            // var_dump('inssidldke');
            wp_delete_post($pack_id['id']);
        }
        else{
            $remove = toolset_disconnect_posts('store-package', $store_id, $pack_id);
        }
    }
    $console = $fraction;
    if($fraction == 'true'){
        update_post_meta($product_id, 'wpcf-fraction', 1);
        $updated_app_rates = update_bag_package_table($store_id, $product_id, $type);
    }else{
        delete_post_meta($product_id, 'wpcf-fraction');
        $updated_app_rates = update_package_table($store_id, $product_id, $type);
    }
    $ajax_array =[];
    $ajax_array['app_rates'] = $updated_app_rates;
    $ajax_array['product_id'] = $product_id;
    $ajax_array['dryliquid'] = $product_dryliquid;
    $ajax_array['console'] = $console;
    echo json_encode($ajax_array);
    die();
}
add_action( 'wp_ajax_indppl_save_product_ajax', 'indppl_save_product_ajax' );
add_action('wp_ajax_nopriv_indppl_save_product_ajax', 'indppl_save_product_ajax');

function indppl_product_save_exit_ajax(){
    echo do_shortcode('[pp-store-products]');
    die();
}
add_action( 'wp_ajax_indppl_product_save_exit_ajax', 'indppl_product_save_exit_ajax' );
add_action('wp_ajax_nopriv_indppl_product_save_exit_ajax', 'indppl_product_save_exit_ajax');


function indppl_remove_package_from_store_ajax(){
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    if(isset($_POST['product_id'])){
        $product_id = $_POST['product_id'];
    }
    $args = array(
        $type => $product_id,
    );
    indppl_delete_apprate($store_id, $args);
    $default = get_post_meta($product_id, 'wpcf-default', true);
    $product_related = toolset_get_related_posts(
        $product_id,
        'product-package',
        'parent',
        '100',
        '0',
        array(),
        'post_id',
        'child'
    );
    $store_related = toolset_get_related_posts(
        $store_id,
        'store-package',
        'parent',
        '100',
        '0',
        array(),
        'post_id',
        'child'
    );
    foreach($product_related as $package){
        if(in_array($package, $store_related)){
            $test = toolset_disconnect_posts('store-package', $store_id, $package);
            if(!get_post_meta($package, 'wpcf-default-package', true)){
                wp_delete_post($package);
            }
        }
    }
    if(!$default){
        wp_delete_post($product_id);
    }
    var_dump($test);
    die();
}
add_action( 'wp_ajax_indppl_remove_package_from_store_ajax', 'indppl_remove_package_from_store_ajax' );
add_action('wp_ajax_nopriv_indppl_remove_package_from_store_ajax', 'indppl_remove_package_from_store_ajax');


// Setup the guide form 
function indppl_setup_guide_forms_ajax(){

    $form = $_POST['form'];
    $defaults = get_posts(array("post_type" => "guide-defaults", 'meta_key' => 'wpcf-guide-type', 'meta_value' => $form));
    $defaults = $defaults[0];
    
    switch($form){
        case 'ground' :

            break;
        case 'pots':

            break;

        case 'beds':

            break;

    }

    ob_start(); ?>

    <div class="container">
        <h2></h2>
        <?php var_dump($defaults); ?>
    </div>

    <?php echo ob_get_clean();
    die();
}

add_action('wp_ajax_indppl_setup_guide_forms_ajax', 'indppl_setup_guide_forms_ajax');
add_action('wp_ajax_nopriv_indppl_setup_guide_forms_ajax', 'indppl_setup_guide_forms_ajax');

function indppl_update_app_rates_ajax(){
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['product_id'])){
        $product_id = $_POST['product_id'];
    }
    if(isset($_POST['brand'])){
        $brand = $_POST['brand'];
    }
    if(isset($_POST['current_pack'])){
        $current_pack = $_POST['current_pack'];
    }
    if(isset($_POST['container_id'])){
        $container_id = $_POST['container_id'];
    }
    if(isset($_POST['container_num'])){
        $container_num = $_POST['container_num'];
    }
    if(isset($_POST['container_unit'])){
        $container_unit = $_POST['container_unit'];
    }
    $cups = get_post_meta($product_id, 'wpcf-5cups', true);
    $update_array = [];
    $items = array(
        array(
            'unit' => $container_unit,
            'amount' => $container_num,
        ),
    );
    // var_dump($cups);
    // var_dump($container_unit);
    foreach($current_pack as $key => $value){
        $conversion = indppl_normalize($items, $value['unit'], intval($cups));
        // var_dump($items);
        // var_dump($value['unit']);
        // var_dump($cups);
        $final = $value['size'] / $conversion[0]['standard-amount'];
        $update_array[] = round($final, 2);
    }
    $send_array = [];
    $send_array['app_rates'] = $update_array;
    echo json_encode($send_array);
    // var_dump($send_array);
    die();
}
add_action( 'wp_ajax_indppl_update_app_rates_ajax', 'indppl_update_app_rates_ajax' );
add_action('wp_ajax_nopriv_indppl_update_app_rates_ajax', 'indppl_update_app_rates_ajax');

function indppl_save_pots_product_ajax(){
    if(isset($_POST['product_id'])){
        $product_id = $_POST['product_id'];
    }
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    if(isset($_POST['brand'])){
        $brand = $_POST['brand'];
    }
    if(isset($_POST['product_input'])){
        $product_rate = $_POST['product_input'];
    }
    if(isset($_POST['product_select'])){
        $product_unit = $_POST['product_select'];
    }
    if(isset($_POST['package_array'])){
        $package_array = $_POST['package_array'];
    }
    if(isset($_POST['package_remove'])){
        $package_remove = $_POST['package_remove'];
    }
    if(isset($_POST['new_pack'])){
        $new_pack = $_POST['new_pack'];
    }
    if(isset($_POST['product_name'])){
        $product_name = $_POST['product_name'];
    }
    if(isset($_POST['product_dryliquid'])){
        $product_dryliquid = $_POST['product_dryliquid'];
    }
    if(isset($_POST['cups_num'])){
        $cups_num = $_POST['cups_num'];
    }
    if(isset($_POST['cups_unit'])){
        $cups_unit = $_POST['cups_unit'];
    }
    if(isset($_POST['fraction'])){
        $fraction = $_POST['fraction'];
    }
    if(isset($_POST['filler'])){
        $filler = $_POST['filler'];
        var_dump($filler);
    }
    if(isset($_POST['blend'])){
        $blend = $_POST['blend'];
        var_dump($blend);
    }
    if(isset($_POST['surface'])){
        $surface = $_POST['surface'];
        var_dump($surface);
    }
    if($product_id == 'new'){
        $new_product_args = array(
            'post_type' => 'product',
            'post_author' => get_current_user_id(),
            'post_title' => $product_name,
            'post_status' => 'publish',
            'meta_input' => array(
                'wpcf-dryliquid' => $product_dryliquid,
                'wpcf-unit' => $new_pack[count($new_pack)-1]['unit'],
                'wpcf-5cups' => $cups_num,
                'wpcf-5cups-unit' => $cups_unit,
            ),
        );
        $product_id = wp_insert_post($new_product_args);
        wp_set_object_terms($product_id, $brand, 'brand');
    }


    $send_array = array($product_id => array());
    // foreach($product_rate as $key => $value){
        $temp = array();
        if($filler == 'true'){
            $temp['filler'] = array();
        }
        if($blend == 'true'){
            $temp['blended'] = array();

        }
        if($surface == 'true'){
            $temp['surface'] = array();
        }
        $send_array[$product_id] = $temp;
    // }
    if($product_rate){
        // var_dump($type);
        var_dump($send_array);
        $save = indppl_apprates($store_id, $type, $send_array);
        // $console = $save;
    }
    // var_dump($package_array);
    // var_dump($new_pack);
    foreach($new_pack as $key => $value){
        $new_id = indppl_create_package($value);
        $new_package = toolset_connect_posts('store-package', $store_id, $new_id);
        $prod_pack = toolset_connect_posts('product-package', $product_id, $new_id);
        // var_dump($new_package);
    }
    foreach($package_array as $package_id){
        if(!$package_id == 0){
            $new_package = toolset_connect_posts('store-package', $store_id, $package_id);
        }
        // var_dump($new_package);
    }
    foreach($package_remove as $pack_id){
        if(is_array($pack_id)){
            // var_dump('inssidldke');
            wp_delete_post($pack_id['id']);
        }
        else{
            $remove = toolset_disconnect_posts('store-package', $store_id, $pack_id);
        }
    }
    // $console = $fraction;
    if($fraction == 'true'){
        update_post_meta($product_id, 'wpcf-fraction', 1);
        // $updated_app_rates = update_bag_package_table($store_id, $product_id, $type);
    }else{
        delete_post_meta($product_id, 'wpcf-fraction');
        // $updated_app_rates = update_package_table($store_id, $product_id, $type);
    }
    $ajax_array =[];
    // $console = $product_rate;
    // $ajax_array['app_rates'] = $updated_app_rates;
    // $ajax_array['product_id'] = $product_id;
    // $ajax_array['dryliquid'] = $product_dryliquid;
    // $ajax_array['console'] = $console;
    // echo json_encode($ajax_array);
    die();
}
add_action( 'wp_ajax_indppl_save_pots_product_ajax', 'indppl_save_pots_product_ajax' );
add_action('wp_ajax_nopriv_indppl_save_pots_product_ajax', 'indppl_save_pots_product_ajax');

function indppl_get_pot_apprates_ajax(){
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    $app_rates = indppl_apprates($store_id);
    $num = 0;
    foreach($app_rates['pots'] as $key => $value){
        if(isset($value['filler'])){
            $num++;
        }
    }
    $ind = floor(100 / $num);
    $count = 100 - ($ind * $num);
    $percent_array = array();
    while($num > 0){
        if($count > 0){
            $temp = $ind + 1;
            $count--;
        }else{
            $temp = $ind;
        }
        $percent_array[] = $temp;
        $num--;
    }

    ob_start();
    ?>
    <div class='pots-apprates-container'>
        <a href='#' class='modal-close'>X</a>
        <h2>Pots / Containers Application Rates</h2>
        <p>Bulk Filler / Substrate(ie Potting Soil)</p>
        <p>Enter the percentage of each product to be used. Percentages must total 100%.</p>
        <table class='pots-apprates-filler-container'>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th class='max-width-500'>Primary Filler - If the amount recommended is small and split between two filler/substrates, which one product would you recommend?</th>
            </tr>
        <?php
        $counter = 0;
        foreach($app_rates['pots'] as $key => $value){
            if(isset($value['filler'])){
                $title = get_the_title($key);
                $brand = get_the_terms($key, 'brand', true);
                $brand = $brand[0]->name;
                // var_dump($brand);
                ?>
                <tr class='pots-apprates-filler-inside-container'>
                    <td class='pots-apprates-filler-cell'>
                        <input type='number' min='0' max='100' name='filler-<?php echo $key; ?>' class='pots-apprates-filler' value='<?php echo $percent_array[$counter]; ?>'>
                    </td>
                    <td class='pots-apprates-filler-cell'>
                        <span class='pots-apprates-filler-percent'>%</span>
                    </td>
                    <td class='pots-apprates-filler-cell'>
                        <img class='height-50' src="https://via.placeholder.com/100.png">
                    </td>
                    <td class='pots-apprates-filler-cell'>
                        <div class='pots-apprates-brand-title'>
                            <h4 class='pots-apprates-brand'><?php echo $brand; ?></h4>
                            <h3 class='pots-apprates-title'><?php echo $title; ?></h3>
                        </div>

                    </td>
                    <td class=''>
                        <input type='radio' class='pots-apprates-filler-radio' name='pots-apprates-filler-radio'>
                    </td>
                </tr>
                <?php
                $counter++;
            }

        }
        ?>
        <tr>
            <td>
                <div class='pots-apprates-filler-total color-red'>0</div>
            </td>
            <td>
                <div class='pots-apprates-filler-percent'>%</div>
            </td>
            <td>

            </td>
            <td>
                <div class='pots-apprates-filler-message color-red'>
                    <p>Oops! This mix doesn't add up to 100%.</p>
                    <p>Please check your numbers and try again.</p>
                </div>
            </td>
        </tr>
        </table>
        
    </div>
    <?php
    $return = ob_get_clean();
    echo $return;
    die();
}
add_action( 'wp_ajax_indppl_get_pot_apprates_ajax', 'indppl_get_pot_apprates_ajax' );
add_action('wp_ajax_nopriv_indppl_get_pot_apprates_ajax', 'indppl_get_pot_apprates_ajax');
