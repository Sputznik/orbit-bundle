<?php
	/*
    Plugin Name: Orbit Bundle
    Plugin URI: http://sputznik.com
    Description: Create wordpress custom post types and custom taxonomies. Search and filter through the wordpress post types and create custom queries using simple shortcodes.
    Author: Samuel Thomas
    Version: 1.0
    Author URI: http://sputznik.com
    */
	
	$inc_files = array(
		"orbit-search/orbit-search.php",
		"orbit-query/orbit-query.php",
		"orbit-templates/orbit-templates.php",
	);
	
	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
	