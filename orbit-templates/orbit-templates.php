<?php
	
	$inc_files = array(
		"class-orbit-templates.php",
		"orbit-shortcodes.php"
	);
	
	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
	
	add_action( 'orbit_import', function(){
		
		$tmp_html = '[orbit_thumbnail_bg size="full"]
			<div class="orbit-post-content">
				<div class="orbit-author">
					<a href="[orbit_author_link]">[orbit_avatar]</a>
					<div class="small"><a href="[orbit_author_link]">[orbit_author]</a><br>[orbit_date]</div>
				</div>
				<h3><a href="[orbit_link]">[orbit_title]</a></h3>
				<div class="post-excerpt">[orbit_excerpt]</div>
				<a class="orbit-read-more" href="[orbit_link]">Continue reading</a>
		</div>';
		
		$predefined_templates = array(
			array(
				'post'	=> array(
					'post_status'	=> 'publish',
					'post_type'		=> 'orbit-tmp',
					'post_title' 	=> 'Orbit Three Column Grid',
					'post_content' 	=> $tmp_html

				),
				'meta'	=> array(
					array(
						'key'	=> 'css_class',
						'value'	=> 'orbit-three-grid'
					)
				)
			),
			array(
				'post'	=> array(
					'post_status'	=> 'publish',
					'post_type'		=> 'orbit-tmp',
					'post_title' 	=> 'Orbit List Grid',
					'post_content' 	=> $tmp_html

				),
				'meta'	=> array(
					array(
						'key'	=> 'css_class',
						'value'	=> 'orbit-list'
					)
				)
			),
			array(
				'post'	=> array(
					'post_status'	=> 'publish',
					'post_type'		=> 'orbit-tmp',
					'post_title' 	=> 'Orbit Jumbo Grid',
					'post_content' 	=> $tmp_html

				),
				'meta'	=> array(
					array(
						'key'	=> 'css_class',
						'value'	=> 'orbit-jumbo-grid'
					)
				)
			)
		);
		
		foreach( $predefined_templates as $template ){
			if( isset( $template['post'] ) ){
				
				if( isset( $template['post']['post_content'] ) ){
					$template['post']['post_content'] = trim( preg_replace( '/\t+/', '', $template['post']['post_content'] ) );
				}
				
				$post_id = wp_insert_post( $template['post'] );
				
				if( $post_id && isset( $template['meta'] ) && is_array( $template['meta'] ) ){
					foreach( $template['meta'] as $field ){
						
						if( isset( $field['key'] ) && isset( $field['value'] ) ){
							update_post_meta( $post_id, $field['key'], $field['value'] );
						}
					}
				}
			}
		}
		
	} );
	
	
	
	
	