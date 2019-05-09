<div class="row type-header-2">
    <div class="col">
        <p><strong>Shopping List</strong><br></p>
    </div>
</div>
<div id="list-container">

    <div class="container shop-list">
        <div class="row products-header">
            <div class="col-3 border1">
                <p>QTY</p>
            </div>
            <div class="col border1">
                <p>Product</p>
            </div>
        </div>
    
    <?php foreach($shopping_list as $key => $item){ ?>
    
    <!-- Standard Product -->
        <div class="row products">
            <div class="col-3 align-self-center border1">
                <p class="qty-bag"><?php echo $item['count']; ?></p>
            </div>
            <div class=" indppl-flex indppl-align-center product">

                <?php 

                // check for a sponsorship
                $sponsorship = toolset_get_related_post($key, 'sponsorship-product');

                // If there is a sponsorship, print it up all happy
                if($sponsorship){ 
                    $sponsored_image = get_post_meta($sponsorship, 'wpcf-sponsorship-image', TRUE);
                    $sponsor_copy = get_post_meta($sponsorship, 'wpcf-sponsorship-copy', TRUE);
                    $sponsor_link = get_post_meta($sponsorship, 'wpcf-sponsor-url', true);
                    update_sponsorship_view_count($storeid, $sponsorship);
                    echo "<div class='sponsorship'>";
                    echo "<div><img src='{$sponsored_image}' class='sponsor-image'></div>";
                        echo "<a href='#' class='sponsor-link'>Learn More</a>";
                        echo "<div class='hide sponsor-copy'>
                            {$sponsor_copy}
                            <br />
                            <a href='{$sponsor_link}' target='_blank' rel='noopener noreferrer'>Learn More...</a>
                            </div>";
                    echo "</div>";
                }
                ?>
                <div class='product-name'>
                    <p><strong><?php echo $item['brand']; ?></strong></p>
                    <p class="bigger"><strong><?php echo str_replace($item['brand'] . " ", '', $item['product']); ?><br><?php echo $item['name'];?></strong></p>

                </div>
            </div>
        </div>
    <!-- /Standard Product -->
    <!-- Standard Product -->
    <?php } ?>
        
    <!-- /Standard Product -->
    </div>
</div>


<div class="row no-gutters" id="buttons">
    <div class="col">
        <p class="button"><a style="color:#fff" href="">Go back</a></p>
    </div>
    <div class="col button2">
        <p class="button"><a style="color:#fff" href="">Start over</a></p>
    </div>
</div>


<div class="container"><img src="<?php echo INDPPL_ROOT_URL; ?>assets/img/keep-going-pg.png" class="keep-going">
    <form action="" method="post" enctype="text/plain" >
    <input type="hidden" name="next-step" value="planting-guide">
    <input type="hidden" name="storeid" value="<?php echo $storeid;?>">
    <input type="hidden" name="plants" value='<?php echo $user_plants; ?>'>
    <input type="hidden" name="list" value='<?php echo $encoded_shopping_list; ?>'>
    <input class="form-control email-address-add" name="email" type="text" placeholder="Enter Email Address"><input type="image" id="get-planting-guide" src="<?php echo INDPPL_ROOT_URL; ?>assets/img/send-guide.png" border="0" class="send-guide" data-store="<?php echo $storeid; ?>" data-plants='<?php echo $user_plants; ?>' data-list='<?php echo $encoded_shopping_list; ?>'></form>
</div>
