<?php

class ORBIT_CSV extends ORBIT_BASE{

	function __construct(){

    /* ACTION HOOK FOR AJAX CALL - import terms */
    add_action('orbit_batch_action_import_terms', function(){

      $path = stripslashes( $_GET['file'] );
			$arrayCsv = $this->toArray( $path );

			$offset = ( $_GET['orbit_batch_step'] - 1 ) * $_GET['per_page'];
			if( ! $offset ){ $offset = 1; }
			$selected_array_csv = array_slice( $arrayCsv, $offset, $_GET['per_page'] );

			echo "<pre>";
			print_r( $selected_array_csv );
			echo "</pre>";

			$this->syncTerms( $selected_array_csv, $_GET['taxonomy'] );
		});

		/* ACTION HOOK FOR AJAX CALL - import posts */
    add_action('orbit_batch_action_import_posts', function(){

      $path = stripslashes( $_GET['file'] );
			$arrayCsv = $this->toArray( $path );

			$headerInfo = $this->getHeaderInfo( $arrayCsv );



			$offset = ( $_GET['orbit_batch_step'] - 1 ) * $_GET['per_page'];
			if( ! $offset ){ $offset = 1; }
			$selected_array_csv = array_slice( $arrayCsv, $offset, $_GET['per_page'] );

			echo "<pre>";
			//print_r( $headerInfo );
			//print_r( $selected_array_csv );
			echo "</pre>";

			$this->importPosts( $selected_array_csv, $headerInfo, array( 'post_status' => 'publish', 'post_type'  => $_GET['post_type'] ) );


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
	function syncTerm( $text, $taxonomy, $parent, $description ){
		$term = term_exists( $text, $taxonomy, $parent );

		if( !$term ){

			// TERM DOES NOT EXIST, SO CREATE NEW TERM
			$term = wp_insert_term( $text, $taxonomy, array( 'parent' => $parent, 'description'	=> $description ) );
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

			$parent_id = 0;

			$desc = "";

			// FIRST ADD THE PARENT OR GET THE ID IF IT ALREADY EXISTS
			if( is_array( $rowCsv ) && count( $rowCsv ) > 1 && $rowCsv[1] ){
				$parent_id = $this->syncTerm( $rowCsv[1], $taxonomy, 0, '' );
			}

			// ADD DESCRIPTION
			if( is_array( $rowCsv ) && count( $rowCsv ) > 2 && $rowCsv[2] ){
				$desc = $rowCsv[2];
			}

			if( $rowCsv[0] ){
				$term_id = $this->syncTerm( $rowCsv[0], $taxonomy, $parent_id, $desc );
			}


			//print_r( $term_id );
			//echo "<br>";

		}

	}

	function getHeaderInfo( $arrayCsv ){

		$headerInfo = array(
			'post_info' => array(),
			'tax_info'	=> array(),
			'cf_info'		=> array()
		);

		$headerCsv = $arrayCsv[0];

		$i = 0;
    foreach( $headerCsv as $col ){

      if ( strpos( $col, 'post_' ) !== false ) {
        $headerInfo['post_info'][ $col ] = $i;
      }
      elseif ( strpos( $col, 'tax_' ) !== false ) {
        $temp_col = explode( 'tax_', $col );
        $headerInfo['tax_info'][ $temp_col[1] ] = $i;
      }
      elseif( strpos( $col, 'cf_' ) !== false ){
        $temp_col = explode( 'cf_', $col );
        $headerInfo['cf_info'][ $temp_col[1] ] = $i;
      }

      $i++;
    }

		return $headerInfo;
	}

	function importPosts( $selectedCsv, $headerInfo, $defaults = array( 'post_status' => 'publish' ) ){

		foreach( $selectedCsv as $rowCsv ){

      $post_id = 0;

      // INSERT POST
      $new_post = array();
      foreach( $headerInfo['post_info'] as $slug => $value ){
        if( isset( $rowCsv[ $value ] ) ){
          $new_post[ $slug ] = $rowCsv[ $value ];
        }
      }
      $new_post = wp_parse_args( $new_post, $defaults );
			if( isset( $new_post['post_title'] ) || isset( $new_post['post_content'] ) || isset( $new_post['post_excerpt'] ) ){
				$post_id = wp_insert_post( $new_post, true );
				if( isset( $post_id->errors ) ){
					echo "<pre>";
					print_r( $post_id->errors );
					echo "</pre>";
				}

			}


      // ADD TERMS AND CUSTOM FIELDS ONLY IF POST ID IS VALID
      if( $post_id ){

        // ADD TAXONOMIES
        foreach( $headerInfo['tax_info'] as $taxonomy => $value ) {
          $terms_id_arr = array();
          if( isset( $rowCsv[ $value ] ) ){
            $terms = explode( ',', $rowCsv[ $value ] );
            foreach( $terms as $term_str ){
    	        $term = term_exists( $term_str, $taxonomy );
    	        if( !$term ){
    	          $term = wp_insert_term( $term_str, $taxonomy );
    	        }
    	        array_push( $terms_id_arr, $term['term_id'] );
    	      }
          }
          if( count( $terms_id_arr ) ){
            wp_set_post_terms( $post_id, $terms_id_arr, $taxonomy );
          }
        }

        // ADD CUSTOM FIELDS
        foreach( $headerInfo['cf_info'] as $metakey => $value ) {
          if( isset( $rowCsv[ $value ] ) ){
            update_post_meta( $post_id, $metakey, $rowCsv[ $value ] );
          }
        }

      }

    }

	}

}

ORBIT_CSV::getInstance();
