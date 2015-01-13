<?php get_header(); ?>
<div id="page-content" class="main-column">
	<?php get_template_part('loop', 'single'); ?>
	<?php comments_template(); ?>
</div>
<?php get_footer(); ?>
