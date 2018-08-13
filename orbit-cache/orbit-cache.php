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
		
		function plain_shortcode( $atts, $content = false ){
			ob_start();
			echo do_shortcode( $content );
			return ob_get_clean();
			
		}
	}
	
	global $orbit_cache;
	$orbit_cache = new ORBIT_CACHE;