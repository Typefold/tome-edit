<?php

/**
 * Tome external_media post type
 * TOOD: extract all functions related to tinymce in different class
 *
 * @package    tome-edit
 * @subpackage tome-edit/admin/lib/external-media
 */
class Tome_External_Media {

	protected $loader;

	function __construct( $loader ) {
		$this->loader = $loader;
		$this->define_admin_hooks();
	}

	private function define_admin_hooks() {
		$this->loader->add_action('init', $this, 'add_post_type');
		$this->loader->add_action('admin_head', $this, 'embedded_media_modal');
		$this->loader->add_action('admin_menu', $this, 'external_media_page', 99);
		$this->loader->add_action('save_post', $this, 'generate_dynamic_thumbnail', 20, 1);
		$this->loader->add_action('wp_ajax_create_media', $this, 'create_media');
		$this->loader->add_action('wp_ajax_delete_external_media', $this, 'delete_external_media');
		$this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');
		$this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_public_scripts');
		$this->loader->add_action('print_media_templates', $this, 'print_media_templates');

		add_shortcode('external_media', array($this, 'external_media_shortcode'));
	}

	function external_media_shortcode( $atts ) {

		$media_type = get_post_meta( $atts['id'], 'media_type', true );
		$external_source = get_post_meta( $atts['id'], 'external_source', true );
		$media = get_post( $atts['id'] );
		$size = $atts['size'];

		if ( $media_type != 'embed' ) {
			$external_source = wp_oembed_get( $external_source );
		}

		$output = "<div class='embed-wrapper $size'>";
		$output .= $external_source;
		$output .= "</div>";

		return $output;
	}


	/** Add external_media post type */
	function add_post_type() {

		$labels = array(
			'name'                => __( 'External Media', 'text-domain' ),
			'singular_name'       => __( 'External Media', 'text-domain' ),
			'add_new'             => _x( 'Add New External Media', 'text-domain', 'text-domain' ),
			'add_new_item'        => __( 'Add New External Media', 'text-domain' ),
			'edit_item'           => __( 'Edit External Media', 'text-domain' ),
			'new_item'            => __( 'New External Media', 'text-domain' ),
			'view_item'           => __( 'View External Media', 'text-domain' ),
			'search_items'        => __( 'Search External Media', 'text-domain' ),
			'not_found'           => __( 'No External Medias found', 'text-domain' ),
			'not_found_in_trash'  => __( 'No External Medias found in Trash', 'text-domain' ),
			'parent_item_colon'   => __( 'Parent External Media:', 'text-domain' ),
			'menu_name'           => __( 'External Media', 'text-domain' ),
		);

		$args = array(
			'labels'                   => $labels,
			'hierarchical'        => false,
			'description'         => 'external video, audio, or any other embed',
			'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => null,
			'menu_icon'           => null,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'page',
			'supports'            => array()
		);

		register_post_type( 'tome_external_media', $args );
		
	}


	/** get all exter_media posts */
	public static function get_all_media() {

		$media = get_posts(array(
			'post_type' => 'tome_external_media',
			'posts_per_page' => -1
		));

		return $media;

	}



	public function create_media() {

		$form_values = $_POST['form_values'];

		$post_params = array(
			'post_title' => $form_values['media_title'],
			'post_content' => '',
			'post_type' => 'tome_external_media',
			'post_status' => 'publish',
			'meta_input' => array(
				'external_source' => $form_values['external_source'],
				'media_type' => $form_values['media_type']
			)
		);

		( isset( $form_values['media_id'] ) ) ? $post_params['ID'] = $form_values['media_id'] : "";

		// $post_id = wp_insert_post($post_params);
		$post_id = wp_insert_post( $post_params, true );


		if ( is_wp_error( $post_id ) ) {
			return false;
			die();
		}



		$this->single_media_item( $post_id );


		die();
	}



	public function delete_external_media() {
		$media_id =  $_POST['id'];

		wp_delete_post( $media_id );

		echo $media_id;

		die();
	}



	/**
	 * Add submenu page for external_media
	 */
	function external_media_page() {
		add_submenu_page( 'upload.php', 'External Media Manager', 'External Media', 'edit_posts', 'external-media-list', array($this, 'print_admin_page') );
	}



	/**
	 * add 'dynamic_thumbnail' post meta when saving tome_external_media of type 'video'
	 */
	function generate_dynamic_thumbnail( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || get_post_type( $post_id ) != 'tome_external_media' || wp_is_post_revision( $post_id ) )
			return;


		$media_type = get_post_meta( $post_id, 'media_type', true );

		if ( $media_type != 'video' )
			return;


		$external_source = get_post_meta( $post_id, 'external_source', true );
		$dynamic_thumbnail = Tome_External_Media_Helpers::get_thumbnails( $external_source );

