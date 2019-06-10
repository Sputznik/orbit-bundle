<?php

class ORBIT_FORM_FIELD extends ORBIT_BASE{

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
      if( isset( $atts['label'] ) && $atts['label'] && $atts['type'] != 'bt_dropdown_checkboxes' ){
        _e("<label>". $atts['label'] ."</label>");
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
