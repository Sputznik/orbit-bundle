<?php 

	$post_types = get_post_types( array(), 'objects' );
	
	$taxonomies = get_taxonomies( array(), 'objects' );

?>

<?php if (!isset($_POST['submit'])): ?>

<p>Select the <strong>Post Type</strong> to bulk <strong>delete</strong> all of it's content.</p>

<form method="POST" >
  <div>
    <p><label>Select Post Type</label></p>
    <select name="orbit_bd_post_type">
      <?php foreach ($post_types as $post_type): if (isset($post_type->name) && isset($post_type->label)): ?>
		    <option value="<?php _e($post_type->name);?>"><?php _e($post_type->label);?></option>
		      <?php endif;endforeach;?>
    </select>
  </div>
  <input type="hidden" name="delete_type" value="post_type">
  <p class='submit'><input type="submit" name="submit" class="button button-primary" value="Bulk Delete Posts"><p>
</form>


<p>Select the <strong>Taxonomy</strong> to bulk <strong>delete</strong> all of it's terms.</p>

<form method="POST" >
  <div>
    <p><label>Select Taxonomy</label></p>
    <select name="orbit_bd_taxonomy">
    	<?php foreach ($taxonomies as $taxonomy) : if (isset($taxonomy->name) && isset($taxonomy->label)) :?>
        	<option value="<?php _e( $taxonomy->name );?>"><?php _e( $taxonomy->label );?></option>
    	<?php endif; endforeach;?>
  	</select>
  </div>
  <input type="hidden" name="delete_type" value="taxonomy">
  <p class='submit'><input type="submit" name="submit" class="button button-primary" value="Bulk Delete Terms"><p>
</form>


<?php else: ?>

<?php

if ( isset($_POST['submit']) ) {

	$delete_type = $_POST['delete_type'];

	$num_rows = 0;
	$resource_name = '';
	
	if( $delete_type == 'post_type' ) {

		$resource_name = $_POST['orbit_bd_post_type'];

		$num_rows = count( get_posts( array( 
			'post_type' => $resource_name, 
			'posts_per_page' => -1 
		) ) );

	} else if( $delete_type == 'taxonomy' ) {

		$resource_name = $_POST['orbit_bd_taxonomy'];

		$num_rows = count( get_terms( array(
		    'taxonomy' => $resource_name, 
		    'hide_empty' => false,
		) ) );

	}

	
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
				  'delete_type' => $delete_type,
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
