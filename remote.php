<?php

// Top level list of styles.
add_filter('stylepress_remote_styles',function(){

	$styles = [];
	$styles['dashkids'] = [
		'title' => 'DashKids',
		'thumbnail_url' => '',
	];

	return $styles;
});

add_filter('stylepress_remote_style',function($style, $style_id){

	$style = [];
	$style['title'] = 'DashKids';
	$style['thumbnail_url'] = 'DashKids';

	return $style;
}, 10, 2);