<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security
/*
Collection of functions for the entire site.
 */

function only_on_home(){
    ?>
        <script>
            var ind_desktop = false;
        </script>
        <?php
    if(isset($_GET['desktop'])){
        if(htmlspecialchars($_GET['desktop'] == true)){
            ?>
            <script>
                var ind_desktop = true;
            </script>
            <?php
        }
    }
    $ind_mobile = 0;
    if(wp_is_mobile()){
        $ind_mobile = 1;
    }
    ?>
    <script>
        var ind_base_url = "<?php echo home_url() . "/"; ?>"; 
        var ind_is_mobile = <?php echo $ind_mobile; ?>;
    </script>
    <?php
}
global $wp;
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
if(home_url() . "/" == $actual_link){
    only_on_home();
}

function geofind($lat, $lon, $radius) {

    $xml = 'http://api.geonames.org/findNearbyPostalCodes?lat=' . $lat . '&lng=' . $lon . '&country=USA&radius=' . $radius . '&username=indelible&maxRows=300';
    // var_dump($xml);
    $xmlfile = file_get_contents($xml);
    $ob = simplexml_load_string($xmlfile);
    $json = json_encode($ob);
    $configData = json_decode($json, true);

    $i = 0;
    foreach ($ob as $taco) {
        $allzips[] = $configData["code"][$i]["postalcode"];
        $i++;
    }
    return $allzips;
} // end Function for Geo

function geozip($zipcode, $radius) {
    $xml = 'http://api.geonames.org/findNearbyPostalCodes?postalcode=' . $zipcode . '&country=USA&radius=' . $radius . '&username=indelible&maxRows=300';

    $xmlfile = file_get_contents($xml);
    $ob = simplexml_load_string($xmlfile);
    $json = json_encode($ob);
    $configData = json_decode($json, true);

    $i = 0;
    foreach ($ob as $taco) {
        $allzips[] = $configData["code"][$i]["postalcode"];
        $i++;
    }
    return $allzips;
} // End Zip Find

function phone_number_format($number) {
    // Allow only Digits, remove all other characters.
    $number = preg_replace("/[^\d]/", "", $number);

    // get number length.
    $length = strlen($number);

    // if number = 10
    if ($length == 10) {
        $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "1-$1-$2-$3", $number);
    }
    return $number;
}

// Generate Unique ID for Results/Guide
function uniqueID() {
    $thedate = date('U') + rand(0, 1000000); // Add some extra flair for seconds in-between
    $thehash = md5($thedate); // Hash the whole thing for a pretty hash.
    return $thehash;
}

function getBagsize($compare, $total) {

    $max = 1.15;

    $i = 0;
    foreach ($compare['id'] as $quick) {
        $size = $compare['size'][$i] . '|' . $compare['units'][$i] . '|' . $compare['prodcups'][$i] . '|' . $compare['id'][$i];
        $check_it[$size] = $compare['percent'][$i];
        $i++;
    }
    ;

    if (count(array_filter($check_it, 'less_than_115')) >= 1) {
        $nacho_me = array_search(getClosest($max, array_filter($check_it, 'less_than_115')), $check_it);
        $nach_ode = explode("|", $nacho_me);
        //echo "<br>Total: ".$total ."Nacho: ".print_r($nach_ode)."<br>";

        if ($nach_ode[1] == 'ea') {
            $qty_product = $total / $nach_ode[0];
        } else {
            $qty_product = $total / $nach_ode[2];
        }
        ;

        if ($qty_product < 1) {
            $final_qty = ceil($qty_product);
        } else {
            $final_qty = round($qty_product);
        }
        $correctsize = $final_qty . "|" . $nach_ode[0] . "|" . $nach_ode[1] . "|" . $nach_ode[3];
        return $correctsize;
    } else {
        $nacho_me = array_search(getClosest($max, array_filter($check_it, 'more_than_115')), $check_it);
        $nach_ode = explode("|", $nacho_me);

        if ($nach_ode[1] == 'ea') {
            $qty_product = $total / $nach_ode[0];
        } else {
            $qty_product = $total / $nach_ode[2];
        }
        ;

        if ($qty_product > 1) {
            $final_qty = ceil($qty_product);
        } else {
            $final_qty = round($qty_product);
        }
        $correctsize = $final_qty . "|" . $nach_ode[0] . "|" . $nach_ode[1] . "|" . $nach_ode[3];
        return $correctsize;
    }
    ;
} // End getBagsize

function getClosest($search, $arr) {
    $closest = null;
    foreach ($arr as $key => $item) {
        if ($closest === null || abs($search - $closest) > abs($item - $search)) {
            $closest = $item;
        }
    }
    return $closest;
}
//echo "<br>Calc for Bag: " . getBagsize("test",$results_submit);

function less_than_115($value) {
    return $value < 1.15;
};

function more_than_115($value) {
    return $value > 1.15;
};

function getProduct($id) {
    global $dbconn;
    $grabSingle = "SELECT * FROM `products` WHERE `id` =" . $id;
    $grabProd = $dbconn->query($grabSingle);
    return $grabProd->fetch_array(MYSQLI_ASSOC);
};

function getProductFam($id) {
    global $dbconn;
    $grabSingle = "SELECT * FROM `products` WHERE `id` =" . $id . " OR `parentid` = " . $id;
    $grabProd = $dbconn->query($grabSingle);
    while ($prodfam = $grabProd->fetch_array(MYSQLI_ASSOC)) {
        $unitid[] = $prodfam;
    }
    return $unitid;
}

function prodUnit($pid, $store) {
    global $dbconn;
    $prodPull = 'SELECT * FROM `products` WHERE `id` = ' . $pid . ' AND `storeid` = ' . $store;
    $grabProd = $dbconn->query($prodPull);
    while ($prodcups = $grabProd->fetch_array(MYSQLI_ASSOC)) {
        $unitid = $prodcups['unit'];
    }
    return $unitid;
}

function productSize($pid, $store) {
    global $dbconn;
    //echo $pid . " - " .$store;
    $psPull = 'SELECT * FROM `products` WHERE `id` = ' . $pid . ' AND `storeid` = ' . $store;
    //echo $psPull;
    $psProd = $dbconn->query($psPull);
    while ($pscups = $psProd->fetch_array(MYSQLI_ASSOC)) {
        $psize = $pscups['size'];
    }
    return $psize;
}

// Checks Range of # if it fits.
function in_range($number, $min, $max, $inclusive = false) {
    if (is_int($number) && is_int($min) && is_int($max)) {
        return $inclusive
        ? ($number >= $min && $number <= $max)
        : ($number > $min && $number < $max);
    }

    return false;
}

// Feed it the width of the box + the product amounts = qty of size to use.
function sizeCheck($width, $ea8, $ea9, $ea24) {
// Convert Strings to INT
    $width = intval($width);
    $ea8 = intval($ea8);
    $ea9 = intval($ea9);
    $ea24 = intval($ea24);

//Check against sizes
    if (in_range($width, 1, 9)) {
        $thissize = $ea8;
        return $thissize;
    }
    if (in_range($width, 8, 24)) {
        $thissize = $ea9;
        return $thissize;
    }
    if (in_range($width, 23, 900)) {
        $thissize = $ea24;
        return $thissize;
    }
}

// Grab Bulk Fillers
function grabBulk($total, $qty, $storeid, $potsize, $type) {
    global $dbconn;
    $grabFillers = "SELECT * FROM `apprates` WHERE `storeid` = " . $storeid . " AND `type` LIKE '" . $type . "' AND `bf` = 1";
    $grabProd = $dbconn->query($grabFillers);
    while ($fillers = $grabProd->fetch_array(MYSQLI_ASSOC)) {
//$pot_fillers[] = $fillers;
        $bf_totals[$fillers['productid']] = $total * $fillers['rate'] * $qty;
    }
    ;
    return $bf_totals;
}; // End Grab Bulk

// Grab Blended Addatives
function grabBA($total, $qty, $storeid, $potsize, $type) {
    global $dbconn;
    $grabBlend = "SELECT * FROM `apprates` WHERE `storeid` = " . $storeid . " AND `type` LIKE '" . $type . "' AND `ba` LIKE '1'";
    $grabProd = $dbconn->query($grabBlend);
    while ($blend = $grabProd->fetch_array(MYSQLI_ASSOC)) {
        $pot_blends[] = $blend;
    }
/*
echo "<br>BA<pre>";
print_r($pot_blends);
echo "</pre>";
 */
// ba - Per cuft
    $pot_count = count($pot_blends);
    $thecount = 1;
    $i = 0;
//echo "PotCOUNT:".$pot_count;
    while ($thecount <= $pot_count) {
        $ba_per = findCups($pot_blends[$i]['rate'], $pot_blends[$i]['unit'], $pot_blends[$i]['rate']) / findCups("1", "cuft", "1");
        $ba_per_raw = $pot_blends[$i]['rate'] / findCups("1", "cuft", "1");
/*
echo "<br>".$thecount ." / ".$pot_count ." / ".$i;
echo "<br><b>[BA]</b> ".getProduct($pot_blends[$i]['productid'])['brand']." ".getProduct($pot_blends[$i]['productid'])['name']." Per cuft: ".$ba_per_raw ."(RAW) ". $ba_per * 100 ."(%)";
print_r(getProduct($pot_blends[$i]['productid']));
 */
        $every_pot[$potsize][$pot_blends[$i]['productid']]['ba_per'] = $ba_per * 100;
        $every_pot[$potsize][$pot_blends[$i]['productid']]['ba_raw'] = $ba_per_raw;
        $every_pot[$potsize][$pot_blends[$i]['productid']]['ba_total'] = $ba_per * $cus_total * $qty;
        $ba_totals[$pot_blends[$i]['productid']] = $ba_per * $total * $qty;
        $thecount++;
        $i++;
    }
    return $ba_totals;
}

