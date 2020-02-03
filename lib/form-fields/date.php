<?php

  $value = isset( $atts['value'] ) ? $atts['value'] : "";

  if( !$value && isset( $atts['default'] ) && $atts['default'] ){
    $value = $atts['default'];
  }

?>
<input type="date" name="<?php _e( $atts['name'] );?>" value="<?php _e( $value );?>" />
