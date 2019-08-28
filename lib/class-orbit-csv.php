<?php

class ORBIT_CSV extends ORBIT_BASE {

	function __construct() {

		/* ACTION HOOK FOR AJAX CALL - import terms */
		add_action('orbit_batch_action_import_terms', function () {

			$path     = stripslashes($_GET['file']);
			$arrayCsv = $this->toArray($path);

			$offset = ($_GET['orbit_batch_step'] - 1) * $_GET['per_page'];

			if (!$offset) {$offset = 1;}

			$selected_array_csv = array_slice($arrayCsv, $offset, $_GET['per_page']);

			// echo "<pre>";
			// print_r($selected_array_csv);
			// echo "</pre>";

			echo "<p><strong> Batch #". $_GET['orbit_batch_step'] ." </strong></p>";

			$this->syncTerms($selected_array_csv, $_GET['taxonomy']);
		});

		/* ACTION HOOK FOR AJAX CALL - import posts */
		add_action('orbit_batch_action_import_posts', function () {

			$path     = stripslashes($_GET['file']);
			$arrayCsv = $this->toArray($path);

			$headerInfo = $this->getHeaderInfo($arrayCsv);

			$offset = ($_GET['orbit_batch_step'] - 1) * $_GET['per_page'];

			if (!$offset) {$offset = 1;}

			$selected_array_csv = array_slice($arrayCsv, $offset, $_GET['per_page']);

			//echo "<pre>";
			//print_r( $headerInfo );
			//print_r( $selected_array_csv );
			//echo "</pre>";

			echo "<p><strong> Batch #". $_GET['orbit_batch_step'] ." </strong></p>";

			$this->importPosts($selected_array_csv, $headerInfo, array('post_status' => 'publish', 'post_type' => $_GET['post_type']));

		});

	}

	function numRows($path) {
		$arrayCsv = $this->toArray($path);
		return count($arrayCsv);
	}

	function toArray($path) {
		$file     = fopen($path, "r");
		$arrayCsv = array();

		while (!feof($file)) {
			$fpTotal = fgetcsv($file);
			array_push($arrayCsv, $fpTotal);
		}

		fclose($file);

		return $arrayCsv;
	}

	// CHECK IF THE TERM EXISTS, IF NOT CREATE A NEW TERM
	function syncTerm($text, $taxonomy, $parent, $description) {
		$term = term_exists($text, $taxonomy, $parent);

		if (!$term) {

			// TERM DOES NOT EXIST, SO CREATE NEW TERM
			$term = wp_insert_term($text, $taxonomy, array('parent' => $parent, 'description' => $description));
		}

		if (isset($term['term_id'])) {return $term['term_id'];}
		return 0;
	}

	function resetTerms($taxonomy) {
		$terms = get_terms($taxonomy, array('fields' => 'ids', 'hide_empty' => false));
		foreach ($terms as $value) {
			wp_delete_term($value, $taxonomy);
		}
	}

	function syncTerms($arrayCsv, $taxonomy) {

		echo "<ul>";

		foreach ($arrayCsv as $rowCsv) {

			$parent_id = 0;

			$desc = "";

			// FIRST ADD THE PARENT OR GET THE ID IF IT ALREADY EXISTS
			if (is_array($rowCsv) && count($rowCsv) > 1 && $rowCsv[1]) {
				$parent_id = $this->syncTerm($rowCsv[1], $taxonomy, 0, '');
			}

			// ADD DESCRIPTION
			if (is_array($rowCsv) && count($rowCsv) > 2 && $rowCsv[2]) {
				$desc = $rowCsv[2];
			}

			if ($rowCsv[0]) {
				$term_id = $this->syncTerm($rowCsv[0], $taxonomy, $parent_id, $desc);
			}

			echo "<li>Added Term id: ". $term_id ."</li>";

			//print_r( $term_id );
			//echo "<br>";

		}

		echo "</ul>";

	}

