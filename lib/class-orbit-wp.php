<?php
/*
* WRAPPER CLASS FOR WORDPRESS FUNCTIONS
*/
class ORBIT_WP extends ORBIT_BASE{

  var $cache;

  function __construct(){
    $this->cache = array();
  }

  // GET TERMS OF A TAXONOMY
  function get_terms( $args ){
    $terms = get_terms( $args );
    return $terms;
  }

  function getCache( $cache_key ){
    if( isset( $this->cache[ $cache_key ] ) ){
      return $this->cache[ $cache_key ];
    }
    return false;
  }

  function setCache( $cache_key, $data ){
    $this->cache[ $cache_key ] =  $data;
  }

  function get_post_terms( $post_id, $taxonomy ){
    $cache_key = 'post_terms' . $post_id . $taxonomy;
    $data = $this->getCache( $cache_key );
    if( !$data ){
      $data = wp_get_post_terms( $post_id, $taxonomy );
      $this->setCache( $cache_key, $terms );
    }
    return $data;

  }

  function query( $query_atts ){
    global $orbit_wp_query;
    $wp_query = new WP_Query( $query_atts );
    $orbit_wp_query = $wp_query;
    return $wp_query;
  }

  function getCurrentURL(){
    global $wp;

    // get current url with query string.
    $current_url =  home_url( $wp->request );

    // REMOVE PAGINATION PARAMETERS
    if( strpos( $current_url, '/page' ) !== false ){
      // get the position where '/page.. ' text start.
      $pos = strpos( $current_url , '/page' );

      // remove string from the specific postion
      $current_url = substr( $current_url, 0, $pos );
    }

    return $current_url;
  }

  // RETURNS THE LIST OF POST IDS ONLY FOR THE ENTIRE QUERY RESULT SET
  function get_post_ids( $query_atts ){
    $query_atts['fields'] = 'ids';
    $query_atts['posts_per_page'] = -1;
    $posts = get_posts( $query_atts );
    return $posts;
  }

  // GET CONTEXTUAL TERM COUNT BY QUERY, USEFUL IN CALCULATING TOTAL OF TERMS FROM A RESULT SET
  function get_term_count_by_query( $term, $query_atts ){

    if( !isset( $query_atts['tax_query'] ) ){ $query_atts['tax_query'] = array(); }

    array_push( $query_atts['tax_query'], array(
      'taxonomy'  => $term->taxonomy,
      'field'     => 'slug',
      'terms'     => array( $term->slug )
    ) );

    $query_atts['fields'] = 'ids';

    $ps = $this->get_post_ids( $query_atts );

    if( count( $ps ) > 0 ){ return count( $ps ); }

    return 0;
  }



}




ORBIT_WP::getInstance();
