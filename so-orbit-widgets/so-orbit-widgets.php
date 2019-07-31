<?php

function widgets_collection( $folders ){
  $folders[] = plugin_dir_path( __FILE__ ).'widgets/';
  return $folders;
}
add_filter( 'siteorigin_widgets_widget_folders','widgets_collection' );
?>
