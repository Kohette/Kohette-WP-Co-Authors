<?php
/*
Module Name: Settings Creator Module.
Module ID: settings-creator
Module Description: Classes to add custom options in the admin settings pages
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/


/**
* Llamamos tambien a las clases referentes a las opciones de customize
*/
require_once('customize-creator.php');


// add section to admin pages
class KTT_new_section {

    private $section_id;
    private $section_name;
    private $section_description;
    private $section_page;
    private $section_priority;

    /**
    * Constructor
    */
    function __construct($args) {

        $this->section_id = $args['section_id'];
        $this->section_name = $args['section_name'];
        $this->section_description = @$args['section_description'];
        $this->section_page = $args['section_page'];

        add_filter( 'admin_init' , array( &$this , 'register_section' ) );

    }

    public function KTT_new_section($args) {$self.__construct($args);}

    function register_section() {

            add_settings_section(
                $this->section_id,
                '<hr><br>' . $this->section_name,
                array( &$this , 'section_description' ),
                $this->section_page
            );

    }


    function section_description() {
        ?>
        <p><?php echo $this->section_description;?></p>
        <?php
    }


}








// add options to admin pages
class KTT_new_setting {

        public $option_id;
        public $option_name;
        public $option_label;
        public $option_placeholder;
        public $option_description;
        public $option_type;
        public $option_order;
        public $option_type_vars;
        public $option_page;
        public $option_page_section;
        public $option_style;
        public $option_default;


        /**
        * Constructor
        */
        function __construct($args) {

            global $KTT_custom_theme_settings;

            if (isset($args['option_id']))              $this->option_id            = $args['option_id'];
            if (isset($args['option_name']))            $this->option_name          = $args['option_name'];
            if (isset($args['option_label']))           $this->option_label         = $args['option_label'];
            if (isset($args['option_placeholder']))     $this->option_placeholder   = $args['option_placeholder'];
            if (isset($args['option_description']))     $this->option_description   = $args['option_description'];
            if (isset($args['option_type']))            $this->option_type          = $args['option_type'];
            if (isset($args['option_order']))           $this->option_order         = $args['option_order'];
            if (isset($args['option_type_vars']))       $this->option_type_vars     = $args['option_type_vars'];
            if (isset($args['option_style']))           $this->option_style         = $args['option_style'];
            if (isset($args['option_page']))            $this->option_page          = $args['option_page'];
            if (isset($args['option_page_section']))    $this->option_page_section  = $args['option_page_section'];
            if (isset($args['option_default']))         $this->option_default       = $args['option_default'];

            if(!isset($this->option_page_section)) $this->option_page_section = $this->option_page . $this->option_id;

            add_filter( 'admin_init' , array( &$this , 'register_fields' ) );

            $KTT_custom_theme_settings[$this->option_id] = $args;

        }



        public function KTT_new_setting( $args ) {$self.__construct($args);}

        function register_fields() {


            register_setting( $this->option_page, $this->option_id );


            // check if the section already exists, if not, we create it
            global $wp_settings_sections;
            $sections_in_page = array();
            if (isset($wp_settings_sections[$this->option_page])) $sections_in_page = @array_keys((array)$wp_settings_sections[$this->option_page]);

            if(!in_array($this->option_page_section, $sections_in_page) ) {
                add_settings_section( $this->option_page_section, '', '', $this->option_page );
            }

            // save the order for the section
            global $sections_order;
            $sections_order[$this->option_page][$this->option_page_section] = $this->option_order;

            add_settings_field(
                    $this->option_id,
                    '<label for="' . $this->option_id . '">'.  $this->option_name .'</label>' ,
                    array(&$this, 'fields_html') ,
                    $this->option_page,
                    $this->option_page_section
                    );

            // if is a font field the save process is different
            do_action('KTT_settings_option_register', $this);

        }


        /**
        * This functions only exists to enable compatiblity between settings creator and customize creator
        */
        function link ($var = '') {}
        function value ($var = '') {}

        function fields_html() {
            $option_default = '';
            if(isset($this->option_default)) $option_default = $this->option_default;
            $value = get_option( $this->option_id, $option_default );

            /**
            * Si la opcion tiene un callback significa que una funcion exterior va a generar
            * los campos/formulario de la opción, por lo tanto no llamamos al hook que se
            * encarga de generar los campos de opcion predeterminados del framework
            */
            if ($this->option_type == 'custom') call_user_func($this->option_type_vars, $this, $value);
            else do_action('KTT_settings_option_field', $this, $value);


        }


}



// load options
foreach (glob(dirname(__FILE__). "/options/*", GLOB_ONLYDIR) as $filename) {
            include('options/' . basename($filename) . '/' . basename($filename) . '.php') ;
};


// load options css
include('options-css/options-css.php');
