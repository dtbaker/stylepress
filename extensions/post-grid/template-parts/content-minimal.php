<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package void
 */

defined( 'STYLEPRESS_PATH' ) || exit;

global $count,$col_no,$col_width,$post_count;
?>

	<div class="col-md-<?php echo esc_attr( $col_width );?>">
		<header class="entry-header item">
		<?php
			if( has_post_thumbnail()) : ?>
			<div class="post-img">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php
					if( get_transient('void_grid_image_size') ){
						$grid_image_size = get_transient('void_grid_image_size');
					}else{
						$grid_image_size = 'full';
					}
					the_post_thumbnail( $grid_image_size, array(
							'class' => 'img-responsive',
							'alt'	=> get_the_title( get_post_thumbnail_id() )
							)
					);
				?>
				</a>
			</div><!--.post-img-->
			<?php endif; ?>

			<div class="post-info">
				<?php
					if ( 'post' === get_post_type() ) : ?>
						<div class="entry-meta">
							<?php
								void_entry_header();
							?>
						</div><!-- .entry-meta -->
				<?php endif; ?>
				<?php
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				?>

				<?php
					if ( 'post' === get_post_type() ) : ?>
						<div class="blog-excerpt">
							<?php the_excerpt(); ?>
						</div><!--.blog-excerpt-->
				<?php endif; ?>
			</div><!--.post-info-->

		</header><!-- .entry-header -->
	</div><!--.col-md-?-->
	<?php
			$last_post = false;
			if( !empty($post_count) ){
				if(  $post_count == $count ){
					$last_post = true;
				}
			}

		?>
		<?php	if( $count%$col_no == 0 || $last_post ) : ?>
			</div><div class="row">


		<?php endif; ?>
