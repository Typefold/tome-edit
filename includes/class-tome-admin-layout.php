<?php

/**
 * This class is resposible for Layout edits in admin. which are general and reused for different post types.
 *
 * @package    tome-edit
 * @subpackage tome-edit/admin
 */
class Tome_Admin_Layout
{

	protected $loader;

	function __construct( $loader)
	{
		$this->loader = $loader;

		$this->define_admin_hooks();

		$this->init_publish_box();
	}

	function define_admin_hooks() {
		$this->loader->add_action('admin_menu', $this, 'remove_metaboxes');
		$this->loader->add_action('in_admin_header', $this, 'start_wrapper');
		$this->loader->add_action('in_admin_footer', $this, 'end_wrapper');
	}

	function init_publish_box() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tome-publish-box.php';
		$this->publish_box = new Tome_Publish_Box( $this->loader );
	}

	function remove_metaboxes() {
		remove_meta_box('trackbacksdiv', 'chapter', 'normal');
		remove_meta_box('commentstatusdiv', 'chapter', 'normal');
		remove_meta_box('commentsdiv', 'chapter', 'normal');
		remove_meta_box('revisionsdiv', 'chapter', 'normal');
		remove_meta_box('authordiv', 'chapter', 'normal');
		remove_meta_box('slugdiv', 'chapter', 'normal');
		remove_meta_box('submitdiv', 'chapter', 'normal');
		remove_meta_box('ref_tooltip_meta', 'chapter', 'normal');

		// remove metaboxes form other post type than chapter
		remove_meta_box('slugdiv', 'tome_gallery', 'normal');
		remove_meta_box('categorydiv', 'tome_gallery', 'normal');
		remove_meta_box('tagsdiv-post_tag', 'tome_gallery', 'normal');
		remove_meta_box('postimagediv', 'tome_gallery', 'normal');

		// remove metaboxes form other post type than chapter
		remove_meta_box('submitdiv', 'tome_map', 'normal');
		remove_meta_box('submitdiv', 'post', 'normal');
		remove_meta_box('submitdiv', 'page', 'normal');
		remove_meta_box('submitdiv', 'tome_media', 'normal');
		remove_meta_box('submitdiv', 'tome_gallery', 'normal');

		remove_meta_box('tagsdiv-post_tag', 'post', 'normal');
	}


	function is_chapter_screen() {

		if ( function_exists( 'get_current_screen' ) ) {

			$screen = get_current_screen();

			if ( $screen->base == 'post' && $screen->post_type == 'chapter' ) {
				return true;
			}
		}


		return false;
	}


	function start_wrapper() {
		global $pagenow, $post_type;

		$post_types = array( 'chapter', 'page', 'post' );

		if ( ( $pagenow == "post.php" || $pagenow == 'post-new.php' ) && in_array( $post_type, $post_types ) )
			echo '<div class="chapter-wrapper">';
	}


	function end_wrapper() {
		global $pagenow, $post_type;

		$post_types = array( 'chapter', 'page', 'post' );

		if ( ( $pagenow == "post.php" || $pagenow == 'post-new.php' ) && in_array( $post_type, $post_types ) )
			echo '</div>';
	}

}


