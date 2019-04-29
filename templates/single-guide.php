<?php defined('ABSPATH') or die('No script kiddies please!'); //For security

wp_head(); ?>

<body <?php body_class('single-guide'); ?>>

    <?php
    $the_id = get_the_ID();
    if (have_posts()) : while (have_posts()) : the_post();
    the_content();
    endwhile; endif;
    wp_footer(); ?>

</body>