<?php

  class ORBIT_ASSETS extends ORBIT_BASE{

    var $wp_assets, $common_assets, $admin_assets;

    function __construct(){

      $wp_assets = array(
        array(
          'handle'      => 'orbit-main',
          'url'         => plugins_url( 'orbit-bundle/dist/css/main.css' ),
          'deps'        => array(),
          'type'  => 'style'
        ),
      );

      $common_assets = array(
        array(
          'handle'      => 'orbit-common',
          'url'         => plugins_url( 'orbit-bundle/dist/css/common.css' ),
          'deps'        => array(),
          'type'  => 'style'
        ),
        array(
          'handle'      => 'orbit-common',
          'url'         => plugins_url( 'orbit-bundle/dist/js/common.js' ),
          'deps'        => array('jquery'),
          'type'  => 'script'
        ),
      );

      $admin_assets = array();

      $this->wp_assets = apply_filters( 'orbit_wp_assets', $wp_assets );

      $this->common_assets = apply_filters( 'orbit_common_assets', $common_assets );

      $this->admin_assets = apply_filters( 'orbit_admin_assets', $admin_assets );

      add_action( 'wp_enqueue_scripts', function(){
        $this->enqueue_assets( $this->wp_assets );
        $this->enqueue_assets( $this->common_assets );
      } );

      add_action( 'admin_enqueue_scripts', function(){
        $this->enqueue_assets( $this->admin_assets );
        $this->enqueue_assets( $this->common_assets );
      } );

    }

    function enqueue_asset( $handle, $url, $deps = array(), $asset_type = 'script' ){
      if( $asset_type == 'script' ){
        wp_enqueue_script( $handle, $url, $deps, ORBIT_BUNDLE_VERSION, true );
      }
      else{
        wp_enqueue_style( $handle, $url, $deps, ORBIT_BUNDLE_VERSION );
      }
    }

    // ENQUEUE MULTIPLE ASSETS AT ONCE
    function enqueue_assets( $assets ){
      if( is_array( $assets ) ){
        foreach( $assets as $asset ){
          $this->enqueue_asset( $asset['handle'], $asset['url'], $asset['deps'], $asset['type'] );
        }
      }
    }
  }


  ORBIT_ASSETS::getInstance();
