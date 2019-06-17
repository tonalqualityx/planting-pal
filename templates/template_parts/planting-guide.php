<?php 
defined('ABSPATH') or die('No script kiddies please!'); //For security 
$store_name = get_the_title($store);
$address1 = get_post_meta($store, 'wpcf-address1', TRUE);
$address2 = get_post_meta($store, 'wpcf-address2', TRUE);
$phone    = get_post_meta($store, 'wpcf-phone', TRUE);
$email    = get_post_meta($store, 'wpcf-email', TRUE);
$website  = get_post_meta($store, 'wpcf-weburl', TRUE);
$guide_rates = indppl_apprates($store);

if(!preg_match('^(http|https):\/\/', $website)){
    $url = "//" . $website;
} else {
    $url = preg_replace('^(http|https):\/\/', '//', $website);
}

switch ($type) {
    case 'ground':
        $type_label = 'In Ground ';
        break;
    case 'pots':
        $type_label = 'Potted Plants ';
        break;
    case 'beds':
        $type_label = 'Raised Beds ';
        break;
}

$store_link = str_replace("//", "", $website); ?>

<div id="planting-guide" class="planting-guide" data-type="ground" data-store="<?php echo $store ; ?>">
    <div class="store-info">
        <div class="indppl-flex indppl-align-center">
            <img src="<?php echo get_post_meta($store, 'wpcf-logo', TRUE);?>">
            <div class="store-address">
                <?php 
                echo "<h4 style='font-size:28px;margin:0;'>{$store_name}</h4>";
                if($address1 && $address1 != ''){
                    echo "<p>$address1</p>";
                }
                if($address2 && $address2 != '') {
                    echo "<p>$address2</p>";
                }
                if($phone && $phone != '') {
                    echo "<p>$phone</p>";
                }
                if($email && $email != '') {
                    echo "<p>$email</p>";
                }
                if($website && $website != '') {
                    echo "<p><a href='{$website}' target='_blank'>$store_link</a></p>";
                } ?>
    
            </div>
        </div>
    </div>
    <div class="planting-guide-header indppl-flex indppl-justify-center">
        <img src="">
        <h1 style="text-align: center;"><?php echo $type_label; ?>Planting Guide</h1>
    </div>
    <div class="planting-guide-content">
        <div class='guide-product-instructions'>
            <?php foreach($guide_options as $step){
                echo "<h3 class='orange-text'>{$step['title']}</h3>";
                echo "<div class='guide-step-instructions'>{$step['description']}</div>";
                if($step['image'] && $step['image'] != ''){
                    echo "<img class='indppl-step-img' src='{$step['image']}'></img>";
                }

                // THIS PART SHOULD BE A SHORTCODE THAT GETS CALLED EVERY TIME
                // indppl_guide_products($step['products']);
                // var_dump($step['products']);
                // $step_products = array();
                // foreach($step['products'] as $product){
                //     // var_dump($product);
                //     $step_products[] = array('product' => $product['id'], 'label' => 'test', 'instructions' => $product['instructions']);
                // }
                // indppl_guide_products($step_products);
                foreach($step['products'] as $product){
                    $prod_name = get_the_title($product['id']);
                    $brands       = get_the_terms($product['id'], 'brand');
                    $brand        = $brands[0];
                    $sponsorship  = toolset_get_related_post($product['id'], 'sponsorship-product');
                    $image        = get_post_meta($product['id'], 'wpcf-product-image', TRUE);
                    $sponsor_copy = '';?>
                    <div class='indppl-flex indppl-align-center guide-product-template'>
                        <?php if ($sponsorship) {
                        $sponsor_image = get_post_meta($sponsorship, 'wpcf-sponsorship-image', TRUE);
                        $sponsor_copy  = get_post_meta($sponsorship, 'wpcf-sponsorship-copy', TRUE);
                        $sponsor_link  = get_post_meta($sponsorship, 'wpcf-sponsor-url', TRUE);
                        $image         = $sponsor_image; ?>
                        <?php }
                        if ($image && $image != '') {?>
                            <div class='product-guide-image'><img src="<?php echo $image; ?>" alt="<?php echo $prod_title; ?>"></div>
                        <?php }?>
                        <div class='product-guide-step-instructions'>
                            <span class='strong product-name'><span class='brand'><?php echo $brand->name; ?></span> <span class='product'><?php echo $prod_name; ?></span></span> <?php echo $product["instructions"]; ?>
                            <?php if ($sponsorship) {?>
                                <br /><a href="#" class='sponsor-link'>Learn more about this product - Click Here</a> <span class='hide sponsor-copy'><?php echo $sponsor_copy; ?><br /><a href='<?php echo $sponsor_link; ?>' target="_blank">Learn More...</a></span>
                                <p>
                                </p>
                            <?php }?>
                        </div>
                        <div class="indppl-full-flex">
                            <h4><?php echo $prod_name; ?> Application Rates</h4>
                            <ul class="indppl-guide-rates" >
                                <?php // GET THE APPROPRIATE APPLICATION RATES

                                // Determine type
                                if($type == 'ground'){ // If guide is in ground
                                    $decode_plants = json_decode( $plants, TRUE );
                                    foreach($ground_list as $gid => $g){

                                        // Check if there's an apprate for this product
                                        if(isset($guide_rates[$type][$product['id']]['containers'][$gid]['amount']) || isset($guide_rates[$type][$product['id']]['bag'][$gid]['amount'])){
                                            $parse_by = key($guide_rates[$type][$product['id']]);
                                            switch ($parse_by) {
                                                case 'bag':
                                                    // If by bag then parse how much of the bag to use for this portion (based on bag size)
                                                    $bag = explode(" ", $list[$product['id']]['name']);
                                                    $cur_cups = get_post_meta( $product['id'], 'wpcf-5cups', TRUE );

                                                    $cur_unit = $guide_rates[$type][$product['id']][$parse_by][$gid]['unit'];
                                                    $cur_amount = $guide_rates[$type][$product['id']][$parse_by][$gid]['amount'];

                                                    $cur_items = array(
                                                        array(
                                                            'amount' => $cur_amount,
                                                            'unit'  => $cur_unit,
                                                        )
                                                    );

                                                    
                                                    $cur_normalized = indppl_normalize($cur_items, $bag[1], $cur_cups);

                                                    $fraction = $bag[0]/$cur_normalized[0]['standard-amount'];
                                                    $fraction = 1/$fraction;
                                                    if($fraction >= 0.1){
                                                        $cur_unit = " of a {$bag[0]} {$bag[1]} package";
                                                        $cur_amount = dec2frac($fraction);
                                                    } else {
                                                        // Now determine if that's a reasonable fraction to manage - if not set the variables as cups...
                                                        $new_normalized = indppl_normalize($cur_items, 'cup', $cur_cups);
                                                        $cur_amount = round($new_normalized[0]['standard-amount'], 1);
                                                        $cur_unit = 'cup';
                                                        
                                                    }
                                                    break;



                                                // Next, just fall through to set the values...
                                                default:
                                                    $cur_amount = round($guide_rates[$type][$product['id']][$parse_by][$gid]['amount'], 1);
                                                    $cur_unit   = $guide_rates[$type][$product['id']][$parse_by][$gid]['unit'];
                                                    break;
                                            }

                                            echo "<li><strong>" .  get_the_title($gid) . ":</strong> " . $cur_amount . " " . $cur_unit . "</li>"; 
                                        }

                                    } 
                                } elseif($type == 'pots' || $type = 'beds') { // If guide is pots or beds
                                    $cur_rates = null;
                                    // var_dump($guide_rates[$type]);
                                    $pi = 0;
                                    foreach($plants[$type]['qty'] as $pot){

                                        if($plants[$type]['qty'][$pi] != '' && $plants[$type]['qty'][$pi] != 0){

                                            $s = '';
                                            $cur_rates = null;
                                            if($step['step'] == 3 || $step['step'] == 4){
    
                                                if(isset($guide_rates[$type]['surface'][$product['id']])) {
                                                    $cur_sqft = $plants[$type]['length'][$pi] * $plants[$type]['width'][$pi];
                                                    $cur_sqft = $cur_sqft/144;
                                                    $cur_sqft = $cur_sqft/$guide_rates[$type]['surface'][$product['id']]['per-sqft'];
    
                                                    $cur_rates = $guide_rates[$type]['surface'][$product['id']]['amount'] * $cur_sqft;
                                                    $cur_rates = round($cur_rates, 2);
                                                    if($cur_sqft > 1){ $s = 's'; }
                                                    $cur_rates = "Apply " . $cur_rates . " " . $guide_rates[$type]['surface'][$product['id']]['unit'] . $s;
    
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
    
                                                $cur_rates = "Use $cur_rates for each";
                                            }
    
                                            if(!$cur_rates){
                                                if(isset($guide_rates[$type]['filler'][$product['id']])){
                                                    $cur_rates = "Fill {$guide_rates[$type]['filler'][$product['id']]['amount']}% with this product";
                                                }
    
                                                if(isset($guide_rates[$type]['blended'][$product['id']])){
                                                    $cur_cuft = $plants[$type]['length'][$pi] * $plants[$type]['width'][$pi] * $plants[$type]['height'][$pi];
                                                    $cur_cuft = $cur_cuft/1728;
    
                                                    $cur_rates = $cur_cuft * $guide_rates[$type]['blended'][$product['id']]['amount'];
                                                    $cur_rates = round($cur_rates, 2);
                                                    if($cur_rates > 1){ $s = 's';}
                                                    $cur_rates = "Blend in " . $cur_rates . " " . $guide_rates[$type]['blended'][$product['id']]['unit'] . $s;
                                                }
    
                                            }
    
                                               
                                            if(true){
                                                echo "<li><strong>{$plants[$type]['length'][$pi]}x{$plants[$type]['width'][$pi]}x{$plants[$type]['height'][$pi]}:</strong> {$cur_rates}</li>";
                                            }
                                        }
                                        
                                        $pi++; // Increment that sucker!
                                    }
                                }
                                
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
</div>