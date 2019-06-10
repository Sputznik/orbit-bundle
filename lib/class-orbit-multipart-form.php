<?php
/*
* UTIL CLASS FOR MULTIPART FORM
*/
class ORBIT_MULTIPART_FORM extends ORBIT_BASE{

  function __construct(){
    wp_register_script( 'orbit-slides', plugins_url( 'orbit-bundle/dist/js/orbit-slides.js' ), array( 'jquery' ), ORBIT_BUNDLE_VERSION, true );
  }

  function enqueue_assets(){
    // LOAD THE MAIN STYLE IF IT HAS NOT BEEN LOADED YET
    wp_enqueue_style( 'orbit-main' );
		wp_enqueue_script( 'orbit-slides' );
	}

  // DISPLAY INLINE SECTION THAT COMPRISES OF MULTIPLE FIELDS
  function display_inline_section( $section ){
    $section['class'] = isset( $section['class'] ) ? $section['class']." " : "";
    $section['class'] .= "inline-section";

    echo "<div class='" . $section['class'] . "'>";

    // IF THERE IS ANY DESCRIPTIVE TEXT
    if( isset( $section['html'] ) ){ _e( "<div>".$section['html']."</div>" ); }

    // NESTED FIELDS WITHIN THE SECTION
    echo "<div class='section-fields'>";
    foreach( $section['fields'] as $field ){

      // SETTING UP ABILITY TO OVERRIDE THE DEFAULT WAY TO DISPLAY THE FIELD
      $custom_function = 'default';
      $custom_function = apply_filters( 'orbit-mf-field', $custom_function, $field );

      if( $custom_function == 'default' ){
        $this->display_field( $field );
      }
      else{
        call_user_func( $custom_function, $field );
      }


    }
    echo "</div></div>";
  }

  // MAIN FUNCTION - CREATES ORBIT SLIDES WHICH FURTHER DISPLAYS INLINE SECTIONS AND FIELDS
  function create( $pages, $buttons = array( 'prev_text' => "Previous", 'next_text'	=> "Next", 'submit_text'	=> "Submit" ) ){

    // TOTAL NUMBER OF MULTI PARTS FORMS - SECTIONS
    $no_sections = count( $pages );

    echo "<div class='orbit-slides' data-behaviour='orbit-slides'>";

    echo "<div class='orbit-form-progress'></div>";

    $i = 0;

    // CREATE N NUMBER OF SLIDES BASED ON THE TOTAL SLIDES PASSED AS ARGUMENT
    foreach( $pages as $page ){
      echo "<section class='orbit-slide'>";

      // DISPLAY INLINE SECTION
      $this->display_inline_section( $page );

      // CREATE NAVIGATION BUTTONS WITHIN THE SLIDE
      _e( "<ul class='orbit-list-inline'>" );

      // HIDE IN THE FIRST PAGE OF THE FORM
      if( $i ){ _e( "<li><button data-behaviour='orbit-slide-prev'>" . $buttons['prev_text'] . "</button></li>" ); }

      // IN THE LAST FORM, THE TEXT SHOULD CHANGE TO SUBMIT OTHERWISE IT SHOULD BE SIMPLY NEXT
      if( $i != $no_sections - 1 ){
        _e( "<li><button data-behaviour='orbit-slide-next'>" . $buttons['next_text'] . "</button></li>" );
      }
      else{
        _e( "<li><button type='submit'>" . $buttons['submit_text'] ."</button></li>" );
      }

      _e( "</ul>" );

      echo "</section>";

      $i++;
    }

    echo "<div class='orbit-form-alert'></div>";

    echo "</div>";

  }

  // DISPLAYING A FIELD WITHIN AN INLINE SECTION
  function display_field( $field ){

    $options = array();

    switch( $field['type'] ){
      // FOR NESTED FIELDS
      case 'section':
        $this->display_inline_section( $field );
        break;

      // FOR CUSTOM FIELDS
      case 'cf':
        // ITERATE THE USER DEFINED OPTIONS INTO THE COMPATIBLE FORM OF OPTIONS
        if( isset( $field['options'] ) && is_array( $field['options'] ) && count( $field['options'] ) ){
          foreach( $field['options'] as $option ){
            array_push( $options, array( 'slug' => $option, 'name' => $option['value'] ) );
          }
        }

        // UPDATE TYPEVAL FOR CUSTOM FIELDS WITH THE POST META NAME
        $field['typeval'] = $field['name'];

        break;
      // FOR POST INFORMATION
      case 'post':

        switch( $field['typeval'] ){
          case 'content':
            $field['form'] = 'textarea';
            break;

          case 'date':
            $field['form'] = 'date';
            break;

          case 'files':
            $field['form'] = 'images';
            break;

          default:
            $field['form'] = 'text';
        }
        break;

      // FOR TAXONOMY TERMS
      case 'tax':
        // GET ALL THE TAXONOMY TERMS INCLUDING THE EMPTY ONES
        $tax_terms = get_terms( array(
          'taxonomy'    => $field['typeval'],
          'hide_empty'  => false
        ) );

        // ITERATE AND ADD TO OPTIONS ARRAY
        foreach( $tax_terms as $term ){
          array_push( $options, array( 'slug' => $term->term_id, 'name' => $term->name, ) );
        }
        break;
      default:

    }

    // USING THE HELPER CLASS PROVIDED BY ORBIT BUNDLE
    $orbit_form_field = new ORBIT_FORM_FIELD;
    $orbit_form_field->display( array(
      'name'      => $field['type'].'_'.$field['typeval'],  // NAME ATTRIBUTE FOR THE INPUT FIELD - this clearly identifies if the field is postfield, taxonomy or custom field
      'type'      => $field['form'],
      'label'     => $field['label'],
      'required'  => isset( $field['required'] ) ? $field['required'] : false,
      'items'     => $options
    ) );

  }

}


ORBIT_MULTIPART_FORM::getInstance();
