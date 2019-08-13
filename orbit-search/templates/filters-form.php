<div class='orbit-search-form'>
	<div class="orbit-search-form-box">
		<div class="orbit-search-form-title">
			<span><?php _e( apply_filters( 'orbit-search-form-title', 'Filter this data' ) );?></span>
			<span class="arrow-down"></span>
		</div>
		<form method='GET' action="<?php _e( $orbit_wp->getCurrentURL() );?>">
		<?php
			// CHECK IF THE ORBIT FILTERS EXISTS INSIDE THE POST META
			$db_filters = $this->getFiltersFromDB( $form->ID );

			if( is_array( $db_filters ) && count( $db_filters ) ){
				foreach ( $db_filters as $db_filter ) {
					// IF CHECKBOX OF HIDE LABEL IS ENABLED THEN EMPTY THE LABEL
					if( isset( $db_filter['hide_label'] ) && $db_filter['hide_label'] ){
						$db_filter['label'] = '';
					}
					if( isset( $db_filter['tax_show_empty'] ) && $db_filter['tax_show_empty'] ){
						$db_filter['tax_hide_empty'] = false;
					}
					$filter_shortcode = $orbit_filter->createShortcode( $db_filter );
					//echo $filter_shortcode;
					_e( do_shortcode( $filter_shortcode ) );
				}

				// SORTING FEATURE BEGINS HERE
				$sorting_options = $this->getSortingFieldsFromDB( $form->ID );
				if( is_array( $sorting_options ) && count( $sorting_options ) ){

					$sorting_options_val = array();

					foreach( $sorting_options as $sorting_option ){
						array_push( $sorting_options_val, array(
							'slug' => $sorting_option['type'].":".$sorting_option['field'].":".$sorting_option['order'],
							'name' => $sorting_option['label'] )
						);
					}

					$orbit_form_field = ORBIT_FORM_FIELD::getInstance();
					$orbit_form_field->display( array(
						'name'	=> 'orbit_sort',
						'value'	=> isset( $_GET[ 'orbit_sort' ] ) ? $_GET[ 'orbit_sort' ] : "",
						'label'	=> 'Sort By',
						'type'	=> 'dropdown',
						'items'	=> $sorting_options_val
					) );


				}

			}
			else{
				// FALLBACK TO DEFAULT FUNCTIONALITY
				_e( do_shortcode( $form->post_content ) );
			}
		?>

		<?php $orbit_wp = ORBIT_WP::getInstance();?>

			<ul class="list-inline" data-list="form-btns">
				<li><button type='submit'>Submit</button></li>
				<li>or</li>
				<li><a href="<?php _e( $orbit_wp->getCurrentURL() );?>" style="text-decoration:underline">Reset</a></li>
			</ul>
		</form>
	</div>
</div>
