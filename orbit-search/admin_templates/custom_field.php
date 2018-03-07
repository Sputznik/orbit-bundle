<div class="form-field">
	
	<?php $f['val'] = get_post_meta( $post->ID, $slug, true ); ?>
	
	<?php if( $f['type'] == 'text' ):?>
	<!-- TEXTBOX -->
	<input type="text" name="<?php _e($slug);?>" placeholder="<?php _e($f['text']);?>" style="width:100%;" value="<?php _e( $f['val'] ); ?>" />
	<!-- TEXTBOX END -->
	
	<?php elseif( $f['type'] == 'checkbox' ):?>
	<!-- CHECKBOX -->
	
		
		<label><?php _e( $f['text'] );?></label>
		
		<?php 
			
			if( !is_array( $f['val'] ) ){
				$f['val'] = array();
			}
		?>
		
		<ul>
		<?php foreach( $f['options'] as $opt_id => $opt_val ):?>
		
		<li>
			<input <?php if( in_array( $opt_id, $f['val'] ) ) _e("checked='checked'"); ?> type="checkbox" name="<?php _e($slug);?>[]" value="<?php _e( $opt_id );?>" />&nbsp;<?php _e( $opt_val );?>
		</li>
		
		
		
		
		<?php endforeach;?>
		</ul>
	<!-- CHECKBOX END -->
	
	<?php elseif( $f['type'] == 'textarea' ):?>
	<!-- TEXTAREA -->
	
	<label><?php _e( $f['text'] );?></label>
	<textarea style="width: 100%;" name="<?php _e($slug);?>" rows="10" placeholder="<?php _e( $f['text'] );?>"><?php _e( $f['val'] ); ?></textarea>
	<?php if( isset( $f['help'] ) ):?><p><?php _e( $f['help'] );?></p><?php endif; ?>	
	<!-- TEXTAREA END -->
	
	<?php elseif( $f['type'] == 'dropdown' && isset( $f['options'] ) && count( $f['options'] ) ):?>
	<!-- DROPDOWN -->
	
	<label><?php _e( $f['text'] );?></label>
	<select name="<?php _e($slug);?>">
		<?php foreach( $f['options'] as $opt_id => $opt_val ):?>
		<option <?php if( $opt_id == $f['val'] ) _e("selected='selected'"); ?> value="<?php _e( $opt_id );?>"><?php _e( $opt_val );?></option>
		<?php endforeach;?>
	</select>
	<!-- DROPDOWN END -->
	
	<?php elseif( $f['type'] == 'help' ):?>
	<?php if( isset( $f['help'] ) ):?><p><?php _e( $f['help'] );?></p><?php endif; ?>	
	<?php endif; ?>
</div>
