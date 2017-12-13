<?php
/*
Module Name: Taxonomy Controls Module.
Module ID: taxonomy-controls
Module Description: Classes and functions to control and extend the Wordpress taxonomy feature
Module Required:
Module Version: 1.0.0
Module Priority: Normal
*/




// Class to create custom meta fields table for taxonomies
class Taxonomy {

        /**
         * Register taxonomy metadata
         *
         * @fixme The code to create the table should only run once
         * @todo the switch_blog action has always to be hooked into
         *
         * @return none
         */
        function add_taxonomy_meta() {
                //$this->create_taxonomy_meta_table();
                $this->update_wpdb();
                add_action( 'switch_blog', array( &$this, 'update_wpdb' ) );
        }

        /**
         * WordPress doesn't seem to support metadata on custom taxonomies out
         * of the box, we need to update $wpdb->taxonomy to the correct table
         * ourself.
         *
         * @return none
         */
        function update_wpdb() {
                global $wpdb;
                $wpdb->taxonomymeta = $wpdb->prefix . 'taxonomymeta';
        }

        /**
         * Create a table for taxonomy meta
         *
         * @return none
         */
        function create_taxonomy_meta_table() {

                if ( $this->taxonomy_meta_table_exists() ) {
                        return;
                }
                global $wpdb;
                $query = "CREATE TABLE `{$wpdb->prefix}taxonomymeta` (
                           `meta_id` bigint(20) unsigned not null auto_increment,
                           `taxonomy_id` bigint(20) unsigned not null default '0',
                           `meta_key` varchar(255),
                           `meta_value` longtext,
                           PRIMARY KEY (`meta_id`),
                           KEY `taxonomy_id` (`taxonomy_id`),
                           KEY `meta_key` (`meta_key`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;";
                $r = $wpdb->query(  $query  );
        }

        /**
         * Check if the taxonomy meta table exists
         *
         * @return bool table exists
         */
        function taxonomy_meta_table_exists() {
                global $wpdb;
                $query = "SHOW TABLES LIKE '{$wpdb->prefix}taxonomymeta';";
                $indexes = $wpdb->get_var(  $query  );
                if ( $indexes )
                        return true;
                return false;
        }

}







// create the taxonomy meta table on theme activation
function register_taxonomy_meta_table() {
  $a = new Taxonomy;
  $a->create_taxonomy_meta_table();
}

add_action("after_switch_theme", "register_taxonomy_meta_table", 4 ,  2);





function update_taxonomy_meta($term_id, $meta_key, $meta_value, $prev_value = '') {

  // now we use the new term_meta functions
  return update_term_meta($term_id, $meta_key, $meta_value, $prev_value);
	//@Taxonomy::update_wpdb();
	//return update_metadata('taxonomy', $term_id, $meta_key, $meta_value, $prev_value);
}

function get_taxonomy_meta($term_id, $key = '', $single = false) {
  @Taxonomy::update_wpdb();
  $result = get_term_meta($term_id, $key, $single);
  if (!$result) {

  	 $result = get_metadata('taxonomy', $term_id, $key, $single);
  }
  return $result;
}

function add_taxonomy_meta($term_id, $meta_key, $meta_value, $unique = false) {
  return add_term_meta($term_id, $meta_key, $meta_value, $unique);
	//@Taxonomy::update_wpdb();
	//return add_metadata('taxonomy', $tax_id, $meta_key, $meta_value, $unique);
}












// add custom meta tag to taxonomy
class KTT_new_taxonomy_meta {

    private $taxmeta_taxonomy;
    private $taxmeta_id;
    private $taxmeta_name;
    private $taxmeta_label;
    private $taxmeta_description;
    private $taxmeta_type;
    private $taxmeta_type_vars;
    private $taxmeta_style;
    private $taxmeta_vars_to_save; // for custom forms
    private $taxmeta_form_html; // for custom forms
    private $taxmeta_callback;


