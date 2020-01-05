<?php defined('ABSPATH') or die('No script kiddies please!'); //For security

// Inherits $sections from ajax functions

$store_title = get_the_title($store);
$address1    = get_post_meta($store, 'wpcf-address1', TRUE);
$address2    = get_post_meta($store, 'wpcf-address2', TRUE);
$phone       = get_post_meta($store, 'wpcf-phone', TRUE);
$email       = get_post_meta($store, 'wpcf-email', TRUE);

$website     = get_post_meta($store, 'wpcf-weburl', TRUE);
$show_website = false;
if ($website && $website != '') {
    $show_website = truel;
    if (!preg_match('^(http|https):\/\/', $website)) {
        $url = "//" . $website;
    } else {
        $url = preg_replace('^(http|https):\/\/', '//', $website);
    }
    $website = $url;
}

$saved_data = get_post_meta($store, 'wpcf-planting-guide-pots-options', TRUE);
$saved_data = str_replace(array("\'", "u201d", "u2019"), array("'", '\"', "'"), $saved_data);
$saved_data = json_decode($saved_data);

$store_owner = get_the_author_meta('ID', $store);
$sub = indppl_user_status();
$pro = in_array('paidaccountpro', $sub) ? true : false;

$saved_defaults = array();
$inst_checked = ' checked="checked" ';

$products_list = array();
foreach ($apprates['pots'] as $type_key => $type) {
    foreach($type as $list_key => $list_val){
        $products_list[$list_key] = true;
    }
}

// Setup checkmarks
$check_box  = '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path></svg>';
$check_mark = '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path><path class="checkmark__check" fill="green" d="M15 12 L12 15 L20 22 L37 2 L20 17 L15 12"></path></svg>';


?>

<div class="indppl-instructions">
  <div class="indppl-instructions-text">
    <h2>Potted Plants Planting Guide Builder</h2>
    <p>For each step in the planting guide, choose from the text and graphics provided or create your own. Then, select the products being used in each step - its also recommended to include product-specific info here too. As you're building, check how your planting guide looks in the preview window.</p>
  </div>
  <div class="indppl-video">
    <iframe width="266" height="150" src="https://www.youtube.com/embed/_u9CgVPHU6A" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>    
  </div>
</div>
<div class="planting-guide-preview-title ppl-med-green-bg">
    <h3 class="white-text text-center">Planting Guide Preview Window</h3>
