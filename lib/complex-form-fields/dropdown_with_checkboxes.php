<?php

$terms = $this->getNestedTerms( $atts );

$param = "tax_" . $atts['typeval'];
$name_param = $param . "[]";
$values = $_GET[ $param ];

_e( "<div data-behaviour='orbit-nested-dropdown-checkboxes'>" );

_e( "<div class='cats'>" );
$this->display( array(
  'label'   => $atts['label'],
  'type'    => 'dropdown',
  'name'    => $name_param,
  'items'   => $terms['cats'],
  'value'   => is_array( $values ) ? $values[0] : ""
) );
_e( "</div>" );

_e( "<div class='subcats'>" );
$this->display( array(
  //'label'           => apply_filters( 'orbit-nested-dropdown-label', 'Select Sub', $atts ),
  'type'            => 'bt_dropdown_checkboxes',
  'name'            => $name_param,
  'items'           => $terms['subcats'],
  'value'           => ( is_array( $values ) && count( $values ) > 1 ) ? $values[1] : ""
) );
_e( "</div>" );

_e( "</div>" );
