<?php
/*
Module Name: Admin pages creator module.
Module ID: admin-pages-creator
Module Description: Classes to add admin pages to wp admin
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/




/**
* This class helps to create admin menus
* @package Kohette Framework
*/
class KTT_admin_menu {

    public $id;
    private $page_title;
    private $menu_title;
    private $capability = 'manage_options';
    private $page;
    private $icon;
    private $position;

    /**
    * Constructor de classes
    */
    public function __construct($args) {

        if (isset($args['id']))             $this->id = $args['id'];
        if (isset($args['page_title']))     $this->page_title = $args['page_title'];
        if (isset($args['menu_title']))     $this->menu_title = $args['menu_title'];
        if (isset($args['capability']))     $this->capability = $args['capability'];
        if (isset($args['page']))           $this->page = $args['page'];
        if (isset($args['icon']))           $this->icon = $args['icon'];
        if (isset($args['position']))       $this->position = $args['position'];

        //add_filter( 'admin_menu' , array( &$this , 'register_admin_menu' ) );

        return $args;

    }

    function KTT_admin_menu($args) {self.__construct($args);}

    function register_admin_menu() {

            //add_menu_page(
            add_theme_page(
                $this->page_title,
                $this->menu_title,
                $this->capability,
                $this->id,
                $this->page,
                $this->icon,
                $this->position
            );


    }








}






/**
* This class helps to create admin submenus
* @package Kohette Framework
*/
class KTT_admin_submenu {

    private $id;
    private $parent;
    private $page_title;
    private $page_description;
    private $alert_message;
    private $menu_title;
    private $capability = 'manage_options';
    private $page;
    private $load_hook;

    /**
    * Constructor de classe
    */
    public function __construct($args) {

        if (isset($args['id']))                 $this->id = $args['id'];
        if (isset($args['parent']))             $this->parent = $args['parent'];
        if (isset($args['page_title']))         $this->page_title = $args['page_title'];
        if (isset($args['page_description']))   $this->page_description = $args['page_description'];
        if (isset($args['alert_message']))      $this->alert_message = $args['alert_message'];
        if (isset($args['menu_title']))         $this->menu_title = $args['menu_title'];
        if (isset($args['capability']))         $this->capability = $args['capability'];
        if (isset($args['page']))               $this->page = $args['page'];
        if (isset($args['load_hook']))          $this->load_hook = $args['load_hook'];


        if (!isset($this->page) || !$this->page)  $this->page = array( $this, 'default_options_page' );
        if (isset($this->page) && $this->page == 'none') $this->page = '';

        add_filter( 'admin_menu' , array( &$this , 'register_admin_submenu' ) );

        // save the submenu order
        global $submenus_order;
        $submenus_order[$this->parent][$this->id] = $args['menu_order'];

        return $args;

    }

    function KTT_admin_submenu($args) {$self.__construct($args);}


    function register_admin_submenu() {

        if($this->submenu_exists())  return;

        //$hook = add_submenu_page(
        $hook = add_theme_page(
                //$this->parent,
                $this->page_title,
                $this->menu_title,
                $this->capability,
                $this->id,
                $this->page
        );

        if ($this->load_hook) add_action( "load-$hook", $this->load_hook);

    }

    function get_hook_suffix() {
      return $this->hook_suffix;
    }


    function default_options_page() {
        // Set class property

        ?>

        <style>
            .nav-tab-wrapper + form > input + input + input + h3 > hr {display:none !important;}
            /*
            .form-table
             {
                background-color:rgba(255,255,255,0.83);
                padding-left:30px;
                border-radius:3px;

            }

            .form-table th {
                padding-left:20px;
            }
            */
        </style>

        <div class="wrap">


            <?php //screen_icon(); ?>
            <h2><?php echo $this->page_title;?></h2>

            <?php if( isset($_GET['settings-updated']) ) { ?>
                <div id="message" class="updated">
                    <p><strong><?php _e('Settings saved.', 'narratium') ?></strong></p>
                </div>
            <?php } ?>


            <?php if (isset($this->page_description)) {?><p><?php echo $this->page_description;?></p><?php } ?>
            <?php if (isset($this->alert_message)) {?>
            <div class="updated">
                <p>
                    <?php echo $this->alert_message;?>
                </p>
            </div>
            <?php }?>


            <?php $page_id = $this->create_inline_submenu();?>








            <form method="post" action="options.php">
            <?php

                do_action( 'KTT_FRAMEWORK_before_render_setting_field');

                // This prints out all hidden setting fields
                settings_fields( $page_id);
                do_settings_sections( $page_id );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }



