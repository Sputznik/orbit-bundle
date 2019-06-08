<ul class="list-unstyled">
<?php foreach( $atts['items'] as $item ): if( isset( $item['slug'] ) && $item['slug'] ):?>
	<li class="radio">
		<label>
			<input type="radio" <?php if( $item['slug'] == $atts['value'] ){_e("checked='checked'");}?> name="<?php _e( $atts['name'] );?>" value="<?php _e( $item['slug'] );?>" />&nbsp;<?php _e( $item['name'] );?>
		</label>
	</li>
<?php endif; endforeach;?>
</ul>
