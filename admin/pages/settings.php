<?php

	$screens = array(
		'import'	=> array(
			'label'	=> 'Import Templates',
			'tab'		=> plugin_dir_path(__FILE__).'settings-import.php'
		),
		'import-terms'	=> array(
			'label'		=> 'Import Terms',
			'tab'			=> plugin_dir_path(__FILE__).'settings-import-terms.php',
			'action'	=> 'import-terms'
		)
	);

	$screens = apply_filters( 'orbit_admin_settings_screens', $screens );

	$active_tab = '';
?>
<div class="wrap">
	<h1>Orbit Settings</h1>
	<h2 class="nav-tab-wrapper">
	<?php
		foreach( $screens as $slug => $screen ){
			$url =  admin_url( 'admin.php?page=orbit-settings' );
			if( isset( $screen['action'] ) ){
				$url =  esc_url( add_query_arg( array( 'action' => $screen['action'] ), admin_url( 'admin.php?page=orbit-settings' ) ) );
			}

			$nav_class = "nav-tab";

			if( isset( $screen['action'] ) && isset( $_GET['action'] ) && $screen['action'] == $_GET['action'] ){
				$nav_class .= " nav-tab-active";
				$active_tab = $slug;
			}

			if( ! isset( $screen['action'] ) && ! isset( $_GET['action'] ) ){
				$nav_class .= " nav-tab-active";
				$active_tab = $slug;
			}

			echo '<a href="'.$url.'" class="'.$nav_class.'">'.$screen['label'].'</a>';
		}
	?>
	</h2>
	<?php

		if( file_exists( $screens[ $active_tab ][ 'tab' ] ) ){
			include( $screens[ $active_tab ][ 'tab' ] );
		}

	?>
</div>
