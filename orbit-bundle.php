<?php
	/*
    Plugin Name: Orbit Bundle
    Plugin URI: http://sputznik.com
    Description: Create Wordpress Custom post types and custom taxonomies. Search and filter through the wordpress post types and create custom queries using simple shortcodes.
    Author: Samuel Thomas
    Version: 1.0
    Author URI: http://sputznik.com
    */

	define( 'ORBIT_BUNDLE_VERSION', '1.3.8' );

	wp_register_style( 'orbit-main', plugins_url( 'orbit-bundle/dist/css/main.css' ), array(), ORBIT_BUNDLE_VERSION );

	$inc_files = array(
		"lib/class-orbit-base.php",
		"lib/class-orbit-shortcode.php",
		"lib/class-orbit-batch-process.php",
		"lib/class-orbit-csv.php",
		"lib/class-orbit-translations.php",
		"lib/class-orbit-util.php",
		"lib/class-orbit-wp.php",
		"lib/class-orbit-multipart-form.php",
		"lib/class-orbit-form-field.php",
		"orbit-search/orbit-search.php",
		"orbit-query/orbit-query.php",
		"orbit-templates/orbit-templates.php",
		"orbit-cache/orbit-cache.php",
		"admin/class-orbit-admin.php"
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}

	/*
	// GUTENBERG BLOCK
	add_action( 'init', function(){

		wp_register_script(
			'orbit-query-block',
			plugins_url( 'dist/js/orbit-query-block.js', __FILE__ ),
			array( 'wp-blocks', 'wp-element' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'dist/js/orbit-query-block.js' )
		);

		wp_register_style(
			'orbit-blocks',
			plugins_url( 'dist/css/orbit-query.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'dist/css/orbit-query.css' )
		);

		// LOCALIZE ORBIT SETTINGS
		$orbit_settings = array( 'post_types' => array(), 'styles' => array(), 'taxonomies' => array() );

		// POST TYPES
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		foreach( $post_types as $post_type ){
			array_push( $orbit_settings['post_types'], array( 'label' => $post_type->label, 'value' => $post_type->name ) );
		}

		// ORBIT TEMPLATES
		global $orbit_templates;
		$templates = $orbit_templates->get_templates_list();
		foreach( $templates as $template ){
			array_push( $orbit_settings['styles'], array( 'label' => $template->post_title, 'value' => $template->ID ) );
		}

		// TAXONOMIES
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		array_push( $orbit_settings['taxonomies'], array( 'label' => 'Select None', 'value' => '' ) );
		foreach( $taxonomies as $taxonomy ){
			array_push( $orbit_settings['taxonomies'], array( 'label' => $taxonomy->label, 'value' => $taxonomy->name ) );
		}

		$orbit_settings['orbit_query_atts'] = array(
			'post_type'	=> array(
				'type' 	=> 'string',
			),
			'posts_per_page' => array(
				'type' 	=> 'number',
			),
			'style_id' 	=> array(
				'type' 	=> 'number',
			),
			'style'	=> array(
				'type' 	=> 'string',
				'default'	=> 'db'
			),
			'taxonomy'	=> array(
				'type' 	=> 'string',
			),
			'term'	=> array(
				'type' 	=> 'string',
			),
			'tax_query'	=> array(
				'type' 	=> 'string',
				//'default'	=> 'category:uncategorized'
			),
		);

		wp_localize_script( 'orbit-query-block', 'orbit_settings', $orbit_settings );
		// END OF LOCALIZE ORBIT SETTINGS


		if( function_exists('register_block_type') ){

			global $orbit_query;

			register_block_type( 'orbit-bundle/orbit-query', array(
				'editor_script' 	=> 'orbit-query-block',
				'editor_style'		=> 'orbit-blocks',
				'attributes' 		=> $orbit_settings['orbit_query_atts'],
				'render_callback'	=> array( $orbit_query, 'plain_shortcode' )
			) );
		}
	} );


	// ENQUEUE ASSETS
	add_action( 'wp_enqueue_scripts', function(){

		wp_enqueue_style( 'orbit-blocks' );

	} );
	*/

	do_action('orbit-bundle-loaded');
