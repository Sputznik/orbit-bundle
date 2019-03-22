<?php



  if( isset( $_POST['submit'] ) ){

    echo "<pre>";
    print_r( $_POST );
    echo "</pre>";

    if ( ! function_exists( 'wp_handle_upload' ) ) {
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    if( isset( $_FILES['file'] ) ){
      $movefile = wp_handle_upload( $_FILES['file'], array( 'test_form' => false ) );

      if ( $movefile && ! isset( $movefile['error'] ) ) {
        echo "File is valid, and was successfully uploaded.\n";
        echo $movefile['file'];
        var_dump( $movefile );
      } else {
        /**
        * Error generated by _wp_handle_upload()
        * @see _wp_handle_upload() in wp-admin/includes/file.php
        */
        echo $movefile['error'];
      }
    }



    echo "<p><b>Import Successful</b></p>";

  }




  $taxonomies = get_taxonomies( array(), 'objects' );

?>
<p>Import terms of taxonomy from a CSV file</p>
<?php if( ! isset( $_POST['submit'] ) ):?>
<form method="POST" enctype="multipart/form-data" >
  <div>
    <p><label>Select Taxonomy</label></p>
    <select>
    <?php foreach ($taxonomies as $taxonomy) : if (isset($taxonomy->name) && isset($taxonomy->label)) :?>
        <option value="<?php _e( $taxonomy->name );?>"><?php _e( $taxonomy->label );?></option>
    <?php endif; endforeach;?>
  </select>
  <div>
    <p><label>CSV File</label></p>
    <input type='file' name='file' />
  </div>
  </div>
  <p class='submit'><input type="submit" name="submit" class="button button-primary" value="Upload"><p>
</form>
<?php else:?>
<?php endif;?>

<?php
  $batch_process = ORBIT_BATCH_PROCESS::getInstance();

  echo $batch_process->plain_shortcode( array(
    'title'	       => '',
    'desc'			   => '',
    'batches'		   => 10,
    'btn_text' 		 => 'Import CSV',
    //'batch_action' => 'import_terms',
    'params'		   => array(
      'per_page'	 => 100,
      'taxonomy'   => '', 
    )
  ) );
?>
