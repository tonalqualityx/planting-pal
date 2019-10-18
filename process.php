<?php

// include './templates/process.tpl';

//echo $guideid . "<br>";
// $arr = $_POST;
/*
echo "<pre>";
print_r($arr);
echo "</pre>";
 */

//echo "This is the storeID: " . $arr['storeid'] ."<br>";

/*
Start InGround Fractional - JUMP[IGO]
-----------------------------------------------
 */
// Grab Conainers for IG
$storePull = 'SELECT * FROM `containers` WHERE `storeid` = ' . $arr['storeid'] . ' AND `active` = 1';
$grabList = $dbconn->query($storePull);

// Loop in Containers
while ($shop = $grabList->fetch_array(MYSQLI_ASSOC)) {

    // PROCESS FOR IGF
    // Grab IG Rates
    $ratesPull = 'SELECT * FROM `apprates` WHERE `storeid` = ' . $arr['storeid'] . ' AND `containerid` = ' . $shop['id'] . ' AND `type` = "IGF" LIMIT 1';
    $grabRates = $dbconn->query($ratesPull);

    // Loop in App Rates
    while ($rates = $grabRates->fetch_array(MYSQLI_ASSOC)) {
        //echo "The Unit: ".$rates['unit'] ."<br>";
        if ($rates['unit'] == "PPC" or $rates['unit'] == "CPP") {
            switch ($rates['unit']) {
                case 'PPC':
                    $therate = productSize($rates['productid'], $arr['storeid']) / $rates['rate'];
                    $theunit = prodUnit($rates['productid'], $arr['storeid']);
                    break;
                case 'CPP':
                    $therate = productSize($rates['productid'], $arr['storeid']) * $rates['rate'];
                    $theunit = prodUnit($rates['productid'], $arr['storeid']);
                    break;
            }
        } else {
            $therate = $rates['rate'];
            $theunit = $rates['unit'];
        }
        //echo "This is a rate: " .$therate ."<br>";
        //echo $shop['name']." - ".$arr["IG|".$shop['id']] ." - ".$arr["IG|".$shop['id']] * $therate." ".$theunit."<br>";

        $total_ig = $total_ig + $arr["IG|" . $shop['id']] * $therate;
        //echo "Total In Ground: ".$total_ig;
        $units_ig = $theunit;

        // Grab Cups Comparison
        $tcupsPull = 'SELECT * FROM `apprates` WHERE `storeid` = ' . $arr['storeid'] . ' AND `containerid` = ' . $shop['id'];
        $grabTcups = $dbconn->query($tcupsPull);
        // Loop in App Rates
        while ($tcups = $grabTcups->fetch_array(MYSQLI_ASSOC)) {
            $prod_ig[] = $tcups['productid'];
        }

    }
    // Take totaled values to Cups!
    $allcups = findCups($total_ig, $units_ig, $total_ig);
}
/* DEBUG
echo "<br>Total Cups: <b>".$allcups."</b> / ".$total_ig."<br>";
echo "<br>Grab from DB - ".$prod_ig." - ".$arr['storeid']."] <br>";
 */
$grabProds = array_unique($prod_ig);

// Get Producs for Cups Need
foreach ($grabProds as $prods) {
    $prodPull = 'SELECT * FROM `products` WHERE `id` = ' . $prods . ' AND `storeid` = ' . $arr['storeid'] . ' OR `parentid` = ' . $prods;
    $grabProd = $dbconn->query($prodPull);

    while ($prodcups = $grabProd->fetch_array(MYSQLI_ASSOC)) {
/* Old Debug Tests
echo "<br><b>START DEBUG</b><br>";
echo "Size: ".$prodcups['size'];
echo "<br>Unit: ".$prodcups['unit'];
echo "<br>This is math: (".$allcups ."/".findCups($prodcups['size'],$prodcups['unit'],$prodcups['size'])." = ".$allcups / findCups($prodcups['size'],$prodcups['unit'],$prodcups['size']) * 100 ."<br>";
echo "Going to compare this: <b>".$allcups / findCups($prodcups['size'],$prodcups['unit'],$prodcups['size']) ."</b><br>";
echo "Total # of Bags: " . round($allcups / findCups($prodcups['size'],$prodcups['unit'],$prodcups['size']));
echo "<br><br>";
 */
        $compare['id'][] = $prodcups['id']; //$prods;
        $compare['size'][] = $prodcups['size'];
        $compare['units'][] = $prodcups['unit'];
        $compare['prodcups'][] = findCups($prodcups['size'], $prodcups['unit'], $prodcups['size']);
        $compare['percent'][] = $allcups / findCups($prodcups['size'], $prodcups['unit'], $prodcups['size']);
    }
    ;
}
;
/*
echo "<h1>DEBUG:</h1>";
echo "<pre>";
print_r($compare);
echo "</pre>";
echo "<br>AllCUPS: ".$allcups;
echo "<br>Bag Size: ".getBagsize($compare,$allcups);
 */
$IGFBag[] = getBagsize($compare, $allcups);
$compare = '';

/*
Start InGround Other (non-dry/cuft/fraction) - - JUMP[IGF]
-----------------------------------------------
 */
// IGO - INGROUND other
// Grab Conainers for IG
$storePull2 = 'SELECT * FROM `containers` WHERE `storeid` = ' . $arr['storeid'] . ' AND `active` = 1';
$grabList2 = $dbconn->query($storePull2);

