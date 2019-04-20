<?php
//Single Store Template
//Use this file to collect input from end users on what they'll be planting

wp_head();

$storeid = get_the_ID(  );

if(isset($_POST['storeid'])){

    $ground = $_POST['ground'];
    $ground = array_filter($ground);

    $apprates = indppl_apprates($storeid);

    $products = array();

    foreach($ground as $container => $count){
        foreach($apprates['ground'] as $key => $val) {
            if(array_key_exists($container, $apprates['ground'][$key]['containers'])){
                $product = get_the_title($key);
                $standard = get_post_meta($key, 'wpcf-unit', TRUE);
                // echo "<h2>STANDARD L1 $standard</h2>";
                $brand = get_the_terms($key, 'brand');
                $cups = get_post_meta($key, 'wpcf-5cups', TRUE);
                $brand = $brand[0];
                $plant = get_the_title($container);
                $amount = $apprates['ground'][$key]['containers'][$container]['amount'] * $count;
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
                
                
                $need = 0;
                foreach($normalized as $k => $v) {
                    $need += $v['standard-amount']; 
                }

                if(isset($products[$key])){
                    $products[$key]['need'] += $need;
                    // echo "true";
                } else {
                    $products[$key]['name'] = $brand->name . " " . $product;
                    $products[$key]['need'] = $need;
                    $products[$key]['unit'] = $standard;
                }
                
                // var_dump($products);

                echo "<h2>$count $plant needs $amount $unit of $brand->name $product </h2>";
            }
        }
    }
    
    // Calculate the shopping list!
    $shopping_list = array();
    foreach($products as $key => $val) {

        // Setup the important values
        $standard = get_post_meta( $key, 'wpcf-unit', TRUE);
        $cups = get_post_meta($key, 'wpcf-5cups', TRUE);
        $brand = get_the_terms( $key, 'brand' );
        $brand = $brand[0]->name;
        $packages = toolset_get_related_posts($key, 'product-package', ['query_by_role' => 'parent', 'role_to_return' => 'child', 'return' => 'post_id'] );

        // Create a conversion array and fill it with all the package sizes for conversion
        $convert = array();
        foreach($packages as $package) {
            $amount = get_post_meta($package, 'wpcf-size', TRUE);
            $unit = get_post_meta($package, 'wpcf-unit', TRUE);
            $convert[$amount . " " . $unit] = array(
                'amount' => $amount,
                'unit'  => $unit,
            );
        } 
        // var_dump($convert);
        $normalized_packs = indppl_normalize($convert, $standard, $cups);
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
        foreach($ref as $pack_key => $pack) {
            var_dump($pack);
            echo "<h2>KEY $key </h2>";
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
                        'name'              => $key,
                    );

                }
            }
        }

    }

}

foreach($shopping_list as $id => $item) {
    var_dump($item);
    echo "<br /><br />";
}

?>

