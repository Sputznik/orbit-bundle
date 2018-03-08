<?php 
	
	/* SHORTCODE TO RETURN THE EXCERPT OF THE POST */
	add_shortcode( 'orbit_excerpt', function(){
		
		global $post;
		
		if( $post->post_excerpt ){
			return $post->post_excerpt;
		}
		
		return wp_trim_excerpt( $post->post_content );
		
	} );
	
	/* SHORTCODE TO RETURN THE CONTENT OF THE POST */
	add_shortcode( 'orbit_content', function(){
		
		global $post;
		
		return $post->post_content;
		
	} );
	
	/* SHORTCODE TO RETURN THE TITLE OF THE POST */
	add_shortcode( 'orbit_title', function(){ 	return get_the_title(); } );
	
	/* SHORTCODE TO RETURN THE LINK OF THE POST */
	add_shortcode( 'orbit_link', function(){ return get_permalink(); } );
	
	/* SHORTCODE TO RETURN THE AUTHOR OF THE POST */
	add_shortcode( 'orbit_author', function(){ return get_the_author(); } );
	
	/* SHORTCODE TO RETURN THE AUTHOR LINK */
	add_shortcode( 'orbit_author_link', function(){ return get_the_author_link(); } );
	
	/* SHORTCODE TO RETURN THE DATE OF THE POST */
	add_shortcode( 'orbit_date', function(){ return get_the_date(); } );
	
	/* SHORTCODE TO RETURN THE FEATURED IMAGE OF THE POST */
	add_shortcode( 'orbit_thumbnail', function( $atts ){
		
		/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
		$atts = shortcode_atts( array('size' => 'post-thumbnail'), $atts, 'orbit_thumbnail' );
		
		return get_the_post_thumbnail( null, $atts['size'] );
		
	} );
	
	/* SHORTCODE TO RETURN THE SRC OF FEATURED IMAGE OF THE POST */
	add_shortcode( 'orbit_thumbnail_bg', function( $atts ){
		
		global $post_id;
		
		/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
		$atts = shortcode_atts( array('size' => 'post-thumbnail'), $atts, 'orbit_thumbnail' );
		
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $atts['size'] );
		
		if( is_array( $thumbnail ) ){
			return "<div class='orbit-thumbnail-bg' style='background-image: url(".$thumbnail[0].");'></div>";
		}
			
		return '';
	} );
	
	/* SHORTCODE TO RETURN THE FEATURED IMAGE OF THE POST */
	add_shortcode( 'orbit_terms', function( $atts ){
		
		/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
		$atts = shortcode_atts( array(
			'taxonomy' => 'post_tag'
		), $atts, 'orbit_terms' );
		
		global $post;
		
		$term_list = wp_get_post_terms($post->ID, $atts['taxonomy']);
		
		$html = "";
		
		$i = 1;
		foreach( $term_list as $term ){
			$html .= $term->name; 
			
			if( $i < count( $term_list ) ){
				$html .= ",";
			}
		}
		
		return $html;
		
	} );
	
	/* SHORTCODE TO RETURN THE AUTHOR LINK */
	add_shortcode( 'orbit_avatar', function( $atts ){
			
		/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
		$atts = shortcode_atts( array('size' => 32), $atts, 'orbit_avatar' );
		
		return get_avatar( get_the_author_meta( 'ID' ), $atts['size'] );
	} );
	
	
	add_shortcode( 'orbit_user', function( $atts ){
		
		/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
		$atts = shortcode_atts( array('field' => 'name', 'avatar_size'	=> 32), $atts, 'orbit_avatar' );
		
		global $orbit_user;
		
		switch( $atts['field'] ){
			
			case 'avatar':
				return get_avatar( $orbit_user->ID, $atts['avatar_size'] );
			
			case 'url':
				return get_author_posts_url( $orbit_user->ID );
				
			case 'description':
				return get_user_meta( $orbit_user->ID, 'description', true );
			
		}
		
		return $orbit_user->display_name;
	} );