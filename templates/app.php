<?php
//Single App Container Template
//Use this file to collect input from end users on what they'll be planting
echo apply_filters('fl_theme_viewport', "<meta name='viewport' content='width=device-width, initial-scale=1.0' />\n");
echo apply_filters('fl_theme_xua_compatible', "<meta http-equiv='X-UA-Compatible' content='IE=edge' />\n");
wp_head();

$storeid = get_the_ID(  );
the_post();
?>

<body class="ppl-green-bg">

    <div class="container">
        <?php the_content(); ?>
    </div>

</body>

<?php wp_footer(); ?>