<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package void
 */

defined( 'STYLEPRESS_PATH' ) || exit;

global $blog_style;
?>


	<header class="entry-header">
		<div class="post-img">
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php
					if( get_transient('void_grid_image_size') ){
						$grid_image_size = get_transient('void_grid_image_size');
					}else{
						$grid_image_size = 'blog-list-post-size';
					}
					the_post_thumbnail( $grid_image_size, array(
							'class' => 'img-responsive',
							'alt'	=> get_the_title( get_post_thumbnail_id() )
							)
					);
				?>
		 	</a>
		</div>
		<div class="post-info">
			<?php
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				?>

				<?php
				if ( 'post' === get_post_type() ) : ?>
					<div class="entry-meta">

						<?php
							void_entry_header();
						?>

					</div><!-- .entry-meta -->
					<?php the_excerpt(); ?>
			<?php endif; ?>
		</div><!--.post-info-->
	</header><!-- .entry-header -->
<div class="clearfix"></div>

