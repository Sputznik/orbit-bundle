<?php
/*
Plugin Name: Orbit Translations
Plugin URI : https://www.sputznik.com
Description: Basic translation framework for taxonomies and custom labels
Author: Samuel V Thomas
*/

class ORBIT_TRANSLATIONS extends ORBIT_BASE{

  var $db_labels;


  function __construct(){

    $this->db_labels = array();
  }

  function getOptionKey( $key, $translation ){
    return 'label_' . $translation . '_' . $key;
  }

  function getValue( $key, $translation, $slug ){

    $option_key = $this->getOptionKey( $key, $translation );

    // GET FROM DB
    $db_labels = get_option( $option_key );

    return isset( $db_labels[ $slug ] ) ? $db_labels[ $slug ] : "";

  }

  function getFromDB( $key, $translation ){

    $option_key = $this->getOptionKey( $key, $translation );
    // CHECK IF IT ALREADY EXISTS
    if( !isset( $this->db_labels[ $option_key ] ) ){
      $this->db_labels[ $option_key ] = get_option( $option_key );
    }

    return $this->db_labels[ $option_key ];

  }

  function formForLabels( $labels, $key, $translation ){

    $option_key = $this->getOptionKey( $key, $translation );

    // FORM SUBMISSION
    if( isset( $_POST['label'] ) ){
      update_option( $option_key, $_POST['label'] );
    }

    _e( '<form class="" method="post">' );
    _e( '<table style="margin-top:10px;">' );
    foreach( $labels as $slug => $label ){
      _e( '<tr>' );
      _e( '<td>' . $label[ 'label' ] . '</td>' );
      _e( '<td><input type="text" name="label[' . $slug .']" value="' . $this->getValue( $key, $translation, $slug ) . '" /></td>' );
      _e('</tr>');

    }
    _e( '</table>' );
    _e( '<p><input type="submit" value="Submit" name="submit"></p>' );
    _e( '</form>' );


  }

}

ORBIT_TRANSLATIONS::getInstance();
