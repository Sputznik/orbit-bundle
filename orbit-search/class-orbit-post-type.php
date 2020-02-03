<?php

	class ORBIT_POST_TYPE extends ORBIT_BASE{

		function __construct(){
			add_action( 'init', array( $this, 'init' ) );
		}


		/* FIRES ON ACTION HOOK - INIT*/
		function init(){

			global $orbit_vars;

			if( ! isset( $orbit_vars['post_types'] ) ){
				$orbit_vars['post_types'] = array();
			}



			/* HOOK TO ADD CUSTOM POST TYPE */
			$orbit_vars['post_types'] = apply_filters( 'orbit_post_type_vars', $orbit_vars['post_types'] );

			/* ITERATE THROUGH THE POST TYPES ARRAY AND CREATE THEM */
			foreach( $orbit_vars['post_types'] as $post_type ){
				$this->create_post_type( $post_type );
			}


			/* CREATE TAXNOMIES */
			if( ! isset( $orbit_vars['taxonomies'] ) ){
				$orbit_vars['taxonomies'] = array();
			}
			/* HOOK TO ADD CUSTOM POST TYPE */
			$orbit_vars['taxonomies'] = apply_filters( 'orbit_taxonomy_vars', $orbit_vars['taxonomies'] );

			foreach( $orbit_vars['taxonomies'] as $taxonomy ){
				$this->create_taxonomy( $taxonomy );
			}

			$this->register_types_from_db();

		}

		function register_types_from_db(){



			$terms = array();

			$query = new WP_Query( array(
				'post_type'			=> 'orbit-types',
				'post_status'		=> 'publish',
				'posts_per_page'	=> 20,
			) );

			if( $query->have_posts() ){

				while( $query->have_posts() ){
					$query->the_post();

					$temp = array(
						'slug'	=> $query->post->post_name,
						'labels'=> array(
							'name' 			=> $query->post->post_title,
							'singular_name'	=> get_post_meta( $query->post->ID, 'singular_name', true )
						),
					);

					/* APPEND THE REST OF THE META FIELDS TO THE TEMP */
					$meta_fields_to_be_appended = apply_filters( 'orbit_post_type_meta_fields_appended', array( 'menu_icon', 'supports' ) );
					foreach( $meta_fields_to_be_appended as $meta_field ){
						$temp[ $meta_field ] = get_post_meta( $query->post->ID, $meta_field, true );
					}

					/* CREATE CUSTOM POST TYPE */
					$this->create_post_type( $temp );

					/* GET POST TERMS AND ITERATE */
					$post_terms = wp_get_post_terms( $query->post->ID, 'orbit_taxonomy' );
					foreach( $post_terms as $post_term ){

						/* CHECK IF THE TERMS ARE VALID */
						if( isset( $post_term->slug ) && isset( $post_term->name ) ){

							/* SET THE TAXONOMY IF NOT ADDED EARLIER */
							if( !isset( $terms[ $post_term->slug ] ) ){
								$terms[ $post_term->slug ] = array(
																'slug'			=> $post_term->slug,
																'label'			=> $post_term->name,
																'post_types'	=> array()
															);
							}

							/* APPEND THE CURRENT POST TO THE TERMS ARRAY */
							array_push( $terms[ $post_term->slug ]['post_types'], $query->post->post_name );

						}

					}



				}

				$query->reset_postdata();
			}

			/* FINALLY CREATE THE CUSTOM TAXONOMIES ADDED FROM THE DASHBOARD */
			foreach( $terms as $taxonomy ){
				$this->create_taxonomy( $taxonomy );
			}

		}



		/* CREATE CUSTOM POST TYPE */
		function create_post_type( $post_type ){

			global $orbit_vars;

			if( ! isset( $orbit_vars['post_types'] ) ){
				$orbit_vars['post_types'] = array();
			}

			if( !isset( $orbit_vars['post_types'][ $post_type['slug'] ] ) ){
				$orbit_vars['post_types'][ $post_type['slug'] ] = $post_type;
			}

			if( !isset( $post_type[ 'rewrite' ] ) ){
				$post_type[ 'rewrite' ] = array('slug' => $post_type['slug'], 'with_front' => false );
			}



			register_post_type($post_type['slug'], array(
				'labels' 							=> $post_type['labels'],
				'public' 							=> isset( $post_type['public'] ) ? $post_type['public'] : true,
				'publicly_queryable' 	=> true,
				'show_ui'							=> true,
				'query_var' 					=> true,
				'rewrite' 						=> $post_type['rewrite'],
				'has_archive' 				=> true,
				'menu_icon'						=> isset( $post_type['menu_icon'] ) && $post_type['menu_icon'] ? $post_type['menu_icon'] : 'dashicons-images-alt',
				'taxonomies'					=> isset( $post_type['taxonomies'] ) ? $post_type['taxonomies'] : array(),
				'supports'						=>	$post_type['supports']
			) );

		}

		/* CREATE CUSTOM TAXONOMIES */
		function create_taxonomy( $taxonomy ) {

			$defaults = array(
				'hierarchical' 		=> true,
				'show_admin_column' => true,
				'show_ui' 			=> true,
				'show_in_menu' 		=> true
			);

			$r = wp_parse_args( $taxonomy, $defaults );

			$labels = array(
				'name' 							=> _x( $r['label'], 'taxonomy general name' ),
				'singular_name' 				=> _x( $r['label'], 'taxonomy singular name' ),
				'search_items' 					=>  __( 'Search '.$r['label'] ),
				'popular_items' 				=> __( 'Popular '.$r['label'] ),
				'all_items' 					=> __( 'All '.$r['label'] ),
				'parent_item' 					=> null,
				'parent_item_colon' 			=> null,
				'edit_item' 					=> __( 'Edit '.$r['label'] ),
				'update_item' 					=> __( 'Update '.$r['label'] ),
				'add_new_item' 					=> __( 'Add New '.$r['label'] ),
				'new_item_name' 				=> __( 'New '.$r['label'] ),
				'separate_items_with_commas' 	=> __( 'Separate '.$r['label'].' with commas' ),
				'add_or_remove_items' 			=> __( 'Add or remove '.$r['label'] ),
				'choose_from_most_used' 		=> __( 'Choose from the most used '.$r['label'] ),
				'menu_name' 					=> __( $r['label'] ),
			);

			register_taxonomy( $r['slug'], $taxonomy['post_types'], array(
				'hierarchical' 			=> $r['hierarchical'],
				'labels' 				=> $labels,
				'show_ui' 				=> $r['show_ui'],
				'show_admin_column' 	=> $r['show_admin_column'],
				'update_count_callback' => '_update_post_term_count',
				'query_var' 			=> true,
				'show_in_menu' 			=> $r['show_in_menu'],
				'rewrite' 				=> array( 'slug' => $r['slug'] ),
			));
		}

	}



	ORBIT_POST_TYPE::getInstance();
