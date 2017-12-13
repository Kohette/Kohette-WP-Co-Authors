<?php
/*
Module Name: URL Catcher Module.
Module ID: url-catcher
Module Description: custom urls for your theme!
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/




function url_catcher() {

  if (is_ssl()) {
      $uri = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  } else {
      $uri = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  }

  $uri = str_replace(home_url("/"), '', $uri);
  $uri = str_replace( '?' . $_SERVER['QUERY_STRING'], '', $uri );
  $uri = str_replace(home_url("/"), '', $uri);
  $uri = explode( '/', $uri );
  $uri = array_values( array_filter( $uri ) );


  // guardamos la url actual como global
  KTT_set_global('current_url', $uri);


  /**
  * guardamos la url si estamos en modo api
  */
  if (isset($uri[0]) && $uri[0] == 'api') {
    $api_url = $uri;
    unset($api_url[0]);
    KTT_set_global('current_api_url', array_values($api_url));
  }



  do_action('KTT_catch_url_priorized', $uri, $_REQUEST);

  do_action('KTT_catch_url', $uri, $_REQUEST);


}

add_action( 'init', 'url_catcher', 2 );
