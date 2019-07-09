<?php

	class ORBIT_SEARCH{

		function __construct(){

			add_shortcode( 'orbit_search', array( $this, 'form' ) );

			/* ADD FORMS THROUGH THE BACKEND */
			add_filter( 'orbit_post_type_vars', array( $this, 'create_post_type' ) );

			/* ADD THE RELEVANT META BOXES TO THE FORM */
			add_filter( 'orbit_meta_box_vars', array( $this, 'create_meta_box' ) );

			// THIS IS WHERE THE FILTERS THAT ARE ADDED BY THE USER FROM THE ADMIN PANEL IS SAVED IN THE DB
			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

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

			// SEPERATE METABOX FOR FILTERS ONLY
			add_action( 'orbit_meta_box_html', function( $post, $box ){

				if( isset( $box['id'] ) && 'orbit-form-filters' == $box['id'] ){

					$orbit_filter = ORBIT_FILTER::getInstance();

					// FORM ATTRIBUTES THAT IS NEEDED BY THE REPEATER FILTERS
					$form_atts = $orbit_filter->vars();
					if( !$form_atts || !is_array( $form_atts ) ){ $form_atts = array(); }
					$form_atts['tax_options'] = get_taxonomies();

					// GET VALUE FROM THE DATABASE
					$form_atts['db'] = $this->getFiltersFromDB( $post->ID );

					$form_atts['sections'] = array(
						'postdate'	=> 'Filter by Date',
						'tax'				=> 'Filter by Taxonomy'
					);

					// TRIGGER THE REPEATER FILTER BY DATA BEHAVIOUR ATTRIBUTE
					_e( "<div data-behaviour='orbit-admin-filters' data-atts='".wp_json_encode( $form_atts )."'></div>");
				}
			}, 1, 2 );

			// SEPARATE METABOX FOR EXPORTING CSV
			// add_action( 'orbit_meta_box_html', function( $post, $box ){
			//
			// 	if( isset( $box['id'] ) && 'orbit-export-csv' == $box['id'] ){
			//
			// 		$orbit_export = ORBIT_FILTER::getInstance();

					// FORM ATTRIBUTES THAT IS NEEDED BY THE REPEATER FILTERS
					// $form_atts = $orbit_export->vars();
					// if( !$form_atts || !is_array( $form_atts ) ){ $form_atts = array(); }
					// $form_atts['tax_options'] = get_taxonomies();

					// GET VALUE FROM THE DATABASE
					// $form_atts['db'] = $this->getExportColsFromDB( $post->ID );
					//
					// $form_atts['sections'] = array(
					// 	'post'	=> 'Post Information',
					// 	'tax'		=> 'Taxonomies',
					// 	'cf'		=> 'Custom Fields'
					// );
					//
					// $form_atts['post_options'] = array(
					// 	'title'				=>	'Title',
					// 	'description'	=>	'Description',
					// 	'date'				=>	'Date'
					// );
					// echo "<pre>";
					// print_r( $form_atts );
					// echo "<pre>";
					// wp_die();

					// TRIGGER THE REPEATER FILTER BY DATA BEHAVIOUR ATTRIBUTE
			// 		_e( "<div data-behaviour='orbit-export' data-atts='".wp_json_encode( $form_atts )."'></div>");
			// 	}
			// }, 1, 2 );
		}

		// GET THE FILTERS STORED AS ARRAY IN POST META
		function getFiltersFromDB( $post_id ){
			$filtersFromDB = get_post_meta( $post_id, 'orbit_filters', true );
			if( $filtersFromDB && is_array( $filtersFromDB ) ){
				return $filtersFromDB;
			}
			return array();
		}

		// GET THE CSV COLUMNS FOR EXPORT STORED AS ARRAY IN POST META
		function getExportColsFromDB( $post_id ){
			$data = get_post_meta( $post_id, 'orbit_export_csv_cols', true );
			if( $data && is_array( $data ) ){
				return $data;
			}
			return array();
		}

		/*
		* TRIGGERED WHEN THE PUBLISH/UPDATE BUTTON IS CLICKED IN THE ADMIN PANEL
		* THIS IS WHERE THE FILTERS THAT ARE ADDED BY THE USER FROM THE ADMIN PANEL IS SAVED IN THE DB
		*/
		function save_post( $post_id ){
			$post_type = get_post_type( $post_id );
			if ( "orbit-form" != $post_type ) return;

			// SAVE FILTERS IN POST META
			if( isset( $_POST['orbit_filter'] ) && is_array( $_POST['orbit_filter'] ) ){

				// SORT ARRAY BY THE VALUE ORDER
				$byOrder = array_column( $_POST['orbit_filter'], 'order');
	 			array_multisort( $byOrder, SORT_ASC, $_POST['orbit_filter'] );

				// SAVE
				update_post_meta( $post_id, 'orbit_filters', $_POST['orbit_filter'] );
			}

			if( isset( $_POST['orbit_export_csv_cols'] ) && is_array( $_POST['orbit_export_csv_cols'] ) ){
				update_post_meta( $post_id, 'orbit_export_csv_cols', $_POST['orbit_export_csv_cols'] );
			}



			//wp_die();
		}


		/* ENQUEUE STYLESHEETS AND SCRIPTS */
		function assets() {
			wp_enqueue_script( 'orbit-dropdown-checkboxes', plugins_url( 'orbit-bundle/dist/js/dropdown-checkboxes.js' ), array('jquery'), ORBIT_BUNDLE_VERSION, true );
			wp_enqueue_style( 'orbit-search', plugin_dir_url( __FILE__ ).'css/style.css', array(), ORBIT_BUNDLE_VERSION );
			wp_enqueue_style( 'slider', plugin_dir_url( __FILE__ ).'css/multirange.css', array(), ORBIT_BUNDLE_VERSION );
			wp_enqueue_script('typeahead', plugin_dir_url( __FILE__ ).'js/typeahead.min.js', array('jquery'), ORBIT_BUNDLE_VERSION, true );
			wp_enqueue_script('orbit-search-script', plugin_dir_url( __FILE__ ).'js/main.js', array( 'jquery', 'typeahead', 'orbit-dropdown-checkboxes' ), ORBIT_BUNDLE_VERSION , true );
			wp_enqueue_script('multirangeMain', plugin_dir_url( __FILE__ ).'js/multirange.js', array(), ORBIT_BUNDLE_VERSION, true );
			wp_enqueue_script('multirange', plugin_dir_url( __FILE__ ).'js/jquery.multirange.js', array( 'jquery' ), ORBIT_BUNDLE_VERSION , true );
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
				array(
					'id'		=> 'orbit-form-filters',
					'title'		=> 'Orbit Filters',
					'fields'	=> array()
				),
				// array(
				// 	'id'		=> 'orbit-export-csv',
				// 	'title'		=> 'Export to csv',
				// 	'fields'	=> array()
				// ),
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
				'supports'	=> array( 'title' ),
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

		function getQueryShortcode( $atts ){

			$orbit_util = ORBIT_UTIL::getInstance();

			$shortcode_str = "[orbit_query pagination='1' ";

			// TEMPLATE FOR OBJECT QUERY
			$tmpl_id = get_post_meta( $atts['id'], 'orbit-tmpl', true );
			if( $tmpl_id ){
				$shortcode_str .= "style='".$atts['style']."' style_id='".$tmpl_id."' ";	/* ADD TO THE SHORTCODE AS AN ATTRIBUTE */
			}

			// POST TYPES - FORM
			$post_types = get_post_meta( $atts['id'], 'posttypes', true );
			if( !$post_types ){ $post_types = array(); } 		/* IF VALUE IS NOT SET */
			$post_types = implode(',', $post_types);			/* CONVERTING ARRAY TO STRING */
			$shortcode_str .= "post_type='".$post_types."' ";	/* ADD TO THE SHORTCODE AS AN ATTRIBUTE */


			// POSTS PER PAGE - FORM
			$posts_per_page = get_post_meta( $atts['id'], 'posts_per_page', true );
			if( !$posts_per_page ){ $posts_per_page = 10; }
			$shortcode_str .= "posts_per_page='".$posts_per_page."' ";	/* ADD TO THE SHORTCODE AS AN ATTRIBUTE */

			// ADD TAXONOMY AND DATE QUERY PARAMETERS TO THE SHORTCODE
			$extra_params = $orbit_util->paramsToString( $_GET );
			if( isset( $extra_params['tax'] ) && $extra_params['tax'] ){ $shortcode_str .= "tax_query='".$extra_params['tax']."'"; }
			if( isset( $extra_params['date'] ) && $extra_params['date'] ){ $shortcode_str .= " date_query='".$extra_params['date']."'"; }

			// END OF SHORTCODE STRING
			$shortcode_str .= "]";

			return $shortcode_str;
		}

		function form( $atts ){

			// CLASSES ORBIT
			$orbit_util 	= ORBIT_UTIL::getInstance();
			$orbit_filter = ORBIT_FILTER::getInstance();

			ob_start();

			// CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE
			$atts = shortcode_atts( $this->get_default_atts(), $atts, 'orbit_search' );

			// GET FORM DETAILS
			$form = get_post( $atts['id'] );

			_e("<div class='orbit-search-container' data-behaviour='orbit-search'>");

			include( 'templates/filters-form.php' );

			_e("<div class='orbit-search-results'>");

			$shortcode_str = $this->getQueryShortcode( $atts );

			//echo $shortcode_str;

			echo do_shortcode( $shortcode_str );

			_e("</div>");

			_e("</div>");

			return ob_get_clean();
		}


	}

	new ORBIT_SEARCH;