</div>
<div id="planting-guide" class="planting-guide-preview planting-guide" data-type="pots" data-store="<?php echo $store; ?>">
    <div class="overflow">
        <div class="store-info">
            <div class="indppl-flex indppl-align-center">
                <img src="<?php echo get_post_meta($store, 'wpcf-logo', TRUE); ?>">
                <div class="store-address">
                    <?php
                    echo "<p>$store_title</p>";
                    if ($address1 && $address1 != '') {
                        echo "<p>$address1</p>";
                    }
                    if ($address2 && $address2 != '') {
                        echo "<p>$address2</p>";
                    }
                    if ($phone && $phone != '') {
                        echo "<p>$phone</p>";
                    }
                    if ($email && $email != '') {
                        echo "<p>$email</p>";
                    }
                    if($show_website) {
                        echo "<p><a href='{$website}'>$website</a></p>";
                    } ?>

                </div>
            </div>
        </div>
        <div class="planting-guide-header indppl-flex indppl-justify-center">
            <h1 class="lobster" style="text-align: center;">Potted Plants Planting Guide</h1>
        </div>
        <div class="planting-guide-content">
            <?php 
            $sec = 0;
            foreach($sections as $section => $options){
                echo "<div class='guide-product-instructions'>";
                    echo "<div class='guide-step-section'>";
                        $format_section = str_replace(array(' ',':'), array('-',''), $section);
                        $step_number = ($sec == 0 ? '' : "<h4 class='white-text'>Step {$sec}:</h4>");
                        echo "<div class='green-header indppl-dark-green-bg'>{$step_number}<h3 class='white-text' id='{$format_section}-header'>$section</h3></div>";
                        echo "<div id='$format_section' class='guide-step-instructions'><p>";
                        if($saved_data[$sec]->image){
                            if($saved_data[$sec]->image && $saved_data[$sec]->image != ''){
                                echo "<img src='{$saved_data[$sec]->image}' class='indppl-step-img'>";
                            }
                            $saved_defaults[$sec]['image'] = $saved_data[$sec]->image;
                        }
                        if($saved_data[$sec]){
                            echo $saved_data[$sec]->description;
                            $saved_defaults[$sec]['description'] = $saved_data[$sec]->description;
                            $saved_defaults[$sec]['products'] = $saved_data[$sec]->products;    
                        } else {
                            if($options['a-image'] && $options['a-image'] != ''){
                                echo "<img src='{$options['a-image']}' class='indppl-step-img'>";
                            }
                            $saved_data[$sec]['description'] = '';
                            echo $options['a-instructions'];
                        }
                        echo "</p>";
                        echo "</div>";
                        echo "<div><p><strong>Product(s) used in this step:</strong></p></div>";
                        echo "<div id='{$format_section}-products' class='guide-product-instructions'>";
                        $preload_prods = array();
                        if($saved_data[$sec]->products){
                            // var_dump($saved_data[$sec]->products);
                            $saved_prods = array();
                            foreach($saved_data[$sec]->products as $saved_prod){
                                $saved_prods[] = array(
                                    'product' => $saved_prod->id,
                                    'instructions' => $saved_prod->instructions,
                                );
                            }

                            $preload_prods = $saved_prods;
                            
                        } elseif($saved_data[0]->description == ''){
                            $def_inst = false;
                            if($sec == 2){
                                foreach ($apprates['pots']['filler'] as $k => $v) {

                                    $def_inst = get_post_meta($k, 'wpcf-pots-instructions-step-2-bulk',TRUE);

                                    if($def_inst){
                                        $preload_prods[] = array(
                                            'product' => $k,
                                            'instructions' => $def_inst,
                                        );
                                    }
                                }
                                foreach($apprates['pots']['blended'] as $k => $v){

                                    $def_inst = get_post_meta($k, 'wpcf-pots-instructions-step-2-blended',TRUE);

                                    if($def_inst){
                                        $preload_prods[] = array(
                                            'product' => $k,
                                            'instructions' => $def_inst,
                                        );
                                    }
                                }
                            } else {
                                foreach($products_list as $k => $v){
                                    $def_inst = get_post_meta($k, 'wpcf-pots-instructions-step-' . $sec,TRUE);

                                    if($def_inst){
                                        $preload_prods[] = array(
                                            'product' => $k,
                                            'instructions' => $def_inst,
                                        );
                                    }
                                }
                            }
                            
                        }
                        indppl_guide_products($preload_prods);
                    echo "</div>";
                echo "</div>";
                echo "</div>";
                $sec++;
            } ?>
        </div>
    </div>
