<?php
/**
 * settings option
 *
 *
 */



/**
* option field
*/
function KTT_css_texts_field($option, $current_value) {

  
  if ($option->option_type != 'css_texts') return;

  ?>

                        <?php foreach ($option->option_type_vars as $key => $val) {

                            $style = '';
                            $label = '';

                            if (isset($val['style']) && $val['style']) $style = $val['style'];
                            if (isset($val['label']) && $val['label']) $label = $val['label'];

                            ?>
                            <input type="hidden" name="<?php echo $option->option_id;?>[<?php echo $key;?>][option_id]" value="<?php echo $option->option_id;?>">
                            <input type="hidden" name="<?php echo $option->option_id;?>[<?php echo $key;?>][selector]" value="<?php echo $val['selector'];?>">
                            <input type="hidden" name="<?php echo $option->option_id;?>[<?php echo $key;?>][property]" value="<?php echo $val['property'];?>">

                            <label>
                            <input 
                            type="text" 
                            class="regular-text"
                            style="<?php echo $style;?>"
                            id="<?php echo $option->option_id . $key;?>" 
                            name="<?php echo $option->option_id ;?>[<?php echo $key;?>][value]" 
                            value="<?php echo $current_value[$key]['value'];?>">
                            <?php echo @$label;?>
                            </label><br>

                        <?php } ?>


                    <?php


                    if ($option->option_description) {?> <p class="description"><?php echo $option->option_description;?></p> <?php }




}
add_action('KTT_settings_option_field', 'KTT_css_texts_field', 2, 2);



// !! register hook, used to save the font settings in the custom style variable
function KTT_css_texts_option_register_hook($option) {
    if ($option->option_type == 'css_texts') add_filter( 'pre_update_option_' . $option->option_id, 'KTT_save_css_option', 10, 2 );
}

add_action('KTT_settings_option_register', 'KTT_css_texts_option_register_hook', 2, 1);


