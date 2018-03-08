<?php

class ORBIT_QUERY extends ORBIT_QUERY_BASE{
	
	function __construct(){
		
		$this->shortcode = 'orbit_query';
		
		
		$this->init();
	}
	
	function get_default_atts() {	
		return array(
			'tax_query'				=> '',
			'sticky_posts'			=> '0',
			'exclude_sticky_posts'	=> '0',
			'post_type' 			=> 'post',
			'post_status'			=> 'publish',
			'posts_per_page'		=> '10',
			'post__not_in'			=> '',
			'post__in'				=> '',
			's'						=> '',
			'author'				=> '',
			'cat'					=> '',
			'category_name' 		=> '',
			'tag' 					=> '',
			'offset'				=> '0',
			'pagination'			=> '0',
			'paged'					=> '1',
			'style'					=> '',
			'id'					=> 'posts-'.rand()
		);
	}

	function get_offset($atts){
		return (((int)$atts['paged'] - 1) * (int)$atts['posts_per_page']) + (int)$atts['offset'];
	}
	
	function get_sticky_posts(){
		return get_option( 'sticky_posts' );
	}
	
	function explode_to_arr( $str, $seperator = ',' ){
		return ! empty( $str ) ? explode( $seperator, $str ) : '';
	}
	
	function plain_shortcode($atts){
		ob_start();
		$atts = $this->get_atts($atts);
		
		/* ADD STICKY POSTS */
		if( $atts['sticky_posts'] != '0' ){
			$atts['post__in'] = $this->get_sticky_posts();
			$atts['post__in'] = implode(',', $atts['post__in']);
		}
		
		/* EXCLUDE STICKY POSTS */
		if( $atts['exclude_sticky_posts'] != '0' ){
			$atts['post__not_in'] = $this->get_sticky_posts();
			$atts['post__not_in'] = implode(',', $atts['post__not_in']);
		}
			
		$query_atts = array(
			'post_type'				=> $this->explode_to_arr( $atts['post_type'] ), 
			'post_status'			=> $atts['post_status'],
			'posts_per_page'		=> $atts['posts_per_page'],
			'cat' 					=> $atts['cat'],
			'author'				=> $atts['author'],
			'category_name' 		=> $atts['category_name'],
			'tag' 					=> $atts['tag'],
			's' 					=> $atts['s'],
			'post__not_in' 			=> $this->explode_to_arr( $atts['post__not_in'] ),
			'post__in'				=> $this->explode_to_arr( $atts['post__in'] ),
			//'ignore_sticky_posts'	=> 1,
			'offset'				=> self::get_offset($atts)
		);
		
		if( isset( $atts['tax_query'] ) && !empty( $atts['tax_query'] ) ){
			$tax_arr = $this->explode_to_arr( $atts['tax_query'], "#" );
			
			$query_atts['tax_query'] = array();
			
			foreach( $tax_arr as $tax ){
				
				$temp = $this->explode_to_arr( $tax, ':' );
				
				if( count( $temp ) > 1 ){
					array_push( $query_atts['tax_query'],
						array(
							'taxonomy'	=> $temp[0],
							'field'		=> 'slug',
							'terms'		=> $this->explode_to_arr( $temp[1] )
						)
					);
				}
			}	
			
		}
		
		$atts['url'] = $this->get_ajax_url($atts, array('paged'));	
			
		$this->query = new WP_Query( $query_atts );
		
		if( $this->query->have_posts() ){
			the_oq_articles( $atts );
			the_oq_pagination( $atts );
			wp_reset_postdata();
		}
			
		return ob_get_clean();
	}
	
	
	
	// CREATE AJAX URL TO REQUEST SUBSEQUENT POSTS LATER
	function get_ajax_url($args, $dont_include = array()){
		$url = admin_url( 'admin-ajax.php' )."?action=".$this->shortcode;
		foreach($args as $key=>$val){
			if(!in_array($key, $dont_include) && $val){
				
				$url .= "&".$key."=".$val;
			}
				
		}
		return $url;
	}
		
	function ajax_shortcode( $atts ){
		ob_start();
		
		$atts = $this->get_atts($atts);
		
		$atts['url'] = $this->get_ajax_url($atts, array('paged'));	
		
		echo "<div data-behaviour='oq-reload-html' data-url='".$atts['url']."'></div>";
		
		return ob_get_clean();
	}
	
	function ajax_callback(){
		
		$shortcode_str = '['.$this->shortcode;
			
		$default_atts = self::get_default_atts();
		
		/* init all attributes for the shortcodes */
		foreach($_GET as $key=>$val){
			if(isset($_GET[$key])){
				$val = $_GET[$key];
			}
			$shortcode_str .= ' '.$key.'="'.$val.'"';
		}
			
		$shortcode_str .= ']';
			
		//echo $shortcode_str;
		echo do_shortcode($shortcode_str);
		
		wp_die();
	}
	
}

global $orbit_query;	
$orbit_query = new ORBIT_QUERY;
