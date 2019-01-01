<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;

?>

<div class="col-md-<?php echo esc_attr( $stylepress['col_width'] ); ?>">
	<header class="entry-header">
		<?php
		if ( has_post_thumbnail() ) : ?>
			<div class="post-img">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php
					the_post_thumbnail( $stylepress['image_size'], array(
							'class' => 'img-responsive',
							'alt'   => get_the_title( get_post_thumbnail_id() )
						)
					);
					?>
				</a>
			</div><!--.post-img-->
		<?php endif; ?>

		<div class="post-info">
			<?php
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			?>

			<?php
			if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta">

					<?php
					stylepress_posted_on();
					stylepress_entry_header();
					?>

				</div><!-- .entry-meta -->
				<div class="blog-excerpt">
					<?php the_excerpt(); ?>
				</div><!--.blog-excerpt-->
			<?php endif; ?>
		</div><!--.post-info-->

	</header><!-- .entry-header -->
</div><!--.col-md-?-->

