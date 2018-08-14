<?php
	
	class ORBIT_CACHE extends ORBIT_SHORTCODE{
		
		function __construct(){
		
			$this->shortcode = 'orbit_cache';
			
			parent::__construct();
			
		}
		
		function get_default_atts() {	
			return array(
				'cache'	=> '0',
				'key'	=> 'default',
			);
		}
		
		function get_cache( $atts ){
			$cache_key = $this->get_cache_key( $atts );
			
			// try to get value from Wordpress cache
			$cache = get_option( $cache_key ); 
			
			if ( empty( $cache ) || is_array( $cache ) || isset( $cache['expires'] ) || $cache['expires'] < time() ){
				return false;
			}
			
			return $cache;
		}
		
		function set_cache( $data, $atts ){
			$cache_key = $this->get_cache_key( $atts );
			
			$cache = array(
				'expires' 	=> time() + ( 3600 * $atts['cache'] ),
				'data' 		=> $data,
			);
			
			// store value in cache for hours
			update_option( $cache_key, $cache ); 
		}
		
		function plain_shortcode( $atts, $content = false ){
			ob_start();
			echo do_shortcode( $content );
			return ob_get_clean();
			
		}
	}
	
	global $orbit_cache;
	$orbit_cache = new ORBIT_CACHE;