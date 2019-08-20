<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security
function indppl_planting_pal_home_ajax(){
    if(isset($_POST['lat'])){
        $lat = $_POST['lat'];
    }
    if(isset($_POST['lon'])){
        $lon = $_POST['lon'];
    }
    if(isset($_POST['radius'])){
        $radius = $_POST['radius'];
    }
    if(isset($_POST['zip'])){
        $zip = $_POST['zip'];
    }
    $return = planting_pal_home($lat, $lon, $radius, $zip);
    

    // do_shortcode('[planting_pal_home]');
    echo $return;
    // var_dump($_POST);
    die();
}
add_action( 'wp_ajax_indppl_planting_pal_home_ajax', 'indppl_planting_pal_home_ajax' );
add_action('wp_ajax_nopriv_indppl_planting_pal_home_ajax', 'indppl_planting_pal_home_ajax');

function indppl_switch_live_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['id'])){
        $store_id = $_POST['id'];
        $status = get_post_meta($store_id, 'wpcf-issetup', true);
        if($status){
            update_post_meta($store_id, 'wpcf-issetup', 0);
            echo 0;
            indppl_notify_new_store($store_id, get_current_user_id(  ) );
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
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
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
                $send_back_array[$key] = $container_id;
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
        // $available_and_new = array_merge($available, );

        
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
        // $store_container_relations = toolset_get_related_posts(
        //     $store_id, // get posts related to this one
        //     'store-container', // relationship between the posts
        //     'parent',
        //     '100',
        //     '0',
        //     array(),
        //     'post_id',
        //     'child'
        // );
        $app_rates = indppl_apprates($store_id);
        $update = [];
        if(!empty($app_rates['ground'])){
            foreach($app_rates['ground'] as $pro_id => $product){
                $update[$pro_id] = false;
                foreach($available as $key => $container){
                    if(!array_key_exists($container, $app_rates['ground'][$pro_id]['containers']) && !array_key_exists($container, $app_rates['ground'][$pro_id]['bag'])){
                        $update[$pro_id] = true;
                    }
                }
            }
        }
        update_post_meta($store_id, 'wpcf-apprate-update', json_encode($update));
        $send_back_array['update'] = $update;
        echo json_encode($send_back_array);
    // }
    die();
}
add_action( 'wp_ajax_indppl_save_container_data_ajax', 'indppl_save_container_data_ajax' );
add_action('wp_ajax_nopriv_indppl_save_container_data_ajax', 'indppl_save_container_data_ajax');


function indppl_add_new_product_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    $return = indppl_get_product_info();
    echo $return;
    die();
}
add_action( 'wp_ajax_indppl_add_new_product_ajax', 'indppl_add_new_product_ajax' );
add_action('wp_ajax_nopriv_indppl_add_new_product_ajax', 'indppl_add_new_product_ajax');

function indppl_get_products_by_brand_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['brand'])){
        $brand = $_POST['brand'];
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }

    $authors = array(get_current_user_id());
    
    $auth    = indppl_get_dup_auth($store_id);
    if (count($auth) > 0) {
        foreach ($auth as $a) {
            $a_user = get_user_by('email', $a['user_email']);
            if ($a_user) {
                $authors[] = $a_user->ID;
            }
        }
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
            'author__in' => $authors,
            'meta_query' => array(
                array(
                    'key' => 'wpcf-default',
                    'value' => 1,
                    'compare' => '=',
                ),
            ),
        ),
        'orderby' => 'title',
        'order'   => 'ASC',
    );
    $products = new WP_Query($args);
    ob_start();
    // var_dump($products);

    ?> <option class='product-create-product-option' value='' disabled selected>Select Product</option> <?php
    if($products->have_posts()){
        while($products->have_posts()){
            $products->the_post();
            $title = get_the_title();
            $id = get_the_id();
            $author = get_the_author_meta('ID');
            $default = get_post_meta($id, 'wpcf-default', true);
            $do_it = true;
            $each_test = get_post_meta($id, 'wpcf-unit', true);
            if($each_test == 'each'){
                if($type == 'beds'){
                    $do_it = false;
                }
            }
            if($default == false && $author != get_current_user_id() ){
                $do_it = false;
            }
            if($do_it){
                ?>
                <option value="<?php echo $id; ?>"><?php echo $title; ?></option>
                <?php
            }
            // var_dump($author);
        }
    }
    echo ob_get_clean();
    die();
}
add_action( 'wp_ajax_indppl_get_products_by_brand_ajax', 'indppl_get_products_by_brand_ajax' );
add_action('wp_ajax_nopriv_indppl_get_products_by_brand_ajax', 'indppl_get_products_by_brand_ajax');

function indppl_add_new_brand_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['brand'])){
        $brand = $_POST['brand'];
    }
    $name = get_term_by('name', $brand, 'brand');
    if($name != false){
        $terms = get_terms( array(
            'taxonomy' => 'brand',
            'hide_empty' => false,
        ) );
        // var_dump($terms);
        $count = 0;
        $is_user_made = '';
        foreach($terms as $key => $value){
            if($value->name == $name->name){
                $id = $value->term_id;
                $default = get_term_meta($id, 'wpcf-custom-brand', true);
                $user_id = get_term_meta($id, 'wpcf-creator-user-id', true);
                if($user_id == get_current_user_id() || $default == false){
                    $is_user_made = $value->slug;
                }
                $count++;
            }
        }
        if($is_user_made){
            echo $is_user_made;
        }else{
            $term = wp_insert_term(
                $brand . "_" . $count,
                'brand'
            );
            wp_update_term($term['term_id'], 'brand', array(
                'name' => $brand
            ));
            $slug = get_term($term['term_id']);
            echo $slug->slug;
            add_term_meta($term['term_id'], 'wpcf-custom-brand', 1);
            add_term_meta($term['term_id'], 'wpcf-creator-user-id', get_current_user_id());
            $name = get_term_by('id', $term['term_id'], 'brand');
            // var_dump($terms);
        }
    }else{
        $term = wp_insert_term(
            $brand,
            'brand'
        );
        $slug = get_term($term['term_id']);
        echo $slug->slug;
        add_term_meta($term['term_id'], 'wpcf-custom-brand', 1);
        add_term_meta($term['term_id'], 'wpcf-creator-user-id', get_current_user_id());
        // echo "you failed";
    }
    
    die();
}
add_action( 'wp_ajax_indppl_add_new_brand_ajax', 'indppl_add_new_brand_ajax' );
add_action('wp_ajax_nopriv_indppl_add_new_brand_ajax', 'indppl_add_new_brand_ajax');