// Grab Blended Addatives
function grabSA($total, $qty, $storeid, $potsize, $type) {
    global $dbconn;
    $grabBlend = "SELECT * FROM `apprates` WHERE `storeid` = " . $storeid . " AND `type` LIKE '" . $type . "' AND `sa` LIKE '1'";
    $grabProd = $dbconn->query($grabBlend);
    while ($blend = $grabProd->fetch_array(MYSQLI_ASSOC)) {
        $pot_blends[] = $blend;
    }
/*
echo "<br>SA<pre>";
print_r($pot_blends);
echo "</pre>";
 */
// sa - Per cuft
    $pot_count = count($pot_blends);
    $thecount = 1;
    $i = 0;
//echo "PotCOUNT:".$pot_count;
    while ($thecount <= $pot_count) {
        $sa_per = findCups($pot_blends[$i]['rate'], $pot_blends[$i]['unit'], $pot_blends[$i]['rate']) / findCups("1", "cuft", "1");
        $sa_per_raw = $pot_blends[$i]['rate'] / findCups("1", "cuft", "1");
/*
echo "<br>".$thecount ." / ".$pot_count ." / ".$i;
echo "<br><b>[SA]</b> ".getProduct($pot_blends[$i]['productid'])['brand']." ".getProduct($pot_blends[$i]['productid'])['name']." Per cuft: ".$sa_per_raw ."(RAW) ". $sa_per * 100 ."(%)";
print_r(getProduct($pot_blends[$i]['productid']));
 */
        $every_pot[$potsize][$pot_blends[$i]['productid']]['sa_per'] = $sa_per * 100;
        $every_pot[$potsize][$pot_blends[$i]['productid']]['sa_raw'] = $sa_per_raw;
        $every_pot[$potsize][$pot_blends[$i]['productid']]['sa_total'] = $sa_per * $cus_total * $qty;
        $sa_totals[$pot_blends[$i]['productid']] = $sa_per * $total * $qty;
        $thecount++;
        $i++;
    }
    return $sa_totals;
}

// Grab Eaches
function grabEA($total, $qty, $storeid, $potsize, $type) {
    global $dbconn;
    $grabBlend = "SELECT * FROM `apprates` WHERE `storeid` = " . $storeid . " AND `type` LIKE '" . $type . "' AND `ea` LIKE '1'";
    $grabProd = $dbconn->query($grabBlend);
    while ($blend = $grabProd->fetch_array(MYSQLI_ASSOC)) {
        $pot_blends[] = $blend;
    }
/*
echo "<br>EA<pre>";
print_r($pot_blends);
echo "</pre>";
 */
// ea - Per cuft
    $pot_count = count($pot_blends);
    $thecount = 1;
    $i = 0;
//echo "PotCOUNT:".$pot_count;
    while ($thecount <= $pot_count) {
        $ea_per = findCups($pot_blends[$i]['rate'], $pot_blends[$i]['unit'], $pot_blends[$i]['rate']) / findCups("1", "cuft", "1");
        $ea_per_raw = $pot_blends[$i]['rate'] / findCups("1", "cuft", "1");
/*
echo "<br>".$thecount ." / ".$pot_count ." / ".$i;
echo "<br><b>[EA]</b> ".getProduct($pot_blends[$i]['productid'])['brand']." ".getProduct($pot_blends[$i]['productid'])['name']." Per cuft: ".$ea_per_raw ."(RAW) ". $ea_per * 100 ."(%)";
print_r(getProduct($pot_blends[$i]['productid']));
 */
        $every_pot[$potsize][$pot_blends[$i]['productid']]['ea_per'] = $ea_per * 100;
        $every_pot[$potsize][$pot_blends[$i]['productid']]['ea_raw'] = $ea_per_raw;
        $every_pot[$potsize][$pot_blends[$i]['productid']]['ea_total'] = $ea_per * $cus_total * $qty;
        $ea_totals[$pot_blends[$i]['productid']] = $ea_per * $total * $qty;
        $thecount++;
        $i++;
    }
    return $ea_totals;
}

function indppl_user_status($id = null){
    if($id == null){
        $id = get_current_user_id();
    }
    $meta = get_user_meta($id, 'wpnr_capabilities', true);
    $account_array = array();
    if(isset($meta['paidaccountpro'])){
        array_push($account_array, 'paidaccountpro');
    }
    if(isset($meta['paidaccount'])){
        array_push($account_array, 'paidaccount');
    }
    if(isset($meta['freeaccount'])){
        array_push($account_array, 'freeaccount');
    }
    return $account_array;
}

function indppl_apprates($store_id, $type = null, $args = null, $bag = null) {

    // Start with the apprates from the store meta. Does it have data? If no, start fresh.
    $meta = get_post_meta($store_id, 'wpcf-apprates', true);
    
    // Create an array if no data exists.
    if ($meta == '' || $meta == null) {
        $apprates = array('ground' => array(), 'pots' => array(), 'beds' => array());
    } else {
        $apprates = json_decode($meta, true);
    }

    if($args){ // We want to make an update to the apprates
        
        switch ($type) { // Which type of product is getting added?
    
        case 'ground':
            if($bag){
                foreach($args as $key => $val) {
                    foreach($val as $k => $v){
                        if(is_array($v)){
                            foreach($v as $item => $value){
                                $apprates[$type][$key][$k][$item] = $value;
                            }
                        }else{
                            $apprates[$type][$key][$k][key($v)] = $v[key($v)];
                        }
                    }
                }
            }else{
                $apprates['ground'][key($args)] = $args[key($args)];
            }

            break;
        case 'pots':
        case 'beds':
        foreach($args as $key => $val) {
            foreach($val as $k => $v){
                // var_dump($v);
                    if(is_array($v)){
                        foreach($v as $item => $value){
                            $apprates[$type][$key][$k][$item] = $value;
                        }
                    }else{
                        $apprates[$type][$key][$k][key($v)] = $v[key($v)];
                    }
                }
            }
            // var_dump($apprates);

            break;
        default:
            return 'Something wrong...';
            break;
        }
    
        $apprates = json_encode($apprates);
    
        $update = update_post_meta($store_id, 'wpcf-apprates', $apprates);

        $results = array( 'apprates' => $apprates, 'update' => $update);


    } else {

        $results = $apprates;

    }

    return $results;

    // var_dump($result);

}

function indppl_delete_apprate($store_id, $args = null) {
    
    $apprates = array();
    
    // If no arguments are given, wipe them all out
    if(!$args){
        $apprates = json_encode(array('ground' => array(), 'pots' => array(), 'beds' => array()));
        $update = update_post_meta($store_id, 'wpcf-apprates', $apprates);

        $results = array('apprates' => $apprates, 'update' => $update);

    } else {
        
        $apprates = json_decode( get_post_meta( $store_id, 'wpcf-apprates', true), true);
        $results = $apprates;
        if(is_array($args)){
            // We have many items to remove
            foreach($args as $k => $v) {
                if($k == 'ground'){
                    unset($apprates[$k][$v]);
                }
                $fill_type = array('filler', 'blended', 'surface', 'each');
                if($k == 'pots'){
                    foreach($fill_type as $val){
                        unset($apprates[$k][$val][$v]);

                    }
                }
                if($k == 'beds'){
                    foreach($fill_type as $val){
                        unset($apprates[$k][$val][$v]);

                    }
                }
            }
            $newapprates = json_encode($apprates);
            // var_dump($newapprates);
            $update = update_post_meta($store_id, 'wpcf-apprates', $newapprates);
            $results = array('apprates' => $newapprates, 'update' => $update);

        } else {
            // We have just one item to remove
            // Version 1.1
        }

    }

    return $results;
}

function dummy_data() {
    // return '<h1>sdfsdf</h1>';
    $args = array(

        // GROUND
        // 17288 => array( 
        //     'measurement' => 'other',
        //     'containers' => array(

        //         218 => array(
        //             'unit'   => 'cuft',
        //             'amount' => 3,
        //         ),
        //         216 => array(
        //             'unit'   => 'cup',
        //             'amount' => 100,
        //         ),
        //     ),
        // ),

        // POTS OR RAISED BEDS
        'filler' => array(
            172087 => array(
                'primary' => true,
                'amount' => 0.15,
            ),
        ),

        'blended' => array(
            17430 => array(
                'unit'  => 'cups',
                'amount' => 13,
                'per-sqft' => 1, //could be 1, 10, or 100 
            ),
        ),

        'each' => array( // pots only
            17670 => array(
                'small' => 2,
                'medium' => 5,
                'large' => 8,
            ),
        ),

    );

    $test = indppl_apprates(252, 'pots', $args);
    // $test = indppl_apprates(252);
    
    // var_dump($test);
}

// add_shortcode('indppl-test', 'dummy_data');

