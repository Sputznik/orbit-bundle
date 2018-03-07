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
					'type'			=> 'customfield',
					'typeval'		=> '',
					'form'			=> 'typeahead',
					'placeholder'	=> 'Type something',
					'options'		=> '',
					'label'			=> ''
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
					$atts['items'] = $this->get_terms_arr( $atts['typeval'] );
					break;
					
				case 'cf':
					$atts['items'] = isset( $atts['options'] ) ? explode( ',', $atts['options'] ) : array();
					break;
				
			}
			
			$this->display( $atts );
			
			return ob_get_clean();
		}
		
		function get_terms($term_type, $parent = false){
			$args = array('orderby' => 'term_id');
			if( $parent ){ $args['child_of'] = $parent; }
			$terms = get_terms( array( $term_type ), $args);
			return $terms;
		}
		
		function get_terms_arr( $term_type, $parent = false ){
			$final_arr = array();
			$terms = $this->get_terms($term_type, $parent);
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
				
				if( isset( $atts['label'] ) && $atts['label'] ){
					_e("<label>". $atts['label'] ."</label>");
				}
				
				switch( $atts['form'] ){
					
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