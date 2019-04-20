<div class="row type-header-2">
    <div class="col">
        <p><strong>Shopping List</strong><br></p>
    </div>
</div>
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
        <div class="col product-details">
            <p><strong><?php echo $item['brand']; ?></strong></p>
            <p class="bigger"><strong><?php echo str_replace($item['brand'] . " ", '', $item['product']); ?><br><?php echo $item['name'];?></strong></p>
        </div>
    </div>
<!-- /Standard Product -->
<!-- Standard Product -->
<?php } ?>
    <div class="row products">
        <div class="col-3 align-self-center border1">
            <p class="qty-bag">1</p>
        </div>
        <div class="col product-details">
            <p><strong>Dr. Earth</strong></p>
            <p class="bigger"><strong>All Purpose Fertilizer<br>4.00 lbs</strong></p>
        </div>
    </div>
<!-- /Standard Product -->
</div>


<div class="row no-gutters" id="buttons">
    <div class="col">
        <p class="button"><a style="color:#fff" href="/s/1">Go back</a></p>
    </div>
    <div class="col button2">
        <p class="button"><a style="color:#fff" href="/">Start over</a></p>
    </div>
</div>


<div class="container"><img src="<?php echo INDPPL_ROOT_URL; ?>assets/img/keep-going-pg.png" class="keep-going">
    <form action="#" method="post">
    <input type="hidden" name="next-step" value="planting-guide">
    <input type="hidden" name="storeid" value="<?php echo $storeid;?>">
    <input type="hidden" name="plants" value='<?php echo $user_plants; ?>'>
    <input type="hidden" name="list" value='<?php echo $encoded_shopping_list; ?>'>
    <input class="form-control email-address-add" name="email" type="text" placeholder="Enter Email Address"><input type="image" src="<?php echo INDPPL_ROOT_URL; ?>assets/img/send-guide.png" border="0" class="send-guide"></form>
</div>