function indppl_store_info($store_id = NULL){
	
    $store_name = '';
    $address1 = '';
    $address2 = '';
    $city = '';
    $state = '';
    $zip = '';
    $weburl = '';
    $phone = '';
    $email = '';
    $logo = '';
    
	if(is_int($store_id)){
		$store_name = get_the_title($store_id);
		$address1 = get_post_meta($store_id, 'wpcf-address1', true);
		$address2 = get_post_meta($store_id, 'wpcf-address2', true);
		$city = get_post_meta($store_id, 'wpcf-city', true);
		$state = get_post_meta($store_id, 'wpcf-state', true);
		$zip = get_post_meta($store_id, 'wpcf-zip', true);
		$weburl = get_post_meta($store_id, 'wpcf-weburl', true);
		$phone = get_post_meta($store_id, 'wpcf-phone', true);
        $email = get_post_meta($store_id, 'wpcf-email', true);
		$logo = get_post_meta($store_id, 'wpcf-logo', true);
    }
    // var_dump($logo);
    // wp_handle_upload( $file, $overrides, $time );
    $top_url = "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $top_url = explode('?new=true', $top_url);
    ob_start();
    ?>
    <?php
	if(is_int($store_id)){ ?>
		<h3>Edit Store Information</h3>
	<?php }else{ ?>
    	<h3>Welcome to Planting Pal!</h3>
    	<p>We just need to get a few quick details to configure your store then you can begin building out your products and rates.</p>
	<?php } ?>
		<!-- <form method="post" action='#' id='store-management-form' class="form-horizontal"> -->
    <form  method="post" action='<?php echo $top_url[0]; ?>' id='store-management-form' class="form-horizontal" enctype="multipart/form-data">
		<fieldset>
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="store-name">Store Name</label>
			<div class="col-md-4">
			<input id="store-name" name="store-name" type="text" placeholder="" class="form-control input-md" required="" value="<?php echo $store_name; ?>">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="address1">Address Line 1</label>
			<div class="col-md-4">
			<input id="address1" required name="address1" type="text" placeholder="" class="form-control input-md" required="" value="<?php echo $address1; ?>">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="address2">Address Line 2</label>
			<div class="col-md-4">
			<input id="address2" name="address2" type="text" placeholder="" class="form-control input-md" value="<?php echo $address2; ?>">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="city">City</label>
			<div class="col-md-4">
			<input id="city" name="city" type="text" placeholder="" class="form-control input-md" required="" value="<?php echo $city; ?>">
                
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="state">State</label>
			<div class="state-selector">
			<select id="state" name="state" type="text" placeholder="" class="form-control input-md" required="" value="<?php echo $state; ?>">
                <?php
                    $state_array = array('AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY');
                    foreach($state_array as $value){
                        if($value == $state){
                            $select = 'selected="selected"';
                        }else{
                            $select = '';
                        }
                        ?>
                        <option <?php echo $select; ?> value="<?php echo $value; ?>"><?php echo $value; ?></option>
                        <?php
                    }
                ?>
            </select>
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="zip">Zipcode</label>
			<div class="col-md-2">
			<input id="zip" name="zip" type="text" placeholder="" class="form-control input-md" required="" value="<?php echo $zip; ?>">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="weburl">Store Website</label>
			<div class="col-md-4">
			<input id="weburl" name="weburl" type="text" placeholder="" class="form-control input-md" value="<?php echo $weburl; ?>">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="phone">Phone Number</label>
			<div class="col-md-4">
			<input id="phone" name="phone" type="text" placeholder="" class="form-control input-md" required="" value="<?php echo $phone; ?>">
			
			</div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			<label class="col-md-4 control-label" for="store-email">Email Address</label>
			<div class="col-md-4">
			<input id="store-email" name="store-email" type="text" placeholder="" class="form-control input-md" required="" value="<?php echo $email; ?>">
			
			</div>
			</div>
		
			<div class="form-group">
			<label class="col-md-4 control-label" for="logo">Store Logo</label>
			<div class="col-md-4">
				<div class="store-edit-logo-container">
					<?php if($logo){ ?>
						<div class='current-store-logo'><img src='<?php echo $logo; ?>'></div>
                            <div>
                                <!-- <a href="#" class='edit-logo-btn'>Change Logo</a>
                                <div class='edit-store-logo'> -->
                                    <label for=b1>
                                        Change Logo
                                        <input type="file" name="my_file_upload[]" data-buttonText="Change Logo" onchange='optionalExtraProcessing(b1.files[0])'>
                                    </label>
                                <!-- </div> -->
                            </div>
						
                        <?php }else{ ?>
                            <input type="file" name="my_file_upload[]">
                        <?php } ?>

                    </div>
                </div>
			</div>
            <?php 
            $user_id = get_current_user_id();
            $args = array(
                'author' => $user_id,
                'post_type' => 'store',
                'orderby' => 'post-date',
            );
            $stores = new WP_Query($args);
            $status = indppl_user_status($user_id);
            global $wp;
            $curernt_url =  home_url( $wp->request );
            if($stores->have_posts()){ ?>
                <!-- <div class="form-group">
                    <div class=" indppl-flex indppl-no-wrap" style="max-width: 600px; margin:auto;align-items:center;">
                        <input id="billing" name="billing" type="checkbox" class="form-control input-md" style="height:auto; width: auto;" required> 
                        <p style="margin-bottom: 0; margin-left:10px;">I understand that I will be billed an additional subscription.</p>
                        
                    
                    </div>
                </div> -->
            <?php } ?>

            <input type='hidden' id='store-id' name='store-id' value='<?php echo $store_id; ?>'>
            <?php
			// }
			?>
			<!-- Button -->
			<div class="form-group">
			<label class="col-md-4 control-label" for="submit"></label>
			<div class="col-md-4">
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>
			</div>
			</div>
		</fieldset>
    </form>

    <?php

    $return = ob_get_clean();
    return $return;
}

function indppl_save_post($store_id = 0){
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        
        $user_id = get_current_user_id();

        $store = array();
        $files = $_FILES["my_file_upload"];
        // var_dump($files);
        // foreach ($files['name'] as $key => $value) {
        if ($files['name'][0]) {
            $file = array(
                'name' => $files['name'][0],
                'type' => $files['type'][0],
                'tmp_name' => $files['tmp_name'][0],
                'error' => $files['error'][0],
                'size' => $files['size'][0]
            );
            $_FILES = array("upload_file" => $file);
            $attachment_id = media_handle_upload("upload_file", 0);
            // var_dump('lskdjf   :   ');
            // var_dump(wp_get_attachment_image_src($attachment_id));
            if (is_wp_error($attachment_id)) {
                // There was an error uploading the image.
                echo "Error adding file";
            }
        }
        // }
        $store = array(
            'ID' => $store_id,
            'post_title' => wp_strip_all_tags($_POST['store-name']),
            'post_author' => $user_id,
            'post_type' => 'store',
            'post_status' => "publish",
            'meta_input' => array(
                'wpcf-address1' => $_POST['address1'],
                'wpcf-address2' => $_POST['address2'],
                'wpcf-city' => $_POST['city'],
                'wpcf-state' => $_POST['state'],
                'wpcf-zip' => $_POST['zip'],
                'wpcf-phone' => $_POST['phone'],
                'wpcf-email' => $_POST['store-email'],
                
                'wpcf-weburl' => $_POST['weburl'],
            ),
        );
        if(isset(wp_get_attachment_image_src($attachment_id)[0])){
            $store['meta_input']['wpcf-logo'] = wp_get_attachment_image_src($attachment_id)[0];
        }
        $store_id = wp_insert_post($store);
        
        $bill = htmlspecialchars($_POST['billing']);
        if($bill == 'on'){
            $email = indppl_notify_new_store($store_id, $user_id);
        }

        return $store_id;
        
    }
}


function indppl_create_container($new_array, $container_id = 0){
    $container = array(
        'ID' => $container_id,
        'post_title' => $new_array['name'],
        'post_author' => get_current_user_id(),
        'post_type' => 'container',
        'post_status' => 'publish',
    );
    $return_id = wp_insert_post($container);
    return $return_id;
}

function indppl_create_package($new_array, $package_id = 0){
    $package = array(
        'ID' => $package_id,
        'post_title' => $new_array['name'],
        'post_author' => get_current_user_id(),
        'meta_input' => array(
            'wpcf-size' => $new_array['size'],
            'wpcf-unit' => $new_array['unit'],
        ),
        'post_type' => 'package',
        'post_status' => 'publish',
    );
    $return_id = wp_insert_post($package);
    return $return_id;
}

