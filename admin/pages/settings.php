<?php
	
	$screens = array(
		'general'	=> array(
			'label'	=> 'General',
		),
		/*
		'social'	=> array(
			'label'		=> 'Social',
			'action'	=> 'social'
		),
		'footer'	=> array(
			'label'		=> 'Footer',
			'action'	=> 'footer'
		)
		
		*/
	);
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
	
	<?php if( 'general' == $active_tab ):?>
	<p>
		Import predefined layouts as templates:
		<ol>
			<li>Three Column Grid to display posts</li>
			<li>Posts listed vertically with small thumbnails and post excerpts</li>
			<li>Posts listed vertically with huge thumbnails</li>
		</ol>
	</p>
	<p class='help'>Note: if import has been done previously, then the items will be duplicated.</p>
	<?php
		
		if( isset( $_POST['import'] ) && $_POST['import'] == '1' ){
			
			do_action('orbit_import');
			
			echo "<p><b>Import Successful</b></p>";
			
		}
		
	?>
	
	<?php if( ! isset( $_POST['import'] ) ):?>
	<form method="POST">
		<input type="hidden" name="import" value="1" />
		<p class='submit'><input type="submit" name="submit" class="button button-primary" value="Import"><p>
	</form>
	<?php endif;?>
	
	
	<?php endif;?>
	
	
</div>