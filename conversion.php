<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security
/* Conversion Map
Math is the usual operators if you are not familar, + - / * - however if you are wanting to cube a value use ** to do square/qubed - First "switch" is the input
type so the value coming in to be converted to CI -> cuft you'd goto "case CI" then scroll down to cuft to adjust the template for that conversion.
 */

// CONVERSION: AREA
function getArea($in_value, $in_input, $in_output)
{
    if ($in_input == "sqin") {
        return $in_value / 144;
    }
    ;
    if ($in_input == "sqft") {
        $pre_calc = 12 ** 2;
        return $in_value * $pre_calc;
    }
    ;
}; // End getArea

// CONVERSION: VOLUME
function getVolume($in_value, $in_input, $in_output)
{
    // var_dump($in_output);
    $in_input = strtolower($in_input);
    $in_output = strtolower($in_output);
    switch ($in_input) {
        case "ci":
            // All inputs for Cubic Inches
            switch ($in_output) {
                case "ci":
                    return $in_value * 1;
                    break;
                case "cuft":
                    $pre_calc = 1 / 12;
                    return $in_value * $pre_calc ** 3;
                    break;
                case "cy":
                    $pre_calc = 1 / 36;
                    return $in_value * $pre_calc ** 3;
                    break;
                case "pt-d":
                    return $in_value * 0.029761627764;
                    break;
                case "qt-d":
                    return $in_value * 0.014880813882;
                    break;
                case "tsp":
                    return $in_value / 231 * 128 * 6;
                    break;
                case "tbls":
                case "tbl":
                    return $in_value / 231 * 128 * 2;
                    break;
                case "floz":
                    return $in_value / 231 * 128;
                    break;
                case "cup":
                    return $in_value / 231 * 16;
                    break;
                case "pt-l":
                    return $in_value / 231 * 8;
                    break;
                case "qt-l":
                    return $in_value / 231 * 4;
                    break;
                case "gal":
                    return $in_value / 231;
                    break;
                case "cc":
                    $pre_calc = 2.54 ** 3;
                    return $in_value * $pre_calc;
                    break;
                case "ml":
                    $pre_calc = 2.54 ** 3;
                    return $in_value * $pre_calc;
                    break;
                case "l":
                    $pre_calc = 2.54 ** 3;
                    return $in_value * $pre_calc / 1000;
                    break;
            }
            break;

        case "cuft":
            // All inputs for Cubic Feet
            switch ($in_output) {
                case "cuft":
                    return $in_value * 1;
                    break;
                case "ci":
                    $pre_calc = 12 ** 3;
                    return $in_value * $pre_calc;
                    break;
                case "cy":
                    $pre_calc = 3 ** 3;
                    return $in_value / $pre_calc;
                    break;
                case "pt-d":
                    return $in_value * 51.428092776;
                    break;
                case "qt-d":
                    return $in_value * 25.714046388;
                    break;
                case "tsp":
                    $pre_calc = 12 ** 3;
                    return $in_value * $pre_calc / 231 * 128 * 6;
                    break;
                case "tbls":
                case "tbl":
                    $pre_calc = 12 ** 3;
                    return $in_value * $pre_calc / 231 * 128 * 2;
                    break;
                case "floz":
                    $pre_calc = 12 ** 3;
                    return $in_value * $pre_calc / 231 * 128;
                    break;
                case "cup":
                    $pre_calc = 12 ** 3;
                    return $in_value * $pre_calc / 231 * 16;
                    break;
                case "pt-l":
                    $pre_calc = 12 ** 3;
                    return $in_value * $pre_calc / 231 * 8;
                    break;
                case "qt-l":
                    $pre_calc = 12 ** 3;
                    return $in_value * $pre_calc / 231 * 4;
                    break;
                case "gal":
                    $pre_calc = 12 ** 3;
                    return $in_value * $pre_calc / 231;
                    break;
                case "cc":
                    $pre_calc = 12 * 2.54;
                    return $in_value * $pre_calc ** 3;
                    break;
                case "ml":
                    $pre_calc = 12 * 2.54;
                    return $in_value * $pre_calc ** 3;
                    break;
                case "l":
                    $pre_calc = 12 * 2.54;
                    return $in_value * $pre_calc ** 3 / 1000;
                    break;
            }
            break;

        case "cy":
            // All inputs for Cubic Yards
            switch ($in_output) {
                case "cy":
                    return $in_value * 1;
                    break;
                case "ci":
                    $pre_calc = 36 ** 3;
                    return $in_value * $pre_calc;
                    break;
                case "cuft":
                    $pre_calc = 36 ** 3;
                    return $in_value * $pre_calc;
                    break;
                case "pt-d":
                    return $in_value * 1388.558505;
                    break;
                case "qt-d":
                    return $in_value * 694.27925248;
                    break;
                case "tsp":
                    $pre_calc = 36 ** 3;
                    return $in_value * $pre_calc / 231 * 128 * 6;
                    break;
                case "tbls":
                case "tbl":
                    $pre_calc = 36 ** 3;
                    return $in_value * $pre_calc / 231 * 128 * 2;
                    break;
                case "floz":
                    $pre_calc = 36 ** 3;
                    return $in_value * $pre_calc / 231 * 128;
                    break;
                case "cup":
                    $pre_calc = 36 ** 3;
                    return $in_value * $pre_calc / 231 * 16;
                    break;
                case "pt-l":
                    $pre_calc = 36 ** 3;
                    return $in_value * $pre_calc / 231 * 8;
                    break;
                case "qt-l":
                    $pre_calc = 36 ** 3;
                    return $in_value * $pre_calc / 231 * 4;
                    break;
                case "gal":
                    $pre_calc = 36 ** 3;
                    return $in_value * $pre_calc / 231;
                    break;
                case "cc":
                    $pre_calc = 36 * 2.54;
                    return $in_value * $pre_calc ** 3;
                    break;
                case "ml":
                    $pre_calc = 36 * 2.54;
                    return $in_value * $pre_calc ** 3;
                    break;
                case "l":
                    $pre_calc = 36 * 2.54;
                    return $in_value * $pre_calc ** 3 / 1000;
                    break;
            }
            break;

        case "pt-d":
            // All inputs for Pints Dry
            switch ($in_output) {
                case "pt-d":
                    return $in_value * 1;
                    break;
                case "ci":
                    return $in_value * 33.600312722;
                    break;
                case "cuft":
                    return $in_value * 0.019444625418;
                    break;
                case "cy":
                    return $in_value * 0.00072017131178;
                    break;
                case "qt-d":
                    return $in_value / 2;
                    break;
                case "tsp":
                    return $in_value * 111.71013061;
                    break;
                case "tbls":
                case "tbl":
                    return $in_value * 37.236710204;
                    break;
                case "floz":
                    return $in_value * 18.618355101;
                    break;
                case "cup":
                    return $in_value * 2.3272943877;
                    break;
                case "pt-l":
                    return $in_value * 1.1636471938;
                    break;
                case "qt-l":
                    return $in_value * 0.58182359692;
                    break;
                case "gal":
                    return $in_value * 0.14545589923;
                    break;
                case "cc":
                    return $in_value * 550.610475;
                    break;
                case "ml":
                    return $in_value * 550.610475;
                    break;
                case "l":
                    return $in_value * 0.5506105;
                    break;
            }
            break;

        case "qt-d":
            // All inputs for Quart Dry
            switch ($in_output) {
                case "qt-d":
                    return $in_value * 1;
                    break;
                case "ci":
                    return $in_value * 67.200625445;
                    break;
                case "cuft":
                    return $in_value * 0.038889250836;
                    break;
                case "cy":
                    return $in_value * 0.0014403426236;
                    break;
                case "pt-d":
                    return $in_value * 2;
                    break;
                case "tsp":
                    return $in_value * 223.42026122;
                    break;
                case "tbls":
                case "tbl":
                    return $in_value * 74.473420407;
                    break;
                case "floz":
                    return $in_value * 37.236710202;
                    break;
                case "cup":
                    return $in_value * 4.6545887754;
                    break;
                case "pt-l":
                    return $in_value * 2.3272943877;
                    break;
                case "qt-l":
                    return $in_value * 1.1636471938;
                    break;
                case "gal":
                    return $in_value * 0.29091179846;
                    break;
                case "cc":
                    return $in_value * 1101.22095;
                    break;
                case "ml":
                    return $in_value * 1101.22095;
                    break;
                case "l":
                    return $in_value * 1.10122095;
                    break;
            }
            break;

        case "tsp":
            // All inputs for Teaspoons
            switch ($in_output) {
                case "tsp":
                    return $in_value * 1;
                    break;
                case "ci":
                    return $in_value / 6 / 128 * 231;
                    break;
                case "cuft":
                    $pre_calc = 12 ** 3;
                    return $in_value / 6 / 128 * 231 / $pre_calc;
                    break;
                case "cy":
                    $pre_calc = 36 ** 3;
                    return $in_value / 6 / 128 * 231 / $pre_calc;
                    break;
                case "pt-d":
                    return $in_value * 0.008951739601;
                    break;
                case "qt-d":
                    return $in_value * 223.42026122;
                    break;
                case "tbls":
                case "tbl":
                    return $in_value / 3;
                    break;
                case "floz":
                    return $in_value / 6;
                    break;
                case "cup":
                    return $in_value / 6 / 8;
                    break;
                case "pt-l":
                    return $in_value / 6 / 16;
                    break;
                case "qt-l":
                    return $in_value / 6 / 32;
                    break;
                case "gal":
                    return $in_value / 6 / 128;
                    break;
                case "cc":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 6 / 128 * 231 * $pre_calc;
                    break;
                case "ml":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 6 / 128 * 231 * $pre_calc;
                    break;
                case "l":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 6 / 128 * 231 * $pre_calc / 1000;
                    break;
            }
            break;

        case "tbls":
        case "tbl":
            // All inputs for Tablespoons
            switch ($in_output) {
                case "tbls":
                case "tbl":
                    return $in_value * 1;
                    break;
                case "ci":
                    return $in_value / 2 / 128 * 231;
                    break;
                case "cuft":
                    $pre_calc = 12 ** 3;
                    return $in_value / 2 / 128 * 231 / $pre_calc;
                    break;
                case "cy":
                    $pre_calc = 36 ** 3;
                    return $in_value / 2 / 128 * 231 / $pre_calc;
                    break;
                case "pt-d":
                    return $in_value * 0.026855218802;
                    break;
                case "qt-d":
                    return $in_value * 0.013427609401;
                    break;
                case "tsp":
                    return $in_value * 3;
                    break;
                case "floz":
                    return $in_value / 2;
                    break;
                case "cup":
                    return $in_value / 2 / 8;
                    break;
                case "pt-l":
                    return $in_value / 2 / 16;
                    break;
                case "qt-l":
                    return $in_value / 2 / 32;
                    break;
                case "gal":
                    return $in_value / 2 / 128;
                    break;
                case "cc":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 2 / 128 * 231 * $pre_calc;
                    break;
                case "ml":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 2 / 128 * 231 * $pre_calc;
                    break;
                case "l":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 2 / 128 * 231 * $pre_calc / 1000;
                    break;
            }
            break;

        case "floz":
            // All inputs for Fluid Ounce
            switch ($in_output) {
                case "floz":
                    return $in_value * 1;
                    break;
                case "ci":
                    return $in_value / 128 * 231;
                    break;
                case "cuft":
                    $pre_calc = 12 ** 3;
                    return $in_value / 128 * 231 / $pre_calc;
                    break;
                case "cy":
                    $pre_calc = 36 ** 3;
                    return $in_value / 128 * 231 / $pre_calc;
                    break;
                case "pt-d":
                    return $in_value * 0.053710437607;
                    break;
                case "qt-d":
                    return $in_value * 0.026855218803;
                    break;
                case "tsp":
                    return $in_value * 6;
                    break;
                case "tbls":
                case "tbl":
                    return $in_value * 2;
                    break;
                case "cup":
                    return $in_value / 8;
                    break;
                case "pt-l":
                    return $in_value / 16;
                    break;
                case "qt-l":
                    return $in_value / 32;
                    break;
                case "gal":
                    return $in_value / 128;
                    break;
                case "cc":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 128 * 231 * $pre_calc;
                    break;
                case "ml":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 128 * 231 * $pre_calc;
                    break;
                case "l":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 128 * 231 * $pre_calc / 1000;
                    break;
            }
            break;

        case "cup":
        case "cups":
            // All inputs for Cups
            switch ($in_output) {
                case "cup":
                    return $in_value * 1;
                    break;
                case "ci":
                    return $in_value / 16 * 231;
                    break;
                case "cuft":
                    $pre_calc = 12 ** 3;
                    return $in_value / 16 * 231 / $pre_calc;
                    break;
                case "cy":
                    $pre_calc = 36 ** 3;
                    return $in_value / 16 * 231 / $pre_calc;
                    break;
                case "pt-d":
                    return $in_value * 0.42968350085;
                    break;
                case "qt-d":
                    return $in_value * 0.21484175042;
                    break;
                case "tsp":
                    return $in_value * 8 * 6;
                    break;
                case "tbls":
                case "tbl":
                    return $in_value * 8 * 2;
                    break;
                case "floz":
                    return $in_value * 8;
                    break;
                case "pt-l":
                    return $in_value / 2;
                    break;
                case "qt-l":
                    return $in_value / 4;
                    break;
                case "gal":
                    return $in_value / 16;
                    break;
                case "cc":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 16 * 231 * $pre_calc;
                    break;
                case "ml":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 16 * 231 * $pre_calc;
                    // return $in_value * 236.588;
                    break;
                case "l":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 16 * 231 * $pre_calc / 1000;
                    // return $in_value * 0.236588;
                    break;
            }
            break;

        case "pt-l":
            // All inputs for Pints (Liquid)
            switch ($in_output) {
                case "pt-l":
                    return $in_value * 1;
                    break;
                case "ci":
                    return $in_value / 8 * 231;
                    break;
                case "cuft":
                    $pre_calc = 12 ** 3;
                    return $in_value / 8 * 231 / $pre_calc;
                    break;
                case "cy":
                    $pre_calc = 36 ** 3;
                    return $in_value / 8 * 231 / $pre_calc;
                    break;
                case "pt-d":
                    return $in_value * 0.85936700169;
                    break;
                case "qt-d":
                    return $in_value * 0.42968350085;
                    break;
                case "tsp":
                    return $in_value * 16 * 6;
                    break;
                case "tbls":
                case "tbl":
                    return $in_value * 16 * 2;
                    break;
                case "floz":
                    return $in_value * 16;
                    break;
                case "cup":
                    return $in_value * 2;
                    break;
                case "qt-l":
                    return $in_value / 2;
                    break;
                case "gal":
                    return $in_value / 8;
                    break;
                case "cc":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 8 * 231 * $pre_calc;
                    break;
                case "ml":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 8 * 231 * $pre_calc;
                    break;
                case "l":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 8 * 231 * $pre_calc / 1000;
                    break;
            }
            break;

        case "qt-l":
            // All inputs for Quarts (Liquid)
            switch ($in_output) {
                case "qt-l":
                    return $in_value * 1;
                    break;
                case "ci":
                    return $in_value / 4 * 231;
                    break;
                case "cuft":
                    $pre_calc = 12 ** 3;
                    return $in_value / 4 * 231 / $pre_calc;
                    // return $in_value * 0.0334201;
                    break;
                case "cy":
                    $pre_calc = 36 ** 3;
                    return $in_value / 4 * 231 / $pre_calc;
                    break;
                case "pt-d":
                    return $in_value * 1.7187340034;
                    break;
                case "qt-d":
                    return $in_value * 0.85936700169;
                    break;
                case "tsp":
                    return $in_value * 32 * 6;
                    break;
                case "tbls":
                case "tbl":
                    return $in_value * 32 * 2;
                    break;
                case "floz":
                    return $in_value * 32;
                    break;
                case "cup":
                    return $in_value * 4;
                    break;
                case "pt-l":
                    return $in_value * 2;
                    break;
                case "gal":
                    return $in_value / 4;
                    break;
                case "cc":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 4 * 231 * $pre_calc;
                    break;
                case "ml":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 4 * 231 * $pre_calc;
                    break;
                case "l":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 4 * 231 * $pre_calc / 1000;
                    break;
            }
            break;

        case "gal":
            // All inputs for Gallons (Liquid)
            switch ($in_output) {
                case "gal":
                    return $in_value * 1;
                    break;
                case "ci":
                    return $in_value * 231;
                    break;
                case "cuft":
                    $pre_calc = 12 ** 3;
                    return $in_value * 231 / $pre_calc;
                    break;
                case "cy":
                    $pre_calc = 36 ** 3;
                    return $in_value * 231 / $pre_calc;
                    break;
                case "pt-d":
                    return $in_value * 6.8749360135;
                    break;
                case "qt-d":
                    return $in_value * 3.4374680068;
                    break;
                case "tsp":
                    return $in_value * 128 * 6;
                    break;
                case "tbls":
                case "tbl":
                    return $in_value * 128 * 2;
                    break;
                case "floz":
                    return $in_value * 128;
                    break;
                case "cup":
                    return $in_value * 16;
                    break;
                case "pt-l":
                    return $in_value * 8;
                    break;
                case "qt-l":
                    return $in_value * 4;
                    break;
                case "cc":
                    $pre_calc = 2.54 ** 3;
                    return $in_value * 231 * $pre_calc;
                    break;
                case "ml":
                    $pre_calc = 2.54 ** 3;
                    return $in_value * 231 * $pre_calc;
                    break;
                case "l":
                    $pre_calc = 2.54 ** 3;
                    return $in_value * 231 * $pre_calc / 1000;
                    break;
            }
            break;

        case "cc":
            // All inputs for Cubic Centimeters
            switch ($in_output) {
                case "cc":
                    return $in_value * 1;
                    break;
                case "ci":
                    $pre_calc = 25.4 ** 3;
                    return $in_value * 1000 / $pre_calc;
                    break;
                case "cuft":
                    $pre_calc = 25.4 * 12;
                    return $in_value * 1000 / $pre_calc ** 3;
                    break;
                case "cy":
                    $pre_calc = 25.4 * 36;
                    return $in_value * 1000 / $pre_calc ** 3;
                    break;
                case "pt-d":
                    return $in_value * 0.0018161659565;
                    break;
                case "qt-d":
                    return $in_value * 0.00090808297826;
                    break;
                case "tsp":
                    $pre_calc = 25.4 ** 3;
                    return $in_value * 1000 / $pre_calc / 231 * 128 * 6;
                    break;
                case "tbls":
                case "tbl":
                    $pre_calc = 25.4 ** 3;
                    return $in_value * 1000 / $pre_calc / 231 * 128 * 2;
                    break;
                case "floz":
                    $pre_calc = 25.4 ** 3;
                    return $in_value * 1000 / $pre_calc / 231 * 128;
                    break;
                case "cup":
                    $pre_calc = 25.4 ** 3;
                    return $in_value * 1000 / $pre_calc / 231 * 16;
                    break;
                case "pt-l":
                    $pre_calc = 25.4 ** 3;
                    return $in_value * 1000 / $pre_calc / 231 * 8;
                    break;
                case "qt-l":
                    $pre_calc = 25.4 ** 3;
                    return $in_value * 1000 / $pre_calc / 231 * 4;
                    break;
                case "gal":
                    $pre_calc = 2.54 ** 3;
                    return $in_value * 1000 / $pre_calc / 231;
                    break;
                case "ml":
                    $pre_calc = 2.54 ** 3;
                    return $in_value * 1;
                    break;
                case "l":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 1000;
                    break;
            }
            break;

        case "ml":
            // All inputs for Milimeters
            switch ($in_output) {
                case "ml":
                    return $in_value * 1;
                    break;
                case "ci":
                    $pre_calc = 10 / 25.4;
                    return $in_value * $pre_calc ** 3;
                    break;
                case "cuft":
                    $pre_calc = 10 / 25.4 / 12;
                    return $in_value * $pre_calc ** 3;
                    break;
                case "cy":
                    $pre_calc = 10 / 25.4 / 36;
                    return $in_value * $pre_calc ** 3;
                    break;
                case "pt-d":
                    return $in_value * 0.0018161659565;
                    break;
                case "qt-d":
                    return $in_value * 0.00090808297826;
                    break;
                case "tsp":
                    $pre_calc = 10 / 25.4;
                    return $in_value * $pre_calc ** 3 / 231 * 128 * 6;
                    break;
                case "tbls":
                case "tbl":
                    $pre_calc = 10 / 25.4;
                    return $in_value * $pre_calc ** 3 / 231 * 128 * 2;
                    break;
                case "floz":
                    $pre_calc = 10 / 25.4;
                    return $in_value * $pre_calc ** 3 / 231 * 128;
                    break;
                case "cup":
                    $pre_calc = 10 / 25.4;
                    return $in_value * $pre_calc ** 3 / 231 * 16;
                    break;
                case "pt-l":
                    $pre_calc = 10 / 25.4;
                    return $in_value * $pre_calc ** 3 / 231 * 8;
                    break;
                case "qt-l":
                    $pre_calc = 10 / 25.4;
                    return $in_value * $pre_calc ** 3 / 231 * 4;
                    break;
                case "gal":
                    $pre_calc = 10 / 25.4;
                    return $in_value * $pre_calc ** 3 / 231;
                    break;
                case "cc":
                    return $in_value * 1;
                    break;
                case "l":
                    $pre_calc = 2.54 ** 3;
                    return $in_value / 1000;
                    break;
            }
            break;

        case "l":
            // All inputs for Liters
            switch ($in_output) {
                case "l":
                    return $in_value * 1;
                    break;
                case "ci":
                    $pre_calc = 10 / 25.4;
                    return $in_value * 1000 * $pre_calc ** 3;
                    break;
                case "cuft":
                    $pre_calc = 10 / 25.4 / 12;
                    return $in_value * 1000 * $pre_calc ** 3;
                    break;
                case "cy":
                    $pre_calc = 10 / 25.4 / 36;
                    return $in_value * 1000 * $pre_calc ** 3;
                    break;
                case "pt-d":
                    return $in_value * 1.8161659565;
                    break;
                case "qt-d":
                    return $in_value * 0.90808297826;
                    break;
                case "tsp":
                    $pre_calc = 10 / 25.4;
                    return $in_value * 1000 * $pre_calc ** 3 / 231 * 128 * 6;
                    break;
                case "tbls":
                case "tbl":
                    $pre_calc = 10 / 25.4;
                    return $in_value * 1000 * $pre_calc ** 3 / 231 * 128 * 2;
                    break;
                case "floz":
                    $pre_calc = 10 / 25.4;
                    return $in_value * 1000 * $pre_calc ** 3 / 231 * 128;
                    break;
                case "cup":
                    $pre_calc = 10 / 25.4;
                    return $in_value * 1000 * $pre_calc ** 3 / 231 * 16;
                    break;
                case "pt-l":
                    $pre_calc = 10 / 25.4;
                    return $in_value * 1000 * $pre_calc ** 3 / 231 * 8;
                    break;
                case "qt-l":
                    $pre_calc = 10 / 25.4;
                    return $in_value * 1000 * $pre_calc ** 3 / 231 * 4;
                    break;
                case "gal":
                    $pre_calc = 10 / 25.4;
                    return $in_value * 1000 * $pre_calc ** 3 / 231;
                    break;
                case "cc":
                    return $in_value * 1000;
                    break;
                case "ml":
                    return $in_value * 1000;
                    break;
            }
            break;

    }
} // End Function

