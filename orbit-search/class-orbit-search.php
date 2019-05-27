<?php

	class ORBIT_SEARCH{

		function __construct(){

			add_shortcode( 'orbit_search', array( $this, 'form' ) );

			/* ADD FORMS THROUGH THE BACKEND */
			add_filter( 'orbit_post_type_vars', array( $this, 'create_post_type' ) );

			/* ADD THE RELEVANT META BOXES TO THE FORM */
			add_filter( 'orbit_meta_box_vars', array( $this, 'create_meta_box' ) );

			// THIS IS WHERE THE FILTERS THAT ARE ADDED BY THE USER FROM THE ADMIN PANEL IS SAVED IN THE DB
			add_action( 'save_post', array( $this, 'saveFiltersFromAdmin' ), 10, 2 );

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

			/*Create metabox*/
			add_action( 'add_meta_boxes', array( $this, 'createMetaBox' ));

			/* ENQUEUE ADMIN ASSETS */
			add_action('admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		}

		function admin_assets(){
				wp_enqueue_script( 'orbit-repeater', plugin_dir_url( __FILE__ ).'js/repeater.js', array('jquery'), ORBIT_BUNDLE_VERSION, true );
				wp_enqueue_script( 'orbit-search-admin', plugin_dir_url( __FILE__ ).'js/admin.js', array('jquery', 'orbit-repeater'), time(), true );

		}

		// THIS IS WHERE THE FILTERS THAT ARE ADDED BY THE USER FROM THE ADMIN PANEL IS SAVED IN THE DB
		function saveFiltersFromAdmin( $post_id, $post ){

			$post_type = get_post_type( $post_id );
			if ( "orbit-form" != $post_type ) return;

			$byOrder = array_column( $_POST['orbit_filter'], 'order');
 			array_multisort( $byOrder, SORT_ASC, $_POST['orbit_filter'] );

			/*
			echo "<pre>";
			print_r( $_POST['orbit_filter'] );
			echo "</pre>";
			*/

			 /*
			 if( isset( $_POST['orbit_filter'] ) && is_array( $_POST['orbit_filter'] ) ){
				foreach( $_POST['orbit_filter'] as $orbit_filter ){
					print_r( $this->getFilterShortcode( $orbit_filter ) );
				}
			 }
			 */

			 if( isset( $_POST['orbit_filter'] ) && is_array( $_POST['orbit_filter'] ) ){
				 update_post_meta( $post_id, 'orbit_filters', $_POST['orbit_filter'] );
			 }

			 //wp_die();
		}

		function getFilterShortcode( $filter ){

			$shortcode_str = "[orbit_filter";

			foreach( $filter as $slug => $value ){
				if( in_array( $slug, array( 'label', 'form', 'type', 'typeval' ) ) ){
					$shortcode_str .= " ".$slug."='".$value."'";
				}
			}

			$shortcode_str .= "]";

			return $shortcode_str;


		}

		function createMetaBox(){

			global $post;
			$type = get_post_type( $post );

			if( $type=='orbit-form' ){

					add_meta_box('form-attributes','Add Form Attributes', function(){


						$form_atts = array(
							'types'	=> array(
								'tax'				=> 'Taxonomy',
								'postdate'	=> 'Date'
							),
							'form'	=> array(
								'checkbox' 								=> 'Checkbox (multiple)',
								'dropdown' 								=> 'Dropdown (single)',
								'typeahead'								=> 'Typeahead (input field)',
								'bt_dropdown_checkboxes'  => 'Single Dropdown (with checkboxes)'
							),
							'tax_options' => get_taxonomies(),

							'postdate_options'	=>	array(
								'year'	=>	'Year',
							)
						);

						echo '<pre>';
						// print_r( $tax_options );

						global $post;


						$filtersFromDB = get_post_meta( $post->ID, 'orbit_filters', true );

						if( $filtersFromDB ){
							$form_atts['db'] = $filtersFromDB;
						}



						echo '</pre>';


						_e( "<div data-behaviour='orbit-admin-filters' data-atts='".wp_json_encode( $form_atts )."'></div>");

					});
			}
		}

		/* ENQUEUE STYLESHEETS AND SCRIPTS */
		function assets() {

			wp_enqueue_style( 'orbit-search', plugin_dir_url( __FILE__ ).'css/style.css', array(), ORBIT_BUNDLE_VERSION );

			wp_enqueue_script('typeahead', plugin_dir_url( __FILE__ ).'js/typeahead.min.js', array('jquery'), ORBIT_BUNDLE_VERSION, true );
			wp_enqueue_script('orbit-search-script', plugin_dir_url( __FILE__ ).'js/main.js', array( 'jquery', 'typeahead' ), ORBIT_BUNDLE_VERSION, true );

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
						'posts_per_page'	=> array(
							'type'		=> 'number',
							'text'		=> 'Posts Per Page',
							'default'	=> 10
						)
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
			do_action( 'orbit_filter_form_header', $form );

			// CHECK IF THE ORBIT FILTERS EXISTS INSIDE THE POST META
			$orbit_filters = get_post_meta( $form->ID, 'orbit_filters', true );
			if( is_array( $orbit_filters ) && count( $orbit_filters ) ){
				foreach ($orbit_filters as $orbit_filter) {
					$filter_shortcode = $this->getFilterShortcode( $orbit_filter );
					echo do_shortcode( $filter_shortcode );
				}
			}
			else{
				// DEFAULT FUNCTIONALITY
				_e( do_shortcode( $form->post_content ) );
			}

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


			/* POSTS PER PAGE - FORM */
			$posts_per_page = get_post_meta( $atts['id'], 'posts_per_page', true );
			if( !$posts_per_page ){ $posts_per_page = 10; }
			$shortcode_str .= "posts_per_page='".$posts_per_page."' ";	/* ADD TO THE SHORTCODE AS AN ATTRIBUTE */

			$tax_query_str = '';


			if( is_array ( $_GET ) && ( count( $_GET ) >= 1 ) ){

				$tax_params = array();

				$date_params = array();

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

							case 'postdate':
								array_push( $date_params, $slug_arr[1].":".$value );
								break;

						}

					}
				}

				$shortcode_str .= "tax_query='".implode('#', $tax_params )."'";

				$shortcode_str .= " date_query='".implode('#', $date_params )."'";
			}

			$shortcode_str .= "]";

			//echo $shortcode_str;

			echo do_shortcode( $shortcode_str );

			_e("</div>");

			_e("</div>");


			return ob_get_clean();
		}


	}

	new ORBIT_SEARCH;
