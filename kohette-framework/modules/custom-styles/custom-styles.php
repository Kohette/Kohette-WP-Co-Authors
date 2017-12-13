<?php
/*
Module Name: Custom Styles module.
Module ID: custom-styles
Module Description: Functions related with custom CSS code
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/






/**
* return the custom font styles array
*/
function KTT_get_custom_styles() {
    return (array)get_option(ktt_var_name('custom_styles'));
}


function KTT_get_custom_styles_simplified() {
    $custom_styles = KTT_get_custom_styles();
    $simplify = array();

    if ($custom_styles) {
    foreach ($custom_styles as $option_id => $style) {
        if ($style) {
        foreach($style as $selector => $values) {

            foreach ($values as $value) {

                $simplify[$selector][] = $value;

            }

        }
        }

    }
    }

    return $simplify;

}


/**
* add new custom style
*/
/*
EXAMPLE:

$new_custom_style = array(

                            'option_id' => array(
                                                    '.selector' => array(
                                                                            'selector' => 'body a',
                                                                            'property' => 'font-size',
                                                                            'value' => '12px',
                                                                            extra => array(),
                                                    )
                            )

)

*/
function KTT_add_custom_style($array) {

    KTT_clean_custom_styles_var();

    $custom_styles = KTT_get_custom_styles();
    foreach($array as $key => $value) {
        $custom_styles[$key] = $value;
    }

    update_option(ktt_var_name('custom_styles'), $custom_styles);

}







/**
* clean the global custom_styles
*/
function KTT_clean_custom_styles_var() {
    global $KTT_custom_theme_settings;
    $options = array_keys($KTT_custom_theme_settings);
    $custom_styles = KTT_get_custom_styles();

    foreach ($custom_styles as $key => $value) {
        if(!array_key_exists($key, $KTT_custom_theme_settings)) unset($custom_styles[$key]);
    }

    update_option(ktt_var_name('custom_styles'), $custom_styles);

}









/**
* display in the header the css necessary to load the custom  styles
*/
function KTT_display_custom_styles_css($current_css) {

    $custom_styles = KTT_get_custom_styles_simplified();

    $result = '';

    foreach ($custom_styles as $selector => $properties) {

        $result .= $selector . '{';

          foreach ($properties as $property) {
              if (!$property['value']) continue;
              $result .= $property['property'] . ':' . $property['value'] . ';';
          }

        $result .= '} ';

    }

    /**
    * we add the result to the current css code of the site
    */
    $current_css .= $result;

    /**
    * We return the modified current css of the site
    */
    return $current_css;

}
//add_action( 'KTT_add_site_custom_css', 'KTT_display_custom_styles_css', 4, 1 );









/**
* Esta funcion se encarga de añadir el css perteneciente a cada una de las opciones del themes
* que utilizan elementos css
*/
function KTT_load_theme_css_options($current_css) {
    $options = array();
    $options = apply_filters('KTT_theme_css_options', $options);

    /**
    * Obtenemos los valores por defecto
    */
    $option_defaults = KTT_get_starter_content_data();

    /**
    * Si hay opciones itineramos por cada una de ellas y añadimos el css a
    */
    if ($options) foreach ($options as $option_id) {

        /**
        * Optenemos los datos de la opcion
        */
        $option_value = get_option($option_id);

        /**
        * Esta linea de codigo se asegura de que en todo momento el selector
        * de la opcion sea el que tengamos definido en el array inicial. wp_customize
        * puede hacer que si actualizamos este selector no se actualice cuando
        * se actualiza el valor del selector, esto lo corrige.
        */
        $option_value['selector'] = $option_defaults['options'][$option_id]['selector'];

        /**
        * Transformamos el array css en css real y lo añadimos al
        * codigo css existente del theme
        */
        $current_css .= KTT_theme_css_option_array_to_code($option_value);

    }

    /**
    * Devolvemos el css modificado
    */
    return $current_css;
}
add_action('KTT_add_site_custom_css', 'KTT_load_theme_css_options', 5, 1);





/**
* Esta funcion se encarga de convertir un array css en una cadena de texto css funcional
*/
function KTT_theme_css_option_array_to_code($array) {

    /**
    * En result vamos formando la string
    */
    $result = '';
    if (!isset($array['selector'])) return '';

    /**
    * Primero colocamos el selector con el corchete de abertura
    */
    $result .= $array['selector'] . ' {';

    /**
    * Quitamos el selector del array
    */
    unset($array['selector']);

    /**
    * Este es un pequño fix hecho para las opciones que tiene definido una property
    */
    if (isset($array['property'])) {

      /**
      * Si no tiene value salimos de aqui
      */
      if (!$array['value']) return;
      $array[$array['property']] = $array['value'];
      unset($array['property']);
      unset($array['value']);
    }

    /**
    * Itineramos por cada elemento css y lo vamos sumando a la string
    */
    foreach ($array as $property => $value) {

        /**
        * FIX: Cambiamos el valor "regular" que da google a las fuentes por "normal"
        * que es el aceptado css
        */
        if ($property == 'font_weight' && $value = 'regular') $value = 'normal';

        /**
        * FIX: Size_unit no es una propiedad css válidad, por lo tanto pasamos
        */
        if (in_array($property, array(
          'size_unit',
          'font_size_unit',
          'load_all_weights'))) $value = '';


        //FIX chungo
        // en lugar de este fix podriamos obtener la libreria de fuentes y en base al code
        // sacar el nombre, pero quizas consuma mucha memoria (array grande) en tal caso
        // una solucion podria ser usar un trasient que se borrara solo por un admin y
        // cuando cambie algo en customize.
        if ($property == 'font_family') if (strpos($value, '+')) $value = "'" . str_replace('+', ' ', $value) . "'";

        /**
        * Si existe un value para la property...
        */
        if ($value) {

              $result .= str_replace('_', '-', $property) . ':' . $value;

              if ($property == 'font_size') $result .= $array['font_size_unit'];
              if ($property == 'line_height') $result .= 'em';

              $result .= ';';
        }


    }

    /**
    * Por ultimo cerramos el corchete
    */
    $result .= '} ';

    /**
    * Devolvemos el result
    */
    return $result;

}




/*

function mytheme_customize_register( $wp_customize ) {

$wp_customize->add_section(
    'layout_section', # Section ID to use in Option Table
    array( # Arguments array
        'title' => __( 'Layout', 'narga' ), # Translatable text, change the text domain to your own
        'capability' => 'edit_theme_options', # Permission to change option date
        'description' => __( 'Allows you to edit your themes layout.', 'narga' )
    )
);



$wp_customize->add_setting('narga_options[use_custom_text]', array(
    'capability' => 'edit_theme_options',
    'type'       => 'option',
    'default'       => '1', # Default checked
));

$wp_customize->add_control('narga_options[use_custom_text]', array(
    'settings' => 'narga_options[use_custom_text]',
    'label'    => __('Display Custom Text', 'narga'),
    'section'  => 'layout_section', # Layout Section
    'type'     => 'checkbox', # Type of control: checkbox
));

# Add text input form to change custom text
$wp_customize->add_setting('narga_options[custom_text]', array(
    'capability' => 'edit_theme_options',
    'type'       => 'option',
    'default'       => 'Custom text', # Default custom text
));

$wp_customize->add_control('narga_options[custom_text]', array(
        'label' => 'Custom text', # Label of text form
        'section' => 'layout_section', # Layout Section
        'type' => 'text', # Type of control: text input
));


}
add_action( 'customize_register', 'mytheme_customize_register' );

*/
