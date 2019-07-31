<?php

// echo "<pre>";
// print_r( $instance );
// echo "</pre>";


$shortcode_str = "[orbit_query ";

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
//wp_die();
?>
<div><?php echo do_shortcode( $shortcode_str );?></div>
