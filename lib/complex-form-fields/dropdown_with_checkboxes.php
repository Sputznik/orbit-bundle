<?php

$terms = $this->getNestedTerms( $atts );

$param = "tax_" . $atts['typeval'];
$name_param = $param . "[]";
$values = $_GET[ $param ];



$parent_param = 'parent_' . $atts['typeval'];
$parent_value = isset( $_GET[ $parent_param ] ) ? $_GET[ $parent_param ] : ( is_array( $values ) ? $values[0] : "" );

_e( "<div data-behaviour='orbit-nested-dropdown-checkboxes'>" );

_e( "<div class='cats'>" );
$this->display( array(
  'label'   => $atts['label'],
  'type'    => 'dropdown',
  'name'    => $name_param,
  'items'   => $terms['cats'],
  'value'   => $parent_value
) );
_e( "</div>" );

/*
$child_values = array();
if( isset( $_GET[ $parent_param ] ) ){
  foreach ( $values as $value ) {
    array_push( $child_values, $value[0] );
  }
}
*/



_e( "<div class='subcats'>" );
$this->display( array(
  'type'            => 'bt_dropdown_checkboxes',
  'name'            => $param,
  'items'           => $terms['subcats'],
  'value'           => isset( $_GET[ $parent_param ] ) ? $values : array()
) );
_e( "</div>" );

_e( "</div>" );
