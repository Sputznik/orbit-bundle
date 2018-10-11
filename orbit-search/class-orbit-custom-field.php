<?php 

	class ORBIT_CUSTOM_FIELD{
		
		function __construct(){
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save' ) );
			
			/* ADD THE RELEVANT META BOXES TO THE ORBIT TYPE */
			add_filter( 'orbit_meta_box_vars', function( $meta_box ){
				global $post_type;
				
				/* CUSTOM FIELDS FOR ORBIT-TYPES */
				if( isset( $meta_box['orbit-types'] ) && count( $meta_box['orbit-types'] ) && isset( $meta_box['orbit-types'][0]['fields'] ) ){
					$meta_box['orbit-types'][0]['fields']['custom_fields'] = array(
						'type'	=> 'textarea',
						'text'	=> 'Custom Fields'
					);
				}
				
				global $orbit_vars;
				
				if( isset( $orbit_vars['post_types'] ) && isset( $orbit_vars['post_types'][$post_type] ) && isset( $orbit_vars['post_types'][$post_type]['custom_fields'] ) && $orbit_vars['post_types'][$post_type]['custom_fields'] ){
					
					$new_meta_box = array(
						'id'		=> 'cf-post-type-main',
						'title'		=> 'Additional Fields',
						'fields'	=> array()
					);
					
					$fields = explode("\r\n", $orbit_vars['post_types'][$post_type]['custom_fields']);
					
					if( is_array( $fields ) && count( $fields ) ){
						foreach( $fields as $field ){
							if( $field ){
								
								$slug_field = sanitize_title( $field );
								
								$new_meta_box['fields'][$slug_field] = array(
									'type'	=> 'text',
									'text'	=> $field
								);
								
							}
						}
						
						if( !isset( $meta_box[ $post_type ] ) ){
							$meta_box[ $post_type ] = array();
						}
						
						array_push( $meta_box[ $post_type ], $new_meta_box );
						
					}
					
				}
				
				return $meta_box;
			});
			
			
			add_filter( 'orbit_post_type_meta_fields_appended', function( $fields ){
			
				array_push( $fields, 'custom_fields' );
				
				return $fields;
			});
			
		}
		
		function get_meta_boxes( ){
			
			global $orbit_vars, $post_type;
			
			/* SET IF THE VAR IS EMPTY */
			if( !isset( $orbit_vars['meta_box'] ) ){
				$orbit_vars['meta_box'] = array();
			}
			
			/* HOOK TO ADD CUSTOM META BOX */
			$orbit_vars['meta_box'] = apply_filters( 'orbit_meta_box_vars', $orbit_vars['meta_box'] );
			
			if( $post_type && isset( $orbit_vars['meta_box'][ $post_type ] ) ){
				return $orbit_vars['meta_box'][ $post_type ];
			}
			
			return array();
		}
		
		/* ADD META BOXES TO THE CUSTOM POST TYPES */
		function add_meta_boxes(){
			
			global $post_type;
			
			$meta_boxes = $this->get_meta_boxes();
			
			foreach( $meta_boxes as $meta_box ){
				
				add_meta_box( 
					$meta_box['id'], 													// Unique ID
					$meta_box['title'], 												// Box title
					array( $this, 'box_html' ), 										// Content callback
					$post_type,
					isset( $meta_box['context'] ) ? $meta_box['context'] : 'normal', 	// Context
					'default',															// Priority
					$meta_box
				);
			}
		}
		
		function box_html( $post, $box ) {
			
			if( isset( $box[ 'args' ] ) && isset( $box[ 'args' ][ 'fields' ] ) ){
				
				_e("<div class='form-wrap'>");
				
				foreach( $box[ 'args' ][ 'fields' ] as $slug => $f ){
					
					/* HOOK TO ADD OPTIONS */
					if( isset( $f['options'] ) ){
						$f['options'] = apply_filters( 'orbit_custom_field_'.$slug.'_options', $f['options'] );
					}
					
					include	"admin_templates/custom_field.php";
				
				}
				
				_e("</div>");
				
			}
			
		}
		
		function save( $post_id ){
			$meta_boxes = $this->get_meta_boxes();
			foreach( $meta_boxes as $meta_box ){
				
				if( isset( $meta_box[ 'fields' ] ) ){
				
					foreach( $meta_box[ 'fields' ] as $slug => $f ){
						
						if(	array_key_exists( $slug, $_POST ) ){
							update_post_meta( $post_id, $slug, $_POST[ $slug ] );
						}
						
					}
				}		
			}
		}
		
	}
	
	new ORBIT_CUSTOM_FIELD;