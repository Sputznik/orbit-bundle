<?php

  class ORBIT_SEARCH_METABOX extends ORBIT_BASE{

    function __construct(){

      /* ADD THE RELEVANT META BOXES TO THE FORM */
      add_filter( 'orbit_meta_box_vars', array( $this, 'create_meta_box' ) );

      // SEPERATE METABOX FOR FILTERS ONLY
			add_action( 'orbit_meta_box_html', array( $this, 'box_html' ), 1, 2 );
    }

    function box_html( $post, $box ){

      $orbit_filter = ORBIT_FILTER::getInstance();
      $orbit_search = ORBIT_SEARCH::getInstance();

      // FORM ATTRIBUTES THAT IS NEEDED BY THE REPEATER FILTERS
      $form_atts = $orbit_filter->vars();
      if( !$form_atts || !is_array( $form_atts ) ){ $form_atts = array(); }
      $form_atts['tax_options'] = get_taxonomies();


      if( isset( $box['id'] ) && 'orbit-form-filters' == $box['id'] ){

        // GET VALUE FROM THE DATABASE
        $form_atts['db'] = $orbit_search->getFiltersFromDB( $post->ID );

        $form_atts['sections'] = array(
          'postdate'	=> 'Filter by Date',
          'tax'				=> 'Filter by Taxonomy'
        );

        // TRIGGER THE REPEATER FILTER BY DATA BEHAVIOUR ATTRIBUTE
        _e( "<div data-behaviour='orbit-admin-filters' data-atts='".wp_json_encode( $form_atts )."'></div>");
      }

      if( isset( $box['id'] ) && 'orbit-export-csv' == $box['id'] ){

        // GET VALUE FROM THE DATABASE
        $form_atts['db'] = $orbit_search->getExportColsFromDB( $post->ID );

        $form_atts['sections'] = array(
          'post'	=> 'Post Information',
          'tax'		=> 'Taxonomies',
          'cf'		=> 'Custom Fields'
        );

        $form_atts['post_options'] = array(
          'title'				=>	'Title',
          'content'	    =>	'Content',
          'date'				=>	'Date'
        );

        //TRIGGER THE REPEATER FILTER BY DATA BEHAVIOUR ATTRIBUTE
        _e( "<div data-behaviour='orbit-export' data-atts='".wp_json_encode( $form_atts )."'></div>");
      }

      if( isset( $box['id'] ) && 'orbit-sort' == $box['id'] ){

        // GET VALUE FROM THE DATABASE
        $form_atts['db'] = $orbit_search->getSortingFieldsFromDB( $post->ID );

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
    }

    function get_settings_metabox(){
			return array(
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
          'default_sorting' => array(
            'type'        => 'dropdown',
            'text'        => 'Default Sorting',
            'options'     => array(
              'post:title:ASC' => 'Alphabetic Order'
            )
          )
				),
				'field_name'	=> 'filter_settings'
			);
		}

		function get_filters_metabox(){
			return array(
				'id'		=> 'orbit-form-filters',
				'title'		=> 'Orbit Filters',
				'fields'	=> array()
			);
		}

		function get_sorting_metabox(){
			return array(
				'id'		      => 'orbit-sort',
				'title'		    => 'Orbit Sorting Fields',
        'field_name'	=> 'orbit_sort_fields',
				'fields'	    => array()
			);
		}

		function get_header_metabox(){
			return array(
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
			);
		}

    function get_export_metabox(){
      return array(
       'id'		=> 'orbit-export-csv',
       'title'		=> 'Export Settings',
       'fields'	=> array()
      );
    }


    function create_meta_box( $meta_box ){

			global $post_type;

			if( 'orbit-form' != $post_type ) return $meta_box;

			$meta_box['orbit-form'] = array(
				$this->get_settings_metabox(),
				$this->get_filters_metabox(),
				$this->get_sorting_metabox(),
        $this->get_export_metabox(),
				$this->get_header_metabox(),
			);

			return $meta_box;
		}


  }

  ORBIT_SEARCH_METABOX::getInstance();
