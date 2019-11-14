<?php

class ORBIT_EXPORT extends ORBIT_BASE{

  function __construct(){

    // SHORTCODE
    add_shortcode( 'orbit_export', array( $this, 'form' ) );

    // ENQUEUE ASSETS
    add_action( 'wp_enqueue_scripts', array( $this, 'assets') );

    /* ACTION HOOK FOR AJAX CALL - import terms */
    add_action('orbit_batch_action_orbit_export', function(){

      $orbit_csv = ORBIT_CSV::getInstance();
      $orbit_search = ORBIT_SEARCH::getInstance();

			// GET PARAMETERS
			$step = $_REQUEST['orbit_batch_step'];
			$file_slug = $_REQUEST['file_slug'];
      $form_id = $_REQUEST['id'];

      $headers = $this->getExportHeaders( $form_id );
      $headerInfo = $orbit_csv->getHeaderInfo( array( $headers ) );
      $settings = $orbit_search->getSettings( $form_id );

      // GET QUERY ARGS
      $query_args = $this->getQueryArgs( $_REQUEST, $settings, $_REQUEST['orbit_batch_step'] );

      // ADD HEADER ROW FOR THE FIRST BATCH REQUEST ONLY
			if( $step == 1 ){
				$orbit_csv->addHeaderToCSV( $file_slug, $header );
			}

      $orbit_csv->exportPosts( $file_slug, $headerInfo, $query_args );
    });

  }

  function getExportHeaders( $form_id ){
    $orbit_search = ORBIT_SEARCH::getInstance();
    $orbit_csv = ORBIT_CSV::getInstance();

    $headers = array();
    $cols = $orbit_search->getExportColsFromDB( $form_id );

    foreach( $cols as $col ){
      $new_column = $col['type'] . "_" . $col['field'];
      array_push( $headers, $new_column );
    }

    return $orbit_csv->prepareHeaderForExport( $headers );
  }



  // ENQUEUE ASSETS ONLY WHEN THE SHORTCODE IS PRESENT
  function assets(){
    global $post;

    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'orbit_export') ) {

      // BATCH PROCESS ENQUEUE ASSETS
      $batch_process = ORBIT_BATCH_PROCESS::getInstance();
      $batch_process->enqueue_assets();
    }
  }

  // WRAPPER FUNCTION TO WPDB QUERY FOR REPORTS
	function getQueryArgs( $params, $filter_settings, $paged = 1 ){

		$orbit_util = ORBIT_UTIL::getInstance();

		$query_args = array(
			'posts_per_page' => $filter_settings['posts_per_page'],
			'post_type'			 => $filter_settings['posttypes'],
			'post_status'		 => 'publish',
			'paged'					 => $paged
		);

		if( isset( $params['tax'] ) && ( !empty( $params['tax'] ) ) ){
      $query_args['tax_query'] = $orbit_util->getTaxQueryParams( $params['tax'] );
		}

		if( isset( $params['date'] ) && ( !empty( $params['date'] ) ) ){
			$query_args['date_query'] = $orbit_util->getDateQueryParams( $params['date'] );
		}

		return $query_args;

	}

  function getQuery( $params, $settings ){
    $query_args = $this->getQueryArgs( $params, $settings );
    return new WP_Query( $query_args );
  }

  function form($atts){

    ob_start();

    $orbit_search   = ORBIT_SEARCH::getInstance();
    $orbit_csv      = ORBIT_CSV::getInstance();
    $orbit_util     = ORBIT_UTIL::getInstance();
    $batch_process  = ORBIT_BATCH_PROCESS::getInstance();

    $orbit_search->filters_form( $atts['id'] );

    if( $_GET && count( $_GET ) > 1 ){

      $filter_settings = $orbit_search->getSettings( $atts['id'] );

      // KEEP THE NAME OF THE FILE DYNAMIC
      $file_slug = 'mv-data-'.time();

      // NEED TO PASS THIS INFORMATION TO THE MODAL TO DOWNLOAD WHEN THE PROCESS IS COMPLETED
      $filePath = $orbit_csv->getFilePath( $file_slug );

      // HAS TWO ITEMS IN THE ARRAY: TAX AND DATE
      $batch_params = $orbit_util->paramsToString( $_GET );

      $total_posts = $this->getQuery( $batch_params, $filter_settings )->found_posts;                                    // TOTAL NUMBER OF POSTS FOUND IN THE QUERY ARGS PASSED
      $batches = (int) ( $total_posts / $filter_settings['posts_per_page'] );    // DYNAMICALLY CREATE THE NUMBER OF BATCHES
      if( !$batches ){ $batches = 1; }                                           // MINIMUM SHOULD BE 1

      // KEEPING THE CONTENTS URL SAFE SO THAT WE DON'T LOOSE ANY INFORMATION DURING THE TRANSFER
      //if( isset( $batch_params['tax'] ) ){ $batch_params['tax'] = urlencode( $batch_params['tax'] ); }

      // ADDING THE FILE SLUG INTO THE PARMAETERS THAT NEEDS TO BE PASSED
      $batch_params['file_slug'] = $file_slug;
      $batch_params['id'] = $atts['id'];

      // PROGRESS BAR TO SHOW THE BATCH PROCESSING OF EXPORTING POSTS INTO A CSV FILE
      echo $batch_process->plain_shortcode( array(
        'ajax_method' => 'POST',
        'result'      => '',
        'title'	      => 'Total Data: '.$total_posts.'. Please wait as the CSV is being exported.',
        'desc'			  => 'Make sure that your popups are enabled for this url or the browser will stop the download. Do not press the back button until the export completes.',
        'batches'		  => $batches,
        'btn_text' 		=> 'Export CSV',
        'batch_action'=> 'orbit_export',
        'params'		  => $batch_params
      ) );


    }

    return ob_get_clean();
  }

}

ORBIT_EXPORT::getInstance();
