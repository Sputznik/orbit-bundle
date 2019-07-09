
<?php
  $range_values = array();
  foreach( $atts['items'] as $name => $value ) {
    $temp = $value['name'];
    array_push( $range_values, $temp  );
  }
  sort( $range_values );
  $minValue = min( $range_values );
  $maxValue = max( $range_values );

  
 ?>
<div data-behaviour="multirange" data-name="<?php _e( $atts['name'] );?>" data-range='<?php _e( $minValue ) ?>,<?php _e( $maxValue )?>'>
  <input type="range" multiple value="0,100">
</div>

<div class='multirange-checkboxes' style="display:none;">
<?php

  $this->display( array(
    'name'  => $atts['name'],
    'type'  => 'checkbox',
    'items' => $atts['items']
  ) );

?>
</div>