function indppl_get_product_info_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
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
        ob_start();
        ?>
            <input type='hidden' class='product-create-dry-wet' value='<?php echo $dryliquid; ?>'>
        <?php
        $dry_wet = ob_get_clean();
    }else{
        ob_start();
        ?>
        <h4 class='indppl-sub-header'>Is this product dry or liquid?</h4>
        <label for='product-create-dry' class=' <?php if($dryliquid == 'dry'){ ?>indppl-green-button-selected<?php }else{ ?>indppl-green-button-not-selected<?php } ?>' >Dry</label>
        <input type='radio' class='hide product-create-dry-wet' name='product-create-dry-wet' id='product-create-dry' <?php if($dryliquid == 'dry'){ ?>checked<?php }?> value='dry' >
        <label for='product-create-wet' class=' <?php if($dryliquid == 'wet'){ ?>indppl-green-button-selected<?php }else{ ?>indppl-green-button-not-selected<?php } ?>'>Liquid</label>
        <input type='radio' class='hide product-create-dry-wet' name='product-create-dry-wet' id='product-create-wet' <?php if($dryliquid == 'wet'){ ?>checked<?php }?> value='wet' >
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
    <h3 class='green-text'>Which Sizes Do You Stock?</h3>
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
                    $in_store = 'indppl-size-selected';
                }else{
                    $in_store = 'indppl-size-not-selected';
                }
                // echo $author;
                // echo $default_package;
                if($unit_meta == 'qt-d' || $unit_meta == 'qt-l'){
                    $unit_val = 'Quart';
                }else{
                    $unit_val = $unit_meta;
                }
                ?>
                
                <a href='#' class='<?php echo $in_store . " " . $non_default; ?> indppl-product-create-size-btn' data-size='<?php echo $size_meta; ?>' data-unit='<?php echo $unit_meta;?>' data-id='<?php echo $value; ?>'><?php echo $size_meta . " " . $unit_val; ?></a>
                <?php
                
            }


        }
    }
    $sizes .= ob_get_clean();

    ob_start();
    ?>
    <a href='#' class='indppl-orange indppl-create-new-size'>+ Create a new size</a>
    <div class='indppl-product-create-size-num-inside-container indppl-hide'>
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
        <h4 class='indppl-sub-header'>How much does 5 level cups of this product weigh?</h4>
        <p>(We'll use this to calculate 'How much to use' on the planting guide)</p>
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
    }else{
        $cups_num = get_post_meta($product_id, 'wpcf-5cups', true);
        $cups_unit = get_post_meta($product_id, 'wpcf-5cups-unit', true);
        ob_start();

        ?>

        <div class='product-create-5-cups-inside-container' style='display: none;'>
            <input type='number' class='indppl-product-create-cups-num' id='indpll-product-create-cups-num' min='0' name='indppl-product-create-cups-num' value='<?php echo $cups_num; ?>'>
            <select class='product-create-5-cups' id='product-create-5-cups' name='product-create-5-cups'>
                <!-- <option class='product-create-5-cups-option' value='' disabled selected>Select Unit</option> -->
                <?php
                    ?>
                    <option class='product-create-5-cups-option' value='<?php echo $cups_unit; ?>' selected >
                    <?php echo $weight_unit; ?>
                    </option>

            </select>
        </div>
        <?php
        $cups = ob_get_clean();

    }

    // app rates chart container
    if($product_id != 'new'){
        $app_rates_chart = update_package_table($store_id, $product_id, $type);
    }

    ob_start();
    if($type == 'pots' || $type == 'beds'){
        ?>
        <input type="submit" name="product-create-pots-next" id="product-create-pots-next" class="product-create-pots-submit" value="Create Another Product">
        <input type="submit" name="product-create-pots-finish" id="product-create-pots-finish" class="product-create-pots-submit" value="Finish">
        <?php
    }else{
        ?>
        <input type="submit" name="product-create-next" data-exit="true" id="product-create-next" class="product-create-submit" value="Next: Enter Application Rates">
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
    if($type == 'pots' || $type == 'beds'){
        ob_start();
        $filler = get_post_meta($product_id, 'wpcf-use-blended-filler');
        $additive = get_post_meta($product_id, 'wpcf-use-blended-additive');
        $surface = get_post_meta($product_id, 'wpcf-use-surface');

        $app_rates = indppl_apprates($store_id);
        if(isset($app_rates[$type]['filler'][$product_id]) || isset($app_rates[$type]['blended'][$product_id]) || isset($app_rates[$type]['surface'][$product_id])){
            $filler = '0';
            $additive = '0';
            $surface = '0';
            if(isset($app_rates[$type]['filler'][$product_id])){
                $filler = '1';
            }
            if(isset($app_rates[$type]['blended'][$product_id])){
                $additive = '1';
            }
            if(isset($app_rates[$type]['surface'][$product_id])){
                $surface = '1';
            }
        }
        $console = $additive;
        ?>
        <div class='indppl-add-product-usage-type'>
            <h3>Select Usage Type (check all that apply)</h3>
            <div>
                <input type='checkbox' name='indppl-add-product-bulk-filler' class='indppl-add-usage-type-check' id='indppl-add-product-bulk-filler' <?php if($filler){ ?>checked<?php }?>>
                <label for='indppl-add-product-bulk-filler'>Bulk Filler/Substrate(ie Potting Soil)</label>
            </div>
            <div>
                <input type='checkbox' name='indppl-add-product-additive-blend' class='indppl-add-usage-type-check' id='indppl-add-product-additive-blend' <?php if($additive){ ?>checked<?php }?>>
                <label for='indppl-add-product-additive-blend'>Additive Blended in with Potting Soil</label>
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
        <h4 class='indppl-sub-header product-create-fraction-bag-title'>How do you recommend this product?</h4>
            <label for='product-create-fraction-bag' class='<?php if($fraction){ ?>indppl-green-button-selected<?php } else { ?> indppl-green-button-not-selected <?php } ?> '>Fraction of a bag</label>
            <input type='radio' class='product-create-fraction-bag indppl-hide' name='product-create-fraction-bag' id='product-create-fraction-bag' <?php if($fraction){ ?>checked<?php }?> value='1' >
            <label for='product-create-other' class='<?php if(!$fraction){ ?>indppl-green-button-selected<?php } else { ?> indppl-green-button-not-selected <?php } ?> '>Other (ie. cups, tsp, tbls etc)</label>
            <input type='radio' class='product-create-other indppl-hide' name='product-create-fraction-bag' id='product-create-other' <?php if(!$fraction){ ?>checked<?php }?> value='1'>
        </div>
        <?php
    $fraction_bag = ob_get_clean();

    ob_start();
    if($type == 'ground'){
        $header = 'Product Setup for In-Ground Plantings';
        $instruction_text = "Planting Pal works best by selecting the 'All Purpose' version for each product type (ie all purpose fertilizer vs. rose fertilizer). If necessary, plant-specific substitutions can be made when using the app. If you don't see the product you need listed in the dropdown, you can create your own!";
        $video = '<iframe width="266" height="150" src="https://www.youtube.com/embed/FrpVUC1A71g" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>';
    }else if($type == 'pots'){
        $header = 'Product Setup for Potted Plants';
    }else if($type == 'beds'){
        $header = 'Product Setup for Raised Beds';
    }
    ?>
    
    <div class="indppl-instructions">
        <div class="indppl-instructions-text">
            <h2><?php echo $header; ?></h2>
            <p><?php echo $instruction_text; ?></p>
        </div>
        <div class="indppl-video">
            <?php echo $video; ?>
            <h4 class='orange-text indppl-watch-video'>Watch: how to use this page</h4>
        </div>
    </div>
    <?php
    $instructions = ob_get_clean();


    // $console = $usage_type;
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
    $send_array['instructions'] = $instructions;
    if($container){
        $send_array['container'] = $container;
        $brand = get_the_terms($product_id, 'brand', true);
        $send_array['brand'] = $brand[0]->name;
        $send_array['product'] = get_the_title($product_id);
    }
    if($product_id == 'new'){
        $send_array['new_product'] = $add_product;
    }
    if($type == 'pots' || $type == 'beds'){
        $send_array['usage_type'] = $usage_type;
    }
    echo json_encode($send_array);
    die();
}
add_action( 'wp_ajax_indppl_get_product_info_ajax', 'indppl_get_product_info_ajax' );
add_action('wp_ajax_nopriv_indppl_get_product_info_ajax', 'indppl_get_product_info_ajax');

function indppl_save_product_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
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
    if(isset($_POST['container_id'])){
        $container_id = $_POST['container_id'];
    }
    if(isset($_POST['first_package'])){
        $first_package = $_POST['first_package'];
    }
    if(isset($_POST['next'])){
        $next = $_POST['next'];
    }
    $default = get_post_meta($product_id, 'wpcf-default', true);
    if($default){
        ob_start();
        ?>
        <div id='indppl-ground-default-product' data-default='1'></div>
        <?php
        $set_default = ob_get_clean();
    }
    // $console = $default;
    // $console = $cups_num;
    $console = $product_rate;
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
    }else{
        update_post_meta($product_id, 'wpcf-5cups', $cups_num);
        update_post_meta($product_id, 'wpcf-5cups-unit', $cups_unit);
    }
    // checking change of 5_cups

    // $console = $product_rate;

    if($fraction == 'true' && $product_rate){
        $args = array(
            $product_id => array(
                'bag' => array(),
            ),
        );
        foreach($product_rate as $key => $value){
            // $value['name'] = container id
            // $value['value'] = input value
            // $product_unit[$key]['value'] = cpp
            // $first_package['num'] = number of first pack
            // $first_package['unit'] = unit of first package
            // $console = $product_unit;
            if($product_unit[$key]['value'] == 'cpp'){
                $app_rate = intval($value['value']) * intval($first_package['num']);
            }else{
                if(!$value['value'] == 0){
                    $app_rate = $first_package['num'] / $value['value'];
                }else{
                    $app_rate = 0;
                }
            }
            $console = $app_rate;
            if($app_rate == 0){
                $args[$product_id]['bag'][$value['name']] = array(
                    'amount' => $app_rate,
                    'unit' => $first_package['unit'],
                );
            }else{
                $args[$product_id]['bag'][$value['name']] = array(
                    'amount' => $app_rate,
                    'unit' => $first_package['unit'],
                );
            }

        }
        // $console = $args;
        indppl_apprates($store_id, $type, $args);
    }else{
        $send_array = array($product_id => array());
        foreach($product_rate as $key => $value){
            if($value['value'] == 0){

                $temp = array(
                    'unit' => $product_unit[$key]['value'],
                    'amount' => 0,
                );
                $send_array[$product_id]['containers'][$value['name']] = $temp;
            }else{
                $temp = array(
                    'unit' => $product_unit[$key]['value'],
                    'amount' => $value['value'],
                );
                $send_array[$product_id]['containers'][$value['name']] = $temp;
            }
        }
        if($product_rate){
            // $console = $send_array;
            $save = indppl_apprates($store_id, $type, $send_array);
            
        }
    }
    // var_dump($package_array);

    // var_dump($new_pack);
    $pack_id_array = [];
    foreach($new_pack as $key => $value){
        $new_id = indppl_create_package($value);
        $new_package = toolset_connect_posts('store-package', $store_id, $new_id);
        $prod_pack = toolset_connect_posts('product-package', $product_id, $new_id);
        // var_dump($new_package);
        // $console = $new_package;

        array_push($pack_id_array, $new_id);
    }
    foreach($package_array as $package_id){
        if($package_id != 0){
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
        $updated_app_rates = update_bag_package_table($store_id, $product_id, $type);
    }else{
        delete_post_meta($product_id, 'wpcf-fraction');
        $updated_app_rates = update_package_table($store_id, $product_id, $type);
        // $console = $updated_app_rates;
    }
    if($next){
        $store_container_relations = toolset_get_related_posts(
            $store_id, // get posts related to this one
            'store-container', // relationship between the posts
            'parent',
            '100',
            '0',
            array(),
            'post_id',
            'child'
        );
        $app_rates = indppl_apprates($store_id);
        $update = [];
        if(!empty($app_rates['ground'])){
            foreach($app_rates['ground'] as $pro_id => $product){
                if($pro_id == $product_id){
                    foreach($store_container_relations as $key => $container){
                        if(!array_key_exists($container, $app_rates['ground'][$pro_id]['containers']) && !array_key_exists($container, $app_rates['ground'][$pro_id]['bag'])){
                            $update[] = $container;
                        }
                    }
                }
            }
        }
        // $console = $update;
    }else{
        $old_update = get_post_meta($store_id, 'wpcf-apprate-update', true);
        $old_update = json_decode($old_update, true);
        // $console = $old_update;
        $old_update[$product_id] = false;
    
        update_post_meta($store_id, 'wpcf-apprate-update', json_encode($old_update));
    }
    
    $ajax_array = [];
    $ajax_array['app_rates'] = $updated_app_rates;
    $ajax_array['product_id'] = $product_id;
    $ajax_array['dryliquid'] = $product_dryliquid;
    $ajax_array['pack_id_array'] = $pack_id_array;
    if($default){
        $ajax_array['default'] = $set_default;
    }
    if($next){
        $ajax_array['product_name'] = $product_name;
        $ajax_array['update'] = $update;
    }
    $ajax_array['console'] = $console;
    echo json_encode($ajax_array);
    die();
}
add_action( 'wp_ajax_indppl_save_product_ajax', 'indppl_save_product_ajax' );
add_action('wp_ajax_nopriv_indppl_save_product_ajax', 'indppl_save_product_ajax');

