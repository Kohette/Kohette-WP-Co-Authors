<?php



/**
* Esta funcion se encarda de devolver un array con todos los authores de un post
*/
function KTT_get_post_coauthors($post) {

    if (is_int($post) || is_string($post)) $post = KTT_get_post($post);

    /**
    * Definimos la varaible que va a contener el array resultante
    */
    $result = array();

    /**
    * Si ya hay posts auhtors definidos los Obtenemos
    */
    if (isset($post->post_coauthors) && $post->post_coauthors) $result = $post->post_coauthors;

    /**
    * Si el author del post aparece como coauthor debemos quitarlo de la lista
    */
    if ($result) if (in_array($post->post_author, $result)) unset($result[$post->post_author]);

    /**
    * Si no tenemos coauthores salimos de aqui
    */
    if (!$result) return array();

    /**
    * Devolvemos los objeto users completos
    */
    $result = get_users(array('include' => $result));

    /**
    * Devolvemos el resultado
    */
    return $result;

}


/**
* Esta funcion se encarga de devolver en un mismo array el author y los coauthores de un post
*/
function KTT_get_post_author_and_coauthors($post) {

    if (is_int($post) || is_string($post)) $post = KTT_get_post($post);

    /**
    * Obtenemos el auhotr del post
    */
    $author = get_users(array('include' => $post->post_author));

    /**
    * Obtenemos los coauthores
    */
    $coauthors = KTT_get_post_coauthors($post);

    /**
    * Si no hay coauthores devolvemos directamente el author
    */
    if (!$coauthors) return $author;

    /**
    * Juntamos el auhotr con los coauthores
    */
    $result = array_merge($author, $coauthors);

    /**
    * Devolvemos el array resultante
    */
    return $result;

}







 ?>
