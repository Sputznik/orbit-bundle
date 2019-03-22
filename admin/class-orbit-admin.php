<?php

	class ORBIT_ADMIN{

		function __construct(){

			/* ENQUEUE SCRIPTS ON ADMIN DASHBOARD */
			add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_script') );

			add_action('admin_head', array( $this, 'admin_head' ), 50);

			add_action('admin_footer', array( $this, 'wp_admin_footer' ) );

			/* ADMIN MENU FOR THE ORBIT */
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 9999 );


		}

		function admin_menu(){

			/* REMOVE MENUS FROM CUSTOM POST TYPES */
			remove_menu_page('edit.php?post_type=orbit-form');
			remove_menu_page('edit.php?post_type=orbit-types');
			remove_menu_page('edit.php?post_type=orbit-tmp');

			/* ADD MAIN MENU FOR THE PLUGIN */
			add_menu_page( 'Orbit Bundle', 'Orbit Bundle', 'manage_options', 'orbit-types', array( $this, 'menu_page' ) );

			/* ADD SUB MENU ITEMS FOR ORBIT TYPES */
			add_submenu_page( 'orbit-types', 'Orbit Types', 'Orbit Types', 'manage_options', 'orbit-types', array( $this, 'menu_page' ) );
			//add_submenu_page( 'orbit-types', 'New Orbit Type', 'New Orbit Type', 'manage_options', 'orbit-types-new', array( $this, 'menu_page' ) );
			add_submenu_page( 'orbit-types', 'Orbit Taxonomies', 'Orbit Taxonomies', 'manage_options', 'orbit-taxonomies', array( $this, 'menu_page' ) );

			/* ADD SUB MENU ITEMS FOR ORBIT TEMPLATES */
			add_submenu_page( 'orbit-types', 'Templates', 'Orbit Templates', 'manage_options', 'orbit-templates', array( $this, 'menu_page' ) );
			//add_submenu_page( 'orbit-types', 'New Template', 'New Orbit Template', 'manage_options', 'orbit-templates-new', array( $this, 'menu_page' ) );

			/* ADD SUB MENU ITEMS FOR ORBIT SEARCH FORMS */
			add_submenu_page( 'orbit-types', 'SearchForms', 'Orbit SearchForms', 'manage_options', 'orbit-form', array( $this, 'menu_page' ) );
			//add_submenu_page( 'orbit-types', 'New SearchForm', 'New SearchForm', 'manage_options', 'orbit-form-new', array( $this, 'menu_page' ) );

			//add_submenu_page( 'orbit-types', 'Import', 'Orbit Import', 'manage_options', 'orbit-import', array( $this, 'page' ) );

			add_submenu_page( 'orbit-types', 'Settings', 'Orbit Settings', 'manage_options', 'orbit-settings', array( $this, 'page' ) );
		}



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

			if( $tmp ){
				include( "pages/".$tmp );
			}

		}

		function menu_page(){

			switch( $_GET['page'] ){

				case 'orbit-types':
					$url = admin_url().'edit.php?post_type=orbit-types';
					break;

				case 'orbit-types-new':
					$url = admin_url().'post-new.php?post_type=orbit-types';
					break;

				case 'orbit-taxonomies':
					$url = admin_url().'edit-tags.php?taxonomy=orbit_taxonomy&post_type=orbit-types';
					break;

				case 'orbit-templates':
					$url = admin_url().'edit.php?post_type=orbit-tmp';
					break;

				case 'orbit-templates-new':
					$url = admin_url().'post-new.php?post_type=orbit-tmp';
					break;

				case 'orbit-form':
					$url = admin_url().'edit.php?post_type=orbit-form';
					break;

				case 'orbit-form-new':
					$url = admin_url().'post-new.php?post_type=orbit-form';
					break;
			}

			/* REDIRECT VIA JS */
			_e("<script>location.href='".$url."';</script>");

		}

		function admin_head(){
			$screen = get_current_screen();

			if( in_array( $screen->id, array('edit-orbit-types', 'orbit-types', 'edit-orbit_taxonomy', 'orbit-tmp', 'edit-orbit-tmp', 'orbit-form', 'edit-orbit-form') ) ):

			/* HIGHLIGHT THE CURRENT MENU ITEM AFTER REDIRECT */
			?>
			<script>
				jQuery(document).ready(function($) {
					$('#toplevel_page_orbit-types').addClass('wp-has-current-submenu wp-menu-open menu-top menu-top-first').removeClass('wp-not-current-submenu');
					$('#toplevel_page_orbit-types > a').addClass('wp-has-current-submenu').removeClass('wp-not-current-submenu');
				});
			</script>
			<?php

			endif;

			?>
			<script>
				jQuery(document).ready(function($) {

					var anchors = [
						['admin.php?page=orbit-types', 'edit.php?post_type=orbit-types'],
						['admin.php?page=orbit-taxonomies', 'edit-tags.php?taxonomy=orbit_taxonomy&post_type=orbit-types'],
						['admin.php?page=orbit-templates', 'edit.php?post_type=orbit-tmp'],
						['admin.php?page=orbit-form', 'edit.php?post_type=orbit-form'],
					];

					for( var i=0; i<anchors.length; i++ ){
						$( "a[href='" + anchors[i][0] + "']" ).attr( 'href', anchors[i][1] );
					}

				});
			</script>
			<?php
		}

		function wp_admin_script( $hook ) {

			global $post_type;


			if( ( $hook == 'post.php' ) && ( $post_type == 'orbit-form' || $post_type == 'page' ) ){
				wp_enqueue_script('orbit-form-default', plugins_url( 'orbit-bundle/dist/js/of.default.js' ), array( 'jquery'), '1.0.9', true );
			}

			if( $hook == 'post.php' && $post_type == 'orbit-form' ) {
				wp_enqueue_script('orbit-form', plugins_url( 'orbit-bundle/dist/js/orbit_form_tinymce_btn.js' ), array( 'jquery', 'orbit-form-default' ), '2.0.1', true );
			}

			if( $hook == 'post.php' && $post_type == 'page' ) {
				wp_enqueue_script('orbit-query', plugins_url( 'orbit-bundle/dist/js/orbit_query_tinymce_btn.js' ), array( 'jquery', 'orbit-form-default' ), '1.0.0', true );
			}

			wp_enqueue_style( 'orbit-form', plugins_url( 'orbit-bundle/dist/css/admin-style.css' ), array(), "1.0.9" );

			wp_enqueue_script( 'orbit-repeater', plugins_url( 'orbit-bundle/dist/js/repeater.js' ), array( 'jquery' ), '1.0.0', true );

			wp_enqueue_script( 'orbit-cf', plugins_url( 'orbit-bundle/dist/js/orbit_cf.js' ), array( 'jquery', 'orbit-repeater' ), '1.0.3', true );

			wp_enqueue_script( 'orbit-bp', plugins_url( 'orbit-bundle/dist/js/batch-process.js' ), array( 'jquery' ), '1.0.3', true );
		}


		function wp_admin_footer(){
			include("backbone-templates.php");
		}

	}

	new ORBIT_ADMIN;
