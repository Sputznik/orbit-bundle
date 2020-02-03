<?php

$screens = array(
	'import' => array(
		'label' => 'Import Templates',
		'tab' => plugin_dir_path(__FILE__) . 'settings-import.php',
	),
	'import-terms' => array(
		'label' => 'Import Terms',
		'tab' => plugin_dir_path(__FILE__) . 'settings-import-terms.php',
		'action' => 'import-terms',
	),
	'import-posts' => array(
		'label' => 'Import Posts',
		'tab' => plugin_dir_path(__FILE__) . 'settings-import-posts.php',
		'action' => 'import-posts',
	),
	'bulk-delete' => array(
		'label' => 'Bulk Delete',
		'tab' => plugin_dir_path(__FILE__) . 'settings-bulk-delete.php',
		'action' => 'bulk-delete',
	),
);

$screens = apply_filters('orbit_admin_settings_screens', $screens);


?>
<div class="wrap">
	<h1>Orbit Settings</h1>
	<?php $this->tabs( $screens );?>
</div>
