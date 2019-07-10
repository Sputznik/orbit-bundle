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
			}
			else{
				// FALLBACK TO DEFAULT FUNCTIONALITY
				_e( do_shortcode( $form->post_content ) );
			}
		?>
			<p><button type='submit'>Submit</button></p>
		</form>
	</div>
</div>
