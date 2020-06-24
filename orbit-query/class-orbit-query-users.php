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
			'paged'					=> '1',
			'style'					=> '',
			'id'						=> 'users-'.rand()
		);
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
			'number'				=> $atts['number'],
			'paged'					=> $atts['paged'],
		);

		$this->query = new WP_User_Query( $query_atts );

		if( ! empty( $this->query->results ) ){
			the_oq_users( $atts );
		}

		return ob_get_clean();
	}

}

global $orbit_query_users;
$orbit_query_users = new ORBIT_QUERY_USERS;
