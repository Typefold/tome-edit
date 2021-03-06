<div id="embedd-media-modal" class="tome-modal general" data-blog="<?php echo get_current_blog_id(); ?>">

	<div class="top-part">
		<div class="modal-content-wrapper">
			<h2>External Media</h2>
			<div class="modal-section-tab active" id="filter-section">All Media</div>
		</div>
	</div>


	<!-- In this div the HTML is beeing rendered from ajax call -->
	<div class="modal-section main-content" tab-id="filter-section">

		<div class="content">
			
			<a href="javascript:;" class="button button-primary add-media">Add media</a>

			<?php $this->print_media_form(); ?>


			<div class="media-items">
				
				<?php
					$all_media = $this->get_all_media();

					foreach ($all_media as $media) {

						$this->single_media_item( $media );

					}
				?>

			</div>
			
		</div>


	</div>


	<div class="bottom-content">
		<a class="close-modal">&#215;</a>
	</div>

</div>