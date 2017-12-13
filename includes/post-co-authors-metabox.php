<?php

/**
* Este script permite añadir soporte para multiples authores
*/

/**
* Creation of the metabox_id with the hyper-amazing KTT Framework
*/
$args = array();
$args['metabox_id'] 					= 	'post_coauthors';
$args['metabox_name']					= 	__("Post Co-Authors", 'ktt-coauthors');
$args['metabox_post_type'] 		= 	'post';
$args['metabox_vars'] 				= 	array(
                                      ktt_var_name('post_coauthors')
                                  );
$args['metabox_callback']			= 	'KTT_post_coauthors_meta_box';
$args['metabox_context']			= 	'normal';
$args['metabox_priority']			= 	'high';
$metabox = new KTT_new_metabox($args);



/**
* Metabox render
*/
function KTT_post_coauthors_meta_box($post) {

    /**
  	* Invocamos la libreria selectd que nos ayuda a crear multiselects
  	*/
  	wp_enqueue_style('style-select2', KTT_path_to_url(KOHETTE_FW_RESOURCES . '/select2/select2.css'));
    wp_enqueue_script( 'select2', KTT_path_to_url(KOHETTE_FW_RESOURCES . '/select2/select2.js') );

    /**
    * Obtenemos el array de authores
    */
    $post_coauthors = KTT_get_post_coauthors($post);
    $post_coauthors = wp_list_pluck($post_coauthors, 'ID');

    /**
    * Obtenemos la lista completa de todos los users del site
    */
    $users = get_users(array('exclude' => $post->post_author));

    ?>
      <p>
        <?php _e('Here you can add users as co-authors of the post.', 'ktt-coauthors');?>
      </p>

      <select
      style="width:100%"
      name="<?php echo ktt_var_name('post_coauthors');?>[]"
      multiple="multiple">
        <?php foreach ($users as $user) {?>
          <option <?php if (in_array($user->ID, $post_coauthors)) {?>selected<?php } ?> value="<?php echo $user->ID;?>"><?php echo $user->display_name ;?> (<?php echo $user->user_login;?>) </option>
        <?php } ?>
      </select>

      <script>jQuery(document).ready(function() { jQuery("select[multiple=multiple]").select2();});</script>
    <?php

}


 ?>