function indppl_product_save_exit_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    echo do_shortcode('[pp-store-products]');
    die();
}
add_action( 'wp_ajax_indppl_product_save_exit_ajax', 'indppl_product_save_exit_ajax' );
add_action('wp_ajax_nopriv_indppl_product_save_exit_ajax', 'indppl_product_save_exit_ajax');


function indppl_remove_package_from_store_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
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
    die();
}
add_action( 'wp_ajax_indppl_remove_package_from_store_ajax', 'indppl_remove_package_from_store_ajax' );
add_action('wp_ajax_nopriv_indppl_remove_package_from_store_ajax', 'indppl_remove_package_from_store_ajax');


// Setup the guide form 
function indppl_setup_guide_forms_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    $form = $_POST['form'];
    $defaults = get_posts(array("post_type" => "guide-defaults", 'meta_key' => 'wpcf-guide-type', 'meta_value' => $form));
    $default = $defaults[0];
    $store = htmlspecialchars($_POST['store']);
    $options = toolset_get_related_posts($default->ID, 'guide-steps',['query_by_role' => 'parent', 'return' => 'post_id', 'role_to_return' => 'child', 'args' =>['meta_key'=>'toolset-post-sortorder'], 'orderby' => 'meta_value_num', 'order' => 'ASC']);
    $b_track = toolset_get_related_posts($default->ID, 'b-track', ['query_by_role' => 'parent', 'return' => 'post_id', 'role_to_return' => 'child', 'args' => ['meta_key' => 'toolset-post-sortorder'], 'orderby' => 'meta_value_num', 'order' => 'ASC']);
    $apprates = indppl_apprates($store);

    $sections = array();
    
    foreach($options as $option) {
        // var_dump(get_post_meta($option));
        $sections[get_post_meta($option, 'wpcf-step-title', TRUE)] = array(
            'a-instructions' => get_post_meta($option, 'wpcf-option-a-instructions', TRUE),
            'a-image' => get_post_meta($option, 'wpcf-option-a-image', TRUE),
            'b-instructions' => get_post_meta($option, 'wpcf-option-b-instructions', TRUE),
            'b-image' => get_post_meta($option, 'wpcf-option-b-image', TRUE),
            'id' => $option,
        );
    }

    foreach($b_track as $b_option) {
        // var_dump(get_post_meta($b_option));
        $b_sections[get_post_meta($b_option, 'wpcf-b-step-title', TRUE)] = array(
            'a-instructions' => get_post_meta($b_option, 'wpcf-b-option-a-instructions', TRUE),
            'a-image' => get_post_meta($b_option, 'wpcf-b-option-a-image', TRUE),
            'b-instructions' => get_post_meta($b_option, 'wpcf-b-option-b-instructions', TRUE),
            'b-image' => get_post_meta($b_option, 'wpcf-b-option-b-image', TRUE),
            'id' => $b_option,
        );
    }
    
    ob_start(); 
    
        switch($form){
            case 'ground' :
                include(INDPPL_ROOT_PATH . '/templates/guides/ground.php');
                break;
            case 'pots':
                include(INDPPL_ROOT_PATH . '/templates/guides/pots.php');
                break;

            case 'beds':
                include INDPPL_ROOT_PATH . '/templates/guides/beds.php';
                break;

        } ?>

    <?php $response = ob_get_clean();
    echo $response;
    die();
}

add_action('wp_ajax_indppl_setup_guide_forms_ajax', 'indppl_setup_guide_forms_ajax');
add_action('wp_ajax_nopriv_indppl_setup_guide_forms_ajax', 'indppl_setup_guide_forms_ajax');

function indppl_update_app_rates_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
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
    $cups_unit = get_post_meta($product_id, 'wpcf-5cups-unit', true);
    if(!$cups_unit || $cups_unit == ''){
        $cups_unit = 'lb';
        update_post_meta($product_id, 'wpcf-5cups-unit', 'lb');
    }
    $update_array = [];
    $items = array(
        array(
            'unit' => $container_unit,
            'amount' => $container_num,
        ),
    );
    $console = $cups;
    foreach($current_pack as $key => $value){
        $conversion = indppl_normalize($items, $value['unit'], $cups, $cups_unit);
        // var_dump($items);
        // $console = $cups;
        // var_dump($value['unit']);
        // var_dump($cups);
        if($conversion[0]['standard-amount'] == 0){
            $final = 0;
        }else{
            $final = $value['size'] / $conversion[0]['standard-amount'];
            if(is_float($final) || is_int($final)){
                $final = round($final, 2);
            }
        }
        $update_array[] = $final;
    }
    $send_array = [];
    $send_array['app_rates'] = $update_array;
    $send_array['console'] = $console;
    echo json_encode($send_array);
    // var_dump($send_array);
    die();
}
add_action( 'wp_ajax_indppl_update_app_rates_ajax', 'indppl_update_app_rates_ajax' );
add_action('wp_ajax_nopriv_indppl_update_app_rates_ajax', 'indppl_update_app_rates_ajax');

