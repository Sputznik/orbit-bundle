<?php

$terms = $this->getNestedTerms( $atts );

$param = "tax_" . $atts['typeval'];
$name_param = $param . "[]";
$values = $_GET[ $param ];

$parent_param = 'parent_' . $atts['typeval'];
$parent_value = isset( $_GET[ $parent_param ] ) ? $_GET[ $parent_param ] : ( is_array( $values ) ? $values[0] : "" );

_e( "<div data-behaviour='orbit-nested-dropdown'>" );

_e( "<div class='cats'>" );
$this->display( array(
  'label'   => $atts['label'],
  'type'    => 'dropdown',
  'name'    => $name_param,
  'items'   => $terms['cats'],
  'value'   => $parent_value
) );
_e( "</div>" );

$child_value = isset( $_GET[ $parent_param ] ) && is_array( $values ) && count( $values ) ? $values[0] : ( ( is_array( $values ) && count( $values ) > 1 ) ? $values[1] : "" );



_e( "<div class='subcats'>" );
$this->display( array(
  //'label'           => apply_filters( 'orbit-nested-dropdown-label', 'Select Sub', $atts ),
  'type'            => 'dropdown',
  'name'            => $name_param,
  'items'           => $terms['subcats'],
  'value'           => $child_value
) );
_e( "</div>" );

_e( "</div>" );
