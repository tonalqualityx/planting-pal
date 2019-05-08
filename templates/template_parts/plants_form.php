<?php defined('ABSPATH') or die('No script kiddies please!'); //For security ?>

<div class="row guide-top" >
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
    <form action="" method="post">

        <input type="hidden" name="storeid" value="<?=$storeid?>">
        <input type="hidden" name="next-step" value="shopping_list">

        <div class="row type-header">
            <div class="col">
                <p><strong>In-Ground Plantings</strong><br></p>
            </div>
        </div>
        <div class="ig-select" style="padding-bottom: 25px;">
            <div class="container">
                <div class="indppl-app-split indppl-flex qty-plant-header">
                    <div class="">
                        <p>QTY</p>
                    </div>
                    <div class="">
                        <p>Plant Size</p>
                    </div>
                </div>
            </div>
            <hr class="light-rule">

            <div class="container">
                <?php 
                //Get the containers!
                $containers = toolset_get_related_posts($storeid, 'store-container', ['query_by_role' => 'parent', 'role_to_return' => 'other', 'return' => 'post_object']);
                foreach($containers as $container){ ?>

                    <div class="indppl-app-split indppl-flex">
                        <div class="" id="qty">
                            <input type="number" class="rounded-input margin-auto" name="ground[<?php echo $container->ID; ?>]" min="0">
                        </div>
                        <div class="" id="plant-size">
                            <p class="plant-size-format"><?php echo $container->post_title; ?></p>
                        </div>
                    </div>
                    
                <?php } ?>
            </div>
        </div>

        <?php 
        $author = $post->post_author;
        $stati = indppl_user_status($author);
        if(in_array('paidaccount', $stati)){ ?>


            <div class="indppl-flex indpl-app-split row type-header">
                <div class="col">
                    <p id="pot"><strong>Pot Plantings</strong><br></p>
                </div>
            </div>
            <div class="ig-select">
                <div class="container">
                    <div class="indppl-app-split indppl-flex qty-plant-header">
                        <div class="">
                            <p>QTY</p>
                        </div>
                        <div class="">
                            <p>Pot Size (inches)</p>
                        </div>
                    </div>
                </div>
                <hr class="light-rule">

                <div class="container">
                    <div class="pots-form">
                        <div class="indppl-app-split indppl-flex" style="margin-bottom:20px;">
                            <div class="">
                                <input type="number" name="pots[qty][]" id="qty_1" class="rounded-input pots margin-auto">
                            </div>
                            <div class=" tacos">
                                <div class="indppl-flex">
                                    <div>
                                        <input type="number" id="plength_1" name="pots[length][]" placeholder="L&quot;" class="rounded-input2 pots">
                                        <label>Length</label>
                                    </div>
                                    <p class="by-the-by">x</p>
                                    <div>
                                        <input type="number" id="pwidth_1" name="pots[width][]" placeholder="W&quot;" class="rounded-input2 pwidth">
                                        <label>Width</label>
                                    </div>
                                    <p class="by-the-by">x</p>
                                    <div>
                                        <input type="number" id="pheight_1" name="pots[height][]" placeholder="H&quot;" class="rounded-input2 pots">
                                        <label>Height</label>
                                    </div>                            
                                </div>
                                <div class="indppl-flex ">
                                    <div class=" empty-filled indppl-flex margin-right-0">
                                        <input class=" pots" type="radio" id="pstatus_1" name="pstatus_1" checked value="empty">
                                        <label class="form-check-label" for="formCheck-1">Empty</label>
                                    </div>
                                    <div class=" empty-filled indppl-flex margin-right-0">
                                        <input class=" pots" type="radio" id="pstatus_1" name="pstatus_1" value="partial">
                                        <label class="form-check-label" for="formCheck-2">Partially Filled</label>
                                    </div>
                                </div>
                                <div class="hide inches-needed" style="margin-top:15px;">
                                    <input type="number" id="pneed_1" name="pots[need][]" class="rounded-input3 pots">
                                    <label class="soil-need">Inches of soil needed</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="indppl-flex indppl-justify-center max-600">
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <p id="pot_add" class="cursor">+ Add More</p>
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
                    <div class="indppl-app-split indppl-flex qty-plant-header">
                        <div class="">
                            <p>QTY</p>
                        </div>
                        <div class="">
                            <p>Raised Bed Size</p>
                        </div>
                    </div>
                </div>
                
                <hr class="light-rule">

                <div class="container">
                    <div class="rb-form">
                        <div class="indppl-app-split indppl-flex" style="margin-bottom:20px;">
                            <div class="" >
                                <input type="number" name="beds[qty][]" class="rounded-input pots margin-auto">
                            </div>
                            <div class="tacos">
                                <div class="indppl-flex">
                                    <div>
                                        <input type="number" name="beds[length][]" placeholder="L&quot;" class="rounded-input2">
                                        <label>Length</label>
                                    </div>
                                    <p class="by-the-by">x</p>
                                    <div>
                                        <input type="number" name="beds[width][]" placeholder="W&quot;" class="rounded-input2">
                                        <label>Width</label>
                                    </div>
                                    <p class="by-the-by">x</p>
                                    <div>
                                        <input type="number" name="beds[height][]" placeholder="H&quot;" class="rounded-input2">
                                        <label>Height</label>
                                    </div>
                                </div>
                                <div class="indppl-flex ">
                                    <div class="empty-filled indppl-flex margin-right-0">
                                        <input class="pots" type="radio" name="rbstatus_1" checked value="empty">
                                        <label class="form-check-label" for="formCheck-1">Empty</label>
                                    </div>
                                    <div class="empty-filled indppl-flex margin-right-0">
                                        <input class="pots" type="radio" name="rbstatus_1" value="partial"><label class="form-check-label" for="formCheck-2">Partially Filled</label>
                                    </div>
                                </div>
                                <div class="hide inches-needed">
                                    <input type="number" id="rbneed_1" name="beds[need][]" class="rounded-input3">
                                    <label class="soil-need">Inches of soil needed</label>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
                <div class="row">
                        <div class="col">
                            <p id="rb_add" class="cursor">+ Add More</p>
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