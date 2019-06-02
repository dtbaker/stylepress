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
						if ( !empty($stylepress['image_style']) && $stylepress['image_style'] === 'category-over' ) {
							$categories_list = strip_tags( get_the_category_list( esc_html__( ', ', 'stylepress' ) ) );
							if ( $categories_list ) {
								?>
								<div class="stylepress-grid__item-thumb-overlay">
									<?php printf( esc_html__( ' %1$s ', 'stylepress' ), $categories_list ); // WPCS: XSS OK. ?>
								</div>
								<?php
							}
						}
						the_post_thumbnail( $stylepress['image_size'] === 'custom' ? array_values( $stylepress['image_custom_dimension'] ) : $stylepress['image_size'], array(
								'class' => 'stylepress-grid__item-image',
								'alt'   => get_the_title( get_post_thumbnail_id() )
							)
						);
						?>
					</a>
				</div><!--.post-img-->
			<?php endif;
		endif; ?>
	</header>

	<section class="stylepress-grid__item-content">
		<?php
		if ( $stylepress['meta_show_title'] ) {
			the_title( '<h2 class="stylepress-grid__item-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		if(!empty($stylepress['decoration_image']) && !empty($stylepress['decoration_image']['url'])) {
			?>
			<div class="stylepress-grid__item-decoration">
				<?php echo '<img src="' . esc_url( $stylepress['decoration_image']['url'] ) . '">' ?>
			</div>
			<?php
		}
		if ( $stylepress['meta_show_excerpt'] ) { ?>
			<div class="stylepress-grid__item-excerpt">
				<?php the_excerpt(); ?>
			</div><!--.stylepress-grid__item-excerpt-->
		<?php }
		$meta_sections = [];
		if ( $stylepress['meta_show_date'] ) {

			$time_string_posted         = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
			$time_string_posted         = sprintf( $time_string_posted,
				esc_attr( get_the_date( 'c' ) ),
				esc_html( get_the_date() )
			);
			$posted_on                  = sprintf(
				esc_html_x( '%s', 'post date', 'stylepress' ),
				'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string_posted . '</a>'
			);
			$meta_sections['posted-on'] = $posted_on;
		}

		if ( $stylepress['meta_show_comments'] ) {
			if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
				ob_start();
				/* translators: %s: post title */
				comments_popup_link( get_comments_number() > 0 ? sprintf( _x( 'One Comment', '%1$s Comments', get_comments_number(), 'stylepress' ), number_format_i18n( get_comments_number() ) ) : esc_html__( 'No Comments', 'stylepress' ) );
				$meta_sections['comments-link'] = ob_get_clean();
			}
		}

		if ( $stylepress['meta_show_author'] ) {
			$byline                  = sprintf(
				esc_html_x( '%s', 'post author', 'stylepress' ),
				'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
			);
			$meta_sections['author'] = $byline;
		}

		if ( $stylepress['meta_show_category'] ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'stylepress' ) );
			if ( $categories_list ) {
				$meta_sections['category'] = sprintf( esc_html__( ' %1$s ', 'stylepress' ), $categories_list ); // WPCS: XSS OK.
			}
		}

		if ( $stylepress['meta_show_tags'] ) {
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html__( ', ', 'stylepress' ) );
			if ( $tags_list ) {
				$meta_sections['tags'] = sprintf( esc_html__( ' %1$s', 'stylepress' ), $tags_list ); // WPCS: XSS OK.
			}
		}
		$meta_sections = apply_filters( 'stylepress_post_meta', $meta_sections, get_the_ID() );
		if ( $meta_sections ) { ?>
			<div class="stylepress-grid__item-meta">
				<ul class="stylepress-grid__item-meta-list">
					<?php foreach ( $meta_sections as $meta_section => $meta_section_data ) { ?>
						<li class="stylepress-grid__item-meta-<?php echo esc_attr( $meta_section ); ?>">
							<?php echo $meta_section_data; ?>
						</li>
					<?php } ?>
				</ul>
			</div><!-- .stylepress-grid__item-meta -->
		<?php } ?>
	</section><!--.stylepress-grid__item-content-->

	<footer class="stylepress-grid__item-footer">
		<?php if ( $stylepress['meta_show_readmore'] ) { ?>
			<div class="stylepress-grid__item-readmore">
				<?php printf( '<a class="stylepress-grid__item-readmorebutton" href="%1$s">%2$s</a>',
					get_permalink( get_the_ID() ),
					esc_attr( ! empty( $stylepress['meta_readmore_text'] ) ? $stylepress['meta_readmore_text'] : 'Read More »' )
				); ?>
			</div><!--.stylepress-grid__item-readmore-->
		<?php } ?>
	</footer>

</div>

