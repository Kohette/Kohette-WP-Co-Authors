<?php
/**
 * settings option
 *
 *
 */



/**
* option field
*/
function KTT_select_field($option, $current_value) {

  if ($option->option_type != 'select') return;

  ?>

                    <select
                    style="<?php echo $option->option_style ;?>"
                    id="<?php echo $option->option_id ;?>"
                    name="<?php echo $option->option_id ;?>"
                    <?php $option->link($option->option_id);?>
                    >

                    <?php foreach ($option->option_type_vars as $key => $name) {

                        $elem_value = $key;
                        $elem_name  = $name;

                        if (is_array($elem_name)) {
                            if(isset($name['value'])) $elem_value = $name['value'];
                            if(isset($name['name'])) $elem_name = $name['name'];
                        }

                        ?>
                        <option <?php if ($current_value == $elem_value) {?>selected<?php } ?> value="<?php echo $elem_value;?>"><?php echo $elem_name;?></option>
                    <?php } ?>

                    </select>

                    <?php if ($option->option_description) {?> <p class="description"><?php echo $option->option_description;?></p> <?php } ?>


                    <?php


}
add_action('KTT_settings_option_field', 'KTT_select_field', 2, 2);