function indppl_build_container_relation_output($id, $title, $relation_array, $int_array, $meta){
    ob_start();
    // old check mark
    // M14.1 25.2l7.1 7.2 16.7-16.8
    $check_box = '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path></svg>';
    $check_mark = '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path><path class="checkmark__check" fill="green" d="M15 12 L12 15 L20 22 L37 2 L20 17 L15 12"></path></svg>';
    $available = '<div class="indppl-dot-container"><svg height="24" width="24">
        <circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/>
        <circle cx="12" cy="12" r="6" stroke="#1ab1ec" stroke-width="2" fill="#1ab1ec" fill-opacity="0.6"/>
        Sorry, your browser does not support inline SVG. 
    </svg></div>';
    $not_available = '<div class="indppl-no-dot-container"><svg height="24" width="24">
        <circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.
        </svg></div>';
    
    ?>
    <tr class='indppl-table-color-offset'>
        <td class='padding-left-40 position-absolute check-box-container'><?php
        $fix_relative_issue = '';
            if(in_array($id, $relation_array)){
                ?>
                <div class='container-available indppl-checked'>
                    <input type="checkbox" id="<?php echo $id; ?>-container-available" class="display-none" data-container="<?php echo $id; ?>" name="<?php echo $id; ?>-container-available" checked>
                    <label class="margin-0 container-available-check" for="<?php echo $id; ?>-container-available"><div class="container-available-in-store"><?php echo $check_mark; ?></div></label>
                </div>
                <?php
                $fix_relative_issue = 'container-title-fix';
            }else{
                ?>
                <div class='container-available'>
                    <input type="checkbox" id="<?php echo $id; ?>-container-available" class="display-none" data-container="<?php echo $id; ?>" name="<?php echo $id; ?>-container-available">
                    <label class="margin-0 container-available-check" for="<?php echo $id; ?>-container-available"><div class="container-not-available-in-store"><?php echo $check_box; ?></div></label>
                </div>
                <?php
                $fix_relative_issue = 'container-title-fix';
            }
            if($meta){
                echo '<p class="' . $fix_relative_issue . ' container-title">' . $title . '</p>';
                $defualt_or_not_class = 'indppl-default-container';
            }else{
                ?>
                <input type='text' class='container-title indppl-container-edit-title' name='indppl-container-title' value='<?php echo $title; ?>'>
                <?php
                $defualt_or_not_class = 'indppl-non-default-container';
            }
        ?></td>
        <td>
            <?php
            if(is_array($int_array)){
                if(in_array($id, $relation_array) && array_key_exists('wpcf-available-in-spring', $int_array)){
                    echo '<input type="checkbox" name="' . $id . '-' . 'spring" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'spring" checked /><label class="margin-0" for="' . $id . '-' . 'spring">' . $available . '</label>';
                }else{
                    echo '<input type="checkbox" name="' . $id . '-' . 'spring" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'spring"/><label class="margin-0" for="' . $id . '-' . 'spring">' . $not_available . '</label>';
                }
            }else{
                echo '<input type="checkbox" name="' . $id . '-' . 'spring" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'spring"/><label class="margin-0" for="' . $id . '-' . 'spring">' . $not_available . '</label>';
            }
            ?>
        </td>
        <td>
            <?php
            if(is_array($int_array)){
                if(in_array($id, $relation_array) && array_key_exists('wpcf-available-in-summer', $int_array)){
                    echo '<input type="checkbox" name="' . $id . '-' . 'summer" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'summer" checked /><label class="margin-0" for="' . $id . '-' . 'summer">' . $available . '</label>';
                }else{
                    echo '<input type="checkbox" name="' . $id . '-' . 'summer" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'summer"/><label class="margin-0" for="' . $id . '-' . 'summer">' . $not_available . '</label>';
                }
            }else{
                echo '<input type="checkbox" name="' . $id . '-' . 'summer" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'summer"/><label class="margin-0" for="' . $id . '-' . 'summer">' . $not_available . '</label>';
            }
            ?>
        </td>
        <td>
            <?php
            if(is_array($int_array)){
                if(in_array($id, $relation_array) && array_key_exists('wpcf-available-in-fall', $int_array)){
                    echo '<input type="checkbox" name="' . $id . '-' . 'fall" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'fall" checked /><label class="margin-0" for="' . $id . '-' . 'fall">' . $available . '</label>';
                }else{
                    echo '<input type="checkbox" name="' . $id . '-' . 'fall" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'fall"/><label class="margin-0" for="' . $id . '-' . 'fall">' . $not_available . '</label>';
                }
            }else{
                echo '<input type="checkbox" name="' . $id . '-' . 'fall" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'fall"/><label class="margin-0" for="' . $id . '-' . 'fall">' . $not_available . '</label>';
            }
            ?>
        </td>
        <td>
            <?php
            if(is_array($int_array)){
                if(in_array($id, $relation_array) && array_key_exists('wpcf-available-in-winter', $int_array)){
                    echo '<input type="checkbox" name="' . $id . '-' . 'winter" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'winter" checked /><label class="margin-0" for="' . $id . '-' . 'winter">' . $available . '</label>';
                }else{
                    echo '<input type="checkbox" name="' . $id . '-' . 'winter" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'winter"/><label class="margin-0" for="' . $id . '-' . 'winter">' . $not_available . '</label>';
                }
            }else{
                echo '<input type="checkbox" name="' . $id . '-' . 'winter" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'winter"/><label class="margin-0" for="' . $id . '-' . 'winter">' . $not_available . '</label>';
            }
            ?>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}

function indppl_add_relation($default, $store_container_relations){
    // specific function not for wide use
    foreach ($default as $key => $value) {
        $name = explode("-", $value['name']);
        $id = $name[0];
        $season ='';
        if($name[1] == 'spring'){
            $season = 'wpcf-available-in-spring';
        }
        else if ($name[1] == 'summer'){
            $season = 'wpcf-available-in-summer';
        }
        else if ($name[1] == 'fall'){
            $season = 'wpcf-available-in-fall';
        }
        else if ($name[1] == 'winter'){
            $season = 'wpcf-available-in-winter';
        }
        foreach($store_container_relations as $key2 => $rel_val){
            $container = get_post($rel_val);
            $name = $container->post_name;
            $name_array = explode('-', $name);
            $cont_id = $name_array[count($name_array)-1];
            if($id == $cont_id){
                $test = update_post_meta((int)$rel_val, (string)$season, "1");
            }
        } 
    }
}

function indppl_get_current_products($type){
    $id = get_current_user_id();
    if(isset($_GET['store-id'])){
        $store_id = $_GET['store-id'];
    }else if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    // Build array based on current user AND any authorized user
    $authors = array($id);
    $auth = indppl_get_dup_auth($store_id);
    if(count($auth) > 0){
        foreach($auth as $a){
            $a_user = get_user_by('email', $a['user_email']);
            if($a_user){
                $authors[] = $a_user->ID;
            }
        }
    }
    $args = array(
        'post_type' => 'product',
        'relation' => 'OR',
        array(
            'author__in' => $authors,
            'meta_query' => array(
                array(
                    'key' => 'wpcf-default',
                    'value' => 1,
                    'compare' => '=',
                ),
            ),
        ),
    );

    $products = new WP_Query($args);
    ob_start();
    $app_rates = indppl_apprates($store_id);
    ?>
    <table>
        <th class='product-list-width'></th>
        <th class='product-list-width'>Brand</th>
        <th class='product-list-width'>Product Name</th>
        <th class='product-list-width'>Sizes</th>

        <?php
        $product_array = $app_rates[$type];
        $no_duplicates = array();
        if(is_array($product_array)){
            foreach($product_array as $key => $value){
                if($type == 'pots' || $type == 'beds'){
                    foreach($value as $k => $v){
                        if($k != 0 && !in_array($k, $no_duplicates)){
                            $no_duplicates[] = $k;
                            indppl_get_products($store_id, $k, $type);
                        }
                    }
                }else{
                    if($key != 0){
                        indppl_get_products($store_id, $key, 'ground');
                    }
                }
            }
        }
    ?>
    </table>
    <?php
    $return = ob_get_clean();
    return $return;
}

function update_package_table($store_id, $product_id, $type){
    // app rates chart container
    $app_rates = indppl_apprates($store_id);
    // var_dump($app_rates);
    ob_start();
    $containers = toolset_get_related_posts(
        $store_id, // get posts related to this one
        'store-container', // relationship between the posts
        'parent',
        '100',
        '0',
        array(),
        'post_id',
        'child'
    );
    $product_related = toolset_get_related_posts(
       $product_id,
       'product-package',
       'parent',
       '100',
       '0',
       array(),
       'post_id',
       'child'
   );
   $store_related = toolset_get_related_posts(
       $store_id,
       'store-package',
       'parent',
       '100',
       '0',
       array(),
       'post_id',
       'child'
   );
    $test = array('parent' => array($product_id), 'child' => $containers);
    $role = array('role_to_return' => 'all');
    $pro_container = toolset_get_related_posts(
        $test,
        'default-apprate',
        ['role_to_return' => 'all']
    );

    if($type == 'ground'){
        $header = 'In-Ground';
    }
    $img = get_post_meta($product_id, 'wpcf-product-image', true);
    if(!$img){
        $img =  home_url() . "/wp-content/uploads/2019/03/big-carrot.png";
    }
    ?>
    <div class='product-create-chart-header-container'>
        <h3><?php echo $header; ?> Planting Application Rates For:</h3>
        <div class='product-create-chart-title-container'>
            <img class='indppl-product-create-img' src="<?php echo $img; ?>">
            <div style='margin-left: 10px'>
                <p class='indppl-product-create-chart-brand'><?php echo get_the_terms($product_id, 'brand')[0]->name; ?></p>
                <h4 class='indppl-product-create-chart-product'><?php echo get_the_title($product_id); ?></h4>
            </div>
        </div>
    </div>
    <table class='product-create-chart-table'>
    <tr>
        <th colspan='2'>Choose Application Rates</th>
            <th colspan='5'>
            <p>Application Rates Automatically Calculated for Other Sizes</p>
            <p>Use the numbers below to fine tune your application rate on the left</p>
        </th>
    </tr>
    <tr>
        <th colspan='2'></th>
        <?php

        foreach($product_related as $key => $value){
            if(in_array($value, $store_related)){
                $name = get_post_meta($value, 'wpcf-unit', true);
                if($name == 'qt-d' || $name == 'qt-l'){
                    $name = 'Quart';
                }
                ?>
                <th colspan='1'><?php echo get_post_meta($value, 'wpcf-size', true) . " " . $name; ?></th>
                <?php
            }
        }
        ?>
    </tr>
    <?php
    $args = array(
        'numberposts' => -1,
        'post_type' => 'container',
    );

    $def_containers = get_posts($args);
    // $console = $pro_container;
    // var_dump($containers); used for sorting
    $key = 0;
    foreach($def_containers as $dcon){
        // echo 'inside';
        if(in_array($dcon->ID, $containers)){
        $id = $dcon->ID;
        // echo 'inside';
        $title = get_the_title($id);
        $pack_id = $store_related[$key];
        $package = get_post_meta($pack_id, 'wpcf-unit', true);
        $default = get_post_meta($id, 'wpcf-default-container', true);
        ?>
        <tr>
            <td>
                <?php echo $title; ?>
            </td>
            <td>
                <?php
                foreach($pro_container as $k => $v){
                    // echo 'inside-foreach';
                    if($id == $v['child']){
                        
                        // echo $v['intermediary'];
                        // $app_qty_array[$k] = get_post_meta($v['intermediary']);
                        if(array_key_exists('amount', $app_rates[$type][$product_id]['containers'][$id])){
                            $app_qty = $app_rates[$type][$product_id]['containers'][$id]['amount'];
                        }else{
                            $app_qty = get_post_meta($v['intermediary'], 'wpcf-apprate-qty', true);
                        }
                        
                        if($app_qty){
                            ?>
                            <input type='text' class='some-kind-of-wonderful indppl-product-create-chart-app-rate-num' name=<?php echo $id; ?> value=<?php echo $app_qty; ?> >
                            <?php
                        }else{
                            ?>
                            <input type='text' class='some-kind-of-wonderful indppl-product-create-chart-app-rate-num' name=<?php echo $id; ?> value=0 >
                            <?php
                        }
                        echo ' ';
                        
                        if(!empty($app_rates[$type][$product_id]['containers'][$id]['unit'])){
                            $app_unit = $app_rates[$type][$product_id]['containers'][$id]['unit'];
                        }else{
                            $app_unit = get_post_meta($v['intermediary'], 'wpcf-apprate-unit-holdover', true);
                        }
                        ?>
                        <select class='some-kind-of-wonderful indppl-product-create-chart-app-unit' name=<?php echo $id; ?> data-unit=<?php echo $app_unit; ?>>
                            
                        </select>
                        <?php
                    }
                }
                // var_dump($pro_container);
                if(!$default || empty($pro_container)){
                    // echo 'no foreach';
                    $app_qty = 0;
                    $wet_dry = get_post_meta($product_id, 'wpcf-dryliquid', true);
                    if($wet_dry = 'dry'){
                        $app_unit = 'lb';
                    }else{
                        $app_unit = 'gal';
                    }
                    if(array_key_exists('amount', $app_rates[$type][$product_id]['containers'][$id])){
                        $app_qty = $app_rates[$type][$product_id]['containers'][$id]['amount'];
                    }
                    if(!empty($app_rates[$type][$product_id]['containers'][$id]['unit'])){
                        $app_unit = $app_rates[$type][$product_id]['containers'][$id]['unit'];
                    }
                    
                    ?>
                    <input type='text' class='some-kind-of-wonderful indppl-product-create-chart-app-rate-num' name=<?php echo $id; ?> value=<?php echo $app_qty; ?>>
                    <select class='some-kind-of-wonderful indppl-product-create-chart-app-unit' name=<?php echo $id; ?> data-unit=<?php echo $app_unit; ?>>
                        
                    </select>
                    <?php
                    
                }
                ?>
            </td>
            <?php
            $items = array(
                array(
                    'unit' => $app_unit,
                    'amount' => $app_qty,
                )
            );
            foreach($product_related as $k => $val){
                if(in_array($val, $store_related)){
                    
                    $package_size = get_post_meta($val, 'wpcf-size', true);
                    $package_unit = get_post_meta($val, 'wpcf-unit', true);
                    $cups = get_post_meta($product_id, 'wpcf-5cups', true);
                    
                    // var_dump($cups);
                    // echo $cups;
                    
                    $conversion = indppl_normalize($items, $package_unit, $cups);
                    // var_dump($conversion);
                    // $conversion = getVolume($app_qty, $app_unit, $package_unit);
                    if($conversion[0]['standard-amount'] == 0){
                        $final = 0;
                    }else{
                        $final = $package_size / $conversion[0]['standard-amount'];
                    }
                    // echo $;
                    
                    ?>
                    <td><?php echo round($final, 2) . " Plants ";  ?></td>
                    <?php
                }
            }

            ?>
        </tr>
        <?php
        $key++;
        }
    }
    
    ?>

    </table>
    <div class="product-create-submit-container">
        <input type="submit" name="product-create-submit-back" class='product-create-submit-back' value="Back"/>
        <input type="submit" name="product-create-submit-exit" data-exit="true" id="product-create-submit-exit" class="product-create-submit" value="Save and Exit"/>
        <input type="submit" name="product-create-submit" id="product-create-submit" class="product-create-submit" value="Save and add another product"/>
        <input type="submit" name="product-create-exit" id="product-create-exit" class="product-create-exit" value="Exit"/>
    </div>
    <?php
    $app_rates_chart = ob_get_clean();

    // $send_array['app_rates_chart'] = $app_rates_chart;
    return $app_rates_chart;
}

