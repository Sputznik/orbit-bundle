<?php

$screens = array(
	'cpt' => array(
		'label' => 'Dataset',
		'tab' => plugin_dir_path(__FILE__) . 'setup-cpt.php',
	),
	'filters' => array(
		'label' => 'Filters',
		'tab' => plugin_dir_path(__FILE__) . 'setup-filters.php',
		'action' => 'filters',
	),
);
?>
<div class="wrap">
	<h1>Orbit Setup</h1>
  <a class="close-btn" href="<?php _e( admin_url('admin.php?page=orbit-settings') );?>">&times;</a>
  <?php $this->tabs( $screens, 'admin.php?page=orbit-setup', true );?>
</div>
<style>
  .close-btn{ text-decoration: none; position: absolute; right: 20px; top: 30px; font-size: 30px; }
  .wrap{
    max-width: 650px;
    margin-left: auto;
    margin-right: auto;
    position: relative;
    background: #fff;
    border-radius: 3px;
    padding: 20px;
  }
  .orbit-fep{ margin-top: 20px; padding: 0px !important; }
  .orbit-fep button[type=submit]{ margin-top: 20px; }
  .orbit-form-progress{ display: none; }

  .orbit-fep .orbit-hidden-field{ display: none; }

  .orbit-fep .inline-section .section-fields .inline-section {
    border: #eee solid 1px;
    padding: 20px;
    margin-bottom: 30px;
}

  #wpfooter, #wpadminbar{ display: none; }
  #wpcontent, #wpfooter{ margin-left: 0; }
  #adminmenumain{ display: none; }
</style>