// Loop in Containers
while ($shop2 = $grabList2->fetch_array(MYSQLI_ASSOC)) {

// PROCESS FOR IGO
    // Grab IG Rates
    $ratesPull2 = 'SELECT * FROM `apprates` WHERE `storeid` = ' . $arr['storeid'] . ' AND `containerid` = ' . $shop2['id'] . ' AND `type` = "IGO" LIMIT 1';
    $grabRates2 = $dbconn->query($ratesPull2);

// Loop in App Rates
    while ($rates2 = $grabRates2->fetch_array(MYSQLI_ASSOC)) {
//echo "The Unit: ".$rates2['unit'] ."<br>";

        $therate2 = findCups($rates2['rate'], $rates2['unit'], "4");
        $theunit2 = $rates2['unit'];

//echo "This is a rate: " .$therate2 ."<br>";
        //echo $shop2['name']." - ".$arr["IG|".$shop2['id']] ." - ".$arr["IG|".$shop2['id']] * $therate2." ".$theunit2."<br>";

        $total_ig2 = $total_ig2 + $arr["IG|" . $shop2['id']] * $therate2;
//echo "Total In Ground: ".$total_ig2;
        $units_ig2 = $theunit2;

// Grab Cups Comparison
        $tcupsPull2 = 'SELECT * FROM `apprates` WHERE `storeid` = ' . $arr['storeid'] . ' AND `containerid` = ' . $shop2['id'] . ' AND `type` = "IGO"';
        $grabTcups2 = $dbconn->query($tcupsPull2);
// Loop in App Rates
        while ($tcups2 = $grabTcups2->fetch_array(MYSQLI_ASSOC)) {
            $prod_ig2[] = $tcups2['productid'];
        }

    }
// Take totaled values to Cups!
    $allcups2 = findCups($total_ig2, $units_ig2, $total_ig2);
}
/* DEBUG
echo "<pre>";
print_r($prod_ig2);
echo "</pre>";
echo "<br>Total Cups: <b>".$allcups2."</b> / ".$total_ig2."<br>";
echo "<br>Grab from DB - ".$prod_ig2." - ".$arr['storeid']."] <br>";
 */
$grabProds2 = array_unique($prod_ig2);

// Get Producs for Cups Need
foreach ($grabProds2 as $prods2) {
    $prodPull2 = 'SELECT * FROM `products` WHERE `id` = ' . $prods2 . ' AND `storeid` = ' . $arr['storeid'] . ' OR `parentid` = ' . $prods2;
    $grabProd2 = $dbconn->query($prodPull2);
    $compare2 = '';
    while ($prodcups2 = $grabProd2->fetch_array(MYSQLI_ASSOC)) {
/* Old Debug Tests
echo "<br><b>START DEBUG</b><br>";
echo "ID: ".$prodcups2['id'] ."/".$$prodcups2['name'];
echo "<br>Size: ".$prodcups2['size'];
echo "<br>Unit: ".$prodcups2['unit'];
echo "<br>5Cups: ".$prodcups2['5cups'];
echo "<br>This is math: (".$allcups2 ."/".findCups($prodcups2['5cups'],$prodcups2['unit'],"1") * $prodcups2['size'] ." = ".$allcups2 / findCups($prodcups2['5cups'],$prodcups2['unit'],"1") * $prodcups2['size'] * 100 ."<br>";
echo "Going to compare this: <b>".$allcups2 / findCups($prodcups2['5cups'],$prodcups2['unit'],"1") * $prodcups2['size'] ."</b><br>";
echo "Total # of Bags: " . round($allcups2 / findCups($prodcups2['5cups'],$prodcups2['unit'],"1")) * $prodcups2['size'];
echo "<br><br>";
 */
        $compare2['id'][] = $prodcups2['id']; //$prods;
        $compare2['size'][] = $prodcups2['size'];
        $compare2['units'][] = $prodcups2['unit'];
        if ($prodcups2['unit'] == "lbs") {
            $compare2['prodcups'][] = findCups($prodcups2['5cups'], $prodcups2['unit'], "1") * $prodcups2['size'];
            $compare2['percent'][] = $allcups2 / findCups($prodcups2['5cups'], $prodcups2['unit'], $prodcups2['size']); // * $prodcups2['size;
        } else {
            $compare2['prodcups'][] = findCups($prodcups2['size'], $prodcups2['unit'], "1");
            $compare2['percent'][] = $allcups2 / findCups($prodcups2['size'], $prodcups2['unit'], $prodcups2['size']); // * $prodcups2['size;
        }
    }
    ;
}
;
/* Debug
echo "<pre>";
print_r($compare2);
echo "</pre>";
echo $allcups2 / 14.2857142857;
 */
$IGOBag[] = getBagsize($compare2, $allcups2);

/*
Start Pots  (includes Eaches) - JUMP[POTS]
-----------------------------------------------
 */

//Build Product from Submitted values
//print_r($arr);
//clean empty
$pots = array_filter($arr['pqty']);
$start_p = 1;
$end_p = count($arr['pqty']);
if (!empty($arr['pqty'])) {
    $i = 0;
    while ($start_p <= $end_p) {
        $c_status = "pstatus_" . $start_p;
        $pot_configs[] = $arr['pqty'][$i] . "|" . $arr['plength'][$i] . "|" . $arr['pwidth'][$i] . "|" . $arr['pheight'][$i] . "|" . $arr[$c_status] . "|" . $arr['pneed'][$i];
        $i++;
        $start_p++;
    }
    ;
}
;
/*
echo "<br><b>Pots!</b><pre>";
print_r($pot_configs);
echo "</pre>";
 */
