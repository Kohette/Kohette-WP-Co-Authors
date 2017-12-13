<?php
/**
 * Settings creator module.
 *
 * Classes to add custom options in the admin settings pages
 * https://core.trac.wordpress.org/browser/tags/4.7/src/wp-includes/class-wp-customize-control.php#L15
 */



function KTT_register_wp_customize_control_extended($wp_customizer) {
    /**
    * Esta clase la utilizaremos para conectar con nuestros controles personalizados
    */
    class KTT_Customize_Control_Extended extends WP_Customize_Control {

          public $option_id;
          public $option_name;
          public $option_label;
          public $option_description;
          public $option_type;
          public $option_priority;
          public $option_type_vars;
          public $option_section;
          public $option_default;


          public function print_parent_render() {
              parent::render_content();
          }

          /**
          * Esta funcion se encarga de mostrar el html del control
          * si no encontramos un control personlaizado pasamos a la clase base
          */
          public function render_content() {

              /**
              * Si el type actual no esta definido dentro del array de opciones entonces pasamos
              * a la funcion base
              */
              /*if (!in_array($this->type, array_keys($options) )) {
                parent::render_content();
                return;
              }*/


              /*
              if ($this->option_type == 'checkbox') {
                $this->choices = $this->option_type_vars;
                parent::render_content();
                return;
              }
              */



              ?>
                 <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                 <span class="description customize-control-description"><?php echo $this->description; ?></span>
              <?php


              /**
              * Incluimos la ruta del script donde se encuentnras las opciones de la custom option
              */
              do_action('KTT_settings_option_field', $this, $this->value($this->id));

              ?>
              <hr style="margin-top:19px">
              <?php


          }
    }
}
add_action( 'customize_register', 'KTT_register_wp_customize_control_extended', 1 );



/**
* Panels
*/
class KTT_new_customize_panel {

    private $panel_id;
    private $panel_title;
    private $panel_description;
    private $panel_priority;
    private $panel_capability;
    private $panel_theme_supports;

    /**
    * Constructor
    */
    function __construct($args) {

        if (isset($args['panel_id']))                 $this->panel_id                 = $args['panel_id'];
        if (isset($args['panel_title']))              $this->panel_title              = $args['panel_title'];
        if (isset($args['panel_description']))        $this->panel_description        = $args['panel_description'];
        if (isset($args['panel_priority']))           $this->panel_priority           = $args['panel_priority'];
        if (isset($args['panel_capability']))         $this->panel_capability         = $args['panel_capability'];
        if (isset($args['panel_theme_supports']))     $this->panel_theme_supports     = $args['panel_theme_supports'];

        add_action( 'customize_register', array( &$this , 'add_customize_panel' ), 1 );


    }

    public function KTT_new_panel($args) {$self.__construct($args);}


    function add_customize_panel($wp_customize) {
        $wp_customize->add_panel( $this->panel_id, array(
            'priority'       => $this->panel_priority,
            'capability'     => $this->panel_capability,
            'theme_supports' => $this->panel_theme_supports,
            'title'          => $this->panel_title,
            'description'    => $this->panel_description,
        ) );
    }

}




/**
* Panels
*/
class KTT_new_customize_section {

    private $section_id;
    private $section_title;
    private $section_description;
    private $section_priority;
    private $section_capability;
    private $section_theme_supports;
    private $section_panel;

    /**
    * Constructor
    */
    function __construct($args) {

        if (isset($args['section_id']))                 $this->section_id                 = $args['section_id'];
        if (isset($args['section_title']))              $this->section_title              = $args['section_title'];
        if (isset($args['section_description']))        $this->section_description        = $args['section_description'];
        if (isset($args['section_priority']))           $this->section_priority           = $args['section_priority'];
        if (isset($args['section_capability']))         $this->section_capability         = $args['section_capability'];
        if (isset($args['section_theme_supports']))     $this->section_theme_supports     = $args['section_theme_supports'];
        if (isset($args['section_panel']))              $this->section_panel              = $args['section_panel'];

        /**
        * Registramos todo
        */
        add_action( 'customize_register', array( &$this , 'add_customize_section' ), 1 );

    }

    public function KTT_new_section($args) {$self.__construct($args);}

    function add_customize_section($wp_customize) {
        $wp_customize->add_section( $this->section_id, array(
            'priority'        => $this->section_priority,
            'capability'      => $this->section_capability,
            'theme_supports'  => $this->section_theme_supports,
            'title'           => $this->section_title,
            'description'     => $this->section_description,
            'panel'           => $this->section_panel,
        ) );
    }





}







// add options to admin pages
class KTT_new_customize_setting {

        public $option_id;
        public $option_name;
        public $option_label;
        public $option_description;
        public $option_type;
        public $option_priority;
        public $option_type_vars;
        public $option_section;
        public $option_default;
        public $option_settings;
        public $input_attrs;

