<?php

	/* SHORTCODE TO RETURN THE EXCERPT OF THE POST */
	add_shortcode( 'orbit_excerpt', function(){

		global $post;

		if( $post->post_excerpt ){
			return $post->post_excerpt;
		}

		return wp_trim_excerpt();

	} );

	/* SHORTCODE TO RETURN THE CONTENT OF THE POST */
	add_shortcode( 'orbit_content', function(){

		//global $post;

		//return do_shortcode( $post->post_content );

		return get_the_content();

	} );

	/* SHORTCODE TO RETURN THE TITLE OF THE POST */
	add_shortcode( 'orbit_title', function(){ 	return get_the_title(); } );

	/* SHORTCODE TO RETURN THE LINK OF THE POST */
	add_shortcode( 'orbit_link', function(){ return get_permalink(); } );

	/* SHORTCODE TO RETURN THE POST TYPE */
	add_shortcode( 'orbit_post_type', function(){ return get_post_type(); } );

	/* SHORTCODE TO RETURN THE AUTHOR OF THE POST */
	add_shortcode( 'orbit_author', function(){ return get_the_author(); } );

	/* SHORTCODE TO RETURN THE AUTHOR LINK */
	add_shortcode( 'orbit_author_link', function(){
		return get_author_posts_url( get_the_author_meta( 'ID' ) );
	} );

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
			return "<div class='orbit-thumbnail-bg' style='background-image: url(".$thumbnail[0].");'><a href='".get_the_permalink()."'></a></div>";
		}

		return '';
	} );

	/* SHORTCODE TO RETURN TERMS OF THE POST */
	add_shortcode( 'orbit_terms', function( $atts ){

		/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
		$atts = shortcode_atts( array(
			'taxonomy' 	=> 'post_tag',
			'seperator'	=> ', ',
			'link'		=> '1'
		), $atts, 'orbit_terms' );

		global $post;

		$term_list = wp_get_post_terms($post->ID, $atts['taxonomy']);

		$html = "";

		$i = 1;
		foreach( $term_list as $term ){
			if( $atts['link'] == '1' ){
				$html .= "<a href='".get_term_link( $term )."'>";
			}

			$html .= $term->name;

			if( $atts['link'] == '1' ){
				$html .= "</a>";
			}

			if( $i < count( $term_list ) ){
				$html .= $atts['seperator'];
			}
			$i++;
		}

		return $html;

	} );

	/* SHORTCODE TO RETURN CUSTOM FIELD */
	add_shortcode( 'orbit_cf', function( $atts ){

		/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
		$atts = shortcode_atts( array(
			'id' 			=> 'name',
			'label'			=> '',
			'hide_if_empty'	=> true,
			'is_link'		=> false
		), $atts, 'orbit_cf' );

		global $post;

		$meta_value = get_post_meta( $post->ID, $atts['id'], true );

		$text = '';

		if( !$atts['hide_if_empty'] || ( $atts['hide_if_empty'] && $meta_value ) ){

			if( $atts['is_link'] && $atts['label'] ){
				$text = "<a href='".$meta_value."' target='_blank'>".$atts['label']."</a>";
			}
			elseif ( is_array( $meta_value ) ) {
				$text = implode( ', ', $meta_value );
			}
			else{
				if( $atts['label'] ){
					$text = $atts['label'];
				}
				$text .= $meta_value;
			}

		}

		return $text;
	} );

	/* SHORTCODE TO RETURN THE AUTHOR AVATAR */
	add_shortcode( 'orbit_avatar', function( $atts ){

		/* CREATE ATTS ARRAY FROM DEFAULT AND USER PARAMETERS IN THE SHORTCODE */
		$atts = shortcode_atts( array('size' => 32), $atts, 'orbit_avatar' );

		return get_avatar( get_the_author_meta( 'ID' ), $atts['size'] );
	} );

	/* SHORTCODE TO RETURN ORBIT USER DETAILS */
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

	/* IF COAUTHORS PLUS PLUGIN IS ACTIVE */
	/* SHORTCODE TO RETURN THE COAUTHORS OF THE POST */
	add_shortcode( 'orbit_coauthors', function(){
		if ( function_exists('coauthors') ) {
			return coauthors(null, null, null, null, false);
		}
		else {
			return "plugin inactive";
		}
	} );

	/* SHORTCODE TO RETURN THE COAUTHOR LINKS */
	add_shortcode( 'orbit_coauthors_links', function(){
		if ( function_exists('coauthors_posts_links') ) {
			return coauthors_posts_links(null, null, null, null, false);
		}
		else {
			return "plugin inactive";
		}
	} );
