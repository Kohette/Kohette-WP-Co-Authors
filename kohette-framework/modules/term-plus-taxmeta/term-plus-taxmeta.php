<?php
/*
Module Name: Term Plus Postmeta Module.
Module ID: term-plus-postmeta
Module Description: Funcion encargada de completar la informacion de un termino con los taxmetas vinculados a el
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/



/**
 *	Funcion encargada de completar la informacion de un termino con los taxmetas vinculados a el
 */
add_action('get_term', 'complete_term_with_taxmetas_NEW', 6, 1);
function complete_term_with_taxmetas_NEW($term) {
	global $wpdb;

	/**
	* Definimos el identificador del theme con el que comienzan todas las variables
	* relacionadas con el
	*/
	$theme_id = ktt_var_name();

	/**
	* Hacemos la consulta que nos devolvera todos los postmetas del post relacionados
	* con el theme
	*/
	$metas = $wpdb->get_results('SELECT meta_key, meta_value FROM '  . $wpdb->termmeta . ' WHERE term_id = ' . $term->term_id . ' AND meta_key LIKE "' . $theme_id . '%"');

	foreach($metas as $nodo => $meta ) {
		$key = ktt_remove_prefix($meta->meta_key);
		$value = maybe_unserialize($meta->meta_value);
		$term->$key = $value;
	}

	return $term;
}






/**
* Esta funcino se encarga de comprobar si en la pagina actual esta definida la global post y si es
* asi busca todos sus postmetas relacionados con el theme y los añade al objeto
*/
function KTT_add_termmetas_to_global_term_object($term) {

  /**
  * Definimos el identificador del theme con el que comienzan todas las variables
  * relacionadas con el
  */
  $theme_id = ktt_var_name();

  /**
  * Invocamos wpdb
  */
  global $wpdb;

  /**
  * Hacemos la consulta que nos devolvera todos los postmetas del post relacionados
  * con el theme
  */
  $metas = $wpdb->get_results('SELECT meta_key, meta_value FROM '  . $wpdb->termmeta . ' WHERE term_id = ' . $term->term_id . ' AND meta_key LIKE "' . $theme_id . '%"');

  /**
  * Si no hemos encontrado postmetas salimos de aqui
  */
  if (!$metas) return;

  /**
  * Itineramos por cada uno de los postmetas y los vamos añadiendo al objeto post
  */
	foreach($metas as $nodo => $meta ) {
		$key = ktt_remove_prefix($meta->meta_key);
		$value = maybe_unserialize($meta->meta_value);

		$term->$key = $value;
	}

	return $term;

}
//add_action('get_term', 'KTT_add_termmetas_to_global_term_object', 6, 1);


/*
add_action('get_terms_fields', 'complete_terms_with_taxmetas', 1);
function complete_terms_with_taxmetas($terms) {
	global $wpdb;
	$taxmetas = $wpdb->get_results('SELECT meta_key, meta_value FROM wp_taxonomymeta WHERE taxonomy_id = ' . $_term->term_id);

	echo '<pre>';
	print_r($terms);
	echo '</pre>';

	return $_term;
}
*/




?>
