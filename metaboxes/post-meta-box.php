<?php
/**
 * Metabox showing on all pages and posts.
 *
 * @package dtbaker-elementor
 */

// grab a list of all page templates.
$styles          = $this->get_all_page_styles();
$components = $this->get_all_page_components();
$current_default = $this->get_current_style(true);

$current_outer_style = $this->get_page_template($post->ID);
$current_inner_style = $this->get_page_inner_style($post->ID);

$current_page_type = get_post_type($post); //$this->get_current_page_type();
$style_settings = $this->get_settings();

wp_nonce_field( 'dtbaker_elementor_style_nonce', 'dtbaker_elementor_style_nonce' );
?>
<label class="screen-reader-text" for="dtbaker_page_style"><?php esc_html_e( 'Page Style', 'stylepress' ); ?></label>
<p>
	<small><?php
		// Translators: The first %s is a link <a href=""> and the second %s is a closing link </a>.
		printf( esc_html__( 'You can override the default style here. Choose the style to apply to this particular page. Edit these styles from the %1$sStylePress%2$s page.', 'stylepress' ), '<a href="' . esc_url( admin_url( 'admin.php?page=dtbaker-stylepress' ) ) . '">', '</a>' ); ?></small>
</p>
<p>
    <small><?php
    // Translators: The %s is the current post type
	    printf( esc_html__( 'This page type is: %s', 'stylepress' ), ucwords( str_replace('_',' ',$current_page_type) )); ?>
        </small>
</p>

<?php if($styles){ ?>
<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="dtbaker_page_style"><?php _e('Outer Style');?></label></p>
<select name="dtbaker_style[style]" id="dtbaker_page_style">
	<option value="0"><?php
		// Translators: %s contains the current default style.
		printf( esc_html__( 'Default %s', 'stylepress' ), esc_attr( isset($styles[ $current_default ]) ? '(' . $styles[ $current_default ] . ')' : '' ) ); ?></option>
    <option value="-1"<?php echo $current_outer_style && (int) $current_outer_style === (int) -1 ? ' selected' : ''; ?>><?php esc_html_e('Original Theme Output', 'stylepress')?></option>
	<?php foreach ( $styles as $option_id => $option_val ) {
		?>
		<option value="<?php echo esc_attr( $option_id ); ?>"<?php echo $current_outer_style && (int) $current_outer_style === (int) $option_id ? ' selected' : ''; ?>><?php echo esc_attr( $option_val ); ?></option>
		<?php
	}
	?>
</select>
<?php }


if($components){

	$component_template = $current_page_type . '_inner';
    // loading this component/
    $default_inner_style = false;
    if(!empty($style_settings['defaults'][$component_template])){
	    $default_inner_style = (int) $style_settings['defaults'][$component_template];
    }else {
        // we use the global inner settings.
        if ( ! empty( $style_settings['defaults']['_global_inner'] ) ) {
	        $default_inner_style = (int) $style_settings['defaults']['_global_inner'];
        }
    }

    ?>

<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="dtbaker_page_inner_style"><?php _e('Inner Style');?></label></p>
<select name="dtbaker_style[inner_style]" id="dtbaker_page_inner_style">
	<option value="0"><?php
		// Translators: %s contains the current default style.
		printf( esc_html__( 'Default %s', 'stylepress' ), esc_attr( $default_inner_style && isset($components[ $default_inner_style ]) ? '(' . $components[ $default_inner_style ] . ')' : '' ) ); ?></option>
    <option value="<?php echo STYLEPRESS_INNER_USE_PLAIN;?>"<?php echo $current_inner_style && (int) $current_inner_style === (int) STYLEPRESS_INNER_USE_PLAIN ? ' selected' : ''; ?>><?php esc_html_e('Plain Output', 'stylepress')?></option>
    <?php  if($this->supports( 'theme-inner' )) { ?>
        <option value="<?php echo STYLEPRESS_INNER_USE_THEME; ?>"<?php echo $current_inner_style && (int) $current_inner_style === (int) STYLEPRESS_INNER_USE_THEME ? ' selected' : ''; ?>><?php esc_html_e( 'Use Theme Default Inner Output', 'stylepress' ) ?></option>

	    <?php
    }foreach ( $components as $option_id => $option_val ) {
		?>
		<option value="<?php echo esc_attr( $option_id ); ?>"<?php echo $current_inner_style && (int) $current_inner_style === (int) $option_id ? ' selected' : ''; ?>><?php echo esc_attr( $option_val ); ?></option>
		<?php
	}
	?>
</select>

<?php } ?>