function indppl_save_pots_product_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
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
    if(isset($_POST['product_unit'])){
        $product_unit = $_POST['product_unit'];
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
        // var_dump($filler);
    }
    if(isset($_POST['blend'])){
        $blend = $_POST['blend'];
        // var_dump($blend);
    }
    if(isset($_POST['surface'])){
        $surface = $_POST['surface'];
        // var_dump($surface);
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
    }else{
        update_post_meta($product_id, 'wpcf-5cups', $cups_num);
        update_post_meta($product_id, 'wpcf-5cups-unit', $cups_unit);
    }

    $app_rates = indppl_apprates($store_id);
    // $console = $app_rates[$type][$product_id];
    var_dump($app_rates[$type]['filler'][$product_id]);
    $filler_check = $app_rates[$type]['filler'][$product_id];
    $blended_check = $app_rates[$type]['blended'][$product_id];
    $surface_check = $app_rates[$type]['surface'][$product_id];
    $each_check = $app_rates[$type]['each'][$product_id];
    // var_dump($product_id);
    // var_dump($surface);
    $send_array = array($product_id => array());
    // foreach($product_rate as $key => $value){
    
    $temp = array();
    if($filler == 'true'){
        if(key_exists('amount', $filler_check)){
            $temp['filler'] = array(
                $product_id => $filler_check
            );
        }else{
            $temp['filler'] = array(
                $product_id => array(
                    'bag' => '',
                ),
            );
        }
    }
    if($blend == 'true'){
        if(key_exists('amount', $blended_check)){
            $temp['blended'] = array(
                $product_id => $blended_check
            );
        }else{
            $temp['blended'] = array(
                $product_id => array(
                    'bag' => '',
                ),
            );
        }

    }
    if($surface == 'true'){
        if(key_exists('amount', $surface_check)){
            $temp['surface'] = array(
                $product_id => $surface_check
            );
        }else{
            $temp['surface'] = array(
                $product_id => array(
                    'bag' => '',
                ),
            );
        }
    }

    if($new_pack[count($new_pack)-1]['unit'] == 'each' || $product_unit == 'each'){
        if(key_exists('small', $each_check)){
            $temp['each'] = array(
                $product_id => $each_check
            );
        }else{
            $temp['each'] = array(
                $product_id => array(
                    'bag' => '',
                ),
            );
        }
    }
    $send_array = $temp;
    
    // }
    if($product_id != 'new'){
        $del_array = array(
            $type => $product_id,
        );
        $del = indppl_delete_apprate($store_id, $del_array);
        // var_dump($send_array);
        $save = indppl_apprates($store_id, $type, $send_array);

        // var_dump($save);
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
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    $app_rates = indppl_apprates($store_id);
    $get_apps = false;
    $num = 0;
    $percent_array = array();
    foreach($app_rates[$type]['filler'] as $key => $value){
        // foreach($v as $key => $value){
            if(isset($value['amount'])){
                $get_apps = true;
                $percent_array[] = $value['amount'];
                // var_dump($value['amount']);
            }
            $num++;
        // }
    }
    if($get_apps == false){
        $ind = floor(100 / $num);
        $count = 100 - ($ind * $num);
        
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
    }

    ob_start();
    ?>
    <div class='pots-apprates-container'>
        <div id='pots-and-beds-type' data-type='<?php echo $type; ?>'></div>
        <a href='#' class='modal-close'>X</a>
        <?php
        if($type == 'pots'){
            $header = 'Pot Application Rates';
            $text = 'pot';
            $apprate_text = "These product(s) make up the largest volume and are what you recommend to fill pots with. Many times, this section will only have one product - your preferred potting soil. But if you're into mixing products together or even recommend creating potting soil from scratch, you can do that here too.";
        }else{
            $header = 'Raised Bed Application Rates';
            $text = 'raised bed';
            $apprate_text = "These product(s) make up the largest volume and are what you recommend to fill raised beds with. Many times, this section will only have one product - your preferred raised bed soil. But if you're into mixing products together or even recommend creating raised bed soil from scratch, you can do that here too.";
        }
        $instructions = array(
            'header' => $header,
            'text' => 'Tell us how much of each product you recoomend for ' . $text . " plantings. Make sure you select all products first before completeing this screen. We'll use these application rates to create a customized shopping list in the app AND show exactly how much to use for each size plant on the planting guide.",
            'video' => '<iframe width="238" height="150" src="https://www.youtube.com/embed/FrpVUC1A71g" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
        );
        echo indppl_instructions($instructions);
        ?>
        <h2 class='green-text apprates-header'>Bulk Filler / Substrate</h2>
        <p><?php echo $apprate_text; ?></p>
        <div class='three-two'>

            <table class='pots-apprates-filler-container'>
                <tr>
                    <th></th>
                    <th class=''></th>
                    <th class='indppl-apprate-sub-header'><?php echo ucwords($text); ?> Soil Formula</th>
                    <?php if(count($app_rates[$type]['filler']) > 1){
                        ?>
                        <th class='indppl-apprate-sub-header header-left-15'>Primary Filler</th>
                        <?php
                    }
                    ?>
                    
                    <!-- <th class='max-width-500'>Primary Filler - If the amount recommended is small and split between two filler/substrates, which one product would you recommend?</th> -->
                </tr>
                <?php
                $counter = 0;
                foreach($app_rates[$type]['filler'] as $key => $value){
                    // if(isset($value['filler'])){
                    $title = get_the_title($key);
                    $brand = get_the_terms($key, 'brand', true);
                    $brand = $brand[0]->name;
                    $primary = '';
                    $img = get_post_meta($key, 'wpcf-product-image', true);
                    if(!$img){
                        $img =  home_url() . "/wp-content/uploads/2019/03/big-carrot.png";
                    }
                    $default = $app_rates[$type]['filler'][$key]['primary'];
                    if($default == "true"){
                        $primary = 'checked';
                    }
                    // var_dump($brand);
                    ?>
                    <tr class='pots-apprates-filler-inside-container'>
                        <td class='pots-apprates-filler-cell'>
                            <img class='height-50 ind-centered' src="<?php echo $img; ?>">
                        </td>
                        <td class='pots-apprates-filler-cell'>
                            <div class='pots-apprates-brand-title'>
                                <h4 class='pots-apprates-brand'><?php echo $brand; ?></h4>
                                <h3 class='pots-apprates-title'><?php echo $title; ?></h3>
                            </div>
                            
                        </td>
                        <td class='pots-apprates-filler-cell'>
                            <input type='number' min='0' max='100' data-product='<?php echo $key; ?>' name='filler-<?php echo $key; ?>' class='pots-apprates-filler' value='<?php echo $percent_array[$counter]; ?>'>
                            <span class='pots-apprates-filler-percent'>%</span>
                        </td>
                        <?php if(count($app_rates[$type]['filler']) > 1){
                            ?>
                            <td class='text-align-center'>
                                <input type='radio' class='pots-apprates-filler-radio hide' id='pots-apprates-filler-radio-<?php echo $key; ?>' name='pots-apprates-filler-radio' <?php echo $primary; ?>>
                                <label class='indppl-apprate-primary-radio' for='pots-apprates-filler-radio-<?php echo $key; ?>'>
                                    <svg class='apprate-primary-select' height="24" width="24">
                                        <circle cx="12" cy="12" r="10" stroke="#6a6e77" stroke-width="2" fill-opacity="0"></circle>
                                        <circle class='apprate-circle-fill <?php if($primary == ''){ echo 'hide'; } ?>' cx="12" cy="12" r="6" stroke="#9ecb3c" stroke-width="2" fill="#9ecb3c" fill-opacity="1"></circle>
                                        Sorry, your browser does not support inline SVG. 
                                    </svg>
                                </label>
                            </td>
                            <?php

                        }else{
                            ?>
                            <td></td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                    $counter++;
                    // }
                    
                }
                if(empty($app_rates[$type]['filler'])){
                    ?>
                    <tr>
                        <th class='color-red max-width-500'>There are currently no products selected for this section. To add a product to this section, add or edit a product under the <?php echo $type; ?> category on the products page.</th>
                    </tr>
                    <?php
                }else{
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            <div class='apprates-filler-100-container'>
                                <div class='pots-apprates-filler-total color-red'>0</div>
                                <div class='pots-apprates-filler-percent'>%</div>
                            </div>
                        </td>
                        <!-- <td>
                            
                            </td> -->
                            <!-- <td>
                                <div class='pots-apprates-filler-message color-red'>
                                    <p>Oops! This mix doesn't add up to 100%.</p>
                                    <p>Please check your numbers and try again.</p>
                                </div>
                            </td> -->
                        </tr>
                        <?php
                }
                ?>
            </table>
            <?php
            if(count($app_rates[$type]['filler']) > 1 && $type == 'pots'){
                    ?>
                <div class='indppl-filler-hint-container'>
                    <div class='indppl-filler-hint-inner-container'>
                        <img class='indppl-hint-img' src='<?php echo home_url(); ?>\wp-content\plugins\planting-pal\assets\img\planting-pal-carrot.png'>
                        <p class='indppl-hint-header indpple-dark-green-bg lobster'>Hint:</p>
                        <p class='indppl-hint-text'>If your customer needs 8 qts or less of potting soil, Planting Pal will recomend the Primary Filler instead of the multiple product formula.</p>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
        if($type == 'pots'){
            $text_type = 'potting';
        }else{
            $text_type = 'raised bed';
        }
        ?>
        <h4 class='margin-top-30 green-text apprates-header'>Additives Blended-in with <?php echo ucwords($text_type); ?> Soil</h4>
        <p>These product(s) are blended into your <?php echo $text_type; ?> soil formula you selected above.</p>
        <div class='three-two'>
            <table>
                <tr>
                    <th></th>
                    <th class=''></th>
                    <th colspan="3" class='indppl-apprate-sub-header'>Recommended Application Rates</th>

                </tr>
                <?php
                foreach($app_rates[$type]['blended'] as $key => $value){
                    // var_dump($value);
                    // var_dump("<br /><br />");
                    // if(isset($value['blended'])){
                        $title = get_the_title($key);
                        $brand = get_the_terms($key, 'brand', true);
                        $brand = $brand[0]->name;
                        // defaults
                        $img = get_post_meta($key, 'wpcf-product-image', true);
                        if(!$img){
                            $img =  home_url() . "/wp-content/uploads/2019/03/big-carrot.png";
                        }
                        $dilution = get_post_meta($key, 'wpcf-blended-additive-dilution', true);
                        $unit = get_post_meta($key, 'wpcf-blended-additive-unit', true);
                        // apprates_array
                        
                        // if($get_apps == true){
                            if(isset($app_rates[$type]['blended'][$key]['amount'])){
                                $dilution = $app_rates[$type]['blended'][$key]['amount'];
                                $unit = $app_rates[$type]['blended'][$key]['unit'];
                            }
                        // }
                        
                        $select_array = array(
                            'cup' => 'Cups',
                            'tbls' => 'Tablespoons',
                            'tsp' => 'Teaspoons',
                        );
                        ?>
                        <tr>
                            <td class='pots-apprates-blended-cell'>
                                <img class='height-50 ind-centered' src="<?php echo $img; ?>">
                            </td>
                            <td class='pots-apprates-blended-cell'>
                                <div class='pots-apprates-brand-title'>
                                    <h4 class='pots-apprates-brand'><?php echo $brand; ?></h4>
                                    <h3 class='pots-apprates-title'><?php echo $title; ?></h3>
                                </div>
                            </td>
                            <td class='pots-apprates-blended-cell'>
                                <input type="number" min='0' data-product='<?php echo $key; ?>' name='blended-num-<?php echo $key; ?>' value='<?php echo $dilution; ?>' class='blended-num'>
                            </td>
                            <td class='pots-apprates-blended-cell'>
                                <select name='blended-select-<?php echo $key; ?>' class='blended-select'>
                                    <?php
                                    foreach($select_array as $k => $v){
                                        $selected='';
                                        if($unit == $k){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="<?php echo $k; ?>" <?php echo $selected; ?> ><?php echo $v; ?></option> 
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td class='pots-apprates-blended-cell'>
                                per cuft of soil
                            </td>


                        </tr>
                        <?php
                    // }
                }
                if(empty($app_rates[$type]['blended'])){
                    ?>
                    <tr>
                        <th class='color-red max-width-500'>There are currently no products selected for this section. To add a product to this section, add or edit a product under the <?php echo $type; ?> category on the products page.</th>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <div class='indppl-hint-container'>
                <div class='indppl-hint-inner-container'>
                    <img class='indppl-hint-img' src='<?php echo home_url(); ?>\wp-content\plugins\planting-pal\assets\img\planting-pal-carrot.png'>
                    <p class='indppl-hint-header indpple-dark-green-bg lobster'>Hint:</p>
                    <p class='indppl-hint-text'>Typical application rates for products in this section are:<br />
                    <span class='indppl-hint-text-bold'>Organic Fertilizer: </span><span>1 cup per cuft of soil</span>
                    <br />
                    <span class='indppl-hint-text-bold'>Chemical Fertilizer: </span><span>1 tbsp per cuft of soil</span>
                    <br />
                    <span class='indppl-hint-text-bold'>Microbe Fertilizer: </span><span>0.25 tsp per cuft of soil</span>
                    </p>
                </div>
            </div>
            
        </div>
        <!-- <p>Typical Application Rates:</p>
        <p>Organic Fertilizer - 1 Cup per cuft of soil</p>
        <p>Chemical Fertilizer - 1 tbs per cuft of soil</p>
        <p>Microbe Products - .25 tsp per cuft of soil</p> -->
        <?php
        if($type == 'pots'){
            $applied_text = 'potted plants';
        }else{
            $applied_text = 'raised beds';
        }
        ?>
        <h4 class='margin-top-30 green-text apprates-header'>Additives Surface Applied after planting</h4>
        <p>These product(s) are after planting to the surface of <?php echo $applied_text; ?>.</p>
        <div class='three-two'>
            <table>
                <tr>
                    <th></th>
                    <th class=''></th>
                    <th colspan="3" class='indppl-apprate-sub-header'>Recommended Application Rates</th>

                </tr>
                <?php
                foreach($app_rates[$type]['surface'] as $key => $value){
                    // if(isset($value['surface'])){
                        $title = get_the_title($key);
                        $brand = get_the_terms($key, 'brand', true);
                        $brand = $brand[0]->name;
                        // defaults
                        $dilution = get_post_meta($key, 'wpcf-surface-dilution', true);
                        $img = get_post_meta($key, 'wpcf-product-image', true);
                        if(!$img){
                            $img =  home_url() . "/wp-content/uploads/2019/03/big-carrot.png";
                        }
                        $units = get_post_meta($key, 'wpcf-surface-units', true);
                        $per_unit = get_post_meta($key, 'wpcf-surface-per-amount', true);
                        // apprates_array
                        // if($get_apps == true){
                        if(array_key_exists('amount', $app_rates[$type]['surface'][$key])){
                            $dilution = $app_rates[$type]['surface'][$key]['amount'];
                            $units = $app_rates[$type]['surface'][$key]['unit'];
                            $per_unit = $app_rates[$type]['surface'][$key]['per-sqft'];
                        }
                        // }

                        $select_unit = array(
                            'cup' => 'Cups',
                            'tbls' => 'Tablespoons',
                            'tsp' => 'Teaspoons',
                        );
                        $select_sqft = array(
                            '1' => 'Per 1 sqft',
                            '10' => 'Per 10 sqft',
                            '100' => 'Per 100 sqft',
                        );
                        ?>
                        <tr>
                            <td class='pots-apprates-surface-cell'>
                                <img class='height-50 ind-centered' src="<?php echo $img; ?>">
                            </td>
                            <td class='pots-apprates-surface-cell'>
                                <div class='pots-apprates-brand-title'>
                                    <h4 class='pots-apprates-brand'><?php echo $brand; ?></h4>
                                    <h3 class='pots-apprates-title'><?php echo $title; ?></h3>
                                </div>
                            </td>
                            <td class='pots-apprates-surface-cell'>
                                <input type='number' min='0' data-product='<?php echo $key; ?>' name='surface-num-<?php echo $key; ?>' value='<?php echo $dilution; ?>' class='surface-num'>
                            </td>
                            <td class='pots-apprates-surface-cell'>
                                <select name='surface-select-<?php echo $key; ?>' class='surface-select'>
                                    <?php
                                    foreach($select_unit as $k => $v){
                                        $selected='';
                                        if($units == $k){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="<?php echo $k; ?>" <?php echo $selected; ?> ><?php echo $v; ?></option> 
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td class='pots-apprates-surface-cell'>
                                <select name='surface-select-sqft-<?php echo $key; ?>' class='surface-select-sqft'>
                                    <?php
                                    foreach($select_sqft as $k => $v){
                                        $selected='';
                                        if($per_unit == $k){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="<?php echo $k; ?>" <?php echo $selected; ?> ><?php echo $v; ?></option> 
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <?php
                    // }
                }
                if(empty($app_rates[$type]['surface'])){
                    ?>
                    <tr>
                        <th class='color-red max-width-500'>There are currently no products selected for this section. To add a product to this section, add or edit a product under the <?php echo $type; ?> category on the products page.</th>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <div class='indppl-hint-container'>
                <div class='indppl-hint-inner-container'>
                    <img class='indppl-hint-img' src='<?php echo home_url(); ?>\wp-content\plugins\planting-pal\assets\img\planting-pal-carrot.png'>
                    <p class='indppl-hint-header indpple-dark-green-bg lobster'>Hint:</p>
                    <p class='indppl-hint-text'>Typical application rates for products in this section are:<br />
                    <span class='indppl-hint-text-bold'>Organic Fertilizer: </span><span>1 cup per sqft</span>
                    <br />
                    <span class='indppl-hint-text-bold'>Chemical Fertilizer: </span><span>1 lb per 100 sqft</span>
                    <br />
                    <span class='indppl-hint-text-bold'>Microbe Fertilizer: </span><span>0.25 tsp per 10 sqft</span>
                    </p>
                </div>
            </div>

        </div>
        <!-- <p>Typical Application Rates:</p>
        <p>Organic Fertilizer - 1 cup per 10 sqft</p>
        <p>Chemical Fertilizer - 1 tsp per 10 sqft</p>
        <p>Microbe Products - .25 tsp per 10 sqft</p> -->

        <?php
        if($type == 'pots'){
            ?>
            <h4 class='margin-top-30 green-text apprates-header'>Products Recommended in Eaches</h4>
            <p>These are typically self-contained products like fertilizer spikes or paks that are sold individually or multi-packs. Enter the number of 'Eaches' you recommend based on the width of the pot.</p>
            <table>
                <tr>
                    <th></th>
                    <th class=''></th>
                    <th colspan="3" class='indppl-apprate-sub-header border-bottom-off'># Eaches Per Pot Size</th>
                </tr>
                <?php
                if(!empty($app_rates[$type]['each'])){
                    ?>
                    <tr>
                        <th></th>
                        <th></th>
                        <th class='indppl-apprate-sub-eaches-header' style="text-align: center">&lt;8" wide</th>
                        <th class='indppl-apprate-sub-eaches-header header-left-15' style="text-align: center">8-24" wide</th>
                        <th class='indppl-apprate-sub-eaches-header header-left-15' style="text-align: center">&gt;24" wide</th>
                    </tr>
                    <?php
                }else{
                    ?>
                    <tr class='margin-bottom-20 display-block'>
                        <th class='color-red max-width-500'>There are currently no products selected for this section. To add a product to this section, add or edit a product under the <?php echo $type; ?> category on the products page.</th>
                    </tr> 
                    <?php
                }
                foreach($app_rates[$type]['each'] as $key => $value){
                    $title = get_the_title($key);
                    $brand = get_the_terms($key, 'brand', true);
                    $brand = $brand[0]->name;
                    // defaults
                    $each_small = get_post_meta($key, 'wpcf-each-small', true);
                    $each_medium = get_post_meta($key, 'wpcf-each-medium', true);
                    $each_large = get_post_meta($key, 'wpcf-each-large', true);

                    $img = get_post_meta($key, 'wpcf-product-image', true);
                    if(!$img){
                        $img =  home_url() . "/wp-content/uploads/2019/03/big-carrot.png";
                    }
                    if(array_key_exists('small', $app_rates[$type]['each'][$key])){
                        $each_small = $app_rates[$type]['each'][$key]['small'];
                        $each_medium = $app_rates[$type]['each'][$key]['medium'];
                        $each_large = $app_rates[$type]['each'][$key]['large'];
                    }

                    ?>
                    <tr>
                        <td class='pots-apprates-each-cell'>
                            <img class='height-50 ind-centered' src="<?php echo $img; ?>">
                        </td>
                        <td class='pots-apprates-each-cell'>
                            <div class='pots-apprates-brand-title'>
                                <h4 class='pots-apprates-brand'><?php echo $brand; ?></h4>
                                <h3 class='pots-apprates-title'><?php echo $title; ?></h3>
                            </div>
                        </td>
                        <td class='pots-apprates-each-cell'>
                            <input type='number' min='0' data-product='<?php echo $key; ?>' class='pots-apprates-each-num-8 max-width-100' name='pots-apprates-each-8-<?php echo $key; ?>' value='<?php echo $each_small; ?>' placeholder='#eaches'>
                        </td>
                        <td class='pots-apprates-each-cell'>
                            <input type='number' class='pots-apprates-each-num-8-24 max-width-100' name='pots-apprates-each-8-24-<?php echo $key; ?>' value='<?php echo $each_medium; ?>' placeholder='#eaches'>
                        </td>
                        <td class='pots-apprates-each-cell'>
                            <input type='number' class='pots-apprates-each-num-24 max-width-100' name='pots-apprates-each-24-<?php echo $key; ?>' value='<?php echo $each_large; ?>' placeholder='#eaches'>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        }
        ?>
        <div class='pots-apprates-save-container'>
            <a href='#' class='orange-text apprates-close'>Back</a>
            <a href='#' class='pots-apprates-save-btn indppl-button'>Save & Exit</a>
        </div>
    </div>
    <?php
    $return = ob_get_clean();
    echo $return;
    die();
}
add_action( 'wp_ajax_indppl_get_pot_apprates_ajax', 'indppl_get_pot_apprates_ajax' );
add_action('wp_ajax_nopriv_indppl_get_pot_apprates_ajax', 'indppl_get_pot_apprates_ajax');

function indppl_save_pot_apprates_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['fill_array'])){
        $fill_array = $_POST['fill_array'];
    }
    if(isset($_POST['blend_array'])){
        $blend_array = $_POST['blend_array'];
    }
    if(isset($_POST['surface_array'])){
        $surface_array = $_POST['surface_array'];
    }
    if(isset($_POST['each_array'])){
        $each_array = $_POST['each_array'];
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    $args = array(
        'filler' => $fill_array,
        'blended' => $blend_array,
        'surface' => $surface_array,
        'each' => $each_array,
    );
    $save = indppl_apprates($store_id, $type, $args);
    var_dump($type);
    die();
}
add_action( 'wp_ajax_indppl_save_pot_apprates_ajax', 'indppl_save_pot_apprates_ajax' );
add_action('wp_ajax_nopriv_indppl_save_pot_apprates_ajax', 'indppl_save_pot_apprates_ajax');

function indppl_guide_products_ajax(){
    $products = $_POST['products'];
    indppl_guide_products($products);
    die();
}

add_action('wp_ajax_indppl_guide_products_ajax', 'indppl_guide_products_ajax');
add_action('wp_ajax_nopriv_indppl_guide_products_ajax', 'indppl_guide_products_ajax');

function indppl_save_guide_ajax(){
    $steps = json_encode( $_POST['steps']);
    $store = $_POST['store'];
    $type = $_POST['type'];

    $results = update_post_meta( $store, 'wpcf-planting-guide-' . $type . '-options', $steps );

    var_dump($results);
    die();

}

add_action('wp_ajax_indppl_save_guide_ajax', 'indppl_save_guide_ajax');
add_action('wp_ajax_nopriv_indppl_save_guide_ajax', 'indppl_save_guide_ajax');

function indppl_build_guide_ajax() {

    // Check nonce
    if(true){ // This should be the nonce later

        // Set the variables
        $store = htmlspecialchars($_POST['store']);
        $plants =  $_POST['plants'];
        $list =  $_POST['list']; 
        $ground_list = $_POST['ground'];
        $send_email = htmlspecialchars( $_POST['email'] );
        $guides = array(); // set the array so we can fill it up and create multiple guides

        // Stash the shopping list, email address, and store in the DB for later marketing
        $market_args = array(
            'user_email' => $send_email,
            'store_id' => $store,
            'shopping_list' => json_encode($list),
            'plants' => json_encode($plants),
        );

        // var_dump(json_encode($list));
        $save_data = indppl_insert_marketing_data($market_args);
        // Load this array so you can build the email
        $guide_links = array();

        // Use the type & product list to build planting guide
        $qty_test = '';
        foreach($plants as $type => $plant){

            if(($type == 'ground' && count($plant) > 0) || ($plant['qty'][0] > 0 && $plant['qty'][0] != '') ){

                $guide_options = get_post_meta($store, 'wpcf-planting-guide-' . $type . '-options', TRUE);
                $guide_options = str_replace(array("\\'",'/','u2019'), array("'",'\/',"'") ,$guide_options);
                $guide_options = json_decode($guide_options, true);
                ob_start();
                    include(INDPPL_ROOT_PATH . '/templates/template_parts/planting-guide.php');
                $guide = ob_get_clean();
                $args = array(
                    'post_type' => 'guide',
                    'post_content' => $guide, 
                    'post_status' => 'publish',
                );
                $new_guide = wp_insert_post( $args ); 
                
                $guide_links[] = array(
                    'link' => get_permalink($new_guide),
                    'type' => $type,
                );
            }
        } ?>
        <div class="container" style="padding-bottom: 300px;">
            <h2>Success!</h2>
            <p>The link for your planting guide has been emailed to you. Your guide is good for 30 days, then we'll need to ask you to complete the process again.</p>
            <?php foreach($guide_links as $guides){ ?>
                <a href="<?php echo $guides['link']; ?>" target="_blank">Check out your <?php echo $guides['type']; ?> planting guide here!</a><br />
            <?php } ?>
        </div>
        <?php
        // Email user the link
        $email_content = "<p>Hey there! Thanks for using the <a href='http://plantingpal.com'>Planting Pal</a> app to calculate your needs. Here are the guide(s) you've generated!</p><ul>";
        foreach($guide_links as $link){
            $email_content .= "<li><a href='{$link['link']}'>Guide for {$link['type']}</a></li>";
        }
        $email_content .= "</ul>";
        $subject = "Your Custom Planting Guide";
        $headers = array();
        $headers[] = 'From: Planting Pal <hello@plantingpal.com>';
        wp_mail($send_email, $subject, $email_content, $headers);

        // Generate the page

    } else {
        echo json_encode("Hacker!");
    }

    // End the script
    die();

}

add_action('wp_ajax_indppl_build_guide_ajax', 'indppl_build_guide_ajax');
add_action('wp_ajax_nopriv_indppl_build_guide_ajax', 'indppl_build_guide_ajax');

function indppl_update_bag_app_rates_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['product_id'])){
        $product_id = $_POST['product_id'];
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    if(isset($_POST['val'])){
        $val = $_POST['val'];
    }
    if(isset($_POST['ppc'])){
        $ppc = $_POST['ppc'];
    }
    if(isset($_POST['product_num'])){
        $product_num = $_POST['product_num'];
    }
    if(isset($_POST['product_unit'])){
        $product_unit = $_POST['product_unit'];
    }
    if(isset($_POST['cont_id'])){
        $cont_id = $_POST['cont_id'];
    }
    $args = array(
        $product_id => array(
            'bag' => array(),
        ),
    );
    if($val == 0){
        $app_rate = 0;
    }else{
        if($ppc == 'cpp'){
            $app_rate = $val * $product_num;
        }else{
            $app_rate = $product_num / $val;
        }
    }
    $args[$product_id]['bag'][$cont_id] = array(
        'amount' => $app_rate,
        'unit' => $product_unit,
    );
    $save = indppl_apprates($store_id, $type, $args, true);
    // var_dump($save);
    $return = update_bag_package_table($store_id, $product_id, $type);
    // var_dump($save);
    echo $return;
    die();
}
add_action( 'wp_ajax_indppl_update_bag_app_rates_ajax', 'indppl_update_bag_app_rates_ajax' );
add_action('wp_ajax_nopriv_indppl_update_bag_app_rates_ajax', 'indppl_update_bag_app_rates_ajax');

function indppl_update_bag_app_rates_single_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['product_id'])){
        $product_id = $_POST['product_id'];
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    if(isset($_POST['val'])){
        $val = $_POST['val'];
    }
    if(isset($_POST['ppc'])){
        $ppc = $_POST['ppc'];
    }
    if(isset($_POST['product_num'])){
        $product_num = $_POST['product_num'];
    }
    if(isset($_POST['product_unit'])){
        $product_unit = $_POST['product_unit'];
    }
    if(isset($_POST['cont_id'])){
        $cont_id = $_POST['cont_id'];
    }
    if(isset($_POST['package_array'])){
        $package_array = $_POST['package_array'];
    }
    if($val == 0){
        $app_rate = 0;
    }else{
        $app_rate = $product_num / $val;
    }

    $args[$product_id]['bag'][$cont_id] = array(
        'amount' => $app_rate,
        'unit' => $product_unit,
    );
    $save = indppl_apprates($store_id, $type, $args, true);

    $bag = 1 / $val;
    $package_return_array = [];
    foreach($package_array as $key => $value){
        if($value['unit'] != $product_unit){
            $norm_args = array(
                array(
                    'unit' => $value['unit'],
                    'amount' => intval($value['num']),
                ),
            );
            $normal = indppl_normalize($norm_args, $product_unit);
            $num = $normal[0]['standard-amount'];
        }else{
            $num = $value['num'];
        }
        $package_num = $product_num / $num;
        $package_num = $val / $package_num;
        if($package_num < 1){
            if($package_num != 0){
                $package_num = 1 / $package_num;
            }
            $package_unit = '#bags / plant';
            $class = 'grey-text';
            $bag_type = 'cpp';
        }else{
            $package_unit = '#plants / bag';
            $class = 'indppl-dark-green';
            $bag_type = 'ppc';
        }
        $package_num = round($package_num, 2);
        ob_start();
        ?>
            <h4 class="indppl-bag-rate-num <?php echo $class; ?>" data-ppc="<?php echo $bag_type; ?>" data-num="<?php echo $package_num; ?>"><?php echo $package_num; ?></h4>
            <p class="indppl-bag-rate-unit <?php echo $class; ?>" data-unit="<?php echo $bag_type; ?>"><?php echo $package_unit; ?></p>
        <?php
        $package_return_array[] = ob_get_clean();
    }
    $return_array = array(
        'package' => $package_return_array,
        'bag' => round($bag, 2),
    );
    echo json_encode($return_array);
    die();
}
add_action( 'wp_ajax_indppl_update_bag_app_rates_single_ajax', 'indppl_update_bag_app_rates_single_ajax' );
add_action('wp_ajax_nopriv_indppl_update_bag_app_rates_single_ajax', 'indppl_update_bag_app_rates_single_ajax');

function indppl_get_sponsorship(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['id'])){
        $set_id = $_POST['id'];
        $hide = ' display-none ';
    }
    if(isset($_POST['brand_id'])){
        $set_brand_id = $_POST['brand_id'];
    }
    if(isset($_POST['product_id'])){
        $set_product_id = $_POST['product_id'];
    }
    ob_start();
    $user_id = get_current_user_id();
    $brand_array = get_terms( array(
        'taxonomy' => 'brand',
        'hide_empty' => false,
    ));
    $brands = array();
    foreach($brand_array as $key => $value){
        $brand_id = $value->term_id;
        $brand_name = $value->name;
        $brand_slug = $value->slug;
        $brand_save = $brand_slug . "-" . $brand_id;
        $user_meta = get_user_meta($user_id, $brand_save, true);
        if($user_meta == 1){
            $new_array = array(
                'name' => $brand_name,
                'slug' => $brand_slug,
                'id' => $brand_id,
            );
            array_push($brands, $new_array);
        }
    }
    ?>
    <div class='sponsorship-main-container'>
        <form method="post" action="" enctype="multipart/form-data" id="add-sponsor-form">
            <label for='indppl-add-sponsor-brand-select' class='<?php echo $hide; ?>'>Select your Brand</label>
            <select class='indppl-add-sponsor-brand-select <?php echo $hide; ?>' name='indppl-add-sponsor-brand-select' id='indppl-add-sponsor-brand-select'>
            <?php

            foreach($brands as $key => $value){
                $brand_id = $value['id'];
                $brand_name = $value['name'];
                $brand_slug = $value['slug'];
                $brand_save = $brand_slug . "-" . $brand_id;
                $selected = '';
                if($key == 0 || $brand_id == $set_brand_id){
                    $selected = 'selected';
                }
                ?>
                <option id='<?php echo $brand_save; ?>' value='<?php echo $brand_id; ?>' <?php echo $selected; ?>><?php echo $brand_name; ?></option>
                <?php
            }
            ?>
            </select>
            <?php
            $init_brand = $brands[0];
            ?>
            <label for='indppl-add-sponsor-product-select' class='<?php echo $hide; ?>'>Select your Product</label>
            <select class='indppl-add-sponsor-product-select <?php echo $hide; ?>' name='indppl-add-sponsor-product-select' id='indppl-add-sponsor-product-select'>
            <?php
            $args = array(
                'post_type' => 'product',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'brand',
                        'field'    => 'slug',
                        'terms'    => $init_brand['slug'],
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
            if($products->have_posts()){
                while($products->have_posts()){
                    $products->the_post();
                    $title = get_the_title();
                    $id = get_the_id();
                    $selected = '';
                    if($id == 0 || $id == $set_product_id){
                        $selected = 'selected';
                    }
                    ?>
                    
                    <option value="<?php echo $id; ?>" <?php echo $selected; ?>><?php echo $title; ?></option>
                    <?php
                }
            }
            // $img = 'https://via.placeholder.com/100.png';
            $img = home_url() . "/wp-content/uploads/2019/03/big-carrot.png";
            if($set_id){
                $img = get_post_meta($set_id, "wpcf-sponsorship-image", true);
            }
            ?>
            </select>
            <p><label for='add-sponsor-url'>URL:</label></p>
            <input type='text' name='add-sponsor-url' id='add-sponsor-url' class='margin-bottom-15 max-width-300' value='<?php echo get_post_meta($set_id, "wpcf-sponsor-url", true); ?>' placeholder='URL'>
            <label for='add-sponsor-copy'>Copy:</label>
            <textarea rows='4' cols='100' class='margin-bottom-15 max-width-300' name='add-sponsor-copy' placeholder='Enter Text Here' id='add-sponsor-copy'><?php echo get_post_meta($set_id, "wpcf-sponsorship-copy", true); ?></textarea>
            <img id='add-sponsor-img' class="margin-bottom-15" src="<?php echo $img; ?>">
            <input type='file' class="margin-bottom-15" id='add-sponsor-img-file' name='add-sponsor-img-file' value='<?php echo get_post_meta($set_id, "wpcf-sponsorship-image", true); ?>'/>
            <input type="submit" class="button" value="Save" id="sponsor-save" data-id='<?php echo $set_id; ?>'>
            <a href='#' id='indppl-delete-sponsor-btn' class='indppl-button' data-id='<?php echo $set_id; ?>'>Delete</a>
        </form>

        <?php
        $sponsor_count_array = json_decode(get_post_meta($set_id, 'wpcf-view-count', true), true);
        ?>
        <div class='sponsor-stats-container'>
            <h3>STATS</h3>
            <div class='sponsor-stats-sub-container'>
                <table class='sponsor-stats-table'>
                    <tr>
                        <th class='padding-right-10 padding-left-10'>Store Name</th>
                        <th class='padding-left-10'>Views</th>
                    </tr>
                    <?php
                    $total = 0;
                    if(is_array($sponsor_count_array)){
                        foreach($sponsor_count_array as $key => $value){
                            $title = get_the_title($key);
                            $store_total = 0;
                            foreach($value as $month => $num){
                                $store_total += $num;
                            }
                            ?>
                            <tr class="sponsor-view-count-btn indppl-table-color-offset">
                                <td class='padding-left-10'>
                                    <?php echo $title . ":"; ?>
                                </td>
                                <td class='padding-left-10'>
                                    <?php echo $store_total; ?>
                                </td>
                            </tr>
                            <tbody class='sponsor-view-count-hidden'>
                                <?php
                                $count = 0;
                                foreach($value as $month2 => $num2){
                                    $offset = '';
                                    if($count % 2 == 1){
                                        $offset = 'offset_background';
                                    }
                                    ?>
                                    <tr class='<?php echo $offset; ?> indppl-table-color-offset'>
                                        <td class='padding-left-20'>
                                            <?php echo $month2 . ":"; ?>
                                        </td>
                                        <td class='padding-left-20'>
                                            <?php echo $num2; ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $count++;
                                }
                                
                                ?>
                            </tbody>
                            
                            <?php
                            $total += $store_total;
                        }
                    }else{
                        ?>
                        <tr class='indppl-table-color-offset'>
                            <td class='padding-left-10'>
                                No Views
                            </td>
                            <td class='padding-left-10'>
                                
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr class="indppl-table-color-offset">
                        <td class='ind-bold padding-left-10'>Total: </td>
                        <td class='padding-left-10'><?php echo $total; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php


    $return = ob_get_clean();
    echo $return;
    die();
}
add_action( 'wp_ajax_indppl_get_sponsorship', 'indppl_get_sponsorship' );
add_action('wp_ajax_nopriv_indppl_get_sponsorship', 'indppl_get_sponsorship');

function indppl_save_sponsorship(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['brand_id'])){
        $brand_id = $_POST['brand_id'];
    }
    if(isset($_POST['product_id'])){
        $product_id = $_POST['product_id'];
    }
    if(isset($_POST['url'])){
        $url = $_POST['url'];
    }
    if(isset($_POST['copy'])){
        $copy = $_POST['copy'];
    }
    if(isset($_POST['id'])){
        $set_id = $_POST['id'];
    }
    if(isset($_POST['img'])){
        $old_img = $_POST['img'];
    }
    $brand_tax = get_term_by('id', $brand_id, 'brand');
    $brand = $brand_tax->name;
    $product = get_the_title($product_id);
    // var_dump($product);
    // var_dump($_FILES['file']);

    $img_url = indppl_image_upload();
    $postarr = array(
        "ID" => $set_id,
        "post_author" => get_current_user_id(),
        "post_title" => $brand . " " . $product . " sponsorship",
        'post_type' => 'sponsorship',
        'post_status' => "publish",
        'meta_input' => array(
            'wpcf-sponsorship-image' => $img_url,
            'wpcf-sponsorship-copy' => $copy,
            'wpcf-sponsorship-active' => 1,
            'wpcf-sponsor-url' => $url,
            'brand_id' => $brand_id,
            'product_id' => $product_id,
        ),
    );
    if(!$img_url && $old_img){
        $postarr['meta_input']['wpcf-sponsorship-image'] = $old_img;
    }
    $sponsorship_id = wp_insert_post($postarr);    
    $connection = toolset_connect_posts('sponsorship-product', $sponsorship_id, $product_id);
    $refresh = pp_sponsor_management();
    $return_array = [];
    $return_array['refresh'] = $refresh;
    $return_array['img'] = $img_url;
    echo json_encode($return_array);
    die();
}
add_action( 'wp_ajax_indppl_save_sponsorship', 'indppl_save_sponsorship' );
add_action('wp_ajax_nopriv_indppl_save_sponsorship', 'indppl_save_sponsorship');

function indppl_delete_sponsorship(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['id'])){
        $id = $_POST['id'];
    }
    if(isset($_POST['product_id'])){
        $product_id = $_POST['product_id'];
    }
    delete_post_meta($id, 'wpcf-sponsorship-active');
    $refresh = pp_sponsor_management();
    $return_array = [];
    $return_array['refresh'] = $refresh;
    echo $refresh;
    die();
}
add_action( 'wp_ajax_indppl_delete_sponsorship', 'indppl_delete_sponsorship' );
add_action('wp_ajax_nopriv_indppl_delete_sponsorship', 'indppl_delete_sponsorship');

function indppl_upload_guide_image_ajax(){
    echo indppl_image_upload();
    die();
}
add_action('wp_ajax_indppl_upload_guide_image_ajax', 'indppl_upload_guide_image_ajax');

function indppl_store_progress_bar_ajax(){

    $store = htmlspecialchars($_POST['store_id']);
    $response = indppl_store_progress_bar($store, TRUE, FALSE);
    echo $response['bar'];
    die();
}

add_action('wp_ajax_indppl_store_progress_bar_ajax', 'indppl_store_progress_bar_ajax');

function indppl_duplicate_store_ajax(){

    // Get all the options...
    $store = htmlspecialchars($_POST['store']);

    $new_details = array();
    $new_details['title'] = htmlspecialchars($_POST['storeName']);
    $new_details['address1'] = htmlspecialchars($_POST['address1']);
    $new_details['address2'] = htmlspecialchars($_POST['address2']);
    $new_details['city'] = htmlspecialchars($_POST['city']);
    $new_details['state'] = htmlspecialchars($_POST['state']);
    $new_details['zip'] = htmlspecialchars($_POST['zip']);
    $new_details['url'] = htmlspecialchars($_POST['webURL']);
    $new_details['phone'] = htmlspecialchars($_POST['phone']);
    $new_details['email'] = htmlspecialchars($_POST['email']);

    indppl_duplicate_store($store, $new_details);

    echo "done";

    die();

}

add_action('wp_ajax_indppl_duplicate_store_ajax', 'indppl_duplicate_store_ajax');

//Setup the prep form for copying a store...
function indppl_copy_store_form_ajax(){ 
    
    $store = $_POST['store'];
    $meta = get_post_meta($store);
    $email = '';
    if($meta['wpcf-email'][0]){
        $email = $meta['wpcf-email'][0];
    }
    $website = '';
    if($meta['wpcf-weburl'][0]){
        $website = $meta['wpcf-weburl'][0];
    }
    // var_dump($meta);
    ?>
    <h1>Store Duplication</h1>
    <p>Please enter the address & contact information for this store.</p>
    <form method="post" action="//localhost/my-account/store-profile/" id="store-duplication-form" class="form-horizontal" enctype="multipart/form-data" _lpchecked="1" onsubmit="return indpplCheckAddedStore(this);" >
		<fieldset>
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="store-name">Store Name</label>
			<div class="col-md-4">
			<input id="store-name" name="store-name" type="text" placeholder="" class="form-control duplicate-store-required input-md" required="" value="" style="">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="address1">Address Line 1</label>
			<div class="col-md-4">
			<input id="address1" name="address1" type="text" placeholder="" class="form-control input-md duplicate-store-required" required="" value="">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="address2">Address Line 2</label>
			<div class="col-md-4">
			<input id="address2" name="address2" type="text" placeholder="" class="form-control input-md" value="">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="city">City</label>
			<div class="col-md-4">
			<input id="city" name="city" type="text" placeholder="" class="form-control input-md duplicate-store-required" required="" value="">
                
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="state">State</label>
			<div class="state-selector">
			<select id="state" name="state" type="text" placeholder="" class="form-control input-md" required="" value="">
                <option value="AL">AL</option>
                <option value="AK">AK</option>
                <option value="AZ">AZ</option>
                <option value="AR">AR</option>
                <option value="CA">CA</option>
                <option value="CO">CO</option>
                <option value="CT">CT</option>
                <option value="DE">DE</option>
                <option value="FL">FL</option>
                <option value="GA">GA</option>
                <option value="HI">HI</option>
                <option value="ID">ID</option>
                <option value="IL">IL</option>
                <option value="IN">IN</option>
                <option value="IA">IA</option>
                <option value="KS">KS</option>
                <option value="KY">KY</option>
                <option value="LA">LA</option>
                <option value="ME">ME</option>
                <option value="MD">MD</option>
                <option value="MA">MA</option>
                <option value="MI">MI</option>
                <option value="MN">MN</option>
                <option value="MS">MS</option>
                <option value="MO">MO</option>
                <option value="MT">MT</option>
                <option value="NE">NE</option>
                <option value="NV">NV</option>
                <option value="NH">NH</option>
                <option value="NJ">NJ</option>
                <option value="NM">NM</option>
                <option value="NY">NY</option>
                <option value="NC">NC</option>
                <option value="ND">ND</option>
                <option value="OH">OH</option>
                <option value="OK">OK</option>
                <option value="OR">OR</option>
                <option value="PA">PA</option>
                <option value="RI">RI</option>
                <option value="SC">SC</option>
                <option value="SD">SD</option>
                <option value="TN">TN</option>
                <option value="TX">TX</option>
                <option value="UT">UT</option>
                <option value="VT">VT</option>
                <option value="VA">VA</option>
                <option value="WA">WA</option>
                <option value="WV">WV</option>
                <option value="WI">WI</option>
                <option value="WY">WY</option>
            </select>
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="zip">Zipcode</label>
			<div class="col-md-2">
			<input id="zip" name="zip" type="text" placeholder="" class="form-control input-md duplicate-store-required" required="" value="">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="weburl">Store Website</label>
			<div class="col-md-4">
			<input id="weburl" name="weburl" type="text" placeholder="" class="form-control input-md" value="<?php echo $website; ?>">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="phone">Phone Number</label>
			<div class="col-md-4">
			<input id="phone" name="phone" type="text" placeholder="" class="form-control input-md duplicate-store-required" required="" value="">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
                <label class="col-md-4 control-label" for="store-email">Email Address</label>
                <div class="col-md-4">
                    <input id="store-email" name="store-email" type="text" placeholder="" class="form-control input-md duplicate-store-required" required="" value="<?php echo $email; ?>">
                
                </div>
			</div>

            <!-- <div class="form-group">
                <div class=" indppl-flex indppl-no-wrap" style="max-width: 600px; margin:auto;align-items:center;">
                    <input id="billing" name="billing" type="checkbox" class="form-control input-md" style="height:auto; width: auto;" required> 
                    <p style="margin-bottom: 0; margin-left:10px;">I understand that I will be billed an additional subscription.</p>
                    
                
                </div>
			</div> -->

            			<!-- Button -->
			<div class="form-group">
                <label class="col-md-4 control-label" for="submit"></label>
                <div class="col-md-4">
                    <p class="submit"><input type="submit" name="submit" id="store-duplicate" class="button button-primary" value="Create Store" data-store="<?php echo $store; ?>"></p>
                </div>
			</div>
            
		</fieldset>
    </form>

    <?php 
    
    die();
}


add_action('wp_ajax_indppl_copy_store_form_ajax', 'indppl_copy_store_form_ajax');

function indppl_delete_store_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['id'])){
        $id = $_POST['id'];
    }
    indppl_notify_deleted_store($id, get_current_user() );
    $return = wp_delete_post($id, true);
    if($return){
        $return = do_shortcode('[pp-my-stores]');
    }
    echo $return;
    die();
}
add_action( 'wp_ajax_indppl_delete_store_ajax', 'indppl_delete_store_ajax' );
add_action('wp_ajax_nopriv_indppl_delete_store_ajax', 'indppl_delete_store_ajax');

