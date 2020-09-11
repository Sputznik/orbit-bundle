<?php

class ORBIT_QUERY_BASE extends ORBIT_SHORTCODE{

	var $query;
	var $shortcode;

	function __construct(){


		add_action( 'wp_enqueue_scripts', array( $this, 'assets') );

		parent::__construct();


	}

	// CHECK IF THE TEMPLATE FILE EXISTS IN THE THEME
	function include_template_file( $template, $atts ){

		$old_template = $template;

		if( isset( $atts['style'] ) && $atts['style'] ){
			$template = $template.'-'.$atts['style'];
		}

		$template_url = $template.'.php';

		$theme_templates_url = apply_filters( 'orbit_query_template_'.$template , get_stylesheet_directory()."/orbit-query/".$template_url );
		if( is_child_theme() && !file_exists( $theme_templates_url ) ){
			// include from the parent theme
			$theme_templates_url = apply_filters( 'orbit_query_template_'.$template , get_template_directory()."/orbit-query/".$template_url );
		}

		$plugin_templates_url = plugin_dir_path(__FILE__)."templates/".$template_url;

		if( file_exists( $theme_templates_url ) ){
			include( $theme_templates_url );
		}
		else if( file_exists( $plugin_templates_url ) ){
			include( $plugin_templates_url );
		}
		else{
			include( "templates/".$old_template.".php" );
		}

	}

	function wp_loop( $query, $atts ){
		global $orbit_templates;
		if( isset( $atts['style_id'] ) ):?>
		<ul id="<?php _e( $atts['id'] );?>" data-target="<?php _e('li.orbit-article-db');?>" data-url="<?php _e( $atts['url'] );?>" class="<?php $orbit_templates->print_template_class( $atts['style_id'] );?>">
			<?php while( $query->have_posts() ) : $query->the_post();?>
			<li class='orbit-article-db'><?php $orbit_templates->print_template( $atts['style_id'] );?></li>
			<?php endwhile;?>
		</ul>
		<?php if($atts['pagination'] != '0'):?>
		<div class='orbit-btn-load-parent'>
			<button data-behaviour='oq-ajax-loading' data-list="<?php _e('#'.$atts['id']);?>" class="load-more" type="button">
				<?php _e( 'Load More', 'orbit-bundle' );?>
			</button>
		</div>
		<?php endif;
		endif;
	}

	/* LOAD SCRIPTS AND STYLES IF THE SHORTCODE IS USED */
	function assets($posts){

		$uri = plugin_dir_url( __FILE__ );

		// ENQUEUE SCRIPT
		wp_enqueue_script('jquery');
		wp_enqueue_script('oq-script', $uri.'js/orbit-query.js', array('jquery'), ORBIT_BUNDLE_VERSION, true);

	}
}
