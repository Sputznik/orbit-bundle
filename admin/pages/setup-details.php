<?php

$cpt_post_id = isset( $_GET['id'] ) ? $_GET['id'] : 0;

$filter_post_id = isset( $_GET['filter_id'] ) ? $_GET['filter_id'] : 0;

if( !$cpt_post_id || !$filter_post_id ){ return ''; }



?>

<h3>Please find the relevant links below:</h3>
<ul style="margin-top: 20px;">
  <li><a href="<?php _e( admin_url( "post.php?post=$cpt_post_id&action=edit" ) );?>" target="_blank">Edit Datatype</a></li>
  <li><a href="<?php _e( admin_url( "post.php?post=$filter_post_id&action=edit" ) );?>" target="_blank">Edit Filters</a></li>
</ul>
