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
			
			add_submenu_page( 'orbit-types', 'Settings', 'Orbit Settings', 'manage_options', 'orbit-settings', array( $this, 'settings_page' ) );
		}
		/*
		public function kv_options_init() { 
			 register_setting(
				'vaajo_general', // Option group
				'vaajo_general', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);

			add_settings_section(
				'setting_section_id', // ID
				'All Settings', // Title
				array( $this, 'print_section_info' ), // Callback
				'vaajo-setting-admin' // Page
			); 
			 add_settings_field(
				'logo_image', 
				'Logo Image', 
				array( $this, 'logo_image_callback' ), 
				'vaajo-setting-admin', 
				'setting_section_id'
			);  		
			
		register_setting(
				'vaajo_social', // Option group
				'vaajo_social', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);
			add_settings_section(
				'setting_section_id', // ID
				'Social Settings', // Title
				array( $this, 'print_section_info' ), // Callback
				'vaajo-setting-social' // Page
			);  
			
		add_settings_field(
				'fb_url', // ID
				'Facebook URL', // Title 
				array( $this, 'fb_url_callback' ), // Callback
				'vaajo-setting-social', // Page
				'setting_section_id' // Section           
			);
			
			
		register_setting(
				'vaajo_footer', // Option group
				'vaajo_footer', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);
			add_settings_section(
				'setting_section_id', // ID
				'Footer Details', // Title
				array( $this, 'print_section_info' ), // Callback
				'vaajo-setting-footer' // Page
			);         

			add_settings_field(
				'hide_more_themes', 
				'Hide Find more themes at Kvcodes.com', 
				array( $this, 'hide_more_themes_callback' ), 
				'vaajo-setting-footer', 
				'setting_section_id'
			);
	}
		*/
		
		function settings_page(){
			include "pages/settings.php";
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
				wp_enqueue_script('orbit-form-default', plugin_dir_url( __FILE__ ).'js/of.default.js', array( 'jquery'), '1.0.9', true );
			}
			
			if( $hook == 'post.php' && $post_type == 'orbit-form' ) {
				wp_enqueue_script('orbit-form', plugin_dir_url( __FILE__ ).'js/orbit_form.js', array( 'jquery', 'orbit-form-default' ), '2.0.1', true );
			}
			
			if( $hook == 'post.php' && $post_type == 'page' ) {
				wp_enqueue_script('orbit-query', plugin_dir_url( __FILE__ ).'js/orbit_query.js', array( 'jquery', 'orbit-form-default' ), '2.0.8', true );
			}
			
			wp_enqueue_style( 'orbit-form', plugin_dir_url( __FILE__ ).'css/admin-style.css', array(), "1.0.7" );
			
		}

		
		function wp_admin_footer(){
			include("backbone-templates.php");
		}
		
	}
	
	new ORBIT_ADMIN;