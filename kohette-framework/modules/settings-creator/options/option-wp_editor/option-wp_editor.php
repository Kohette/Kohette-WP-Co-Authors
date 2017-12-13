<?php
/**
 * settings option
 *
 *
 */



/**
* option field
*/
function KTT_wp_editor_field($option, $current_value) {

  if ($option->option_type != 'wp_editor') return;



                    ?>
                    <style>
                        #wp-link-wrap {
                            z-index: 99999999999999;
                        }
                        #wp-link-backdrop {
                            z-index: 99999999999999;
                        }
                        .mce-floatpanel, .mce-toolbar-grp.mce-inline-toolbar-grp {
                          z-index: 99999999999999 !important;
                        }
                    </style>
                    <input
                    type="hidden" <?php $option->link($option->option_id); ?>
                    value="<?php echo esc_textarea( $current_value ); ?>">
                    <?php
                    wp_editor(
                        $current_value,
                        $option->option_id,
                        $option->option_type_vars
                    );

                    /**
                    * Esto carga las librerias js para poder activar el editor
                    */
                    do_action('admin_footer');
                    do_action('admin_print_footer_scripts');



                    if ($option->option_description) {?> <p class="description"><?php echo $option->option_description;?></p> <?php }


}
add_action('KTT_settings_option_field', 'KTT_wp_editor_field', 2, 2);




function your_customize_backend_init(){
	wp_enqueue_script('KTT_wp_editor_setting_customizer', KTT_path_to_url(dirname(__FILE__)) . '/js/customizer.js');
}
add_action( 'customize_controls_enqueue_scripts', 'your_customize_backend_init' );
