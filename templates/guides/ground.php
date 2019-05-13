<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security 

// Inherits $sections from ajax functions

$store_title = get_the_title($store);
$address1 = get_post_meta($store, 'wpcf-address1', TRUE);
$address2 = get_post_meta($store, 'wpcf-address2', TRUE);
$phone = get_post_meta($store, 'wpcf-phone', TRUE);
$email = get_post_meta($store, 'wpcf-email', TRUE);
$website = get_post_meta($store, 'wpcf-weburl', TRUE);
$saved_data = get_post_meta($store, 'wpcf-planting-guide-ground-options', TRUE);
$saved_data = str_replace(array("\'", "u201d","u2019"), array("'",'\"',"'"), $saved_data);
$saved_data = json_decode($saved_data);

$sub = indppl_user_status();

$saved_defaults = array();
$inst_checked = ' checked="checked" ';
?> 


<h2>In Ground Planting Guide</h2>
<div id="planting-guide" class="planting-guide-preview planting-guide" data-type="ground" data-store="<?php echo $store ; ?>">
    <div class="overflow">
        <div class="store-info">
            <div class="indppl-flex indppl-align-center">
                <img src="<?php echo get_post_meta($store, 'wpcf-logo', TRUE);?>">
                <div class="store-address">
                    <?php 
                    echo "<p>$store_title</p>";
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
                        echo "<p><a href='{$website}'>$website</a></p>";
                    } ?>
        
                </div>
            </div>
        </div>
        <div class="planting-guide-header indppl-flex indppl-justify-center">
            <img src="">
            <h1 style="text-align: center;">Planting Guide</h1>
        </div>
        <div class="planting-guide-content">
            <?php 
            $sec = 0;
            foreach($sections as $section => $options){
                $format_section = str_replace(array(' ',':'), array('-',''), $section);
                echo "<h3 class='orange-text' id='{$format_section}-header'>$section</h3>";
                echo "<div id='$format_section' class='guide-step-instructions'><p>";
                if($saved_data[$sec]){
                    echo $saved_data[$sec]->description;
                    $saved_defaults[$sec]['description'] = $saved_data[$sec]->description;
                    $saved_defaults[$sec]['products'] = $saved_data[$sec]->products;    
                } else {
                    $saved_data[$sec]['description'] = '';
                    echo $options['a-instructions'];
                    echo "<img src='{$options['a-image']}'>";
                }
                echo "</p></div>";
                echo "<div id='{$format_section}-products' class='guide-product-instructions'>";
                if($saved_data[$sec]->products){
                    // var_dump($saved_data[$sec]->products);
                    $saved_prods = array();
                    foreach($saved_data[$sec]->products as $saved_prod){
                        $saved_prods[] = array(
                            'product' => $saved_prod->id,
                            'instructions' => $saved_prod->instructions,
                        );
                    }

                    indppl_guide_products($saved_prods);
                }
                echo "</div>";
                $sec++;
            } ?>
        </div>
    </div>
