<?php
	
function the_oq_articles( $atts ){
	global $orbit_query;
	$orbit_query->include_template_file( 'articles', $atts );
}

function the_oq_pagination( $atts ){
	global $orbit_query;
	$orbit_query->include_template_file( 'pagination', $atts );
}

function the_oq_users( $atts ){
	global $posts_query_users;
	$posts_query_users->include_template_file( 'users', $atts );
}