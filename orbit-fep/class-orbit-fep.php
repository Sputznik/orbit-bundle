<?php


class ORBIT_FEP extends ORBIT_BASE{

  function __construct(){

    // ADD TO THE ORBIT MENU IN THE BACKEND
    add_filter( 'orbit_admin_menus', function( $menus ){
      $menus[ 'orbit-fep' ] = array(
        'label'	=> 'Orbit FEP',
        'url'		=> 'edit.php?post_type=orbit-fep'
      );
      return $menus;
    } );

    // CREATE CUSTOM POST TYPE WHERE THE FORM FIELDS CAN BE ADDED
    add_filter( 'orbit_post_type_vars', function( $post_types ){
      $post_types['orbit-fep'] = array(
        'slug' 		=> 'orbit-fep',
        'labels'	=> array(
          'name' 					=> 'Orbit Fep',
          'singular_name' => 'Orbit Fep',
        ),
        'public'		=> true,
        'supports'	=> array( 'title' )
      );
      return $post_types;
    } );

    add_filter( 'orbit_meta_box_vars', array( $this, 'createMetaBox' ) );

    // SEPERATE METABOX FOR FILTERS ONLY
    add_action( 'orbit_meta_box_html', array( $this, 'metaboxForFEP' ), 1, 2 );

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

    add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );

    add_action( 'save_post', array( $this, 'save_post' ) );

    //SHORTCODE
    add_shortcode( 'orbit_fep', array( $this, 'shortcode' ) );