function update_bag_package_table($store_id, $product_id, $type){
    // app rates chart container
    $app_rates = indppl_apprates($store_id);
    ob_start();
    // var_dump($app_rates);
    $containers = toolset_get_related_posts(
        $store_id, // get posts related to this one
        'store-container', // relationship between the posts
        'parent',
        '100',
        '0',
        array(
            'orderby' => 'title',
            'order' => 'ASC',
        ),
        'post_id',
        'child'
    );
    $product_related = toolset_get_related_posts(
       $product_id,
       'product-package',
       'parent',
       '100',
       '0',
       array(),
       'post_id',
       'child'
    );
    $store_related = toolset_get_related_posts(
        $store_id,
        'store-package',
        'parent',
        '100',
        '0',
        array(),
        'post_id',
        'child'
    );
    $test = array('parent' => array($product_id), 'child' => $containers);
    $role = array('role_to_return' => 'all');
    $pro_container = toolset_get_related_posts(
        $test,
        'default-apprate',
        ['role_to_return' => 'all']
    );
    // var_dump($pro_container);

    if($type == 'ground'){
        $header = 'In-Ground';
    }
    $img = get_post_meta($product_id, 'wpcf-product-image', true);
    if(!$img){
        $img =  home_url() . "/wp-content/uploads/2019/03/big-carrot.png";
    }
    ?>
    <div class='product-create-chart-header-container'>
        <h3><?php echo $header; ?> Planting Application Rates For:</h3>
        <div class='product-create-chart-title-container'>
            <img class='indppl-product-create-img' src="<?php echo $img; ?>">
            <div style='margin-left: 10px'>
                <p class='indppl-product-create-chart-brand'><?php echo get_the_terms($product_id, 'brand')[0]->name; ?></p>
                <h4 class='indppl-product-create-chart-product'><?php echo get_the_title($product_id); ?></h4>
            </div>
        </div>
    </div>
    <table class='product-create-chart-table'>
    <tr>
        <th colspan='2'>Choose Application Rates</th>
        <?php if(count($product_related) > 1){
            ?>
            <th colspan='5'>
            <p>Application Rates Automatically Calculated for Other Sizes</p>
            <p>Use the numbers below to fine tune your application rate on the left</p>
        <?php } ?>
        </th>
    </tr>
    <tr>
        <th colspan='1'></th>
        <!-- <th colspan='1'>Largest Product</th> -->
        <?php
        $order_array = array();

        foreach($product_related as $key => $value){
            if(in_array($value, $store_related)){
                // var_dump($value);
                if(array_key_exists(get_post_meta($value, 'wpcf-size', true), $order_array)){
                    $order_array[get_post_meta($value, 'wpcf-size', true)+1] = $value;
                }else{
                    $order_array[get_post_meta($value, 'wpcf-size', true)] = $value;
                }
                
            }
        }
        krsort($order_array);
        foreach($order_array as $key => $value){
            $name = get_post_meta($value, 'wpcf-unit', true);
            if($name == 'qt-d' || $name == 'qt-l'){
                $name = 'Quart';
            }
            ?>
            
            <th class='bag-apprates-title' data-num='<?php echo get_post_meta($value, 'wpcf-size', true); ?>' data-unit='<?php echo get_post_meta($value, 'wpcf-unit', true); ?>' colspan='1'><?php echo get_post_meta($value, 'wpcf-size', true) . " " . $name; ?></th>
            <?php
        }
        ?>
    </tr>
    <?php
    // $console = $pro_container;
    // var_dump($containers); used for sorting
    // var_dump($containers);
    // var_dump("<br /><br />");
    // var_dump($store_related);
    // var_dump("<br /><br />");
    // var_dump($product_related);
    // var_dump("<br /><br />");
    // var_dump($order_array);
    // var_dump("<br /><br />");
    // var_dump($pro_container);
    $args = array(
        'numberposts' => -1,
        'post_type' => 'container',
    );

    $def_containers = get_posts($args);

    $first_key = key($order_array);
    $non_default_app_rate = "";
    $volume_units = indppl_get_units('volume');
    $mass_units = indppl_get_units('mass');
    $key = 0;
    foreach($def_containers as $dcon){
        // echo 'inside';
        if(in_array($dcon->ID, $containers)){
        $id = $dcon->ID;
        
        $title = get_the_title($id);
        $pack_id = $store_related[$key];
        $package = get_post_meta($pack_id, 'wpcf-unit', true);
        $default = get_post_meta($id, 'wpcf-default-container', true);
        // $app_qty_array = [];
        ?>
        <tr>
            <td class='bag-apprates-container-title' data-id='<?php echo $id; ?>'>
                <?php echo $title; ?>
            </td>
                <?php
                
                foreach($order_array as $knife => $pack_id){
                    ?>
                    <td>
                    <?php
                    foreach($pro_container as $k => $v){
                        $items = array(
                            array(
                                'unit' => $app_unit,
                                'amount' => $app_qty,
                                )
                            );

                        if($id == $v['child']){
                            
                            
                            $qty = get_post_meta($pro_container[$k]['intermediary'], 'wpcf-apprate-qty', true);
                            $unit = get_post_meta($pro_container[$k]['intermediary'], 'wpcf-apprate-unit-holdover', true);
                            if(isset($app_rates[$type][$product_id]['bag'][$id])){
                                $qty = $app_rates[$type][$product_id]['bag'][$id]['amount'];
                                $unit = $app_rates[$type][$product_id]['bag'][$id]['unit'];
                            }
                            // var_dump($qty);
                            // var_dump($app_qty);
                            // var_dump(get_post_meta($pack_id, 'wpcf-size', true));
                            $package_size = get_post_meta($pack_id, 'wpcf-size', true);
                            $package_unit = get_post_meta($pack_id, 'wpcf-unit', true);
                            // var_dump($package_size);
                            $cups = get_post_meta($product_id, 'wpcf-5cups', true);
                            $cups = getMass($cups, get_post_meta($product_id, 'wpcf-5cups-unit', true), 'lb');
                            $pp_dilema = 'ppc';
                            // if($package_unit == 'cuft'){
                            if($qty == 0){
                                $final = 0;
                                $pp_dilema = 'cpp';
                            }else{
                                $conversion = getVolume($qty, $unit, $package_unit);
                                if($conversion >= $package_size){
                                    $final = $conversion / $package_size;
                                    $pp_dilema = 'cpp';
                                }else{
                                    $final = $package_size / $conversion;
                                    $pp_dilema = 'ppc';
                                }
                            }
                            // var_dump($conversion);
                            // }else{
                            //     $conversion = indppl_normalize($items, $package_unit, intval($cups));
                            //     $conversion = $conversion[0]['standard-amount'];
                            //     $final = $package_size / $conversion;
                            // }
                            $app_qty = round($final, 2);
                            if($knife != $first_key){
                                if($pp_dilema == 'ppc'){
                                    $ppc_text = "plants per bag / container";
                                }else{
                                    $ppc_text = 'bags / containers per plant';
                                }
                                ?>
                                <p data-ppc='<?php echo $pp_dilema; ?>' data-num='<?php echo $app_qty; ?>'><?php echo $app_qty . "  " . $ppc_text; ?></p>
                                <?php
                            }else{
                                if($app_qty){
                                    ?>
                                    <input type='text' class='some-kind-of-wonderful indppl-product-create-chart-app-rate-num' name=<?php echo $id; ?> value=<?php echo $app_qty; ?> >
                                    <?php
                                }else{
                                    ?>
                                    <input type='text' class='some-kind-of-wonderful indppl-product-create-chart-app-rate-num' name=<?php echo $id; ?> value=0 >
                                    <?php
                                }
                                echo ' ';
                                
                                ?>
                                <select class='some-kind-of-wonderful indppl-product-create-chart-bag-unit' name=<?php echo $id; ?> data-unit=<?php echo $pp_dilema; ?>>
                                    
                                </select>
                                <?php
                            }
                        }
                    }
                    
                    if(!$default || empty($pro_container)){
                        if($non_default_app_rate){
                            $unit = $non_default_app_rate;
                        }else{
                            $unit = get_post_meta($pack_id, 'wpcf-unit', true);
                        }
                        $qty = 0;

                        if(isset($app_rates[$type][$product_id]['bag'][$id])){
                            $qty = $app_rates[$type][$product_id]['bag'][$id]['amount'];
                            $unit = $app_rates[$type][$product_id]['bag'][$id]['unit'];
                        }
                        // var_dump(get_post_meta($pack_id, 'wpcf-size', true));
                        $package_size = get_post_meta($pack_id, 'wpcf-size', true);
                        $package_unit = get_post_meta($pack_id, 'wpcf-unit', true);
                        if(!$non_default_app_rate){
                            $non_default_app_rate = $package_unit;
                        }

                        $cups = get_post_meta($product_id, 'wpcf-5cups', true);
                        $cups = getMass($cups, get_post_meta($product_id, 'wpcf-5cups-unit', true), 'lb');

                        $pp_dilema = 'ppc';
                        $type_same = false;
                        if((in_array($package_unit, $mass_units) && in_array($unit, $mass_units)) || (in_array($package_unit, $volume_units) && in_array($unit, $volume_units))){
                            $type_same = true;
                        }
                        
                        if($qty == 0){
                            $final = 0;
                            $pp_dilema = 'cpp';
                        }else{ 
                            if($type_same == true && in_array($package_unit, $mass_units)){
                                $conversion = getMass($qty, $unit, $package_unit);
                            }else if($type_same == true){
                                $conversion = getVolume($qty, $unit, $package_unit);
                            }else{
                                $cup = $cups/5;
                                if(in_array($package_unit, $mass_units)){
                                    $another_unit = getVolume($qty, $unit, 'cup');
                                    $conversion = $cup * $another_unit;
                                }else{
                                    $another = $qty / $cup;
                                    $conversion = getVolume($another, 'cup', $package_unit);
                                }
                            }
    
                            // var_dump($non_default_app_rate);
                            if($conversion >= $package_size){
                                $final = $conversion / $package_size;
                                $pp_dilema = 'cpp';
                            }else{
                                $final = $package_size / $conversion;
                                $pp_dilema = 'ppc';
                            }
                        }
                        $app_qty = round($final, 2);
                        if($app_qty == INF){
                            $app_qty = 0;
                        }
                        if($knife != $first_key){
                            if($pp_dilema == 'ppc'){
                                $ppc_text = "plants per bag / container";
                            }else{
                                $ppc_text = 'bags / containers per plant';
                            }
                            if($qty){
                                ?>
                                <p data-ppc='<?php echo $pp_dilema; ?>' data-num='<?php echo $app_qty; ?>'><?php echo $app_qty . "  " . $ppc_text; ?></p>
                                <?php
                            }else{
                                ?>
                                <p data-ppc='<?php echo $pp_dilema; ?>' data-num='<?php echo $app_qty; ?>'><?php echo "Fill out info to Left"; ?></p>
                                <?php
                            }
                        }else{
                            if($app_qty){
                                if($app_qty == INF){
                                    $app_qty = 0;
                                }
                                ?>
                                <input type='text' class='some-kind-of-wonderful indppl-product-create-chart-app-rate-num' name=<?php echo $id; ?> value=<?php echo $app_qty; ?> >
                                <?php
                            }else{
                                ?>
                                <input type='text' class='some-kind-of-wonderful indppl-product-create-chart-app-rate-num' name=<?php echo $id; ?> value=0 >
                                <?php
                            }
                            echo ' ';
                            
                            ?>
                            <select class='some-kind-of-wonderful indppl-product-create-chart-bag-unit' name=<?php echo $id; ?> data-unit=<?php echo $pp_dilema; ?>>
                                
                            </select>
                            <?php
                        }
                    }
                    ?>
                    </td>
                    <?php
                }
                ?>
            
        </tr>
        <?php
        $key++;
        }
    }
    
    ?>

    </table>
    <div class="product-create-submit-container">
        <input type="submit" name="product-create-submit-back" class='product-create-submit-back' value="Back"/>
        <input type="submit" name="product-create-submit-exit" data-exit="true" id="product-create-submit-exit" class="product-create-submit" value="Save and Exit"/>
        <input type="submit" name="product-create-submit" id="product-create-submit" class="product-create-submit" value="Save and add another product"/>
        <input type="submit" name="product-create-exit" id="product-create-exit" class="product-create-exit" value="Exit"/>
    </div>
    <?php
    $app_rates_chart = ob_get_clean();

    // $send_array['app_rates_chart'] = $app_rates_chart;
    return $app_rates_chart;
}

