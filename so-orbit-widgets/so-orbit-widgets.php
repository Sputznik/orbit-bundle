<?php

function widgets_collection( $folders ){
  $folders[] = plugin_dir_path( __FILE__ ).'widgets/';
  return $folders;
}
add_filter( 'siteorigin_widgets_widget_folders','widgets_collection' );



// Banner Image for widgets
function banner_img( $banner_url, $widget_meta ) {
    if( in_array( $widget_meta['ID'], array( 'so-orbit-query' )  ) ) {
        $banner_url = plugin_dir_url(__FILE__).'assets/banner.svg';
    }
    return $banner_url;
}

add_filter( 'siteorigin_widgets_widget_banner', 'banner_img', 10, 2);

?>
