<?php
$tags = get_tags();

$counter = 1;
?>

<?php foreach ( $tags as $tag ): ?>


	<div class="chapter">
		<h2 class="chapter-title">
			<a href="<?php echo $tag->link; ?>">
				<?php echo $counter . '. ' . $tag->name; ?>
			</a>
		</h2>
	</div>

	<?php echo $tag->name; ?>

	<?php $counter++; ?>

<?php endforeach; ?>

<?php die(); ?>