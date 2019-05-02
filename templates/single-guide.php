<?php defined('ABSPATH') or die('No script kiddies please!'); //For security

wp_head(); ?>

<body <?php body_class('single-guide'); ?>>

    <?php
    $the_id = get_the_ID();
    if (have_posts()) : while (have_posts()) : the_post();
    $the_date = get_the_date();
    if(strtotime($the_date) < strtotime('-30 days')){ ?>
        <div class="container">
            <h1>Expired</h1>
            <p>Sorry, your planting guide has expired. You can create a new one <a href="/app">here</a></p>
        </div>
    <?php } else {
        the_content();
    }
    endwhile; endif;
    wp_footer(); ?>

</body>