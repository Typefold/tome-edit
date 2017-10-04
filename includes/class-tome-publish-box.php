<?php


/**
 * Tome Publish Box class
 *
 * @package    tome-edit
 * @subpackage tome-edit/admin
 */
class Tome_Publish_Box
{

	protected $loader;

	function __construct( $loader)
	{
		$this->loader = $loader;

		$this->define_admin_hooks();
	}

	function define_admin_hooks() {
		$this->loader->add_action('edit_form_after_title', $this, 'post_type_options_modal');
		$this->loader->add_action('edit_form_after_title', $this, 'tome_publishbox');
	}

	function post_type_options_modal() {
		global $post, $pagenow, $post_type;



		if ( $this->is_exact_screen( array( 'chapter' ) ) ) {
		?>

			<div id="chapter-options-modal" class="tome-modal general">
				<div class="top-part">
					<div class="modal-content-wrapper">
						<h2>Chapter Options</h2>
						<div class="modal-section-tab active" id="header-section">Header Options</div>
						<div class="modal-section-tab" id="general-options">Discussion</div>
						<div class="modal-section-tab" id="revisions-section">Revisions</div>
						<div class="modal-section-tab" id="visibility-section">Visibility</div>
					</div>
				</div>

			
				<div class="modal-section" tab-id="header-section">
					<div class="modal-content-wrapper">
						<?php
						// Header Options Group
						$fields = acf_get_fields( 'group_56b96f816afb7' );
						acf_render_fields( $post->ID, $fields, 'div' );
						?>
					</div>
				</div>

			
				<div class="modal-section hidden" tab-id="general-options">
					<div class="modal-content-wrapper">
						<input name="advanced_view" type="hidden" value="1" />
						<label for="comment_status" class="selectit">
							<input name="comment_status" type="checkbox" id="comment_status" value="open" class="hidden-field"  <?php checked( $post->comment_status, 'open' ); ?> >
							Allow comments.
						</label>
						<?php do_action( 'post_comment_status_meta_box-options', $post ); ?>
					</div>
				</div>

			
				<div class="modal-section hidden" tab-id="revisions-section">
					<div class="modal-content-wrapper">
						<?php
							if ( $revisions = wp_get_post_revisions( $post->ID ) ) {

								$rows = '';
								foreach ( $revisions as $revision ) {
									if ( ! current_user_can( 'read_post', $revision->ID ) )
										continue;

									$is_autosave = wp_is_post_autosave( $revision );
									if ( ( 'revision' === $type && $is_autosave ) || ( 'autosave' === $type && ! $is_autosave ) )
										continue;

									$rows .= "\t<li>" . wp_post_revision_title_expanded( $revision ) . "</li>\n";
								}

								echo "<ul class='post-revisions'>\n";
								echo $rows;
								echo "</ul>";

							}
						?>

					</div>
				</div>

			
				<div class="modal-section hidden" tab-id="visibility-section">
					<div class="modal-content-wrapper">
						<?php $visibility = $post->post_status;  ?>

						<?php ( !empty( $post->post_password ) ) ? $visibility = 'password' : ''; ?>

						<div class="visibility-options">
							
							<input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="<?php echo $post->post_status; ?>">
							<input type="hidden" name="hidden_post_status" id="hidden-post-visibility" value="<?php echo esc_attr( ('auto-draft' == $post->post_status ) ? 'draft' : $post->post_status); ?>">
							<input type="hidden" name="hidden_post_password" id="hidden-post-password" value="<?php echo esc_attr($post->post_password); ?>" />

							<div class="option-wrapper">									
								<input type="radio" name="visibility" id="visibility-radio-public" value="public" <?php checked( $visibility, 'publish' ); ?> />
								<label for="visibility-radio-public" class="selectit"><?php _e('Public'); ?></label><br />
							</div>
							<div class="option-wrapper">
								<input type="radio" name="visibility" id="visibility-radio-password" value="password" <?php checked( $visibility, 'password' ); ?> />
								<label for="visibility-radio-password" class="selectit"><?php _e('Password protected'); ?></label><br />

								<?php $hidden = ( $visibility !== 'password' ) ? 'hidden' : ''; ?>
								<input type="text" name="post_password" id="post_password" value="<?php echo esc_attr($post->post_password); ?>"  maxlength="20" placeholder="password"/ class="<?php echo $hidden; ?>">
							</div>									
							<div class="option-wrapper">
								<input type="radio" name="visibility" id="visibility-radio-private" value="private" <?php checked( $visibility, 'private' ); ?> />
								<label for="visibility-radio-private" class="selectit"><?php _e('Private'); ?></label><br />
							</div>									


						</div> <!-- .visibility-options -->

					</div>
				</div>

				<div class="bottom-content">
					<input type="button" class="button button-primary save-chapter-options" value="Save">
					<a class="close-modal">&#215;</a>
				</div>
			</div>

		<?php }
	}

