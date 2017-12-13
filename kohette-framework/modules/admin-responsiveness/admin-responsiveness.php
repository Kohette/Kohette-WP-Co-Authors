<?php
/*
Module Name: Admin Responsiveness module.
Module ID: admin-responsiveness
Module Description: This load the libraries needed to make responsive the admin tables, metaboxes, etc.
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/

function load_admin_responsiveness_style() {
        wp_register_style( 'admin-responsiveness',  ktt_path_to_url(dirname(__FILE__)) . '/stylesheets/responsive.css', false, '1.0.0' );
        wp_enqueue_style( 'admin-responsiveness' );
}
add_action( 'admin_enqueue_scripts', 'load_admin_responsiveness_style' );
