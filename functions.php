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
    $meta = get_user_meta($id)['wpnr_capabilities'];
    $data = unserialize($meta[0]);
    $account_array = array();
    if(isset($data['paidaccount'])){
        array_push($account_array, 'paidaccount');
    }
    if(isset($data['freeaccount'])){
        array_push($account_array, 'freeaccount');
    }
    return $account_array;
}

