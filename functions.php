<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security
/*
Collection of functions for the entire site.
 */

function geofind($lat, $lon) {

    $xml = 'http://api.geonames.org/findNearbyPostalCodes?lat=' . $lat . '&lng=' . $lon . '&country=USA&radius=25&username=mrcbrown&maxRows=15';
    var_dump($xml);
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

function geozip($zipcode) {

    $xml = 'http://api.geonames.org/findNearbyPostalCodes?postalcode=' . $zipcode . '&country=USA&radius=25&username=mrcbrown&maxRows=15';

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

function indppl_user_status($id){
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
	
    ob_start();
	if(is_int($store_id)){ ?>
		<h1>Edit Store Information</h1>
	<?php }else{ ?>
    	<h1>Welcome to Planting Pal!</h1>
    	<p>We just need to get a few quick details to configure your store then you can begin building out your products and rates.</p>
	<?php } ?>
		<!-- <form method="post" action='#' id='store-management-form' class="form-horizontal"> -->
    <form  method="post" action='#' id='store-management-form' class="form-horizontal" enctype="multipart/form-data">
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
			<input id="address1" name="address1" type="text" placeholder="" class="form-control input-md" required="" value="<?php echo $address1; ?>">
			
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
        // var_dump(wp_get_attachment_image_src($attachment_id)[0]);
        $store = array(
            'ID' => $store_id,
			'post_title' => wp_strip_all_tags($_POST['store-name']),
			'post_author' => get_current_user_id(),
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
				'wpcf-logo' => wp_get_attachment_image_src($attachment_id)[0],
				'wpcf-weburl' => $_POST['weburl'],
			),
		);
		$store_id = wp_insert_post($store);
		return $store_id;
	}
}

function indppl_build_container_relation_output($id, $title, $relation_array, $int_array, $meta){
    ob_start();
    // old check mark
    // M14.1 25.2l7.1 7.2 16.7-16.8
    $check_mark = '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 17 L30 37 L10 37 L10 17 Z"></path><path class="checkmark__check" fill="green" d="M15 18 L13 22 L20 27 L37 7 L20 23 L15 18"></path></svg>';
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
        <td class='padding-left-40 position-absolute'><?php
        $fix_relative_issue = '';
            if(in_array($id, $relation_array)){
                ?>
                <input type="checkbox" id="<?php echo $id; ?>-container-available" class="display-none container-available-in-store" name="<?php echo $id; ?>-container-available" checked>
                <label class="margin-0 container-available-check" for="<?php echo $id; ?>-container-available"><?php echo $check_mark; ?></label>
                <?php
                $fix_relative_issue = 'container-title-fix';
            }
            if($meta){
                echo '<p class="' . $fix_relative_issue . ' container-title">' . $title . '</p>';
                $defualt_or_not_class = 'indppl-default-container';
            }else{
                ?>
                <input type='text' class='<?php echo $fix_relative_issue; ?> container-title indppl-container-edit-title' name='indppl-container-title' value='<?php echo $title; ?>'>
                <?php
                $defualt_or_not_class = 'indppl-non-default-container';
            }
        ?></td>
        <td>
            <?php
            if(in_array($id, $relation_array) && in_array('wpcf-available-in-spring', $int_array[$id])){
                echo '<input type="checkbox" name="' . $id . '-' . 'spring" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'spring" checked /><label class="margin-0" for="' . $id . '-' . 'spring">' . $available . '</label>';
            }else{
                echo '<input type="checkbox" name="' . $id . '-' . 'spring" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'spring"/><label class="margin-0" for="' . $id . '-' . 'spring">' . $not_available . '</label>';
            }
            ?>
        </td>
        <td>
            <?php
            if(in_array($id, $relation_array) && in_array('wpcf-available-in-summer', $int_array[$id])){
                echo '<input type="checkbox" name="' . $id . '-' . 'summer" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'summer" checked /><label class="margin-0" for="' . $id . '-' . 'summer">' . $available . '</label>';
            }else{
                echo '<input type="checkbox" name="' . $id . '-' . 'summer" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'summer"/><label class="margin-0" for="' . $id . '-' . 'summer">' . $not_available . '</label>';
            }
            ?>
        </td>
        <td>
            <?php
            if(in_array($id, $relation_array) && in_array('wpcf-available-in-fall', $int_array[$id])){
                echo '<input type="checkbox" name="' . $id . '-' . 'fall" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'fall" checked /><label class="margin-0" for="' . $id . '-' . 'fall">' . $available . '</label>';
            }else{
                echo '<input type="checkbox" name="' . $id . '-' . 'fall" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'fall"/><label class="margin-0" for="' . $id . '-' . 'fall">' . $not_available . '</label>';
            }
            ?>
        </td>
        <td>
            <?php
            if(in_array($id, $relation_array) && in_array('wpcf-available-in-winter', $int_array[$id])){
                echo '<input type="checkbox" name="' . $id . '-' . 'winter" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'winter" checked /><label class="margin-0" for="' . $id . '-' . 'winter">' . $available . '</label>';
            }else{
                echo '<input type="checkbox" name="' . $id . '-' . 'winter" class="display-none ' . $defualt_or_not_class . '" id="' . $id . '-' . 'winter"/><label class="margin-0" for="' . $id . '-' . 'winter">' . $not_available . '</label>';
            }
            ?>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}

