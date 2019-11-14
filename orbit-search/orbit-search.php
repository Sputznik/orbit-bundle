<?php

	$inc_files = array(
		"vars.php",
		"class-orbit-post-type.php",
		"class-orbit-custom-field.php",
		"class-orbit-filter.php",
		"class-orbit-search.php",
    "class-orbit-export.php",
		"class-orbit-search-metabox.php"
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
