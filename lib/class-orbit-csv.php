<?php

class ORBIT_CSV extends ORBIT_BASE{

	function __construct(){

    /* SAMPLE ACTION HOOK FOR AJAX CALL */
    add_action('orbit_batch_action_import_terms', function(){

      $path = stripslashes( $_GET['file'] );

      //$arrayCsv = $this->toArray( $path );

      //print_r( count( $arrayCsv ) );
      
      echo $this->numRows( $path );

      echo "<pre>";
      print_r( $_GET );
      echo "</pre>";



      $users = array( 'Samuel', 'Jay', 'Dennis', 4, 5, 6, 7, 8, 9, 10 );

      echo $users[ $_GET['orbit_batch_step'] - 1 ];

      // echo "AJAX ".$_GET['space_batch_step']." ".$_GET['space_batch_action'];

    });

	}

  function numRows( $path ){
    $arrayCsv = $this->toArray( $path );
    return count( $arrayCsv );
  }

	function toArray( $path ){
		$file = fopen( $path, "r" );
		$arrayCsv = array();
		while( !feof( $file ) ) {
			$fpTotal = fgetcsv( $file );
			array_push( $arrayCsv, $fpTotal );
		}
		fclose( $file );
		return $arrayCsv;
	}

	// CHECK IF THE TERM EXISTS, IF NOT CREATE A NEW TERM
	function getTermID( $text, $taxonomy, $parent = 0 ){
		$term = term_exists( $text, $taxonomy, $parent );

		if( !$term ){


			// TERM DOES NOT EXIST, SO CREATE NEW TERM
			$term = wp_insert_term( $text, $taxonomy, array( 'parent' => $parent ) );

		}



		if( isset( $term['term_id'] ) ){ return $term['term_id']; }
		return 0;
	}
	/*
	function reset_locations(){
		$terms = get_terms( 'locations', array( 'fields' => 'ids', 'hide_empty' => false ) );
		foreach ( $terms as $value ) {
			wp_delete_term( $value, 'locations' );
		}
	}
	*/

	function import_locations(){

		$csv_path = get_stylesheet_directory().'/lib/cpt/csv/locations.csv';

		$this->syncTerms( $csv_path, 'locations' );

	}

	function import_categories(){

		$csv_path = get_stylesheet_directory().'/lib/cpt/csv/categories.csv';

		$this->syncTerms( $csv_path, 'report-type' );

	}

	function import_victims(){

		$csv_path = get_stylesheet_directory().'/lib/cpt/csv/victims.csv';

		$this->syncTerms( $csv_path, 'victims' );

	}

	function syncTerms( $csv_path, $taxonomy ){

		$arrayCsv = $this->exportToArray( $csv_path );

		$i = 0;
		foreach( $arrayCsv as $rowCsv ){

			if( $i ){

				$parent_id = $this->getTermID( $rowCsv[0], $taxonomy );

				if( $parent_id && count( $rowCsv ) > 1 ){
					$this->getTermID( $rowCsv[1], $taxonomy, $parent_id );
				}

				echo '<pre>';
				print_r( $rowCsv );
				echo '</pre>';
			}
			$i++;
		}

		wp_die();
	}

}

ORBIT_CSV::getInstance();
