<?php

$form_pages = array(
  array(
    'page_title' => 'Page 1',
    'fields'     => array(
      array(
        'label' => 'Give a name to your data type',
        'type'  => 'post',
        'typeval' => 'title',
        'form'    => 'multiple-text',
        'required'  => 1,
        'help'      => 'Examples: service providers, resources, posts, etc'
      ),
      array(
        'label' => 'Add Categories',
        'type'  => 'tax',
        'required'  => 1,
        'typeval' => 'orbit_taxonomy',
        'form' => 'multiple-text',
      ),
      array(
        'label'     => '',
        'type'      => 'cf',
        'name'      => 'custom_fields',
        'form'      => 'repeater_cf',
      ),
    )
  )
);

$orbit_fep = ORBIT_FEP::getInstance();

$settings = array(
  'post_type'         => 'orbit-types',
  'post_status'       => 'publish',
  'form_success_msg'  => 'Your form was submitted successfully'
);



$orbit_fep->create( $form_pages, $settings, function( $new_post_id ){
  if( $new_post_id ){
    $url = admin_url("admin.php?page=orbit-setup&action=filters&id=$new_post_id");
    echo "<script>window.location.href = '$url';</script>";
  }
} );
