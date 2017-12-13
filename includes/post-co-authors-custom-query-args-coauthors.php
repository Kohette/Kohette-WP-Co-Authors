<?php
/**
 * Esta clase se encarga de generar los hooks necesarios para tener en cuenta el campo
* de coauthores en el caso en el que en la query se haya indicado el author como filtro
 */




/**
* Clase necesario para añadir el parametro "author"
*/
class KTT_argument_coauthor {

    // query acutal
    private $query;

    // el valor del argumento
    private $value;

    public function __construct(){
        add_action( 'parse_query', array( $this, 'parse_query' ) );
    }


    public function parse_query( $query ) {

    	// guardamos la query actual
        $this->query = $query;

        // si los argumentos tienen el date_limit en el orderby...
        if( isset( $query->query_vars['author']) &&  $query->query_vars['author'] ) $this->value = $query->query_vars['author'];
        if( isset( $query->query_vars['author_name']) &&  $query->query_vars['author_name'] ) $this->value = $query->query_vars['author_name'];


        if ($this->value) {

            /**
            * Si el valor que tenemos no es numerico significa que en lugar de la id de usuario tenemos el
            * nickname, por lo tanto vamos a intentar obtener la id
            */
            if (!is_numeric($this->value)) $this->value = get_user_by('slug', $this->value)->ID;

            /**
            * Fitro para añadir el join necesario para incluir el postmeta con la fecha limite
            */
            add_filter('posts_where', array( $this, 'filter_where' ));

            /**
            * Una vez seleccionados los posts borramos los filtros
            */
            add_filter( 'posts_selection', array( $this, 'remove_filters' ) );

        }
    }


    /**
    * Creamos el estamento que nos va a permitir seleccionar la fecha limite (formato timestamp)
    */
    public function filter_where($where_string = '') {
        global $wpdb;

        if ($this->value) $where_string .= " OR (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '" . ktt_var_name('post_coauthors') . "' AND post_id = {$wpdb->posts}.ID AND meta_value LIKE '%" . ':"' . $this->value . '";' . "%') ";

        return $where_string;

    }


    /**
    * eliminamos los hooks para volver a la normalidad
    */
    public function remove_filters() {
        remove_filter( 'posts_where', array( $this, 'filter_where' ) );
    }


}

// activamos el nuevo parametro
$argument_coauthor = new KTT_argument_coauthor();
