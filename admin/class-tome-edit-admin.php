<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    tome-edit
 * @subpackage tome-edit/admin
 */
class Tome_Edit_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	function tome_admin_fotoer_text( $text ) {
		return ''; 
	}

	function tome_update_footer( $content ) {
		return ''; 
	}


	function tome_tinymce_toolbar( $in ) {
		$in['menubar'] = false;
		$in['toolbar1'] = 'bold | italic | underline | strikethough | forecolor | link | dropcap | reference | tome_blockquote | pullquote | alignleft | aligncenter | alignright | formatselect';
    // $in['toolbar'] = false;
    // $in['toolbar1'] = true;
    // $in['toolbar2'] = false;
		$in['toolbar3'] = false;
		$in['toolbar4'] = false;
		$in['statusbar'] = false;

		return $in;
	}

	function change_howdy($translated, $text, $domain) {

		if (!is_admin() || 'default' != $domain)
			return $translated;

		if (false !== strpos($translated, 'Howdy'))
			return str_replace('Howdy', 'Welcome', $translated);

		return $translated;
	}

	function remove_default_image_sizes( $sizes) {
		unset( $sizes['thumbnail']);
		unset( $sizes['medium']);
		unset( $sizes['full']);

		return $sizes;
	}

	function image_sizes() {
		add_image_size( 'small', 380, 9999, false );
		add_image_size( 'jumbo', 1100, 9999, false );
		add_image_size( 'large_tome', 1100, 9999, false );

		update_option( 'large_size_w', 803, false );
		update_option( 'large_size_h', 9999, false );
		update_option( 'large_crop', false );
	}

	function tome_custom_sizes_names($sizes){
		return array_merge( $sizes, array(
			'small' => __('Small'),
			'large' => __('Large'),
			'jumbo' => __('Jumbo')
		) );
	}


	// Initialize custom tomeEditor javascript class for custom tome functionality
	function activate_custom_tinymce( $initArray ) {

		return $initArray;
	}

	/**
	 * Set one column as default number of columns in admin
	 */
	function default_layout_columns()
	{
	    add_screen_option('layout_columns', array('default' => 1));
	}

	function tome_modal() {
		?>
		<div class="tome-modal general empty loading">
		</div>
		<?php
	}

	/**
	 *
	 * mce_external_plugins
	 * Adds our tinymce plugin
	 * @param  array $plugin_array
	 * @return array
	 */
	function tome_mce_plugins( $plugin_array ) {
		$plugin_array['tomeMediaSelector'] = plugins_url( 'js/mce-media-selector.js' , __FILE__ );
		$plugin_array['shortcodeReplace'] = plugins_url( 'js/shortcode_replace.js' , __FILE__ );
		return $plugin_array;
	}

	function set_tome_thumbnail() {

		update_post_meta( $_POST['post_id'], '_thumbnail_id', $_POST['thumb_id'] );
		echo wp_get_attachment_url( $_POST['thumb_id'] );
		die();
	}

	/**
	* Hides all unnecessary metaboxes when user get registered
	*/
	function set_user_metaboxes( $user_id ) {
		update_user_meta( $user_id, 'metaboxhidden_tome_place', array('slugdiv', 'description_section', 'commentsdiv', 'commentstatusdiv', 'revisionsdiv', 'postimagediv', 'tagsdiv-post_tag') );
		update_user_meta( $user_id, 'metaboxhidden_tome_media', array('commentstatusdiv', 'commentsdiv' , 'slugdiv') );
		update_user_meta( $user_id, 'metaboxhidden_page', array('commentsdiv', 'pageparentdiv', 'postimagediv') );
		update_user_meta( $user_id, 'metaboxhidden_chapter', array('commentstatusdiv', 'authordiv', 'slugdiv', 'postimagediv', 'pageparentdiv') );
		update_user_meta( $user_id, 'metaboxhidden_nav-menus', array('add-post-type-tome_gallery', 'add-post-type-tome_media', 'add-custom-links', 'add-category', 'add-post_tag') );
	}

	function hide_permalink_options() {
		return '';
	}


	/**
	 * Add css file in which you can edit TinyMce editor style
	 * @return void
	 */
	function tome_editor_style() {
		add_editor_style( array('tome-editor.css') );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( is_admin() == false )
			return;

		$form = array(
			'return' => false
			);

		$tome_fonts = array(
			'family' => 'Noto+Sans:400,400italic,700,700italic|Lora:400,400italic,700,700italic',
			'subset' => 'latin',
		);
		wp_register_style( 'lora', add_query_arg( $tome_fonts, "//fonts.googleapis.com/css" ), array(), null );
		wp_register_style( 'main', plugin_dir_url( __FILE__ ) . 'css/main.css', null, $this->version, 'all' );

		wp_enqueue_style('lora');
		wp_enqueue_style('main');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'tome_edit_master', plugin_dir_url( __FILE__ ) . 'js/dist/master.js', array( 'jquery' ), $this->version, false );
		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Customize the "href" attribute of "customize" link in wp admin bar
	 * in front-end
	 */
	function admin_bar_customize_link( $wp_admin_bar ){

	    $customize = $wp_admin_bar->get_node( 'customize' );

	    if ( ! $customize || is_home() == false )
	        return;

	    $edited_customize = $customize;
	    $edited_customize->href = get_site_url() . '/wp-admin/admin.php?page=tome-cover-settings';

	    // override existing node
	    $wp_admin_bar->add_node($edited_customize);
	}

	function default_folded_menu( $classes ) {

		if ( get_user_setting('mfold') == false ) {
			return "$classes folded";
		}

	}

}