</div>
<div class="planting-guide-sections">
    <?php 
    $hide = '';
    $count = count($sections);
    $i = 0;
    
    foreach($sections as $section => $options){ 
        $format_section = str_replace(array(' ',':'), array('-',''), $section);
        
        //Determine the default option
        $a_text = str_replace('<p>', '', $options['a-instructions']);
        $a_text = str_replace('</p>', '', $a_text);
        $b_text = str_replace('<p>', '', $options['b-instructions']);
        $b_text = str_replace('</p>', '', $b_text);
        $c_text = str_replace('<p>', '', $c_text);
        $c_text = str_replace('</p>', '', $c_text);

        $a = $inst_checked;
        $b = '';
        $c = '';
        if($c_text == $a_text || $c_text == ''){
            $a = $inst_checked;
        } elseif($c_text == $b_text){
            $a = '';
            $b = $inst_checked;
        } elseif($saved_defaults[$i] != $a_text){
            $a = '';
            $c = $inst_checked;
        }
        
        ?>
        <div class="planting-guide-options <?php echo $hide; ?> section-<?php echo $options['id']; ?>" data-step="<?php echo $i; ?>" data-title="<?php echo $format_section; ?>-header" >
            <h3><?php echo $section; ?></h3>
            <p>Customize this step by selecting an option below:</p>
            <ul class="style-free" data-products="products-<?php echo $i; ?>">

                <li class="planting-guide-instructions indppl-flex indppl-align-center indppl-no-wrap">
                    <div class="planting-guide-option-input indppl-flex">
                        <input type="radio" name="section-<?php echo $i; ?>" id="radio-<?php echo $options['id']; ?>-a" class='guide-step-description' data-content='content-<?php echo $options['id']; ?>-a' data-target="<?php echo $format_section; ?>" <?php echo $a; ?>> <label for="radio-<?php echo $options['id']; ?>-a" >Option A</label>
                    </div>
                    <div id="content-<?php echo $options['id']; ?>-a" class='instructions-content'>
                        <?php echo $options['a-instructions']; ?>
                        <img src="<?php echo $options['a-image']; ?>">
                    </div>
                </li>

                <li class="planting-guide-instructions  indppl-flex indppl-align-center indppl-no-wrap">
                    <div class="planting-guide-option-input indppl-flex">
                        <input type="radio" name="section-<?php echo $i; ?>" id="radio-<?php echo $options['id']; ?>-b" data-content='content-<?php echo $options['id']; ?>-b' data-target="<?php echo $format_section; ?>" class='guide-step-description' <?php echo $b; ?>> <label for="radio-<?php echo $options['id']; ?>-b" >Option B</label>
                    </div>
                    <div id="content-<?php echo $options['id']; ?>-b" class='instructions-content'>
                        <?php echo $options['b-instructions']; ?>
                        <img src="<?php echo $options['b-image']; ?>">
                    </div>
                </li>
                <?php if(in_array('paidaccountpro',$sub)){ ?>

                    <li class="planting-guide-instructions  indppl-flex indppl-align-center indppl-no-wrap">
                        <div class="planting-guide-option-input indppl-flex">
                            <input type="radio" name="section-<?php echo $i; ?>" id="radio-<?php echo $options['id']; ?>-custom" data-content='content-<?php echo $options['id']; ?>-custom' data-target="<?php echo $format_section; ?>" class='guide-step-description' <?php echo $c; ?> data-custom="true"> <label for="radio-<?php echo $options['id']; ?>-custom" >Custom</label>
                        </div>
                        <div class='indppl-custom-guide-instructions'>
                            <textarea id="content-<?php echo $options['id']; ?>-custom" style="height:100px;" data-custom="true"><?php if($c_text != $a_text && $c_text != $b_text){ echo $c_text;} ?></textarea>
                            <label for="<?php echo $format_section; ?>-image">Upload an Image for This Step</label>
                            <div id="<?php echo $format_section; ?>-uploaded"></div>
                            <input type="file" name="<?php echo $format_section; ?>-image" id="<?php echo $format_section; ?>-image" data-target="#<?php echo $format_section; ?>-uploaded" data-option="#radio-<?php echo $options['id']; ?>-custom" data-section="#<?php echo $format_section; ?>">
                        </div>
                    </li>
                <?php } ?>
            </ul>
            <p>Products used in this step:</p>
            <div id="products-<?php echo $i; ?>" class="step-product-select">
                <?php foreach($apprates['ground'] as $key => $value){
                    $product = get_post($key);
                    $product_instructions = get_post_meta($key, 'wpcf-step-' . $i . '-instructions', TRUE);
                    // If there are product instructions, let's just check the box
                    $checked = '';
                    if($product_instructions != ''){
                        $checked = 'checked="checked"';
                    }
                    ?>
                    
                    <div class="indppl-flex planting-guide-products indppl-align-center indppl-no-wrap">
                        <input type='checkbox' name="step-<?php echo $i; ?>[use-<?php echo $key; ?>]" id="use-<?php echo $key; ?>-<?php echo $i; ?>" data-step="<?php echo $i; ?>" data-product="<?php echo $key; ?>" data-target="<?php echo $format_section; ?>" data-instructions="instructions-<?php echo $key; ?>-<?php echo $format_section; ?>" name="instructions-<?php echo $key; ?>" <?php echo $checked; ?>>
                        <label for="use-<?php echo $key . '-' . $i; ?>"><?php echo $product->post_title; ?></label>
                        <textarea id="instructions-<?php echo $key; ?>-<?php echo $format_section; ?>" name="instructions-<?php echo $key; ?>" rows=1 ><?php echo $product_instructions; ?></textarea>
                    </div>

                <?php } ?>
            </div>
            <?php
            $i++;
            if($i > 1){ ?>
                <a href="#" id="guide-back" class="indppl-button guide-controls" data-target="section-<?php echo prev($sections)['id']; next($sections); ?>" data-header="<?php echo $format_section; ?>-header">Back</a> 
            <?php }
            if($i < $count){ ?>
                <a href="#" id="guide-next" class="indppl-button guide-controls" data-target="section-<?php echo next($sections)['id']; ?>" data-header="<?php echo $format_section; ?>-header">Next</a>
            <?php } else { ?>
                <a href="#" id="guide-save" class="indppl-button" >Save</a>

            <?php } 
            ?>
        </div>
        <?php $hide = 'display-none'; ?>
    <?php } ?>
</div>