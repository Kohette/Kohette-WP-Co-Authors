<?php
/**
 * settings option
 *
 *
 */



// load font manager functions
include('font-manager/font-manager.php');



/**
* option field
*/
function KTT_font_field($option, $current_value) {



  if ($option->option_type != 'font') return;

  if(!isset($option->option_type_vars['selector'])) return;


                    $selector = $option->option_type_vars['selector'];
                    $google_fonts = KTT_get_available_google_fonts();
                    $default_fonts = KTT_get_available_default_fonts();
                    $sizes_em = range(0.0, 5, 0.1);
                    $sizes = range(10, 98);
                    $line_heights = range(0.5, 3, 0.1);
                    $size_units = array('px','em', 'pt','%', 'vh', 'vw');
                    $font_styles = array(
                                    'normal' => __('Normal','narratium'),
                                    'italic' => __('Italic', 'narratium'),
                                    'oblique' => __('Oblique', 'narratium')
                                    );

                    $current = $current_value;





                    if (!isset($current)) $current = $option->option_default;



                    $current['selector'] = $selector;
                    if (!isset($current)) $current = array();
                    if (!isset($current['load_all_weights'])) $current['load_all_weights'] = '';
                    if (!isset($current['font_family'])) $current['font_family'] = '';
                    if (!isset($current['font_weight'])) $current['font_weight'] = '';
                    if (!isset($current['font_size'])) $current['font_size'] = '';
                    if (!isset($current['font_size_unit'])) $current['font_size_unit'] = '';
                    if (!isset($current['font_style'])) $current['font_style'] = '';
                    if (!isset($current['line_height'])) $current['line_height'] = '';



                    /**
                    * Si estamos ante una opcion customize cargamos los valores que pueda tener
                    * guardados
                    */
                    if ($option->value('font_family')) $current['font_family'] = $option->value('font_family');
                    if ($option->value('font_size')) $current['font_size'] = $option->value('font_size');
                    if ($option->value('font_size_unit')) $current['font_size_unit'] = $option->value('font_size_unit');
                    if ($option->value('font_weight')) $current['font_weight'] = $option->value('font_weight');
                    if ($option->value('line_height')) $current['line_height'] = $option->value('line_height');
                    if ($option->value('font_style')) $current['font_style'] = $option->value('font_style');
                    if ($option->value('selector')) $current['selector'] = $option->value('selector');



                    ?>


                    <script>
                        var fonts_<?php echo $option->option_id;?> = {

                            <?php foreach ($default_fonts as $code => $font) {?>
                                '<?php echo $code;?>': ['<?php echo implode("','", $font['variants']);?>'],
                            <?php } ?>
                            <?php foreach ($google_fonts as $code => $font) {?>
                                '<?php echo $code;?>': ['<?php echo implode("','", $font['variants']);?>'],
                            <?php } ?>
                        }

                        function load_variants<?php echo $option->option_id;?>(code) {

                            if (!code) return;

                            // variants select
                            $variants_select = jQuery('#variants_<?php echo $option->option_id;?>');

                            variants = fonts_<?php echo $option->option_id;?>[code];

                            //remove variants select options
                            $variants_select.find('option').remove().end();

                            if (variants) {
                                for (variant in variants) {
                                    $variants_select.append(new Option( variants[variant].charAt(0).toUpperCase() + variants[variant].slice(1) , variants[variant] ));
                                }
                            } else {
                                $variants_select.append(new Option( 'Regular' , '' ));
                            }




                        }
                    </script>


                    <input
                    type="hidden"
                    <?php $option->link('selector');?>
                    name="<?php echo $option->option_id;?>[selector]"
                    value="<?php echo $current['selector'];?>">

                    <input
                    type="hidden"
                    <?php $option->link('load_all_weights');?>
                    name="<?php echo $option->option_id;?>[load_all_weights]"
                    value="<?php echo $current['load_all_weights'];?>">






                    <?php if (isset($option->option_type_vars['font_size']) && $option->option_type_vars['font_size']) {?>

                        <span style="display:inline-block;padding-top:0px;">
                          <input
                          <?php $option->link('font_size');?>
                          name="<?php echo $option->option_id;?>[css][font_size]"
                          style="text-align:center;padding-bottom:5px;line-height:35px;max-width:50px;border-radius:5px;height:30px;"
                          type="number"
                          value="<?php  echo $current['font_size'];?>"
                          step="any">
                        </span>

                        <select
                        style="height:30px;width:50px"
                        <?php $option->link('font_size_unit');?>
                        name="<?php echo $option->option_id;?>[css][font_size_unit]"
                        >
                            <option value=""><?php _e('Unit','narratium');?></option>
                            <?php foreach ($size_units as $unit) {?>
                            <option <?php if ($current['font_size_unit'] == $unit) {?> selected <?php } ?> value="<?php echo $unit;?>"><?php echo $unit;?></option>
                            <?php } ?>
                        </select>

                        ·

                    <?php }  else {
                      ?>
                      <input type="hidden" <?php $option->link('font_size');?> value="">
                      <input type="hidden" <?php $option->link('font_size_unit');?> value=" ">
                    <?php } ?>






                    <?php if (isset($option->option_type_vars['font_family']) && $option->option_type_vars['font_family']) {?>

                    <select
                    style="height:30px;"
                    <?php $option->link('font_family');?>
                    name="<?php echo $option->option_id;?>[font_family]"
                    onchange="load_variants<?php echo $option->option_id;?>(this.value)"
                    >
                        <option value=""><?php _e('Default','narratium');?></option>

                        <optgroup label="<?php _e('Basic fonts','narratium');?>">
                        <?php foreach($default_fonts as $code => $font) {?>
                        <option <?php echo selected($current['font_family'], $code, false) ?> value="<?php echo $code;?>"><?php echo $font['name'];?></option>
                        <?php } ?>
                        </optgroup>

                        <optgroup label="<?php _e('Google fonts','narratium');?>">
                        <?php foreach($google_fonts as $code => $font) {?>
                        <option <?php echo selected($current['font_family'], $code, false) ?> value="<?php echo $code;?>"><?php echo $font['name'];?></option>
                        <?php } ?>
                        </optgroup>


                    </select>

                    <select
                    id="variants_<?php echo $option->option_id;?>"
                    style="height:30px;"
                    <?php $option->link('font_weight');?>
                    name="<?php echo $option->option_id;?>[font_weight]"
                    >
                        <option value="regular"><?php _e('Regular', 'narratium');?></option>
                    </select>

                    <script>

                    load_variants<?php echo $option->option_id;?>('<?php echo $current['font_family'];?>');
                    jQuery('#variants_<?php echo $option->option_id;?>').val('<?php echo $current['font_weight'];?>');

                    </script>

                    <?php }?>









                    <?php if (isset($option->option_type_vars['font_style']) && $option->option_type_vars['font_style']) {?>

                    <select
                    style="height:30px;"
                    name="<?php echo $option->option_id;?>[font_style]"
                    >
                        <option value=""><?php _e('Font style','narratium');?></option>
                        <?php foreach ($font_styles as $code => $name) {?>
                        <option <?php if ($current['font_style'] == $code) {?> selected <?php } ?> value="<?php echo $code;?>"><?php echo $name;?></option>
                        <?php } ?>
                    </select>

                    <?php } ?>





                    <?php if (isset($option->option_type_vars['line_height']) && $option->option_type_vars['line_height']) {?>

                    <select
                    style="height:30px;width:150px"
                    <?php $option->link('line_height');?>
                    name="<?php echo $option->option_id;?>[css][line_height]"
                    >
                        <option value=""><?php _e('Line height','narratium');?></option>
                        <?php foreach ($line_heights as $line) {?>
                        <option <?php echo selected($current['line_height'], (string)$line, false) ?> value="<?php echo $line;?>"><?php echo $line;?>em</option>
                        <?php } ?>
                    </select>

                    <?php } ?>





                    <?php if ($option->option_description) {?> <p class="description"><?php echo $option->option_description;?></p> <?php } ?>


                    <?php


}
add_action('KTT_settings_option_field', 'KTT_font_field', 2, 2);
