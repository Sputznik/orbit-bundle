<?php
/*
Plugin Name: Orbit Query
Plugin URI: http://wordpress.org/plugins/orbit-query/
Description: The simplest way to build and display WordPress Posts without writing a single line of code. Includes multiple customizable templates that allows to match the look and feel of the website.
Author: Samuel Thomas
Version: 1.0
Author URI: http://sputznik.com/
*/

$inc_files = array(
	'class-orbit-query-base.php',
	'class-orbit-query.php',
	'class-orbit-query-users.php',
	'class-orbit-related-query.php',
	'class-orbit-coauthors-query.php',
	'the.php',
);

foreach( $inc_files as $inc_file ){
	include( $inc_file );
}
