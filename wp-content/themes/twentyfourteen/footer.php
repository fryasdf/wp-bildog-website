<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>

            </div><!-- #main -->
        </div><!-- #page -->
    </div><!-- #wrapper -->

    <footer id="colophon" class="site-footer" role="contentinfo">

        <?php get_sidebar( 'footer' ); ?>

    </footer><!-- #colophon -->

	<?php wp_footer(); ?>
</body>
</html>