function indppl_get_product_info(){
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    
    ob_start();
    if($type == 'ground'){
        $heading = 'Product selection for In-Ground Plantings';
    }else if($type == 'pots'){
        $heading = 'Product selection for Pots';
    }else{
        $heading = 'Product selection for Raised Beds';
    }
    ?>
    
        <div class='slide-in-products-inside-container'>
            <a href='#' class='modal-close'>X</a>
            <h2><?php echo $heading; ?></h2>
            <form id='product-create-form' method="post" action='#' class="form-horizontal">
                <input type='hidden' name='indppl-modal-product-type' id='indppl-modal-product-type' value=<?php echo $type; ?>>
                <select class='product-create-brand' id='product-create-brand' name='product-create-brand'>
                    <option value='' disabled selected>Select Brand</option>
                    <?php
                    $brands = get_terms('brand');
                    // var_dump($brands);
                    foreach($brands as $key => $value){
                        $custom = get_term_meta($value->term_id, 'wpcf-custom-brand', true);
                        if($custom){
                            $mine = get_term_meta($value->term_id, 'wpcf-creator-user-id', true);
                            if($mine == get_current_user_id()){
                                ?> <option value="<?php echo $value->slug; ?>"><?php echo $value->name; ?> </option><?php
                            }
                        }else{
                            ?> <option value="<?php echo $value->slug; ?>"><?php echo $value->name; ?> </option><?php
                        }
                    }
                    $get_user_status = indppl_user_status(get_current_user_id());
                    if(in_array('paidaccountpro', $get_user_status)){
                        ?> <option value="new">Add Brand</option> <?php
                    }
                    
                    // var_dump($brands);
                    ?>
                </select>
                <select class='product-create-product' id='product-create-product' name='product-create-product'>
                    <option class='product-create-product-option' value='' disabled selected>Select Product</option>
                </select>
                <div class='product-create-brand-cut-off'>
                    <div class='product-create-first-part-container product-create-add-product-name'>
                    </div>
                    <div class='product-create-first-part-container product-create-dry-wet-container'>
                    </div>
                    <div class='product-create-standard-unit-container'>
                    </div>
                    <div class='product-create-first-part-container product-create-new-size-container'>
                    </div>
                    <div class='product-create-first-part-container product-create-size-container'>
                    </div>
                    <div class='product-create-first-part-container product-create-app-rate-container'>
                    </div>
                    <div class='product-create-first-part-container product-create-5-cups-container'>
                    </div>
                    <div class='product-create-first-part-container product-create-usage-type'>
                    </div>
                    <div class='product-create-first-part-container product-create-fraction-bag'>
                    </div>
                    <div class='product-create-first-part-container product-create-save-done-container'>
                    </div>
                    <div class='product-create-app-rates-chart-container'>
                    </div>
                </div>
            </form>
        </div>
    <?php
    $return = ob_get_clean();
    return $return;
}

