<select name="<?php _e( $atts['name'] );?>">
	<option value="">Select</option>
	<?php foreach( $atts['items'] as $item ):?>
	<option <?php if( isset( $atts['value'] ) && $item['slug'] == $atts['value'] ){_e("selected='selected'");}?>  value="<?php _e( $item['slug'] );?>">&nbsp;<?php _e( $item['name'] );?></option>
	<?php endforeach;?>
</select>
