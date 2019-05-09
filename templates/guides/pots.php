<?php defined('ABSPATH') or die('No script kiddies please!'); //For security

// Inherits $sections from ajax functions

$store_title = get_the_title($store);
$address1    = get_post_meta($store, 'wpcf-address1', TRUE);
$address2    = get_post_meta($store, 'wpcf-address2', TRUE);
$phone       = get_post_meta($store, 'wpcf-phone', TRUE);
$email       = get_post_meta($store, 'wpcf-email', TRUE);
$website     = get_post_meta($store, 'wpcf-weburl', TRUE);

?>


<h2>Potted Plants Planting Guide</h2>
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
if ($website && $website != '') {
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
            <?php foreach ($sections as $section => $options) {
    $format_section = str_replace(array(' ', ':'), array('-', ''), $section);
    echo "<h3 class='orange-text' id='{$format_section}-header'>$section</h3>";
    echo "<div id='$format_section' class='guide-step-instructions'></div>";
    echo "<div id='{$format_section}-products' class='guide-product-instructions'></div>";
}?>
        </div>
    </div>
</div>
<div class="planting-guide-sections">
    <?php
    $hide  = '';
    $count = count($sections);
    $i     = 0;
    foreach ($sections as $section => $options) {
        $format_section = str_replace(array(' ', ':'), array('-', ''), $section);?>
        <h3 style="display:none;"><?php echo $section; ?></h3>
        <div class="planting-guide-options <?php echo $hide; ?> section-<?php echo $options['id']; ?>" data-step="<?php echo $i; ?>" data-title="<?php echo $format_section; ?>-header" >
            <p>Customize this step by selection an option below:</p>
            <ul class="style-free" data-products="products-<?php echo $i; ?>">

                <li class="planting-guide-instructions indppl-flex indppl-align-center">
                    <div class="planting-guide-option-input indppl-flex">
                        <input type="radio" name="section-<?php echo $i; ?>" id="radio-<?php echo $options['id']; ?>-a" class='guide-step-description' data-content='content-<?php echo $options['id']; ?>-a' data-target="<?php echo $format_section; ?>"> <label for="radio-<?php echo $options['id']; ?>-a" >Option A</label>
                    </div>
                    <div id="content-<?php echo $options['id']; ?>-a">
                        <?php echo $options['a-instructions']; ?>
                    </div>
                </li>

                <li class="planting-guide-instructions  indppl-flex indppl-align-center">
                    <div class="planting-guide-option-input indppl-flex">
                        <input type="radio" name="section-<?php echo $i; ?>" id="radio-<?php echo $options['id']; ?>-b" data-content='content-<?php echo $options['id']; ?>-b' data-target="<?php echo $format_section; ?>" class='guide-step-description'> <label for="radio-<?php echo $options['id']; ?>-b" >Option B</label>
                    </div>
                    <div id="content-<?php echo $options['id']; ?>-b">
                        <?php echo $options['b-instructions']; ?>
                    </div>
                </li>
            </ul>
            <p>Products used in this step:</p>
            <div id="products-<?php echo $i; ?>" class="step-product-select">
                <?php foreach ($apprates['pots'] as $type) {
                    foreach($type as $key => $value){
                        $product              = get_post($key);
                        $product_instructions = get_post_meta($key, 'wpcf-step-' . $i . '-instructions', TRUE);
                        // If there are product instructions, let's just check the box
                        $checked = '';
                        if ($product_instructions != '') {
                            $checked = 'checked="checked"';
                        }
                        ?>

                        <div class="indppl-flex planting-guide-products indppl-align-center">
                            <input type='checkbox' name="step-<?php echo $i; ?>[use-<?php echo $key; ?>]" id="use-<?php echo $key; ?>-<?php echo $i; ?>" data-step="<?php echo $i; ?>" data-product="<?php echo $key; ?>" data-target="<?php echo $format_section; ?>" data-instructions="instructions-<?php echo $key; ?>-<?php echo $format_section; ?>" name="instructions-<?php echo $key; ?>" <?php echo $checked; ?>>
                            <label for="use-<?php echo $key . '-' . $i; ?>"><?php echo $product->post_title; ?></label>
                            <textarea id="instructions-<?php echo $key; ?>-<?php echo $format_section; ?>" name="instructions-<?php echo $key; ?>" rows=1 ><?php echo $product_instructions; ?></textarea>
                        </div>
                    <?php }

                }?>
            </div>
            <?php
            $i++;
            if ($i > 1) {?>
                <a href="#" id="guide-back" class="indppl-button guide-controls" data-target="section-<?php echo prev($sections)['id'];
                next($sections); ?>" data-header="<?php echo $format_section; ?>-header">Back</a>
            <?php }
            if ($i < $count) {?>
                <a href="#" id="guide-next" class="indppl-button guide-controls" data-target="section-<?php echo next($sections)['id']; ?>" data-header="<?php echo $format_section; ?>-header">Next</a>
            <?php } else {?>
                <a href="#" id="guide-save" class="indppl-button" >Save</a>

            <?php } ?>
        </div>
        <?php $hide = 'display-none';?>
    <?php }?>
</div>