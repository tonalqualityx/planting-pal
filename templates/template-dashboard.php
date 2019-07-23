<?php defined('ABSPATH') or die('No script kiddies please!'); //For enemies
global $post;

// get_header();

$cur_user = wp_get_current_user();
$user_id = $cur_user->ID;
$first_name = $cur_user->user_firstname;
$status = indppl_user_status($user_id);

wp_head();?>

<body >
    <div id="dashboard-title-bar" class="indppl-flex indppl-space-between indppl-darkest-grey-bg indppl-align-center">
        <div class="ind-flex indppl-align-center">
            <img src="<?php echo INDPPL_ROOT_URL . '/assets/img/logo-1.png'; ?>" style="max-height: 75px;">
            <h1 class="" style="margin:13px 0 0 15px; padding:15px 7px; font-weight: 800;font-size: 1.25em;"> admin center</h1>
        </div>
        <div class="">
            Welcome <?php echo $first_name; ?> | <a href="<?php echo wp_logout_url("/"); ?>" class="indppl-orange">Logout</a>
        </div>
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