<?php

class ORBIT_CSV extends ORBIT_BASE{

	function __construct(){

    /* SAMPLE ACTION HOOK FOR AJAX CALL */
    add_action('orbit_batch_action_import_terms', function(){

      $path = stripslashes( $_GET['file'] );
			$arrayCsv = $this->toArray( $path );

			echo $_GET['per_page'];

			$offset = ( $_GET['orbit_batch_step'] - 1 ) * $_GET['per_page'];
			if( ! $offset ){ $offset = 1; }
			$selected_array_csv = array_slice( $arrayCsv, $offset, $_GET['per_page'] );

			echo "<pre>";
			print_r( $selected_array_csv );
			echo "</pre>";

			$this->syncTerms( $selected_array_csv, $_GET['taxonomy'] );

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
	function syncTerm( $text, $taxonomy, $parent = 0 ){
		$term = term_exists( $text, $taxonomy, $parent );

		if( !$term ){

			// TERM DOES NOT EXIST, SO CREATE NEW TERM
			$term = wp_insert_term( $text, $taxonomy, array( 'parent' => $parent ) );
		}

		if( isset( $term['term_id'] ) ){ return $term['term_id']; }
		return 0;
	}

	function resetTerms( $taxonomy ){
		$terms = get_terms( $taxonomy, array( 'fields' => 'ids', 'hide_empty' => false ) );
		foreach ( $terms as $value ) {
			wp_delete_term( $value, $taxonomy );
		}
	}


	function syncTerms( $arrayCsv, $taxonomy ){

		foreach( $arrayCsv as $rowCsv ){

			$parent_id = $this->syncTerm( $rowCsv[0], $taxonomy );

			if( $parent_id && count( $rowCsv ) > 1 ){ $this->syncTerm( $rowCsv[1], $taxonomy, $parent_id ); }


		}

	}

}

ORBIT_CSV::getInstance();
