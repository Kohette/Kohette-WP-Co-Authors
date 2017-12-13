<?php
/*
Module Name: Theme Activation Module.
Module ID: theme-activation-options
Module Description: Theme activation options.
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/



function set_default_options() {
  global $KTT_custom_theme_settings;

  if ($KTT_custom_theme_settings) {
  foreach ($KTT_custom_theme_settings as $option_key => $option) {

    if (!isset($option['option_default'])) continue;
    if (!$option['option_default']) continue;


    $option_is_saved = get_option($option_key, NULL);
    if ($option_is_saved === NULL) {
        update_option($option_key, $option['option_default']);
    }


  }
  }

  /*
  echo '<pre>';
  print_r($KTT_custom_theme_settings);
  echo '</pre>';
  */

}
