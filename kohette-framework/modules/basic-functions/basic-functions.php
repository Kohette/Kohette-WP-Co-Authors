<?php
/*
Module Name: Basic Functions Module.
Module ID: basic-functions
Module Description: Custom functions by Kohette Framework.
Module Required:
Module Version: 1.0.0
Module Priority: Core
*/




/**
* Custom functions by Kohette Framework.
*/
function KTT_framework_unique_prefix() {
  return 'ktt_';
}


/**
* return the correct name for a option (postmeta, usermeta, etc) adding the theme prefix
* @package Kohette Framework\Basic functions
*/
function KTT_add_prefix($string, $prefix = '') {
    if (!$prefix) $prefix = KTT_framework_unique_prefix();
    return $prefix . $string;
}

/**
* return the correct name for a option (postmeta, usermeta, etc) adding the frmwork prefix
* @package Kohette Framework\Basic functions
*/
function ktt_var_name($string = '') {
    return KTT_framework_unique_prefix() . $string;
}

/**
* Add prefix to string
* @package Kohette Framework\Basic functions
*/
function KTT_add_ktt_prefix($string = '', $prefix = '') {
    if (!$prefix) $prefix = KTT_framework_unique_prefix();
    return $prefix . $string;
}

/**
* return the correct name for a option (postmeta, usermeta, etc) adding the theme prefix
* @package Kohette Framework\Basic functions
*/
function ktt_theme_var_name($string = '') {
    return KTT_add_prefix($string, THEME_PREFIX);
}

/**
* This function adds the theme unique prefix to a string
* @package Kohette Framework\Basic functions
*/
function KTT_add_theme_prefix($string = '') {
    return KTT_add_prefix($string, THEME_PREFIX);
}

/**
* Remove the theme prefix
* @package Kohette Framework\Basic functions
*/
function KTT_remove_theme_prefix($string) {
    return ktt_remove_prefix($tring, KTT_add_theme_prefix());
}

/**
* Get a theme global variable
* @package Kohette Framework\Basic functions
*/
function KTT_get_global($variable_name) {
	return $GLOBALS[ktt_var_name($variable_name)];
}


/**
* set a theme global variable
* @package Kohette Framework\Basic functions
*/
function KTT_set_global($variable_name, $value) {
	$GLOBALS[ktt_var_name($variable_name)] = $value;
}





/**
* remove the theme prefix from string
* @package Kohette Framework\Basic functions
*/
function ktt_remove_prefix($string, $prefix = '') {
    if (!$prefix) $prefix = ktt_var_name();
    return str_replace($prefix, '', $string);
}


/**
* Transform a local path in a direct url
* @package Kohette Framework\Basic functions
*/
function KTT_path_to_url($string) {
    $string = str_replace("\\","/",$string);
    $correct_wp_content_url = str_replace(parse_url(WP_CONTENT_URL, PHP_URL_HOST), parse_url(home_url("/"), PHP_URL_HOST), WP_CONTENT_URL);
  	$result = str_replace( str_replace("\\","/", WP_CONTENT_DIR), $correct_wp_content_url, $string );
    $result = apply_filters('KTT_path_to_url_filter', $result);
    return $result;
}

/**
* transform a direct url in a local path
* @package Kohette Framework\Basic functions
*/
function KTT_url_to_path($string) {
    $string = str_replace("\\","/",$string);
    $correct_wp_content_url = str_replace(parse_url(WP_CONTENT_URL, PHP_URL_HOST), parse_url(home_url("/"), PHP_URL_HOST), WP_CONTENT_URL);
  	$result = str_replace(  $correct_wp_content_url, str_replace("\\","/", WP_CONTENT_DIR), $string );
    $result = apply_filters('KTT_url_to_path_filter', $result);
    return $result;
}


/**
* change the status of a post
* @package Kohette Framework\Basic functions
*/
function KTT_change_post_status($post_id, $new_status) {
	$args = array(
      'ID'           => $post_id,
      'post_status' => $new_status,
  	);

	return wp_update_post( $args );
}