        /**
        * Constructor
        */
        function __construct($args) {

            if (isset($args['option_id']))              $this->option_id            = $args['option_id'];
            if (isset($args['option_name']))            $this->option_name          = $args['option_name'];
            if (isset($args['option_label']))           $this->option_label         = $args['option_label'];
            if (isset($args['option_description']))     $this->option_description   = $args['option_description'];
            if (isset($args['option_type']))            $this->option_type          = $args['option_type'];
            if (isset($args['option_priority']))        $this->option_priority      = $args['option_priority'];
            if (isset($args['option_type_vars']))       $this->option_type_vars     = $args['option_type_vars'];
            if (isset($args['option_section']))         $this->option_section       = $args['option_section'];
            if (isset($args['option_default']))         $this->option_default       = $args['option_default'];
            if (isset($args['option_settings']))        $this->option_settings      = $args['option_settings'];
            if (isset($args['input_attrs']))            $this->input_attrs          = $args['input_attrs'];

            /**
            * Antes de llamar al customize register corregimos los atributos de la clase
            * si es necesario
            */
            $this->fix_attributes();

            /**
            * Registramos el valor por defecto de la opcion, esto es util para iniciar el
            * theme con las opciones correctas
            */
            add_filter('KTT_starter_content_data', array(&$this, 'register_setting_default_value'), 5, 1);

            /**
            * el registro!
            */
            add_action( 'customize_register', array( &$this , 'add_customize_control' ), 5 );


        }

        public function KTT_new_setting( $args ) {$self.__construct($args);}



        /**
        * Esta funcion se encarga de registrar los defaults
        */
        function register_setting_default_value($defaults) {

            /**
            * Registramos el valor por defecto de esta opcion en el array de starter-content
            */
            $defaults['options'][$this->option_id] = $this->option_default;

            /**
            * Devolvemos el array modificado
            */
            return $defaults;

        }

        function KTT_common_add_setting_sanitize_callback($value) {
          return $value;
        }

        /**
        * Esta funcion se encarga de añadir la opcion en la pagina de customize si es necesario
        */
        function add_customize_control($wp_customize) {

              /**
              * Si no se ha definido unas settings ponemos la id del control
              */
              if (!$this->option_settings) $this->option_settings = $this->option_id;

              /**
              * Si la settings no es un array lo transformamos en uno para hacer mas facil
              * su manejo
              */
              if (!is_array($this->option_settings)) {
                  $cucu = $this->option_settings;
                  $this->option_settings = array();
                  $this->option_settings[$cucu] = $cucu;
              }

              /**
              * Itineramos por cada setting y la registramos
              */
              foreach ($this->option_settings as $setting_key => $setting_value) {

                  $wp_customize->add_setting(
                    $setting_value,

                    array(
                          'default' => (is_array($this->option_default) ? @$this->option_default[$setting_key] : $this->option_default),
                          'type' => 'option',
                          'sanitize_callback' => array(&$this, 'KTT_common_add_setting_sanitize_callback'),
                    )
                  );

              }


              $wp_customize->add_control( new KTT_Customize_Control_Extended(
                $wp_customize,
                $this->option_id,
                array(
                  'option_id' => $this->option_id,
                  'label' => $this->option_name, //Yes, its correct
                  'description' => $this->option_description,
                  'section' => $this->option_section,
                  'type' => $this->option_type,
                  'option_type' => $this->option_type,
                  'settings' => $this->option_settings,
                  'priority' => $this->option_priority,
                  'option_type_vars' => $this->option_type_vars,
                  ) )
              );




        }



        /**
        *^Esta funcion se encarga de arreglar los atributos de la clase si es encesario, al gunos
        * contoladores como el "font" necesita ciertas modificaciones
        */
        public function fix_attributes() {


            /**
            * Si la opcion tiene un typevars con un array entonces vamos a formar
            * esta cosa rara
            */
            if ($this->option_type == 'checkboxes') {
            if ($this->option_type_vars && is_array($this->option_type_vars)){

              $this->option_settings['option_id']     = $this->option_id;
              foreach ($this->option_type_vars as $id => $value) if (!is_array($value)) $this->option_settings[$id] = $this->option_id . '[' . $id . ']';

            }
            }




            if ($this->option_type == 'font') {

                $this->option_settings['option_id']           = $this->option_id;
                $this->option_settings['font_family']         = $this->option_id . '[font_family]';
                $this->option_settings['font_size']           = $this->option_id . '[font_size]';
                $this->option_settings['font_size_unit']      = $this->option_id . '[size_unit]';
                $this->option_settings['font_weight']         = $this->option_id . '[font_weight]';
                $this->option_settings['line_height']         = $this->option_id . '[line_height]';
                $this->option_settings['selector']            = $this->option_id . '[selector]';
                $this->option_settings['load_all_weights']    = $this->option_id . '[load_all_weights]';

                // Fix especial
                $this->option_default['selector'] = $this->option_type_vars['selector'];

                // if is a font field the save process is different
                add_filter( 'KTT_theme_css_options', function($options){
                    $options[] = $this->option_id;
                    return $options;
                });

            }




            /**
            * Para una opcion CSS tambien tenemos que hacer un pequeño arreglo
            */
            if ($this->option_type == 'css_select') {

                $this->option_settings['option_id']       = $this->option_id;
                $this->option_settings['selector']        = $this->option_type_vars['selector'];
                $this->option_settings['property']        = $this->option_type_vars['property'];
                //$this->option_settings['values']          = $this->option_id . '[values]';
                $this->option_settings['value']           = $this->option_id . '[value]';

                $this->option_default['selector'] = $this->option_type_vars['selector'];
                $this->option_default['property'] = $this->option_type_vars['property'];

                add_filter( 'KTT_theme_css_options', function($options){
                    $options[] = $this->option_id;
                    return $options;
                });

            }

        }
}
