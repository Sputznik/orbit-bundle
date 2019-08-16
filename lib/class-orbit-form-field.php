<?php

class ORBIT_FORM_FIELD extends ORBIT_BASE{

  function nested_dropdown( $atts ){

    $terms = get_terms( array(
      'taxonomy'    => $atts['typeval'],
      'hide_empty'  => false,
      'orderby'     => 'term_id'
    ) );

    $cats = array();
    $subcats = array();
    foreach ( $terms as $term ) {
      if( $term->parent ){
        array_push( $subcats, array(
          'name'    => $term->name,
          'slug'    => $term->term_id,
          'parent'  => $term->parent
        ) );
      }
      else{
        array_push( $cats, array(
          'name'    => $term->name,
          'slug'    => $term->term_id,
          'parent'  => $term->parent
        ) );
      }
    }

    $param = "tax_" . $atts['typeval'];
    $name_param = $param . "[]";
    $values = $_GET[ $param ];

    _e( "<div data-behaviour='orbit-nested-dropdown'>" );

    _e( "<div class='cats'>" );
    $this->display( array(
      'label'   => $atts['label'],
      'type'    => 'dropdown',
      'name'    => $name_param,
      'items'   => $cats,
      'value'   => is_array( $values ) ? $values[0] : ""
    ) );
    _e( "</div>" );

    _e( "<div class='subcats'>" );
    $this->display( array(
      'label'           => apply_filters( 'orbit-nested-dropdown-label', 'Select Sub', $atts ),
      'type'            => 'dropdown',
      'name'            => $name_param,
      'items'           => $subcats,
      'value'           => ( is_array( $values ) && count( $values ) > 1 ) ? $values[1] : ""
    ) );
    _e( "</div>" );

    _e( "</div>" );
  }

  function display( $atts = array( 'name' => '', 'value' => '', 'label' => '', 'type' => '', 'class' => '' ) ){

    if( isset( $atts['type'] ) ){

      // SETTING CLASS TO THE FIELD CONTAINER
      $default_class = "orbit-form-group field-".$atts['type'];
      $atts['class'] = isset( $atts['class'] ) ? $atts['class']." ".$default_class : $default_class;
      if( isset( $atts['required'] ) && $atts['required'] ){
        $atts['class'] .= ' orbit-field-required';
      }

      _e( "<div class='" . $atts['class'] . "'>" );

      // DISPLAY LABEL IF THERE IS ANY
      if( isset( $atts['label'] ) && $atts['label'] ){

        $atts['new_label'] = $atts['label'];

        if( isset( $atts['required'] ) && $atts['required'] ){
          $atts['new_label'] .= " <span>*</span>";
        }

        _e("<label>". $atts['new_label'] ."</label>");
      }

      // CHECK IF FORM VALUE IS NOT SET FOR CHECKBOXES THEN SET DEFAULT VALUE TO ARRAY
      switch( $atts['type'] ){
        case 'bt_dropdown_checkboxes':
        case 'checkbox':
          if( !isset( $atts['value'] ) || !is_array( $atts['value'] ) ){ $atts['value'] = array();}
          break;
      }

      $filter_form_dir = plugin_dir_path(__FILE__) . "form-fields/" . $atts['type'] . ".php";

      /* INCLUDE THE FILTER FORM */
      if( file_exists( $filter_form_dir ) ){ include( $filter_form_dir ); }

      // DISPLAY ANY SUBSEQUENT HELP INFORMATION HERE
      if( isset( $atts['help'] ) && $atts['help'] ){ _e("<p class='help'>".$atts['help']."</p>"); }

      _e("</div>");

    }








  }


}

ORBIT_FORM_FIELD::getInstance();
