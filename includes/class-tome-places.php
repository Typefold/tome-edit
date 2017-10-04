<?php

class Tome_Places
{
	protected $loader;

	public $is_place_edit_screen;

	public $place_lat;
	public $place_long;
	public $place_zoom;
	public $place_pov;

	function __construct( $loader )
	{
		$this->loader = $loader;

		$this->define_admin_hooks();
	}

	/**
	 * Get all metadata for current place
	 */
	function get_meta()
	{
		if ( $this->is_place_edit_screen == false )
			return false;

		global $post;
		$custom = get_post_custom($post->ID);

		$this->place_lat = $custom["tome_place_lat"][0];
		$this->place_long = $custom["tome_place_long"][0];
		$this->place_pov = $custom["tome_place_pov"][0];
		$this->place_zoom = $custom["tome_place_zoom"][0];
		$this->place_map_type = $custom["tome_place_map_type"][0];

		( empty($this->place_map_type) ) ? $this->place_map_type = 'satellite' : "";

	}


	private function define_admin_hooks()   
	{
		$this->loader->add_action('in_admin_header', $this, 'current_screen');

		$this->loader->add_action('in_admin_header', $this, 'get_meta');
		$this->loader->add_action('edit_form_after_title', $this, 'print_fields');
		$this->loader->add_action('in_admin_header', $this, 'print_map');
		$this->loader->add_action('edit_form_before_permalink', $this, 'title_field_label');
		$this->loader->add_action('in_admin_header', $this, 'start_wrapper');
		$this->loader->add_action('in_admin_footer', $this, 'end_wrapper');
		$this->loader->add_action('admin_menu', $this, 'remove_metaboxes');
		$this->loader->add_action('save_post', $this, 'save_tome_place_metadata');


		$this->loader->add_filter('enter_title_here', $this, 'title_box_placeholder');
		$this->loader->add_filter('gettext', $this, 'excerpt_box_description', 10, 2);
		$this->loader->add_filter('get_user_option_screen_layout_tome_place', $this, 'set_one_column_layout' );
	}


	function remove_metaboxes() {
		remove_meta_box('submitdiv', 'tome_place', 'normal');
	}

	/**
	 * Edits the placeholder for post title field
	 */
	function title_box_placeholder( $title )
	{

		if ( $this->is_place_edit_screen )
			$title = 'Name This Location';

		return $title;
	}

	function title_field_label() {
		global $post;
		
		if ($post->post_type == 'place')
			echo '<span class="titlediv-label">Put custom name of the place here</span>';
	}


	function excerpt_box_description( $translation, $original )
	{
		if ( 'Excerpt' == $original ) {
			return 'Excerpt';
		} else {
			$pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');
			if ($pos !== false) {
				return  'This is tome place excerpt.';
			}
		}
		return $translation;
	}



	public function print_fields()
	{
		global $post;

		if ( $this->is_place_edit_screen == false )
			return false;

		?>

		<div class="place-form">

			<div class="form-group search-place-wrapp">
				<i class="dashicons dashicons-search"></i>
				<input id="pac-input" type="textbox" class="place-input address" placeholder="Search for a place">
			</div>

			<div class="title-field">
				<input type="text" class="custom-title-field" name="post_title" value="<?php echo the_title(); ?>" placeholder="Name This Location">
			</div>

			<div class="cords-inputs">
				<div class="input-wrapp">
					<input type="text" class="place-input" name="tome_place_loc_lat" placeholder="Latitude" value="<?php echo $this->place_lat; ?>" />
				</div>
				<div class="input-wrapp">
					<input type="text" class="place-input" name="tome_place_loc_long" placeholder="Longitude" value="<?php echo $this->place_long; ?>" />
				</div>
				<div class="input-wrapp">
					<input type="hidden" class="place-input" name="tome_place_zoom" placeholder="Zoom" value="<?php echo $this->place_zoom; ?>" size="2" />
				</div>
				<div class="input-wrapp submit-wrapp">
					<input type="button" class="find-place" value="Find" onclick="codeAddress('admin')">
				</div>
				
				<input type="hidden" name="tome_place_pov" value="<?php echo $this->place_pov; ?>">
				<input type="hidden" name="tome_place_map_type" value="<?php echo $this->place_map_type; ?>">
			</div>

		</div>

		<div class="place-content tome-tabs">
			<ul class="tabs-nav">
				<li class="active" data-section-id="tab-content">Place page</li>
				<li data-section-id="tab-excerpt">Place description</li>
			</ul>
			<div class="tabs-section active" id="tab-content">
				<p class="section-description">Add text and insert rich media to the page for this place.</p>
				<?php
				$content = $post->post_content;
				?>
				<?php wp_editor($content, 'content', array('media_buttons' => false,)); ?>
			</div>
			<div class="tabs-section" id="tab-excerpt">
				<p class="section-description">Write a short description to display with the drop pin.</p>
				<textarea name="excerpt" id="excerpt" class="custom-excerpt"><?php echo $post->post_excerpt; ?></textarea>
			</div>
		</div>

		<?php

	}

	/**
	 * Are we on Tome Place edit screen
	 */
	public function current_screen()
	{

		$screen = get_current_screen();

		if ( $screen->base == 'post' && $screen->post_type == 'tome_place' ) {
			$this->is_place_edit_screen = true;
			return true;
		}

		$this->is_place_edit_screen = false;
	}

	public function print_map()
	{
		if ( $this->is_place_edit_screen == false )
			return false;

		echo '<div class="map-wrapper"><div id="map-canvas-admin" class="map-canvas tome_place_admin-map haveCoords" data-latitude="'.$this->place_lat.'" data-longitude="'.$this->place_long.'" data-zoom="'.$this->place_zoom.'" data-pov=\''.$this->place_pov.'\' data-type="'.$this->place_map_type.'" data-id="admin" style="width: 100%; height: 500px;"></div></div>';
	}


	function set_one_column_layout() {
		return 1;
	}


	function start_wrapper() {
		global $pagenow, $post_type;

		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && $post_type == 'tome_place' )
			echo '<div class="chapter-wrapper">';
	}


	function end_wrapper() {
		global $pagenow, $post_type;

		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && $post_type == 'tome_place' )
			echo '</div>';

	}


	function save_tome_place_metadata( $post_id ) {

		$post_type = get_post_type( $post_id );

		if ( $post_type == 'tome_place' ) {

			update_post_meta($post_id, "tome_place_lat", $_POST["tome_place_loc_lat"]);
			update_post_meta($post_id, "tome_place_long", $_POST["tome_place_loc_long"]);
			update_post_meta($post_id, "tome_place_zoom", $_POST["tome_place_zoom"]);
			update_post_meta($post_id, "tome_place_map_type", $_POST["tome_place_map_type"]);
			update_post_meta($post_id, "tome_place_pov", $_POST["tome_place_pov"]);

		}

	  }

}
