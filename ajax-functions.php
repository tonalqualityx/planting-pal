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
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    
    ob_start();
    ?>
    
        <div class='slide-in-products-inside-container'>
            <a href='#' class='modal-close'>X</a>
            <h2>something <?php echo $type; ?></h2>
            <form id='product-create-form' method="post" action='#' class="form-horizontal">
                <input type='hidden' name='indppl-modal-product-type' id='indppl-modal-product-type' value=<?php echo $type; ?>>
                <select class='product-create-brand' id='product-create-brand' name='product-create-brand'>
                    <option value='' disabled selected>Select Brand</option>
                    <?php
                    $brands = get_terms('brand');
                    foreach($brands as $key => $value){
                        ?> <option value="<?php echo $value->slug; ?>"><?php echo $value->name; ?> <?php
                    }
                    // var_dump($brands);
                    ?>
                </select>
                <select class='product-create-product' id='product-create-product' name='product-create-product'>
                    <option class='product-create-product-option' value='' disabled selected>Select Product</option>
                </select>
                <div class='product-create-brand-cut-off'>
                    <div class='product-create-dry-wet-container'>
                    </div>
                    <div class='product-create-standard-unit-container'>
                    </div>
                    <div class='product-create-size-container'>
                    </div>
                    <div class='product-create-new-size-container'>
                    </div>
                    <div class='product-create-app-rate-container'>
                    </div>
                    <div class='product-create-5-cups-container'>
                    </div>
                    <div class='product-create-save-done-container'>
                    </div>
                    <div class='product-create-app-rates-chart-container'>
                    </div>
                </div>
            </form>
        </div>
    <?php
    $return = ob_get_clean();
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
    $default = get_post_meta($product_id, 'wpcf-default', true);
    $unit = get_post_meta($product_id, 'wpcf-unit', true);
    $dryliquid = get_post_meta($product_id, 'wpcf-dryliquid', true);
    
    $send_array = array();
    if($default){
        $standard_unit = "<div id='product-create-standard-unit' data-unit='" . $unit . "'></div>";
    }else{
        ob_start();
        ?>
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
    // getting sizes
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
    $sizes = '';
    $pack_units = [];
    ob_start();
    ?>
    <h3>Select the sizes you stock:</h3>
    <?php
    if($product_related){
        foreach ($product_related as $key => $value) {
            $size_meta = get_post_meta($value, 'wpcf-size', true);
            $unit_meta = get_post_meta($value, 'wpcf-unit', true);
            $pack_units[] = [$size_meta, $unit_meta];
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
    
    if($cups_active){
        ob_start();
        ?>
        <h3>How much does 5 level coups of this product weigh?</h3>
        <div class='product-create-5-cups-inside-container'>
            <input type='number' class='indppl-product-create-cups-num' id='indpll-product-create-cups-num' min='0' name='indppl-product-create-cups-num'>
            <select class='product-create-5-cups' id='product-create-5-cups' name='product-create-5-cups'>
                <option class='product-create-5-cups-option' value='' disabled selected>Select Unit</option>
                <option class='product-create-5-cups-option' value='lb' >lb</option>
                <option class='product-create-5-cups-option' value='g' >g</option>
                <option class='product-create-5-cups-option' value='kg' >kg</option>
                <option class='product-create-5-cups-option' value='oz' >oz</option>
            </select>
        </div>
        <?php
        // $console = $unit;
        
        $cups = ob_get_clean();
    }

    // app rates chart container
    $app_rates_chart = update_package_table($store_id, $product_id, $type);

    ob_start();
    ?>
    <input type="submit" name="product-create-next" data-exit="true" id="product-create-next" class="product-create-submit" value="Next">
    <?php
    $next_btn = ob_get_clean();
    
    $send_array['standard_unit'] = $standard_unit;
    $send_array['dry_wet'] = array(0 => $dry_wet, 1 => $dryliquid, 2=> $unit);
    $send_array['size'] = $sizes;
    $send_array['new_size'] = $new_size;
    // $send_array['app_rate'] = $app_rate;
    $send_array['cups'] = $cups;
    $send_array['app_rates_chart'] = $app_rates_chart;
    $send_array['next_btn'] = $next_btn;
    $send_array['console'] = $console;
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
    $send_array = array($product_id => array());
    foreach($product_rate as $key => $value){
        $temp = array(
                'unit' => $product_unit[$key]['value'],
                'amount' => $value['value'],
        );
        $send_array[$product_id]['containers'][$value['name']] = $temp;
    }
    $save = indppl_apprates($store_id, $type, $send_array);
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
    $updated_app_rates = update_package_table($store_id, $product_id, $type);
    echo $updated_app_rates;
    die();
}
add_action( 'wp_ajax_indppl_save_product_ajax', 'indppl_save_product_ajax' );
add_action('wp_ajax_nopriv_indppl_save_product_ajax', 'indppl_save_product_ajax');


