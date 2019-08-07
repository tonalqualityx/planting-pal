<?php 
defined('ABSPATH') or die('Sectumsempra!'); //For enemies
$store_name = get_the_title($store);
$address1 = get_post_meta($store, 'wpcf-address1', TRUE);
$address2 = get_post_meta($store, 'wpcf-address2', TRUE);
$phone    = get_post_meta($store, 'wpcf-phone', TRUE);
$email    = get_post_meta($store, 'wpcf-email', TRUE);
$website  = get_post_meta($store, 'wpcf-weburl', TRUE);
$guide_rates = indppl_apprates($store);
$pro = false;

$status = indppl_user_status(get_the_author_id($store));
if(in_array('paidaccountpro', $status)){
    $pro = true;
}
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
        <h1 class="lobster" style="text-align: center;"><?php echo $type_label; ?>Planting Guide</h1>
    </div>
    <div class="planting-guide-content">
        <div class='guide-product-instructions'>
            <?php 
            $i = 0;
            foreach($guide_options as $step){
                $step_title = explode(":", $step['title']);
                if(isset($step_title[1])){
                    $step_num = $step_title[0] . ":";
                    $step_title_text = $step_title[1];
                } else {
                    $step_num = "Step {$i}:";
                    $step_title_text = $step_title[0];
                }
                echo "<div class='guide-step-section'>";
                    echo "<div class='green-header indppl-dark-green-bg'>";
                    echo "<h4 class='white-text'>{$step_num}</h4>";
                    echo "<h3 class='white-text'>{$step_title_text}</h3>";
                    echo "</div>";
                    if($step['image'] && $step['image'] != ''){
                        echo "<img class='indppl-step-img' src='{$step['image']}'></img>";
                    }
                    echo "<div class='guide-step-instructions'><p>{$step['description']}</p></div>";
                    if(($type == 'pots' || $type == 'beds') && $step['step'] == 1  ){
                        $partial = false;
                        foreach($plants[$type]['need'] as $cur_need){
                            if($cur_need != '' && $cur_need != 0){
                                $partial = true;
                                $term = $type;
                                if($type == 'beds') {
                                    $term = 'raised beds';
                                }
                            }
                        }
                        if($partial){
                            echo "<div><p>For partially filled {$term}, thoroughly blend the existing soil with the new soil.</p></div>";
                        }
                    }
                    
                    echo "<div><p><strong>Product(s) used in this step:</strong></p></div>";

                        $pi = 0;
                        foreach($step['products'] as $product){
                            if($pi){ ?>
                                <div>
                                    <hr class="product-separator" />
                                </div>
                            <?php }
                            $pi++;
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
                            
                                <div class='product-guide-image'>
                                    <img src="<?php echo $image; ?>" alt="<?php echo $prod_title; ?>">
                                </div>

                            <?php }?>

                            <div class='product-guide-step-instructions'>
                                <div class='product-name'>
                                    <div class='brand'><?php echo $brand->name; ?></div> 
                                    <div class='strong product'><p><?php echo $prod_name; ?></p></div>
                                </div> 
                                <?php echo $product["instructions"]; ?>
                                <?php if($pro){ 
                                    include INDPPL_ROOT_PATH . '/templates/guides/apprates.php'; 
                                    echo "</p>";
                                }
                                
                                if($sponsorship){ ?>
                                    <p>
                                        <a href="#" class='sponsor-link orange-text'>+ Learn more about this product</a>
                                        <span class="product-name hide">
                                            <span class="brand"><?php echo $brand->name; ?></span>
                                            <span class="product"><?php echo $prod_name; ?></span>
                                        </span>
                                        <?php 
                                        if (!preg_match('^(http|https):\/\/', $sponsor_link)) {
                                            $sponsor_url = "http://" . $sponsor_link;
                                        } else {
                                            $sponsor_url = preg_replace('^(http|https):\/\/', 'http://', $sponsor_link);
                                        } ?>
                                        <span class='hide sponsor-copy'><?php echo $sponsor_copy; ?><br /><a href='<?php echo $sponsor_url; ?>' target="_blank">Learn More...</a></span> 
                                    </p>
                                <?php } ?>
                            </div>
                            
                        </div>
                    <?php }
                echo "</div>";
                $i++;
            } ?>
        </div>
    </div>
</div>