function indppl_get_products($store_id, $key, $type){
        // var_dump($key);
        $pid = $key;
        $title = get_the_title($pid);
        $brand = get_the_terms($pid, 'brand');
        // var_dump($type);
        $package_relations = toolset_get_related_posts(
            $pid, // get posts related to this one
            'product-package', // relationship between the posts
            'parent',
            '100',
            '0',
            array(),
            'post_id',
            'child'
        );
        $store_related = toolset_get_related_posts(
            $store_id,
            'store-package',
            'parent',
            '100',
            '0',
            array(),
            'post_id',
            'child'
        );
        ?>
        <tr class='indppl-table-color-offset'>
            <td>
                <?php
                if($type == 'pots' || $type == 'beds'){
                    ?>
                    <a href="#" class="indppl-product-pots-edit" data-store=<?php echo $store_id; ?> data-product=<?php echo $pid; ?> data-type=<?php echo $type; ?>>edit</a>
                    <?php
                }else{
                    ?>
                    <a href="#" class="indppl-product-edit" data-store=<?php echo $store_id; ?> data-product=<?php echo $pid; ?> data-type=<?php echo $type; ?>>edit</a>
                    <?php
                }
                ?>
                <a href="#" class="indppl-product-delete" data-store=<?php echo $store_id; ?> data-product=<?php echo $pid; ?> data-type=<?php echo $type; ?>>delete</a>
            </td>
            <td>
                <?php echo $brand[0]->name; ?>
            </td>
            <td>
                <?php echo $title; ?>
            </td>
            <td>
                <?php
                $size_array = array_intersect($package_relations, $store_related);
                foreach($size_array as $key => $value){
                    $meta = get_post_meta($size_array[$key]);
                    echo $meta['wpcf-size'][0];
                    echo $meta['wpcf-unit'][0];
                    echo ' ';
                }
                ?>
            </td>
            <?php
            if($type == 'ground'){
            ?>
                <td>
                    <?php
                    $update = get_post_meta($store_id, 'wpcf-apprate-update', true);
                    $update = json_decode($update, true);
                    if($update[$pid] == true){
                        $url = plugin_dir_url(__FILE__);
                        $url = $url . "assets/img/error.png";
                        echo "<span class='color-red'><img class='height-17' src='" . $url . "'> Verify App-rates</span>";
                    }
                    ?>
                </td>
            <?php
            }
            ?>
        </tr>
        <?php  
    
}


function indppl_image_upload(){
    $filename = $_FILES['file'];
    $wp_upload_dir = wp_upload_dir();
    $upload_overrides = array( 'test_form' => false );
    $return = wp_handle_upload($filename, $upload_overrides);
    return $return['url'];
}

function indppl_encodeURIComponent($str) {
    $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
    return strtr(rawurlencode($str), $revert);
}

function indppl_guide_products($products){
    foreach ($products as $product) {
    $brands       = get_the_terms($product['product'], 'brand');
    $brand        = $brands[0];
    $sponsorship  = toolset_get_related_post($product['product'], 'sponsorship-product');
    $image        = get_post_meta($product['product'], 'wpcf-product-image', TRUE);
    $sponsor_copy = '';?>
        <div class='indppl-flex indppl-align-center guide-product-template'>
            <?php if ($sponsorship) {
                $sponsor_image = get_post_meta($sponsorship, 'wpcf-sponsorship-image', TRUE);
                $sponsor_copy  = get_post_meta($sponsorship, 'wpcf-sponsorship-copy', TRUE);
                $sponsor_link  = get_post_meta($sponsorship, 'wpcf-sponsor-url', TRUE);
                $image         = $sponsor_image;
                ?>
            <?php }

            if ($image && $image != '') {?>
                <div class='product-guide-image'><img src="<?php echo $image; ?>" alt="<?php echo $product['label']; ?>"></div>
            <?php }?>

            <div class='product-guide-step-instructions'>
                <span class='strong product-name'><span class='brand'><?php echo $brand->name; ?></span> <span class='product'><?php echo $product["label"]; ?></span></span> <?php echo $product["instructions"]; ?>
                <?php if ($sponsorship) {?>
                    <br /><a href="#" class='sponsor-link'>Learn more about this product - Click Here</a><span class='hide sponsor-copy'><?php echo $sponsor_copy; ?><br /><a href='<?php echo $sponsor_link; ?>' target="_blank">Learn More...</a></span>
                <?php }?>
            </div>
        </div>
    <?php }
}

function update_sponsorship_view_count($store_id, $sponsor_id){
    $sponsor_count = get_post_meta($sponsor_id, 'wpcf-view-count', TRUE);
    $sponsor_count = json_decode($sponsor_count, true);
    // $month = date('F, Y', strtotime("+3 month", strtotime(date('F, Y'))));
    $month = date('F, Y');
    if(is_array($sponsor_count)){
        if(array_key_exists($store_id, $sponsor_count)){
            if(array_key_exists($month, $sponsor_count[$store_id])){
                $sponsor_count[$store_id][$month]++;
            }else{
                $sponsor_count[$store_id][$month] = 1;
            }
        }else{
            $sponsor_count[$store_id][$month] = 1;
        }
        update_post_meta( $sponsor_id, 'wpcf-view-count', json_encode($sponsor_count));
    }else{
        $array = array(
            $store_id => array(
                $month => 1,
            ),
        );
        update_post_meta( $sponsor_id, 'wpcf-view-count', json_encode($array));
    }
}

function get_add_store_button(){
    ob_start();
    ?>
    <div class='indppl-add-store-container'>

        <a class='indppl-add-store-link' href='<?php
            echo home_url() . "/store-profile/?new=true";
        ?>'>
            <div class='indppl-add-store-centered'>
                <svg id='path' class="icon  icon--plus" viewBox="-52.5 -52.5 100 100" xmlns="http://www.w3.org/2000/svg">
                    <path d="M-5 -25 h5 v20 h20 v5 h-20 v20 h-5 v-20 h-20 v-5 h20 z" />
                </svg>
            </div>
        </a>
        <div class='indppl-add-store-text-container'>
            <h4 class="orange-text">Add New Store</h4>
            <p class="grey-text">Start from scratch</p>
        </div>
    </div>
    <?php
    $return = ob_get_clean();
    return $return;
}

function indppl_store_progress_bar($store, $next_step = FALSE, $container = TRUE){

    // Setup the basics

    $meta = get_post_meta($store);
    $total = 4;
    $sub = indppl_user_status(get_current_user_id(  ));
    $pro = FALSE;
    if(in_array('paidaccountpro', $sub)){
        $pro = TRUE;
        $total = 9;
    }
    $complete = 0;
    $containers = toolset_get_related_posts($store, 'store-container', ['query_by_role' => 'parent', 'return' => 'post_id', 'role_to_return' => 'child'] );
    $apprates = json_decode($meta['wpcf-apprates'][0], TRUE);
    $bar = '';

    $steps = array();

    // Containers
    if(count($containers) > 0){
        $complete++;
        $steps['containers'] = TRUE;
    } else {
        $steps['containers'] = FALSE;
    }

    // var_dump($meta);

    // In Ground Products
    if(isset($apprates['ground']) && count($apprates['ground']) > 0){
        
        $complete++;
        $steps['ground'] = TRUE;
            
    } else {

        $steps['ground'] = FALSE;

    }

    // IF STORE IS PREMIUM
    if($pro){

        // Pots Products
        if(isset($apprates['pots']) && count($apprates['pots']) > 0){
            $complete++;
            $steps['pots'] = TRUE;
        } else {
            $steps['pots'] = FALSE;
        }
    
        // Pots Apprates
        if(isset($apprates['pots']['filler']) && count($apprates['pots']['filler']) > 0){
            $complete++;
            $steps['pots-rates'] = TRUE;
        } else {
            $steps['pots-rates'] = FALSE;
        }

        // Pots Products
        if (isset($apprates['beds']) && count($apprates['beds']) > 0) {
            $complete++;
            $steps['beds'] = TRUE;
        } else {
            $steps['beds'] = FALSE;
        }
    
        // Beds Products
        if (isset($apprates['beds']['filler']) && count($apprates['beds']['filler']) > 0) {
            $complete++;
            $steps['beds-rates'] = TRUE;
        } else {
            $steps['beds-rates'] = FALSE;
        }
    }


    // In Ground Planting Guide
    if(isset($meta['wpcf-planting-guide-ground-options']) && $meta['wpcf-planting-guide-ground-options'] != ''){
        $complete++;
        $steps['guide-ground'] = TRUE;
    } else {
        $steps['guide-ground'] = FALSE;
    }

    // IF STORE IS PREMIUM
    if($pro){

        // Pots Planting Guide
        if (isset($meta['wpcf-planting-guide-pots-options']) && $meta['wpcf-planting-guide-pots-options'] != '') {
            $complete++;
            $steps['guide-pots'] = TRUE;
        } else {
            $steps['guide-pots'] = FALSE;
        }
    
        // Beds Planting Guide
        if (isset($meta['wpcf-planting-guide-beds-options']) && $meta['wpcf-planting-guide-beds-options'] != '') {
            $complete++;
            $steps['guide-beds'] = TRUE;
        } else {
            $steps['guide-beds'] = FALSE;
        }
    }

    
    $percentage = round(($complete/$total) * 100, 0);
    $styles = '';
    if($percentage == 100){
        $styles = "border-top-right-radius:15px;border-bottom-right-radius:15px;";
    }
    if($container){
        $bar = "<div class='indppl-progress-container'>";
    }
    $bar .= "<div class='indppl-progress'><div class='indppl-progress-fill' style='width:{$percentage}%;{$styles}'><span>{$percentage}%</span></div></div>";

    if($next_step){
        
        $next_incomplete = '';
        foreach($steps as $title => $step){
            if(!$step){
                $next_incomplete = $title;
                break;
            }
        }
        
        switch($next_incomplete){
            case '' :
            $next = "Start by adding some containers under the 'Plant Containers' tab below.";
                break;
                case 'containers' :
                $next = "Next up - select which container sizes you carry. Do this under the 'Plant Containers' tab.";
                break;
                case 'ground' :
                $next = "Great work! Next you'll need to add some in-ground products. Do that under the 'Products' tab below.";
                break;
                case 'pots' :
                $next = "Next you'll need to setup the products you recommend for potted plants. Find this just below the in ground products.";
                break;
                case 'pots-rates' :
                $next = "Great! Now be sure to setup your recommended application rates for potted plants.";
                break;
                case 'beds' :
                $next = "Coming along! Now add your recommended products for raised beds.";
                break;
                case 'beds-rates' :
                $next = "Excellent. Now wrap up your product recommendations by setting up your recommended application rates for raised beds.";
                break;
                case 'guide-ground' :
                $next = "Almost done! Now move to the 'Guides' tab and setup your planting guide for in-ground products";
                break;
                case 'guide-pots' :
                $next = "Making great progress! The planting guide for potted plants is next on your to-do list!";
                break;
                case 'guide-beds' :
                $next = "Just about there. Wrap up your planting guides by setting up your recommendations for raised beds.";
                break;
            }

            if($percentage == 100){
                $next = "Excellent work! You've completed all the steps to setup your store, but it's not live yet. If you're ready, go ahead and hit the button below to make it public. Don't worry, if you still need to make some changes you don't have to go live until you're ready! <br /><br /> <a href='#' class='store-go-live-btn button button-primary
            ' data-id='{$store}'>Make Public</a>";
            }
            
            $bar .= "<div class='next-step'><p>{$next}</p></div>";
    }
    
    if($container){
        $bar .= "</div>";
    }
    $response = array(
        'bar' => $bar,
        'complete' => $percentage,
    );

    return $response;
    
}