	function getHeaderInfo($arrayCsv) {

		$headerCsv = $arrayCsv[0];

		$headerInfo = array(
			'post_info' 	=> array(),
			'tax_info'  	=> array(),
			'cf_info'   	=> array(),
			'term_info'		=> array()
		);

		$i = 0;
		foreach ($headerCsv as $col) {

			if (strpos($col, 'post_') !== false) {
				// POST INFORMATION
				$headerInfo['post_info'][$col] = $i;
			} elseif (strpos($col, 'tax_') !== false) {
				// TAXONOMY INFORMATION
				$temp_col = explode('tax_', $col);
				$headerInfo['tax_info'][$temp_col[1]] = $i;
			} elseif (strpos($col, 'cf_') !== false) {
				// CUSTOM FIELDS INFORMATION
				$temp_col = explode('cf_', $col);
				$headerInfo['cf_info'][$temp_col[1]] = $i;
			} elseif (strpos($col, 'term_') !== false) {
				// TERMS INFORMATION - PASSING TERM ID
				$temp_col = explode('term_', $col);
				$headerInfo['term_info'][$temp_col[1]] = $i;
			} elseif (strpos($col, '|') !== false) {
				// NEED SOME DOCUMENTATION FOR THIS - MAYBE THIS WAS USED WHILE EXPORTING
				// TERMS INFORMATION
				$temp_col = explode('|', $col);
				$headerInfo[$temp_col[0]][$temp_col[1]] = $i;
			}

			$i++;
		}

		return $headerInfo;
	}

	function exportPosts($file_slug, $headerInfo, $query_args) {

		/*
			echo "<pre>";
			print_r( $headerInfo );
			echo "</pre>";
		*/

		$orbit_wp = ORBIT_WP::getInstance();

		$the_query = new WP_Query($query_args);

		if ($the_query->have_posts()) {
			// ITERATING THROUGH THE QUERY POSTS
			while ($the_query->have_posts()) {
				$the_query->the_post();

				$row = array();

				global $post;

				foreach ($headerInfo as $type => $valueArray) {

					if ($type == 'post_info') {
						// ACCUMULATING ALL POST INFORMATION

						foreach ($valueArray as $slug => $value) {
							// UNIQUE CASE FOR ID
							if ($slug == 'post_id') {$slug = 'ID';}

							if (isset($post->$slug)) {$row[$value] = $post->$slug;}
						}
					} else if ($type == 'tax_info') {
						// ACCUMULATING ALL TAXONOMY RELATED INFORMATION

						foreach ($valueArray as $taxonomy => $value) {
							$terms          = $orbit_wp->get_post_terms(get_the_ID(), $taxonomy); //wp_get_post_terms( get_the_ID(), $taxonomy );
							$term_names_arr = array();
							if (is_array($terms) && count($terms)) {
								foreach ($terms as $term) {
									array_push($term_names_arr, $term->name);
								}
							}
							$row[$value] = implode(',', $term_names_arr);
						}
					} else {

						// CHECK IF THE NEXT HEADER INFORMATION IS A TAXONOMY
						if (taxonomy_exists($type)) {

							$post_terms       = $orbit_wp->get_post_terms(get_the_ID(), $type);
							$post_terms_slugs = array();
							foreach ($post_terms as $post_term) {
								array_push($post_terms_slugs, $post_term->slug);
							}

							// BOOLEAN ASSIGNMENT
							foreach ($valueArray as $term_slug => $value) {
								if (in_array($term_slug, $post_terms_slugs)) {$row[$value] = 1;} else { $row[$value] = 0;}
							}
						}

					}

				}

				// SORT ARRAY BY KEYS IN ASCENDING
				ksort($row);
				/*
					echo "<pre>";
					print_r( $row );
					echo "</pre>";
				*/

				$this->addRowToCSV($file_slug, $row);

			}
			wp_reset_postdata();
		}
	}

