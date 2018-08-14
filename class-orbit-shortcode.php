<?php

class ORBIT_SHORTCODE{
	
	var $shortcode;
	var $default_atts;
	
	function __construct(){
		
		add_shortcode( $this->shortcode, array( $this, 'main_shortcode' ), 100 );
		add_shortcode( $this->shortcode."_ajax", array( $this, 'ajax_shortcode' ), 100 );
		
		add_action( 'wp_ajax_'.$this->shortcode, array( $this, 'ajax_callback' ) );
		add_action( 'wp_ajax_nopriv_'.$this->shortcode, array( $this, 'ajax_callback' ) );
		
	}
	
	function get_default_atts(){
		return array();
	}
	
	function get_atts( $atts ){
		$defaults_atts = apply_filters( $this->shortcode.'_atts', $this->get_default_atts() );
		$atts = shortcode_atts( $defaults_atts, $atts, $this->shortcode );
		
		/* ADD URL THAT IS THE EQUIVALENT VERSION OF ALL THE ATTS */
		$atts['url'] = $this->get_ajax_url( $atts );	
		
		return $atts;
	}
	
	function remove_atts_from_cache_key(){
		return array('url');
	}
	
	function get_cache_key( $atts ){
		$atts = $this->get_atts( $atts );
		
		$cache_key = apply_filters('orbit_shortcode_cache_key_str', $this->shortcode);
		
		foreach( $atts as $key => $val ){
			if( ! in_array( $key, $this->remove_atts_from_cache_key() ) ){
				$cache_key .= $val;
			}
		}
		
		return md5( $cache_key );
	}
	
	function get_cache( $atts ){
		$cache_key = $this->get_cache_key( $atts );
		
		// try to get value from Wordpress cache
		return get_transient( $cache_key );
	}
	
	function set_cache( $data, $atts ){
		$cache_key = $this->get_cache_key( $atts );
		// store value in cache for hours
		set_transient( $cache_key, $data, 3600 * $atts['cache'] ); 
	}
	
	function main_shortcode( $atts, $content ){
		
		$data = false;
		
		if( isset( $atts['cache'] ) && $atts['cache'] && is_numeric( $atts['cache'] ) ){
			$data = $this->get_cache( $atts ); 
		}
		
		// if no value in the cache
		if ( $data === false ) {
			
			$data = $this->plain_shortcode( $atts, $content );
			
			if( isset( $atts['cache'] ) && $atts['cache'] ){
				$this->set_cache( $data, $atts );
			}
		}
		
		
		return $data;
		
	}

	function plain_shortcode( $atts, $content = false ){
		
	}
	
	
	
	/* AJAX CALLBACK FUNCTION */
	function ajax_callback(){
		
		/* CREATE SHORTCODE STRING */
		$shortcode_str = '['.$this->shortcode;
			
		/* init all attributes for the shortcodes */
		foreach($_GET as $key=>$val){
			if(isset($_GET[$key])){ $val = $_GET[$key]; }
			$shortcode_str .= ' '.$key.'="'.$val.'"';
		}
		
		/* CLOSE THE SHORTCODE STRING */	
		$shortcode_str .= ']';
		
		/* PRINT THE SHORTCODE */	
		echo do_shortcode( $shortcode_str );
		
		wp_die();
	}
	
	function get_ajax_url( $atts ){
		return $this->get_ajax_url_from_args( $atts );
	}
	
	/* AJAX VERSION OF THE SAME SHORTCODE */
	function ajax_shortcode( $atts ){
		ob_start();
		
		/* GET ATTRIBUTES FROM THE SHORTCODE */
		$atts = $this->get_atts( $atts );
		
		$cache = false;
		
		if( isset( $atts['cache'] ) && $atts['cache'] && is_numeric( $atts['cache'] ) ){
			$cache = $this->get_cache( $atts ); 
		}
		
		if ( $cache === false ) {
			/* CREATE PARENT ELEMENT THAT WILL HOLD THE AJAX CALLBACK POSTS */
			$cache = "<div data-behaviour='oq-reload-html' data-url='".$atts['url']."'></div>";
		}
		
		echo $cache;
		
		return ob_get_clean();
	}
	
	// CREATE AJAX URL TO REQUEST SUBSEQUENT POSTS LATER
	function get_ajax_url_from_args( $args, $dont_include = array() ){
		$url = admin_url( 'admin-ajax.php' )."?action=".$this->shortcode;
		foreach($args as $key=>$val){
			/* CHECK IF THE KEY IS NOT IN THE $DONT_INCLUDE ARRAY */
			if(!in_array($key, $dont_include) && $val){ $url .= "&".$key."=".$val;}
		}
		return $url;
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
	
}