<?php
/**
* Columns
*/

/**
* Definimos las columnas que va a tener la tabla
*/
function KTT_add_coauthors_column_head( $defaults ) {

    /**
    * Vamos a formar un array nuvo con todas las columnas
    */
    $result = array();

    /**
    * Itineramos por cada uno de las columnas
    */
    if ($defaults) foreach ($defaults as $key => $value) {

        /**
        * Aladimos la columna al nuevo arrays
        */
        $result[$key] = $value;

        /**
        * Si la columna actual es la de author añadimos justo al lado la de coauthors
        */
        if ($key == 'author') $result['coauthors'] = __('Co-Authors', 'narratium');

    }

    /**
    * Devolvemos el nuevo arrays
    */
  	return $result;

}
add_filter('manage_posts_columns', 'KTT_add_coauthors_column_head');




/**
* Definimos las columnas que va a tener la tabla
*/
function KTT_add_coauthors_column( $column_name, $post_ID ) {

    /**
    * Obtenemos el post completos
    */
    $post = KTT_get_post($post_ID);

    /**
    * Identificamos la columnas
    */
    if ($column_name == 'coauthors') {

        /**
        * Obtenemos los coauthores del post
        */
        $coauthors = KTT_get_post_coauthors($post);

        /**
        * Si hay coauthors...
        */
        $prefix = '';
        if ($coauthors) foreach ($coauthors as $user) {

            /**
            * Mostramos el author
            */
            echo $prefix;
            ?><a href="<?php echo admin_url('edit.php?author=' . $user->ID);?>"><?php echo $user->display_name;?></a><?php
            $prefix = ', ';
        }

    }


}
add_action('manage_posts_custom_column', 'KTT_add_coauthors_column', 10, 2);
