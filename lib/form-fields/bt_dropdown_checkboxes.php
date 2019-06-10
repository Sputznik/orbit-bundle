<!-- ASSUMES THAT THE BOOTSTRAP DROPDOWN IS BEING USED -->
<div class="dropdown" data-behaviour="bt-dropdown-checkboxes">
  <button class="btn btn-primary dropdown-toggle" id="menu1" type="button"><?php _e( $atts['label'] );?>
  <span class="caret"></span></button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
    <?php foreach( $atts['items'] as $item ): if( isset( $item['slug'] ) && $item['slug'] ):?>
      <li class="checkbox">
    		<label>
    			<input type="checkbox" <?php if( in_array( $item['slug'], $atts['value']) ){_e("checked='checked'");}?> name="<?php _e( $atts['name'] );?>[]" value="<?php _e( $item['slug'] );?>" />
          <span><?php _e( $item['name'] );?></span>
    		</label>
    	</li>
  <?php endif;endforeach;?>
  </ul>
</div>