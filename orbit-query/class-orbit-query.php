<?php

class ORBIT_QUERY extends ORBIT_QUERY_BASE{

	function __construct(){

		$this->shortcode = 'orbit_query';

		add_filter( 'orbit_shortcode_cache_key_str', function( $cache_key ){
			if( $cache_key == 'orbit_query' ){
				$cache_key = 'oq';
			}

			return $cache_key;
		});

		parent::__construct();

	}

	function get_default_atts() {
		return array(
			'cache'									=> '0',
			'tax_query'							=> '',
			'date_query'						=> '',
			'sticky_posts'					=> '0',
			'exclude_sticky_posts'	=> '0',
			'post_type' 						=> 'post',
			'post_status'						=> 'publish',
			'posts_per_page'				=> '10',
			'post__not_in'					=> '',
			'post__in'							=> '',
			's'											=> '',
			'author'								=> '',
			'cat'										=> '',
			'category_name' 				=> '',
			'tag' 									=> '',
			'offset'								=> '0',
			'pagination'						=> '0',
			'paged'									=> '1',
			'style'									=> '',
			'id'										=> 'posts-'.rand()
		);
	}

	function remove_atts_from_cache_key(){
		return array('url', 'tax_query', 'id', 'date_query');
	}

	function get_offset($atts){

		// IF PAGED ATTRIBUTE IS PASSED AS A GET PARAM
		if( get_query_var('paged') ){
			$atts['paged'] = max( 1, get_query_var('paged') );
		}

		return (((int)$atts['paged'] - 1) * (int)$atts['posts_per_page']) + (int)$atts['offset'];
	}

	function get_sticky_posts(){
		return get_option( 'sticky_posts' );
	}

	function explode_to_arr( $str, $seperator = ',' ){
		return ! empty( $str ) ? explode( $seperator, $str ) : '';
	}

	function plain_shortcode( $atts, $content = false ){
		ob_start();

		/* GET ATTRIBUTES FROM THE SHORTCODE */
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

		/* CREATE QUERY ATTRIBUTES WITH DEFAULT VALUES FROM THE SHORTCODE ATTRIBUTES */
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
			'offset'				=> self::get_offset($atts)
		);

		/* DONT FETCH SQL_CALC_FOUND_ROWS */
		if( $atts['pagination'] == '0' ){
			$query_atts['no_found_rows'] = true;
		}

		/* SET TAXONOMY QUERY IF ANY */
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

		/* SET DATE QUERY IF ANY */
		if( isset( $atts['date_query'] ) && !empty( $atts['date_query'] ) ){
			$date_arr = $this->explode_to_arr( $atts['date_query'], "#" );

			$query_atts['date_query'] = array();

			foreach( $date_arr as $tax ){

				$temp = $this->explode_to_arr( $tax, ':' );

				if( count( $temp ) > 1 ){

					$query_atts['date_query'][ $temp[0] ] = $temp[1];

				}
			}
		}

		/*
		echo "<pre>";
		print_r( $query_atts );
		echo "</pre>";
		*/

		$this->query = new WP_Query( $query_atts );

		if( $this->query->have_posts() ){
			the_oq_articles( $atts );
			if( $this->query->max_num_pages > 1 ){
				the_oq_pagination( $atts );
			}
			wp_reset_postdata();
		}

		return ob_get_clean();
	}

	/* OVERRIDDEN FROM THE PARENT */
	function get_ajax_url( $atts ){
		return $this->get_ajax_url_from_args( $atts, array('paged') );
	}

}

global $orbit_query;
$orbit_query = new ORBIT_QUERY;
