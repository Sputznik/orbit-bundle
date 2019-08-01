<?php
/*
Widget Name: Sputznik Orbit Query Widget
Description: Sputznik Orbit Query Widget, allows users to use orbit query functionalities without writing the orbit shortcodes.
Author: Stephen Anil, Sputznik
Author URI: http://www.sputznik.com
Widget URI:
Video URI:
*/
class SP_ORBIT_WIDGET extends SiteOrigin_Widget{

  function __construct(){
    $form_options = array(
      'post_type' => array(
        'type'    => 'checkboxes',
        'label'   => __( 'Choose post type', 'siteorigin-widgets' ),
        'default' => false,
        'options' => $this->get_posts_type()
      ),
      'style' => array(
        'type' => 'select',
        'label' => __( 'Choose Template Style', 'siteorigin-widgets' ),
        'default' => 'select',
        'options' => $this->get_templates()
      ),
      'style_id' => array(
        'type' => 'select',
        'label' => __( 'Choose Style', 'siteorigin-widgets' ),
        'default' => '',
        'options' => $this->get_db_templates()
      ),
      'posts_per_page' => array(
        'type' => 'text',
        'label' => __( 'Posts Per Page', 'widget-form-fields-text-domain' ),
        'default' => '4'
      ),
    );
    parent::__construct(
      'so-orbit-query',
      __( 'Orbit Query Widget','siteorigin-widgets' ),
      array(
        'description' =>  __( 'Widget for Orbit Queries','siteorigin-widgets' ),
        'help'        =>  ''
      ),
      array(),
      $form_options,
      plugin_dir_path(__FILE__).'/widgets/so-orbit-query'
    );
  }//construct function ends here

  // Post types
  function get_posts_type(){

    // Gets built in post types
    $types = get_post_types( array( 'public' => true ), 'names' );

    // Gets orbit bundle post types
    $args = array(
      'post_type'       => 'orbit-types',
      'posts_per_page'  =>  20
    );

    $query = new WP_Query( $args );
    foreach ( $query->posts as $key => $value) {
      $types[$value->post_name] = $value->post_title;
    }
    return $types;
  }

  function get_templates(){
    $templates = array(
      'select' => 'Select',
      'db'     =>  'db',
    );
    return $templates;
  }

  function get_db_templates(){

    $db_templates = array();

    $args = array(
      'post_type'       => 'orbit-tmp',
      'posts_per_page'  =>  20
    );
    $query = new WP_Query( $args );

    foreach ( $query->posts as $key => $value) {
      $db_templates[$value->ID] = $value->post_title;
    }

    return $db_templates;
  }

  function get_template_name($instance){
    return 'template';
  }
  function get_template_dir($instance) {
    return 'templates';
  }
  function get_style_name($instance){
    return '';
    }
}
siteorigin_widget_register('so-orbit-query',__FILE__,'SP_ORBIT_WIDGET');