    /**
    * Constructor
    */
    function __construct($args) {
        if (!is_admin()) return;

        if (isset($args['taxmeta_taxonomy']))       $this->taxmeta_taxonomy 		  = $args['taxmeta_taxonomy'];
        if (isset($args['taxmeta_id']))             $this->taxmeta_id 				    = $args['taxmeta_id'];
        if (isset($args['taxmeta_name']))           $this->taxmeta_name 			    = $args['taxmeta_name'];
        if (isset($args['taxmeta_label']))          $this->taxmeta_label 			    = $args['taxmeta_label'];
        if (isset($args['taxmeta_description']))    $this->taxmeta_description 		= $args['taxmeta_description'];
        if (isset($args['taxmeta_type']))           $this->taxmeta_type 			    = $args['taxmeta_type'];
        if (isset($args['taxmeta_type_vars']))      $this->taxmeta_type_vars 		  = $args['taxmeta_type_vars'];
        if (isset($args['taxmeta_style']))          $this->taxmeta_style          = $args['taxmeta_style'];
        if (isset($args['taxmeta_callback']))       $this->taxmeta_callback       = $args['taxmeta_callback'];

        if (isset($args['taxmeta_vars_to_save']))   $this->taxmeta_vars_to_save 	= $args['taxmeta_vars_to_save'];


        $this->taxmeta_form_html 		= array(&$this, 'fields_html');
        if (isset($args['taxmeta_form_html']))      $this->taxmeta_form_html    = $args['taxmeta_form_html'];

        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );
        add_filter( 'admin_init' , array( &$this , 'register_save' ) );

    }

    public function KTT_new_taxonomy_meta($args) {$self.__construct($args);}


    function register_fields() {

        add_action( $this->taxmeta_taxonomy . '_add_form_fields', $this->taxmeta_form_html , 10, 2 );
        add_action( $this->taxmeta_taxonomy . '_edit_form_fields', $this->taxmeta_form_html , 10, 2 );

    }


    function register_save() {

    	/** Save Custom Field Of Category Form */
			add_action( 'created_' . $this->taxmeta_taxonomy,  array( &$this , 'taxmeta_save'), 10, 2 );
			add_action( 'edited_' . $this->taxmeta_taxonomy,  array( &$this , 'taxmeta_save'), 10, 2 );

    }



    function taxmeta_save($term_id, $taxonomy_id = '' ) {

    	@update_taxonomy_meta($term_id, $this->taxmeta_id, $_POST[$this->taxmeta_id]);

    }



