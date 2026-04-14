<?php
$short_code_files = [
	'hero-slider',
	'file-download',
	'contact-details',
	'show-more-info',
	'next-post',
	'share-post',
	'post-listing',
	'description',
	'authors',
	'page-information',
	'slideshow',
	'collection',
	'page-popup',
	'deep-dive',
	'custom-post-title',
	'jump-navigation-bar',
	'embedded-video',
	'custom-cookie-banner',
];

foreach( $short_code_files as $file ) :
	require get_stylesheet_directory() . '/short-codes/' . $file . '.php';
endforeach;