	// IMPORT POSTS FROM A CSV DATA
	function importPosts($selectedCsv, $headerInfo, $defaults = array('post_status' => 'publish')) {

		$orbit_util = ORBIT_UTIL::getInstance();

		echo "<ul>";

		foreach ($selectedCsv as $rowCsv) {

			$post_id = 0;

			// INSERT POST
			$new_post = array();

			foreach ($headerInfo['post_info'] as $slug => $value) {

				if (isset($rowCsv[$value])) {
					$new_post[$slug] = $rowCsv[$value];
				}

			}

			$new_post = wp_parse_args($new_post, $defaults);

			if (isset($new_post['post_title']) || isset($new_post['post_content']) || isset($new_post['post_excerpt'])) {

				$post_id = wp_insert_post($new_post, true);

				if (isset($post_id->errors)) {
					echo "<pre>";
					print_r($post_id->errors);
					echo "</pre>";
				}

			}

			//$orbit_util->test($post_id);

			// ADD TERMS AND CUSTOM FIELDS ONLY IF POST ID IS VALID
			if ($post_id) {

				//print_r($headerInfo['tax_info']);

				//$orbit_util->test($rowCsv);

				$terms_info_arr = array();

				// ADD TAXONOMIES
				foreach ($headerInfo['tax_info'] as $taxonomy => $value) {
					if (taxonomy_exists($taxonomy) && isset($rowCsv[$value])) {
						$terms_id_arr = array();

						//$orbit_util->test( $taxonomy );

						$terms = explode(',', $rowCsv[$value]);

						foreach ($terms as $term_str) {
							$term = term_exists($term_str, $taxonomy);
							if (!$term) {
								$term = wp_insert_term($term_str, $taxonomy);
							}
							array_push($terms_id_arr, $term['term_id']);
						}

						//$orbit_util->test( $terms );

						if (count($terms_id_arr)) {

							if( !isset( $terms_info_arr[ $taxonomy ] ) ){
								$terms_info_arr[ $taxonomy ] = array();
							}

							$terms_info_arr[ $taxonomy ] = array_merge( $terms_info_arr[ $taxonomy ], $terms_id_arr );

							//wp_set_post_terms($post_id, $terms_id_arr, $taxonomy);
						}
					}
				}

				// ADD CUSTOM FIELDS
				foreach ($headerInfo['cf_info'] as $metakey => $value) {
					if (isset($rowCsv[$value])) {
						update_post_meta($post_id, $metakey, $rowCsv[$value]);
					}
				}

				// TEMRS INFO
				foreach ($headerInfo['term_info'] as $term_info => $value) {
					// $term_col[0] - taxonomy
					// $term_col[1] - term ID
					$term_col = explode( '|', $term_info );

					if( $rowCsv[$value] == 'Y' ){

						if( !isset( $terms_info_arr[ $taxonomy ] ) ){
							$terms_info_arr[ $taxonomy ] = array();
						}

						array_push( $terms_info_arr[ $taxonomy ], $term_col[1] );

					}
				}


				// FINALLY ADDING ALL THE TERMS TOGETHER
				foreach ( $terms_info_arr as $taxonomy => $terms_id_arr ) {
					wp_set_post_terms( $post_id, $terms_id_arr, $taxonomy );
				}

				//echo "<pre>";
				//print_r( $terms_info_arr );
				//echo "</pre>";

				echo "<li>Added Post id: ".$post_id."</li>";
			}
		}

		echo "</ul>";

	}

	// RETURNING THE FILE PATH WHICH EXISTS IN THE WP UPLOADS DIRECTORY
	function getFilePath($file_slug) {
		$file             = "$file_slug.csv";
		$filePath         = array();
		$path             = wp_upload_dir();
		$filePath['path'] = $path['path'] . "/$file";
		$filePath['url']  = $path['url'] . "/$file";
		return $filePath;
	}

	// POSSIBLY A NEW FILE WHERE HEADER IS THE FIRST ROW OF DATA IN THE FILE
	function addHeaderToCSV($file_slug, $header) {
		$path      = $this->getFilePath($file_slug);
		$outstream = fopen($path['path'], "w");
		fputcsv($outstream, $header);
		fclose($outstream);
	}

	// APPENDS THE ROW OF DATA TO AN ALREADY EXISTING FILE
	function addRowToCSV($file_slug, $row) {
		$path      = $this->getFilePath($file_slug);
		$outstream = fopen($path['path'], "a");
		fputcsv($outstream, $row);
		fclose($outstream);
	}

	/*
		* PREPARE HEADER ROW
		* CHECK IF THE TAXONOMY HAS TO BE FIRTHER BROKEN DOWN INTO ITS INDIVIDUAL TERM COLUMNS
		* REPORT-TYPE IS A TAXONOMY. IF tax_report-type[] IS FOUND AS ONE OF THE HEADER ITEMS THEN BREAK IT DOWN
	*/
	function prepareHeaderForExport($header) {
		$data = array();
		foreach ($header as $headerItem) {

			// CHECK IF TAXONOMY NEEDS TO BROKEN DOWN INTO ITS RESPECTIVE TERMS AS COLUMNS
			if ((strpos($headerItem, 'tax_') !== false) && (strpos($headerItem, '[]') !== false)) {
				$headerItem = str_replace('[]', '', $headerItem);
				// ADD THE MAIN TAXONOMY TO THE HEADER COLUMN FIRST BEFORE ADDING THE TERMS
				array_push($data, $headerItem);

				$taxonomy = str_replace('tax_', '', $headerItem);

				$orbit_wp = ORBIT_WP::getInstance();

				$terms = $orbit_wp->get_terms(array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				));
				foreach ($terms as $term) {
					$newHeaderItem = $taxonomy . "|" . $term->slug;
					// ADD TERM INTO THE HEADER COLUMN
					array_push($data, $newHeaderItem);
				}

			} else {
				array_push($data, $headerItem);
			}
		}
		return $data;
	}

}

ORBIT_CSV::getInstance();
