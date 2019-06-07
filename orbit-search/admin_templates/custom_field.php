<?php

	$orbit_form_field = ORBIT_FORM_FIELD::getInstance();

	$form_field_atts = array(
		'name'	=> $slug,
		'value'	=> $f['val'],
		'label'	=> $f['text'],
		'type'	=> $f['type'],
		'items'	=> array(),
		'help'	=> isset( $f['help'] ) ? $f['help'] : ""
	);

	// CONFORM THE OPTIONS TO THE ITEMS ARRAY THAT IS NEEDED IN ORBIT_FORM_FIELD
	if( isset( $f['options'] ) && is_array( $f['options'] ) ){
		foreach( $f['options'] as $opt_id => $opt_val ){
			array_push( $form_field_atts['items'], array( 'slug' => $opt_id, 'name' => $opt_val ) );
		}
	}

	$orbit_form_field->display( $form_field_atts );

?>


<?php global $post;?>
<?php if( $f['type'] == 'repeater' ):?>
<div class="form-field">
	<!-- REPEATER -->
	<div data-behaviour='orbit-repeater-cf' data-slug='<?php _e( $slug );?>' data-rows='<?php _e( json_encode( $f['val'] ? $f['val'] : array() ) );?>' data-fields='<?php _e( json_encode( $f['items'] ) );?>' style="border: #ddd solid 1px;padding:20px;background: #eee;"></div>
	<!-- REPEATER END -->
</div>
<?php endif; ?>