function indppl_duplicate_store($store_id, $new_details){

    // Get store meta
    $meta = get_post_meta($store_id);

    $apprates = $meta['wpcf-apprates'][0];
    // $junk = var_export($apprates, TRUE);
    // $apprates = 'apprates';
    // $ground_guide = 'sdfsd';
    // $pots_guide = 'sdfsd';
    // $beds_guide = 'ssfsgsg';
    // $ground_guide = $meta['wpcf-planting-guide-ground-options'][0];
    // $pots_guide = $meta['wpcf-planting-guide-pots-options'][0];
    // $beds_guide = $meta['wpcf-planting-guide-beds-options'][0];

    $ground_guide = str_replace(array('"',"\'"),array('\"',"'"), $meta['wpcf-planting-guide-ground-options'][0]);
    $pots_guide = str_replace(array('"',"\'"),array('\"',"'"), $meta['wpcf-planting-guide-pots-options'][0]);
    $beds_guide = str_replace(array('"',"\'"),array('\"',"'"), $meta['wpcf-planting-guide-beds-options'][0]);

    $args = array(
        'post_type' => 'store',
        'post_status' => 'publish',
        'post_title' => $new_details['title'],
        'meta_input' => array(
            'wpcf-logo' => $meta['wpcf-logo'][0],
            'wpcf-spring-start' => $meta['wpcf-spring-start'][0],
            'wpcf-spring-end' => $meta['wpcf-spring-end'][0],
            'wpcf-summer-start' => $meta['wpcf-summer-start'][0],
            'wpcf-summer-end' => $meta['wpcf-summer-end'][0],
            'wpcf-fall-start' => $meta['wpcf-fall-start'][0],
            'wpcf-fall-end' => $meta['wpcf-fall-end'][0],
            'wpcf-winter-start' => $meta['wpcf-winter-start'][0],
            'wpcf-winter-end' => $meta['wpcf-winter-end'][0],
            'wpcf-apprates' => $apprates,
            'wpcf-planting-guide-ground-options' => $ground_guide,
            'wpcf-planting-guide-pots-options' => $pots_guide,
            'wpcf-planting-guide-beds-options' => $beds_guide,
            'wpcf-address1' => $new_details['address1'],
            'wpcf-address2' => $new_details['address2'],
            'wpcf-city' => $new_details['city'],
            'wpcf-state' => $new_details['state'],
            'wpcf-zip' => $new_details['zip'],
            'wpcf-phone' => $new_details['phone'],
            'wpcf-email' => $new_details['email'],
            'wpcf-weburl' => $new_details['url'],
        ),
    );

    // Create the post
    $new_store = wp_insert_post($args);

    // Set default toolset args
    $toolset_args = array(
        'query_by_role' => 'parent',
        'limit' => 9999,
        'return' => 'post_id',
        'role_to_return' => 'child',
    );

    // Get related packages
    $packages = toolset_get_related_posts($store_id, 'store-package', $toolset_args);
    foreach($packages as $package){
        toolset_connect_posts('store-package', $new_store, $package);
    }

    // Get related containers
    $containers = toolset_get_related_posts($store_id, 'store-container', $toolset_args);
    foreach ($containers as $container) {

        // Get the intermediary post id
        $options = toolset_get_related_posts(
            ['parent' => $store_id,'child' => $container,],
            'store-container',
            ['return' => 'post_id', 'role_to_return' => 'intermediary']
        );

        // Get the seasons from the intermediary post
        $spring = get_post_meta($options[0], 'wpcf-available-in-spring', true);
        $summer = get_post_meta($options[0], 'wpcf-available-in-summer', true);
        $fall = get_post_meta($options[0], 'wpcf-available-in-fall', true);
        $winter = get_post_meta($options[0], 'wpcf-available-in-winter', true);

        //Create the connection
        $intermediary = toolset_connect_posts('store-container', $new_store, $container);

        // Set the seasons
        if($spring){
            update_post_meta( $intermediary['intermediary_post'], 'wpcf-available-in-spring', $spring);
        }
        if ($summer) {
            update_post_meta($intermediary['intermediary_post'], 'wpcf-available-in-summer', $summer);
        }
        if ($fall) {
            update_post_meta($intermediary['intermediary_post'], 'wpcf-available-in-fall', $fall);
        }
        if ($winter) {
            update_post_meta($intermediary['intermediary_post'], 'wpcf-available-in-winter', $winter);
        }

    }

    // Get related products
    $products = toolset_get_related_posts($store_id, 'store-product', $toolset_args);
    foreach ($products as $product) {
        toolset_connect_posts('store-product', $new_store, $product);
    }
    
    indppl_notify_new_store($store_id, get_current_user_id() );
}
    
function indppl_notify_new_store($store, $user){

    $user_info = get_userdata( $user );
    
    $to = get_option('admin_email');
    $subject = "Billing update needed";
    $message = "A new store has been published to the user account with the email {$user_info->user_email}";
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // wp_mail($to, $subject, $message, $headers);
    
}

function indppl_notify_deleted_store($store, $user){

    $user_info = get_userdata($user);

    // Get all the user's active stores
    $args = array(
        'author' => $user,
        'post_type' => 'store',
        'posts_per_page' => -1,
        // 'meta_query' => array(
            'meta_key' => 'wpcf-issetup',
            'meta_value' => 1
        // ),
    );

    $stores = get_posts($args);
    $count_stores = count($stores);

    if($count_stores > 0){
        $to      = get_option('admin_email');
        $subject = "Billing update needed";
        $message = "An additional store has been removed from the user account with the email {$user_info->user_email}";
        $headers = array('Content-Type: text/html; charset=UTF-8');

        // wp_mail($to, $subject, $message, $headers);
    }

}

// GRAVITY FORMS DYNAMIC POPULATION FUNCTIONS

function indppl_gform_user_type_pop($value) {
    $user = get_current_user_id();
    $status = indppl_user_status($user);
    $subscriptions = "Current user subscriptions:";
    if(count($status) < 1) {
        $subscriptions .= " no active subs!?";
    } else {
        foreach($status as $stat){
            $subscriptions .= " {$stat},";
        }
    }
    return $subscriptions;
}
add_filter('gform_field_value_indppl-user-type', 'indppl_gform_user_type_pop');

function indppl_gform_user_email_pop($value) {

    $user = get_userdata(get_current_user_id());
    return $user->user_email;
}
add_filter('gform_field_value_indppl-user-email', 'indppl_gform_user_email_pop');

function indppl_gform_username_pop($value) {

    $user = get_userdata(get_current_user_id());
    return $user->user_login;
}
add_filter('gform_field_value_indppl-username', 'indppl_gform_username_pop');

function indppl_gform_user_id_pop($value) {

    $user = get_userdata(get_current_user_id());
    return $user->ID;
}
add_filter('gform_field_value_indppl-account-number', 'indppl_gform_user_id_pop');

function indppl_gform_user_phone_pop($value) {

    $phone = get_user_meta(get_current_user_id(), 'mepr_phone_number', TRUE);
    return $phone;
}
add_filter('gform_field_value_indppl-user-phone', 'indppl_gform_user_phone_pop');

function indppl_gform_firstname_pop($value) {

    $user = get_userdata(get_current_user_id());
    return $user->first_name;
}
add_filter('gform_field_value_indppl-first-name', 'indppl_gform_firstname_pop');

function indppl_gform_lastname_pop($value) {

    $user = get_userdata(get_current_user_id());
    return $user->last_name;
}
add_filter('gform_field_value_indppl-last-name', 'indppl_gform_lastname_pop');

function indppl_readable_fraction($decimal){
    $fraction  = dec2frac($decimal);
    $calc_frac = explode("/", $fraction);
    if ($calc_frac[1] > 8) {
        while ($calc_frac[1] > 8) {
            $calc_frac[0]++;
            if($calc_frac[0] == $calc_frac[1]){
                return 1;
            }
            $fraction = dec2frac($calc_frac[0] / $calc_frac[1]);
            // var_dump($fraction);
            $calc_frac = explode("/", $fraction);
        }
    }

    return $fraction;
}

function indppl_user_is_auth($user_id, $store){
    $user_info  = get_userdata($user_id);
    $user_email = $user_info->user_email;
    $authorized = indppl_get_dup_auth($user_email, 'sub');
    if(count($authorized > 0)){
        foreach($authorized as $auth){
            if($auth['store_id'] == $store){
                return true;
            }
        }
    }

    return false;
}