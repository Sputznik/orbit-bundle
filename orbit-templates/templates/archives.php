<?php get_header();?>
<?php 
	global $orbit_templates;
	echo $orbit_templates->print_template( $orbit_templates->get_current_post_template_id( 'archives' ) );
?>
<?php get_footer();?>

