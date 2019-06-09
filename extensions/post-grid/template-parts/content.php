<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


if ( ! function_exists( 'stylepress_post_grid_thumbnail' ) ) {
	function stylepress_post_grid_thumbnail( $settings ) {
		if ( has_post_thumbnail() ) :
			?>
			<div class="stylepress-grid__item-thumb-wrap">
				<?php
				if ( ! empty( $settings['image_style'] ) && $settings['image_style'] === 'category-over' ) {
					$categories_list = strip_tags( get_the_category_list( esc_html__( ', ', 'stylepress' ) ) );
					if ( $categories_list ) {
						?>
						<div class="stylepress-grid__item-thumb-overlay">
							<?php printf( esc_html__( ' %1$s ', 'stylepress' ), $categories_list ); // WPCS: XSS OK. ?>
						</div>
						<?php
					}
				}
				?>
				<div class="stylepress-grid__item-thumb">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php
						the_post_thumbnail( $settings['image_size'] === 'custom' ? [
							0 => $settings['image_custom_dimension']['width'],
							1 => $settings['image_custom_dimension']['height']
						] : $settings['image_size'], array(
								'class' => 'stylepress-grid__item-image',
								'alt'   => get_the_title( get_post_thumbnail_id() )
							)
						);
						?>
					</a>
				</div>
			</div>
		<?php endif;
	}
}
if ( ! function_exists( 'stylepress_post_grid_title' ) ) {
	function stylepress_post_grid_title( $settings ) {
		the_title( '<h2 class="stylepress-grid__item-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
	}
}
if ( ! function_exists( 'stylepress_post_grid_decoration' ) ) {
	function stylepress_post_grid_decoration( $settings ) {
		if ( ! empty( $settings['decoration_image'] ) && ! empty( $settings['decoration_image']['url'] ) ) {
			?>
			<div class="stylepress-grid__item-decoration">
				<?php echo '<img src="' . esc_url( $settings['decoration_image']['url'] ) . '" class="stylepress-grid__item-decoration-image" aria-hidden="true">' ?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'stylepress_post_grid_excerpt' ) ) {
	function stylepress_post_grid_excerpt( $settings ) {
		?>
		<div class="stylepress-grid__item-excerpt">
			<?php the_excerpt(); ?>
		</div><!--.stylepress-grid__item-excerpt-->
		<?php
	}
}
if ( ! function_exists( 'stylepress_post_grid_meta' ) ) {
	function stylepress_post_grid_meta( $stylepress, $settings ) {
		$meta_sections = [];
		if ( ! empty( $settings['meta_show_date'] ) ) {

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

		if ( ! empty( $settings['meta_show_comments'] ) ) {
			if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
				ob_start();
				/* translators: %s: post title */
				comments_popup_link( get_comments_number() > 0 ? sprintf( _x( 'One Comment', '%1$s Comments', get_comments_number(), 'stylepress' ), number_format_i18n( get_comments_number() ) ) : esc_html__( 'No Comments', 'stylepress' ) );
				$meta_sections['comments-link'] = ob_get_clean();
			}
		}

		if ( ! empty( $settings['meta_show_author'] ) ) {
			$byline                  = sprintf(
				esc_html_x( '%s', 'post author', 'stylepress' ),
				'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
			);
			$meta_sections['author'] = $byline;
		}

		if ( ! empty( $settings['meta_show_category'] ) ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'stylepress' ) );
			if ( $categories_list ) {
				$meta_sections['category'] = sprintf( esc_html__( ' %1$s ', 'stylepress' ), $categories_list ); // WPCS: XSS OK.
			}
		}

		if ( ! empty( $settings['meta_show_tags'] ) ) {
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
		<?php }
	}
}
if ( ! function_exists( 'stylepress_post_grid_read_more' ) ) {
	function stylepress_post_grid_read_more( $settings ) {
		?>
		<div class="stylepress-grid__item-readmore">
			<a href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" role="button"
			   class="elementor-button stylepress-grid__item-readmorebutton elementor-size-<?php echo esc_attr( $settings['read_more_size'] ); ?> elementor-animation-<?php echo esc_attr( $settings['read_more_hover_animation'] ); ?>">
					<span class="elementor-button-content-wrapper">
						<?php if ( ! empty( $settings['read_more_icon'] ) ) : ?>
							<span
								class="elementor-button-icon elementor-align-icon-<?php echo $settings['read_more_icon_align']; ?>">
							<i class="<?php echo esc_attr( $settings['read_more_icon'] ); ?>" aria-hidden="true"></i>
						</span>
						<?php endif; ?>
						<span
							class="elementor-button-text"><?php echo esc_attr( ! empty( $settings['read_more_text'] ) ? $settings['read_more_text'] : 'Read More »' ); ?></span>
					</span>

			</a>
		</div>
		<?php
	}
}

?>

<div class="stylepress-grid__item <?php echo ! has_post_thumbnail() ? 'stylepress-grid__item--nothumb' : ''; ?>">

	<?php
	if ( ! empty( $stylepress['meta_components'] ) ) {
		foreach ( $stylepress['meta_components'] as $meta_component ) {
			$component_name = ! empty( $meta_component['meta_component'] ) ? $meta_component['meta_component'] : '';
			if ( function_exists( 'stylepress_post_grid_' . $meta_component['meta_component'] ) ) {
				call_user_func( 'stylepress_post_grid_' . $meta_component['meta_component'], $stylepress, $meta_component );
			}
		}
	}
	?>

</div>

