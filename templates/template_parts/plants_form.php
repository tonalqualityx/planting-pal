<?php defined('ABSPATH') or die('No script kiddies please!'); //For security ?>
<?php
$list = $_POST['ground_store'];
$list = stripslashes($list);
$list = json_decode($list, true);
// var_dump($_POST);
$plants = $_POST['plants'];
$plants = stripslashes($plants);
$plants = json_decode($plants, true);
$pots = $plants['pots'];

$beds = $_POST['plants'];
$beds = stripslashes($beds);
$beds = json_decode($beds, true);
$beds = $beds['beds'];

if ($storeid == '') {
    echo '<br><br><p align="center">Sorry we could not find that store. <a href="/">Please go back to search.</a></center>';
    exit;
};
?>
<div id="buttons" style="width: 100%;"></div>
<div id="app-header" class="types">
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
<form action="" method="post" id="plants-form">

    <input type="hidden" name="storeid" value="<?=$storeid?>">
    <input type="hidden" name="next-step" value="shopping_list">

    <div class="row type-header-2  plants-form-header">
        <div class="col">
            <h3 class="white-text">In-Ground Plantings</h3>
            <p class="light-green-text">Enter the size & quantity of plants below</p>
        </div>
    </div>
    <div class="ig-select container" style="padding-bottom: 25px;">
        <div class="container">
            <div class="indppl-app-split indppl-flex qty-plant-header">
                <div class="">
                    <p class="indppl-plantform-subtitles">QTY</p>
                </div>
                <div class="">
                    <p class="indppl-plantform-subtitles">Plant Container Size</p>
                </div>
            </div>
        </div>
        <hr class="light-rule">

        <div class="container">
            <?php 
            //Get the containers!
            
            
            $args = array(
                'query_by_role' => 'parent',
                'role_to_return' => 'other',
                'return' => 'post_id',
                'args' => [
                    'meta_key' => 'wpcf-display-number',
                ],
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
            );
            
            $pro = FALSE;
            $author = $post->post_author;
            $stati = indppl_user_status($author);
            if(in_array('paidaccountpro', $stati)){
                $pro = TRUE; // Make life easier later...
                $now = date('m/d');
                $store_meta = get_post_meta($storeid);
                $spring_start = date('m/d',strtotime($store_meta['wpcf-spring-start'][0]));
                $spring_end = date('m/d', strtotime($store_meta['wpcf-spring-end'][0]));
                $summer_start = date('m/d',strtotime($store_meta['wpcf-summer-start'][0]));
                $summer_end = date('m/d', strtotime($store_meta['wpcf-summer-end'][0]));
                $fall_start = date('m/d',strtotime($store_meta['wpcf-fall-start'][0]));
                $fall_end = date('m/d', strtotime($store_meta['wpcf-fall-end'][0]));
                $winter_start = date('m/d',strtotime($store_meta['wpcf-winter-start'][0]));
                $winter_end = date('m/d', strtotime($store_meta['wpcf-winter-end'][0]));
                
                $available = 'wpcf-available-in-spring';
                if($now >= $spring_start && $now <= $spring_end){
                    $available = 'wpcf-available-in-spring';
                } elseif($now >= $summer_start && $now <= $summer_end){
                    $available = 'wpcf-available-in-summer';
                } elseif($now >= $fall_start && $now <= $fall_end){
                    $available = 'wpcf-available-in-fall';
                } elseif($now >= $winter_start && $now <= $winter_end){
                    $available = 'wpcf-available-in-winter';
                }
                $args['role_to_return'] = 'intermediary';
                $args['return'] = 'post_object';
                $args['args'] = ['meta_key' => $available, 'meta_value' => 1];
                $relationships = toolset_get_related_posts($storeid, 'store-container', $args);
                $containers = array();
                $i = 1000;
                foreach($relationships as $relation){
                    $cont_id = explode(' - ', $relation->post_title);
                    $display_order = get_post_meta($cont_id[1], 'wpcf-display-number', TRUE);
                    if($display_order && $display_order != ''){
                        $containers[$display_order] = $cont_id[1];
                    } else {
                        $containers[$i] = $cont_id[1];
                    }
                    $i++;
                }
                ksort($containers);
            } else {
                $containers = toolset_get_related_posts($storeid, 'store-container', $args);
            }
            

            
            foreach($containers as $cont){ $container = get_post($cont); ?>

                <div class="indppl-app-split indppl-flex">
                    <div id="qty" class='ground-shopping-list'>
                        <input type="number" min="0" class="rounded-input" name="ground[<?php echo $container->ID; ?>]" min="0" value='<?php echo $list[$container->ID]; ?>'>
                    </div>
                    <div class="" id="plant-size">
                        <p class="plant-size-format"><?php echo $container->post_title; ?></p>
                    </div>
                </div>
                
            <?php } ?>
        </div>
    </div>

    <?php 
    if($pro){ ?>


        <div class="indppl-flex indpl-app-split row type-header-2  plants-form-header" style="margin:auto;">
            <div class="col">
                <h3 class="white-text">Pot Plantings</h3>
                <p class="light-green-text">Enter the size & quantity of pots</p>
                <img src="<?php echo INDPPL_ROOT_URL . 'assets/img/pot-header.jpg'; ?>" class='plant-form-header-image'>
            </div>
        </div>
        <div class="ig-select container">

            <div class="container">
                <?php
                if(!$pots){
                    $pots = array('qty' => array(
                        '1' => ''
                    ));
                }
                foreach($pots['qty'] as $key => $value){
                    ob_start();
                    ?>
                    <div class="pots-form pb-first">
                        <h3>Pot #<span class="counter" data-count="1">1</span></h3>
                        <div class="indppl-app-split indppl-flex" style="margin-bottom:20px;">
                            <div class="">
                                <p style='margin-bottom: 35px;'></p>
                                <input type="number" min="0" name="pots[qty][]" id="qty_1" class="rounded-input pots margin-auto" value='<?php echo $pots["qty"][$key]; ?>'>
                                <label class="dark-green-text" style="width:100%;text-align:center;">Qty</label>
                            </div>
                            <div class=" tacos">
                                <div class="indppl-flex">
                                    <div>
                                        <p style='margin-bottom: 35px;'></p>
                                        <input type="number" min="0" id="plength_1" name="pots[length][]" placeholder="L&quot;" class="rounded-input2 pots" value='<?php echo $pots["length"][$key]; ?>'>
                                        <label class="dark-green-text">Length</label>
                                    </div>
                                    <p class="by-the-by green-text">x</p>
                                    <div>
                                        <p style='margin-bottom: 35px;'></p>
                                        <input type="number" min="0" id="pwidth_1" name="pots[width][]" placeholder="W&quot;" class="rounded-input2 pwidth" value='<?php echo $pots["width"][$key]; ?>'>
                                        <label class="dark-green-text">Width</label>
                                    </div>
                                    <p class="by-the-by green-text">x</p>
                                    <div>
                                        <p style='margin-bottom: 35px;'></p>
                                        <input type="number" min="0" id="pheight_1" name="pots[height][]" placeholder="H&quot;" class="height rounded-input2 pots" value='<?php echo $pots["height"][$key]; ?>'>
                                        <label class="dark-green-text">Height</label>
                                    </div>                            
                                </div>
                                <div class="indppl-flex partial-container">
                                    <div class=" empty-filled indppl-flex margin-right-0">
                                        <input class="pots indppl-pots-empty fill-empty" type="radio" id="pstatus_e-<?php echo $key; ?>" name="pstatus_<?php echo $key; ?>" <?php if(!$pots["need"][$key]){ echo "checked"; } ?> value="empty">
                                        <label class="form-check-label empty-label ppl-green-bg white-text indppl-pots-empty fill-empty" for="pstatus_e-<?php echo $key; ?>">Empty</label>
                                    </div>
                                    <div class=" empty-filled indppl-flex margin-right-0">
                                        <input class="indppl-pots-partial pots" <?php if($pots["need"][$key]){ echo "checked"; } ?> type="radio" id="pstatus_p-<?php echo $key; ?>" name="pstatus_<?php echo $key; ?>" value="partial">
                                        <label class="form-check-label partial-label" for="pstatus_p-<?php echo $key; ?>">Partially Filled</label>
                                    </div>
                                </div>
                                <div class="<?php if(!$pots["need"][$key]){ echo "hide"; } ?> inches-needed" style="margin-top:15px;">
                                    <p style='margin-bottom: 35px;'></p>    
                                    <input type="number" min="0" id="pneed_1" name="pots[need][]" class="rounded-input3 pots" value='<?php echo $pots["need"][$key]; ?>'>
                                    <label class="soil-need">Inches of soil needed</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="indppl-flex indppl-justify-center max-600">
                                
                                
                            </div>
                        </div>
                    </div>
                    <?php
                    $return .= ob_get_clean();
                }
                echo $return;
                ?>
            </div>
            <div class="row">
                <div class="col">
                    <p id="pot_add" class="cursor orange-text">+ Add Another Pot</p>
                </div>
            </div>
        </div>
        <div class="row type-header-2  plants-form-header">
            <div class="col">
            <h3 class="white-text">Raised Bed Plantings</h3>
                <p class="light-green-text">Enter the size & quantity of raised beds</p>
                <img src="<?php echo INDPPL_ROOT_URL . 'assets/img/bed-header.jpg'; ?>" class='plant-form-header-image'>
            </div>
        </div>
        <div class=" ig-select container">

            <div class="container">
            <?php
                if(!$beds){
                    $beds = array('qty' => array(
                        '1' => ''
                    ));
                }
                foreach($beds['qty'] as $key => $value){
                    ob_start();
                    ?>
                    <div class="rb-form pb-first">
                        <h3>Raised Bed #<span class="counter" data-count="1">1</span></h3>

                        <div class="indppl-app-split indppl-flex" style="margin-bottom:20px;">
                            <div class="" >
                                <p style='margin-bottom: 35px;'></p>
                                <input type="number" min="0" name="beds[qty][]" class="rounded-input beds margin-auto" value='<?php echo $beds["qty"][$key]; ?>'>
                                <label class="dark-green-text" style="width:100%;text-align:center;">Qty</label>
                            </div>
                            <div class="tacos">
                                <div class="indppl-flex">
                                    <div>
                                        <p style='margin-bottom: 35px;'></p>
                                        <input type="number" min="0" name="beds[length][]" placeholder="L&quot;" class="rounded-input2" value='<?php echo $beds["length"][$key]; ?>'>
                                        <label class="dark-green-text">Length</label>
                                    </div>
                                    <p class="by-the-by dark-green-text">x</p>
                                    <div>
                                        <p style='margin-bottom: 35px;'></p>
                                        <input type="number" min="0" name="beds[width][]" placeholder="W&quot;" class="rounded-input2" value='<?php echo $beds["width"][$key]; ?>'>
                                        <label class="dark-green-text">Width</label>
                                    </div>
                                    <p class="by-the-by dark-green-text">x</p>
                                    <div>
                                        <p style='margin-bottom: 35px;'></p>
                                        <input type="number" min="0" name="beds[height][]" placeholder="H&quot;" class="height rounded-input2" value='<?php echo $beds["height"][$key]; ?>'>
                                        <label class="dark-green-text">Height</label>
                                    </div>
                                </div>
                                <div class="indppl-flex ">
                                    <div class="empty-filled indppl-flex margin-right-0">
                                        <input class="pots fill-empty" <?php if(!$beds["need"][$key]){ echo "checked"; } ?> type="radio" id="rbstatus_e-<?php echo $key; ?>"  name="rbstatus_<?php echo $key; ?>" checked value="empty">
                                        <label class="form-check-label empty-label ppl-green-bg white-text fill-empty" for="rbstatus_e-<?php echo $key; ?>">Empty</label>
                                    </div>
                                    <div class="empty-filled indppl-flex margin-right-0">
                                        <input class="indppl-beds-partial pots" <?php if($beds["need"][$key]){ echo "checked"; } ?> type="radio" id="rbstatus_p-<?php echo $key; ?>"  name="rbstatus_<?php echo $key; ?>" value="partial"><label class="form-check-label partial-label" for="rbstatus_p-<?php echo $key; ?>">Partially Filled</label>
                                    </div>
                                </div>
                                <div class="<?php if(!$beds["need"][$key]){ echo "hide"; } ?> inches-needed">
                                    <p style='margin-bottom: 35px;'></p>
                                    <input type="number" min="0" id="rbneed_1" name="beds[need][]" class="rounded-input3" value='<?php echo $beds["need"][$key]; ?>'>
                                    <label class="soil-need dark-green-text">Inches of soil needed</label>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <?php
                    $return_bed .= ob_get_clean();
                }
                echo $return_bed;
                ?>
            </div>
            <div class="row">
                    <div class="col">
                        <p id="rb_add" class="cursor orange-text">+ Add Another Raised Bed</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rbedBox_add"></div>


        <?php } ?>
        <div class="container footer">
            <div class="row">
                <div class="col"><input type="image"  border="0" src="<?php echo INDPPL_ROOT_URL; ?>assets/img/next-button.png" class="next-button">
                <p class="copyright">© Copyright 2019 Planting Pal.&nbsp; All rights reserved.<br></p>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">

