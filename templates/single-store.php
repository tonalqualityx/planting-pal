<?php
//Single Store Template
//Use this file to collect input from end users on what they'll be planting

wp_head();

$storeid = get_the_ID(  );
$user_plants = array();
$display = 'plants_form';

if(isset($_POST['next-step'])){
    $display = $_POST['next-step'];
}

if(isset($_POST['next-step']) && $_POST['next-step'] == 'shopping_list'){

    $display = htmlspecialchars($_POST['next-step']);
    $apprates = indppl_apprates($storeid);
    $products = array();
    
    $ground = $_POST['ground'];
    $ground = array_filter($ground);
    
    // Quick fix - REFACTOR
    foreach($apprates['ground'] as $prod => $data){
        if(key($data) == 'bag'){
            $apprates['ground'][$prod]['containers'] = $data['bag'];
        }
    }
    
    // var_dump($apprates);
    
    // $ground_rates = array_merge($apprates['ground']['containers']);
    
    foreach($ground as $container => $count){
        $user_plants['ground'][] = $count;
        foreach($apprates['ground'] as $key => $val) {
            if(array_key_exists($container, $apprates['ground'][$key]['containers'])){
                $product = get_the_title($key);
                $standard = get_post_meta($key, 'wpcf-unit', TRUE);
                $brand = get_the_terms($key, 'brand');
                $cups = get_post_meta($key, 'wpcf-5cups', TRUE);
                $brand = $brand[0];
                $plant = get_the_title($container);
                $amount = $apprates['ground'][$key]['containers'][$container]['amount'] * $count;
                // echo "<h1>$amount</h1>";

                if($standard != 'each'){
                    $unit = $apprates['ground'][$key]['containers'][$container]['unit'];
                    $unit_args = array(array('unit' => $unit, 'amount' => $amount));
                    
                    $normalized = indppl_normalize($unit_args, $standard, $cups);
                    
                    if($standard != 'lb'){
                        $need = getVolume($amount, $unit, $standard);
                        // echo "<h2>Volume: $need $standard</h2>";
                    } else {
                        $calc = getDensity($cups, $unit);
                        $cups1 = $cups/5;
                        $amount_cups = getVolume($amount, $unit, 'cup');
                        $need = $amount_cups * $cups1;
                        // echo "<h2>Weight: 5 cups = $cups lbs so 1 cup = $cups1 lbs so $amount $unit = $need lbs</h2>";
                    }
                } 
                // var_dump($need);
                if($amount > 0){
                    $need = 0;
                    foreach($normalized as $k => $v) {
                        if($standard != 'each'){
                            $need += $v['standard-amount']; 
                        } else {
                            $need = $amount;
                        }
                    }
    
                    if(isset($products[$key])){
                        $products[$key]['need'] += $need;
                        // echo "true";
                    } else {
                        $products[$key]['name'] = $brand->name . " " . $product;
                        $products[$key]['need'] = $need;
                        $products[$key]['unit'] = $standard;
                    }
                }


            }
        }
    }

    // Check for pots
    if(isset($_POST['pots']) && isset($apprates['pots'])){

        $pots = $_POST['pots'];
        $user_plants['pots'] = $pots;

        // Loop through however many rows of pots were added, then each type of apprate
        $i = 0;
        foreach($pots['length'] as $pot){
            foreach($apprates['pots'] as $type => $prods){

                // On refactor turn this into a function that returns an array - it's used elsewhere
                foreach($prods as $prod => $rates){

                    $product  = get_the_title($prod);
                    $standard = get_post_meta($prod, 'wpcf-unit', TRUE);
                    $brand = get_the_terms($prod, 'brand');
                    $cups  = get_post_meta($prod, 'wpcf-5cups', TRUE);
                    
                    $brand = $brand[0];
                    
                    // Check if it's full height or just some
                    if($pots['need'][$i] > 0){
                        $pots['height'][$i] = $pots['need'][$i];
                    }
                    
                    // Convert capacity to cups because that's what Chuck did...
                    $ci = intval($pots['qty'][$i]) * intval($pots['length'][$i]) * intval($pots['width'][$i]) * intval($pots['height'][$i]);

                    $cuft = getVolume($ci, 'ci', 'cuft');

                    $sqft = (intval($pots['qty'][$i]) * intval($pots['length'][$i]) * intval($pots['width'][$i]))/144;
                    
                    switch($type){
                        
                        case 'filler':
                            $fill_rate = intval($rates['amount'])/100;
                            $amount = $cuft * $fill_rate;
                            $unit_args = array(array('unit' => 'cuft', 'amount' => $amount));

                            $normalized = indppl_normalize($unit_args, $standard, $cups);
                            $need = $normalized[0]['standard-amount'];
                            break;
                            
                        case 'blended' :
                            // Calculate the blended rates
                            $fill_rate = $rates['amount'] * $cuft;
                            $unit_args = array(array('unit' => $rates['unit'], 'amount' => $fill_rate));
                            $normalized = indppl_normalize($unit_args, $standard, $cups);
                            $need = $normalized[0]['standard-amount'];
                            
                            break;
                            
                            case 'surface':
                            
                            // Calculate the surface rates
                            $fill_rate = ($rates['amount'] * $sqft)/$rates['per-sqft'];
                            $unit_args = array(array('unit' => $rates['unit'], 'amount' => $fill_rate));
                            $normalized = indppl_normalize($unit_args, $standard, $cups);
                            $need = $normalized[0]['standard-amount'];
                            
                            break;
                            
                        case 'each':
                            
                            // Multiply the eaches
                            if($pots['width'][$i] < 8){
                                $need = $pots['qty'][$i] * $rates['small']; 
                            } elseif($pots['width'][$i] >= 8 && $pots['width'][$i] < 24){
                                $need = $pots['qty'][$i] * $rates['medium'];
                            } elseif($pots['width'][$i] >= 24){
                                $need = $pots['qty'][$i] * $rates['large'];
                            }
                            break;
                    }
                        
                    // Check if product is in list, if so add standard units
                    if($need > 0){

                        if(array_key_exists($prod, $products)){
                            $products[$prod]['need'] += $need;
                        } else {
                            // If not, just add it and set the unit
                            $products[$prod]['name'] = $brand->name . " " . $product;
                            $products[$prod]['need'] = $need;
                            $products[$prod]['unit'] = $standard;

                        }

                    }
                }
            } 

            $i++;
        }


    }

    if (isset($_POST['beds']) && isset($apprates['beds'])) {

        $beds = $_POST['beds'];
        $user_plants['beds'] = $beds;

        // Loop through however many rows of beds were added, then each type of apprate
        // THE NEXT MAJOR SECTION SHOULD BE REFACTORED TO MERGE WITH THE POTS SECTION
        $i = 0;
        foreach ($beds['length'] as $pot) {
            foreach ($apprates['beds'] as $type => $prods) {
                // On refactor turn this into a function that returns an array - it's used elsewhere
                foreach ($prods as $prod => $rates) {

                    $product  = get_the_title($prod);
                    $standard = get_post_meta($prod, 'wpcf-unit', TRUE);
                    $brand    = get_the_terms($prod, 'brand');
                    $cups     = get_post_meta($prod, 'wpcf-5cups', TRUE);
                    $brand    = $brand[0];

                    // Check if it's full height or just some
                    if ($beds['need'][$i] > 0) {
                        $beds['height'][$i] = $beds['need'][$i];
                    }

                    // Convert capacity to cups because that's what Chuck did...
                    $ci = intval($beds['qty'][$i]) * intval($beds['length'][$i]) * intval($beds['width'][$i]) * intval($beds['height'][$i]);

                    $cuft = getVolume($ci, 'ci', 'cuft');

                    $sqft = (intval($beds['qty'][$i]) * intval($beds['length'][$i]) * intval($beds['width'][$i])) / 144;

                    switch ($type) {

                    case 'filler':
                        $fill_rate  = intval($rates['amount']) / 100;
                        $amount     = $cuft * $fill_rate;
                        $unit_args  = array(array('unit' => 'cuft', 'amount' => $amount));
                        $normalized = indppl_normalize($unit_args, $standard, $cups);
                        $need       = $normalized[0]['standard-amount'];
                        break;

                    case 'blended':
                        // Calculate the blended rates
                        $fill_rate  = $rates['amount'] * $cuft;
                        $unit_args  = array(array('unit' => $rates['unit'], 'amount' => $fill_rate));
                        $normalized = indppl_normalize($unit_args, $standard, $cups);
                        $need       = $normalized[0]['standard-amount'];

                        break;

                    case 'surface':

                        // Calculate the surface rates
                        $fill_rate  = ($rates['amount'] * $sqft) / $rates['per-sqft'];
                        $unit_args  = array(array('unit' => $rates['unit'], 'amount' => $fill_rate));
                        $normalized = indppl_normalize($unit_args, $standard, $cups);
                        $need       = $normalized[0]['standard-amount'];

                        break;
                    }

                    if($need > 0){

                        // Check if product is in list, if so add standard units
                        if (array_key_exists($prod,$products)) {
                            $products[$prod]['need'] += $need;
                        } else {
                            // If not, just add it and set the unit
                            $products[$prod]['name'] = $brand->name . " " . $product;
                            $products[$prod]['need'] = $need;
                            $products[$prod]['unit'] = $standard;

                        }

                    }

                }
            }

            $i++;
        }

    }

    // echo "<h1>PRODUCTS</h1>";
    // var_dump($products);
    // Check for beds

    // Add beds products to product list

    // Calculate the shopping list!
    $shopping_list = array();
    foreach($products as $key => $val) {
        // Setup the important values
        $standard = get_post_meta( $key, 'wpcf-unit', TRUE);
        // echo "<h1>List Standard $standard</h1>";
        $cups = get_post_meta($key, 'wpcf-5cups', TRUE);
        $brand = get_the_terms( $key, 'brand' );
        $brand = $brand[0]->name;
        $packages = toolset_get_related_posts($key, 'product-package', ['query_by_role' => 'parent', 'role_to_return' => 'child', 'return' => 'post_id'] );
        $normalized_packs = array();

        // Create a conversion array and fill it with all the package sizes for conversion
        $convert = array();
        foreach($packages as $package) {
            $amount = get_post_meta($package, 'wpcf-size', TRUE);
            $unit = get_post_meta($package, 'wpcf-unit', TRUE);
            if($unit == 'each') {
                $normalized_packs[$amount . " " . $unit] = array(
                    "amount" => $amount,
                    "unit" => $unit,
                    "standard" => $standard,
                    "standard-amount" => $amount,
                );
            } else {
                $convert[$amount . " " . $unit] = array(
                    'amount' => $amount,
                    'unit'  => $unit,
                );
            }
        } 
        
        if($standard == 'each'){

        } else {
            $normalized_packs = indppl_normalize($convert, $standard, $cups);
        }

        // var_dump($normalized_packs);
        // echo "<br /><br />";
        // var_dump($convert);
        // echo "<h3>Normalization</h3>";
        // var_dump($normalized_packs);
        // echo "<br /><br /><h4>Sorted</h4>";
        // Sort the packages from larges to smallest
        uasort($normalized_packs, function($b, $a) {
            return $a['standard-amount'] <=> $b['standard-amount'];
        });

        // Find the largest package that the needed amount is larger than
        $ref = &$normalized_packs;
        $check_last = array_keys($normalized_packs);
        $last_pack = array_pop($check_last);
        $skipped_packs = array();
        // echo "<h2>{$val['need']}</h2>";
        foreach($ref as $pack_key => $pack) {

            if($val['need'] >= $pack['standard-amount']){
                $pack_count = $val['need']/$pack['standard-amount'];
                $whole = floor($pack_count);
                $dec = $pack_count - $whole;
                $pack_name = $pack_key;

                // If the remainder is more than 15% we need to round up
                if($dec > 0.15){
                    $whole++;
                    $new_amount = $whole * $pack['standard-amount'];
                    foreach($skipped_packs as $k => $v){
                        if($new_amount >= $v['standard-amount']) {
                            $whole = 1;
                            $pack_name = $v['name'];
                        }
                    }
                }

                $pack_count = $whole;
                $shopping_list[$key] = array(
                    'count' => $pack_count,
                    'name' => $pack_name,
                    'product' => $val['name'],
                    'brand' => $brand,
                    'unit'  => $pack['unit'],
                ); 
                // echo "<h3>$pack_count $pack_name {$val['name']}</h3> ";
                break;

            } else {

                if($pack_key == $last_pack){

                    $shopping_list[$key] = array(
                        'count'   => 1,
                        'name'    => $pack_key,
                        'product' => $val['name'],
                        'brand'   => $brand,
                    );

                } else {

                    // Add the skipped package to the array so we can compare rounded values later
                    $skipped_packs[] = array(
                        'standard-amount'   => $pack['standard-amount'],
                        'name'              => $pack_key,
                    );

                }
            }
        }

    }

    $user_plants = json_encode( $user_plants );
    $encoded_shopping_list = json_encode($shopping_list);
}


?>

<body>
    <!-- <div class="desktopWarning">
        <p class="desktopWarning-p">This site is optimized for mobile phones in portrait layout.</p><i class="material-icons d-block portrait-only">screen_lock_portrait</i>
    </div> -->
    <div class="container"><img src="<?php echo INDPPL_ROOT_URL; ?>/assets/img/general-logo-x2.png" id="logo-header"></div>
    <?php include(INDPPL_ROOT_PATH . "/templates/template_parts/" . $display . ".php"); ?>
    <?php echo wp_footer(); ?>
</body>