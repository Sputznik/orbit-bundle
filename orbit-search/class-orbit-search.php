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

			/* ADD REGISTERED TAXONOMIES TO THE CUSTOM FIELDS */
			add_filter( 'orbit_custom_field_taxonomies_options', function( $opt ){
				$taxonomies = get_taxonomies();
				return $taxonomies;
			} );

			/* ENQUEUE ASSETS */
			add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );

			// SEPERATE METABOX FOR FILTERS ONLY
			add_action( 'orbit_meta_box_html', function( $post, $box ){

				$orbit_filter = ORBIT_FILTER::getInstance();

				// FORM ATTRIBUTES THAT IS NEEDED BY THE REPEATER FILTERS
				$form_atts = $orbit_filter->vars();
				if( !$form_atts || !is_array( $form_atts ) ){ $form_atts = array(); }
				$form_atts['tax_options'] = get_taxonomies();


				if( isset( $box['id'] ) && 'orbit-form-filters' == $box['id'] ){

					// GET VALUE FROM THE DATABASE
					$form_atts['db'] = $this->getFiltersFromDB( $post->ID );

					$form_atts['sections'] = array(
						'postdate'	=> 'Filter by Date',
						'tax'				=> 'Filter by Taxonomy'
					);

					// TRIGGER THE REPEATER FILTER BY DATA BEHAVIOUR ATTRIBUTE
					_e( "<div data-behaviour='orbit-admin-filters' data-atts='".wp_json_encode( $form_atts )."'></div>");
				}

				if( isset( $box['id'] ) && 'orbit-export-csv' == $box['id'] ){

					// GET VALUE FROM THE DATABASE
					$form_atts['db'] = $this->getExportColsFromDB( $post->ID );

					$form_atts['sections'] = array(
						'post'	=> 'Post Information',
						'tax'		=> 'Taxonomies',
						'cf'		=> 'Custom Fields'
					);

					$form_atts['post_options'] = array(
						'title'				=>	'Title',
						'description'	=>	'Description',
						'date'				=>	'Date'
					);

					//TRIGGER THE REPEATER FILTER BY DATA BEHAVIOUR ATTRIBUTE
					_e( "<div data-behaviour='orbit-export' data-atts='".wp_json_encode( $form_atts )."'></div>");
				}

				if( isset( $box['id'] ) && 'orbit-sort' == $box['id'] ){

					// GET VALUE FROM THE DATABASE
					$form_atts['db'] = $this->getSortingFieldsFromDB( $post->ID );

					$form_atts['sections'] = array(
						'post'	=> 'Post Information',
						'cf'		=> 'Custom Fields'
					);

					$form_atts['post_options'] = array(
						'ID'					=> 	'Post ID',
						'author'			=>	'Author',
						'title'				=>	'Title',
						'date'				=>	'Date'
					);

					//TRIGGER THE REPEATER FILTER BY DATA BEHAVIOUR ATTRIBUTE
					_e( "<div data-behaviour='orbit-sort' data-atts='".wp_json_encode( $form_atts )."'></div>");
				}


			}, 1, 2 );


		}


		// GET THE SORTING FIELDS STORED AS ARRAY IN POST META
		function getSortingFieldsFromDB( $post_id ){
			$filtersFromDB = get_post_meta( $post_id, 'orbit_sort_fields', true );
			if( $filtersFromDB && is_array( $filtersFromDB ) ){
				return $filtersFromDB;
			}
			return array();
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

			if( isset( $_POST['orbit_sort_fields'] ) && is_array( $_POST['orbit_sort_fields'] ) ){
				update_post_meta( $post_id, 'orbit_sort_fields', $_POST['orbit_sort_fields'] );
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
						),
						'filter_heading'	=> array(
							'type' 				=> 'text',
							'text'				=> 'Results Heading',
							'placeholder'	=> '',
							'default'			=> 'FILTER'
						),
					),
					'field_name'	=> 'filter_settings'
				),
				array(
					'id'		=> 'orbit-form-filters',
					'title'		=> 'Orbit Filters',
					'fields'	=> array()
				),
				array(
					'id'		=> 'orbit-sort',
					'title'		=> 'Orbit Sorting Fields',
					'fields'	=> array()
				),
				// array(
				// 	'id'		=> 'orbit-export-csv',
				// 	'title'		=> 'Export to csv',
				// 	'fields'	=> array()
				// ),
				array(
					'id'		=> 'orbit-header',
					'title'		=> 'Header Section',
					'fields'	=> array(
						'results_heading'	=> array(
							'type' 		=> 'text',
							'text'		=> 'Results Heading',
							'placeholder'	=> 'Items (%d)'
						),
						'taxonomies'	=> array(
							'type'		=> 'checkbox',
							'text'		=> 'Show Inline Terms Of The Taxonomies With Count',
							'options'	=> array()
						)
					),
					'field_name'	=> 'filter_header'
				),
			);
			return $meta_box;
		}

		function create_post_type( $post_types ){

			$post_types['orbit-form'] = array(
				'slug' 		=> 'orbit-form',
				'labels'	=> array(
					'name' 			=> 'Orbit Filters',
					'singular_name' => 'Orbit Filter',
				),
				'supports'	=> array( 'title' ),
				'public'	=> false,
				'menu_icon'	=> 'dashicons-media-document'
			);

			return $post_types;
		}

		function get_default_atts(){
			return array(
				'id' 			=> '0',
				'theme'		=> 'default',
				'style'		=> 'db'
			);
		}

		function getQueryShortcode( $atts, $filter_settings ){

			$orbit_util = ORBIT_UTIL::getInstance();

			$shortcode_str = "[orbit_query pagination='1' ";

			// TEMPLATE FOR OBJECT QUERY
			$tmpl_id = isset( $filter_settings[ 'orbit-tmpl' ] ) ? $filter_settings[ 'orbit-tmpl' ] : "";
			if( $tmpl_id ){
				$shortcode_str .= "style='".$atts['style']."' style_id='".$tmpl_id."' ";	/* ADD TO THE SHORTCODE AS AN ATTRIBUTE */
			}

			// POST TYPES - FORM
			$post_types = isset( $filter_settings[ 'posttypes' ] ) ? $filter_settings[ 'posttypes' ] : array();
			$post_types = implode(',', $post_types);					/* CONVERTING ARRAY TO STRING */
			$shortcode_str .= "post_type='".$post_types."' ";	/* ADD TO THE SHORTCODE AS AN ATTRIBUTE */

			// POSTS PER PAGE - FORM
			$posts_per_page = isset( $filter_settings[ 'posts_per_page' ] ) ? $filter_settings[ 'posts_per_page' ] : 0;
			if( !$posts_per_page ){ $posts_per_page = 10; }
			$shortcode_str .= "posts_per_page='".$posts_per_page."' ";	/* ADD TO THE SHORTCODE AS AN ATTRIBUTE */

			// ADD TAXONOMY AND DATE QUERY PARAMETERS TO THE SHORTCODE
			$extra_params = $orbit_util->paramsToString( $_GET );
			if( isset( $extra_params['tax'] ) && $extra_params['tax'] ){ $shortcode_str .= "tax_query='".$extra_params['tax']."'"; }
			if( isset( $extra_params['date'] ) && $extra_params['date'] ){ $shortcode_str .= " date_query='".$extra_params['date']."'"; }

			// ADD ORDER AND ORDER BY PARAMS
			if( isset( $_GET['orbit_sort'] ) ){

				$orbit_sort = explode( ':', $_GET[ 'orbit_sort' ] );
				if( $orbit_sort[0] == 'post' ){
					$shortcode_str .= " orderby='".$orbit_sort[1].":".$orbit_sort[2]."'";
					//print_r( $orbit_sort );
				}
				elseif( $orbit_sort[0] == 'cf' ){
					$shortcode_str .= " orderby='meta_value:".$orbit_sort[2]."' meta_key='".$orbit_sort[1]."'";
				}

			}

			// END OF SHORTCODE STRING
			$shortcode_str .= "]";


			return $shortcode_str;
		}

		function has_sorting( $form_id ){
			$sorting_options = $this->getSortingFieldsFromDB( $form_id );
			if( is_array( $sorting_options ) && count( $sorting_options ) ) return true;
			return false;
		}

		// HTML FOR SORTING DROPDOWN
		function sorting_dropdown( $form_id ){
			$sorting_options = $this->getSortingFieldsFromDB( $form_id );

			if( is_array( $sorting_options ) && count( $sorting_options ) ){
				$sorting_options_val = array();

				foreach( $sorting_options as $sorting_option ){
					array_push( $sorting_options_val, array(
						'slug' => $sorting_option['type'].":".$sorting_option['field'].":".$sorting_option['order'],
						'name' => $sorting_option['label'] )
					);
				}
				echo "<div data-behaviour='orbit-sorting'>";
				$orbit_form_field = ORBIT_FORM_FIELD::getInstance();
				$orbit_form_field->display( array(
					'name'	=> 'orbit_sort',
					'value'	=> isset( $_GET[ 'orbit_sort' ] ) ? $_GET[ 'orbit_sort' ] : "",
					'default_option'	=> 'Sort By',
					//'label'	=> 'Sort By',
					'type'	=> 'dropdown',
					'items'	=> $sorting_options_val
				) );
				echo "</div>";
			}
		}

		function form( $atts ){

			// CLASSES ORBIT
			$orbit_util 	= ORBIT_UTIL::getInstance();
			$orbit_wp = ORBIT_WP::getInstance();

			ob_start();

			// CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE
			$atts = shortcode_atts( $this->get_default_atts(), $atts, 'orbit_search' );

			// GET SETTINGS THAT ARE REQUIRED
			$filter_settings = get_post_meta( $atts['id'], 'filter_settings', true );
			$filter_header = get_post_meta( $atts['id'], 'filter_header', true );
			if( !is_array( $filter_header ) ){ $filter_header = array(); }

			// GET FORM DETAILS
			$form = get_post( $atts['id'] );

			// GET RESULTS HTML
			$shortcode_str = $this->getQueryShortcode( $atts, $filter_settings );
			$results_html = do_shortcode( $shortcode_str );

			global $orbit_wp_query;
			$posts = $orbit_wp->get_post_ids( $orbit_wp_query->query );
			$total_posts = count( $posts );

			// FINAL RENDERING
			$atts['theme'] .= "-theme";
			_e("<div class='orbit-search-container ". $atts['theme']."' data-behaviour='orbit-search'>");
			include( "templates/" . $atts['theme'] .".php" );
			_e("</div>");

			return ob_get_clean();
		}

		function filters_form( $form ){
			$orbit_filter = ORBIT_FILTER::getInstance();
			$orbit_wp = ORBIT_WP::getInstance();
			include( 'templates/filters-form.php' );
		}

		function results_title( $filter_header, $total_posts ){
			if( !isset( $filter_header['results_heading'] ) || empty( $filter_header['results_heading'] ) ){
				$filter_header['results_heading'] = "Total Items (%d)";
			}
			return sprintf( $filter_header['results_heading'], $total_posts );
		}

		function results_inline_terms( $filter_header, $posts ){
			$orbit_wp = ORBIT_WP::getInstance();
			global $orbit_wp_query;

			// LIST OF TERMS FROM THE TAXONOMIES SELECTED IN THE BACKEND
      if( isset( $_GET ) && count( $_GET ) ){
        $taxonomies = isset( $filter_header['taxonomies'] ) ? $filter_header['taxonomies'] : array();
        foreach( $taxonomies as $taxonomy_slug ){
          $taxonomy = get_taxonomy( $taxonomy_slug );
          $terms_list = $orbit_wp->getPostsTerms( $taxonomy_slug, $posts, $orbit_wp_query->query );
          if( count( $terms_list ) ){
            echo "<div class='orbit-terms-count'><b>" . $taxonomy->label . "</b>: " . implode( ', ', $terms_list ) . "</div>";
          }
        }
      }
		}
	}

	new ORBIT_SEARCH;
