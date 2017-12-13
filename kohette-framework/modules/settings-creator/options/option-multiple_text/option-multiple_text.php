<?php
/**
* multiple text inputs for settings option
*/



/**
* option field
*/
function KTT_multiple_text_field($option, $current_value) {

  if (!in_array($option->option_type, array('multiple_text'))) return;

  /**
  * If not array we transform it in that
  */
  if (!is_array($current_value)) $current_value = (array)$current_value;

  ?>
    <?php echo $option->option_label;?>
    <div id="<?php echo $option->option_id;?>-wrap">

          <?php
          /**
          * For every element in value que create a text input
          */
          foreach($current_value as $value) {
                  if (count($current_value) > 1 && !$value) continue;?>
                  <div>
                    <input
                    type="<?php echo $option->option_type ;?>"
                    step="any"
                    style="<?php echo $option->option_style;?>"
                    placeholder="<?php echo $option->option_placeholder;?>"
                    class="regular-text ltr"
                    id="<?php echo $option->option_id;?>"
                    name="<?php echo $option->option_id ;?>[]"
                    value="<?php echo  $value ;?>">
                  </div>
          <?php } ?>
    </div>

      <a
      onclick="jQuery('#<?php echo $option->option_id;?>-wrap div').last().clone().appendTo('#<?php echo $option->option_id;?>-wrap').find('input').val('')"
      style="margin-top:5px;display:inline-block;cursor:pointer">+ Add new</a>



                    <?php if ($option->option_description) {?> <p class="description"><?php echo $option->option_description;?></p> <?php } ?>


                    <?php


}
add_action('KTT_settings_option_field', 'KTT_multiple_text_field', 2, 2);

 ?>
