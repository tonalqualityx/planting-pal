<?php
//Single Store Template
//Use this file to collect input from end users on what they'll be planting

wp_head();

$storeid = get_the_ID(  );
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
    <form action="/proces's/" method="post">
      <input type="hidden" name="storeid" value="<?=$storeid?>">
    <div class="row type-header">
        <div class="col">
            <p><strong>In-Ground Plantings</strong><br></p>
        </div>
    </div>
    <div class="container ig-select">
        <div class="row qty-plant-header">
            <div class="col-3 offset-2">
                <p>QTY</p>
            </div>
            <div class="col-4">
                <p>Plant Size</p>
            </div>
        </div>
        <hr class="light-rule">

<?php 
//START THE WP LOOP 
$containers = types_child_posts('container');
foreach($containers as $container){ ?>
    <div class="row">
        <div class="col-3 offset-2" id="qty">
            <input type="number" class="rounded-input" name="IG|<?php  ?>" min="0">
        </div>
        <div class="col-4" id="plant-size">
            <p class="plant-size-format"><?php echo $container->post_title; ?></p>
        </div>
    </div>
<?php } ?>
    </div>
    <div class="row type-header">
        <div class="col">
            <p id="pots"><strong>Pot Plantings</strong><br></p>
        </div>
    </div>
    <div class="container ig-select">
        <div class="row qty-plant-header">
            <div class="col-3 offset-1">
                <p>QTY</p>
            </div>
            <div class="col-8">
                <p>Pot Size</p>
            </div>
        </div>
        <hr class="light-rule">
<div class="pots-form">
        <div class="row pot-plant">
            <div class="col-3 offset-1"><input type="number" name="pqty[]" id="qty_1" class="rounded-input pots"></div>
            <div class="col-8 tacos">
               <input type="number" id="plength_1" name="plength[]" placeholder="L" class="rounded-input2 pots">
                <p class="by-the-by">x</p><input type="number" id="pwidth_1" name="pwidth[]" placeholder="W" class="rounded-input2 pwidth">
                <p class="by-the-by">x</p><input type="number" id="pheight_1" name="pheight[]" placeholder="H" class="rounded-input2 pots"></div>
        </div>
        <div class="row">
            <div class="col offset-1">
                <div class="form-check empty-filled"><input class="form-check-input empty-filled pots" type="radio" id="pstatus_1" name="pstatus_1" checked value="empty"><label class="form-check-label" for="formCheck-1">Empty</label></div>
                <div class="form-check"><input class="form-check-input empty-filled pots" type="radio" id="pstatus_1" name="pstatus_1" value="partial"><label class="form-check-label" for="formCheck-2">Partially Filled</label></div>
                <div><input type="number" id="pneed_1" name="pneed[]" class="rounded-input3 pots"><label class="soil-need">Inches of soil needed</label></div>
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
    <div class="container ig-select">
        <div class="row qty-plant-header">
            <div class="col-3 offset-1">
                <p>QTY</p>
            </div>
            <div class="col-8">
                <p>Raised Bed Size</p>
            </div>
        </div>
        <hr class="light-rule">
<div class="rb-form">
          <div class="row pot-plant">
            <div class="col-3 offset-1" id="qty"><input type="number" name="rbqty[]" class="rounded-input"></div>
            <div class="col-8"><input type="number" name="rbl[]" placeholder="L" class="rounded-input2">
                <p class="by-the-by">x</p><input type="number" name="rbw[]" placeholder="W" class="rounded-input2">
                <p class="by-the-by">x</p><input type="number" name="rbh[]" placeholder="H" class="rounded-input2"></div>
        </div>
        <div class="row">
            <div class="col offset-1">
                <div class="form-check empty-filled"><input class="form-check-input empty-filled" type="radio" name="rbstatus_1" checked value="empty"><label class="form-check-label" for="formCheck-1">Empty</label></div>
                <div class="form-check"><input class="form-check-input empty-filled" type="radio" name="rbstatus_1" value="partial"><label class="form-check-label" for="formCheck-2">Partially Filled</label></div>
                <div><input type="number" id="rbneed_1" name="rbneed[]" class="rounded-input3"><label class="soil-need">Inches of soil needed</label></div>
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
    <div class="container footer">
        <div class="row">
            <div class="col"><input type="image" border="0" src="/assets/img/next-button.png" class="next-button"></form>
                <p class="copyright">© Copyright 2019 Planting Pal.&nbsp; All rights reserved.<br></p>
            </div>
        </div>
    </div>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>-->
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