// Loop Results (Explode Response to break out pot info)
foreach ($pot_configs as &$pot_info) {
    $p_info = explode("|", $pot_info);
//New ArrayID
    $arrayID = $p_info[1] . "x" . $p_info[2] . "x" . $p_info[3];
// Volume for POT
    if ($p_info[4] == "empty") {
        $cus_total = $p_info[1] * $p_info[2] * $p_info[3] * 0.069264069;
    } else {
        $cus_total = $p_info[1] * $p_info[2] * $p_info[5] * 0.069264069;
    }
    $bf_totals[$arrayID] = grabBulk($cus_total, $p_info[0], $arr['storeid'], $arrayID, 'POT');
    $ba_totals[$arrayID] = grabBA($cus_total, $p_info[0], $arr['storeid'], $arrayID, 'POT');
    $sa_totals[$arrayID] = grabSA($cus_total, $p_info[0], $arr['storeid'], $arrayID, 'POT');
    $ea_totals[$arrayID] = grabEA($cus_total, $p_info[0], $arr['storeid'], $arrayID, 'POT');
}
/*
echo "<h3>BF Totals:</h3><br><pre>";
print_r($bf_totals);
echo "</pre>";

echo "<h3>BA Totals:</h3><br><pre>";
print_r($ba_totals);
echo "</pre>";
 */
// Loop Results (Explode Response to break out pot info)
$grabBlend = "SELECT count(*) FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'POT' AND `bf` LIKE '1'";
$grabProd = $dbconn->query($grabBlend);
$count_bf = $grabProd->fetch_array(MYSQLI_NUM);

$countUp = '1';
$i = '0';
while ($count_bf[0] >= $countUp) {
    $grabBf = "SELECT * FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'POT' AND `bf` LIKE '1' LIMIT 1 OFFSET " . $i;
    $grabPf = $dbconn->query($grabBf);
    $bfGrab = $grabPf->fetch_array(MYSQLI_NUM);

//echo "<br>(".$countUp."/".$count_bf[0].")This is a Pull: " . $bfGrab[2];
    foreach ($pot_configs as &$pot_info) {
        $p_info = explode("|", $pot_info);
//New ArrayID
        $arrayID = $p_info[1] . "x" . $p_info[2] . "x" . $p_info[3];
        $bf_grand += $bf_totals[$arrayID][$bfGrab[2]];
    }
    ; // End foreach
    $bf_gt[$bfGrab[2]] = $bf_grand;

    $rq_test = getProductFam($bfGrab[2]);
    $count_bf2 = count($rq_test);
    $countUp2 = 1;
    $i2 = 0;
    $compare3 = '';
    while ($countUp2 <= $count_bf2) {
/*
echo "<br><h3>TACOS! Product Family</h3><br><pre>";
print_r($rq_test);
echo "</pre>";
 */
//echo "<br>Current Count: ".$count_bf2."/".$countUp2."<br>";
        $compare3['id'][] = $rq_test[$i2]['id']; //$prods;
        $compare3['size'][] = $rq_test[$i2]['size'];
        $compare3['units'][] = $rq_test[$i2]['unit'];

//echo "<h1>".$bf_grand."</h1>";
        //echo "<h1>Something: ".findCups($rq_test[$i2]['size'],$rq_test[$i2]['unit'],$rq_test[$i2]['size'])."</h1>";
        if ($rq_test[$i2]['unit'] == "lbs") {
            $compare3['prodcups'][] = findCups($rq_test[$i2]['5cups'], $rq_test[$i2]['unit'], "1") * $rq_test[$i2]['size'];
            $compare3['percent'][] = $bf_grand / findCups($rq_test[$i2]['5cups'], $rq_test[$i2]['unit'], $rq_test[$i2]['size']); // * $prodcups2['size;
        } else {
            $compare3['prodcups'][] = findCups($rq_test[$i2]['size'], $rq_test[$i2]['unit'], "1");
            $compare3['percent'][] = $bf_grand / findCups($rq_test[$i2]['size'], $rq_test[$i2]['unit'], $rq_test[$i2]['size']); // * $prodcups2['size;
        }

        $countUp2++;
        $i2++;
    }
/*
echo "<h1>COMPARE3.0</h1><pre>";
print_r($compare3);
echo "</pre>";
 */
    $pot_bf[] = getBagsize($compare3, $bf_grand);
//print_r(getBagsize($compare3,$bf_grand));
    /*
    echo "<br><h3>Product Family</h3><br><pre>";
    print_r($pot_bf);
    echo "</pre>";
     */
    $bf_grand = 0;
    $countUp++;
    $i++;
}
; // End While

/*

Array Buildout for Blended Addatives - JUMP[BA]

 */

// Loop Results (Explode Response to break out pot info)
$grabBlend = "SELECT count(*) FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'POT' AND `ba` LIKE '1'";
$grabProd = $dbconn->query($grabBlend);
$count_ba = $grabProd->fetch_array(MYSQLI_NUM);

