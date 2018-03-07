<?php 
    /*
    Plugin Name: Orbit Search & Filter
    Plugin URI: http://sputznik.com
    Description: Plugin for filtering custom fields from custom post type. 
    Author: Samuel Thomas
    Version: 1.0
    Author URI: http://sputznik.com
    */
	
	$inc_files = array(
		"vars.php",
		"class-orbit-post-type.php",
		"class-orbit-custom-field.php",
		"class-orbit-filter.php",
		"class-orbit-search.php",
		"class-orbit-templates.php",
		"class-orbit-admin.php",
		"the.php",
		"orbit-shortcodes.php"
	);
	
	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
	
	
