<?php
/**
 * settings option
 *
 *
 */



/**
* option field
*/
function KTT_image_field($option, $current_value) {

  if ($option->option_type != 'image') return;

                    $image = get_post($current_value);
                    if (!$image) {
                        $current_value = '';
                    } else {
                        $image_src = wp_get_attachment_url( $current_value );

                    }


                    wp_enqueue_media();
                    wp_enqueue_script('media-upload');
                    ?>

                    <div id="upload-field-<?php echo $option->option_id;?>">

                        <div  style="margin-bottom:20px;<?php if (!$current_value) {?>display:none;<?php } ?>" class="show-on-image">

                            <img id="uploaded-image-<?php echo $option->option_id;?>" style="display:block;max-height:160px;max-width:500px;" src="<?php echo $image_src;?>">

                        </div>

                       <span
                       data-uploader_title="<?php echo $option->option_name;?>"
                       id="upload-button-<?php echo $option->option_id;?>"
                       class="button button-primary">
                            <?php _e('Select image', 'narratium');?>
                       </span>


                       <span
                       <?php if (!$current_value) {?>style="display:none;"<?php } ?>
                       id="remove-button-<?php echo $option->option_id;?>"
                       onclick="jQuery('#upload-field-<?php echo $option->option_id;?> .show-on-image').hide();jQuery('#upload-image-<?php echo $option->option_id;?>').val('');"
                       class="show-on-image button button-secondary">
                            <?php _e('Remove','narratium');?>
                       </span>



                        <input
                        id="upload-image-<?php echo $option->option_id;?>"
                        type="hidden"
                        id="<?php echo $option->option_id ;?>"
                        name="<?php echo $option->option_id ;?>"
                        value="<?php echo $current_value;?>">


                        <?php echo $option->option_label;?>

                    </div>

                    <script>

                    // Uploading files
                    var file_frame;

                      jQuery('#upload-button-<?php echo $option->option_id;?>').live('click', function( event ){

                        event.preventDefault();

                        button = jQuery(this);

                        // If the media frame already exists, reopen it.


                        // Create the media frame.
                        file_frame = wp.media.frames.file_frame = wp.media({
                          title: jQuery( this ).data( 'uploader_title' ),
                          button: {
                            text: jQuery( this ).data( 'uploader_button_text' ),
                          },
                          multiple: false  // Set to true to allow multiple files to be selected
                        });

                        // When an image is selected, run a callback.
                        file_frame.on( 'select', function() {
                          // We set multiple to false so only get one image from the uploader
                          attachment = file_frame.state().get('selection').first().toJSON();
                          jQuery('#upload-image-<?php echo $option->option_id;?>').val(attachment.id);

                          jQuery('#uploaded-image-<?php echo $option->option_id;?>').attr('src', attachment.url);

                          jQuery('#upload-field-<?php echo $option->option_id;?> .show-on-image').show();

                        });

                        // Finally, open the modal
                        file_frame.open();
                      });

                    </script>



                    <?php if ($option->option_description) {?> <p class="description"><?php echo $option->option_description;?></p> <?php } ?>


                    <?php


}
add_action('KTT_settings_option_field', 'KTT_image_field', 2, 2);