$countUp = '1';
$i = '0';
while ($count_ba[0] >= $countUp) {
    $grabba = "SELECT * FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'POT' AND `ba` LIKE '1' LIMIT 1 OFFSET " . $i;
    $grabPf = $dbconn->query($grabba);
    $baGrab = $grabPf->fetch_array(MYSQLI_NUM);
/*
echo "<h1>LOOK AT ME</h1><pre>";
print_r($baGrab);
echo "</pre>";
echo "<br>(".$countUp."/".$count_ba[0].")This is a Pull: " . $baGrab[2];
 */
    foreach ($pot_configs as &$pot_info) {
        $p_info = explode("|", $pot_info);
//New ArrayID
        $arrayID = $p_info[1] . "x" . $p_info[2] . "x" . $p_info[3];
        $ba_grand += $ba_totals[$arrayID][$baGrab[2]];
    }
    ; // End foreach
    $ba_gt[$baGrab[2]] = $ba_grand;

    $rq_test = getProductFam($baGrab[2]);
/*
echo "<br><h3>GET Product Family</h3><br><pre>";
print_r($rq_test);
echo "</pre>";
 */
    $count_ba2 = count($rq_test);
    $countUp3 = 1;
    $i3 = 0;
    $compare4 = '';
    while ($countUp3 <= $count_ba2) {

//echo "<br><h3>Product Family</h3><br><pre>";
        //print_r($rq_test[0]);
        //echo "</pre>";

//echo "<br>Current Count: ".$count_ba2."/".$countUp3."<br>";
        $compare4['id'][] = $rq_test[$i3]['id']; //$prods;
        $compare4['size'][] = $rq_test[$i3]['size'];
        $compare4['units'][] = $rq_test[$i3]['unit'];

//echo "<h1>".$ba_grand."</h1>";
        //echo "<h1>Something: ".findCups($rq_test[$i3]['size'],$rq_test[$i3]['unit'],$rq_test[$i3]['size'])."</h1>";
        if ($rq_test[$i3]['unit'] == "lbs") {

            //echo "<h1>LBS2CUPS:</h1> ".findCups($rq_test[$i3]['5cups'],$rq_test[$i3]['unit'],"1");
            $compare4['prodcups'][] = findCups($rq_test[$i3]['5cups'], $rq_test[$i3]['unit'], "1") * $rq_test[$i3]['size'];
            $compare4['percent'][] = $ba_grand / findCups($rq_test[$i3]['5cups'], $rq_test[$i3]['unit'], $rq_test[$i3]['size']); // * $prodcups2['size;
        } else {
//echo "<br>This is 5: ".$baGrab[5];
            //echo "<br>This is 6: ".$baGrab[6];
            $compare4['prodcups'][] = findCups($rq_test[$i3]['size'], $rq_test[$i3]['unit'], "1") * $rq_test[$i3]['size'];
            $compare4['percent'][] = $ba_grand / findCups($rq_test[$i3]['size'], $rq_test[$i3]['unit'], $rq_test[$i3]['size']); //* $prodcups2['size'];
            //  $compare4['prodcups'][] = findCups($baGrab[5],$baGrab[6],$baGrab[5]);
            //  $compare4['percent'][] = $baGrab[5] / findCups('1','cuft','1') * 100;// * $prodcups2['size;
        }
//echo "<pre>";
        //print_r($compare4);
        //echo "</pre>";

        $countUp3++;
        $i3++;
    }
/*
echo "<br>Compare4:";
echo "<br><pre>";
print_r($compare4);
echo "</pre><br>BG: ".$ba_grand;
 */
//echo "<br>Bag Size: ".getbagsize($compare4,$ba_grand);

    $pot_ba[] = getbagsize($compare4, $ba_grand);
// End ba Loop

//print_r(getbagsize($compare4,$ba_grand));
    /*
    echo "<br><h3>Product Family</h3><br><pre>";
    print_r($pot_ba);
    echo "</pre>";
     */
    $ba_grand = 0;
    $countUp++;
    $i++;
}
;

/*

Array Buildout for Surface Added - JUMP[SA]

 */
//echo "<h1>Surface Added</h1>";
// Loop Results (Explode Response to break out pot info)
$grabBlend = "SELECT count(*) FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'POT' AND `sa` LIKE '1'";
$grabProd = $dbconn->query($grabBlend);
$count_sa = $grabProd->fetch_array(MYSQLI_NUM);

