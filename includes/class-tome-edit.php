<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    tome-edit
 * @subpackage tome-edit/includes
 */
class Tome_Edit {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tome_Dashboard_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;


	/**
	 * Page options class is responsible for all behavior associated with page options area (weird right?)
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tome_Page_Options   $page_options
	 */
	protected $page_options;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->plugin_name = 'tome-edit';
		$this->version = '1.0.0';
		$this->load_dependencies();
		$this->loader = new Tome_Edit_Loader();

		$this->external_media = new Tome_External_Media( $this->loader );

		// $this->page_options = new Tome_Page_Options( $this->loader );

		$this->tome_places = new Tome_Places( $this->loader );

		$this->tome_dashboard = new Tome_Dashboard( $this->loader );

		$this->tome_admin_menu = new Tome_Admin_Menu( $this->loader );

		$this->map = new Tome_Maps_Backend( $this->loader );

		$this->chapter_options = new Tome_Chapter( $this->loader );

		$this->admin_layout = new Tome_Admin_Layout( $this->loader );

		// $this->set_locale();
		$this->define_admin_hooks();
		// $this->define_public_hooks();
	}
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n. Defines internationalization functionality.
	 * - Plugin_Name_Admin. Defines all hooks for the admin area.
	 * - Plugin_Name_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tome-edit-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/lib/external-media/class-tome-external-media.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/lib/external-media/class-external-media-helpers.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tome-places.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tome-dashboard.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tome-admin-menu.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tome-maps-backend.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tome-chapter.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tome-admin-layout.php';


		// *
		//  * The class responsible for defining internationalization functionality
		//  * of the plugin.

		// // require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plugin-name-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tome-edit-admin.php';



	}
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	// private function set_locale() {
	// 	$plugin_i18n = new Plugin_Name_i18n();
	// 	$plugin_i18n->set_domain( $this->get_plugin_name() );
	// 	$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	// }

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Tome_Edit_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'tome_editor_style' );
		$this->loader->add_action( 'admin_print_styles', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_network_dashboard_setup', $plugin_admin, 'default_layout_columns' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'tome_modal' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'admin_bar_customize_link', 999 );

		// embedded media
		$this->loader->add_action( 'wp_ajax_embedded_media_modal', $plugin_admin, 'embedded_media_modal_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_embedded_media_modal', $plugin_admin, 'embedded_media_modal_callback' );
		$this->loader->add_action( 'user_register', $plugin_admin, 'set_user_metaboxes' );
		$this->loader->add_action( 'user_register', $plugin_admin, 'set_menu_hidden_fields' );

		// filters
		$this->loader->add_filter( 'gettext', $plugin_admin, 'change_howdy', 10, 3 );
		$this->loader->add_filter( 'intermediate_image_sizes_advanced', $plugin_admin, 'remove_default_image_sizes' );
		$this->loader->add_filter( 'tiny_mce_before_init', $plugin_admin, 'tome_tinymce_toolbar' );
		$this->loader->add_filter( 'image_size_names_choose', $plugin_admin, 'tome_custom_sizes_names' );
		$this->loader->add_filter( 'admin_body_class', $plugin_admin, 'default_folded_menu' );
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'tome_admin_fotoer_text' );
		$this->loader->add_filter( 'update_footer', $plugin_admin, 'tome_update_footer', 11, 3 );

		// tome editor
		$this->loader->add_filter( 'tiny_mce_before_init', $plugin_admin, 'activate_custom_tinymce' );
		$this->loader->add_filter( 'mce_external_plugins', $plugin_admin, 'tome_mce_plugins' );
	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
	}






	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
