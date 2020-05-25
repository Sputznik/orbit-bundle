<?php

class ORBIT_TEMPLATES{

	var $templates;
	var $posts;

	function __construct(){

		$this->templates = array();
		$this->posts = array();

		/* ADD FORMS THROUGH THE BACKEND */
		add_filter( 'orbit_post_type_vars', array( $this, 'create_post_type' ) );

		/* ADD THE RELEVANT META BOXES TO THE FORM */
		add_filter( 'orbit_meta_box_vars', array( $this, 'create_meta_box' ) );

		/* OVERRIDE THE TEMPLATE FROM ORBIT QUERY */
		add_filter( 'orbit_query_template_articles-db', function( $template_url ){
			return plugin_dir_path(__FILE__)."templates/articles-db.php";
		} );

		/* OVERRIDE THE TEMPLATE FROM ORBIT USERS QUERY */
		add_filter( 'orbit_query_template_users-db', function( $template_url ){
			return plugin_dir_path(__FILE__)."templates/users-db.php";
		} );

		/* ADD TO THE ORBIT QUERY ATTS */
		add_filter( 'orbit_query_atts', function( $atts ){
			$atts['style_id'] = 0;
			return $atts;
		});

		/* ADD TO THE ORBIT USER QUERY ATTS */
		add_filter( 'orbit_query_users_atts', function( $atts ){
			$atts['style_id'] = 0;
			return $atts;
		});


		/* OVERRIDE SINGLE TEMPLATE FOR POST-TYPES */
		add_filter( 'single_template', function( $single_template ){

			if( $this->get_current_post_template_id( 'single' ) ){
				$single_template = plugin_dir_path(__FILE__)."templates/single.php";
			}

			return $single_template;

		} );

		/* OVERRIDE ARCHIVE TEMPLATE FOR POST-TYPES */
		add_filter( 'archive_template', function( $archive_template ){

			if( $this->get_current_post_template_id( 'archives' ) ){
				$archive_template = plugin_dir_path(__FILE__)."templates/archives.php";
			}

			return $archive_template;

		} );

		/* OVERRIDE ARCHIVE TEMPLATE FOR POST-TYPES */
		add_filter( 'author_template', function( $author_template ){

			$author_template = plugin_dir_path(__FILE__)."templates/author.php";

			return $author_template;

		} );


		/* ADD TEMPLATES CUSTOM FIELDS AS OPTION TO ORBIT TYPES */
		add_filter( 'orbit_meta_box_vars', array( $this, 'meta_box_fields' ) );

		add_filter( 'orbit_post_type_meta_fields_appended', function( $fields ){

			array_push( $fields, 'orbit-tmpl' );

			//array_push( $fields, 'override_content' );
			//array_push( $fields, 'override_excerpt' );

			return $fields;
		});

		/* OVERRIDE EXCERPT FOR ORBIT POST TYPES
		add_filter( 'the_excerpt', array( $this, 'override_excerpt' ) );
		add_filter( 'get_the_excerpt', array( $this, 'override_excerpt' ) );

		/* OVERRIDE CONTENT FOR ORBIT POST TYPES
		add_filter( 'the_content', array( $this, 'override_content' ) );
		add_filter( 'get_the_content', array( $this, 'override_content' ) );

		/* ADD ADMIN SCREENS TO ORBIT SETTINGS PAGE */
		add_filter( 'orbit_admin_settings_screens', function( $screens ){

			$screens['override'] = array(
				'label'		=> 'Override Templates',
				'action'	=> 'override',
				'tab'		=> plugin_dir_path(__FILE__).'admin-templates/settings-override.php'
			);

			return $screens;
		});

	}


	/*
	function override_post_text( $text, $meta_field ){
		global $orbit_vars, $post;

		if( $post && isset( $post->post_type ) ){

			// GET POST TYPE OF THE POST
			$post_type = $post->post_type;

			// CHECK IF THE POST TYPE IS ONE OF THE CUSTOM ORBIT TYPES
			if( isset( $orbit_vars['post_types'] ) ){

				foreach( $orbit_vars['post_types'] as $new_post_type ){

					if( isset( $new_post_type['slug'] ) && $post_type == $new_post_type['slug'] && isset( $new_post_type[ $meta_field ] ) && $new_post_type[ $meta_field ] ){

						$text = do_shortcode( $new_post_type[ $meta_field ] );

					}

				}
			}

		}

		return $text;
	}

	function override_excerpt( $excerpt ){
		return $this->override_post_text( $excerpt, 'override_excerpt' );
	}

	function override_content( $excerpt ){
		return $this->override_post_text( $excerpt, 'override_content' );
	}
	*/

	function meta_box_fields( $meta_box ){

		/* CUSTOM FIELDS FOR ORBIT-TYPES */
		if( isset( $meta_box['orbit-types'] ) && count( $meta_box['orbit-types'] ) && isset( $meta_box['orbit-types'][0]['fields'] ) ){

			/* OVERRIDE CONTENT
			$meta_box['orbit-types'][0]['fields']['override_content'] = array(
					'type'		=> 'textarea',
					'text'		=> 'Override Content',
					'help'		=> 'To override the default post content.',
				);

			/* OVERRIDE EXCERPT
			$meta_box['orbit-types'][0]['fields']['override_excerpt'] = array(
					'type'		=> 'textarea',
					'text'		=> 'Override Excerpt',
					'help'		=> 'To override the default post excerpt.',
				);
			*/
		}

		/* CUSTOM FIELDS FOR ORBIT-FORM */
		if( isset( $meta_box['orbit-form'] ) && count( $meta_box['orbit-form'] ) && isset( $meta_box['orbit-form'][0]['fields'] ) ){

			$templates = $this->get_templates_list();
			$options_templates = array(
				'0'	=> 'Default'
			);

			foreach ($templates as $template ) {
				$options_templates[ $template->ID ] = $template->post_title;
			}

			/* OVERRIDE CONTENT */
			$meta_box['orbit-form'][0]['fields']['orbit-tmpl'] = array(
					'type'		=> 'dropdown',
					'text'		=> 'Choose Template',
					'help'		=> '',
					'options'	=> $options_templates
				);


		}

		return $meta_box;

	}

