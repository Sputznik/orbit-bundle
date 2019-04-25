<?php

	class ORBIT_FILTER{

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
					'tax'	=> 'Taxonomy',
					'cf'	=> 'Custom Field'
				),
				'forms'	=> array(
					'checkbox'	=> 'Checkbox',
					'dropdown'	=> 'Dropdown',
					'typeahead'	=> 'Typeahead Input'
				),
				'shortcode'	=> array(
					'type'						=> 'customfield',
					'typeval'					=> '',
					'form'						=> 'typeahead',
					'placeholder'			=> 'Type something',
					'options'					=> '',
					'label'						=> '',
					'tax_parent'			=> 0,
					'tax_hide_empty'	=> true
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


		/* MAIN SHORTCODE FUNCTION */
		function shortcode( $atts ){

			ob_start();

			/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
			$atts = shortcode_atts( $this->vars()['shortcode'], $atts, 'orbit_filter' );

			$atts['items'] = array();

			/* GET ITEMS FOR THE FILTER */
			switch( $atts['type'] ){

				case 'tax':

					if( $atts['tax_hide_empty'] == 'true' ){
						$atts['tax_hide_empty'] = true;
					}
					else{
						$atts['tax_hide_empty'] = false;
					}

					$atts['items'] = $this->get_terms_arr( $atts['typeval'], $atts['tax_parent'], $atts['tax_hide_empty'] );

					break;

				case 'cf':
					$atts['items'] = isset( $atts['options'] ) ? explode( ',', $atts['options'] ) : array();
					break;

			}

			$this->display( $atts );

			return ob_get_clean();
		}

		function get_terms( $args ){
			$terms = get_terms( $args );
			return $terms;
		}

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

			$terms = $this->get_terms( $args );
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

				_e("<div class='orbit-form-group'>");

				if( isset( $atts['label'] ) && $atts['label'] && $atts['form'] != 'bt_dropdown_checkboxes' ){
					_e("<label>". $atts['label'] ."</label>");
				}

				switch( $atts['form'] ){
					case 'bt_dropdown_checkboxes':

					case 'checkbox':
						/* CHECK IF FORM VALUE IS NOT SET */
						if( !isset( $atts['form_value'] ) ){ $atts['form_value'] = array();}
						break;
				}

				$filter_form_dir = plugin_dir_path(__FILE__) . "templates/filters/" . $atts['form'] . ".php";

				/* INCLUDE THE FILTER FORM */
				if( file_exists( $filter_form_dir ) ){
					include( $filter_form_dir );
				}

				_e("</div>");

			}

		}

	}

	global $orbit_filter_obj;
	$orbit_filter_obj = new ORBIT_FILTER;
