<?php

class ORBIT_COAUTHORS_QUERY extends ORBIT_QUERY_BASE{

	function __construct(){

		$this->shortcode = 'orbit_coauthors_query';

		parent::__construct();

	}

	function get_default_atts() {
		return array(
			'guest_authors_only' => '0',
			'hide_empty'    		 => '0', // Whether to hide authors not assigned to any posts
			'orderby'						 => 'name',
			'order'							 => 'ASC',
			'role'							 => '',
			'per_page'					 => '10',
			'style'							 => '',
			'id'								 => 'coauthors-'.rand()
		);
	}

	function plain_shortcode( $atts, $content = false ){

		$orbit_util = ORBIT_UTIL::getInstance();

		ob_start();

		$atts = $this->get_atts($atts);

		$atts['guest_authors_only'] = (bool) $atts['guest_authors_only'];

		$query_atts = array(
			'taxonomy'      => 'author',
			'hide_empty'		=> (bool) $atts['hide_empty'],
			'orderby'				=> $atts['orderby'],
			'order'					=> $atts['order'],
			'number'				=> $atts['per_page']
		);

		$authors = get_terms( $query_atts );

		global $coauthors_plus;

		$this->query = array();

		foreach ( $authors as $author_term ){

			if ( false === ( $contributor = $coauthors_plus->get_coauthor_by( 'user_login', $author_term->name ) ) ) {
				continue;
			}

			$this->query[$author_term->name] = $contributor;

			// ONLY SHOW GUEST AUTHORS IF THE $atts['guest_authors_only'] is true
			// ELSE SHOW ALL THE AUTHORS
			if ( ! $atts['guest_authors_only'] || $this->query[$author_term->name]->type === 'guest-author' ) {

				// SHOW ALL GUEST AUTHORS AND WP-USERS ( BASED ON THE USER ROLE IN $atts['role'] )
				// USER ROLE FILTERING WILL NOT WORK IF $atts['guest_authors_only'] is set to true
				if( ! $atts['guest_authors_only'] && $atts['role'] ){
					if( $this->query[$author_term->name]->type === 'wpuser' && ! $this->checkUserRole( $contributor, $atts['role'] ) ){
						unset( $this->query[$author_term->name] );
					}
				} //enduser role check

			} else {
					unset( $this->query[$author_term->name] );
			}

		} //endforeach

		if( ! empty( $this->query ) ){
			the_oq_coauthors( $atts );
		}

		return ob_get_clean();
	}

	function checkUserRole( $user, $roles ){
		$orbit_util = ORBIT_UTIL::getInstance();
		$allowed_roles = $orbit_util->explode_to_arr( $roles );
		if( array_intersect( $allowed_roles, $user->roles ) ){
      return true;
    }
    return false;
	}

}

global $orbit_coauthors_query;
$orbit_coauthors_query = new ORBIT_COAUTHORS_QUERY;
