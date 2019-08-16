<?php defined('ABSPATH') or die('Sectumsempra!'); //For enemies ?>

<?php // GET THE APPROPRIATE APPLICATION RATES

$print_apprates = '';
$s = '';
$cur_cups = get_post_meta( $product['id'], 'wpcf-5cups', TRUE );
$dry = get_post_meta($product['id'], 'wpcf-dryliquid', TRUE);
$bag = explode(" ", $list[$product['id']]['name']);
$volume = false;
$volume_units = indppl_get_units('volume');
if(in_array($bag[1], $volume_units) ){
    $volume = true;
}

// Determine type
if($type == 'ground'){ // If guide is in ground
    $decode_plants = json_decode( $plants, TRUE );
    foreach($ground_list as $gid => $g){
        
        $fraction_int = '';
        // Check if there's an apprate for this product
        if(isset($guide_rates[$type][$product['id']]['containers'][$gid]['amount']) || isset($guide_rates[$type][$product['id']]['bag'][$gid]['amount'])){
            $parse_by = key($guide_rates[$type][$product['id']]);
            // var_dump($guide_rates[$type][$product['id']]);  
            switch ($parse_by) {
                case 'bag':

                    // If by bag then parse how much of the bag to use for this portion (based on bag size)
                    $cur_unit = $guide_rates[$type][$product['id']][$parse_by][$gid]['unit'];
                    $cur_amount = $guide_rates[$type][$product['id']][$parse_by][$gid]['amount'];

                    $cur_items = array(
                        array(
                            'amount' => $cur_amount,
                            'unit'  => $cur_unit,
                        )
                    );
                    
                    $cur_normalized = indppl_normalize($cur_items, $bag[1], $cur_cups);

                    $fraction = $cur_normalized[0]['standard-amount']/$bag[0];

                    // Is the amount required larger than the bag?
                    if($cur_normalized[0] > $bag[0] && $fraction > 1){
                        $fraction_int = floor($fraction - 1);
                        $fraction = $fraction - $fraction_int;
                    }
                    
                    // $fraction = 1/$fraction;
                    if($fraction >= 0.15){

                        // Fix qt-d & qt-l
                        $cur_unit = " of a {$bag[0]} {$bag[1]} package";
                        $fraction = indppl_readable_fraction($fraction);
                        if($fraction == floor($fraction) && $fraction_int != ''){
                            $fraction = $fraction_int + $fraction;
                            $fraction_int = '';
                        } elseif($fraction_int != '' && $fraction_int > 0) {
                            $fraction_int = $fraction_int . " & ";
                        } 
                        $cur_amount = $fraction_int . $fraction;

                    } else {
                        // Now determine if that's a reasonable fraction to manage - if not set the variables as cups...
                        $new_normalized = indppl_normalize($cur_items, 'cup', $cur_cups);
                        $cur_amount = round($new_normalized[0]['standard-amount'], 1);
                        if($cur_amount > 1){
                            $s = 's';
                        }
                        $cur_unit = 'cup' . $s ;
                        
                    }
                    break;



                // Next, just fall through to set the values...
                default:
                    $cur_amount = round($guide_rates[$type][$product['id']][$parse_by][$gid]['amount'], 1);
                    $cur_unit   = $guide_rates[$type][$product['id']][$parse_by][$gid]['unit'];
                    break;
            }

            $print_apprates .= "" .  get_the_title($gid) . " (" . $cur_amount . " " . $cur_unit . "), "; 
        }

    } 
} elseif($type == 'pots' || $type = 'beds') { // If guide is pots or beds
    $cur_rates = null;
    // var_dump($guide_rates[$type]);
    $pi = 0;
    foreach($plants[$type]['qty'] as $pot){

        $proceed = false;
        $fraction = 0;
        $fraction_int = '';
        $s = '';
        $cur_rates = null;
        $cur_sqft = $plants[$type]['length'][$pi] * $plants[$type]['width'][$pi];
        $cur_sqft = $cur_sqft/144;
        $cur_need = 0;

        if($plants[$type]['need'][$pi] != ''){
            $cur_need = $plants[$type]['need'][$pi];
        } else {
            $cur_need = $plants[$type]['height'][$pi];
        }
        $cur_cuft = $plants[$type]['length'][$pi] * $plants[$type]['width'][$pi] * $cur_need;
        $cur_cuft = $cur_cuft / 1728;

        $cur_rates_need = ' ';
        if ($cur_need > 0) {
            $cur_rates_need = " with $cur_need inches of soil needed ";
        }

        if($plants[$type]['qty'][$pi] != '' && $plants[$type]['qty'][$pi] != 0){


            if($step['step'] == 3 || $step['step'] == 4){

                if(isset($guide_rates[$type]['surface'][$product['id']])) {
                    $cur_sqft = $cur_sqft/$guide_rates[$type]['surface'][$product['id']]['per-sqft'];

                    $cur_rates = $guide_rates[$type]['surface'][$product['id']]['amount'] * $cur_sqft;
                    $cur_rates = round($cur_rates, 2);
                    if($cur_sqft > 1){ $s = 's'; }
                    $cur_rates = "Apply " . $cur_rates . " ". $guide_rates[$type]['surface'][$product['id']]['unit'] . $s;
                    $proceed = true;
                }

            } 
            // Eaches
            if(isset($guide_rates[$type]['each'][$product['id']])){
                if($plants[$type]['width'][$pi] < 8){
                    $cur_rates = $guide_rates[$type]['each'][$product['id']]['small']; 
                } elseif($plants[$type]['width'][$pi] >= 8 && $plants[$type]['width'][$pi] < 24){
                    $cur_rates = $guide_rates[$type]['each'][$product['id']]['medium'];
                } elseif($plants[$type]['width'][$pi] >= 24){
                    $cur_rates = $guide_rates[$type]['each'][$product['id']]['large'];
                }

                $cur_rates = "$cur_rates each";
                $proceed = true;
            }

            if(!$cur_rates){
                if(isset($guide_rates[$type]['filler'][$product['id']])){

                    // Figure out if we need to indicate that this product has existing soil
                    $quarts = getVolume($cur_cuft, 'cuft', 'qt-d');
                    if($quarts >= 8){
                        $prod_need = ($cur_cuft * $guide_rates[$type]['filler'][$product['id']]['amount'])/100;
                    } elseif($guide_rates[$type]['filler'][$product['id']]['primary'] == 'true'){
                        $prod_need = $cur_cuft;
                    }
                    
                    if($quarts >=8 || $guide_rates[$type]['filler'][$product['id']]['primary'] == 'true'){

                        $proceed = true;

                        // Setup the current item to be normalized
                        $cur_items = array(
                            array(
                                'amount' => $prod_need,
                                'unit'  => 'cuft',
                            )
                        );
                        
                        // Normalize to bag value
                        $cur_normalized = indppl_normalize($cur_items, $bag[1], $cur_cups);
    
                        
                        // var_dump($guide_rates[$type]['filler'][$product['id']]);
                        $fraction = $cur_normalized[0]['standard-amount']/$bag[0];
                        $fraction_int = '';
                   
                        if($cur_normalized[0]['standard-amount'] > $bag[0] && $fraction > 1){
                            $fraction_int = floor($fraction);
                            $fraction = $fraction - $fraction_int;
                        }
                        // $fraction = 1/$fraction;
                        if($fraction >= 0.15){
                            $cur_unit = " of a {$bag[0]} {$bag[1]} package";
                            if($fraction == floor($fraction) && $fraction_int != ''){
                                $fraction = $fraction_int + $fraction;
                                $fraction_int = '';
                            } elseif($fraction_int != '' && $fraction_int > 0) {
                                $fraction_int = $fraction_int . " & ";
                            } 
                            $fraction = indppl_readable_fraction($fraction);
                            $cur_amount = $fraction_int . $fraction;
                            $cur_rates = $cur_amount . " " . $cur_unit;
    
                        } else {
                            // Now determine if that's a reasonable fraction to manage - if not set the variables as cups...
                            $new_normalized = indppl_normalize($cur_items, 'cup', $cur_cups);
    
                            $cur_amount = round($new_normalized[0]['standard-amount'], 1);
                            if($cur_amount > 1){
                                $s = 's';
                            }
                            $cur_unit = 'cup' . $s ;
    
                            $cur_rates = $cur_amount . " " . $cur_unit;
                            
                        }
                    }

                }

                // BLENDED!
                if(isset($guide_rates[$type]['blended'][$product['id']])){
                    
                    $proceed = true;

                    if($dry == 'dry' && $volume){

                        $prod_need = ($cur_cuft * $guide_rates[$type]['blended'][$product['id']]['amount'])/100;
                        $cur_items = array(
                            array(
                                'amount' => $prod_need,
                                'unit'  => 'cuft',
                            )
                        );
                        
                        $cur_normalized = indppl_normalize($cur_items, $bag[1], $cur_cups);
    
                        
                        // var_dump($guide_rates[$type]['blended'][$product['id']]);
                        $fraction = $cur_normalized[0]['standard-amount']/$bag[0];
                        $fraction_int = '';
                   
                        if($cur_normalized[0]['standard-amount'] > $bag[0] && $fraction > 1){
                            $fraction_int = floor($fraction);
                            $fraction = $fraction - $fraction_int;
                        }
                        // $fraction = 1/$fraction;
                        if($fraction >= 0.15){
                            $cur_unit = " of a {$bag[0]} {$bag[1]} package";
    
                            if($fraction == floor($fraction) && $fraction_int != ''){
                                $fraction = $fraction_int + $fraction;
                                $fraction_int = '';
                            } elseif($fraction_int != '' && $fraction_int > 0) {
                                $fraction_int = $fraction_int . " & ";
                            } 
                            $fraction = indppl_readable_fraction($fraction);
                            // var_dump($fraction_int);
                            $cur_amount = $fraction_int . $fraction;
                            $cur_rates = $cur_amount . " " . $cur_unit;
    
                        } else {
                            // Now determine if that's a reasonable fraction to manage - if not set the variables as cups...
                            $new_normalized = indppl_normalize($cur_items, 'cup', $cur_cups);
    
                            $cur_amount = round($new_normalized[0]['standard-amount'], 1);
                            if($cur_amount > 1){
                                $s = 's';
                            }
                            $cur_unit = 'cup' . $s ;
    
                            $cur_rates = $cur_amount . " " . $cur_unit;
                            
                        }
                    } else {
                        //Ye Old Way
                        $cur_rates = $cur_cuft * $guide_rates[$type]['blended'][$product['id']]['amount'];
                        $cur_rates = round($cur_rates, 2);
                        if($cur_rates > 1){ $s = 's';}
                        $cur_rates = $cur_rates . " " . $guide_rates[$type]['blended'][$product['id']]['unit'] . $s;
                    }

                }

            }

                
            if($proceed){
                $print_apprates .= "{$plants[$type]['length'][$pi]}x{$plants[$type]['width'][$pi]}x{$plants[$type]['height'][$pi]} $cur_rates_need ({$cur_rates}), ";
            }
        }
        
        $pi++; // Increment that sucker!
    }
}

if($print_apprates != ''){
    return "<strong>How much to use: </strong>" . rtrim($print_apprates, ", ");
} else {
    return false;
}