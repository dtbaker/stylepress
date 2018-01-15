<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

// Help tab: Previewing and Customizing.
if ( !$this->has_permission() ) {
    die ('No permissions');
}

$style_id = isset($_GET['style_id']) ? (int)$_GET['style_id'] : 0;
$post = false;
$styles = $components = array();

if($style_id){
	$post = get_post($style_id);
    if(!$post || $post->post_type !== 'dtbaker_style'){
        $style_id = 0;
	    $post = false;
    }
}
if($post && $style_id){
	$args        = array(
		'post_type'           => 'dtbaker_style',
		'post_parent'         => $post->ID,
		'post_status'         => 'any',
		'posts_per_page'      => - 1,
		'ignore_sticky_posts' => 1,
		'order'=> 'ASC',
        'orderby' => 'title'
	);
	$posts_array = get_posts( $args );

	$styles[]         = $post;

	foreach ( $posts_array as $post_array ) {
		if ( get_post_meta( $post_array->ID, 'dtbaker_is_component', true ) ) {
			$components[] = $post_array;
		} else {
			$styles[] = $post_array;
		}
	}
}

?>

<div class="wrap">


	<?php require_once DTBAKER_ELEMENTOR_PATH . 'admin/_header.php'; ?>


	<div class="dtbaker-elementor-browser">

		<div class="wp-clearfix">

			<?php if(!$styles && !$components){ ?>

            <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
                <input type="hidden" name="action" value="dtbaker_elementor_create" />
				<?php wp_nonce_field( 'dtbaker_elementor_create_options', 'dtbaker_elementor_create_options' ); ?>


                <h3 class="stylepress-header">
                    <span><?php _e('Create New Website Style', 'stylepress'); ?></span>
                    <small><?php _e('Choose the name for your new website style. After creating a new style you can start designing the full site layout in Elementor.', 'stylepress'); ?></small>
                </h3>

                <p>
                    <label for="new_style_name"><?php _e('Style Name:', 'stylepress'); ?></label> <input type="text" name="new_style_name" value="">
                </p>

                <p>
                    <input type="submit" name="save" value="<?php _e('Create New', 'stylepress'); ?>" class="button button-primary">
                </p>

            </form>

                <?php }else{ ?>
                <h3 class="stylepress-header">
                    <div class="buttons">
                        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=dtbaker_style&post_parent=' . (int) $post->ID ) ); ?>"
                           class="button button-primary"><?php _e('Create New', 'stylepress'); ?></a>
                    </div>
                    <span><?php _e('Your Outer Styles for', 'stylepress'); ?> "<?php echo $post ? esc_html($post->post_title) : __('Create New', 'stylepress');?>"</span>
                    <small><?php _e('These styles can surround your existing website content. Activate these styles from the', 'stylepress'); ?> <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress-settings'));?>"><?php _e('Settings', 'stylepress'); ?></a> <?php _e('page.', 'stylepress'); ?> <br/> <?php _e('There can be multiple variations for an outer style (e.g. Home Page, Blog Page, Product Page, 404 Page).', 'stylepress'); ?></small>
                </h3>

                <div class="stylepress-item-wrapper">



                    <table class="style-list widefat striped">
                        <thead>
                            <tr>
                                <th><?php _e('Outer Style Name', 'stylepress'); ?></th>
                                <th><?php _e('Applied To', 'stylepress'); ?></th>
                                <th><?php _e('Actions', 'stylepress'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $styles as $style ) {

	                        $used = array();
	                        foreach($page_types as $post_type => $post_type_title){
		                        if($settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type] === (int) $style->ID){
			                        $used[$post_type] = $post_type_title;
		                        }
		                        if($settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type.'_inner'] === (int) $style->ID){
			                        $used[$post_type.'_inner'] = $post_type_title .' Inner';
		                        }
	                        }

                            ?>
                            <tr>
                                <td>
                                    <a class="" href="<?php echo esc_url( \Elementor\Utils::get_edit_link( $style->ID ) ); ?>"><?php echo esc_html( $style->post_title ); ?></a>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress-settings'));?>">
		                                <?php if ( $used ){ ?>
                                            <i class="fa fa-check"></i> <?php _e('Style Applied To:', 'stylepress'); ?> <?php echo implode(', ',$used); ?>.
		                                <?php }else{ ?>
                                            <i class="fa fa-times"></i> <?php _e('Style Not Used.', 'stylepress'); ?>
		                                <?php } ?>
                                    </a>
                                </td>
                                <td>
                                    <a class="button button" href="<?php echo esc_url( get_edit_post_link( $style->ID ) ); ?>"><?php _e('Settings', 'stylepress'); ?></a>
                                    <a class="button button" href="<?php print wp_nonce_url(admin_url('admin.php?action=stylepress_clone&post_id=' . (int)$style->ID), 'stylepress_clone', 'stylepress_clone');?>"><?php esc_html_e( 'Clone', 'stylepress' ); ?></a>
                                    <a class="button button" href="<?php echo esc_url( get_permalink( $style->ID ) );?>"><?php esc_html_e( 'Preview', 'stylepress' ); ?></a>
                                    <a class="button button-primary" href="<?php echo esc_url( \Elementor\Utils::get_edit_link( $style->ID ) ); ?>"><?php esc_html_e( 'Elementor', 'stylepress' ); ?></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <h3 class="stylepress-header">
                    <div class="buttons">
                        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=dtbaker_style&dtbaker_component=1&post_parent=' . (int) $post->ID ) ); ?>"
                           class="button button-primary"><?php _e('Create New', 'stylpress'); ?></a>
                    </div>
                    <span><?php _e('Your Inner Styles for', 'stylepress'); ?> "<?php echo $post ? esc_html($post->post_title) : _e('Create New', 'stylepress');?>"</span>
                    <small><?php _e('These are your inner website styles. Activate these styles from the', 'stylepress'); ?> <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress-settings'));?>"><?php _e('Settings', 'stylepress'); ?></a> <?php _e('page.', 'stylepress'); ?> <br/> <?php _e('These styles can be used to style inner website components (Blog Summary, Shop Product, Sidebars).', 'stylepress'); ?></small>
                </h3>

                <div class="stylepress-item-wrapper">



                    <table class="style-list widefat striped">
                        <thead>
                            <tr>
                                <th><?php _e('Inner Style Name', 'stylepress'); ?></th>
                                <th><?php _e('Applied To', 'stylepress'); ?></th>
                                <th><?php _e('Actions', 'stylepress'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $components as $style ) {

	                        $used = array();
	                        foreach($page_types as $post_type => $post_type_title){
		                        if($settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type] === (int) $style->ID){
			                        $used[$post_type] = $post_type_title;
		                        }
		                        if($settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type.'_inner'] === (int) $style->ID){
			                        $used[$post_type.'_inner'] = $post_type_title .' Inner';
		                        }
	                        }

                            ?>
                            <tr>
                                <td>
                                    <a class="" href="<?php echo esc_url( \Elementor\Utils::get_edit_link( $style->ID ) ); ?>"><?php echo esc_html( $style->post_title ); ?></a>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress-settings'));?>">
		                                <?php if ( $used ){ ?>
                                            <i class="fa fa-check"></i> <?php _e('Style Applied To:', 'stylepress'); ?> <?php echo implode(', ',$used); ?>.
		                                <?php }else{ ?>
                                            <i class="fa fa-times"></i> <?php _e('Style Not Used.', 'stylepress'); ?>
		                                <?php } ?>
                                    </a>
                                </td>
                                <td>
                                    <a class="button button" href="<?php echo esc_url( get_edit_post_link( $style->ID ) ); ?>"><?php _e('Settings', 'stylepress'); ?></a>
                                    <a class="button button" href="<?php print wp_nonce_url(admin_url('admin.php?action=stylepress_clone&post_id=' . (int)$style->ID), 'stylepress_clone', 'stylepress_clone');?>"><?php esc_html_e( 'Clone', 'stylepress' ); ?></a>
                                    <a class="button button" href="<?php echo esc_url( get_permalink( $style->ID ) );?>"><?php esc_html_e( 'Preview', 'stylepress' ); ?></a>
                                    <a class="button button-primary" href="<?php echo esc_url( \Elementor\Utils::get_edit_link( $style->ID ) ); ?>"><?php esc_html_e( 'Elementor', 'stylepress' ); ?></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

			<?php } ?>
		</div>

	</div>

</div>
