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
                    <?php
                    $name = $item['name'];
                    if(explode(' ', $item['name'])[1] == 'qt-d' || explode(' ', $item['name'])[1] == 'qt-l'){
                        
                        $name = explode(' ', $item['name'])[0] . ' Quart';
                    }
                    ?>
                    <p class='smaller'><strong><?php echo $item['brand']; ?></strong></p>
                    <p class="bigger"><strong><?php echo str_replace($item['brand'] . " ", '', $item['product']); ?><br><?php echo $name;?></strong></p>

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
    <div class="col indppl-big-button-container">
        <!-- <a style="color:#fff" class='indppl-go-back-btn indppl-big-button indppl-green-button' href="">Go back</a> -->
        <?php $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; ?>
        <form id='return-form-data' method='post' action='' enctype="multipart/form-data">
        <input type="hidden" name="next-step" value="plants_form">
        <input type="hidden" name="storeid" value="<?php echo $storeid;?>">
        <input type="hidden" name="plants" value='<?php echo $user_plants; ?>'>
        <input type="hidden" name="list" value='<?php echo $encoded_shopping_list; ?>'>
        <input type="hidden" name="ground_store" value='<?php echo $ground_store; ?>'>
        <input style="color:#fff" id='indppl-form-go-back' value='Go Back' class='indppl-go-back-btn indppl-big-button indppl-green-button' type='submit'>
        </form>           
    </div>
    <div class="col button2 indppl-big-button-container">
        <a style="color:#fff" class='indppl-big-button indppl-green-button' href="">Start over</a>
    </div>
</div>


<div id='keep-going-container' class="container"><img src="<?php echo INDPPL_ROOT_URL; ?>assets/img/keep-going-pg.png" class="keep-going">
    <form action="" method="post" enctype="text/plain" >
    <input type="hidden" name="next-step" value="planting-guide">
    <input type="hidden" name="storeid" value="<?php echo $storeid;?>">
    <input type="hidden" name="plants" value='<?php echo $user_plants; ?>'>
    <input type="hidden" name="list" value='<?php echo json_encode($encoded_shopping_list); ?>'>
    <input type="hidden" name="ground_store" value='<?php echo $ground_store; ?>'>
    <input class="form-control email-address-add" name="email" type="text" placeholder="Enter Email Address"><input type="image" id="get-planting-guide" src="<?php echo INDPPL_ROOT_URL; ?>assets/img/send-guide.png" border="0" class="send-guide" data-store="<?php echo $storeid; ?>" data-plants='<?php echo $user_plants; ?>' data-list='<?php echo $encoded_shopping_list; ?>' data-ground='<?php echo $ground_store; ?>'></form>
</div>