	function tome_publishbox() {

		if ( $this->is_exact_screen( array( 'chapter', 'tome_place', 'post', 'page', 'tome_media' ) ) == false )
			return;

		global $post;

		// // If we are creating new post we want 
		( $post->post_status === 'auto-draft' ) ? $post_status_label = 'draft' : "";


		if ( $post->post_status == 'private' ) {
			$post->post_status = 'publish';
		}

		$status = array(
			'publish' => 'Published',
			'pending' => 'Pending Review',
			'auto-draft' => 'Draft',
			'draft' => 'Draft',
			'private' => 'Private',
		);

		?>
		<div class="publish-cover"></div>

		<div class="custom-publish">

			<div class="top-part">
				
				<div class="settings">		
					<div class="single-setting">			
						<span>Status: <b><?php echo $status[ $post->post_status ]; ?></b> </span>
						<span class="separator">-</span>
						<a href="javascript:;" class="edit-link" data-options-id="status-options">Edit</a>
					</div>
					<div class="single-setting">			
						<span>Revisions: <b><?php echo count( wp_get_post_revisions( $post->ID ) ); ?></b> </span>
						
						<?php $revision_id = array_keys( wp_get_post_revisions( $post->ID ) ); ?>

						<?php if( count( $revision_id ) > 0 ) { ?>
							<span class="separator">-</span>
							<a href="/wp-admin/revision.php?revision=<?php echo $revision_id[0]; ?>" target="_blank">Browse</a>
						<?php } ?>

					</div>
					<div class="single-setting">			
						<?php
						$datef = __( 'M j, Y @ H:i' );
						$stamp = __('Uploaded on: <b>%1$s</b>');
						$date = date_i18n( $datef, strtotime( $post->post_date ) );
						?>
						<span>Published on: <b><?php echo $date; ?></b> </span>
						<span class="separator">-</span>
						<a href="javascript:;" class="edit-link" data-options-id="date-options">Edit</a>
					</div>
				</div>

				<div class="tome-publish-actions">
				<?php
					if ( current_user_can('edit_page', $post->ID) ) {
							echo '<a href="'.get_delete_post_link($post->ID).'" class="tome-delete-link button dashicons dashicons-trash"></a>';

							if ( $this->is_exact_screen( array( 'chapter' ) ) ) {
								echo '<button type="button" class="button open-modal tooltip-holder" data-modal-id="chapter-options-modal" data-tooltip-content="Manage header design, visibility, and revisions" data-tooltip-position="bottom right">Chapter Options</button>';
							}

							$this->custom_publish_button($post);
					}
				?>
				</div>

			</div>

			<div class="sub-wrapper">  	
				<div id="status-options" class="options">			
					<div class="single-option">						
						<label>Published</label>
						<input type="radio" value="publish" <?php checked($post->post_status, 'publish'); ?> name="post_status">
					</div>
					<div class="single-option">						
						<label>Pending Review</label>
						<input type="radio" value="pending" <?php checked($post->post_status, 'pending') ?> name="post_status">
					</div>
					<div class="single-option">						
						<label>Draft</label>
						<input type="radio" value="draft" <?php checked($post->post_status, 'draft') ?> name="post_status">
					</div>
					<span class="close-options">/ <a href="javascript:;" class="cancel-editing">cancel</a></span>
				</div>
				<div id="date-options" class="options">			

				<?php
				touch_time($multi = false);
				?>
				<span class="close-options">/ <a href="javascript:;" class="cancel-editing">cancel</a></span>


				</div>

				<input type="submit" class="save-publish-options" value="Save">

			</div>

		</div>
		<?
	}

	function is_exact_screen( $allowed_post_types ) {
		global $post, $pagenow, $post_type;

		if ( ! $pagenow == 'post.php' && in_array($post_type, $allowed_post_types) == false )
			return false;
		
		return true;
	}

	/**
	 * This function is copied from  wp-admin/includes/meta-boxes.php (line 262)
	 */
	function custom_publish_button($post)
	{
		?>

	<div id="submitdiv">
		<div id="submitpost"> <!-- !!!!! it's very important to have the submit button wrapped in this ID -->

			<?php

				if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
					if ( current_user_can( 'publish_posts' ) ) :
						if ( !empty($post->post_date_gmt) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) : ?>
						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Schedule') ?>" />
						<?php submit_button( __( 'Schedule' ), 'button button-primary', 'publish', false ); ?>
				<?php	else : ?>
						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>" />
						<?php submit_button( __( 'Publish' ), 'button button-primary', 'publish', false ); ?>
				<?php	endif;
					else : ?>
						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Submit for Review') ?>" />
						<?php submit_button( __( 'Submit for Review' ), 'button button-primary', 'publish', false ); ?>
				<?php
					endif;
				} else { ?>
						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update') ?>" />
						<input name="save" type="submit" class="button button-button button-primary" id="publish" value="<?php esc_attr_e( 'Update' ) ?>" />
				<?php
				} 

			?>
		</div>
	</div>

	<?php }

}


