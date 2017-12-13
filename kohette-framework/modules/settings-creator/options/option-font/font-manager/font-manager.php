<?php
/*
Plugin Name: Kohette Google's fonts loader
Plugin URI:
Description: Load google fonts.
Author: Rafael Martín
Author URI: http://kohette.com/
Version: 1.0.0
License: GNU General Public License v3.0
License URI: http://www.opensource.org/licenses/gpl-license.php
*/





include('fonts-getter-functions.php');
include('option-hook.php');




/**
* create the array of google fonts needed to call
*/
function KTT_get_necessary_google_fonts_array() {
    $result = array();
    $current_google_fonts = KTT_get_current_google_fonts();

    if ($current_google_fonts) {
    foreach($current_google_fonts as $font) {

        if (isset($font['font']['type']) && $font['font']['type'] == 'google') {


            if(isset($font['load_all_variants']) && $font['load_all_variants']) {

                foreach ($font['font']['variants'] as $variant) {
                    $result[$font['font']['name']][] = $variant;
                }

            } else {

                $result[$font['font']['name']][] = $font['variant'];

            }

        }

    }
    }

    return $result;

}




/**
* get the array of fonts required by the theme and load them in the site header
*/
function KTT_load_theme_google_fonts() {
    $protocol = is_ssl() ? 'https' : 'http';

    global $list;
    $list = KTT_get_necessary_google_fonts_array();

    do_action('KTT_load_theme_google_fonts', $list);

    $full_string = '';

    if (isset($list) && $list) {

    foreach ($list as $font_name => $sizes) {

      	$name = str_replace(' ', '+', $font_name);
      	$sizes = implode(',', $sizes);
      	$full_name = $name . ':' . $sizes;
      	$full_string .= $full_name . '|';

    }

    //wp_enqueue_style( THEME_PREFIX . '-google-fonts', "$protocol://fonts.googleapis.com/css?family=" . $full_string );
    wp_enqueue_style( THEME_PREFIX . '-google-fonts', add_query_arg( 'family', urlencode( $full_string ), "//fonts.googleapis.com/css" ), array(), '1.0.0' );

    };

}
//add_action( 'wp_enqueue_scripts', 'KTT_load_theme_google_fonts' );