function getMass($in_value, $in_input, $in_output)
{
    switch ($in_input) {
        case "oz":
            // All inputs for Ounces
            switch ($in_output) {
                case "oz":
                    return $in_value * 1;
                    break;
                case "lbs":
                case "lb":
                    return $in_value / 166;
                    break;
                case "g":
                    return $in_value * 28.349523125;
                    break;
                case "kg":
                    return $in_value * 28.349523125 / 1000;
                    break;
            }
            break;

        case "lbs":
        case "lb":
            // All Inputs in Pounds
            switch ($in_output) {
                case "lbs":
                case "lb":
                    return $in_value * 1;
                    break;
                case "oz":
                    return $in_value * 16;
                    break;
                case "g":
                    return $in_value * 16 * 28.349523125;
                    break;
                case "kg":
                    return $in_value * 16 * 28.349523125 / 1000;
                    break;
            }
            break;


        case "g":
            // All Inputs in Pounds
            switch ($in_output) {
                case "g":
                    return $in_value * 1;
                    break;
                case "oz":
                    return $in_value / 28.349523125;
                    break;
                case "lbs":
                case "lb":
                    return $in_value / 28.349523125 / 16;
                    break;
                case "kg":
                    return $in_value / 1000;
                    break;
            }
        case "kg":
            // All Inputs in Pounds
            switch ($in_output) {
                case "kg":
                    return $in_value * 1;
                    break;
                case "oz":
                    return $in_value * 1000 / 28.349523125;
                    break;
                case "lbs":
                case "lb":
                case "lb":
                    return $in_value * 1000 / 28.349523125 / 16;
                    break;
                case "g":
                    return $in_value * 1000;
                    break;
            }
            break;
    };
};
// CONVERSION: DENSITY
function getDensity($in_value, $in_output)
{
    // var_dump($in_value . ": " . $in_output);
    $pre_calc = getVolume("5", "cup", $in_output);
    return $pre_calc / $in_value;
};

