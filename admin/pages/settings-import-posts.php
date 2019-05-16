<?php $post_types = get_post_types( array(), 'objects'); ?>
<p>Import posts from a CSV file where the hedaer row defines the contents of the file.</p>
<p class="help">Header Information starting with "post_" is assumed to be part of the post information. And starting with "tax_" is assumed to be part of the taxonomy and terms information. And starting with "cf_" is assumed to be part of the custom fields information.</p>
<?php if( ! isset( $_POST['submit'] ) ):?>

<form method="POST" enctype="multipart/form-data" >
  <div>
    <p><label>Select Post Type</label></p>
    <select name="csv_post_type">
    <?php foreach ( $post_types as $post_type ) : if ( isset( $post_type->name ) && isset( $post_type->label ) ) :?>
        <option value="<?php _e( $post_type->name );?>"><?php _e( $post_type->label );?></option>
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

<?php
  // INCLUDE THE NECESSARY FILES FOR UPLOAD
  if ( ! function_exists( 'wp_handle_upload' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
  }

  if( isset( $_FILES['file'] ) ){
    // UPLOAD THE FILE
    $movefile = wp_handle_upload( $_FILES['file'], array( 'test_form' => false ) );

    // CHECK IF UPLOAD PROCESS WAS COMPLETED WITHOUT ANY ERROR
    if ( $movefile && ! isset( $movefile['error'] ) ) {
      echo "File is valid, and was successfully uploaded.\n";

      $orbit_csv = ORBIT_CSV::getInstance();
      $num_rows = $orbit_csv->numRows( $movefile['file'] );

      $per_page = 100;

      $batches = round( $num_rows / $per_page );
      if( !$batches ){
        $batches = 1;
      }

      $batch_process = ORBIT_BATCH_PROCESS::getInstance();

      echo $batch_process->plain_shortcode( array(
        'title'	      => '',
        'desc'			  => '',
        'batches'		  => $batches,
        'btn_text' 		=> 'Import CSV',
        'batch_action'=> 'import_posts',
        'params'		  => array(
          'per_page'	=> $per_page,
          'post_type'  => $_POST['csv_post_type'],
          'file'      => $movefile['file']
        )
      ) );


    } else {
      /**
      * Error generated by _wp_handle_upload()
      * @see _wp_handle_upload() in wp-admin/includes/file.php
      */
      echo $movefile['error'];
    }
  }


?>
<?php endif;?>