$(document).ready(function(){
    
            $('#pot_add').click(function(){

               // Selecting last id
               var lastname_id = $('.pots-form input[type=number]:nth-child(2)').last().attr('id');
               var split_id = lastname_id.split('_');

               // New index
               var index = Number(split_id[1]) + 1;

               // Create clone
               var p_newel = $('.pots-form:last-of-type').clone(true);
               var count = $(p_newel).find('.counter').data('count');
               count++;
               $(p_newel).find('.counter').data('count', count);
               $(p_newel).find('.counter').html(count);
               $(p_newel).removeClass('pb-first');
               // Set id of new element
               $(p_newel).find('input[type=number]:nth-child(1)').attr("id","qty_"+index);
               $(p_newel).find('input[type=number]:nth-child(2)').attr("id","plength_"+index);
               $(p_newel).find('input[type=number]:nth-child(3)').attr("id","pwidth_"+index);
               $(p_newel).find('input[type=number]:nth-child(4)').attr("id","pheight_"+index);
               $(p_newel).find('input[type=radio].indppl-pots-partial').attr("id","pstatus_p-"+index);
               $(p_newel).find('.empty-label').attr("for", "pstatus_e-"+index);
               $(p_newel).find('input[type=radio].indppl-pots-empty').attr("id","pstatus_e-"+index);
               $(p_newel).find('.partial-label').attr("for", "pstatus_p-"+index);
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
               var lastname_id = $('.rb-form input[type=number]:nth-child(2)').last().attr('id');
               var split_id = lastname_id.split('_');

               // New index
               var index = Number(split_id[1]) + 1;

               // Create clone
               var r_newel = $('.rb-form:last').clone(true);
            //    if($(r_newel).hasClass('pb-first')){
                   $(r_newel).removeClass('pb-first');
            //    }

                var count = $(r_newel).find('.counter').data('count');
                count++;
                $(r_newel).find('.counter').data('count', count);
                $(r_newel).find('.counter').html(count);    

               // Set id of new element
               $(r_newel).find('.offset-1 input[type=number]:nth-child(1)').attr("id","rbqty_"+index);
               $(r_newel).find('.tacos  input[type=number]:nth-child(1)').attr("id","plength_"+index);
               $(r_newel).find('.pwidth input[type=number]:nth-child(1)').attr("id","pwidth_"+index);
               $(r_newel).find('.tacos  input[type=number]:nth-child(2)').attr("id","pheight_"+index);
               $(r_newel).find('.indppl-beds-partial').attr("id","rbstatus_p-"+index);
               $(r_newel).find('.empty-label').attr("for", "rbstatus_e-"+index);
               $(r_newel).find('input.fill-empty').attr("id","rbstatus_e-"+index);
               $(r_newel).find('.partial-label').attr("for", "rbstatus_p-"+index);
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