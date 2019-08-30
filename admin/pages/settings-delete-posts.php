<?php 

	$post_types = get_post_types( array(), 'objects' );

?>

<?php if (!isset($_POST['submit'])): ?>

<p>Select the <strong>Post Type</strong> to bulk <strong>delete</strong> all of it's content.</p>

<form method="POST" class="orbit-bd-form">
  <div>
    <p><label>Select Post Type</label></p>
    <select name="orbit_bd_post_type">
      <?php foreach ($post_types as $post_type): if (isset($post_type->name) && isset($post_type->label)): ?>
		    <option value="<?php _e($post_type->name);?>"><?php _e($post_type->label);?></option>
		      <?php endif;endforeach;?>
    </select>
  </div>
  
  <p class='submit'><input type="submit" name="submit" class="button button-primary" value="Bulk Delete Posts"><p>
</form>


<?php else: ?>

<?php

if ( isset($_POST['submit']) ) {

	$num_rows = 0;
	$resource_name = '';
	
	$resource_name = $_POST['orbit_bd_post_type'];

	$num_rows = count( get_posts( array( 
		'post_type' => $resource_name, 
		'posts_per_page' => -1 
	) ) );

	
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
				'batch_action'	=> 'bulk_delete_posts',
				'params'		=> array(
				  'per_page'	=> $per_page,
				  'resource_name' => $resource_name,
				)
			) 
		);

	} else {
		echo "<p>Nothing to delete for selected resource.</p>";
	}      

}

?>
<?php endif;?>
