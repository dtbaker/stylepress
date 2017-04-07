<?php

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


add_action( 'wp_enqueue_scripts', function(){

	wp_enqueue_style( 'stylepress-email', DTBAKER_ELEMENTOR_URI . 'extensions/email-subscribe/subscribe.css', false );
	wp_register_script( 'stylepress-email-script', DTBAKER_ELEMENTOR_URI . 'extensions/email-subscribe/subscribe.js', array('jquery') );
	wp_localize_script( 'stylepress-email-script', 'stylepress_email', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'stylepress-email-script' );
} );




// todo: option these out in 'Add-Ons' section
require_once DTBAKER_ELEMENTOR_PATH . 'extensions/email-subscribe/widget.email-subscribe.php';

add_action( 'wp_ajax_stylepress_email_sub', function(){

	$return = array();

	$post = isset($_POST['post']) ? (int)$_POST['post'] : false;
	$elm = isset($_POST['elm']) ? $_POST['elm'] : false;
	$email = isset($_POST['email']) ? strtolower($_POST['email']) : '';
	if($post && $elm){

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			wp_send_json_error( "Invalid Email. Please try again." );
		}
		// find this elementor post and it widget settings.
		$data = @json_decode( get_post_meta( $post, '_elementor_data', true), true );
		if($data) {
			function stylepress_email_find( $data, $findkey ) {
				if(is_array($data)){
					foreach($data as $d){
						if ( $d && ! empty( $d['id'] ) && $d['id'] === $findkey ) {
							return $d;
						}
						if ( $d && ! empty( $d['elements'] ) && is_array($d['elements']) ) {
							$value = stylepress_email_find( $d['elements'], $findkey );
							if($value){
								return $value;
							}
						}
					}
				}
				return false;
			}
			$element = stylepress_email_find( $data, $elm );
			if($element && !empty($element['settings']['mailchimp_api_key']) && !empty($element['settings']['mailchimp_list_id'])){

				// shoot this off to mailchimp via api
				$status = 'pending';

				$args = array(
					'method' => 'PUT',
					'headers' => array(
						'Authorization' => 'Basic ' . base64_encode( 'user:'. $element['settings']['mailchimp_api_key'] )
					),
					'body' => json_encode(array(
						'email_address' => $email,
						'status'        => $status
					))
				);
				$response = wp_remote_post( 'https://' . substr($element['settings']['mailchimp_api_key'],strpos($element['settings']['mailchimp_api_key'],'-')+1) . '.api.mailchimp.com/3.0/lists/' . $element['settings']['mailchimp_list_id'] . '/members/' . md5(strtolower($email)), $args );

				$body = json_decode( $response['body'] );

				if ( $response['response']['code'] == 200 && $body->status == $status ) {
					wp_send_json_success( !empty($element['settings']['thank_you'] ) ? $element['settings']['thank_you'] : 'Subscribed. Please check your email.' );
				} else {
					wp_send_json_error( $body->title.": ".$body->detail );
				}
			}else{
				wp_send_json_error( "Missing Mailchimp API Details" );
			}
		}else{
			wp_send_json_error( "Missing Elementor Widget" );
		}


	}else{

		wp_send_json_error("Invalid Request");
	}

	exit;

} );