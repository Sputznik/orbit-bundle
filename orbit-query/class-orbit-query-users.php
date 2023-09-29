<?php

class ORBIT_QUERY_USERS extends ORBIT_QUERY_BASE{

	function __construct(){

		$this->shortcode = 'orbit_query_users';

		parent::__construct();
	}

	function get_default_atts() {
		return array(
			'search' 				=> '',
			'role'					=> '',
			'role__in'			=> '',
			'role__not_in'	=> '',
			'exclude' 			=> '',
			'include' 			=> '',
			'orderby'				=> 'ID',
			'order'					=> 'ASC',
			'number'				=> '10',
			'offset'				=> '0',
			'pagination'		=> '0',
			'paged'					=> '1',
			'style'					=> '',
			'id'						=> 'users-'.rand()
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

		return (((int)$atts['paged'] - 1) * (int)$atts['number']) + (int)$atts['offset'];
	}

	function plain_shortcode( $atts, $content = false ){
		ob_start();
		$atts = $this->get_atts($atts);

		$query_atts = array(
			'search' 				=> $atts['search'],
			'role'					=> $atts['role'],
			'role__in'			=> ! empty($atts['role__in']) ? explode(',', $atts['role__in']) : '',
			'role__not_in'	=> ! empty($atts['role__not_in']) ? explode(',', $atts['role__not_in']) : '',
			'exclude' 			=> ! empty($atts['exclude']) ? explode(',', $atts['exclude']) : '',
			'include' 			=> ! empty($atts['include']) ? explode(',', $atts['include']) : '',
			'orderby'				=> $atts['orderby'],
			'order'					=> $atts['order'],
			'number'				=> (int) $atts['number'],
			'offset'				=> self::get_offset($atts)
		);

		/* DONT FETCH SQL_CALC_FOUND_ROWS */
		if( $atts['pagination'] == '0' ){
			$query_atts['count_total'] = false;
		}

		$this->query = new WP_User_Query( $query_atts );

		// CALCULATE TOTAL NUMBER OF PAGES BASED ON THE QUERY
		$total_pages = $this->query->total_users && $query_atts['number'] > 0 ? ceil( $this->query->total_users / $query_atts['number'] ) : 0;

		if( ! empty( $this->query->results ) ){
			the_oq_users( $atts );

			// SHOW LOAD MORE
			if( $total_pages > 1 ){
				the_oq_pagination( $atts );
			}
		}

		return ob_get_clean();
	}

}

global $orbit_query_users;
$orbit_query_users = new ORBIT_QUERY_USERS;
