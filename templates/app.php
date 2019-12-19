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

    <!-- <div class="container"> -->
        <?php the_content(); ?>
    <!-- </div> -->
    <?php if(wp_is_mobile()){ ?>
        <div class="desktop-link" style="width:100%;text-align:center;">
            <a class='orange-text' href="<?php echo home_url(); ?>?desktop=true">View Desktop Site</a>
        </div>
    <?php } ?>

    <script>
        var isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
        var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        
        if (isSafari && iOS) {
            // alert("You are using Safari on iOS!");
        } else if(isSafari) {
            // alert("You are using Safari.");
        }else if(document.documentMode || /Edge/.test(navigator.userAgent)) {
            // alert('Hello Microsoft User! This site works best in Chrome!');
        }else{
            navigator.permissions.query({name: 'geolocation'}).then(function(status) {
                status.onchange = function(){
                    if(navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            lat = position.coords.latitude;
                            lon = position.coords.longitude;
                            showPosition(lat, lon);
                        },
                        function(error){
                            // console.log(error);
                        });
                    }
                };
                // console.log(status);
            });
        }
    </script>
</body>

<?php include INDPPL_ROOT_PATH . "templates/footer.php"; ?>
<?php wp_footer(); ?>