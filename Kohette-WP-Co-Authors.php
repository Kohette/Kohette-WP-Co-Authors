<?php
/*
Plugin Name: Kohette Co-Authors
Description: Adds support for co-authors in every WP post.
Version: 1.0.0
Author: Rafael Martín
Author URI: http://kohette.com
Text Domain: ktt-coauthors
Domain Path: /languages
*/


/**
* Load kohette framework toolkit
*/
require_once('kohette-framework/kohette-framework.php');

/**
* create a kohette framework object
*/
$kohette = new kohette_framework();

/**
* Configuration of the framework
*/
$kohette->add_to_config(array(
    // moduls to load in the framework
    'modules' => array('basic-functions', 'metabox-creator'),

    // Constant global variables
    'constants' => array(
        'textdomain' => 'ktt-coauthors',
        'prefix' => 'ktt_'
    ),

    // Los archivos necesarios del plugin
    'plugins' => array(
        'metabox' => array('source' => dirname(__FILE__) . "/includes/post-co-authors-metabox.php",
        'functions' => array('source' => dirname(__FILE__) . "/includes/post-co-authors-functions.php",
        'table' => array('source' => dirname(__FILE__) . "/includes/post-co-authors-columns.php",
        'queryargs' => array('source' => dirname(__FILE__) . "/includes/post-co-authors-custom-query-args-coauthors.php",
    ),

));

/**
* Cargamos los modules
*/
$kohette->load_framework_modules();

/**
* Cargamos los plugins
*/
$kohette->load_framework_plugins();





?>
