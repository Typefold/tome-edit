<?php

/**
 * Tome Publish Box class
 *
 * @package    tome-edit
 * @subpackage tome-edit/admin
 */
class Tome_Chapter
{

	protected $loader;

	function __construct( $loader)
	{
		$this->loader = $loader;

		$this->define_admin_hooks();
	}

	function define_admin_hooks() {
		$this->loader->add_action('edit_form_after_title', $this, 'author_name');
		$this->loader->add_action('save_post', $this, 'tome_update_chapter' );

		$this->loader->add_filter('get_user_option_screen_layout_chapter', $this, 'set_one_column_layout' );
		$this->loader->add_filter('get_user_option_screen_layout_page', $this, 'set_one_column_layout' );

	}

	function tome_update_chapter( $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( $post_type !== 'chapter' )
			return;


		if ( $_POST['visibility'] == 'public' ) {

			if ( ! wp_is_post_revision( $post_id ) ) {

				// unhook this function so it doesn't loop infinitely
				remove_action('save_post', array( $this, 'tome_update_chapter') );

				// update the post, which calls save_post again
				$args = array(
					'ID' => $post_id,
					'post_status' => 'publish'
				);

				wp_update_post( $args );

				// re-hook this function
				add_action('save_post', array( $this, 'tome_update_chapter') );

			}

		}

	}

	function set_one_column_layout() {
		return 1;
	}


	function author_name() {
		global $post;

		if ( ! $this->is_chapter_screen() )
			return;

		$custom = get_post_custom($post->ID);
		$byline = $custom["chapter_byline"][0]; // authors name
		?>
		<div class="byline">
			<input type="text" name="chapter_byline" placeholder="Author Name" id="byline-input" value="<?php echo $byline; ?>">
		</div>
		<?php
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

}