function indppl_store_go_live_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['id'])){
        $id = $_POST['id'];
    }
    $return = update_post_meta($id, 'wpcf-issetup', 1);
    echo $return;
    die();
}
add_action( 'wp_ajax_indppl_store_go_live_ajax', 'indppl_store_go_live_ajax' );
add_action('wp_ajax_nopriv_indppl_store_go_live_ajax', 'indppl_store_go_live_ajax');

function indppl_store_deactivate_ajax(){
    if(isset($_POST['version_check'])){
        if($_POST['version_check'] != 1.0){
            exit;
            die();
        }
    }else{
        exit;
        die();
    }
    if(isset($_POST['id'])){
        $id = $_POST['id'];
    }
    $return = update_post_meta($id, 'wpcf-issetup', 0);
    echo $return;
    die();
}
add_action( 'wp_ajax_indppl_store_deactivate_ajax', 'indppl_store_deactivate_ajax' );
add_action('wp_ajax_nopriv_indppl_store_deactivate_ajax', 'indppl_store_deactivate_ajax');

function indppl_dup_auth_form_ajax(){

    if($_POST['version_check'] == '1.0'){
        
        $store = htmlspecialchars($_POST['store']); ?>

        <h1>Manage Authorized Users</h1>
        <p>The users below have permission to duplicate your store's containers, application rates, and planting guides. Removing a user from this list does not remove a store that they have already duplicated!</p>
        <form style="max-width: 400px;">
            <label for="auth-email">Add Authorized User by Email:</label>
            <div class="indppl-flex indppl-no-wrap">
                <input type="text" data-store="<?php echo $store; ?>" name="authorized_email" id="auth-email" style="border-top-right-radius: 0;
    border-bottom-right-radius: 0;">
                <input type="submit" value="Add User" id="indppl-add-auth-user" class="orange-bg" style="border-top-left-radius: 0;
    border-bottom-left-radius: 0;">
            </div>
        </form>

        <?php $results = indppl_get_dup_auth($store);

        echo "<h2>Users authorized to duplicate your store</h2>";
        echo "<ul id='authorized-users' class='style-free'>";
        foreach($results as $key => $val){
            $username = "Not a member yet";
            $auth_user = get_user_by( 'email', $val['user_email'] );
            if($auth_user){
                $auth_data = get_userdata($auth_user->ID);
                $username = $auth_data->first_name . " " . $auth_data->last_name;
            }
            echo "<li>{$val['user_email']} ({$username}) <a href='#' class='remove-auth' data-id='{$val['auth_id']}' data-email='{$val['user_email']}' style='color:red'>remove access</a></li>";
        }
        echo "</ul>";

    }

    die();

}
add_action('wp_ajax_indppl_dup_auth_form_ajax', 'indppl_dup_auth_form_ajax');
add_action('wp_ajax_nopriv_indppl_dup_auth_form_ajax', 'indppl_dup_auth_form_ajax');