$countUp = '1';
$i = '0';
while ($count_sa[0] >= $countUp) {
    $grabsa = "SELECT * FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'POT' AND `sa` LIKE '1' LIMIT 1 OFFSET " . $i;
    $grabPf = $dbconn->query($grabsa);
    $saGrab = $grabPf->fetch_array(MYSQLI_NUM);

//echo "<br>(".$countUp."/".$count_sa[0].")This is a Pull: " . $saGrab[2];

    foreach ($pot_configs as &$pot_info) {
        $p_info = explode("|", $pot_info);
//New ArrayID
        $arrayID = $p_info[1] . "x" . $p_info[2] . "x" . $p_info[3];
//echo "<br>Value for SA_GRAND: ".$sa_totals[$arrayID][$saGrab[2]];
        $sa_grand += $sa_totals[$arrayID][$saGrab[2]];
    }
    ; // End foreach
    $sa_gt[$saGrab[2]] = $sa_grand;

    $rq_test = getProductFam($saGrab[2]);
    $count_sa2 = count($rq_test);
    $countUp4 = 1;
    $i4 = 0;
    while ($countUp4 <= $count_sa2) {
/*
echo "<br><h3>Product Family</h3><br><pre>";
print_r($rq_test[0]);
echo "</pre>";
 */
//echo "<br>Current Count: ".$count_sa2."/".$countUp4."<br>";
        $compare5['id'][] = $rq_test[$i4]['id']; //$prods;
        $compare5['size'][] = $rq_test[$i4]['size'];
        $compare5['units'][] = $rq_test[$i4]['unit'];
/*
echo "<h1>SA Grand: ".$sa_grand."</h1>";
echo "<h1>Into Cups: ".$rq_test[$i4]['size'] ." - ".$rq_test[$i4]['unit']." - ".$rq_test[$i4]['size']."</h1>";
 */
        if ($rq_test[$i4]['unit'] == "lbs") {
            $compare5['prodcups'][] = findCups($rq_test[$i4]['5cups'], $rq_test[$i4]['unit'], "1") * $rq_test[$i4]['size'];
            $compare5['percent'][] = $sa_grand / findCups($rq_test[$i4]['5cups'], $rq_test[$i4]['unit'], $rq_test[$i4]['size']); // * $prodcups2['size;
        } else {
            $compare5['prodcups'][] = findCups($rq_test[$i4]['size'], $rq_test[$i4]['unit'], "1");
            $compare5['percent'][] = $sa_grand / findCups($rq_test[$i4]['size'], $rq_test[$i4]['unit'], "1"); // * $prodcups2['size;
        }
/*
echo "<h1>COMPARE5</h1><br><pre>";
print_r($compare5);
echo "</pre>";
 */
        $countUp4++;
        $i4++;
    }
    $pot_sa[] = getBagsize($compare5, $sa_grand);
// End sa Loop

//print_r(getsagsize($compare5,$sa_grand));
    /*
    echo "<br><h3>Product Family</h3><br><pre>";
    print_r($pot_sa);
    echo "</pre>";
     */
    $compare5 = '';
    $sa_grand = 0;
    $countUp++;
    $i++;
}
;

/*

Begin Eaches for POTS - JUMP[EA]

 */

// Loop Results (Explode Response to break out pot info)
$grabEaches = "SELECT count(*) FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'POT' AND `ea` LIKE '1'";
$grabProd = $dbconn->query($grabEaches);
$count_ea = $grabProd->fetch_array(MYSQLI_NUM);

$countUp = '1';
$i = '0';

while ($count_ea[0] >= $countUp) {
//echo "(".$count_ea[0]."|".$countUp.")";
    $grabea = "SELECT * FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'POT' AND `ea` LIKE '1' LIMIT 1 OFFSET " . $i;
    $grabPf = $dbconn->query($grabea);
    $eaGrab = $grabPf->fetch_array(MYSQLI_NUM);

    foreach ($pot_configs as &$pot_info) {
        $p_info = explode("|", $pot_info);
//New ArrayID
        $arrayID = $p_info[1] . "x" . $p_info[2] . "x" . $p_info[3];
        $ea_grand += $ea_totals[$arrayID][$eaGrab[2]];
        $grabRates = array_map('intval', explode('|', $eaGrab[12]));
        $ans = sizeCheck($p_info[2], $grabRates[0], $grabRates[1], $grabRates[2]);
/*
echo $ans;
echo "<pre>";
var_dump($grabRates);
echo "</pre>";
 */
        $ea_compiled += sizeCheck($p_info[2], $grabRates[0], $grabRates[1], $grabRates[2]) * $p_info[0];
//echo "<br>Compiled:".$ea_compiled;
        $rq_test = getProductFam($eaGrab[2]);
        $count_e1 = count($rq_test);
        $countUpEA = 1;
        $iea = 0;
        $compareEA = '';
    }
    while ($countUpEA <= $count_e1) {
//echo "<br>Status: (".$count_e1." / ".$countUpEA." / EAC: ".$ea_compiled." ProdID: ".$eaGrab[2];
        $compareEA['id'][] = $rq_test[$iea]['id']; //$prods;
        $compareEA['size'][] = $rq_test[$iea]['size'];
        $compareEA['units'][] = $rq_test[$iea]['unit'];
        $compareEA['prodcups'][] = '';
        $compareEA['percent'][] = $ea_compiled / $rq_test[$iea]['size'];
        $countUpEA++;
        $iea++;
    }
    ;
//};// End foreach

//$compareEA['percent'][] = $ea_compiled / $rq_test[$i3]['qty'];

/*
echo "<br>This is Product#: ". $eaGrab[2];
echo "<br>This is EA Compiled: " . $ea_compiled;
echo "<pre>";
print_r($compareEA);
echo "</pre>";
 */
    $pot_ea[] = getBagsize($compareEA, $ea_compiled);
    $ea_compiled = '';
// Count Up!
    $i++;
    $countUp++;
}
/*
echo "<h1>Picked Sizes</h1><br><pre>";
print_r($picked_size);
echo "</pre>";
 */
// Fill Pots Bag with All Goodies
$potsBag_bf = $pot_bf;
$potsBag_ba = $pot_ba;
$potsBag_sa = $pot_sa;
$potsBag_ea = $pot_ea;

/*
Raised Beds - No Eaches - JUMP[RB]
-----------------------------------------------
 */

