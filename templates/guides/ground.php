<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security 

// Inherits $sections from ajax functions

$address1 = get_post_meta($store, 'wpcf-address1', TRUE);
$address2 = get_post_meta($store, 'wpcf-address2', TRUE);
$phone = get_post_meta($store, 'wpcf-phone', TRUE);
$email = get_post_meta($store, 'wpcf-email', TRUE);
$website = get_post_meta($store, 'wpcf-weburl', TRUE);

?> 


<h2>In Ground Planting Guide</h2>
<div class="planting-guide-preview planting-guide">
    <div class="overflow">
        <div class="store-info">
            <div class="indppl-flex align-center">
                <img src="<?php echo get_post_meta($store, 'wpcf-logo', TRUE);?>">
                <div class="store-address">
                    <?php 
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
            <?php foreach($sections as $section => $options){
                echo "<h3 class='orange-text'>$section</h3>";
                $format_section = str_replace(array(' ',':'), array('-',''), $section);
                echo "<div id='$format_section'></div>";
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
        $format_section = str_replace(array(' ',':'), array('-',''), $section); ?>
        <h3 style="display:none;"><?php echo $section; ?></h3>
        <div class="planting-guide-options <?php echo $hide; ?> section-<?php echo $options['id']; ?>" >
            <p>Customize this step by selection an option below:</p>
            <ul class="style-free" data-products="products-<?php echo $i; ?>">

                <li class="planting-guide-instructions indppl-flex indppl-align-center">
                    <div class="planting-guide-option-input indppl-flex">
                        <input type="radio" name="section-<?php $options['id']; ?>" id="radio-<?php echo $options['id']; ?>-a" data-content='content-<?php echo $options['id']; ?>-a' data-target="<?php echo $format_section; ?>"> <label for="radio-<?php echo $options['id']; ?>-a" >Option A</label>
                    </div>
                    <div id="content-<?php echo $options['id']; ?>-a">
                        <?php echo $options['a-instructions']; ?>
                    </div>
                </li>

                <li class="planting-guide-instructions  indppl-flex indppl-align-center">
                    <div class="planting-guide-option-input indppl-flex">
                        <input type="radio" name="section-<?php $options['id']; ?>" id="radio-<?php echo $options['id']; ?>-b" data-content='content-<?php echo $options['id']; ?>-b' data-target="<?php echo $format_section; ?>"> <label for="radio-<?php echo $options['id']; ?>-b" >Option B</label>
                    </div>
                    <div id="content-<?php echo $options['id']; ?>-b">
                        <?php echo $options['b-instructions']; ?>
                    </div>
                </li>
            </ul>
            <p>Products used in this step:</p>
            <div id="products-<?php echo $i; ?>">
                <?php foreach($apprates['ground'] as $key => $value){
                    $product = get_post($key);
                    $product_instructions = get_post_meta($key, 'wpcf-step-' . $i . '-instructions', TRUE);?>
                    
                    <div class="indppl-flex planting-guide-products indppl-align-center">
                        <input type='checkbox' name="use-<?php echo $key; ?>" id="use-<?php echo $key; ?>" data-step="<?php echo $i; ?>" data-product="<?php echo $key; ?>" data-target="<?php echo $format_section; ?>">
                        <label for="use-<?php echo $key; ?>"><?php echo $product->post_title; ?></label>
                        <textarea name="instructions-<?php echo $key; ?>" ><?php echo $product_instructions; ?></textarea>
                    </div>

                <?php } ?>
            </div>
            <?php
            $i++;
            if($i < $count){ ?>
                <a href="#" class="indppl-button" data-target="section-<?php echo next($sections)['id']; ?>">Next</a>
            <?php } else { ?>
                <a href="#" class="indppl-button" >Save</a>

            <?php } 
            ?>
        </div>
        <?php $hide = 'display-none'; ?>
    <?php } ?>
</div>