    function correct_id() {
        global $submenu;
        $page = $this->id;

        $id = $this->id ;
        $parent = $this->parent;
        $parent_parent = @$submenu[$this->parent][2][2];

        if ( $parent_parent && isset($submenu[$this->id]) ) $page = @$submenu[$this->id][0][2];

        if (!$page) $page =  $this->id;

        return $page;
    }

    /**
    * get the correct current page
    */
    function get_master_page_id() {
        global $submenu;
        $page = '';

        /*
        echo '<pre>';
        print_r($submenu);
        echo '</pre>';
        */


        $id = $this->id ;
        $parent = $this->parent;
        $parent_parent = @$submenu[$this->parent][2][2];



        if (in_array( $this->id, array_keys($submenu)) && $this->parent) $page = $this->id;

        if ( !$parent_parent && !$page ) $page = $this->parent;

        /*
        echo 'id: ' . $id .'<br>';
        echo 'parent: ' . $parent . '<br>';
        echo 'parent parent: ' . $parent_parent . '<br>';
        */


        return $page;
    }



    /**
    *  Create the inline submenu for pages
    */
    function create_inline_submenu() {
        global $submenu;
        $return_page_id = $this->correct_id();
        $current_page = $this->get_master_page_id();
        if (isset($_REQUEST['tab'])) $return_page_id = $_REQUEST['tab'];

        /**
        * Solo mostramos las pestañas si hay mas de dos paginas
        */
        if ($current_page && count($submenu[$current_page]) > 2 ) { ?>


        <h2 class="nav-tab-wrapper">
            <?php foreach ($submenu[$current_page] as $key => $page) {
                if ($key == $page[2]) continue;?>

                    <a class="nav-tab <?php if($return_page_id == $page[2]){;?>nav-tab-active<?php } ?>" href="<?php echo admin_url( 'admin.php?page=' . $this->id . '&tab=' . $page[2] );?>">
                        <?php echo $page[0];?>
                    </a>

            <?php }?>
        </h2>


        <?php }



        return $return_page_id;;

    }

    /**
    *check if submenu already exists
    */
    function submenu_exists(){

        global $submenu;

        if ($submenu) {
            if (isset($submenu[$this->parent])) {
                foreach ($submenu[$this->parent] as $menu => $menu_vars) {

                    if ($menu_vars[2] == $this->id) return true;

                }
            }
        }

        return false;

    }


}






// --------------------------------------------------------------------------------------------------------------




// change the order of the submenus
add_filter( 'custom_menu_order', 'KTT_submenu_order' );
function KTT_submenu_order( $menu_ord )
{
    global $submenu, $submenus_order;



    if ($submenus_order) {


        foreach ($submenus_order as $menu_parent => $submenu_array) {

            $arr = array();

            if (isset($submenu[$menu_parent])) {



                // order
                asort($submenu_array);
                foreach ($submenu_array as $submenu_slug => $order) {


                    foreach ($submenu[$menu_parent] as $key => $value) {

                        if ($value[2] == $submenu_slug) $arr[] = $submenu[$menu_parent][$key];

                    }


                }


            }

            $submenu[$menu_parent] = $arr;


        }



    }

    return $menu_ord;
}







// Re-order setting fields
add_filter( 'KTT_FRAMEWORK_before_render_setting_field', 'KTT_order_setting_fields' );
function KTT_order_setting_fields() {
            global $wp_settings_sections, $wp_settings_fields, $sections_order;



            if ($sections_order) {
                $arr = array();

                foreach ($sections_order as $page => $sections) {

                    if (isset($wp_settings_sections[$page])) {


                        // order
                        asort($sections);
                        foreach ($sections as $section => $order) {


                            foreach ($wp_settings_sections[$page] as $key => $value) {

                                if($key == $section) {
                                    $arr[$page][$key] = $wp_settings_sections[$page][$key];
                                }


                            }


                        }

                    }

                }


            }


            $wp_settings_sections = $arr;




}