function indppl_auth_users_ajax(){
    
    if($_POST['process'] == 'add'){

        $store = htmlspecialchars($_POST['store']);
        $email = htmlspecialchars($_POST['email']);
        if($email == ''){
            $response = 'error';
        } else {
            $args = array(
                'store_id' => $store,
                'user_email' => $email,
            );
    
            $response = indppl_dup_auth($args);
    
            if($response[0]){
                $username  = "Not a member yet";
                $auth_user = get_user_by('email', $email);
                if ($auth_user) {
                    $auth_data = get_userdata($auth_user->ID);
                    $username  = $auth_data->first_name . " " . $auth_data->last_name;
                }
    
                $response = "<li>{$email} ({$username}) <a href='#' data-id='{$response[1]}' class='remove-auth' style='color:red;'>remove access</a></li>";
    
                echo $response;
    
            } else {
                $response = 'There was a problem. Please refresh and try again.';
            }
        }

    } elseif($_POST['process'] == 'remove'){

        $auth_id = $_POST['auth'];
        $response = indppl_dup_auth($auth_id, 'delete');
        if($response){
            echo "success";
        } else {
            echo "error";
        }

    } else {
        echo "<li>There was an error. Please refresh and try again... If this persists contact support.</li>";
    }


    die();
}
add_action('wp_ajax_indppl_auth_users_ajax', 'indppl_auth_users_ajax');
add_action('wp_ajax_nopriv_indppl_auth_users_ajax', 'indppl_auth_users_ajax');

function indppl_delete_container_ajax(){
    if($_POST['version_check'] == '1.0'){
        if(isset($_POST['id'])){
            $id = $_POST['id'];
        }
        if(isset($_POST['store_id'])){
            $store_id = $_POST['store_id'];
        }
        $result = indppl_delete_apprate($store_id, $id);
        wp_delete_post($id);
        echo $id;
    }
    die();
}
add_action('wp_ajax_indppl_delete_container_ajax', 'indppl_delete_container_ajax');
add_action('wp_ajax_nopriv_indppl_delete_container_ajax', 'indppl_delete_container_ajax');