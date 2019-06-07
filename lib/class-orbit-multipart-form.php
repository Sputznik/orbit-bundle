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

  function create( $no_sections, $callback_func, $buttons = array( 'prev_text' => "Previous", 'next_text'	=> "Next", 'submit_text'	=> "Submit" ) ){

    echo "<div class='orbit-slides' data-behaviour='orbit-slides'>";

    // CREATE N NUMBER OF SLIDES BASED ON THE TOTAL SLIDES PASSED AS ARGUMENT
    for( $i = 0; $i < $no_sections; $i++ ){
      echo "<section class='orbit-slide'>";

      // CALLBACK FUCNTION TO EXECUTE MORE DETAILED WITHIN THE SLIDE
      call_user_func( $callback_func, $i );

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
    }

    echo "</div>";

  }



}


ORBIT_MULTIPART_FORM::getInstance();
