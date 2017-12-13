<?php


/**
* Esta funcion hace uso del filtro KTT_theme_css_options para obtener las opciones
* css que necesitan cargar una fuente de google y las añade en una lista
*/
function KTT_get_required_theme_fonts_list($options) {

      /**
      * Si no hay ptions nos vamos
      */
      if (!$options) return $options;

      /**
      * Aqui vamos a ir guardando el array de fuentes
      * array(font_code => array(type => 'google', weights => array(font_weights))
      */
      $fonts = array();

      /**
      * Cargamos un array con la lista de fuentes de google
      */
      $fonts_list = KTT_get_available_fonts();

      /**
      * Itineramos por cada una de las opciones y si encontramos que se trata de una
      * opcion que necesita cargar una fuente de google la añadimos a la lista que despues
      * vamos a pasar a la funcion enqueue_style
      */
      foreach ($options as $option) {

          /**
          * Obtenemos el value de la opcion
          */
          $option_value = get_option($option);

          /**
          * Si en la opcion no hay definido un font_family nos la saltamos
          */
          //if (!isset($option_value['css'])) continue;
          if (!isset($option_value['font_family'])) continue;
          if (!$option_value['font_family']) continue;

          /**
          * Obtenemos el code
          */
          $font_family_code = $option_value['font_family'];

          /**
          * Añadimos el tipo de fuente (google, default, etc)
          */
          $fonts[$font_family_code]['type'] = $fonts_list[$font_family_code]['type'];

          /**
          * Añadimos el tname
          */
          $fonts[$font_family_code]['name'] = $fonts_list[$font_family_code]['name'];

          /**
          * Si esta indicado que debemos cargar todas las variantes añadimos todas
          * las variantes posibles
          */
          if (isset($option_value['load_all_weights']) && $option_value['load_all_weights']) $fonts[$font_family_code]['weights'] = $fonts_list[$font_family_code]['variants'];
          else $fonts[$font_family_code]['weights'] = array(@$option_value['weight']);

      }


      /**
      * Si no hay fonts salimos de Aqui
      */
      if ($fonts) {

        $full_string = '';

        foreach ($fonts as $font_code => $font) {

                $name = str_replace(' ', '+', $font['name']);
                $sizes = implode(',', $font['weights']);
                if ($sizes) $font['weights'] = array('regular');
                $full_name = $name . ':' . $sizes;

                $full_string .= $full_name . '|';

        }

        /**
        * Enqueue font css style
        * #https://gist.github.com/kailoon/e2dc2a04a8bd5034682c
        */
        wp_enqueue_style( THEME_PREFIX . '-google-fonts', add_query_arg( 'family',  $full_string, "//fonts.googleapis.com/css" ), array(), '1.0.0' );

      }

      /**
      * Por ultimo devolvemos las opciones
      */
      return $options;


}
add_filter( 'KTT_theme_css_options', 'KTT_get_required_theme_fonts_list', 999, 1);





















/**
* get current google fonts required by the theme
*/
function KTT_get_current_google_fonts() {
    return (array)get_option(ktt_var_name('current_google_fonts'));
}

/**
* clean the  current_google_fonts
*/
function KTT_clean_current_google_fonts() {
    global $KTT_custom_theme_settings;
    $options = array_keys($KTT_custom_theme_settings);
    $current_google_fonts = KTT_get_current_google_fonts();

    foreach ($current_google_fonts as $key => $value) {
        if(!array_key_exists($key, $KTT_custom_theme_settings)) unset($current_google_fonts[$key]);
    }

    update_option(ktt_var_name('current_google_fonts'), $current_google_fonts);

}



