<div class="row">
	<div class="large-8 large-offset-2">

		<?php
			$form_class = "embedded-media-form";

			if ( $form_type == "update" )
				$form_class = "update-media-form";
		?>

		<form class="<?php echo $form_class; ?>" id="embedded-media-form" data-abide>

			<div class="row">
				<div class="large-6 columns">
					<div class="row">

						<div class="form-row">
							<label>Media Title</label>
							<input name="post_title" required type="text">
							<small class="error">Name is required and must be a string.</small>
						</div>

						<?php if ( $form_type !== "update" ): ?>
							<div class="form-row">
								<label>Media url</label>
								<input name="tome_media_embed_script" placeholder="eg. https://www.youtube.com/watch?v=dQw4w9WgXcQ" required pattern="embed_code" type="text">
								<small class="error">Link must enter valid link.</small>
							</div>
						<?php endif ?>

<!-- 						<div class="form-row">
							<label>Caption</label>
							<input name="tome_media_embed_caption" type="text">
						</div>
 -->
						<input type="submit" class="tome-btn" value="Submit">

					</div>
				</div>
				<div class="large-6 columns">
					<div class="row">
						<div class="large-12 columns">
							<a href="#" class="pick-thumbnail">Custom Thumbnail</a>
							<div class="image-holder"></div>
							<input type="hidden" class="custom-thumbnail-id" name="media_thumbnail">
						</div>
					</div>
				</div>
			</div>

		</form>

	</div>
</div>