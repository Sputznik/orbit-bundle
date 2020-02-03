<?php

$form_pages = array(
  array(
    'page_title' => 'Page 1',
    'fields'     => array(
      array(
        'label' => 'Give a name to your data type',
        'type'  => 'post',
        'typeval' => 'title',
        'form'    => 'multiple-text',
        'required'  => 1,
        'help'      => 'Examples: service providers, resources, posts, etc'
      ),
      array(
        'label' => 'Add Categories',
        'type'  => 'tax',
        'required'  => 1,
        'typeval' => 'orbit_taxonomy',
        'form' => 'multiple-text',
      ),
      array(
        'label'     => 'Add Meta Information',
        'type'      => 'cf',
        'name'      => 'custom_fields',
        'form'      => 'repeater_cf',
      ),
    )
  )
);

?>
<div class="wrap">
	<h1>Orbit Setup</h1>
  <a class="close-btn" href="<?php _e( admin_url('admin.php?page=orbit-settings') );?>">&times;</a>
  <?php
    $orbit_fep = ORBIT_FEP::getInstance();

    $settings = array(
      'post_type'         => 'orbit-types',
      'post_status'       => 'publish',
      'form_success_msg'  => 'Your form was submitted successfully'
    );

    $orbit_fep->create( $form_pages, $settings );
  ?>
</div>
<style>
  .close-btn{ text-decoration: none; position: absolute; right: 0; top: 10px; font-size: 30px; }
  .wrap{ max-width: 700px; margin-left: auto; margin-right: auto; position: relative; }
  .orbit-fep{
    padding: 20px !important;
    margin-top: 20px;
    border: #999 solid 1px;
    border-radius: 3px;
    background: #fff;
  }
  .orbit-fep button[type=submit]{ margin-top: 20px; }
  #wpfooter, #wpadminbar{ display: none; }
  #wpcontent, #wpfooter{ margin-left: 0; }
  #adminmenumain{ display: none; }
</style>