/**
* override custom font styles option before save a font setting option
*
*/
function KTT_save_custom_font_styles( $new_value, $old_value ) {

    //KTT_clean_custom_font_styles_var();

    if (is_array($new_value)) {




        //$custom_font_styles = KTT_get_custom_font_styles();

        $option_id = $new_value['option_id'];
        $selector = $new_value['selector'];

        if ($option_id && $selector) {


            // save font family ------------------------------------------------
            if (isset($new_value['font_family'])) {
                $fonts = KTT_get_available_fonts();

                //$style_array[$option_id][$selector]['selector'] = $selector;
                $style_array[$option_id][$selector]['font-family']['property'] = 'font-family';
                $style_array[$option_id][$selector]['font-family']['value'] = @$fonts[$new_value['font_family']]['css_code'];
                //$style_array[$option_id][$selector]['extra']['font'] = $fonts[$new_value['font_family']];
                KTT_add_custom_style($style_array);

                // guardamos el objeto fuente para despues poder sacarlo por css
                if(@$fonts[$new_value['font_family']]['type'] == 'google') {

                    KTT_clean_current_google_fonts();
                    $current_google_fonts = KTT_get_current_google_fonts();
                    $new_value['font'] = $fonts[$new_value['font_family']];
                    $current_google_fonts[$option_id] = $new_value;
                    update_option(ktt_var_name('current_google_fonts'), $current_google_fonts);

                }



            }
            // -----------------------------------------------------------------



            // save font weight ------------------------------------------------
            if (isset($new_value['variant']) && $new_value['variant']) {

              /**
              * Solo guardamos la variant (weight) si hay una font_family definida
              */
              if (isset($new_value['font_weight']) && $new_value['font_weight']) {
                  //$style_array[$option_id][$selector]['selector'] = $selector;
                  $style_array[$option_id][$selector]['font-weight']['property'] = 'font-weight';
                  $style_array[$option_id][$selector]['font-weight']['value'] = $new_value['variant'];
                  KTT_add_custom_style($style_array);
              }

            }
            // -----------------------------------------------------------------


            // save font size ------------------------------------------------
            if (isset($new_value['size']) && isset($new_value['size_unit']) && $new_value['size']) {

                //$style_array[$option_id][$selector]['selector'] = $selector;
                $style_array[$option_id][$selector]['font-size']['property'] = 'font-size';
                $style_array[$option_id][$selector]['font-size']['value'] = $new_value['size'] . $new_value['size_unit'];
                KTT_add_custom_style($style_array);

            }
            // -----------------------------------------------------------------


            // save font color ------------------------------------------------
            if (isset($new_value['color']) && $new_value['color']) {

                //$style_array[$option_id][$selector]['selector'] = $selector;
                $style_array[$option_id][$selector]['color']['property'] = 'color';
                $style_array[$option_id][$selector]['color']['value'] = $new_value['color'];
                KTT_add_custom_style($style_array);

            }
            // -----------------------------------------------------------------


            // save font style ------------------------------------------------
            if (isset($new_value['font_style']) && $new_value['font_style']) {

                //$style_array[$option_id][$selector]['selector'] = $selector;
                $style_array[$option_id][$selector]['font-style']['property'] = 'font-style';
                $style_array[$option_id][$selector]['font-style']['value'] = $new_value['font_style'];
                KTT_add_custom_style($style_array);

            }
            // -----------------------------------------------------------------


            // save font style ------------------------------------------------
            if (isset($new_value['line_height']) && $new_value['line_height']) {

                //$style_array[$option_id][$selector]['selector'] = $selector;
                $style_array[$option_id][$selector]['line-height']['property'] = 'line-height';
                $style_array[$option_id][$selector]['line-height']['value'] = $new_value['line_height'] . 'em';
                KTT_add_custom_style($style_array);

            }
            // -----------------------------------------------------------------


        }



        // we save the font data in the option --------------------------------------------------
        if (isset($new_value['font_family']) && $new_value['font_family']) {
            $new_value['font'] = $fonts[$new_value['font_family']];
        }
        // --------------------------------------------------------------------------------------



    }

    return $new_value;

}
