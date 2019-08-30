<?php 

	$taxonomies = get_taxonomies( array(), 'objects' );

?>

<?php if (!isset($_POST['submit'])): ?>

<p>Select the <strong>Taxonomy</strong> to bulk <strong>delete</strong> all of it's terms.</p>

<form method="POST" class="orbit-bd-form">
  <div>
    <p><label>Select Taxonomy</label></p>
    <select name="orbit_bd_taxonomy">
    	<?php foreach ($taxonomies as $taxonomy) : if (isset($taxonomy->name) && isset($taxonomy->label)) :?>
        	<option value="<?php _e( $taxonomy->name );?>"><?php _e( $taxonomy->label );?></option>
    	<?php endif; endforeach;?>
  	</select>
  </div>
  
  <p class='submit'><input type="submit" name="submit" class="button button-primary" value="Bulk Delete Terms"><p>
</form>


<?php else: ?>

<?php

if ( isset($_POST['submit']) ) {

	$num_rows = 0;
	$resource_name = '';
	
	$resource_name = $_POST['orbit_bd_taxonomy'];

	$num_rows = count( get_terms( array(
	    'taxonomy' => $resource_name, 
	    'hide_empty' => false,
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
				'batch_action'	=> 'bulk_delete_terms',
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
