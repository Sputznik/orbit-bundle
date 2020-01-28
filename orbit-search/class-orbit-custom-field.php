<?php

	class ORBIT_CUSTOM_FIELD extends ORBIT_BASE{

		function __construct(){
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_filter( 'orbit_meta_box_vars', array( $this, 'orbit_fields' ) );
			add_action( 'save_post', array( $this, 'save' ) );

			/* ADD THE RELEVANT META BOXES TO THE ORBIT TYPE */
			add_filter( 'orbit_meta_box_vars', function( $meta_box ){
				global $post_type;

				/* CUSTOM FIELDS FOR ORBIT-TYPES */
				if( isset( $meta_box['orbit-types'] ) && count( $meta_box['orbit-types'] ) && isset( $meta_box['orbit-types'][1]['fields'] ) ){
					$meta_box['orbit-types'][1]['fields']['custom_fields'] = array(
						'type'	=> 'repeater_cf',
						'text'	=> 'Custom Fields',
						'items'	=> array(
							'type' => array(
								'type' 		=> 'dropdown',
								'text' 		=> 'Select Field Type',
								'options'	=> array(
									'text'			=> 'Text',
									'textarea'	=> 'Textarea',
									'dropdown'	=> 'Dropdown',
									'checkbox'	=> 'Checkboxes'
								)
							),
							'text' => array(
								'type' 		=> 'text',
								'text' 		=> 'Label',
							),
							'placeholder'	=> array(
								'type'	=> 'text',
								'text'	=> 'Placeholder',
								'help'	=> 'Apeears within the input text fields'
							),
							'options' => array(
								'type' 		=> 'repeater-options',
								'text' 		=> 'Options',
								'help'		=> 'Only valid for dropdown or checkboxes. Enter each item on a new line.'
							),
						),
						'rules'	=> array(
							'options'	=> array(
								'hide'	=> array(
									'type'	=> array( 'text', 'textarea' )
								),
								'show'	=> array(
									'type'	=> array( 'dropdown', 'checkbox' )
								)
							),
							'placeholder'	=> array(
								'show'	=> array(
									'type'	=> array( 'text', 'textarea' )
								),
								'hide'	=> array(
									'type'	=> array( 'dropdown', 'checkbox' )
								)
							),
						)

					);
				}



				global $orbit_vars;

				// BUILD CUSTOM FIELDS BASED ON WHAT THE USER HAS SELECTED IN THE ORBIT-TYPES
				if( isset( $orbit_vars['post_types'] ) && isset( $orbit_vars['post_types'][$post_type] ) && isset( $orbit_vars['post_types'][$post_type]['custom_fields'] ) && $orbit_vars['post_types'][$post_type]['custom_fields'] ){

					$new_meta_box = array(
						'id'		=> 'cf-post-type-main',
						'title'		=> 'Additional Fields',
						'fields'	=> array()
					);

					$fields = $orbit_vars['post_types'][$post_type]['custom_fields'];

					if( is_array( $fields ) && count( $fields ) ){

						foreach( $fields as $field ){

							if( isset( $field['text'] ) && isset( $field['type'] ) ){
								$slug_field = sanitize_title( $field['text'] );

								if( isset( $field['options'] ) ){

									// INCASE STRING IS PASSED AS OPTIONS - VERSION 1
									if( !is_array( $field['options'] ) ){
										$options = explode( "\r\n", $field['options'] );

										$field['options'] = array();

										foreach( $options as $opt ){
											$opt_slug = sanitize_title( $opt );
											$field['options'][$opt_slug] = $opt;
										}
									}




								}

								$new_meta_box['fields'][$slug_field] = $field;

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

		function orbit_fields( $meta_box ){

			global $post_type;

			if( 'orbit-types' != $post_type ) return $meta_box;

			$meta_box['orbit-types'] = array_merge( $meta_box['orbit-types'] , array(
				array(
					'id'		=> 'orbit-custom',
					'title'		=> 'Orbit Custom Fields',
					'fields'	=> array()
				),
			));
			return $meta_box;
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

				$clubbed_meta_values = array();
				if( isset( $box['args']['field_name'] ) ){
					$clubbed_meta_values = get_post_meta( $post->ID, $box['args']['field_name'], true );
					$clubbed_meta_values = is_array( $clubbed_meta_values ) ? $clubbed_meta_values : array();
				}

				foreach( $box[ 'args' ][ 'fields' ] as $slug => $f ){

					/* HOOK TO ADD OPTIONS */
					if( isset( $f['options'] ) ){
						$f['options'] = apply_filters( 'orbit_custom_field_'.$slug.'_options', $f['options'] );
					}

					// GETTING VALUE FROM THE POST META TABLE
					if( isset( $box['args']['field_name'] ) && isset( $clubbed_meta_values[ $slug ] ) ){
						$f['val'] = $clubbed_meta_values[ $slug ];
					}
					else{
						$f['val'] = get_post_meta( $post->ID, $slug, true );
					}

					if( isset( $box['args']['field_name'] ) ){
						$slug = $box['args']['field_name']."[".$slug."]";
					}

					$this->field_html( $slug, $f );

				}

				_e("</div>");

			}

			do_action( 'orbit_meta_box_html', $post, $box );

		}

		function field_html( $slug, $f ){

			$orbit_form_field = ORBIT_FORM_FIELD::getInstance();

			$form_field_atts = array(
				'name'				=> $slug,
				'value'				=> $f['val'],
				'default'			=> isset( $f['default'] ) ? $f['default'] : '',
				'placeholder'	=> isset( $f['placeholder'] ) ? $f['placeholder'] : '',
				'label'				=> $f['text'],
				'type'				=> $f['type'],
				'rules'				=> isset( $f['rules'] ) ? $f['rules'] : array(),
				'items'				=> array(),
				'help'				=> isset( $f['help'] ) ? $f['help'] : ""
			);

			// CONFORM THE OPTIONS TO THE ITEMS ARRAY THAT IS NEEDED IN ORBIT_FORM_FIELD
			if( isset( $f['options'] ) && is_array( $f['options'] ) ){
				foreach( $f['options'] as $opt_id => $opt_val ){
					array_push( $form_field_atts['items'], array( 'slug' => $opt_id, 'name' => $opt_val ) );
				}
			}

			if( $f['type'] == 'repeater_cf' && isset( $f['items'] ) ){
				$form_field_atts['items'] = $f['items'];
			}

			$orbit_form_field->display( $form_field_atts );


		}

		function save_field( $post_id, $slug ){
			if( array_key_exists( $slug, $_POST ) ){
				update_post_meta( $post_id, $slug, $_POST[ $slug ] );
			}
			else{
				//print_r( "$slug needs to be deleted" );
				delete_post_meta( $post_id, $slug );
			}
		}

		function save( $post_id ){
			$meta_boxes = $this->get_meta_boxes();

			//echo "<pre>";
			//print_r( $meta_boxes );
			//echo "</pre>";

			//echo "<pre>";
			//print_r( $_POST );
			//echo "</pre>";

			foreach( $meta_boxes as $meta_box ){

				if( isset( $meta_box['field_name'] ) ){
					$this->save_field( $post_id, $meta_box['field_name'] );
				}
				elseif( isset( $meta_box[ 'fields' ] ) ){
					foreach( $meta_box[ 'fields' ] as $slug => $f ){
						$this->save_field( $post_id, $slug );
					}	// end of foreach
				}		// end of if
			}			// end of foreach
			//wp_die();
		}				// end of function

	}

	ORBIT_CUSTOM_FIELD::getInstance();