/**
* this special hook change the {term}_status field of a term
* @package Kohette Framework\Basic functions
*/
function KTT_change_term_status($term_id, $new_status) {
    $term = get_term($term_id);
    if ($term) return update_term_meta($term_id, ktt_var_name($term->taxonomy . '_status'), $new_status);
}

/**
* Update the post to change the field indicated
* @package Kohette Framework\Basic functions
*/
function KTT_change_post_field($post_id, $post_field, $field_value) {

	$args = array(
      'ID'           => $post_id,
      $post_field => $field_value,
  	);

	return wp_update_post( $args );
}



/**
* This function return all the prefixes that are been used in the querys of
* the site
*/
function KTT_get_all_site_variable_prefixes() {

    /**
    * Declaramos un array con los prefix que deseamos extraer de la ddbb
    */
    $prefixes = array(KTT_framework_unique_prefix());

    /**
    * Añadimos un filter para poder extraer variables cn un prefix desde
    * una funcion esterior
    */
    $prefixes = apply_filters('KTT_meta_prefixes', $prefixes);

    /**
    * Devolvemos lal ista de prefixes
    */
    return $prefixes;

}


/**
* Adds postmetas to post object
*/
function KTT_add_postmetas_to_post_object($post) {
      global $wpdb;

      /**
      * We get the prefixes for the variables
      */
      $prefixes = KTT_get_all_site_variable_prefixes();

      /**
      * En base a la lista de prefixes vamos a ir creando la query
      */
      $query_string = $wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post->ID);

      /**
      * Si hay definida una lista de prefixes vamos a ir itinerando por cada uno de ellos
      * y añadirlos a la query
      */
      if ($prefixes) {
        $query_string .= " AND (";
        $c = 0;
        foreach ($prefixes as $prefix) {
          if ($c) $query_string .= " OR ";
          $query_string .= $wpdb->prepare("meta_key LIKE %s", $prefix . '%');
          $c += 1;
        }
        $query_string .= ")";
      }

      /**
      * Ejecutamos la query_string
      */
      $postmetas = $wpdb->get_results($query_string);

      /**
      * Añadimos los postmetas al objeto Post
      */
      foreach($postmetas as $nodo => $meta ) {
        $key = $meta->meta_key;
        if ($prefixes) foreach ($prefixes as $prefix) $key = ktt_remove_prefix($key, $prefix);
        $value = maybe_unserialize($meta->meta_value);
        $post->$key = $value;
      }

      /**
      * Por ultimo devolvemos el objeto post
      */
      return $post;

}


/**
* This returns a post object with all their postmetas
* @package Kohette Framework\Basic functions
*/
function KTT_get_post($post_id) {
	$post = get_post($post_id);
	if (!$post) return;
  $post = KTT_add_postmetas_to_post_object($post);
	return $post;
}






/**
* This function can order a list of object by one of his properties/attributes
* @package Kohette Framework\Basic functions
*/
function KTT_order_objects_by_field($objects, $field, $order = 'ASC') {
  $comparer = ($order === 'DESC') ? "return -strcmp(\$a->{$field},\$b->{$field});" : "return strcmp(\$a->{$field},\$b->{$field});";
  usort($objects, create_function('$a,$b', $comparer));
  return $objects;
}

/**
* This returns a wp_user object with all their postmetas (related to the theme) added
* @package Kohette Framework\Basic functions
*/
function KTT_get_user_by($field, $value) {
    /**
    * First we get the common user object
    */
    $user = get_user_by($field, $value);

    /**
    * If not user then get out
    */
    if (!$user) return;

    /**
    * We use the wpdb object to make a request and get all the postmetas
    * related with the user and the theme
    */
    global $wpdb;
    $theme_prefix = ktt_var_name();
  	$usermetas = $wpdb->get_results("SELECT meta_key, meta_value FROM {$wpdb->usermeta} WHERE user_id = {$user->ID} AND meta_key like '{$theme_prefix}%'");

  	foreach($usermetas as $nodo => $meta ) {
    		$key = ktt_remove_prefix($meta->meta_key);
    		$value = maybe_unserialize($meta->meta_value);

    		$user->data->$key = $value;
  	}

  	return $user;
}


