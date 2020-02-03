<?php

  $cpt_post_id = isset( $_GET['id'] ) ? $_GET['id'] : 0;

  if( !$cpt_post_id ){ return ''; }

  $orbit_filter = ORBIT_FILTER::getInstance();

  $form_atts = $orbit_filter->vars();

  $form_options = array_merge( $form_atts['forms'], $form_atts['tax_forms'] );

  $terms = ORBIT_WP::getInstance()->get_post_terms( $cpt_post_id, 'orbit_taxonomy' );

  //ORBIT_UTIL::getInstance()->test( $form_options );

  //ORBIT_UTIL::getInstance()->test( $terms );


  $form_pages = array(
    array(
      'page_title' => 'Page 1',
      'fields'     => array(
        array(
          'class'     => 'orbit-hidden-field',
          'label'     => 'Give a name to your data type',
          'type'      => 'post',
          'typeval'   => 'title',
          'form'      => 'multiple-text',
          'required'  => 1,
          'help'      => 'Examples: service providers, resources, posts, etc',
          'value'     => "Filters for " . get_the_title( $cpt_post_id )
        )
      )
    )
  );

  $i = 0;
  foreach ($terms as $term) {
    $temp_arr = array(
      'type'    => 'section',
      'html'    => '<h3>Choose filter options for '.$term->name.'</h3>',
      'fields'  => array(
        array(
          'label'     => "Give a label for the filter",
          'type'      => 'cf',
          'required'  => 1,
          'name'      => "orbit_filters[$i][label]",
          'form'      => 'text',
          'value'     => $term->name
        ),
        array(
          'label'     => "Choose a form option",
          'type'      => 'cf',
          'required'  => 1,
          'name'      => "orbit_filters[$i][form]",
          'form'      => 'dropdown',
          'options'     => $form_options
        ),
        array(
          'class'     => 'orbit-hidden-field',
          'label'     => "Slug",
          'type'      => 'cf',
          'required'  => 0,
          'name'      => "orbit_filters[$i][typeval]",
          'form'      => 'text',
          'value'     => $term->slug
        ),
        array(
          'class'     => 'orbit-hidden-field',
          'label'     => "Type",
          'type'      => 'cf',
          'required'  => 0,
          'name'      => "orbit_filters[$i][type]",
          'form'      => 'text',
          'value'     => 'tax'
        ),
      )
    );
    array_push( $form_pages[0]['fields'], $temp_arr );
    $i++;
  }

$orbit_fep = ORBIT_FEP::getInstance();

$settings = array(
  'post_type'         => 'orbit-form',
  'post_status'       => 'publish',
  'form_success_msg'  => 'Your form was submitted successfully'
);

$orbit_fep->create( $form_pages, $settings );