//Build Product from Submitted values
//print_r($arr);
//clean empty
$pots = array_filter($arr['rbqty']);
$start_p = 1;
$end_p = count($arr['rbqty']);
if (!empty($arr['rbqty'])) {
    $i = 0;
    while ($start_p <= $end_p) {
        $c_status = "rbstatus_" . $start_p;
        $rb_configs[] = $arr['rbqty'][$i] . "|" . $arr['rbl'][$i] . "|" . $arr['rbw'][$i] . "|" . $arr['rbh'][$i] . "|" . $arr[$c_status] . "|" . $arr['rbneed'][$i];
        $i++;
        $start_p++;
    }
    ;
}
;
/*
echo "<br><b>Pots!</b><pre>";
print_r($rb_configs);
echo "</pre>";
 */
// Loop Results (Explode Response to break out pot info)
foreach ($rb_configs as &$rb_info) {
    $p_info = explode("|", $rb_info);
//New ArrayID
    $arrayID = $p_info[1] . "x" . $p_info[2] . "x" . $p_info[3];
// Volume for POT
    if ($p_info[4] == "empty") {
        $cus_total = $p_info[1] * $p_info[2] * $p_info[3] * 0.069264069;
    } else {
        $cus_total = $p_info[1] * $p_info[2] * $p_info[5] * 0.069264069;
    }
    $bf_totals[$arrayID] = grabBulk($cus_total, $p_info[0], $arr['storeid'], $arrayID, 'RB');
    $ba_totals[$arrayID] = grabBA($cus_total, $p_info[0], $arr['storeid'], $arrayID, 'RB');
    $sa_totals[$arrayID] = grabSA($cus_total, $p_info[0], $arr['storeid'], $arrayID, 'RB');
    $ea_totals[$arrayID] = grabEA($cus_total, $p_info[0], $arr['storeid'], $arrayID, 'RB');
}
/*
echo "<h3>BF Totals:</h3><br><pre>";
print_r($bf_totals);
echo "</pre>";

echo "<h3>BA Totals:</h3><br><pre>";
print_r($ba_totals);
echo "</pre>";
 */
// Loop Results (Explode Response to break out pot info)
$grabBlend = "SELECT count(*) FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'RB' AND `bf` LIKE '1'";
$grabProd = $dbconn->query($grabBlend);
$count_bf = $grabProd->fetch_array(MYSQLI_NUM);

$countUp = '1';
$i = '0';
while ($count_bf[0] >= $countUp) {
    $grabBf = "SELECT * FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'RB' AND `bf` LIKE '1' LIMIT 1 OFFSET " . $i;
    $grabPf = $dbconn->query($grabBf);
    $bfGrab = $grabPf->fetch_array(MYSQLI_NUM);

//echo "<br>(".$countUp."/".$count_bf[0].")This is a Pull: " . $bfGrab[2];
    foreach ($rb_configs as &$rb_info) {
        $p_info = explode("|", $rb_info);
//New ArrayID
        $arrayID = $p_info[1] . "x" . $p_info[2] . "x" . $p_info[3];
        $bf_grand += $bf_totals[$arrayID][$bfGrab[2]];
    }
    ; // End foreach
    $bf_gt[$bfGrab[2]] = $bf_grand;

    $rq_test = getProductFam($bfGrab[2]);
    $count_bf2 = count($rq_test);
    $countUp2 = 1;
    $i2 = 0;
    $compare3 = '';
    while ($countUp2 <= $count_bf2) {
/*
echo "<br><h3>TACOS! Product Family</h3><br><pre>";
print_r($rq_test);
echo "</pre>";
 */
//echo "<br>Current Count: ".$count_bf2."/".$countUp2."<br>";
        $compare3['id'][] = $rq_test[$i2]['id']; //$prods;
        $compare3['size'][] = $rq_test[$i2]['size'];
        $compare3['units'][] = $rq_test[$i2]['unit'];

//echo "<h1>".$bf_grand."</h1>";
        //echo "<h1>Something: ".findCups($rq_test[$i2]['size'],$rq_test[$i2]['unit'],$rq_test[$i2]['size'])."</h1>";
        if ($rq_test[$i2]['unit'] == "lbs") {
            $compare3['prodcups'][] = findCups($rq_test[$i2]['5cups'], $rq_test[$i2]['unit'], "1") * $rq_test[$i2]['size'];
            $compare3['percent'][] = $bf_grand / findCups($rq_test[$i2]['5cups'], $rq_test[$i2]['unit'], $rq_test[$i2]['size']); // * $prodcups2['size;
        } else {
            $compare3['prodcups'][] = findCups($rq_test[$i2]['size'], $rq_test[$i2]['unit'], "1");
            $compare3['percent'][] = $bf_grand / findCups($rq_test[$i2]['size'], $rq_test[$i2]['unit'], $rq_test[$i2]['size']); // * $prodcups2['size;
        }

        $countUp2++;
        $i2++;
    }
/*
echo "<h1>COMPARE3.0</h1><pre>";
print_r($compare3);
echo "</pre>";
 */
    $rb_bf[] = getBagsize($compare3, $bf_grand);
//print_r(getBagsize($compare3,$bf_grand));
    /*
    echo "<br><h3>Product Family</h3><br><pre>";
    print_r($rb_bf);
    echo "</pre>";
     */
    $bf_grand = 0;
    $countUp++;
    $i++;
}
; // End While

/*

Array Buildout for Blended Addatives

 */