/**
* Alias from KTT_get_user_by
* @package Kohette Framework\Basic functions
*/
function KTT_get_user($user_id = '') {

    /**
    * If not exists a user_id, we try to get the global id of the current user
    */
    if (!$user_id) {
      global $user_ID;
      $user_id = $user_ID;
    }

    if (!$user_id) return;

    return KTT_get_user_by('id', $user_id);
}




/**
* Esta funcion se encarga de extraer una opcion del site relacionada con el theme
* @package Kohette Framework\Basic functions
*/
function KTT_get_theme_option($option_name) {
    return get_option(ktt_var_name($option_name));
}

/**
* Esta funcion se encarga de extraer una opcion del site relacionada con el theme
* @package Kohette Framework\Basic functions
*/
function KTT_get_option($option_name) {
    return get_option(ktt_var_name($option_name));
}




/**
* Check if the user has the required permission to edit an object
* @package Kohette Framework\Basic functions
*/
function KTT_current_user_can_edit_object($object) {

	if (is_int($object) || is_string($object)) $object = KTT_get_post($object);

  $result = false;

	/**
	* We get the id of the logged user if exists
	*/
	global $user_ID;

  /**
  * Si hay una id de usuario y esta es la autora del objeto entonces puede pasar
  */
	if ($user_ID && ($user_ID == $object->post_author) ) $result = true;

  /**
  * Creamos un filtro para que la salida de esta funcion pueda ser editada
  * desde otra funcion
  */
  $result = apply_filters('KTT_current_user_can_edit_object', $object, $user_ID, $result );

  /**
  * Devolvemos false si hemos llegado hasta aqui
  */
	return $result;
}



/**
* This function allows to add all the custom css of the site in one place
* @package Kohette Framework\Basic functions
*/
function KTT_get_site_custom_css() {

    /**
    * result of the function
    */
    $result = '';

    /**
    * We add a filter to add css from other exterior functions
    */
    $result = apply_filters('KTT_add_site_custom_css', $result);

    /**
    * We return the resultado
    */
    return $result;

}


/**
* This function allows to add all the custom js code of the footer site in one place
* @package Kohette Framework\Basic functions
*/
function KTT_get_site_custom_js_footer() {

    /**
    * result of the function
    */
    $result = '';

    /**
    * We add a filter to add css from other exterior functions
    */
    $result = apply_filters('KTT_add_site_custom_js_footer', $result);

    /**
    * We return the resultado
    */
    return $result;

}

/**
* This function allows to add all the custom js code of the header site in one place
* @package Kohette Framework\Basic functions
*/
function KTT_get_site_custom_js_header() {

    /**
    * result of the function
    */
    $result = '';

    /**
    * We add a filter to add css from other exterior functions
    */
    $result = apply_filters('KTT_add_site_custom_js_header', $result);

    /**
    * We return the resultado
    */
    return $result;

}

/**
* Obtain a term object
* @package Kohette Framework\Basic functions
*/
function KTT_get_taxonomy_term($taxonomy, $term_id_or_slug) {

      $result = '';

      /**
      * Comprobamos primero si es un integer, si es asi lo vamos
      * a intentar obtener a partir del term_id
      */
      if (is_numeric($term_id_or_slug)) $result = @get_term_by('id', $term_id_or_slug, $taxonomy);

      /**
      * Si se ha obtenido un term lo devolvemos
      */
      if ($result) return $result;

      /**
      * Si a estas alturas no hemos obtenido aun un term vamos a intentar buscarlo
      * a traves del slug
      */
      $result = @get_term_by('slug', $term_id_or_slug, $taxonomy);

      /**
      * Devolvemos el resultado
      */
      return $result;

}