//IN GROUND QTY PER PLANT
function getIGQ_plant($p_size, $p_unit, $p_def, $c_input, $c_unit)
{
    $per_cont = $p_size / $p_def;
    if ($p_unit == $c_unit) {
        return $per_cont;
    } else {
        return getVolume("1", $c_unit, $p_unit) * $c_input / $per_cont;
    }
} // End In-Ground QTY plant

// IN GROUND QTY PER PRODUCT
// getIGQ_prod(12,"cuft",10,12,"qt-d")*1;
function getIGQ_prod($p_size, $p_unit, $p_def, $c_input, $c_unit)
{
    $per_cont = $p_size * $p_def;
    if ($p_unit == $c_unit) {
        return $per_cont;
    } else {
        return getVolume("1", $p_unit, $c_unit) * $per_cont / $c_input;
    }

} // End In-Ground QTY product

// BUILD FRACTION WITH WHOLE NUMBER SPLIT NOT HUGE FRACTION
function getFraction($total)
{
    // Reduce to a single Decimal point.
    $total = number_format($total, 1, '.', '');
    // Break it up at the decimal, whole number seperated so the decimal can be fractioned.
    $fraction = explode(".", $total);
    if ($fraction[1] == "" || $fraction[1] == "0") {
        return $fraction[0];
    } else {
        return $fraction[0] . " " . dec2frac("0.$fraction[1]");
    }
}

