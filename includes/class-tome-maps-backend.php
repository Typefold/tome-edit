<?php

/**
* Handles Creating / adding maps to TinyMce
* Associated files JS files
*/
class Tome_Maps_Backend
{

	protected $loader;
     
	function __construct( $loader )
	{
		$this->loader = $loader;

		$this->define_admin_hooks();
	}

	function define_admin_hooks() {
		$this->loader->add_action('in_admin_header', $this, 'map_modal');
	}



	/**
	 * Content of a modal window for creating / adding Map to a chapter.
	 * Pops out when you click on "map icon" in Tome Inline Media Selector
	 */
	function map_modal() {

		// TODO: Add section for creating map dynamicly in modal window 

		?>

		<div id="tome-maps-modal" class="tome-modal general">
			<div class="top-part">
				<div class="modal-content-wrapper">   
					<h2>Tome Map</h2>
					<div class="modal-section-tab active" id="list-section">Maps List</div>
				</div>
			</div>

			<div class="modal-section hidden" tab-id="map-section">
				<div class="modal-content-wrapper">
				</div>
			</div>

			<div class="modal-section" tab-id="list-section">
				<div class="modal-content-wrapper">

				<?php
				$args = array(
					'post_type' => 'tome_map',
					'posts_per_page' => -1
				);
				$all_maps = get_posts( $args );
				?>
				
				<ul>
				<?php foreach ($all_maps as $map): ?>
					<li class="map-item" id="<?php echo $map->ID; ?>"><a href="javascript:;"><?php echo $map->post_title; ?></a></li>
				<?php endforeach ?>
				</ul>

				<a href="post-new.php?post_type=tome_map" class="button button-primary">Create New Map</a>
				

				</div>
			</div>

			<div class="bottom-content">
				<a class="close-modal">&#215;</a>
			</div>
		</div>
		
	<?php 
	}

}

?>