</div>
<div class="planting-guide-sections">
    <?php
    $hide  = '';
    $count = count($sections);
    $i     = 0;
    foreach ($sections as $section => $options) {
        $format_section = str_replace(array(' ', ':'), array('-', ''), $section);

        $c_text = $saved_defaults[$i]['description'];
        
        //Determine the default option
        $a_text = str_replace('<p>', '', $options['a-instructions']);
        $a_text = str_replace('</p>', '', $a_text);
        $b_text = str_replace('<p>', '', $options['b-instructions']);
        $b_text = str_replace('</p>', '', $b_text);
        $c_text = str_replace('<p>', '', $c_text);
        $c_text = str_replace('</p>', '', $c_text);

        $a = '';
        $b = '';
        $c = '';

        if(is_array($saved_data)){
            $selected_option = $saved_data[$i]->option;
            switch($selected_option){
                case 'b' : 
                    $b = $inst_checked;
                    break;
                case 'c' :
                    $c = $inst_checked;
                    break;
                default:
                    $a = $inst_checked;
                    break;
            }
        } else {
            $a = $inst_checked;
        }
        
        
        ?>
        <div class="planting-guide-options <?php echo $hide; ?> section-<?php echo $options['id']; ?>" data-step="<?php echo $i; ?>" data-title="<?php echo $format_section; ?>-header" >
            <h3><?php echo $section; ?></h3>
            <h4>Text and Graphics</h4>
            <p>Choose from the pre-written text and graphics for this step or use your own.</p>
            <ul class="style-free" data-products="products-<?php echo $i; ?>">

                <li class="planting-guide-instructions indppl-flex indppl-no-wrap indppl-align-start indppl-no-wrap">
                    <div class="planting-guide-option-input indppl-flex">
                        <input type="radio" name="section-<?php echo $i; ?>" id="radio-<?php echo $options['id']; ?>-a" class='guide-step-description' data-content='content-<?php echo $options['id']; ?>-a' data-target="<?php echo $format_section; ?>" data-option="a" <?php echo $a; ?>>
                        <label for="radio-<?php echo $options['id']; ?>-a" >Option #1</label>
                    </div>
                    <div class='instructions-content <?php if($a != ''){echo " active";} ?>'>
                        <?php if ($options['a-image'] && $options['a-image'] != '') {?>
                                <img id="content-<?php echo $options['id']; ?>-a-image" src="<?php echo $options['a-image']; ?>">
                            <?php }?>
                        <div id="content-<?php echo $options['id']; ?>-a" class="instructions-content-text">
                            <?php echo $options['a-instructions']; ?>
                            <a href="#" class="instructions-edit orange-text">Edit Text</a>
                        </div>
                    </div>
                </li>

                <li class="planting-guide-instructions indppl-no-wrap indppl-flex indppl-align-start">
                    <div class="planting-guide-option-input indppl-flex">
                        <input type="radio" name="section-<?php echo $i; ?>" id="radio-<?php echo $options['id']; ?>-b" data-content='content-<?php echo $options['id']; ?>-b' data-target="<?php echo $format_section; ?>" data-option="b"  class='guide-step-description' <?php echo $b; ?>>
                        <label for="radio-<?php echo $options['id']; ?>-b" >Option #2</label>
                    </div>
                    <div class='instructions-content <?php if($b != ''){echo " active";} ?>'>
                        <?php if($options['b-image'] && $options['b-image'] != ''){ ?>
                            <img src="<?php echo $options['b-image']; ?>" id="content-<?php echo $options['id']; ?>-b-image" >
                        <?php } ?>
                        <div id="content-<?php echo $options['id']; ?>-b" class="instructions-content-text">
                            <?php echo $options['b-instructions']; ?>
                            <a href="#" class="instructions-edit orange-text">Edit Text</a>
                        </div>
                    </div>
                </li>
                
                <?php if($pro){ ?>
                    <li class="planting-guide-instructions  indppl-flex indppl-align-center indppl-no-wrap indppl-custom">

                        <div class="planting-guide-option-input indppl-flex">
                            <input type="radio" name="section-<?php echo $i; ?>" id="radio-<?php echo $options['id']; ?>-custom" data-content='content-<?php echo $options['id']; ?>-custom' data-target="<?php echo $format_section; ?>" class='guide-step-description' <?php echo $c; ?> data-custom="true" data-option="c"> <label for="radio-<?php echo $options['id']; ?>-custom" >Custom</label>
                        </div>

                        <div class='indppl-custom-guide-instructions instructions-content <?php if ($c != '') {echo " active";}?>'>

                            <div id="<?php echo $format_section; ?>-uploaded">
                                <p style="margin-top:0;font-weight:bold;">Upload planting graphic:</p>
                                <label for="<?php echo $format_section; ?>-image" class="indppl-btn indppl-file-upload">Browse</label>
                                <input type="file" name="<?php echo $format_section; ?>-image" id="<?php echo $format_section; ?>-image" data-target="#<?php echo $format_section; ?>-custom-image" data-option="#radio-<?php echo $options['id']; ?>-custom" data-section="#<?php echo $format_section; ?>" class="hide">
                                <div id="<?php echo $format_section; ?>-custom-image" class="custom-image-container ">
                                    <?php if($c != ''){
                                        echo "<img src='{$saved_defaults[$i]['image']}' id='content-{$options['id']}-custom-image'>";
                                    }?>
                                </div>      
                            </div>

                            <textarea id="content-<?php echo $options['id']; ?>-custom" style="height:200px;" data-custom="true" data-target="<?php echo $format_section; ?>"><?php if($c_text != $a_text && $c_text != $b_text){ echo $c_text;} ?></textarea>
                        </div>
                    </li>
                <?php } ?>
            </ul>
            <h4>Product Recommendations</h4>
            <p>Choose the products you'd like to appear in this step of the planting guide. Customize the text to provide your customers with the product-specific instructions or tips. For example, if the product is best applied in the morning or should only be 1/4" thick - say that here.</p>
            <div id="products-<?php echo $i; ?>" class="step-product-select">
                <?php 
                $displayed = array();
                foreach ($apprates['pots'] as $type_key => $type) {
                    $checked = '';
                    $product_instructions = '';
                    foreach($type as $key => $value){
                        if(!in_array($key,$displayed)){
                            $displayed[] = $key;
                            $product = get_post($key);
                            $prod_brand = get_the_terms($key, 'brand');
                            $prod_brand = $prod_brand[0]->name;
                            if(isset($saved_data[$i]->products)){
                                foreach($saved_data[$i]->products as $saved_prod_instructions){
                                    if($saved_prod_instructions->id == $key){
                                        $product_instructions = $saved_prod_instructions->instructions;
                                        $checked = 'checked="checked"';
                                    }
                                }
                            } else {
                                if($i == 2 && $type_key == 'filler'){
                                    $product_instructions = get_post_meta($key, 'wpcf-pots-instructions-step-2-bulk', TRUE);
                                } elseif($i == 2 && $type_key == 'blended'){
                                    $product_instructions = get_post_meta($key, 'wpcf-pots-instructions-step-2-blended', TRUE);
                                } else {
                                    $product_instructions = get_post_meta($key, 'wpcf-pots-instructions-step-' . $i , TRUE);
                                }
                                // If there are product instructions, let's just check the box
                                $checked = '';
                                if ($product_instructions != '') {
                                    $checked = 'checked="checked"';
                                }
                            }
                            ?>

                            <div class="indppl-flex indppl-no-wrap planting-guide-products indppl-align-start">
                                <input type='checkbox' name="step-<?php echo $i; ?>[use-<?php echo $key; ?>]" id="use-<?php echo $key; ?>-<?php echo $i; ?>" class="guide-product-select hide" data-step="<?php echo $i; ?>" data-product="<?php echo $key; ?>" data-target="<?php echo $format_section; ?>" data-instructions="instructions-<?php echo $key; ?>-<?php echo $format_section; ?>" name="instructions-<?php echo $key; ?>" <?php echo $checked; ?>>
                                <label for="use-<?php echo $key . '-' . $i; ?>" class="product-check" ><?php echo $checked == '' ? $check_box : $check_mark; ?></label>

                                <div class="product-instructions-section indppl-flex <?php echo $checked == '' ? '' : 'active'; ?>">
                                    <div class="product-instructions-input">
                                        <label for="use-<?php echo $key . '-' . $i; ?>">
                                            <?php echo "<span class='product-instructions-brand'>$prod_brand</span>, $product->post_title"; ?>
                                            <br /><span class="product-instructions-blurb">Product specific instructions or tips:</span>
                                        </label>
                                        <textarea id="instructions-<?php echo $key; ?>-<?php echo $format_section; ?>" name="instructions-<?php echo $key; ?>" rows=1 ><?php echo $product_instructions; ?></textarea>
                                    </div>
                                    <div class="product-instructions-sponsored-image">
                                        <?php $sponsorship = check_sponsorship($key);
                                        // var_dump($sponsorship);
                                        if($sponsorship){
                                            echo "<img src='{$sponsorship['image']}'>";
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    }

                }?>
            </div>
            <?php
            $i++;
            if ($i > 1) {?>
                <a href="#" id="guide-back" class="orange-text guide-controls" data-target="section-<?php echo prev($sections)['id'];
                next($sections); ?>" data-header="<?php echo $format_section; ?>-header">Back</a>
            <?php }
            if ($i < $count) {?>
                <a href="#" id="guide-next" class="indppl-button guide-controls" data-target="section-<?php echo next($sections)['id']; ?>" data-header="<?php echo $format_section; ?>-header">NEXT <?php echo key($sections); ?></a>
            <?php } else {?>
                <a href="#" id="guide-save" class="indppl-button" >Save</a>

            <?php } ?>
        </div>
        <?php $hide = 'display-none';?>
    <?php }?>
    <script>
        // Set some variables
        var indpplP = <?php echo $pro ? 'true' : 'false'; ?>
    </script>
</div>