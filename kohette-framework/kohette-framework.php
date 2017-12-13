<?php
/*
Name: Kohette WDT Framework
URI: https://github.com/Kohette/Kohette-WordPress-Dev-Tools/
Description: Load the theme configuration and custom functions & features.
Author: Rafael Martín
Author URI: http://kohette.com/
Version: 1.5.9
*/



/**
* Definimos la global que tendra un array con todas las opciones
* generales del site relacionadas con el themes
* @package Kohette Framework
*/
global $KTT_theme_options;


/**
* Clase que maneja el Framework.
*
* Esta clase inicializa todos los procesos requeridos para implementar kohette en
* el theme.
*
* @package Kohette Framework
*
* @param array $theme_config Array conteniendo las variables iniciales para el framework como textdomain, etc.
*/
class kohette_framework {

    /**
    * configuración
    */
    private $framework_config = array(
      'constants' => array(),
      'modules' => array(),
      'plugins' => array(),
    );

    /**
    * Available modules
    */
    private $framework_available_modules;

    /**
    * Constructor de Clase
    */
    public function __construct($framework_config = '') {

            /**
            * Declaramos las variables globales del core del framework
            */
            $this->set_fw_constants();

            /**
            * En primer lugar vamos a obtener la lista completa de modulos
            * disponibles en el framework
            */
            $this->framework_available_modules = $this->get_available_framework_modules();


          	//$this->load_framework_modules(); // load framework handy classes
            //$this->create_theme_options_page();
            //$this->load_plugins();

    }

    /**
    * Constructor de Clase
    */
    public function kohette_framework($framework_config) {
            self::__construct($framework_config);
    }

    /**
    * Esta funcion se encarga de navegar por los directorios dentro de la carpeta modules
    * y crea un array con la informacion de cada uno de los modules encontrados
    *
    */
    private function get_available_framework_modules() {

          /**
          * Aqui guardaremos los resultados
          */
          $result = array();

          /**
          * Itineramos por cada uno de los modulos dentro de la carpeta modules
          */
          foreach (glob(dirname(__FILE__). "/modules/*", GLOB_ONLYDIR) as $filename) {

              /**
              * Obtenemos los datos
              */
              $filedata = get_file_data(dirname(__FILE__) . "/modules/" . basename($filename) . '/' . basename($filename) . '.php', array(
                'Module Name' => 'Module Name',
                'Module ID' => 'Module ID',
                'Module Description' => 'Module Description',
                'Module Required' => 'Module Required',
                'Module Version' => 'Module Version',
                'Module Priority' => 'Module Priority',
              ));

              /**
              * A los datos obtenidos le sumamos la path de archivo
              */
              if ($filedata) $filedata['Module Path'] = dirname(__FILE__). '/modules/' . basename($filename) . '/' . basename($filename) . '.php';

              /**
              * Añadimos los datos al Array
              */
              if ($filedata) $result[$filedata['Module ID']] = $filedata;

              //include('modules/' . basename($filename) . '/' . basename($filename) . '.php') ;
          };

          /**
          * Devovlemos el Array
          */
          return $result;

    }

    /**
    * Esta funcion se encarga de guardar en una global un array de opciones
    * generales relacionadas con el theme.
    *
    * @global array $KTT_theme_options Array que contiene la informacion inicial de clase.
    * @global object $wpdb.
    */
    private function set_theme_options_global() {

          /**
          * Invocamos la variable wpdb
          */
          global $wpdb, $KTT_theme_options;

          /**
          * En result vamos a formar el array Final
          */
          $result = new stdClass();;

          /**
          * Ejecutamos una query que estraera todas las opciones del theme guardadas
          */
          $options = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '" . ktt_var_name() . "%%'" );

          /**
          * Si no hemos encontrado opciones salimos de aqui
          */
          if (!$options) return;

          /**
          * Itineramos por cada resultado y lo vamos añadiendo al result
          */
          foreach ($options as $key => $value) if ($key) @$result->{ktt_remove_prefix($value->option_name)} = maybe_unserialize($value->option_value);

          /**
          * Guardamos en la global
          */
          $KTT_theme_options = $result;

    }