// MAKE FRACTIONS
function dec2frac($num = 0.0, $err = 0.001)
{
    if ($err <= 0.0 || $err >= 1.0) {
        $err = 0.001;
    }

    $sign = ($num > 0) ? 1 : (($num < 0) ? -1 : 0);

    if ($sign === -1) {
        $num = abs($num);
    }

    if ($sign !== 0) {
        // $err is the maximum relative $err; convert to absolute
        $err *= $num;
    }

    $n = (int) floor($num);
    $num -= $n;

    if ($num < $err) {
        return (string) ($sign * $n);
    }

    if (1 - $err < $num) {
        return (string) ($sign * ($n + 1));
    }

    // The lower fraction is 0/1
    $lower_n = 0;
    $lower_d = 1;

    // The upper fraction is 1/1
    $upper_n = 1;
    $upper_d = 1;

    while (true) {
        // The middle fraction is ($lower_n + $upper_n) / (lower_d + $upper_d)
        $middle_n = $lower_n + $upper_n;
        $middle_d = $lower_d + $upper_d;

        if ($middle_d * ($num + $err) < $middle_n) {
            // real + $err < middle : middle is our new upper
            $upper_n = $middle_n;
            $upper_d = $middle_d;
        } elseif ($middle_n < ($num - $err) * $middle_d) {
            // middle < real - $err : middle is our new lower
            $lower_n = $middle_n;
            $lower_d = $middle_d;
        } else {
            // Middle is our best fraction
            return (string) (($n * $middle_d + $middle_n) * $sign) . '&frasl;' . (string) $middle_d;
        }
    }

    return '???'; // should be unreachable.
}