    function fields_html($term, $taxonomy = '' ) {
            $value = '';
            if ($term && is_object($term)) $value = get_term_meta( $term->term_id, $this->taxmeta_id, true );



            if (isset($term->term_id)) {?>

                <tr class="form-field">
		                <th scope="row" valign="top">
		        	         <label for="<?php echo $this->taxmeta_id;?>"><?php echo $this->taxmeta_name; ?></label>
                    </th>
		              <td>

    		    <?php } else { ?>

                <div class="form-field">
                  <label for="<?php echo $this->taxmeta_id ;?>"><?php echo $this->taxmeta_name; ?></label>

            <?php }







            switch ($this->taxmeta_type) {


                case 'checkbox':
                    ?>

                    <input
                    type="checkbox"
                    style="<?php echo $this->taxmeta_style ;?>;max-width:16px;"
                    id="<?php echo $this->taxmeta_id ;?>"
                    name="<?php echo $this->taxmeta_id ;?>"
                    <?php if ($value) { ?> checked <?php } ?>
                    value="1">

                    <?php echo $this->taxmeta_label;?>

                    <?php
                    break;


                case 'wp_editor':

                    wp_editor(
                        $value,
                        $this->taxmeta_id,
                        $this->taxmeta_type_vars
                    );


                    break;

                case 'select':

                    ?>

                    <select
                    style="<?php echo $this->taxmeta_style ;?>;"
                    id="<?php echo $this->taxmeta_id ;?>"
                    name="<?php echo $this->taxmeta_id ;?>"
                    >
                        <?php foreach ($this->taxmeta_type_vars as $key => $name) {

                        $elem_value = $key;
                        $elem_name  = $name;

                        if (is_array($elem_name)) {
                            if(isset($name['value'])) $elem_value = $name['value'];
                            if(isset($name['name'])) $elem_name = $name['name'];
                        }

                        ?>
                        <option <?php if ($value == $elem_value) {?>selected<?php } ?> value="<?php echo $elem_value;?>"><?php echo $elem_name;?></option>
                        <?php } ?>

                    </select>

                    <?php





                    break;


                case 'select2':

                    wp_enqueue_style('style-select2', KTT_path_to_url(KOHETTE_FW_RESOURCES . '/select2/select2.css'));
                    wp_enqueue_script( 'select2', KTT_path_to_url(KOHETTE_FW_RESOURCES . '/select2/select2.js') );
                    ?>

                    <select
                    style="min-width:300px;<?php echo $this->taxmeta_style ;?>;"
                    id="<?php echo $this->taxmeta_id ;?>"
                    name="<?php echo $this->taxmeta_id ;?>[]"
                    multiple="multiple"
                    >
                        <?php foreach($this->taxmeta_type_vars as $select_option) {?>
                        <option <?php if ($value && in_array( $select_option['value'], $value)) { ;?> selected <?php } ?> value="<?php echo $select_option['value'];?>"><?php echo $select_option['name'];?></option>
                        <?php } ?>
                    </select>


                    <script>
                          jQuery(document).ready(function() {

                            jQuery("#<?php echo $this->taxmeta_id ;?>").select2();

                          });
                    </script>
                    <?php
                    break;


                case 'image':

                    $image = get_post($value);
                    if (!$image) {
                        $image_src = '';
                    } else {
                        $image_src = wp_get_attachment_url( $value );

                    }



                    wp_enqueue_media();
                    wp_enqueue_script('media-upload');
                	?>

                    <div id="upload-field-<?php echo $this->taxmeta_id;?>">

                        <div  style="margin-bottom:20px;<?php if (!$value) {?>display:none;<?php } ?>" class="show-on-image">

                            <img id="uploaded-image-<?php echo $this->taxmeta_id;?>" style="display:block;max-height:160px;max-width:500px;" src="<?php echo $image_src;?>">

                        </div>

                       <span
                       data-uploader_title="<?php echo $this->taxmeta_name;?>"
                       id="upload-button-<?php echo $this->taxmeta_id;?>"
                       class="button button-primary">
                            <?php _e('Select image', 'narratium');?>
                       </span>


                	   <span
                       <?php if (!$value) {?>style="display:none;"<?php } ?>
                       id="remove-button-<?php echo $this->taxmeta_id;?>"
                       onclick="jQuery('#upload-field-<?php echo $this->taxmeta_id;?> .show-on-image').hide();jQuery('#upload-image-<?php echo $this->taxmeta_id;?>').val('');"
                       class="show-on-image button button-secondary">
                            <?php _e('Remove','narratium');?>
                       </span>



                        <input
                        id="upload-image-<?php echo $this->taxmeta_id;?>"
                        type="hidden"
                        id="<?php echo $this->taxmeta_id ;?>"
                        name="<?php echo $this->taxmeta_id ;?>"
                        value="<?php echo $value;?>">


                        <?php echo $this->taxmeta_label;?>

                    </div>

                	<script>

                    // Uploading files
                    var file_frame;

                      jQuery('#upload-button-<?php echo $this->taxmeta_id;?>').live('click', function( event ){

                        event.preventDefault();

                        button = jQuery(this);

                        // If the media frame already exists, reopen it.
                        if ( file_frame ) {
                          // Open frame
                          file_frame.open();
                          return;
                        }

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
                          jQuery('#upload-image-<?php echo $this->taxmeta_id;?>').val(attachment.id);

                          jQuery('#uploaded-image-<?php echo $this->taxmeta_id;?>').attr('src', attachment.url);

                          jQuery('#upload-field-<?php echo $this->taxmeta_id;?> .show-on-image').show();

                        });

                        // Finally, open the modal
                        file_frame.open();
                      });

					         </script>




                	<?php

                	break;



                default:

                    if(isset($this->taxmeta_callback) && $this->taxmeta_callback) {
                        $function = $this->taxmeta_callback;
                        $function($term);
                        break;
                    }



                    ?>

                    <input
                    type="<?php echo $this->taxmeta_type ;?>"
                    style="<?php echo $this->taxmeta_style;?>"
                    class="regular-text ltr"
                    id="<?php echo $this->taxmeta_id;?>"
                    name="<?php echo $this->taxmeta_id ;?>"
                    step="any"
                    value="<?php echo  $value ;?>">

                    <?php echo $this->taxmeta_label;?>



                    <?php
                    break;

                }


                ?>


                 <?php if ($this->taxmeta_description) {?> <p class="description"><?php echo $this->taxmeta_description;?></p> <?php } ?>






        <?php if (isset($term->term_id)) {?>
		            </td>
    		    </tr>

        <?php } else {?>

            </div>

        <?php } ?>






    		<?php

    }

}
