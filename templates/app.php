<?php
//Single App Container Template
//Use this file to collect input from end users on what they'll be planting

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