<?php
/*
Module Name: Color Schemes Module.
Module ID: color-schemes
Module Description: Esta funcionalidad nos permite implementar plantillas de colores en le theme
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/



/**
* Esta funcion se encarga de devolver la lista de schemes de colores
* registradas en el sitiio
*
* @package Kohette Framework\Color schemes
* @return Array Contiene una lista compuesta con todas color schemes registradas.
*/
function KTT_get_theme_color_schemes() {

      /**
      * Una scheme debera tener este formato
      * [scheme_id] = array(
      *   'id' => scheme_id,
      *   'name' => 'Nombre de la scheme',
      *   'description' => 'Descripcion de la scheme'
      *   'colors' => array(
      *     'yin' => array(
      *       'yin_1' => '#000000',
      *       'yin_2' => '#000000',
      *       'yin_3' => '#000000',
      *       'yin_4' => '#000000',
      *       'yin_special_1' => '#000000',
      *       'yin_special_2' => '#000000',
      *     )
      *     'yang' => array (
      *       'yang_1' => '#000000',
      *       'yang_2' => '#000000',
      *       'yang_3' => '#000000',
      *       'yang_4' => '#000000',
      *       'yang_special_1' => '#000000',
      *       'yang_special_2' => '#000000',
      *      )
      *
      *   )
      * )
      */

      /**
      * Registramos el array de resultados
      */
      $result = array();

      /**
      * Aplicamos un filter, esto nos permite añadir schemes desde terceras funciones
      */
      $result = apply_filters('KTT_THEME_color_schemes', $result);

      /**
      * Devolvemos el array con los resultados
      */
      return $result;

}


/**
* Esta funcion se encarga de crear el nombre de clase de una scheme
*
* @package Kohette Framework\Color schemes
*
* @param String $scheme_id Identificador de color scheme.
* @return String Devuelve la clase CSS que identifica la scheme_id que se pase como parámetro, esta será la clase que se utilice en el html.
*/
function KTT_get_color_scheme_classname($scheme_id) {
  return 'color-scheme-' . $scheme_id;
}



/**
* Esta funcion se encarga de obtener la scheme que se haya indicado como parametro
*
* @package Kohette Framework\Color schemes
*
* @param String $scheme_id Identificador de color scheme.
* @return Array Devuelve toda la información relevante de la scheme en forma de array.
*/
function KTT_get_theme_color_scheme($scheme_id) {

      /**
      * En primer lugar obtenemos la lista completa de tempaltes
      */
      $schemes = KTT_get_theme_color_schemes();

      /**
      * si la id que buscamos no se encuentra en la lista devolvemos un false
      */
      if (!isset($schemes[$scheme_id])) return;

      /**
      * Devolvemos la scheme
      */
      return $schemes[$scheme_id];

}



