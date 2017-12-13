<?php






/**
* convert the json repsonse of google fonts in and array
*/
function KTT_google_jsonfonts_to_array() {
    include('webfonts/webfonts.php');
    $result_array = array();
    $fonts_object = json_decode($fonts);


    foreach($fonts_object->items as $key => $font) {

        $code = strtolower(str_replace(' ', '+' , $font->family));

        $result_array[$code]['type'] = 'google';
        //$result_array[$code]['kind'] = $font->kind;
        $result_array[$code]['name'] = $font->family;
        $result_array[$code]['css_code'] = "'" . $font->family . "'";
        $result_array[$code]['variants'] = $font->variants;
        $result_array[$code]['subsets'] = $font->subsets;
    }

    //file_put_contents(__DIR__ . '/webfonts/webfonts.bin', serialize($result_array));

    return $result_array;
}





/**
*return the list of basic fonts
*/
function KTT_get_available_default_fonts() {
        $result_array = array();

        $result_array['arial']['type'] = 'default';
        $result_array['arial']['name'] = 'Arial';
        $result_array['arial']['css_code'] = 'Arial, Helvetica, sans-serif';
        $result_array['arial']['variants'] = array(100, 200, 300, 400, 500, 600, 700, 800, 900 );

        $result_array['helvetica']['type'] = 'default';
        $result_array['helvetica']['name'] = 'Helvetica';
        $result_array['helvetica']['css_code'] = '"Helvetica Neue", Helvetica, Arial, sans-serif';
        $result_array['helvetica']['variants'] = array(100, 200, 300, 400, 500, 600, 700, 800, 900 );

        $result_array['georgia']['type'] = 'default';
        $result_array['georgia']['name'] = 'Georgia';
        $result_array['georgia']['css_code'] = 'Georgia, "Times New Roman", Times, serif';
        $result_array['georgia']['variants'] = array(100, 200, 300, 400, 500, 600, 700, 800, 900 );

        $result_array['tahoma']['type'] = 'default';
        $result_array['tahoma']['name'] = 'Tahoma';
        $result_array['tahoma']['css_code'] = 'Tahoma, Geneva, sans-serif';
        $result_array['tahoma']['variants'] = array(100, 200, 300, 400, 500, 600, 700, 800, 900 );

        $result_array['times']['type'] = 'default';
        $result_array['times']['name'] = 'Times';
        $result_array['times']['css_code'] = '"Times New Roman", Times, serif';
        $result_array['times']['variants'] = array(100, 200, 300, 400, 500, 600, 700, 800, 900 );

        $result_array['trebuchet']['type'] = 'default';
        $result_array['trebuchet']['name'] = 'Trebuchet';
        $result_array['trebuchet']['css_code'] = '"Trebuchet MS", Arial, Helvetica, sans-serif';
        $result_array['trebuchet']['variants'] = array(100, 200, 300, 400, 500, 600, 700, 800, 900 );

        $result_array['verdana']['type'] = 'default';
        $result_array['verdana']['name'] = 'Verdana';
        $result_array['verdana']['css_code'] = 'Verdana, Geneva, sans-serif';
        $result_array['verdana']['variants'] = array(100, 200, 300, 400, 500, 600, 700, 800, 900 );

        return $result_array;

}




/**
* return the list of available google fonts
*/
function KTT_get_available_google_fonts() {
    return KTT_google_jsonfonts_to_array();
    //return unserialize(file_get_contents(__DIR__ . '/webfonts/webfonts.bin'));
}





/**
* return the list of all the available fonts
*/
function KTT_get_available_fonts() {
    $all = array();

    $default_fonts = KTT_get_available_default_fonts();
    $all = array_merge($all, $default_fonts);

    $google_fonts = KTT_get_available_google_fonts();
    $all = array_merge($all, $google_fonts);

    return $all;
}
