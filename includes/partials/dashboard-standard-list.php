<h1 class="dashboard-widget-title"><?php echo $heading_text; ?></h1>

<a class="new-chapter tooltip-holder" data-tooltip-content="Main section of text + media. Can be arranged in <br> any order and navigate in sequence." data-tooltip-position="right middle" href="<?php echo $new_link; ?>">New</a>

<input type="text" class="search search-chapters" placeholder="Search">

<?php

$args = array(
	'post_type' => $post_type, // this is variable from class-tome-dashboard.php FUNCTION "dashboard_lsit"
	'post_status' => 'any',
	'posts_per_page' => 30
);
$attachments_loop = new WP_Query($args);
$counter = 1;
?>

<div class="list">

	<?php if ( $attachments_loop->have_posts() ) : while ( $attachments_loop->have_posts() ) : $attachments_loop->the_post(); ?>

		<?php $post = get_post(); ?>

		<div class="chapter" data-neco="<?php echo $post_type; ?>">
			<?php $title = get_the_title(); ?>

			<?php if ( empty( $title ) ): ?>
				<h2 class="chapter-title">
					<?php if ( current_user_can( 'edit_posts' ) ): ?>
						<a href="<?php echo get_edit_post_link( $post->ID ); ?>"> <?php echo $counter . '. ' . $post->post_name; ?></a>
					<?php else: ?>
						<?php echo $post->post_name; ?>
					<?php endif; ?>
				</h2>
			<?php else: ?>
				<h2 class="chapter-title">
					<?php if ( current_user_can( 'edit_posts' ) ): ?>
						<a href="<?php echo get_edit_post_link( $post->ID ); ?>"> <?php echo $counter . '. ' . $post->post_name; ?></a>
					<?php else: ?>
						<?php echo $post->post_name; ?>
					<?php endif; ?>
				</h2>
			<?php endif ?>
			<a href="<?php the_permalink(); ?>" target="_blank" class="post-edit-link">Preview</a>

			<?php
			if ( function_exists('icl_object_id') )
				Tome_Dashboard::the_translation_link( $post );
			?>

		</div>

		<?php $counter++; ?>

	<?php endwhile; ?>
	<!-- post navigation -->
	<?php else: ?>
		<h2>You haven't created any <?php echo $plural_post_type; ?> yet.</h2>
	<?php endif; ?>

</div>

<ul class="pagination"></ul>

