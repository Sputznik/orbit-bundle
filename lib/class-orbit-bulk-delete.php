<?php

class ORBIT_BULK_DELETE extends ORBIT_BASE {

	function __construct() {
		/* ACTION HOOK FOR AJAX CALL - bulk delete posts */
		add_action('orbit_batch_action_bulk_delete_posts', function () {

			$per_page 		= $_GET['per_page'];
			$batch_step 	= $_GET['orbit_batch_step'];
			$resource_name 	= $_GET['resource_name'];

			$this->handlePostDelete($resource_name, $per_page, $batch_step);

		});


		/* ACTION HOOK FOR AJAX CALL - bulk delete terms*/
		add_action('orbit_batch_action_bulk_delete_terms', function () {

			$per_page 		= $_GET['per_page'];
			$batch_step 	= $_GET['orbit_batch_step'];
			$resource_name 	= $_GET['resource_name'];

			$this->handleTermDelete($resource_name, $per_page, $batch_step);

		});
	}

	function handlePostDelete( $name, $per_page, $batch_step ) {

		$posts= get_posts( array(
			'post_type'   => $name,
			'numberposts' => $per_page
			)
		);


		echo "<p><strong> Batch #". $batch_step ." </strong></p>";

		echo "<ul>";

		foreach ($posts as $post) {

			wp_delete_post( $post->ID );

			echo "<li>Deleted Post id: ".$post->ID."</li>";
		}

		echo "</ul>";


	}


	function handleTermDelete( $name, $per_page, $batch_step ) {

		$terms = get_terms( array(
		    'taxonomy' => $name,
		    'hide_empty' => false,
		) );


		echo "<p><strong> Batch #". $batch_step ." </strong></p>";

		echo "<ul>";

		foreach ($terms as $term) {

			wp_delete_term( $term->term_id, $term->taxonomy );

			echo "<li>Deleted Term id: ".$term->term_id."</li>";
		}

		echo "</ul>";

	}

}

ORBIT_BULK_DELETE::getInstance();