	function update_override_options( $data ){
		update_option( 'orbit_override_templates', $data );
	}

	function get_override_options(){
		return get_option( 'orbit_override_templates' );
	}

	/* GET THE TEMPLATE ID OF THE CURRENT POST */
	function get_current_post_template_id( $template_type ){

		/*
		* TEMPLATE TYPE CAN BE EITHER SINGLE OR ARCHIVE
		*/

		global $post, $orbit_vars;

		$data = $this->get_override_options();

		if( $post && isset( $post->post_type ) ){

			if( is_array( $data ) && isset( $data[$post->post_type] ) && isset( $data[$post->post_type][$template_type] ) ){
				return $data[$post->post_type][$template_type];
			}

		}


		return 0;

	}

	/* GET THE TEMPLATE ID OF THE AUTHOR TEMPLATE */
	function get_current_author_template_id(){
		$data = $this->get_override_options();
		if( is_array( $data ) && isset( $data['author'] ) ){
			return $data['author'];
		}
		return 0;
	}

	function create_post_type( $post_types ){

		$post_types['orbit-tmp'] = array(
			'slug' 		=> 'orbit-tmp',
			'labels'	=> array(
				'name' 			=> 'Orbit Templates',
				'singular_name' => 'Orbit Template',
			),
			'supports'	=> array( 'title', 'editor', 'custom-fields' ),
			'public'	=> false,
			'menu_icon'	=> 'dashicons-media-spreadsheet'
		);

		return $post_types;
	}

	function create_meta_box( $meta_box ){

		global $post_type;

		if( 'orbit-tmp' != $post_type ) return $meta_box;

		$meta_box['orbit-tmp'] = array(
			array(
				'id'		=> 'orbit-tmp-help',
				'title'		=> 'Notes',
				'fields'	=> array(
					'help' => array(
						'type' 		=> 'help',
						'text' 		=> 'HTML',
						'help'		=> 'Shortcodes available: [orbit_title] [orbit_link] [orbit_author_link] [orbit_author] [orbit_date] [orbit_excerpt] [orbit_terms]',
					),
				)
			),
			array(
				'id'		=> 'orbit-tmp-settings',
				'title'		=> 'Template Settings',
				'context'	=> 'side',
				'fields'	=> array(
					'css_class' => array(
						'type' 		=> 'text',
						'text' 		=> 'CSS CLASS',
						'help'		=> 'Custom CSS class/classes for the unordered list that displays the list of posts',
					),
				)
			),
		);

		return $meta_box;
	}

	function get_template( $post_id ){

		/* CHECK IF THE TEMPLATE IS IN CACHE */
		if( isset( $this->templates[ $post_id ] ) ){
			return do_shortcode( $this->templates[ $post_id ] );
		}

		/* CHECK IF TEMPLATE IS IN THE DB */
		$post = $this->get_post( $post_id );
		if( isset( $post->post_content ) ){
			/* SET THE TEMPLATE IN CACHE */
			$this->templates[ $post_id ] = $post->post_content;
			return do_shortcode( $this->templates[ $post_id ] );
		}

		/* BY DEFAULT RETURN NOTHING */
		return '';

	}

	function get_post( $post_id ){

		/* CHECK IF THE POST IS IN CACHE */
		if( isset( $this->posts[ $post_id ] ) ){
			return $this->posts[ $post_id ];
		}

		// SET IN CACHE
		$this->posts[ $post_id ] = get_post( $post_id );

		return $this->posts[ $post_id ];
	}

	/* CSS CLASS TO THE UNLISTED LIST THAT IS HAVING THE LIST OF POSTS */
	function print_template_class( $post_id ){

		$class = '';

		$post = $this->get_post( $post_id );

		// ADDING POST TITLE
		if( isset( $post->post_title ) && $post->post_title ){
			$class = sanitize_title( $post->post_title );
		}

		// ADDING POST ID
		if( $post_id ){
			$class .= ' orbit-list-'.$post_id;
		}

		// ADDING CUSTOM CSS CLASS THROUGH CUSTOM FIELD
		$custom_css_class = get_post_meta( $post_id, 'css_class', true );
		if( $custom_css_class ){
			$class .= ' '.$custom_css_class;
		}

		$class .= ' orbit-list-db ';

		echo $class;
	}

	function print_template( $post_id ){

		echo $this->get_template( $post_id );

	}

	function set_user( $user ){
		global $orbit_user;
		$orbit_user = $user;
	}


	function get_templates_list(){
		return get_posts( array(
				'post_type'		=> 'orbit-tmp',
				'post_status'	=> 'publish',
				'numberposts'	=> 20
			) );
	}

}

global $orbit_templates;
$orbit_templates = new ORBIT_TEMPLATES;
