<p>
	<select name="<?php _e( $atts['form_name'] );?>">
		<option value="">Select</option>
		<?php foreach( $atts['items'] as $item ):?>
		<option <?php if( isset( $atts['form_value'] ) && $item == $atts['form_value'] ){_e("selected='selected'");}?>  value="<?php _e( $item );?>">&nbsp;<?php _e( $item );?></option>
		<?php endforeach;?>
	</select>
</p>