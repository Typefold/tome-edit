<?php


/**
 * Tome Dashboard class
 *
 * @package    tome-edit
 * @subpackage tome-edit/admin
 */
class Tome_Dashboard
{

	protected $loader;

	function __construct( $loader )
	{
		$this->loader = $loader;

		$this->define_admin_hooks();
	}

	private function define_admin_hooks()
	{
		$this->loader->add_action('admin_menu', $this, 'tome_dashboard_menu_item');
		$this->loader->add_action('load-index.php', $this, 'dashboard_redirect');
		$this->loader->add_action('load-about.php', $this, 'dashboard_redirect');

		$this->loader->add_action( 'wp_ajax_dashboard_list', $this, 'dashboard_list' );
		$this->loader->add_action( 'wp_ajax_nopriv_dashboard_list', $this, 'dashboard_list' );

		// $this->loader->add_filter('posts_orderby', $this, 'tome_sort', 99, 2);
	}

	function tome_sort($orderBy, $query) {
		global $wpdb;

		$orderBy = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";

		return $orderBy;
	}


	/**
	 * redirect from default WP dashboard to Tome Dashboard
	 */
	function dashboard_redirect() {

		if( is_admin() ) {

			if ( current_user_can( 'edit_posts' ) ) 
				return wp_redirect( admin_url( 'admin.php?page=tome-dashboard' ) );

			return wp_redirect( admin_url( 'profile.php' ) );

		}

	}

	/**
	 * Adds new 'Tome Dashboard' to the admin menu
	 */
	function tome_dashboard_menu_item() {
		add_menu_page(
			'Tome Dashboard',
			'Dashboard',
			'edit_posts',
			'tome-dashboard',
			array($this, 'tome_dashboard_page'),
			'dashicons-dashboard',
			1
			);
	}

	/**
	 * Callback for the "tome_dashboard_menu_item" function.
	 * HTML for Tome Dashboard
	 */
	function tome_dashboard_page() {
		// TO-DO rewrite the way how we display "loader" animation when switching dashboard sections
		// because right now it's overlapping all elements in the "white panel" and I had to add
		// z-index 99999 to all elements which are clickable which is not really sexy.
		?>

		<div id="tome-dashboard">

			<div class="row">
				<div class="small-8 columns list-wrapper" id="chapters-widget">

					<?php
					$post_type = 'chapter';
					$new_link = "post-new.php?post_type=chapter";
					$heading_text = "Chapters";
					$plural_post_type = $this->get_post_type_label_name( $post_type );

					require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/partials/dashboard-standard-list.php';
					$this->display_reorder_button( $post_type );
					?>
				</div>

				<div class="small-4 columns dashboard-nav-wrapper">          	
					<div class="dashboard-nav">
						<div class="small-6 columns action active" data-type="chapter" data-new="post-new.php?post_type=chapter">
							<i class="dashicons dashicons-edit"></i>
							<h2>Chapters</h2>
						</div>
						<div class="small-6 columns action" data-type="tome_place" data-new="post-new.php?post_type=tome_place">
							<i class="dashicons dashicons-location"></i>
							<h2>Places</h2>
						</div>
						<div class="small-6 columns action" data-type="tome_place" data-new="post-new.php?post_type=tome_map">
							<i class="dashicons dashicons-location-alt"></i>
							<h2>Maps</h2>
						</div>
						<div class="small-6 columns action" data-type="post" data-new="post-new.php?post_type=post">
							<i class="dashicons dashicons-admin-post"></i>
							<h2>Blog Posts</h2>
						</div>
						<a href="upload.php">
							<div class="small-6 columns action redirect" data-type="attachment" data-new="post-new.php?post_type=attachment">
								<i class="dashicons dashicons-admin-media"></i>
								<h2>Media Library</h2>
							</div>
						</a>
						<a href="upload.php?page=external-media-list">
							<div class="small-6 columns action redirect" data-type="attachment" data-new="">
								<i class="dashicons dashicons-media-code"></i>
								<h2><?php _e('External Media', 'tome'); ?></h2>
							</div>
						</a>	
						<div class="small-6 columns action" data-type="tome_gallery" data-new="post-new.php?post_type=tome_gallery">
							<i class="dashicons dashicons-format-gallery"></i>
							<h2>Galleries</h2>
						</div>
						<a href="admin.php?page=biblio-list">
							<div class="small-6 columns action redirect" data-type="attachment" data-new="post-new.php?post_type=attachment">
								<i class="dashicons bilbio-entries-nav-icon"></i>
								<h2>Bibliographic entries</h2>
							</div>
						</a>
						<a href="nav-menus.php">
							<div class="small-6 columns action redirect dark">
								<i class="dashicons dashicons-menu"></i>
								<h2>Edit Menu</h2>
							</div>
						</a>
						<a href="admin.php?page=tome-cover-settings">
							<div class="small-6 columns action redirect dark">
								<i class="cover-settings-icon"></i>
								<h2>Cover Settings</h2>
							</div>
						</a>

						<?php do_action('dashboard_menu'); ?>

						<a href="admin.php?page=simply-static">
							<div class="small-6 columns publish-action tooltip-holder" data-tooltip-content="Saves and downloads a static HTML version of <br> your project for preservation." data-tooltip-position="left middle">
								<i class="dashicons dashicons-upload"></i>
								<h2>Publish</h2>
							</div>
						</a>


					</div>
				</div>

			</div>

		</div>

	<?php
	}



	private function display_reorder_button( $post_type = null ) {
		if ( is_plugin_active( 'post-types-order/post-types-order.php' ) )
			echo '<a href="/wp-admin/admin.php?page=order-post-types-'.$post_type.'" class="button reorder-link" >Re-order</a>';
	}



	public static function the_translation_link( $post ) {
		global $sitepress;

		$trid = $sitepress->get_element_trid( $post->ID, 'post_' . $post->post_type );
		$translations = $sitepress->get_element_translations($trid);

		foreach ($translations as $translation) {

			if ( $translation->language_code !== ICL_LANGUAGE_CODE ) {						
				$lang_code = $translation->language_code;
				$translation_id = $translation->element_id;
				$translation_link = get_edit_post_link( $translation_id );
				$translation_name = icl_disp_language( $lang_code );
				echo '<a href="' .$translation_link. '" class="post-translation-link">' . $translation_name . '</a>';
			}

		}
	}



	function dashboard_list() {

		$post_type = $_POST['post_type'];
		$post_type_obj = get_post_type_object ( $post_type );
		$new_link = $_POST['new_link'];
		$heading_text = $_POST['heading_text'];
		$plural_post_type = $this->get_post_type_label_name( $post_type );

		switch ( $post_type ) {
			case 'tags':
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/partials/dashboard-tags-list.php';
			break;

			default:
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/partials/dashboard-standard-list.php';
			break;
		}

		$this->display_reorder_button( $post_type );

		die();

	}


	function get_post_type_label_name( $post_type ) {
		$post_type_obj = get_post_type_object ( $post_type );
		return $post_type_obj->labels->name;
	}

}