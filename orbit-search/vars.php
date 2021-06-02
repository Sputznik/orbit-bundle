<?php

	global $orbit_vars;

	if( ! is_array( $orbit_vars ) ){
		$orbit_vars = array();
	}

	/* PUSH INTO THE GLOBAL VARS OF ORBIT TYPES */
	add_filter( 'orbit_post_type_vars', function( $post_types ){

		$post_types['orbit-types'] = array(
			'slug' 	=> 'orbit-types',
			'labels'	=> array(
				'name' 			=> 'Orbit Types',
				'singular_name' => 'Orbit Type',
			),
			'public'	=> false,
			'supports'	=> array('title')
		);

		return $post_types;
	} );

	/* PUSH INTO THE GLOBAL VARS OF ORBIT TAXNOMIES */
	add_filter( 'orbit_taxonomy_vars', function( $taxonomies ){

		$taxonomies['orbit-taxonomy']	= array(
			'label'			=> 'Orbit Taxonomy',
			'slug' 			=> 'orbit_taxonomy',
			'post_types'	=> array( 'orbit-types' )
		);

		return $taxonomies;
	} );


	/* ADD THE RELEVANT META BOXES TO THE FORM */
	add_filter( 'orbit_meta_box_vars', function( $meta_box ){
		global $post_type;

		if( 'orbit-types' != $post_type ) return $meta_box;

		$meta_box['orbit-types'] = array(
			array(
				'id'		=> 'orbit-types-settings',
				'title'		=> 'Settings',
				'fields'	=> array(
					'singular_name' => array(
						'type' => 'text',
						'text' => 'Singular Name - Name for one object of this post type'
					),
					'menu_icon' => array(
						'type' 		=> 'dropdown',
						'text' 		=> 'Menu Icon',
						'options'	=> array(
							'dashicons-format-aside'	=> 'Aside',
							'dashicons-format-image'	=> 'Image',
							'dashicons-format-gallery'	=> 'Gallery',
							'dashicons-format-video'	=> 'Video',
							'dashicons-format-status'	=> 'Status',
							'dashicons-format-quote'	=> 'Quote',
							'dashicons-groups'			=> 'User Groups'
						)
					),
					'supports' => array(
						'type' 		=> 'checkbox',
						'text' 		=> 'Supports',
						'options'	=> array(
							'title'			=> 'Title',
							'editor'		=> 'Editor',
							'author'		=> 'Author',
							'thumbnail'		=> 'Featured Image',
							'excerpt'		=> 'Excerpt',
							'comments'		=> 'Comments',
							'custom_fields'	=> 'Custom Fields',
						)
					),
					'show_in_rest' => array(
						'type' 		=> 'checkbox',
						'text' 		=> 'Rest API Support',
						'options'	=> array(
							'show_post'			=> 'Enable rest api for post',
							'show_taxonomy'	=> 'Enable rest api for taxonomies',
						)
					),
				)
			),
		);

		return $meta_box;
	} );
