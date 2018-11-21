<?php global $post;?>
<div class="form-field">
	
	<!-- PRINTING THE LABEL -->
	<?php if( isset( $f['text'] ) ):?><label><?php _e( $f['text'] );?></label><?php endif;?>
	<!-- END OF LABEL -->
	
	<?php if( $f['type'] == 'text' ):?>
	<!-- TEXTBOX -->
	<input type="text" name="<?php _e($slug);?>" placeholder="<?php _e($f['text']);?>" style="width:100%;" value="<?php _e( $f['val'] ); ?>" />
	<!-- TEXTBOX END -->
	
	<?php elseif( $f['type'] == 'number' ): $f['default'] = isset( $f['default'] ) ? $f['default'] : 0;?>
	<!-- TEXTBOX -->
	<input type="number" name="<?php _e($slug);?>" placeholder="<?php _e($f['text']);?>" style="width:100%;" value="<?php _e( $f['val'] ? $f['val'] : $f['default'] ); ?>" />
	<!-- TEXTBOX END -->
	
	
	<?php elseif( $f['type'] == 'checkbox' ): if( !is_array( $f['val'] ) ){ $f['val'] = array(); }?>
	<!-- CHECKBOX -->
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
	<textarea style="width: 100%;" name="<?php _e($slug);?>" rows="10" placeholder="<?php _e( $f['text'] );?>"><?php _e( $f['val'] ); ?></textarea>
	<!-- TEXTAREA END -->
	
	<?php elseif( $f['type'] == 'repeater' ):?>
	<!-- REPEATER -->
	<div data-behaviour='orbit-repeater' data-slug='<?php _e( $slug );?>' data-slugs='<?php _e( implode( ',', array_keys( $f['items'] ) ) );?>' style="border: #ddd solid 1px;padding:20px;background: #eee;">
		<div class='hidden-item' style="display:none;">
		<?php 
			foreach( $f['items'] as $item_slug => $item ){ 
				$this->field_html( $item_slug, $item ); 
			}
		?>
		</div>
		
		<div class='nested-fields'>
			<?php $i = 0; if( is_array( $f['val'] ) ):foreach( $f['val'] as $row ): if( implode( ',', array_keys( $row ) ) == implode( ',', array_keys( $f['items'] ) ) ):?>
			<div class='item'>
			<?php 
				foreach( $f['items'] as $item_slug => $item ){ 
					if( isset( $row[ $item_slug ] ) ){
						$item['val'] = $row[ $item_slug ];
						$this->field_html( $slug.'['.$i.']['.$item_slug.']', $item ); 
					}
				}
			?>
			</div>
			<?php $i++;endif;endforeach;endif;?>
		</div>
		<button type='button' data-behaviour='clone' class='button'>Add Item</button>
	</div>	
	<!-- REPEATER END -->
	
	<?php elseif( $f['type'] == 'dropdown' && isset( $f['options'] ) && count( $f['options'] ) ):?>
	<!-- DROPDOWN -->
	<select name="<?php _e($slug);?>">
		<?php foreach( $f['options'] as $opt_id => $opt_val ):?>
		<option <?php if( $opt_id == $f['val'] ) _e("selected='selected'"); ?> value="<?php _e( $opt_id );?>"><?php _e( $opt_val );?></option>
		<?php endforeach;?>
	</select>
	<!-- DROPDOWN END -->
	
	<?php endif; ?>
	
	<?php if( isset( $f['help'] ) ):?><p><?php _e( $f['help'] );?></p><?php endif; ?>	
</div>