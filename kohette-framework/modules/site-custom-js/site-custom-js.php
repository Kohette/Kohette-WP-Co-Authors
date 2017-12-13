<?php
/*
Module Name: Site Custom JS Module.
Module ID: site-custom-js
Module Description: This hook allow us to add custom js code to our site
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/



/**
* This hook allow us to add custom js code to our site
*/
function KTT_print_site_custom_js_footer() {

    /**
    * first we extract all the custom css of our site
    */
    $result = KTT_get_site_custom_js_footer();

    /**
    * We add a filter, util for modify the return of this function
    */
    $result = apply_filters('KTT_print_site_custom_js_footer', $result);

    /**
    * We create the style tags and print the css
    */
    if ($result) {
      echo '<script>';
      echo $result;
      echo '</script>';
    };

}
//add_action('wp_footer', 'KTT_print_site_custom_js_footer', 100000);



/**
* registramos la libreria que se encarga de mostrar el custom css
*/
function KTT_register_site_custom_js_file_footer() {
    wp_register_script(KTT_add_ktt_prefix('custom-js-footer'), esc_url(home_url(KTT_add_ktt_prefix('custom-js-footer') . '/scripts.js')));
}
add_action('wp_head', 'KTT_register_site_custom_js_file_footer', 1 );


/**
* Con esto nos encargamos de añadir el custom css del sitio en forma de archivo css que
* ira incluido en el header de la pagina
*/
function KTT_enqueue_site_custom_js_file_footer() {
    wp_enqueue_script(KTT_add_ktt_prefix('custom-js-footer'));
}
add_action('wp_head', 'KTT_enqueue_site_custom_js_file_footer', 100000 );



/**
* Esta función se encarga de manejar la url de custom css en nuestro sitio
* Detecta cuando se esta llamando a la url que se encarma de mostrar el custom css del sitio y genera
* una respuesta en forma de archivo CSS que contendrá el custom CSS
*/
function KTT_catch_custom_js_url_footer($uri, $request) {

  /**
  * Si estamos ante la url de custom css vamos a generarlo y mostrarlo como si fuese un archivo CSS
  */
  if (isset($uri[0]) && $uri[0] == KTT_add_ktt_prefix('custom-js-footer')) {

      /**
      * Modificamos los headers para devolver un archivo CSS
      */
      header("Content-Type: application/javascript");
      header("X-Content-Type-Options: nosniff");

      /**
      * Obtenemos todo el custom cSS del sitio junto en una variable
      */
      $result = KTT_get_site_custom_js_footer();
      //$result = apply_filters('KTT_print_site_custom_css', $result);

      /**
      * Mostramos el codigo css
      */
      echo $result;

      /**
      * Por ultimo salimos
      */
      exit;

  }

}
add_action('KTT_catch_url', 'KTT_catch_custom_js_url_footer', 5, 2);










/* --------------------------------------------------------------------------------- */


/**
* The same but for the header
*/
function KTT_print_site_custom_js_header() {

    /**
    * first we extract all the custom css of our site
    */
    $result = KTT_get_site_custom_js_header();

    /**
    * We add a filter, util for modify the return of this function
    */
    $result = apply_filters('KTT_print_site_custom_js_header', $result);

    /**
    * We create the style tags and print the css
    */
    if ($result) {
      echo '<script>';
      echo $result;
      echo '</script>';
    };

}
//add_action('wp_head', 'KTT_print_site_custom_js_header', 100000);





/**
* registramos la libreria que se encarga de mostrar el custom css
*/
function KTT_register_site_custom_js_file_header() {
    wp_register_script(KTT_add_ktt_prefix('custom-js-header'), esc_url(home_url(KTT_add_ktt_prefix('custom-js-header') . '/scripts.js')));
}
add_action('wp_head', 'KTT_register_site_custom_js_file_header', 1 );


/**
* Con esto nos encargamos de añadir el custom css del sitio en forma de archivo css que
* ira incluido en el header de la pagina
*/
function KTT_enqueue_site_custom_js_file_header() {
    wp_enqueue_script(KTT_add_ktt_prefix('custom-js-header'));
}
add_action('wp_head', 'KTT_enqueue_site_custom_js_file_header', 5 );



/**
* Esta función se encarga de manejar la url de custom css en nuestro sitio
* Detecta cuando se esta llamando a la url que se encarma de mostrar el custom css del sitio y genera
* una respuesta en forma de archivo CSS que contendrá el custom CSS
*/
function KTT_catch_custom_js_url_header($uri, $request) {

  /**
  * Si estamos ante la url de custom css vamos a generarlo y mostrarlo como si fuese un archivo CSS
  */
  if (isset($uri[0]) && $uri[0] == KTT_add_ktt_prefix('custom-js-header')) {

      /**
      * Modificamos los headers para devolver un archivo CSS
      */
      header("Content-Type: application/javascript");
      header("X-Content-Type-Options: nosniff");

      /**
      * Obtenemos todo el custom cSS del sitio junto en una variable
      */
      $result = KTT_get_site_custom_js_header();
      //$result = apply_filters('KTT_print_site_custom_css', $result);

      /**
      * Mostramos el codigo css
      */
      echo $result;

      /**
      * Por ultimo salimos
      */
      exit;

  }

}
add_action('KTT_catch_url', 'KTT_catch_custom_js_url_header', 5, 2);
