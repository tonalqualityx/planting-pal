<div class='light-blue-bg'>
    <div class="row type-header-2">
        <div class="col">
            <p class='shopping-list-header'>Shopping List</p>
            <p class='shopping-list-header-text'>Show your plants some LOVE!</p>
            <p class='shopping-list-header-text'>The experts at <?php echo get_the_title($storeid); ?> recommend:</p>
        </div>
    </div>
    <div id="list-container">

        <div class="container shop-list">
            <!-- <div class="row products-header">
                <div class="col-3">
                    <p>QTY</p>
                </div>
                <div class="col">
                    <p>Product</p>
                </div>
            </div> -->
        
        <?php foreach($shopping_list as $key => $item){ ?>
        
        <!-- Standard Product -->
            <div class="row products">
                <div class="col-3 align-self-center">
                    <p class='qty-title'>Qty:</p>
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
                            echo "<a href='#' class='sponsor-link orange-text'>Learn More</a>";
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
                        <p class='smaller grey-text'><strong><?php echo $item['brand']; ?></strong></p>
                        <p class="bigger black-text"><strong><?php echo str_replace($item['brand'] . " ", '', $item['product']); ?></strong></p>
                        <p class='smaller black-text'><strong>Size: <?php echo $name;?></strong></p>

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
            <input style="color:#fff" id='indppl-form-go-back' value='go back' class='indppl-go-back-btn indppl-big-button indppl-green-button' type='submit'>
            </form>           
        </div>
        <div class="col button2 indppl-big-button-container">
            <a style="color:#fff" class='indppl-big-button indppl-green-button' href="">start over</a>
        </div>
    </div>
</div>

<div id='keep-going-container' class="container">
    <div class='flex-basis-66'>
        <form action="" id="get-planting-guide-form" method="post" enctype="text/plain" >
            <h3 class='lobster keep-going light-green-text'>Keep Going!</h3>
            <p class='light-green-text keep-going-text'>Get a step-by-step guide how to use these products when you get home</p>
            <input type="hidden" name="next-step" value="planting-guide">
            <input type="hidden" name="storeid" value="<?php echo $storeid;?>">
            <input type="hidden" name="plants" value='<?php echo $user_plants; ?>'>
            <input type="hidden" name="list" value='<?php echo json_encode($encoded_shopping_list); ?>'>
            <input type="hidden" name="ground_store" value='<?php echo $ground_store; ?>'>
            <input class="form-control email-address-add" name="email" type="text" placeholder="Enter Email">
            <a href='#' id="get-planting-guide" border="0" class="send-guide indppl-button" data-store="<?php echo $storeid; ?>" data-plants='<?php echo $user_plants; ?>' data-list='<?php echo $encoded_shopping_list; ?>' data-ground='<?php echo $ground_store; ?>'>Send Planting Guide</a>
        </form>
    </div>
    <div class='flex-basis-33'>
        <img src="<?php echo INDPPL_ROOT_URL; ?>assets/img/planting-pal-carrot.png" class="keep-going">
    </div>
</div>