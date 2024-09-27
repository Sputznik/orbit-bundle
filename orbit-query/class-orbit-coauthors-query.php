<?php

class ORBIT_COAUTHORS_QUERY extends ORBIT_QUERY_BASE{

	function __construct(){

		$this->shortcode = 'orbit_coauthors_query';

		parent::__construct();

	}

	function get_default_atts() {
		return array(
			'hide_empty'    		 => '0', // Whether to hide authors not assigned to any posts
			'orderby'						 => 'name',
			'order'							 => 'ASC',
			'per_page'					 => '10',
			'offset'						 => '0',
			'pagination'				 => '0',
			'paged'							 => '1',
			'style'							 => '',
			'id'								 => 'coauthors-'.rand()
		);
	}

	/* OVERRIDDEN FROM THE PARENT */
	function get_ajax_url( $atts ){
		return $this->get_ajax_url_from_args( $atts, array('paged') );
	}

	function get_offset( $atts ){
		// IF PAGED ATTRIBUTE IS PASSED AS A GET PARAM
		if( get_query_var('paged') ){
			$atts['paged'] = max( 1, get_query_var('paged') );
		}
		return ( ( (int)$atts['paged'] - 1 ) * (int)$atts['per_page'] ) + (int)$atts['offset'];
	}

	function plain_shortcode( $atts, $content = false ){
		ob_start();

		$atts = $this->get_atts( $atts );

		$query_atts = array(
			'taxonomy'   	=> 'author',
			'hide_empty'	=> (bool) $atts['hide_empty'],
			'orderby'			=> $atts['orderby'],
			'order'				=> $atts['order'],
			'number'			=> (int) $atts['per_page'],
			'offset'			=> self::get_offset( $atts )
		);

		$count_coauthors_args =  $query_atts;
		$term_query = new WP_Term_Query( $query_atts );
		$authors 		= $term_query->terms ? $term_query->terms : array();

		unset( $count_coauthors_args['number'], $count_coauthors_args['offset'] );

		$total_coauthors = wp_count_terms( $count_coauthors_args );

		if( is_wp_error( $total_coauthors ) ){ return "Something went wrong"; }

		// wp_count_terms() can return a falsy value when the term has no children.
		if( !$total_coauthors ){ $total_coauthors = 0; }

		$total_pages = (int) $total_coauthors && $query_atts['number'] > 0 ? ceil( $total_coauthors / $query_atts['number'] ) : 0;

		global $coauthors_plus;

		$this->query = array();

		foreach( $authors as $author_term ){
			if( false === ( $contributor = $coauthors_plus->get_coauthor_by( 'user_login', $author_term->name ) ) ){
				continue;
			}

			$this->query[$author_term->name] = $contributor;
		}

		if( ! empty( $this->query ) ){
			the_oq_coauthors( $atts );

			// SHOW LOAD MORE
			if( $total_pages > 1 ){
				the_oq_pagination( $atts );
			}
		}

		return ob_get_clean();
	}

}

global $orbit_coauthors_query;
$orbit_coauthors_query = new ORBIT_COAUTHORS_QUERY;