// INGROUND OTHER
function getIGO($c_size, $c_unit, $p_unit, $p_size, $pl_size, $pl_unit)
{
    // if LBS do this calc
    if ($c_unit == "lbs") {
        return getDensity($pl_size, $pl_unit, $p_unit) * $c_size / $p_size; //. " What went in? " .$c_size ." ". $c_unit ." ". $p_unit . " ". $p_size;
    } else {
        return getVolume($c_size, $c_unit, $p_unit) / $p_size; //"What went in? " .$c_size ." ". $c_unit ." ". $p_unit . " ". $p_size;
    }
}

// FIND CUPS for NORMALIZING SIZE SELECTION
function findCups($in_value, $in_unit, $in_target)
{
// $in_value = Product 5cups LB weight for LBS -or- volume conversion to cups
    // $in_unit = What unit to find cups from
    // $in_target = size of bag (mainly for lbs calc to multiply)
    if ($in_unit == "lbs") {
        return getDensity($in_value, $in_unit, "cup") * $in_target;
    } else {
        return getVolume($in_value, $in_unit, "cup");
    }
} // end FindCups

function indppl_normalize($items = array(), $unit, $cups = null, $cups_unit = null){
    // Enter an array of items to normalize to one unit type and spit them back in order
    // from largest to smallest. Hooray.
    // Each item should be given at least a 'unit' and an 'amount' value
    // Will return a 'standardized-amount' and 'standardized-unit' value
    // CANNOT CONTAIN THESE KEYS OR THEY WILL BE OVERWRITTEN: 
    // 'type' 'standardized-unit' 'standardized-value'
    $units = indppl_get_units();
    if (in_array($unit, $units['volume'])) {
        $standard_type = 'volume';
    } else {
        $standard_type = 'mass';
    }
    
    $ref = &$items; // Copy items so we can manipulate it
    foreach($ref as $k => $item){
        // Find the type of the unit
        if (in_array($item['unit'], $units['volume'])) {
            $items[$k]['type'] = 'volume';
        } else {
            $items[$k]['type'] = 'mass';
        }
        
        // echo "<h4>Type</h4>";
        // var_dump($items);
        // // var_dump($standard_type);
        // // Compare and run the appropriate function
        // echo $standard_type;
        // echo $items[$k]['type'];
        // var_dump("<br /><br />");
        if($standard_type == $items[$k]['type']){
            if($unit == $item['unit'] || $item['amount'] == 0){

                $items[$k]['standard-amount'] = $item['amount'];
                
            } else {
                $convert = 'get' . ucfirst($standard_type);
                $items[$k]['standard-amount'] = $convert( $item['amount'], $item['unit'], $unit);
            
            }
        } else {
            if($item['amount'] == 0){
                $items[$k]['standard-amount'] = $item['amount'];
            }else if($standard_type == 'mass'){
                if($cups_unit == null){
                    $cups_unit = 'lb';
                }
                if($cups == null){
                    $cups = 1.5;
                }
                // var_dump($items[$k]['unit']);
                // var_dump($unit);
                // $single_cup = getVolume($cups/5, 'cup', $items[$k]['unit']);
                // $density = getMass($single_cup * 5, $cups_unit, $unit);
                $single_pound = 5/$cups;
                // var_dump($single_pound);
                $single_pack_unit = getVolume($single_pound, 'cup', $items[$k]['unit']);
                // var_dump($single_pack_unit);
                $single_pack_density = getMass($single_pack_unit, $cups_unit, $unit);
                // var_dump($single_pack_density);
                // var_dump($items[$k]['amount']);
                $items[$k]['unit-per-standard'] = $single_pack_density;
                // echo "<br /><br />";

                // $items[$k]['standard-amount'] = $item['amount']/$items[$k]['unit-per-standard'];
                $items[$k]['standard-amount'] = $single_pack_density * $items[$k]['amount'];
                // var_dump($items[$k]['standard-amount']);
                $items[$k]['invert'] = true;
            }else{
                $cup = $cups/5;
                $mass = getMass($cup, $cups_unit, $item['unit']);
                $total_cups = $item['amount'] / $mass;
                $blank = getVolume($total_cups, 'cup', $unit);
                $items[$k]['unit-per-standard'] = 1;
                $items[$k]['standard-amount'] = $blank;
            }

        }
        // echo "<h4>{$item['amount']} {$item['unit']}<br /> Unit: $unit <br /> Per Standard: {$items[$k]['unit-per-standard']}<br />Item Unit: {$item['unit']}<br />5 Cups = $cups <br /> Size = {$item['amount']} <br /> Standard size: {$items[$k]['standard-amount']}</h4>";
    }
    
    // var_dump($items);
    return $items;
    

    // foreach($items as $k => $val){

        
    //     if (in_array($val['unit'], $units['volume'])) {
    //         $item_type = 'volume';
    //     } else {
    //         $item_type = 'mass';
    //     }

    //     if (in_array($out_unit, $units['volume'])) {
    //         $out_type = 'volume';
    //     } else {
    //         $out_type = 'mass';
    //     }
   
    //     //Compare and run the functions accordingly
    //     if($item_type == $out_unit) {
    //         $results[$k] = call_user_func("get" . ucfirst($item_type), $val['amount'] );
    //     }
        
    // }
    


}

function indppl_get_units($return = 'all'){
    $units = array(
        "volume" => array(
            "l",
            "L",
            "ci",
            "cuft",
            "cy",
            "pt-d",
            "qt-d",
            "tsp",
            "tbls",
            "tbl",
            "floz",
            "cup",
            "cups",
            "pt-l",
            "qt-l",
            "gal",
            "cc",
            "ml",
            "mL",

        ),
        "mass" => array(
            "lbs",
            "lb",
            "oz",
            "g",
            "kg"
        ),
    );

    if($return != 'all'){
        $units = $units[$return];
    }

    return $units;

    
}

// Returns the amount of pounds needed to meet the cuft requirement
function ind_mass_to_cuft($unit, $cups, $cuft){

    // Convert to lbs
    $lbs = getMass(1, 'lb', $unit);

    // Get 1lb in CUFT
    $convert = getVolume($cuft, 'cuft', 'cup');
    
    return ($convert/5) * $cups;

}

function ind_vol_to_lbs($value, $unit, $cups, $lbs){

    // convert to cuft

    // get lbs

}