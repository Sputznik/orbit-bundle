<?php get_header();?>
<?php
	global $orbit_templates;
	echo $orbit_templates->print_template( $orbit_templates->get_current_author_template_id() );
?>
<?php get_footer();?>
