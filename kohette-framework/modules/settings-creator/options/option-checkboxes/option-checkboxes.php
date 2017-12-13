<?php
/**
 * settings option
 *
 *
 */



/**
* option field
*/
function KTT_checkboxes_field($option, $current_value) {

  if ($option->option_type != 'checkboxes') return;

  print_r(get_option(ktt_var_name('template_displays')));

  ?>

                        <?php foreach ($option->option_type_vars as $key => $val) {

                            ?>
                            <label>

                                <input
                                type="checkbox"
                                <?php $option->link($key);?>
                                style="<?php echo @$option->option_style ;?>"
                                name="<?php echo $option->option_id ;?>[<?php echo $key;?>]"
                                <?php  checked( $current_value ); ?>
                                value="<?php echo $option->value($key);?>">


                                <?php echo $val;?>

                            </label><br>

                        <?php } ?>


                    <?php


                    if ($option->option_description) {?> <p class="description"><?php echo $option->option_description;?></p> <?php }




}
add_action('KTT_settings_option_field', 'KTT_checkboxes_field', 2, 2);
