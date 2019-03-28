<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security
function planting_pal(){
    $return = "<h2 class='indppl-test'>this is a test</h2>";
    return $return;
}
add_shortcode( 'planting-pal', 'planting_pal' );