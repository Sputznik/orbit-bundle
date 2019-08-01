<?php

  add_filter( 'siteorigin_widgets_widget_folders', function( $folders ){
    $folders[] = plugin_dir_path( __FILE__ ).'widgets/';
    return $folders;
  } );

  // BANNER IMAGE FOR WIDGETS
  add_filter( 'siteorigin_widgets_widget_banner', function( $banner_url, $widget_meta ){
    if( in_array( $widget_meta['ID'], array( 'so-orbit-query' )  ) ) {
        $banner_url = plugin_dir_url(__FILE__).'assets/banner.svg';
    }
    return $banner_url;
  }, 10, 2);
