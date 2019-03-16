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

<div class="stylepress-grid__item <?php echo ! has_post_thumbnail() ? 'stylepress-grid__item--nothumb' : ''; ?>">
	<header class="stylepress-grid__item-header">
		<?php
		if ( $stylepress['meta_show_thumbnail'] ) :
		if ( has_post_thumbnail() ) : ?>
			<div class="stylepress-grid__item-thumb">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php
					the_post_thumbnail( $stylepress['image_size'], array(
							'class' => 'stylepress-grid__item-image',
							'alt'   => get_the_title( get_post_thumbnail_id() )
						)
					);
					?>
				</a>
			</div><!--.post-img-->
		<?php endif;
		endif; ?>

		<div class="stylepress-grid__item-content">
			<?php
			if ( $stylepress['meta_show_title'] ) {
				the_title( '<h2 class="stylepress-grid__item-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			}
			?>

			<?php
			if ( 'post' === get_post_type() ) : ?>
				<div class="stylepress-grid__item-meta">
					<?php
					if ( $stylepress['meta_show_date'] ) {

						$time_string_posted = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
						$time_string_posted = sprintf( $time_string_posted,
							esc_attr( get_the_date( 'c' ) ),
							esc_html( get_the_date() )
						);
						$posted_on          = sprintf(
							esc_html_x( '%s', 'post date', 'stylepress' ),
							'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string_posted . '</a>'
						);
						echo '<span class="posted-on">' . $posted_on . '</span> ';
					}

					if ( $stylepress['meta_show_author'] ) {
						$byline = sprintf(
							esc_html_x( '%s', 'post author', 'stylepress' ),
							'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
						);
						echo '<span class="byline">' . $byline . '</span> ';
					}

					if ( $stylepress['meta_show_category'] ) {
						/* translators: used between list items, there is a space after the comma */
						$categories_list = get_the_category_list( esc_html__( ', ', 'stylepress' ) );
						if ( $categories_list ) {
							printf( '<span class="cat-links">' . esc_html__( ' %1$s ', 'stylepress' ) . '</span>', $categories_list ); // WPCS: XSS OK.
						}
					}

					if ( $stylepress['meta_show_tags'] ) {
						/* translators: used between list items, there is a space after the comma */
						$tags_list = get_the_tag_list( '', esc_html__( ', ', 'stylepress' ) );
						if ( $tags_list ) {
							printf( '<span class="tags-links">' . esc_html__( ' %1$s', 'stylepress' ) . '</span>', $tags_list ); // WPCS: XSS OK.
						}
					}

					if ( $stylepress['meta_show_comments'] ) {
						if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
							echo '<span class="comments-link">';
							/* translators: %s: post title */
							comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'stylepress' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
							echo '</span>';
						}
					}
					?>
				</div><!-- .stylepress-grid__item-meta -->
				<?php
				if ( $stylepress['meta_show_excerpt'] ) { ?>
					<div class="stylepress-grid__item-excerpt">
						<?php the_excerpt(); ?>
					</div><!--.stylepress-grid__item-excerpt-->
				<?php } ?>
			<?php endif; ?>
		</div><!--.stylepress-grid__item-content-->

	</header>
</div>