    // SAMPLE EXAMPLE OF OVERRIDING FIELD WITHIN THE MULTIPART
    add_filter( 'orbit-mf-field', function( $custom_function, $field ){

      if( isset( $field['type'] ) && $field['type'] == 'tax' &&
        isset( $field['typeval'] ) && $field['typeval'] == 'locations' &&
        isset( $field['form'] ) && $field['form'] == 'dropdown' ){

        $custom_function = function( $field ){

          $locations = get_terms( array(
            'taxonomy'    => $field['typeval'],
            'hide_empty'  => false
          ) );


          $states = array();
          $districts = array();
          foreach ( $locations as $location ) {
            if( $location->parent ){
              array_push( $districts, array(
                'slug'    => $location->term_id,
                'name'    => $location->name,
                'parent'  => $location->parent
              ) );
            }
            else{
              array_push( $states, array(
                'slug'    => $location->term_id,
                'name'    => $location->name,
                'parent'  => $location->parent
              ) );
            }
          }

          // USING THE HELPER CLASS PROVIDED BY ORBIT BUNDLE
          $orbit_form_field = new ORBIT_FORM_FIELD;

          $orbit_form_field->display( array(
            'name'  => 'state',
            'type'  => $field['form'],
            'label' => 'Select State',
            'items' => $states
          ) );

          $orbit_form_field->display( array(
            'name'  => 'district',
            'type'  => $field['form'],
            'label' => 'Select District',
            'items' => $districts
          ) );


        };

      }

      return $custom_function;
    }, 10, 2 );
  }

  /*
  * METABOX TO CREATE
  * FORM FIELDS IN MULTIPART FORM
  * SETTINGS: POST TYPE AND STATUS
  */
  function createMetaBox( $meta_box ){
    global $post_type;

    if( 'orbit-fep' != $post_type ) return $meta_box;

    // POST STATUS

    $meta_box['orbit-fep'] = array(
      array(
        'id'		=> 'orbit-fep-pages',
        'title'		=> 'Orbit Form Fields',
        'fields'	=> array()
      ),
      array(
        'id'      =>  'orbit-fep-settings',
        'title'   =>  'Settings',
        'fields'  =>  array(
          'posttypes' => array(
            'type' 		=> 'dropdown',
            'text' 		=> 'Select Post Types',
            'options'	=> array()
          ),
          'poststatus' => array(
            'type' 		=> 'dropdown',
            'text' 		=> 'Select Post Status',
            'options'	=> get_post_statuses()
          ),
          'user_email' => array(
            'type' 		=> 'text',
            'text' 		=> 'Email',
            'options'	=> array()
          ),
          'form_success_msg' => array(
            'type' 		=> 'textarea',
            'text' 		=> 'Enter the message to be shown when the form gets submitted.',
            'options'	=> array()
          ),
        )
      ),
      array(
        'id'      =>  'orbit-fep-notification',
        'title'   =>  'Notification Settings',
        'fields'  =>  array(
          'to' => array(
            'type' 		=> 'text',
            'text' 		=> 'Email To',
          ),
          'subject' => array(
            'type' 		=> 'text',
            'text' 		=> 'Subject',
          ),
          'content' => array(
            'type' 		=> 'textarea',
            'text' 		=> 'Enter the content of the email',
            'help'    => 'Use variables like $post_title for title of the post, $post_date for date of the post, $post_edit_link for link of the post in the backend',
            'options'	=> array()
          ),
        ),
        'field_name'	=> 'fep_email'
      )
    );
    return $meta_box;
  }

  function metaboxForFEP( $post, $box ){
    if( isset( $box['id'] ) && 'orbit-fep-pages' == $box['id'] ){
      $orbit_filter = ORBIT_FILTER::getInstance();

      // FORM ATTRIBUTES THAT IS NEEDED BY THE REPEATER FILTERS
      $form_atts = $orbit_filter->vars();
      if( !$form_atts || !is_array( $form_atts ) ){ $form_atts = array(); }
      $form_atts['tax_types'] = get_taxonomies();

      $form_atts['db'] = $this->getDBData( $post->ID );

      //ADD A NEW TYPE INTO THE TYPES ARRAY
      $new_type = array(
        'post'    => 'Post',
        'cf'      => 'Custom Fields',
        'section' => 'Inline Section'
      );
      foreach( $new_type as $slug_type => $value_type ){ $form_atts['types'][$slug_type] = $value_type; }
      unset( $form_atts['types']['postdate'] );

      //WHEN TYPE IS POST
      $form_atts['post_types'] = array(
        'title'     =>  'Title',
        'content'   =>  'Description',
        'date'      =>  'Date',
        'files'     =>  'Attachments',
        'featured'  =>  'Featured Image',
      );

      //NEW FORM FIELDS
      $new_form_fields = array(
        'radio'         => 'Radio Buttons',
        'text'          => 'Input Text (single)',
        'multiple-text' => 'Multiple Input Text',
        'textarea'      => 'Textarea'
      );
      foreach( $new_form_fields as $slug => $value ){
        $form_atts['forms'][$slug] = $value;
      }

      // TRIGGER THE REPEATER FILTER BY DATA BEHAVIOUR ATTRIBUTE
      _e( "<div data-behaviour='orbit-fep-pages' data-atts='".wp_json_encode( $form_atts )."'></div>");// data-atts='".wp_json_encode( $form_atts )."'

    }
  }

  // GET THE FEP FORMS DATA STORED AS ARRAY IN POST META
  function getDBData( $post_id ){
    $data = get_post_meta( $post_id, 'fep', true );
    if( $data && is_array( $data ) ){
      return $data;
    }
    return array();
  }

  function sortFields( $section ){
    $fields = array();
    foreach( $section['fields'] as $field ){
      if( isset( $field['fields'] ) ){
        $field['fields'] = $this->sortFields( $field );
      }
      array_push( $fields, $field );
    }
    return $fields;
  }

  /*
  * TRIGGERED WHEN THE PUBLISH/UPDATE BUTTON IS CLICKED IN THE ADMIN PANEL
  * THIS IS WHERE THE FILTERS THAT ARE ADDED BY THE USER FROM THE ADMIN PANEL IS SAVED IN THE DB
  */
  function save_post( $post_id ){
    $post_type = get_post_type( $post_id );
    if ( "orbit-fep" != $post_type ) return;

    // SAVE FILTERS IN POST META
    if( isset( $_POST['fep'] ) && is_array( $_POST['fep'] ) ){

      // SORT ARRAY BY THE VALUE ORDER
      //$byOrder = array_column( $_POST['fep'], 'order' );
      //array_multisort( $byOrder, SORT_ASC, $_POST['fep'] );

      //echo "<pre>";
      //print_r( $fep );
      //echo "</pre>";

      $fep = array();

      foreach( $_POST['fep'] as $page ){
        if( isset( $page['fields'] ) ){
          $page['fields'] = $this->sortFields( $page );
        }
        array_push( $fep, $page );
      }

      //echo "<pre>";
      //print_r( $fep );
      //echo "</pre>";


      // SAVE
      update_post_meta( $post_id, 'fep', $fep );
    }
    // wp_die();
  }

  function shortcode( $atts ){

    $atts = shortcode_atts( array(
      'id'          =>  '0'     // POST ID
    ), $atts, 'fep-form' );

    ob_start();

    // GET FORM PAGES INFORMATION FROM THE METADATA IN ORBIT-FEP
    $fep_pages = $this->getDBData( $atts['id'] );

    //ORBIT_UTIL::getInstance()->test( $fep_pages );

    $new_post = array(
      'post_type'         => get_post_meta( $atts['id'], 'posttypes', true ),
      'post_status'       => get_post_meta( $atts['id'], 'poststatus', true ),
      'form_success_msg'  => get_post_meta( $atts['id'], 'form_success_msg',true )
    );

    $new_post_id = $this->create( $fep_pages, $new_post );

    if( $new_post_id && $_POST ){

      // SEND EMAIL AS NOTIFICATION WHEN THE POST HAS BEEN CREATED SUCCESSFULLY
      $this->sendMail( $atts['id'], $_POST, $new_post_id );
    }

    return ob_get_clean();
  }

  function create( $pages, $new_post = array(), $callback_func = false ){

    $new_post_id = 0;

    $form_success_flag = false;
    $success_message = isset( $new_post['form_success_msg'] ) ? $new_post['form_success_msg'] : "";

    // INSERT POST ONCE THE FORM HAS BEEN SUBMITTED
    if( $_POST ){
      $new_post_id = $this->insertPost( $new_post );

      // SEND EMAIL ON SUCCESSFUL FORM SUBMISSION
      if( $new_post_id ){
        $form_success_flag = true;
      }
    }

    // STARTING OF FORM TAG
    echo "<form class='orbit-fep' method='post' enctype='multipart/form-data'>";

    // USING THE ORBIT MULTIPART FORM TO CREATE SLIDES
    $orbit_multipart_form = ORBIT_MULTIPART_FORM::getInstance();
    $orbit_multipart_form->create( $pages );

    wp_nonce_field( 'orbit-fep' );
    // END OF FORM TAG
    echo "</form>";

    // DISPLAY MESSAGE ON FORM SUBMISSION
    if( $form_success_flag ){
      // echo "<div style='margin-top:50px;' class='form-alert'>" . $success_message . "</div>";
      //echo "<style>.soah-fep{ display:none; }</style>";
      $this->showMessage( $success_message );
      if( is_callable( $callback_func ) ) {
        call_user_func( $callback_func, $new_post_id );
      }
    }
    return $new_post_id;
  }
  //Message on form submission
  function showMessage( $success_message ){
		if( !empty( $success_message ) ){
			echo "<div style='margin-top:50px;' class='orbit-form-alert'>" . $success_message . "</div>";
		}
	}

  function validateFiles(){
    foreach( $_FILES as $key => $fileobject ){
      if( isset( $fileobject['name'] ) && is_array( $fileobject['name'] ) && count( $fileobject['name'] ) ){
        $total_files = count( $fileobject['name'] );
        for( $i=0; $i<$total_files; $i++ ){
          $temp_file = array();

          //$fields = array( 'name', 'type', 'tmp_name', 'error', 'size' );

          $fields = array_keys( $fileobject );
          foreach( $fields as $field ){
            if( isset( $fileobject[ $field ] ) && is_array( $fileobject[ $field ] ) && isset( $fileobject[ $field ][ $i ] ) ){
              $temp_file[ $field ] = $fileobject[ $field ][ $i ];
            }
          }
          $_FILES[ $key."_".$i ] = $temp_file;
        }
        unset( $_FILES[ $key ] );
      }
    }
  }

  function handleMediaUpload( $post_id, $data = array() ){
		if( is_array( $data ) ){
      require_once( ABSPATH . 'wp-admin/includes/image.php' );
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
      require_once( ABSPATH . 'wp-admin/includes/media.php' );

      foreach( $data as $key => $value ){
        $attachment_id = media_handle_upload( $key, $post_id, array( 'test_form'=> false ) );
        if( is_wp_error( $attachment_id ) ){
          // print_r( $attachment_id );
        }
				elseif ( $key == 'post_featured' ) {
					set_post_thumbnail( $post_id, $attachment_id );
				}
      }
    }
	}


  function insertPost( $post_info ){

    wp_verify_nonce( $_REQUEST['_wpnonce'], 'orbit-fep' );

    // ADD POST RELATED INFORMATION TO AN ARRAY
    $post_fields_arr = array( 'post_title', 'post_content', 'post_date' );
    foreach( $post_fields_arr as $post_field ){
      if( isset( $_POST[ $post_field ] ) ){
        $post_info[ $post_field ] = $_POST[ $post_field ];
      }
    }

    if( isset( $post_info['post_title'] ) && !empty( $post_info['post_title'] ) ){
      $post_id = wp_insert_post( $post_info );
    }
    else{
      $post_id = 0;
    }

    // IF POST ID IS NOT VALID THEN RETURN ERROR
    if( !$post_id || is_array( $post_id ) ){ print_r( $post_id );return 0; }

    // INCASE THERE ARE OVERRIDING FIELDS
    do_action( 'orbit-fep-after-save' );

    // ONLY IF POST ID IS VALID - ensures that the above insert was successfull
    foreach( $_POST as $slug => $value ){
      // ADDING TERMS TO THE NEW POST
      if( strpos( $slug, 'tax_') !== false ){

        $taxonomy = str_replace( "tax_", "", $slug );

        // CHECK IF THE VALUE IS TERM ID OR TERM NAME
        if( is_array( $value ) && count( $value ) && 'id' != ORBIT_UTIL::getInstance()->getVariableType( $value[0] ) ){
          $term_ids = array();

          foreach ( $value as $term_name ) {
            // CHECK IF THE TERM EXISTS
            $term = term_exists( $term_name, $taxonomy );

            // print_r( $term );

            // IF IT DOES NOT EXIST THEN CREATE ONE
            if( !$term ){ $term = wp_insert_term( $term_name, $taxonomy ); }

            // IF EVERYTHING IS FINE THEN ADD TO THE ARRAY
            if( isset( $term->term_id ) ){
              array_push( $term_ids, $term->term_id );
            }
            elseif ( isset( $term['term_id'] ) ) {
              array_push( $term_ids, $term['term_id'] );
            }
            else{
              print_r( $term );
            }

          }
          $value = $term_ids;
        }
        //echo $post_id;
        //print_r( $value );
        wp_set_post_terms( $post_id, $value, $taxonomy );
      }
      elseif( strpos( $slug, 'cf_') !== false ){

        //print_r( $slug );
        //print_r( $value );


        // ADDING CUSTOM META VALUES TO THE POST
        $meta_name = str_replace( "cf_", "", $slug );

        //print_r( $meta_name );

        update_post_meta( $post_id, $meta_name, $value );

        //wp_die();
      }
    }

    if( $_FILES ){
      $this->validateFiles();
      $this->handleMediaUpload( $post_id, $_FILES );
      // $this->handleFeaturedImage( $post_id, $_FILES );
    }

    return $post_id;

  } // END OF FUNCTION


  function sendMail( $fep_id, $form_info, $new_post_id ){

    $post = get_post( $new_post_id );

    if ( ( $post instanceof WP_Post ) && isset( $settings['to'] ) && !empty( $settings['to'] ) && isset( $settings['content'] )
    && isset( $settings['subject'] ) ){
      // POST TITLE
      $post_title = $post->post_title;

      // POST DATE
      $post_date = $post->post_date;

      // CONTAINS THE LINK TO EDIT THE POST
      $post_edit_link = html_entity_decode( get_edit_post_link( $new_post_id ), ENT_QUOTES, 'UTF-8' );

			// FEP SETTINGS
      $settings = get_post_meta( $fep_id, 'fep_email', true );

      $email_content = $settings['content'];
      eval("\$email_content = \"$email_content\";");

 			$email_subject = $settings['subject'];
			eval("\$email_subject = \"$email_subject\";");

			$mail = wp_mail( $settings['to'], $email_subject, $email_content );
		}
  }

  function assets(){
    $orbit_multipart_form = ORBIT_MULTIPART_FORM::getInstance();
    $orbit_multipart_form->enqueue_assets();
  }

  function admin_assets(){
    wp_enqueue_script( 'orbit-fep-pages', plugins_url( '/orbit-bundle/dist/js/repeater-pages.js' ), array('jquery', 'orbit-repeater' ), ORBIT_BUNDLE_VERSION, true );
    wp_enqueue_script( 'orbit-fields', plugins_url( '/orbit-bundle/dist/js/repeater-fields.js' ), array('jquery', 'orbit-repeater' ), ORBIT_BUNDLE_VERSION, true );
    wp_enqueue_script( 'orbit-options-repeater', plugins_url( '/orbit-bundle/dist/js/repeater-options.js' ), array('jquery', 'orbit-repeater' ), ORBIT_BUNDLE_VERSION, true );

    $orbit_multipart_form = ORBIT_MULTIPART_FORM::getInstance();
    $orbit_multipart_form->enqueue_assets();

    // NEEDED FOR THE RICH TEXT EDITOR IN THE FIELDS REPEATER
    wp_enqueue_editor();
  }

}//class ends

ORBIT_FEP::getInstance();
