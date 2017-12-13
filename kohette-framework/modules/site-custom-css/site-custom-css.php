<?php
/*
Module Name: Site Custom CSS Module.
Module ID: site-custom-css
Module Description: This hook allow us to add custom css code to our site
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/


/**
* Esta funcion se encarga de mostrar directamente el codigo css custom del sitio
* Se ayuda del hook wp_head para mostrarlo incluido en el header del sitio (esto es
* una mala practica, mejor mostrarlo mediante un enlace link)
*/
function KTT_print_site_custom_css() {

    /**
    * first we extract all the custom css of our site
    */
    $result = KTT_get_site_custom_css();

    /**
    * We add a filter, util for modify the return of this function
    */
    $result = apply_filters('KTT_print_site_custom_css', $result, get_the_ID());

    /**
    * We create the style tags and print the css
    */
    if ($result) {
      echo '<style id="site-custom-css">';
      echo $result;
      echo '</style>';
    };

}
if (is_customize_preview()) add_action('wp_head', 'KTT_print_site_custom_css', 100000);





/**
* registramos la libreria que se encarga de mostrar el custom css
*/
function KTT_register_site_custom_css_file() {
    global $post;
    $post_id = 0;
    if ($post) $post_id = $post->ID;
    $result = KTT_get_site_custom_css(); // esto es solo para disparar el action que sirve para cargar las google fonts
    wp_register_style(KTT_add_ktt_prefix('custom-css'), esc_url(home_url(KTT_add_ktt_prefix('custom-css') . '/css.css?pid=' . $post_id)));
}
add_action('wp_head', 'KTT_register_site_custom_css_file', 1 );


/**
* Con esto nos encargamos de añadir el custom css del sitio en forma de archivo css que
* ira incluido en el header de la pagina
*/
function KTT_enqueue_site_custom_css_file() {
    wp_enqueue_style(KTT_add_ktt_prefix('custom-css'));
}
add_action('wp_head', 'KTT_enqueue_site_custom_css_file', 2 ); // < 10 = header // > 10 = footer



/**
* Esta función se encarga de manejar la url de custom css en nuestro sitio
* Detecta cuando se esta llamando a la url que se encarma de mostrar el custom css del sitio y genera
* una respuesta en forma de archivo CSS que contendrá el custom CSS
*/
function KTT_catch_custom_CSS_url($uri, $request) {

  /**
  * Si estamos ante la url de custom css vamos a generarlo y mostrarlo como si fuese un archivo CSS
  */
  if (isset($uri[0]) && $uri[0] == KTT_add_ktt_prefix('custom-css')) {

      /**
      * Modificamos los headers para devolver un archivo CSS
      */
      header("Content-Type: text/css");
      header("X-Content-Type-Options: nosniff");

      /**
      * Obtenemos el identificador de pagina, esto nos permite poder añadir css dinamicamente
      * que solo aparezca en determinadas páginas
      */
      $pid = 0;
      if (isset($_REQUEST['pid'])) $pid = $_REQUEST['pid'];

      /**
      * Obtenemos todo el custom cSS del sitio junto en una variable
      */
      $result = KTT_get_site_custom_css();

      /**
      * Este script se ejecuta despues de obtener el css custom general del sitio
      * y añade nuevo css dinamico que solo afecte a la pagina cullo pid
      * pasamos en el parametro
      */
      $result = apply_filters('KTT_print_site_custom_css', $result, $pid);

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
add_action('KTT_catch_url', 'KTT_catch_custom_CSS_url', 222, 2);
