<?php

class ORBIT_BULK_DELETE extends ORBIT_BASE {

	function __construct() {
		/* ACTION HOOK FOR AJAX CALL - bulk delete */
		add_action('orbit_batch_action_bulk_delete', function () {
			
			$per_page 	= $_GET['per_page'];
			$post_type 	= $_GET['post_type'];
			$batch_step = $_GET['orbit_batch_step'];

			$this->handleBulkDelete($post_type, $per_page, $batch_step);
		});
	}

	function handleBulkDelete( $post_type, $per_page, $batch_step ) {

		$posts= get_posts( array(
			'post_type'=>$post_type,
			'numberposts'=> $per_page 
			) 
		);
		
		echo "<p><strong> Batch #". $batch_step ." </strong></p>";
		
		echo "<ul>";
		
		foreach ($posts as $post) {
			
			wp_delete_post( $post->ID, true );
			
			echo "<li>Deleted Item id: ".$post->ID."</li>";
		}
		
		echo "</ul>";
	

	}

}

ORBIT_BULK_DELETE::getInstance();