// Loop Results (Explode Response to break out pot info)
$grabBlend = "SELECT count(*) FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'RB' AND `ba` LIKE '1'";
$grabProd = $dbconn->query($grabBlend);
$count_ba = $grabProd->fetch_array(MYSQLI_NUM);

$countUp = '1';
$i = '0';
while ($count_ba[0] >= $countUp) {
    $grabba = "SELECT * FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'RB' AND `ba` LIKE '1' LIMIT 1 OFFSET " . $i;
    $grabPf = $dbconn->query($grabba);
    $baGrab = $grabPf->fetch_array(MYSQLI_NUM);
/*
echo "<h1>LOOK AT ME</h1><pre>";
print_r($baGrab);
echo "</pre>";
echo "<br>(".$countUp."/".$count_ba[0].")This is a Pull: " . $baGrab[2];
 */
    foreach ($rb_configs as &$rb_info) {
        $p_info = explode("|", $rb_info);
//New ArrayID
        $arrayID = $p_info[1] . "x" . $p_info[2] . "x" . $p_info[3];
        $ba_grand += $ba_totals[$arrayID][$baGrab[2]];
    }
    ; // End foreach
    $ba_gt[$baGrab[2]] = $ba_grand;

    $rq_test = getProductFam($baGrab[2]);
/*
echo "<br><h3>GET Product Family</h3><br><pre>";
print_r($rq_test);
echo "</pre>";
 */
    $count_ba2 = count($rq_test);
    $countUp3 = 1;
    $i3 = 0;
    $compare4 = '';
    while ($countUp3 <= $count_ba2) {

//echo "<br><h3>Product Family</h3><br><pre>";
        //print_r($rq_test[0]);
        //echo "</pre>";

//echo "<br>Current Count: ".$count_ba2."/".$countUp3."<br>";
        $compare4['id'][] = $rq_test[$i3]['id']; //$prods;
        $compare4['size'][] = $rq_test[$i3]['size'];
        $compare4['units'][] = $rq_test[$i3]['unit'];

//echo "<h1>".$ba_grand."</h1>";
        //echo "<h1>Something: ".findCups($rq_test[$i3]['size'],$rq_test[$i3]['unit'],$rq_test[$i3]['size'])."</h1>";
        if ($rq_test[$i3]['unit'] == "lbs") {

            //echo "<h1>LBS2CUPS:</h1> ".findCups($rq_test[$i3]['5cups'],$rq_test[$i3]['unit'],"1");
            $compare4['prodcups'][] = findCups($rq_test[$i3]['5cups'], $rq_test[$i3]['unit'], "1") * $rq_test[$i3]['size'];
            $compare4['percent'][] = $ba_grand / findCups($rq_test[$i3]['5cups'], $rq_test[$i3]['unit'], $rq_test[$i3]['size']); // * $prodcups2['size;
        } else {
//echo "<br>This is 5: ".$baGrab[5];
            //echo "<br>This is 6: ".$baGrab[6];
            $compare4['prodcups'][] = findCups($rq_test[$i3]['size'], $rq_test[$i3]['unit'], "1") * $rq_test[$i3]['size'];
            $compare4['percent'][] = $ba_grand / findCups($rq_test[$i3]['size'], $rq_test[$i3]['unit'], $rq_test[$i3]['size']); //* $prodcups2['size'];
            //  $compare4['prodcups'][] = findCups($baGrab[5],$baGrab[6],$baGrab[5]);
            //  $compare4['percent'][] = $baGrab[5] / findCups('1','cuft','1') * 100;// * $prodcups2['size;
        }
//echo "<pre>";
        //print_r($compare4);
        //echo "</pre>";

        $countUp3++;
        $i3++;
    }
/*
echo "<br>Compare4:";
echo "<br><pre>";
print_r($compare4);
echo "</pre><br>BG: ".$ba_grand;
echo "<br>Bag Size: ".getbagsize($compare4,$ba_grand);
 */
    $rb_ba[] = getbagsize($compare4, $ba_grand);
// End ba Loop

//print_r(getbagsize($compare4,$ba_grand));
    /*
    echo "<br><h3>Product Family</h3><br><pre>";
    print_r($rb_ba);
    echo "</pre>";
     */
    $ba_grand = 0;
    $countUp++;
    $i++;
}
;

/*

Array Buildout for Surface Added

 */
//echo "<h1>Surface Added</h1>";
// Loop Results (Explode Response to break out pot info)
$grabBlend = "SELECT count(*) FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'RB' AND `sa` LIKE '1'";
$grabProd = $dbconn->query($grabBlend);
$count_sa = $grabProd->fetch_array(MYSQLI_NUM);