<body>
    <!-- <div class="desktopWarning">
        <p class="desktopWarning-p">This site is optimized for mobile phones in portrait layout.</p><i class="material-icons d-block portrait-only">screen_lock_portrait</i>
    </div> -->
    <div class="container"><img src="<?php echo INDPPL_ROOT_URL; ?>/assets/img/general-logo-x2.png" id="logo-header"></div>
    <div class="row guide-top" style="margin-top: -15px;">
        <div class="col">
            <p class="text-uppercase text-center text-white d-flex justify-content-center align-items-center" style="font-size: 9px;margin-top: 7px;margin-bottom: 10px;"><strong>AMENDMENT CALCULATOR &amp; PLANTING GUIDE</strong><br></p>
        </div>
    </div>
    <div class="row no-gutters" id="buttons">
        <div class="col">
            <p class="d-none button">Retailer</p>
        </div>
        <div class="col button2">
            <p class="d-none button">manufacturer</p>
        </div>
    </div>
    <?php
    if ($storeid == '') {
      echo '<br><br><p align="center">Sorry we could not find that store. <a href="/">Please go back to search.</a></center>';
      exit;
    };
    ?>
    <div id="buttons" style="width: 100%;"></div>
    <div class="container types">
        <div class="row no-gutters">
            <div class="col-4 selections"><img src="<?php echo INDPPL_ROOT_URL; ?>assets/img/inground.png" id="type">
                <p><strong>In-Ground</strong><br><strong>Plantings</strong><br></p>
            </div>
            <div class="col-4 selections"><a href="#pot"><img src="<?php echo INDPPL_ROOT_URL; ?>assets/img/pot.png" id="type"></a>
                <p><strong>Pot </strong><br><strong>Plantings</strong><br></p>
            </div>
            <div class="col-4 selections"><a href="#bed"><img src="<?php echo INDPPL_ROOT_URL; ?>assets/img/raisedbed.png" id="type"></a>
                <p><strong>Raised Bed Plantings</strong><br></p>
            </div>
        </div>
    </div>
    <form action="" method="post">
      <input type="hidden" name="storeid" value="<?=$storeid?>">
    <div class="row type-header">
        <div class="col">
            <p><strong>In-Ground Plantings</strong><br></p>
        </div>
    </div>
    <div class="ig-select">
        <div class="container">
            <div class="row qty-plant-header">
                <div class="col-3 offset-2">
                    <p>QTY</p>
                </div>
                <div class="col-4">
                    <p>Plant Size</p>
                </div>
            </div>
        </div>
        <hr class="light-rule">

        <div class="container">
            <?php 
            //Get the containers!
            $containers = types_child_posts('container');
            foreach($containers as $container){ ?>

                <div class="row">
                    <div class="col-3 offset-2" id="qty">
                        <input type="number" class="rounded-input margin-auto" name="ground[<?php echo $container->ID; ?>]" min="0">
                    </div>
                    <div class="col-4" id="plant-size">
                        <p class="plant-size-format"><?php echo $container->post_title; ?></p>
                    </div>
                </div>
                
            <?php } ?>
        </div>
    </div>

    <?php 
    
    $stati = indppl_user_status(2);
    if(in_array('paidaccount', $stati)){ ?>


        <div class="row type-header">
            <div class="col">
                <p id="pots"><strong>Pot Plantings</strong><br></p>
            </div>
        </div>
        <div class="ig-select">
            <div class="container">
                <div class="row qty-plant-header">
                    <div class="col-3 offset-1">
                        <p>QTY</p>
                    </div>
                    <div class="col-8">
                        <p>Pot Size</p>
                    </div>
                </div>
            </div>
            <hr class="light-rule">

            <div class="container">
                <div class="pots-form">
                    <div class="row pot-plant">
                        <div class="col-3 offset-1"><input type="number" name="pqty[]" id="qty_1" class="rounded-input pots margin-auto"></div>
                        <div class="col-8 tacos">
                            <input type="number" id="plength_1" name="plength[]" placeholder="L" class="rounded-input2 pots">
                            <p class="by-the-by">x</p><input type="number" id="pwidth_1" name="pwidth[]" placeholder="W" class="rounded-input2 pwidth">
                            <p class="by-the-by">x</p><input type="number" id="pheight_1" name="pheight[]" placeholder="H" class="rounded-input2 pots">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col offset-1">
                            <div class="form-check empty-filled">
                                <input class="form-check-input empty-filled pots" type="radio" id="pstatus_1" name="pstatus_1" checked value="empty">
                                <label class="form-check-label" for="formCheck-1">Empty</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input empty-filled pots" type="radio" id="pstatus_1" name="pstatus_1" value="partial">
                                <label class="form-check-label" for="formCheck-2">Partially Filled</label>
                            </div>
                            <div>
                                <input type="number" id="pneed_1" name="pneed[]" class="rounded-input3 pots"><label class="soil-need">Inches of soil needed</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <p id="pot_add">+ Add More</p>
                </div>
            </div>
        </div>
        <div class="row type-header">
            <div class="col">
                <p id="bed"><strong>Raised bed Plantings</strong><br></p>
            </div>
        </div>
        <div class=" ig-select">
            <div class="container">
                <div class="row qty-plant-header">
                    <div class="col-3 offset-1">
                        <p>QTY</p>
                    </div>
                    <div class="col-8">
                        <p>Raised Bed Size</p>
                    </div>
                </div>
            </div>
            
            <hr class="light-rule">

            <div class="container">
                <div class="rb-form">
                    <div class="row pot-plant ">
                        <div class="col-3 offset-1" id="qty">
                            <input type="number" name="rbqty[]" class="rounded-input">
                        </div>
                        <div class="col-8">
                            <input type="number" name="rbl[]" placeholder="L" class="rounded-input2">
                            <p class="by-the-by">x</p>
                            <input type="number" name="rbw[]" placeholder="W" class="rounded-input2">
                            <p class="by-the-by">x</p>
                            <input type="number" name="rbh[]" placeholder="H" class="rounded-input2">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col offset-1">
                            <div class="form-check empty-filled">
                                <input class="form-check-input empty-filled" type="radio" name="rbstatus_1" checked value="empty">
                                <label class="form-check-label" for="formCheck-1">Empty</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input empty-filled" type="radio" name="rbstatus_1" value="partial"><label class="form-check-label" for="formCheck-2">Partially Filled</label>
                            </div>
                            <div>
                                <input type="number" id="rbneed_1" name="rbneed[]" class="rounded-input3"><label class="soil-need">Inches of soil needed</label>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>

        <div class="rbedBox_add"></div>

        <div class="row">
                <div class="col">
                    <p id="rb_add">+ Add More</p>
                </div>
            </div>
        </div>

    <?php } ?>
        <div class="container footer">
            <div class="row">
                <div class="col"><input type="image" border="0" src="<?php echo INDPPL_ROOT_URL; ?>/assets/img/next-button.png" class="next-button"></form>
                    <p class="copyright">© Copyright 2019 Planting Pal.&nbsp; All rights reserved.<br></p>
                </div>
            </div>
        </div>
    <script type="text/javascript">