/**
* A partir de la scheme sacamos el css que forma los colores
*
* @package Kohette Framework\Color schemes
*/
function KTT_get_theme_color_scheme_css($scheme_id) {

      /**
      * Definimos el css var
      */
      $css = '';

      /**
      * Definimos las properties que vamos a definir en el css
      */
      $properties = array('color', 'background-color', 'border-color');

      /**
      * obtenemos la scheme
      */
      $scheme = KTT_get_theme_color_scheme($scheme_id);

      /**
      * Si no hay scheme id salimos de aqui
      */
      if (!$scheme) return;

      /**
      * Si no hay colores salimos de aqui
      */
      if (!isset($scheme['colors']) && !$scheme['colors']) return;





      foreach ($scheme['colors']['yang_special'] as $color_id => $color_value) {
          $css_array = array();
          $css_array['selector'] = ' .color-scheme-' . $scheme_id;
          foreach ($scheme['colors']['yang'] as $base_id => $base_value) {
            $css_array['selector'] .= ' .' . str_replace('_', '-', $base_id) . '-color a,';
          }
          $css_array['selector'] = substr($css_array['selector'], 0, -1);
          $css_array['color'] = $color_value;
          $css .= KTT_theme_css_option_array_to_code($css_array);
          break;
      }

      foreach ($scheme['colors']['yin_special'] as $color_id => $color_value) {
          $css_array = array();
          $css_array['selector'] = ' .color-scheme-' . $scheme_id;
          foreach ($scheme['colors']['yin'] as $base_id => $base_value) {
            $css_array['selector'] .= ' .' . str_replace('_', '-', $base_id) . '-color a,';
          }
          $css_array['selector'] = substr($css_array['selector'], 0, -1);
          $css_array['color'] = $color_value;
          $css .= KTT_theme_css_option_array_to_code($css_array);
          break;
      }







      foreach ($scheme['colors']['yang'] as $color_id => $color_value) {
          $css_array = array();
          $css_array['selector'] = ' .color-scheme-' . $scheme_id;
          $css_array['selector'] .= ' .' . str_replace('_', '-', $color_id) . '-background-color a';

          $var = array_slice($scheme['colors']['yin_special'], 0, 1);
          $first = array_shift($var);
          $css_array['color'] = $first;
          $css .= KTT_theme_css_option_array_to_code($css_array);
          break;
      }

      /**
      * Esto se encarga de poner los links de color claro en fondo oscuros y viceversa
      */
      foreach ($scheme['colors']['yin'] as $color_id => $color_value) {
          $css_array = array();
          $css_array['selector'] = ' .color-scheme-' . $scheme_id;
          $css_array['selector'] .= ' .' . str_replace('_', '-', $color_id) . '-background-color a';

          $var = array_slice($scheme['colors']['yang_special'], 0, 1);
          $first = array_shift($var);
          $css_array['color'] = $first;
          $css .= KTT_theme_css_option_array_to_code($css_array);
          break;
      }











      foreach ($scheme['colors']['yin_special'] as $color_id => $color_value) {
        foreach ($properties as $property) {
          $css_array = array();
          $css_array['selector'] = ' .color-scheme-' . $scheme_id;
          foreach ($scheme['colors']['yin'] as $base_id => $base_value) {


            $css_array['selector'] .= ' .' . str_replace('_', '-', $base_id) . '-' . $property;
            $css_array['selector'] .= ' .' . str_replace('_', '-', $color_id) . '-' . $property . ',';


          }
          $css_array['selector'] = substr($css_array['selector'], 0, -1);
          $css_array[$property] = $color_value;
          $css .= KTT_theme_css_option_array_to_code($css_array);
        }




      }





      foreach ($scheme['colors']['yin_special'] as $color_id => $color_value) {
        foreach ($properties as $property) {
          $css_array = array();
          $css_array['selector'] = ' .color-scheme-' . $scheme_id;
          foreach ($scheme['colors']['yin'] as $base_id => $base_value) {


            $css_array['selector'] .= ' .' . str_replace('_', '-', $base_id) . '-' . $property;
            $css_array['selector'] .= ' .' . str_replace('_', '-', $color_id) . '-' . $property . ',';


          }
          $css_array['selector'] = substr($css_array['selector'], 0, -1);
          $css_array[$property] = $color_value;
          $css .= KTT_theme_css_option_array_to_code($css_array);
        }
      }













      /**
      * Itineramos por cada una de las properties y añadimos una card_classes
      * que defina un color para cada property
      */
      foreach ($properties as $property) {
          /**
          * Itineramos por cada uno de los colores
          */
          foreach ($scheme['colors']['yin'] as $color_id => $color_value) {

              $css_array = array();
              $css_array['selector'] = ' .color-scheme-' . $scheme_id . ' .' . str_replace('_', '-', $color_id) . '-' . $property ;
              $css_array['selector'] .= ', .color-scheme-' . $scheme_id . ' a.' . str_replace('_', '-', $color_id) . '-' . $property;
              $css_array[$property] = $color_value;

              $css .= KTT_theme_css_option_array_to_code($css_array);
          }

          foreach ($scheme['colors']['yang'] as $color_id => $color_value) {

              $css_array = array();
              $css_array['selector'] = ' .color-scheme-' . $scheme_id . ' .' . str_replace('_', '-', $color_id) . '-' . $property ;
              $css_array['selector'] .= ', .color-scheme-' . $scheme_id . ' a.' . str_replace('_', '-', $color_id) . '-' . $property;
              $css_array[$property] = $color_value;

              $css .= KTT_theme_css_option_array_to_code($css_array);
          }
      }






      /**
      * Devolvemos el css
      */
      return $css;


}