/**
* Create a taxonomy's term in a easy way
* argumentos: taxonomy and args
* args = array('taxonomy', 'name', 'slug', 'description', 'parent');
* return new term id or wp_error
* @package Kohette Framework\Basic functions
*/
function KTT_create_taxonomy_term( $term_args) {

      /**
      * Si entre la lista de argumentos no encontramos el slug entonces salimos de aquí
      * devolviendo un wp_error
      */
      if (!isset($term_args['slug']) || !isset($term_args['taxonomy'])) return new WP_Error( 'invalid_args', __('Invalid arguments.', 'narratium') );

      /**
      * En primer lugar vamos a comprobar si el termino ya existe, de ser asi devolvemos el termino
      * existente en la funcion
      */
      $term = KTT_get_taxonomy_term($term_args['taxonomy'], $term_args['slug']);
      if ($term) return $term;

      /**
      * Si en los argumentos se ha indicado un parent vamos a corregirlo para obtener
      * el termino correcto si se ha pasado su term_id o su slug
      */
      if (isset($term_args['parent']) && $term_args['parent']) {
          $parent = KTT_get_taxonomy_term($term_args['taxonomy'], $term_args['parent']);
          if ($parent) $term_args['parent'] = $parent->term_id;
      }

      /**
      * Insertamos el término nuevo
      */
      $term = wp_insert_term(
          $term_args['name'],
          $term_args['taxonomy'], // the taxonomy
          array(
            'description'=> @$term_args['description'],
            'slug' => @$term_args['slug'],
            'parent'=> @$term_args['parent']
          )
      );

      /**
      * Si ha ocurrido un error devolvemos dicho error
      */
      if (is_wp_error($term)) return $term;

      /**
      * Devolvemos el objeto term creado
      */
      return KTT_get_taxonomy_term($term_args['taxonomy'], $term['term_id']);

}







/**
* get the greatest common divisor of two numbers
* @package Kohette Framework\Basic functions
*/
function KTT_greatest_common_divisor( $a, $b ){
    return ($a % $b) ? KTT_greatest_common_divisor($b,$a % $b) : $b;
}

/**
* return the aspect ratio
* @package Kohette Framework\Basic functions
*/
function KTT_ratio( $x, $y ){
    $gcd = KTT_greatest_common_divisor($x, $y);
    return ($x/$gcd).':'.($y/$gcd);
}


/**
* Special attrs for body tag element
* @package Kohette Framework\Basic functions
*/
function KTT_body_attrs($attrs = '') {
    $result = '';
    $result .= ' ' . $attrs;
    $result = apply_filters( 'KTT_body_attrs', $result );
    echo $result;
}


/**
 * Get human readable time difference between 2 dates
 *
 * Return difference between 2 dates in year, month, hour, minute or second
 * The $precision caps the number of time units used: for instance if
 * $time1 - $time2 = 3 days, 4 hours, 12 minutes, 5 seconds
 * - with precision = 1 : 3 days
 * - with precision = 2 : 3 days, 4 hours
 * - with precision = 3 : 3 days, 4 hours, 12 minutes
 *
 * From: http://www.if-not-true-then-false.com/2010/php-calculate-real-differences-between-two-dates-or-timestamps/
 *
 * @package Kohette Framework\Basic functions
 *
 * @param mixed $time1 a time (string or timestamp)
 * @param mixed $time2 a time (string or timestamp)
 * @param integer $precision Optional precision
 * @return string time difference
 */
