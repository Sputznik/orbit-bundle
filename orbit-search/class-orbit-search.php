<?php

	class ORBIT_SEARCH{
		
		function __construct(){
			
			add_shortcode( 'orbit_search', array( $this, 'form' ) );
			
			/* ADD FORMS THROUGH THE BACKEND */
			add_filter( 'orbit_post_type_vars', array( $this, 'create_post_type' ) );
			
			/* ADD THE RELEVANT META BOXES TO THE FORM */
			add_filter( 'orbit_meta_box_vars', array( $this, 'create_meta_box' ) );
			
			/* ADD REGISTERED POST TYPES TO THE CUSTOM FIELDS */
			add_filter( 'orbit_custom_field_posttypes_options', function( $opt ){
				$post_types = get_post_types( array( 'public' => true ), 'objects' );
				foreach( $post_types as $post_type_item ){
					if( ! ( strpos( $post_type_item->name, 'orbit' ) !== false ) ) {
						$opt[$post_type_item->name] = $post_type_item->label;
					}
				}
				return $opt;
			} );
			
			/* ENQUEUE ASSETS */
			add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
			
		}
		
		/* ENQUEUE STYLESHEETS AND SCRIPTS */
		function assets() {
			
			wp_enqueue_style( 'orbit-search', plugin_dir_url( __FILE__ ).'css/style.css', array(), "1.0.9" );
			
			wp_enqueue_script('typeahead', plugin_dir_url( __FILE__ ).'js/typeahead.min.js', array('jquery'), '1.0.0', true );
			wp_enqueue_script('orbit-search-script', plugin_dir_url( __FILE__ ).'js/main.js', array( 'jquery', 'typeahead' ), '1.0.1', true );
		
		}
	
		function create_meta_box( $meta_box ){
			
			global $post_type;
			
			if( 'orbit-form' != $post_type ) return $meta_box;
			
			$meta_box['orbit-form'] = array(
				array(
					'id'		=> 'orbit-form-cf',
					'title'		=> 'Settings',
					'fields'	=> array(
						'posttypes' => array( 
							'type' 		=> 'checkbox',
							'text' 		=> 'Select Post Types', 
							'options'	=> array()
						),
					)
				),
			);
			return $meta_box;
		}
		
		function create_post_type( $post_types ){
			
			$post_types['orbit-form'] = array(
				'slug' 		=> 'orbit-form',
				'labels'	=> array(
					'name' 			=> 'Orbit Searchforms',
					'singular_name' => 'Orbit Searchform',
				),
				'supports'	=> array('title', 'editor'),
				'public'	=> false,
				'menu_icon'	=> 'dashicons-media-document'
			);
			
			return $post_types;
		}
		
		function get_default_atts(){
			return array(
				'id' 	=> '0',
				'style'		=> 'db'
			);
		}
		
		function form( $atts ){
			
			ob_start();
			
			/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
			$atts = shortcode_atts( $this->get_default_atts(), $atts, 'orbit_search' );
			
			/* GET FORM DETAILS */
			$form = get_post( $atts['id'] );
			
			_e("<div class='orbit-search-container'>");
			
			_e("<div class='orbit-search-form'>");
			_e("<form method='GET'>");
			_e( do_shortcode( $form->post_content ) );
			_e("<p><button type='submit'>Submit</button></p>");
			_e("</form>");
			_e("</div>");
			
			_e("<div class='orbit-search-results'>");
			
			$shortcode_str = "[orbit_query pagination='1' ";
			
			/* TEMPLATE FOR OBJECT QUERY */
			$tmpl_id = get_post_meta( $atts['id'], 'orbit-tmpl', true );
			if( $tmpl_id ){
				$shortcode_str .= "style='".$atts['style']."' style_id='".$tmpl_id."' ";	/* ADD TO THE SHORTCODE AS AN ATTRIBUTE */
			}
			
			/* POST TYPES - FORM */
			$post_types = get_post_meta( $atts['id'], 'posttypes', true );
			if( !$post_types ){ $post_types = array(); } 		/* IF VALUE IS NOT SET */
			$post_types = implode(',', $post_types);			/* CONVERTING ARRAY TO STRING */
			$shortcode_str .= "post_type='".$post_types."' ";	/* ADD TO THE SHORTCODE AS AN ATTRIBUTE */
			
			$tax_query_str = '';
			
			
			
			if( is_array ( $_GET ) && count( $_GET > 1 ) ){
				
				$tax_params = array();
				
				/* USER VALUES FROM GET PARAMETERS */
				foreach( $_GET as $slug => $value ){
					
					/* DIFFERENTIATING FIELD TYPE AND VALUE */
					$slug_arr = explode( '_', $slug );
					
					/* FOR CHECKBOX */
					if( is_array( $value ) ){ $value = implode( ',', $value ); }
					
					if( count( $slug_arr ) > 1 && $value ){
						/* LOOK FOR FIELD TYPE */
						switch( $slug_arr[0] ){
							
							case 'tax':
								array_push( $tax_params, $slug_arr[1].":".$value );
								break;
								
						}
						
					}
				}
				
				$shortcode_str .= "tax_query='".implode('#', $tax_params )."'";
				
			}
			
			$shortcode_str .= "]";
			
			echo do_shortcode( $shortcode_str );
			
			_e("</div>");
			
			_e("</div>");
			
			
			return ob_get_clean();
		}
		
		
	}
	
	new ORBIT_SEARCH;