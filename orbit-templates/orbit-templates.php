<?php
	
	$inc_files = array(
		"class-orbit-templates.php",
		"orbit-shortcodes.php"
	);
	
	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
	