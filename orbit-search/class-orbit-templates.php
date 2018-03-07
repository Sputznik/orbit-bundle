<?php

class ORBIT_TEMPLATES{
	
	var $templates;
		
	function __construct(){
		
		$this->templates = array();
		
		/* ADD FORMS THROUGH THE BACKEND */
		add_filter( 'orbit_post_type_vars', array( $this, 'create_post_type' ) );
		
		/* ADD THE RELEVANT META BOXES TO THE FORM */
		add_filter( 'orbit_meta_box_vars', array( $this, 'create_meta_box' ) );
		
		/* OVERRIDE THE TEMPLATE FROM ORBIT QUERY */
		add_filter( 'orbit_query_template_articles-db', function( $template_url ){
			return plugin_dir_path(__FILE__)."templates/articles-db.php";
		} );
		
		/* ADD TO THE ORBIT QUERY ATTS */
		add_filter( 'orbit_query_atts', function( $atts ){
			
			$atts['style_id'] = 0;  
			
			return $atts;
			
		});
		
		/* OVERRIDE SINGLE TEMPLATE FOR ORBIT-TYPES */
		add_filter( 'single_template', function( $single_template ){
			
			if( $this->get_current_post_template_id() ){
				$single_template = plugin_dir_path(__FILE__)."templates/single.php";
			}
			
			return $single_template;
			
		} );
		
		
		/* ADD TEMPLATES TO THE CUSTOM FIELDS */
		add_filter( 'orbit_custom_field_orbit-tmpl_options', function( $options ){
				
			global $orbit_templates;
				
			$templates = $orbit_templates->get_templates_list();
			$options[ '0' ] = 'Default';	
			foreach( $templates as $template ){
				$options[ $template->ID ] = $template->post_title;
			}
				
			return $options;
		} );
		
		/* ADD TEMPLATES CUSTOM FIELDS AS OPTION TO ORBIT TYPES */
		add_filter( 'orbit_meta_box_vars', array( $this, 'meta_box_fields' ) );
		
		add_filter( 'orbit_post_type_meta_fields_appended', function( $fields ){
			
			array_push( $fields, 'orbit-tmpl' );
			
			array_push( $fields, 'override_content' );
			array_push( $fields, 'override_excerpt' );
			
			return $fields;
		});
		
		/* OVERRIDE EXCERPT FOR ORBIT POST TYPES */
		add_filter( 'the_excerpt', array( $this, 'override_excerpt' ) );
		add_filter( 'get_the_excerpt', array( $this, 'override_excerpt' ) );
		
		/* OVERRIDE CONTENT FOR ORBIT POST TYPES */
		add_filter( 'the_content', array( $this, 'override_content' ) );
		add_filter( 'get_the_content', array( $this, 'override_content' ) );
			
		
	}
	
	
		
	function override_post_text( $text, $meta_field ){
		global $orbit_vars, $post;
		
		/* GET POST TYPE OF THE POST */
		$post_type = $post->post_type;
			
		/* CHECK IF THE POST TYPE IS ONE OF THE CUSTOM ORBIT TYPES */
		if( isset( $orbit_vars['post_types'] ) ){
				
			foreach( $orbit_vars['post_types'] as $new_post_type ){
					
				if( isset( $new_post_type['slug'] ) && $post_type == $new_post_type['slug'] && isset( $new_post_type[ $meta_field ] ) && $new_post_type[ $meta_field ] ){
						
					$text = do_shortcode( $new_post_type[ $meta_field ] );
						
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
	
	function meta_box_fields( $meta_box ){
		
		/* CUSTOM FIELDS FOR ORBIT-TYPES */
		if( isset( $meta_box['orbit-types'] ) && count( $meta_box['orbit-types'] ) && isset( $meta_box['orbit-types'][0]['fields'] ) ){
				
			/* ADD TEMPLATES CUSTOM FIELDS AS OPTION TO ORBIT TYPES */
			$meta_box['orbit-types'][0]['fields']['orbit-tmpl'] = array(
					'type'		=> 'dropdown',
					'text'		=> 'Choose Orbit Template To Override Single Template',
					'help'		=> 'To override the default template.',
					'options'	=> array()
				);
				
			/* OVERRIDE CONTENT */
			$meta_box['orbit-types'][0]['fields']['override_content'] = array(
					'type'		=> 'textarea',
					'text'		=> 'Override Content',
					'help'		=> 'To override the default post content.',
				);
				
			/* OVERRIDE EXCERPT */	
			$meta_box['orbit-types'][0]['fields']['override_excerpt'] = array(
					'type'		=> 'textarea',
					'text'		=> 'Override Excerpt',
					'help'		=> 'To override the default post excerpt.',
				);
		}
		
		/* CUSTOM FIELDS FOR ORBIT-FORM */
		if( isset( $meta_box['orbit-form'] ) && count( $meta_box['orbit-form'] ) && isset( $meta_box['orbit-form'][0]['fields'] ) ){
			/* ADD TEMPLATES CUSTOM FIELDS AS OPTION TO ORBIT TYPES */
			$meta_box['orbit-form'][0]['fields']['orbit-tmpl'] = array(
					'type'		=> 'dropdown',
					'text'		=> 'Choose Orbit Template',
					'help'		=> 'To override the default template.',
					'options'	=> array()
				);
		}
		return $meta_box;
		
	}
	
	/* GET THE TEMPLATE ID OF THE CURRENT POST */
	function get_current_post_template_id(){
		global $post, $orbit_vars;
			
		if( isset( $orbit_vars['post_types'] ) && isset( $orbit_vars['post_types'][ $post->post_type ] ) && isset( $orbit_vars['post_types'][ $post->post_type ]['orbit-tmpl'])  ){
			return $orbit_vars['post_types'][ $post->post_type ]['orbit-tmpl'];
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
			'supports'	=> array( 'title', 'editor' ),
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
		);
			
		return $meta_box;
	}
	
	function get_template( $post_id ){
		
		/* CHECK IF THE TEMPLATE IS IN CACHE */
		if( isset( $this->templates[ $post_id ] ) ){
			return do_shortcode( $this->templates[ $post_id ] );
		}
		
		/* CHECK IF TEMPLATE IS IN THE DB */
		$post = get_post( $post_id );
		if( isset( $post->post_content ) ){
			/* SET THE TEMPLATE IN CACHE */
			$this->templates[ $post_id ] = $post->post_content;
			return do_shortcode( $this->templates[ $post_id ] );
		}
		
		/* BY DEFAULT RETURN NOTHING */
		return '';
		
	}
	
	function print_template( $post_id ){
		
		echo $this->get_template( $post_id );
		
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