<?php 
defined('ABSPATH') or die('No script kiddies please!'); //For security 
$address1 = get_post_meta($store, 'wpcf-address1', TRUE);
$address2 = get_post_meta($store, 'wpcf-address2', TRUE);
$phone    = get_post_meta($store, 'wpcf-phone', TRUE);
$email    = get_post_meta($store, 'wpcf-email', TRUE);
$website  = get_post_meta($store, 'wpcf-weburl', TRUE);
?>

<div id="planting-guide" class="planting-guide" data-type="ground" data-store="<?php echo $store ; ?>">
    <div class="store-info">
        <div class="indppl-flex indppl-align-center">
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
        <div class='guide-product-instructions'>
            <?php foreach($guide_options as $step){
                echo "<h3 class='orange-text'>{$step['title']}</h3>";
                echo "<div class='guide-step-instructions'>{$step['description']}</div>";
                if($step['image'] && $step['image'] != ''){
                    echo "<img src='{$step['image']}'></img>";
                }

                // THIS PART SHOULD BE A SHORTCODE THAT GETS CALLED EVERY TIME
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
                                <br /><a href="#" class='sponsor-link'>Learn more about this product - Click Here</a><div class='hide sponsor-copy'><?php echo $sponsor_copy; ?><br /><a href='<?php echo $sponsor_link; ?>' target="_blank">Learn More...</a></div>
                            <?php }?>
                        </div>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
</div>