$countUp = '1';
$i = '0';
while ($count_sa[0] >= $countUp) {
    $grabsa = "SELECT * FROM `apprates` WHERE `storeid` = " . $arr['storeid'] . " AND `type` LIKE 'RB' AND `sa` LIKE '1' LIMIT 1 OFFSET " . $i;
    $grabPf = $dbconn->query($grabsa);
    $saGrab = $grabPf->fetch_array(MYSQLI_NUM);

//echo "<br>(".$countUp."/".$count_sa[0].")This is a Pull: " . $saGrab[2];

    foreach ($rb_configs as &$rb_info) {
        $p_info = explode("|", $rb_info);
//New ArrayID
        $arrayID = $p_info[1] . "x" . $p_info[2] . "x" . $p_info[3];
//echo "<br>Value for SA_GRAND: ".$sa_totals[$arrayID][$saGrab[2]];
        $sa_grand += $sa_totals[$arrayID][$saGrab[2]];
    }
    ; // End foreach
    $sa_gt[$saGrab[2]] = $sa_grand;

    $rq_test = getProductFam($saGrab[2]);
    $count_sa2 = count($rq_test);
    $countUp4 = 1;
    $i4 = 0;
    while ($countUp4 <= $count_sa2) {
/*
echo "<br><h3>Product Family</h3><br><pre>";
print_r($rq_test[0]);
echo "</pre>";
 */
//echo "<br>Current Count: ".$count_sa2."/".$countUp4."<br>";
        $compare5['id'][] = $rq_test[$i4]['id']; //$prods;
        $compare5['size'][] = $rq_test[$i4]['size'];
        $compare5['units'][] = $rq_test[$i4]['unit'];
/*
echo "<h1>SA Grand: ".$sa_grand."</h1>";
echo "<h1>Into Cups: ".$rq_test[$i4]['size'] ." - ".$rq_test[$i4]['unit']." - ".$rq_test[$i4]['size']."</h1>";
 */
        if ($rq_test[$i4]['unit'] == "lbs") {
            $compare5['prodcups'][] = findCups($rq_test[$i4]['5cups'], $rq_test[$i4]['unit'], "1") * $rq_test[$i4]['size'];
            $compare5['percent'][] = $sa_grand / findCups($rq_test[$i4]['5cups'], $rq_test[$i4]['unit'], $rq_test[$i4]['size']); // * $prodcups2['size;
        } else {
            $compare5['prodcups'][] = findCups($rq_test[$i4]['size'], $rq_test[$i4]['unit'], "1");
            $compare5['percent'][] = $sa_grand / findCups($rq_test[$i4]['size'], $rq_test[$i4]['unit'], "1"); // * $prodcups2['size;
        }
/*
echo "<h1>COMPARE5</h1><br><pre>";
print_r($compare5);
echo "</pre>";
 */
        $countUp4++;
        $i4++;
    }
    $rb_sa[] = getBagsize($compare5, $sa_grand);
// End sa Loop

//print_r(getsagsize($compare5,$sa_grand));
    /*
    echo "<br><h3>Product Family</h3><br><pre>";
    print_r($rb_sa);
    echo "</pre>";
     */
    $compare5 = '';
    $sa_grand = 0;
    $countUp++;
    $i++;
}
;

/*
echo "RAISEDBEDS:<pre>";
print_r($rb_configs);
echo "</pre>";
 */

$rbBag_bf = $rb_bf;
$rbBag_ba = $rb_ba;
$rbBag_sa = $rb_sa;
/*
echo "<br><h3>Going to Shopping List</h3><br><pre>";
print_r($IGFBag);
print_r($IGOBag);
print_r($potsBag_bf);
print_r($potsBag_ba);
print_r($potsBag_sa);
print_r($rbBag_bf);
print_r($rbBag_ba);
print_r($rbBag_sa);
echo "</pre>";
 */
$super_array = '';
$super_array = array_merge($IGFBag, $IGOBag, $potsBag_bf, $potsBag_ba, $potsBag_ea, $potsBag_sa, $rbBag_bf, $rbBag_ba, $rbBag_sa);
/*
echo "<h1>SUPER ARRAY!!!!</h1><br><pre>";
print_r($super_array);
echo "</pre>";
 */
//echo "<h1>Prdocut ID List:</h1><br>";
foreach ($super_array as $prod_list) {
    $build_list = explode("|", $prod_list);
    $products[] = $build_list[3];
}
;

$total = count($super_array);
$i = "0";
$thin_list = array_unique($products);
//echo "Total # of Rates: ".$total;
foreach ($thin_list as $prod) {
    $i = "0";
//echo "<br>Prod:".$prod."<br>";
    while ($total >= $i) {
/*
echo $total ." / ".$i."<br>";
echo "Rates: ".$rates[$i];
 */
        $breakout = explode("|", $super_array[$i]);
        //echo "<br>This is the exploded value: ".$breakout[0]."<br>";
        if ($prod == $breakout[3]) {
            //  echo "Breakout Value: ".$breakout[0]."<br>";
            $grand_total += $breakout[0];
        }

        $i++;
    }
    $super_duper[] = $grand_total . "|" . $prod;
    $i = 0;
    $grand_total = 0;
}
/*
echo "<h1>SUPER ARRAY!!!!</h1><br><pre>";
print_r($super_duper);
echo "</pre><br>";
 */

/*
$_SESSION['IGF'] = $IGFBag;
$_SESSION['IGO'] = $IGOBag;
$_SESSION['POTS_BF'] = $potsBag_bf;
$_SESSION['POTS_BA'] = $potsBag_ba;
$_SESSION['POTS_SA'] = $potsBag_sa;
$_SESSION['POTS_EA'] = $potsBag_ea;
$_SESSION['RB'] = $rbBag;
 */
$_SESSION['STORE'] = $arr['storeid'];
$_SESSION['SUPER'] = $super_duper;
/*
echo "<pre>";
print_r($arr);
echo "</pre>";
 */

/*
foreach ($arr as $key => $value) {
echo "{$key} => {$value} <br>";
}
 */
