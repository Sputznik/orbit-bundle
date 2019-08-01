<?php

$shortcode_str = "[orbit_query ";

if( $instance[ 'is_db_template' ] ){ $instance[ 'style' ] = 'db'; }

foreach( $instance as $key => $value ){
  if( in_array( $key, array( 'post_type', 'posts_per_page', 'style_id', 'style' ) ) ){
    if( $key == 'post_type' ){
      $value = implode( ',', $value );
    }
    $shortcode_str .= " ".$key."='".$value."'";
  }
}

$shortcode_str .= "]";

//echo $shortcode_str;

echo do_shortcode( $shortcode_str );
