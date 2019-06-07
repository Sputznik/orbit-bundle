<?php
/**
* COPIED THE CODEBASE FROM https://github.com/samvthom16/Orbit-batch-process
* BATCH PROCESSING BY SENDING HTTP REQUESTS THROUGH AJAX
*/

class ORBIT_BATCH_PROCESS extends ORBIT_SHORTCODE{

	var $slug;

	function __construct(){

		$this->shortcode = 'orbit_batch_process';

    /* AJAX CALLBACK */
		add_action( 'wp_ajax_'.$this->shortcode, array( $this, 'ajax' ) );

		/* SAMPLE ACTION HOOK FOR AJAX CALL */
		add_action('space_batch_action_default', function(){

			$users = array( 'Samuel', 'Jay', 'Dennis', 4, 5, 6, 7, 8, 9, 10 );

			echo $users[ $_GET['space_batch_step'] - 1 ];

			// echo "AJAX ".$_GET['space_batch_step']." ".$_GET['space_batch_action'];

		});

		wp_register_script( 'orbit-batch-process', plugins_url( 'orbit-bundle/dist/js/batch-process.js' ), array( 'jquery' ), ORBIT_BUNDLE_VERSION, true );

		parent::__construct();

	}

	/* SHORTCODE FUNCTION */
  function plain_shortcode( $atts, $content = false ){

    ob_start();

		/* CREATE ATTS ARRAY FROM DEFAULT PARAMETERS IN THE SHORTCODE */
		$atts = shortcode_atts( array(
			'result'				=> 'Entire process is completed',
			'title'					=> 'Title of the process',
			'desc'					=> 'Description of the process',
			'batches' 			=> '10',
			'btn_text' 			=> 'Process Request',
			'batch_action' 	=> 'default',
			'auto'					=> '1',
			'params'		=> array()
		), $atts, $this->slug );

		$url = admin_url( 'admin-ajax.php' ) . '?action=' . $this->shortcode;

		_e( "<div data-atts='".wp_json_encode( $atts )."' data-url='".$url."' data-behaviour='orbit-batch'></div>");

    return ob_get_clean();

	}

	function enqueue_assets(){
		// LOAD THE MAIN STYLE IF IT HAS NOT BEEN LOADED YET
		wp_enqueue_style( 'orbit-main' );
		wp_enqueue_script( 'orbit-batch-process' );
	}

	/* AJAX CALLBACK */
	function ajax(){

		if( isset( $_GET['orbit_batch_action'] ) ){
			do_action('orbit_batch_action_'.$_GET['orbit_batch_action']);
		}

		wp_die();
	}
}
// CREATE AN INSTANCE FOR THE AJAX CALLBACK TO BE HANDLED
ORBIT_BATCH_PROCESS::getInstance();
