<?php

	class ORBIT_FILTER extends ORBIT_BASE{

		function __construct(){

			/* SHORTCODE */
			add_shortcode( 'orbit_filter', array( $this, 'shortcode' ) );

			/* AJAX */
			add_action( 'wp_ajax_orbit_filter', array( $this, 'ajax' ) );

		}

		/* VARIABLES */
		function vars(){
			return array(
				'types'	=> array(
					'tax'				=> 'Taxonomy',
					//'cf'				=> 'Custom Field',
					'postdate'	=> 'Date'
				),
				'postdate_options'	=> array(
					'year'		=> 'Year',
					'after'		=> 'From (After Date Query)',
					'before' 	=> 'To (Before Date Query)'
				),
				'forms'	=> array(
					'checkbox' 								=> 'Checkbox (multiple)',
					'dropdown' 								=> 'Dropdown (single)',
					'typeahead'								=> 'Typeahead (input field)',
					'bt_dropdown_checkboxes'  => 'Single Dropdown (with checkboxes)',
					'date'										=> 'Date (input field)',
					'multirange'							=>	'Multirange'
				),
				'shortcode'	=> array(
					'type'						=> 'customfield',
					'typeval'					=> '',
					'form'						=> 'typeahead',
					'placeholder'			=> 'Type something',
					'options'					=> '',
					'label'						=> '',
					'tax_parent'			=> 0,
					'tax_hide_empty'	=> true,
				)
			);
		}

		/* PRINT THE SHORTCODE TO BE DISPLAYED WHILE CREATING THE ORBIT FILTER */
		function ajax(){

			$shortcode = urldecode( $_GET['shortcode'] );
			$shortcode = str_replace( "\\", "", $shortcode );
			echo do_shortcode( $shortcode );
			wp_die();
		}

		/* CREATE SHORTCODE FROM ARRAY OF ATTRTIBUTES */
		function createShortcode( $params ){
			$orbit_util = ORBIT_UTIL::getInstance();
			return $orbit_util->createShortcode( 'orbit_filter', $params, array( 'label', 'form', 'type', 'typeval', 'tax_hide_empty' ) );
		}


		/* MAIN SHORTCODE FUNCTION */
		function shortcode( $atts ){

			ob_start();

			/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
			$atts = shortcode_atts( $this->vars()['shortcode'], $atts, 'orbit_filter' );

			$atts['items'] = array();

			/* GET ITEMS FOR THE FILTER */
			switch( $atts['type'] ){

				case 'tax':

					if( $atts['tax_hide_empty'] == 'true' ){ $atts['tax_hide_empty'] = true; }
					else{ $atts['tax_hide_empty'] = false; }

					$atts['items'] = $this->get_terms_arr( $atts['typeval'], $atts['tax_parent'], $atts['tax_hide_empty'] );

					break;

				case 'postdate':
					$atts['items'] = $this->get_post_options( $atts['typeval'] );
					
					break;

				case 'cf':

					$atts['items'] = isset( $atts['options'] ) ? explode( ',', $atts['options'] ) : array();
					break;

			}



			$this->display( $atts );

			return ob_get_clean();
		}

		function get_post_options( $field ){
			$options = array();
			global $wpdb;

			switch ( $field ) {
				case 'year':
					$years = $wpdb->get_results( "SELECT YEAR(post_date) FROM
						{$wpdb->posts} WHERE post_status = 'publish' GROUP BY YEAR(post_date) DESC", ARRAY_N );
					if ( is_array( $years ) && count( $years ) > 0 ) {
		        foreach ( $years as $year ) {
							$options[] = $year[0];
		      	}
		    	}
					break;

				default:

					break;
			}
			return $options;
		}

		/*
		function get_terms( $args ){
			$terms = get_terms( $args );
			return $terms;
		}
		*/

		function get_terms_arr( $term_type, $parent = -1, $hide_empty = true ){
			$final_arr = array();

			$args = array(
				'taxonomy'		=> $term_type,
				'hide_empty'	=> $hide_empty,
				'orderby' 		=> 'term_id'
			);
			if( $parent ==0 ){
				// RETURNS ONLY PARENT TERMS THAT ARE NOT DESCENDANTS
				$args['parent'] = 0;
			}
			elseif( $parent > 0 ){
				$args['child_of'] = $parent;
			}

			$orbit_wp = 	ORBIT_WP::getInstance();

			$terms = $orbit_wp->get_terms( $args );
			foreach($terms as $term){
				array_push($final_arr, $term->name);
			}
			return $final_arr;
		}

		function display( $atts ){

			/* SET FORM NAME AND FORM VALUE */
			$atts['form_name'] = $atts['type']."_".$atts['typeval'];
			if( isset( $_GET[ $atts['form_name'] ] ) ){
				$atts['form_value'] = $_GET[ $atts['form_name'] ];
			}

			if( isset( $atts['form'] ) ){

				$orbit_form_field = ORBIT_FORM_FIELD::getInstance();

				$new_items = array();
				foreach( $atts['items'] as $item ){
					array_push( $new_items, array( 'slug' => $item, 'name'	=> $item ) );
				}

				$orbit_form_field->display( array(
					'name'	=> $atts['form_name'],
					'value'	=> $atts['form_value'],
					'label'	=> $atts['label'],
					'type'	=> $atts['form'],
					'items'	=> $new_items
				) );

			}

		}

	}

	ORBIT_FILTER::getInstance();