function KTT_get_date_diff( $time1, $time2, $precision = 2 ) {
	// If not numeric then convert timestamps
	if( !is_int( $time1 ) ) {
		$time1 = strtotime( $time1 );
	}
	if( !is_int( $time2 ) ) {
		$time2 = strtotime( $time2 );
	}

	// If time1 > time2 then swap the 2 values
	if( $time1 > $time2 ) {
		list( $time1, $time2 ) = array( $time2, $time1 );
	}

	// Set up intervals and diffs arrays
	$intervals = array( 'year', 'month', 'day', 'hour', 'minute', 'second' );
	$diffs = array();

	foreach( $intervals as $interval ) {
		// Create temp time from time1 and interval
		$ttime = strtotime( '+1 ' . $interval, $time1 );
		// Set initial values
		$add = 1;
		$looped = 0;
		// Loop until temp time is smaller than time2
		while ( $time2 >= $ttime ) {
			// Create new temp time from time1 and interval
			$add++;
			$ttime = strtotime( "+" . $add . " " . $interval, $time1 );
			$looped++;
		}

		$time1 = strtotime( "+" . $looped . " " . $interval, $time1 );
		$diffs[ $interval ] = $looped;
	}

	$count = 0;
	$times = array();
	foreach( $diffs as $interval => $value ) {
		// Break if we have needed precission
		if( $count >= $precision ) {
			break;
		}
		// Add value and interval if value is bigger than 0
		if( $value > 0 ) {
			if( $value != 1 ){
				$interval .= "s";
			}
			// Add value and interval to times array
			$times[] = $value . " " . $interval;
			$count++;
		}
	}

	// Return string with times
	return implode( ", ", $times );
}







/**
* Return a date as product of the sum of a date plus working days, skipping the weekends and custom dates
* http://codereview.stackexchange.com/questions/51895/calculate-future-date-based-on-business-days
*
* @package Kohette Framework\Basic functions
*
* @param DateTime   $startDate       Date to start calculations from
* @param DateTime[] $holidays        Array of holidays, holidays are no considered business days.
* @param int[]      $nonBusinessDays Array of days of the week which are not business days.
* USE:
$calculator = new KTT_business_days_calculator(
    new DateTime(), // Today
    [new DateTime("2014-06-01"), new DateTime("2014-06-02")],
    [KTT_business_days_calculator::SATURDAY, KTT_business_days_calculator::FRIDAY]
);

$calculator->add_business_days(3); // Add three business days

var_dump($calculator->get_timestamp());
*/
class KTT_business_days_calculator {

    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;
    const SATURDAY  = 6;
    const SUNDAY    = 7;


    public function __construct(DateTime $startDate, array $holidays, array $nonBusinessDays) {
        $this->date = $startDate;
        $this->holidays = $holidays;
        $this->nonBusinessDays = $nonBusinessDays;
    }

    public function add_business_days($howManyDays) {
        $i = 0;
        while ($i < $howManyDays) {
            $this->date->modify("+1 day");
            if ($this->is_business_day($this->date)) {
                $i++;
            }
        }
    }

    public function get_date() {
        return $this->date;
    }

    public function get_timestamp() {
    	return $this->date->getTimestamp();
    }

    private function is_business_day(DateTime $date) {
        if (in_array((int)$date->format('N'), $this->nonBusinessDays)) {
            return false; //Date is a nonBusinessDay.
        }
        foreach ($this->holidays as $day) {
            if ($date->format('Y-m-d') == $day->format('Y-m-d')) {
                return false; //Date is a holiday.
            }
        }
        return true; //Date is a business day.
    }
}



/**
* Get a image path in the size indicated
*
* @package Kohette Framework\Basic functions
*/
function KTT_scaled_image_path($attachment_id, $size = 'thumbnail') {
    $file = get_attached_file($attachment_id, true);
    if (empty($size) || $size === 'full') {
        // for the original size get_attached_file is fine
        return realpath($file);
    }
    if (! wp_attachment_is_image($attachment_id) ) {
        return false; // the id is not referring to a media
    }
    $info = image_get_intermediate_size($attachment_id, $size);
    if (!is_array($info) || ! isset($info['file'])) {
        return false; // probably a bad size argument
    }

    return realpath(str_replace(wp_basename($file), $info['file'], $file));
}

/**
* Get a image url in the size indicated
*
* @package Kohette Framework\Basic functions
*/
function KTT_scaled_image_url($attachment_id, $size = 'thumbnail') {

  $medium_array = image_downsize( $attachment_id, $size);
  $medium_path = $medium_array[0];
  return $medium_path;

}
