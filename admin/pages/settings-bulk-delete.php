<?php $post_types = get_post_types(array(), 'objects');?>

<?php if (!isset($_POST['submit'])): ?>

<p>Select the Post Type to bulk <strong>delete</strong> all of it's content.</p>

<form method="POST" enctype="multipart/form-data" >
  <div>
    <p><label>Select Post Type</label></p>
    <select name="bulk_post_type">
      <?php foreach ($post_types as $post_type): if (isset($post_type->name) && isset($post_type->label)): ?>
		    <option value="<?php _e($post_type->name);?>"><?php _e($post_type->label);?></option>
		      <?php endif;endforeach;?>
    </select>
  </div>
  <p class='submit'><input type="submit" name="submit" class="button button-primary" value="Bulk Delete"><p>
</form>

<?php else: ?>

<?php

if (isset($_POST['submit'])) {

	$num_rows = count( get_posts( array( 
			'post_type' => $_POST['bulk_post_type'], 
			'posts_per_page' => -1 
			)
		)
	); 
	
	if ( $num_rows ) {
	      
		$per_page = 100;

		$batches = round( $num_rows / $per_page );
		
		if( !$batches ) {
			$batches = 1;
		}

		$batch_process = ORBIT_BATCH_PROCESS::getInstance();

		echo $batch_process->plain_shortcode( array(
				'title'	      	=> '',
				'desc'			=> '',
				'batches'		=> $batches,
				'btn_text' 		=> 'Bulk Deleting',
				'batch_action'	=> 'bulk_delete',
				'params'		=> array(
				  'per_page'	=> $per_page,
				  'post_type'  	=> $_POST['bulk_post_type'],
				)
			) 
		);

	} else {
		echo "<p>Nothing to delete for selected Post Type.</p>";
	}      

}

?>
<?php endif;?>
