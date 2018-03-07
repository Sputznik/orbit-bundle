<?php

class ORBIT_QUERY_BASE{
	
	var $query;
	var $shortcode;
	var $default_atts;
	
	function __construct(){
		
		$this->shortcode = '';
		$this->shortcode_slug = '';
		
		$this->init();
		
	}
	
	function init(){
		add_action( 'wp_enqueue_scripts', array( $this, 'assets') );
		
		add_shortcode( $this->shortcode, array( $this, 'plain_shortcode' ), 100 );
		add_shortcode( $this->shortcode."_ajax", array( $this, 'ajax_shortcode' ), 100 );
		
		add_action( 'wp_ajax_'.$this->shortcode_slug, array( $this, 'ajax_callback' ) );
		add_action( 'wp_ajax_nopriv_'.$this->shortcode_slug, array( $this, 'ajax_callback' ) );
		
	}
	
	
	
	
	
	function get_default_atts(){
		return array();
	}
	
	function get_atts( $atts ){
		$defaults_atts = apply_filters( $this->shortcode_slug.'_atts', $this->get_default_atts() );
		$atts = shortcode_atts( $defaults_atts, $atts, $this->shortcode );
		return $atts;
	}

	function plain_shortcode( $atts ){
		
	}
	
	function ajax_shortcode( $atts ){
		
	}
	
	function ajax_callback(){
		
	}
	
		
	// CHECK IF THE TEMPLATE FILE EXISTS IN THE THEME
	function include_template_file( $template, $atts ){
		
		$old_template = $template;
		
		if( isset( $atts['style'] ) && $atts['style'] ){
			$template = $template.'-'.$atts['style'];
		}
		
		$template_url = $template.'.php';	
		
		$theme_templates_url = apply_filters( 'orbit_query_template_'.$template , get_stylesheet_directory()."/orbit-query/".$template_url );
		$plugin_templates_url = plugin_dir_path(__FILE__)."templates/".$template_url;
		
		
		if( file_exists( $theme_templates_url ) ){
			include( $theme_templates_url );
		}
		else if( file_exists( $plugin_templates_url ) ){
			include( $plugin_templates_url );
		}
		else{
			include( "templates/".$old_template.".php" );
		}
		
	}
	
	function has_shortcode( $posts ){
		$found = false;
		if ( !empty($posts) ){
			foreach ($posts as $post) {
				if ( has_shortcode($post->post_content, $this->shortcode ) ){
					$found = true;
					break;
				}
			}	
		}
		return $found;
	}
	
	/* LOAD SCRIPTS AND STYLES IF THE SHORTCODE IS USED */
	function assets($posts){
		
		$uri = plugin_dir_url( __FILE__ );
			
		// ENQUEUE SCRIPT
		wp_enqueue_script('jquery');
		wp_enqueue_script('oq-script', $uri.'js/orbit-query.js', array('jquery'), '1.0.1', true);
			
	}
}