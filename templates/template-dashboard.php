<?php defined('ABSPATH') or die('No script kiddies please!'); //For enemies
global $post;

// get_header();

$user_id = get_current_user_id();
$status = indppl_user_status($user_id);

wp_head();?>

<body >
    <div id="dashboard-title-bar">
        <h1 style="color:white;margin:0; padding:15px 7px; font-size: 1.25em; font-weight: 800;">Planting Pal admin center</h1>
    </div>
    <div class="indppl-flex grey-background">
        <div id="indppl-dashboard-nav">
            <ul>
                <li <?php if($post->post_name == 'subscription-info') { echo "class='active'";} ?>><a href="/my-account/subscription-info/">Account Profile</a></li>
                <li <?php if ($post->post_name == 'store-profile') {echo "class='active'";}?>><a href="/my-account/store-profile">Manage Stores</a></li>
                <li class='no-small'><a href="#">Manage Users</a> <span class='coming-soon'>coming soon!</span></li>
                <!-- <li><a href="#">Billing</a></li> -->
                <li class='no-small'><a href="#">Reports</a> <span class='coming-soon'>coming soon!</span></li>
                <li <?php if ($post->post_name == 'support') {echo "class='active'";}?>><a href="/my-account/support/">Support</a></li>
            </ul>
        </div>
        <div id="dashboard-content" class="main-body">
            <h1 style="display:flex;justify-content: space-between; align-items: baseline;"><?php the_title(); ?> <?php if (is_page('store-profile') && empty($_GET) && in_array('paidaccountpro', $status)) {$add_button = get_add_store_button();
    echo $add_button;}?></h1>
            <?php 
            if (have_posts()) : while (have_posts()) : the_post();
                the_content();
            endwhile;
            endif; ?> 
        </div>
    </div>
</body>


<?php echo wp_footer(); ?>