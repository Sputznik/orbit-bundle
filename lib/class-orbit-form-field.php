<?php

class ORBIT_FORM_FIELD extends ORBIT_BASE{

  function __construct(){

    // OVERRIDING THE NORMAL BEHAVIOUR OF THE FORM FIELD
    // USED IN SPECIAL CASES OF COMPLEX FORM FIELDS THAT INCLUDES JAVASCRIPT
    add_filter( 'orbit-filter-field', function( $custom_function, $atts ){
      if( $atts['type'] == 'tax' && $atts['form'] == 'nested_dropdowns' ){
        $custom_function = function( $atts ){
          include( 'complex-form-fields/nested_dropdowns.php' );
        };
      }
      elseif( $atts['type'] == 'tax' && $atts['form'] == 'dropdown_with_checkboxes' ){
        $custom_function = function( $atts ){
          include( 'complex-form-fields/dropdown_with_checkboxes.php' );
        };
      }
      return $custom_function;
    }, 2, 10 );

  }

  // GETTING CATEGORIES AND SUBCATEGORIES SEPERATELY FOR A Taxonomy
  // USEFUL FOR LOCATIONS
  function getNestedTerms( $atts ){
    global $orbit_wp_query;
    $current_post_types = $orbit_wp_query->query['post_type'];

    $data = array( 'cats' => array(), 'subcats' => array() );

    $args = array(
      'taxonomy'    => $atts['typeval'],
      'hide_empty'  => $atts['tax_hide_empty'] == 1 ? true : false,
      'orderby'     => 'term_id'
    );

    $terms = get_terms( $args );

    // CHECK IF POST TYPE IS NOT EMPTY
    if( $current_post_types && !( count( $current_post_types ) > 1 ) ){
      $terms = apply_filters('orbit_filter_nested_terms', $terms, $args, $current_post_types );
    }

    foreach ( $terms as $term ) {
      if( $term->parent ){
        array_push( $data['subcats'], array(
          'name'    => $term->name,
          'slug'    => $term->term_id,
          'parent'  => $term->parent
        ) );
      }
      else{
        array_push( $data['cats'], array(
          'name'    => $term->name,
          'slug'    => $term->term_id,
          'parent'  => $term->parent
        ) );
      }
    }

    //Sort the categories and sub-categories alphabetically
    usort( $data['cats'], array( $this, 'locationByName' ) );
    usort( $data['subcats'], array( $this, 'locationByName' ) );

    return $data;
  }


  function locationByName( $a, $b ) {
    return strcmp( $a["name"], $b["name"] );
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
