<?php defined('ABSPATH') or die('No script kiddies please!'); //For enemies
global $post;
wp_head();?>

<body>
    <div id="dashboard-title-bar">
        <h1 style="color:white;margin:0; padding:15px 7px; font-size: 1.25em; font-weight: 800;">Planting Pal admin center</h1>
    </div>
    <div class="indppl-flex">
        <div id="indppl-dashboard-nav">
            <ul>
                <li <?php if($post->post_name == 'subscription-info') { echo "class='active'";} ?>><a href="/my-account/subscription-info/">Account Profile</a></li>
                <li <?php if ($post->post_name == 'store-profile') {echo "class='active'";}?>><a href="/my-account/store-profile">Manage Stores</a></li>
                <li><a href="#">Manage Users</a> <span class='coming-soon'>coming soon!</span></li>
                <li><a href="#">Billing</a></li>
                <li><a href="#">Reports</a> <span class='coming-soon'>coming soon!</span></li>
                <li><a href="#">Support</a></li>
            </ul>
        </div>
        <div id="dashboard-content" class="main-body">
            <?php 
            if (have_posts()) : while (have_posts()) : the_post();
                the_content();
            endwhile;
            endif; ?> 
        </div>
    </div>
</body>


<?php echo wp_footer(); ?>