    /**
    * set the default constants of the framework
    */
    private function set_fw_constants() {

        /**
        * this defines the path of the resources of the framework
        */
        if (!defined('KOHETTE_FW_RESOURCES')) define("KOHETTE_FW_RESOURCES" , str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname(__FILE__)) . '/resources/');

    }


    /**
    * Esta funcion permite añadir configuraciones de manera parcial al array completo
    * de configuracion del framework que se cargará mas tarde
    */
    public function add_to_config($config) {

        /**
        * Añadimos la configuracion a la configuracion global
        */
        $this->framework_config = array_merge($this->framework_config, $config);

    }

    /**
    * set the basic configuration of the theme
    */
    private function set_config() {

        /**
        * We add the default theme config
        */
        $framework_config = $this->framework_config; //wp_parse_args($this->framework_config, $this->load_theme_data_constants());

        /**
        * Antes de crear la instancia del framework con el array de configuración aplicamos un filter
        * para añadir a la configuración informacion que se haya podido añadir por otras funciones
        * esto es util para que cada theme añada su propia configuración del framework.
        */
        $framework_config = apply_filters( 'KTT_theme_config', $framework_config ); // Deprecated
        $framework_config = apply_filters( 'KTT_framework_config', $framework_config );

        /**
        * Si no tenemos constants salimos de aqui
        */
        if (!$framework_config) return;

        /**
        * Pasamos la configuracion al global del objeto
        */
        //$this->framework_config = $framework_config;

        /**
        * We create the defined constants for the theme
        */
        if (isset($framework_config['constants'])) {
        foreach($framework_config['constants'] as $item => $value) {

            $this->$item = $framework_config['constants'][$item];
            define("THEME_" . strtoupper($item) , $this->$item); //deprecated
            define("KTT_" . strtoupper($item) , $this->$item);

        }
        }

    }



    /**
    * load framework custom functions
    */
    private function load_framework_functions() {
		      include('functions/basic-functions.php');
    }



    /**
    * load framework handy classes
    */
    public function load_framework_modules() {

        /**
        * A traves de la configuracion obtenemos la lista de modules que debemos cargarç
        */
        $modules_to_load = $this->framework_config['modules'];

        /**
        * Si no hay lista de modulos los cargamos todos
        */
        if (!$modules_to_load) $modules_to_load = array_keys($this->framework_available_modules);

        /**
        * Itineramos por cada uno de los modulos y los vamos incluyendo
        */
        foreach( $modules_to_load as $module_id) {
          require_once($this->framework_available_modules[$module_id]['Module Path']);
        }

    }


    /**
    * create the theme options admin page/menu
    */
    public function create_theme_options_page() {

        $args = array();
        $args['id']             = 'theme-options';
        $args['page_title']     = 'Theme Options';
        $args['menu_title']     = 'Theme options';
        $args['page']           = ''; //array( &$this, 'default_theme_options_page');

        $new_admin_page = new KTT_admin_menu($args);

    }

    function default_theme_options_page() {
        global $submenu;

    }



    /**
    * Start trigger
    */
    function start_kohette_framework() {

        $this->set_config();
        $this->load_framework_modules(); // load framework handy classes
        //$this->create_theme_options_page();
        $this->load_framework_plugins();

        global $pagenow;
        if ( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {
            if (function_exists("set_default_options")) set_default_options();
        }
    }



    /**
    * include the plugin file in the theme
    */
    private function run_activate_plugin( $plugin_source ) {
	    include($plugin_source);
	  }


	  /**
    * load the list of plugins
    */
    public function load_framework_plugins() {

        /**
        * Antes de cargar definitivamente el array de plugins aplicamos un filter para comprobar
        * si otras funciones del theme quieren añadir archivos para incluir
        * Esto es util para que cada theme añada sus archivos (post_types, scripts, etc)
        */
        $plugins = apply_filters( 'KTT_plugins', $this->framework_config['plugins']);

        /**
        * Si no hay plugins salimos de aqui
        */
        if (!$plugins) return;

      	foreach ($plugins as $plugin => $plugin_config) {

      		$this->run_activate_plugin($plugin_config['source']);

      	}

    }

    /**
    * load theme data through style.css
    */
    public function load_theme_data_constants() {

        /**
        * Obtenemos los datos del theme
        */
        $theme_data = wp_get_theme();

        /**
        * Create the array data
        */
        $this->framework_config['constants']['textdomain'] = $theme_data->get("TextDomain");
        $this->framework_config['constants']['prefix'] = $this->framework_config['constants']['textdomain'] . '_';

        /**
        * this define a constant for every folder of the theme directory
        * if the folder is named "the libs" the constant with the path will  defined as THEME_THE_LIBS_PATH
        */
        foreach (glob(get_stylesheet_directory() . "/*", GLOB_ONLYDIR) as $f) {

            $name = basename($f);
            $name = str_replace(' ', '_', $name);
            $name = str_replace('-', '_', $name);

            $this->framework_config['constants'][strtoupper($name) . '_PATH'] = $f;

        };

    }




}
