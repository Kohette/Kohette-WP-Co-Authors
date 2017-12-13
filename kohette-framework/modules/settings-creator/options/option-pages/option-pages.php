<?php
/**
 * settings option
 *
 *
 */



/**
* option pages
*/
function KTT_pages_field($option, $current_value) {

  if ($option->option_type != 'pages') return;

  /**
  * Obtenemos los argumentos por defecto
  */
  $defaults = array(
    'depth'                 => 0,
    'child_of'              => 0,
    'selected'              => $current_value,
    'echo'                  => 1,
    'name'                  => $option->option_id,
    'id'                    => $option->option_id, // string
    'class'                 => null, // string
    'show_option_none'      => null, // string
    'show_option_no_change' => null, // string
    'option_none_value'     => null, // string
  );

  /**
  * Formamos el array de argumentos
  */
  $args = wp_parse_args( $option->option_type_vars, $defaults );

  ?>

                    <?php wp_dropdown_pages( $args ); ?>

                    <?php if ($option->option_description) {?> <p class="description"><?php echo $option->option_description;?></p> <?php } ?>


  <?php


}
add_action('KTT_settings_option_field', 'KTT_pages_field', 2, 2);
