<?php

	class ORBIT_SEARCH extends ORBIT_BASE{

		function __construct(){

			add_shortcode( 'orbit_search', array( $this, 'form' ) );

			/* ADD FORMS THROUGH THE BACKEND */
			add_filter( 'orbit_post_type_vars', array( $this, 'create_post_type' ) );


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


		function enqueue_assets(){
			wp_enqueue_style( 'orbit-search', plugin_dir_url( __FILE__ ).'css/style.css', array(), ORBIT_BUNDLE_VERSION );
			wp_enqueue_style( 'slider', plugin_dir_url( __FILE__ ).'css/multirange.css', array(), ORBIT_BUNDLE_VERSION );
			wp_enqueue_script( 'orbit-dropdown-checkboxes', plugins_url( 'orbit-bundle/dist/js/dropdown-checkboxes.js' ), array('jquery'), ORBIT_BUNDLE_VERSION, true );
			wp_enqueue_script('typeahead', plugin_dir_url( __FILE__ ).'js/typeahead.min.js', array('jquery'), ORBIT_BUNDLE_VERSION, true );
			wp_enqueue_script('orbit-search-script', plugin_dir_url( __FILE__ ).'js/main.js', array( 'jquery', 'typeahead', 'orbit-dropdown-checkboxes' ), ORBIT_BUNDLE_VERSION , true );
			wp_enqueue_script('multirangeMain', plugin_dir_url( __FILE__ ).'js/multirange.js', array(), ORBIT_BUNDLE_VERSION, true );
			wp_enqueue_script('multirange', plugin_dir_url( __FILE__ ).'js/jquery.multirange.js', array( 'jquery' ), ORBIT_BUNDLE_VERSION , true );
		}


		/* ENQUEUE STYLESHEETS AND SCRIPTS */
		function assets() {
			global $post;
			if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'orbit_search' ) ) {
				$this->enqueue_assets();
			}
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
			if( ( isset( $_GET['orbit_sort'] ) && $_GET['orbit_sort'] ) ||
					( isset( $filter_settings['default_sorting'] ) && $filter_settings['default_sorting'] ) ){

				if( isset( $_GET['orbit_sort'] ) && $_GET['orbit_sort'] ){
					// USER SELECTED SORTING
					$orbit_sort = $_GET['orbit_sort'];
				}
				else{
					// DEFAULT SORTING SHOULD HAPPEN HERE
					$orbit_sort = $filter_settings['default_sorting'];
				}

				$orbit_sort = explode( ':', $orbit_sort );
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

		function getSettings($post_id){ return get_post_meta( $post_id, 'filter_settings', true ); }

		function form( $atts ){

			// CLASSES ORBIT
			$orbit_util 	= ORBIT_UTIL::getInstance();
			$orbit_wp = ORBIT_WP::getInstance();

			ob_start();

			// CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE
			$atts = shortcode_atts( $this->get_default_atts(), $atts, 'orbit_search' );

			// GET SETTINGS THAT ARE REQUIRED
			$filter_settings = $this->getSettings( $atts['id'] );
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
			$template_file = apply_filters( 'orbit_search_template', "templates/" . $atts['theme'] .".php", $atts );
			_e("<div class='orbit-search-container ". $atts['theme']."' data-behaviour='orbit-search'>");
			include( $template_file );
			_e("</div>");

			return ob_get_clean();
		}

		function filters_form( $form ){

			$form = get_post($form);

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

			ob_start();

			$orbit_wp = ORBIT_WP::getInstance();
			global $orbit_wp_query;

			// LIST OF TERMS FROM THE TAXONOMIES SELECTED IN THE BACKEND
      if( isset( $_GET ) && count( $_GET ) ){
        $taxonomies = isset( $filter_header['taxonomies'] ) ? $filter_header['taxonomies'] : array();
        foreach( $taxonomies as $taxonomy_slug ){
          $taxonomy = get_taxonomy( $taxonomy_slug );
          $terms_list = $orbit_wp->getPostsTerms( $taxonomy_slug, $posts, $orbit_wp_query->query );
          if( count( $terms_list ) ){
            echo "<div class='orbit-terms-count'><b>" . $taxonomy->label . "</b><span class='colon'>:</span> " . implode( '<span class="comma">,</span> ', $terms_list ) . "</div>";
          }
        }
      }
			return ob_get_clean();
		}
	}

	ORBIT_SEARCH::getInstance();
