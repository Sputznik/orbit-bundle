<?php

	class ORBIT_ADMIN{

		function __construct(){

			/* ENQUEUE SCRIPTS ON ADMIN DASHBOARD */
			add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_script') );

			add_action('admin_head', array( $this, 'admin_head' ), 50);

			/* ADMIN MENU FOR THE ORBIT */
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 9999 );
		}

		function getMenus(){
			$menus = array(
				'orbit-types' => array(
					'label'	=> 'Orbit Types',
					'url'		=> 'edit.php?post_type=orbit-types'
				),
				'orbit_taxonomy' => array(
					'label'	=> 'Orbit Taxonomies',
					'url'		=> 'edit-tags.php?taxonomy=orbit_taxonomy&post_type=orbit-types'
				),
				'orbit-tmp' => array(
					'label'	=> 'Orbit Templates',
					'url'		=> 'edit.php?post_type=orbit-tmp'
				),
				'orbit-form' => array(
					'label'	=> 'Orbit Filters',
					'url'		=> 'edit.php?post_type=orbit-form'
				),
			);
			return apply_filters( 'orbit_admin_menus', $menus );
		}

		function admin_menu(){

			/* ADD MAIN MENU FOR THE PLUGIN */
			add_menu_page( 'Orbit Bundle', 'Orbit Bundle', 'manage_options', 'orbit-types', array( $this, 'menu_page' ) );

			$menus = $this->getMenus();
			foreach( $menus as $menu_slug => $menu ){
				// ADD SUBMENU ITEM
				add_submenu_page( 'orbit-types', $menu['label'], $menu['label'], 'manage_options', $menu_slug, array( $this, 'menu_page' ) );

				// 	REMOVE THE DUPLICATED MENU APPEARING ELSEWHERE
				remove_menu_page( $menu['url'] );
			}

			// SETTINGS FOR THE ADMIN
			add_submenu_page( 'orbit-types', 'Settings', 'Orbit Settings', 'manage_options', 'orbit-settings', array( $this, 'page' ) );
			// add_submenu_page( 'orbit-types', 'Orbit Quick Setup', 'Orbit Quick Setup', 'manage_options', 'orbit-quick-setup', array( $this, 'quick_setup' ) );
		}

		// function quick_setup(){
		// 	echo "<h1>ORBIT QUICK SETUP</h1>";
		// }

		function page(){
			$page = $_GET['page'];
			$tmp = "";
			switch( $page ){
				case 'orbit-import':
					$tmp = "import.php";
					break;

				case 'orbit-settings':
					$tmp = "settings.php";
					break;
			}

			if( $tmp ){ include( "pages/".$tmp ); }

		}

		/* COMMON MENU PAGE THAT REDIRECTS USING JS */
		function menu_page(){
			$menus = $this->getMenus();
			if( isset( $menus[ $_GET['page'] ] ) && isset( $menus[ $_GET['page'] ]['url'] ) ){
				$url = $menus[ $_GET['page'] ]['url'];

				/* REDIRECT VIA JS */
				_e("<script>location.href='".admin_url().$url."';</script>");
			}
		}

		function admin_head(){
			$screen = get_current_screen();
			$menus = $this->getMenus();

			$anchors = array();
			$orbit_screens = array();

			foreach( $menus as $menu_slug => $menu ){
				array_push( $anchors, array( 'admin.php?page='.$menu_slug, $menu['url'] ) );

				array_push( $orbit_screens, $menu_slug );
				array_push( $orbit_screens, 'edit-'.$menu_slug );
			}

			/* HIGHLIGHT THE CURRENT MENU ITEM AFTER REDIRECT */
			if( in_array( $screen->id,  $orbit_screens ) ):?>
			<script>
				jQuery(document).ready(function($) {
					$('#toplevel_page_orbit-types').addClass('wp-has-current-submenu wp-menu-open menu-top menu-top-first').removeClass('wp-not-current-submenu');
					$('#toplevel_page_orbit-types > a').addClass('wp-has-current-submenu').removeClass('wp-not-current-submenu');
				});
			</script>
			<?php endif; ?>
			<script>
				jQuery(document).ready(function($) {
					var anchors = <?php _e( wp_json_encode( $anchors ) )?>;
					for( var i=0; i<anchors.length; i++ ){
						$( "a[href='" + anchors[i][0] + "']" ).attr( 'href', anchors[i][1] );
					}
				});
			</script>
			<?php
		}

		function wp_admin_script( $hook ) {

			global $post_type;

			wp_enqueue_script( 'orbit-repeater', plugins_url( 'orbit-bundle/dist/js/repeater.js' ), array( 'jquery' ), ORBIT_BUNDLE_VERSION, true );

			if( ( $hook == 'post.php' || $hook == 'post-new.php' ) && $post_type == 'orbit-form' ) {
				wp_enqueue_script( 'orbit-repeater-filters', plugins_url( 'orbit-bundle/dist/js/repeater-filters.js' ), array( 'jquery', 'orbit-repeater' ), ORBIT_BUNDLE_VERSION, true );
				wp_enqueue_script( 'orbit-repeater-export', plugins_url( 'orbit-bundle/dist/js/repeater-export.js' ), array( 'jquery', 'orbit-repeater' ), ORBIT_BUNDLE_VERSION, true );
				wp_enqueue_script( 'orbit-repeater-sort', plugins_url( 'orbit-bundle/dist/js/repeater-sort.js' ), array( 'jquery', 'orbit-repeater' ), ORBIT_BUNDLE_VERSION, true );
			}

			/*
			if( ( $hook == 'post.php' ) && ( $post_type == 'orbit-form' || $post_type == 'page' ) ){
				//wp_enqueue_script('orbit-form-default', plugins_url( 'orbit-bundle/dist/js/of.default.js' ), array( 'jquery'), ORBIT_BUNDLE_VERSION, true );
			}
			if( $hook == 'post.php' && $post_type == 'page' ) {
				wp_enqueue_script('orbit-query', plugins_url( 'orbit-bundle/dist/js/orbit_query_tinymce_btn.js' ), array( 'jquery', 'orbit-form-default' ), ORBIT_BUNDLE_VERSION, true );
			}
			*/

			wp_enqueue_script( 'orbit-cf', plugins_url( 'orbit-bundle/dist/js/orbit_cf.js' ), array( 'jquery', 'orbit-repeater' ), ORBIT_BUNDLE_VERSION, true );

			// BATCH PROCESS ENQUEUE ASSETS
			$batch_process = ORBIT_BATCH_PROCESS::getInstance();
			$batch_process->enqueue_assets();

			wp_enqueue_style( 'orbit-admin', plugins_url( 'orbit-bundle/dist/css/admin-style.css' ), array(), ORBIT_BUNDLE_VERSION );
		}
}

new ORBIT_ADMIN;
