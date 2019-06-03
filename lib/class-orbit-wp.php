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


}


ORBIT_WP::getInstance();