$(document).ready(function(){
    
            $('#pot_add').click(function(){

               // Selecting last id
               var lastname_id = $('.pots-form input[type=number]:nth-child(1)').last().attr('id');
               var split_id = lastname_id.split('_');

               // New index
               var index = Number(split_id[1]) + 1;

               // Create clone
               var p_newel = $('.pots-form:last').clone(true);

               // Set id of new element
               $(p_newel).find('input[type=number]:nth-child(1)').attr("id","qty_"+index);
               $(p_newel).find('input[type=number]:nth-child(2)').attr("id","plength_"+index);
               $(p_newel).find('input[type=number]:nth-child(3)').attr("id","pwidth_"+index);
               $(p_newel).find('input[type=number]:nth-child(4)').attr("id","pheight_"+index);
               $(p_newel).find('input[type=radio]:nth-child(1)').attr("id","pstatus_"+index);
               $(p_newel).find('input[type=radio]:nth-child(2)').attr("id","pstatus_"+index);
               $(p_newel).find('input[type=number]:nth-child(7)').attr("id","pneed_"+index);

               // Set Name to new element
               $(p_newel).find('input[type=radio]:nth-child(1)').attr("name","pstatus_"+index);
               $(p_newel).find('input[type=radio]:nth-child(2)').attr("name","pstatus_"+index);

               // Set value
               $(p_newel).find('input[type=number]:nth-child(1)').val("");
               $(p_newel).find('input[type=number]:nth-child(2)').val("");
               $(p_newel).find('input[type=number]:nth-child(3)').val("");
               $(p_newel).find('input[type=number]:nth-child(4)').val("");
               //$(p_newel).find('input[type=radio]:nth-child(1)').val("");
               //$(p_newel).find('input[type=radio]:nth-child(2)').val("");
               $(p_newel).find('input[type=number]:nth-child(5)').val("");
               $(p_newel).find('input[type=number]:nth-child(6)').val("");
               $(p_newel).find('input[type=number]:nth-child(7)').val("");

               // Insert element
               $(p_newel).insertAfter(".pots-form:last");
           });
        });

        $(document).ready(function(){

            $('#rb_add').click(function(){

               // Selecting last id
               var lastname_id = $('.rb-form input[type=number]:nth-child(1)').last().attr('id');
               var split_id = lastname_id.split('_');

               // New index
               var index = Number(split_id[1]) + 1;

               // Create clone
               var r_newel = $('.rb-form:last').clone(true);

               // Set id of new element
               $(r_newel).find('.offset-1 input[type=number]:nth-child(1)').attr("id","rbqty_"+index);
               $(r_newel).find('.tacos  input[type=number]:nth-child(1)').attr("id","plength_"+index);
               $(r_newel).find('.pwidth input[type=number]:nth-child(1)').attr("id","pwidth_"+index);
               $(r_newel).find('.tacos  input[type=number]:nth-child(2)').attr("id","pheight_"+index);
               $(r_newel).find('input[type=radio]:nth-child(1)').attr("id","pstatus_"+index);
               $(r_newel).find('input[type=radio]:nth-child(2)').attr("id","pstatus_"+index);
               $(r_newel).find('input[type=number]:nth-child(7)').attr("id","pneed_"+index);

               // Set Name to new element
               $(r_newel).find('input[type=radio]:nth-child(1)').attr("name","rbstatus_"+index);
               $(r_newel).find('input[type=radio]:nth-child(2)').attr("name","rbstatus_"+index);

               // Set value
               $(r_newel).find('input[type=number]:nth-child(1)').val("");
               $(r_newel).find('input[type=number]:nth-child(2)').val("");
               $(r_newel).find('input[type=number]:nth-child(3)').val("");
               $(r_newel).find('input[type=number]:nth-child(4)').val("");
               //$(r_newel).find('input[type=radio]:nth-child(1)').val("");
               //$(r_newel).find('input[type=radio]:nth-child(2)').val("");
               $(r_newel).find('input[type=number]:nth-child(5)').val("");
               $(r_newel).find('input[type=number]:nth-child(6)').val("");
               $(r_newel).find('input[type=number]:nth-child(7)').val("");

               // Insert element
               $(r_newel).insertAfter(".rb-form:last");
           });
        });
    </script>
</body>