		update_post_meta( $post_id, 'dynamic_thumbnail', $dynamic_thumbnail );
	}



	public function print_media_form() {
		?>

		<div class="media-form-wrapper">
			
			<form method="post" id="external-media-form">
				<div class="input-wrapp">
					<select id="media_type" name="media_type">
						<option value="">media type</option>
						<option value="video">video (youtube, vimeo)</option>
						<option value="audio">audio (soundcloud)</option>
						<option value="embed">embed code</option>
					</select>
				</div>

				<div class="input-wrapp">
					<input type="text" id="media_title" name="media_title" placeholder="title" value="">
				</div>

				<div class="input-wrapp">
					<textarea type="text" id="external_source" name="external_source" placeholder="external source" rows="5"></textarea>
				</div>

				<div class="input-wrapp">
					<input type="submit" name="submit" class="button button-primary" value="Submit">
				</div>

			</form>

		</div>
		<?php
	}



	function get_preview_thumbnail( $media_id ) {
		$media_type = get_post_meta( $media_id, 'media_type', true );

		if ( $media_type == 'video' ) {

			$thumbnail = get_post_meta( $media_id, 'dynamic_thumbnail', true );
			echo "<img src='$thumbnail'/>";
		}


		return;
	}



	/**
	 * print media item
	 * @param  WP_Object|int $media - $media object or ID of the external_media post
	 * @return void        - html for media item
	 */
	function single_media_item( $media ) {

		if ( is_int( $media ) )
			$media = get_post( $media );

		$custom = get_post_custom( $media->ID );
		$external_source = $custom['external_source'][0];
		$media_type = $custom['media_type'][0];
		$title = $media->post_title;
		$item_class = "";


		switch ( $media_type ) {
			case 'embed':
				$item_class = "dashicons-before dashicons-editor-code";
			break;
			case 'audio':
				$item_class = "dashicons-before dashicons-format-audio";
			break;
			default:
				$item_class = "";
			break;
		}


		?>

		<div class="media-item item-<?php echo $media_type . ' ' . $item_class; ?>" data-item="<?php echo $media->ID; ?>" data-type="<?php echo $media_type; ?>">
			<div class="item-info">
				<?php $this->get_preview_thumbnail( $media->ID ); ?>
				<span class="title"><?php echo $title; ?></span>
				<textarea class="external-source"><?php echo $external_source; ?></textarea>
			</div>

			<div class="media-actions">
				<a href="javascript:;" class="edit-external-media">edit</a>
				<a href="javascript:;" class="delete-external-media">delete</a>
			</div>
		</div>
		
		<?php
	}



	function print_admin_page() {
		?>
			<div class="external-media-admin-page wrap" id="external-media-page">

				<div class="wrap acf-settings-wrap">
					<h1>External Media</h1>
					<a href="javascript:;" class="button button-primary add-media">Add media</a>
				</div>

				<div class="notifications-wrapper">
					<div class="notification success"><?php _e('External media was successfully created.', 'tome'); ?></div>
					<div class="notification alert"><?php _e('Something went wrong the media was not created.', 'tome'); ?></div>
				</div>



				<?php $this->print_media_form(); ?>


				<div class="search-wrapper">
					<input type="text" class="search search-media" name="search_bibliographies" id="biblio-search" placeholder="<?php _e('search', 'tome'); ?>" value="">
				</div>

				<div class="media-items list">
					
					<?php
						$all_media = $this->get_all_media();

						foreach ($all_media as $media) {

							$this->single_media_item( $media );

						}
					?>

				</div>


			</div>
		<?php
	}



	function enqueue_scripts() {
		wp_register_script( 'external_media', plugin_dir_url( __FILE__ ) . 'js/tome-external-media.js', array('shortcode', 'jquery', 'wp-util'), false, true );
		wp_register_style( 'admin-styles', plugin_dir_url( __FILE__ ) . 'css/external-media-admin.css' );

		wp_enqueue_media();
		wp_enqueue_script( 'external_media' );
		wp_enqueue_style( 'admin-styles' );
	}



	function enqueue_public_scripts() {
		wp_register_script( 'external-media-public', plugin_dir_url( __FILE__ ) . 'js/external-media-public.js', array('jquery'), false, true );
		wp_register_style( 'public-styles', plugin_dir_url( __FILE__ ) . 'css/external-media-public.css' );

		wp_enqueue_script( 'external-media-public' );
		wp_enqueue_style( 'public-styles' );
	}



	public function print_media_templates() {
		if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
			return;

		include_once plugin_dir_path( __FILE__ ) . 'mce-templates/tmpl-external-media.html';
	}



	function embedded_media_modal() {
		global $pagenow;
		if( !in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) || get_current_screen()->post_type == 'tome_media' )
			return false;

		require_once plugin_dir_path( __FILE__ ) . 'partials/external-media-modal.php';
	}


}