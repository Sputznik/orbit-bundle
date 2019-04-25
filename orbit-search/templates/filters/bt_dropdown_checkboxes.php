<!-- ASSUMES THAT THE BOOTSTRAP DROPDOWN IS BEING USED -->
<div class="dropdown" data-behaviour="bt-dropdown-checkboxes">
  <button class="btn btn-primary dropdown-toggle" id="menu1" type="button"><?php _e( $atts['label'] );?>
  <span class="caret"></span></button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
    <?php foreach( $atts['items'] as $item ): if( $item ):?>
      <li class="checkbox">
    		<label>
    			<input type="checkbox" <?php if( in_array( $item, $atts['form_value']) ){_e("checked='checked'");}?> name="<?php _e( $atts['form_name'] );?>[]" value="<?php _e( $item );?>" />
          <span><?php _e( $item );?></span>
    		</label>
    	</li>
  <?php endif;endforeach;?>
  </ul>
</div>
