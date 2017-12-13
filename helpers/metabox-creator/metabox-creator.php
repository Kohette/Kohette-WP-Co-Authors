<?php
/*
Module Name: Metabox Creator Module.
Module ID: metabox-creator
Module Description: Classes to add metaboxes to post types
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/
namespace ktt_helpers.classes {

if (!class_exists('KTT_metabox_helper'))  {
// add section to admin pages
class KTT_metabox_helper {

    private $metabox_id;
    private $metabox_name;
    private $metabox_post_type;
    private $metabox_vars;
    private $metabox_callback;
    private $metabox_context;
    private $metabox_priority;

    /**
    * Constructor de classe
    */
    function __construct($args) {

        if (!is_admin()) return;

        $this->metabox_id               = $args['metabox_id'];
        $this->metabox_name             = $args['metabox_name'];
        $this->metabox_post_type        = $args['metabox_post_type'];
        $this->metabox_vars             = $args['metabox_vars'];
        $this->metabox_callback         = $args['metabox_callback'];
        $this->metabox_context          = $args['metabox_context'];
        $this->metabox_priority         = $args['metabox_priority'];

        add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
        add_action( 'save_post', array( $this, 'save_metabox' ) );


    }

    function KTT_new_metabox($args) {$self.__construct($args);}

    function register_metabox() {

            /**
            * we transform the post_type param in an array
            */
            $post_type = $this->metabox_post_type;
            if (!is_array($post_type)) $post_type = (array)$post_type;

            /**
            * Create the metabox for every post_type indicated
            */
            foreach ($post_type as $type) {
                add_meta_box(
                    $this->metabox_id,
                    $this->metabox_name,
                    array(&$this, 'metabox_render_content'),
                    $type,
                    $this->metabox_context,
                    $this->metabox_priority
                );
            }

    }


    function metabox_render_content( $post ) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field( $this->metabox_id . '_meta_box', $this->metabox_id . '_meta_box_nonce' );

        $function = $this->metabox_callback;
        $function($post);

    }


    function save_metabox($post_id) {



        // Check if our nonce is set.
        if ( ! isset( $_POST[$this->metabox_id . '_meta_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST[$this->metabox_id . '_meta_box_nonce'], $this->metabox_id . '_meta_box' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }

        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }



        if ($this->metabox_vars) {
        foreach( $this->metabox_vars as $variable) {

            // Make sure that it is set.
            //if ( ! isset( $_POST[$variable] ) ) continue;

            // Sanitize user input.
            //$my_data = sanitize_text_field( $_POST[$variable] );

            // Update the meta field in the database.
            //if (isset($_POST[$variable]) )
            @update_post_meta( $post_id, $variable, $_POST[$variable] );

        }
        }



    }


}
}
}
