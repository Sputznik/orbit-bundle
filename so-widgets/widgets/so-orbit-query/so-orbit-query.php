<?php
/*
Widget Name: Sputznik Orbit Query
Description: Sputznik Orbit Query allows users to use orbit query functionalities without writing the orbit shortcodes.
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
      'is_db_template' => array(
        'type'     => 'checkbox',
        'label'    => __( 'Use DB template', 'siteorigin-widgets' ),
        'default'  => false,
        'state_emitter' => array(
          'callback' 	  => 'conditional',
          'args' 	=> array(
            'is_db_template[active]: val',
            'is_db_template[inactive]: !val'
          )
        ),
      ),
      'style' => array(
        'type' => 'select',
        'label' => __( 'Choose Template', 'siteorigin-widgets' ),
        'default' => 'select',
        'options' => $this->get_templates(),
        'state_handler' => array(
          'is_db_template[active]' 	=> array( 'hide' ),
          '_else[is_db_template]' 	=> array( 'show' ),
        ),
      ),
      'style_id' => array(
        'type' => 'select',
        'label' => __( 'Choose Template', 'siteorigin-widgets' ),
        'default' => '',
        'options' => $this->get_db_templates(),
        'state_handler' => array(
          'is_db_template[active]' 	=> array( 'show' ),
          '_else[is_db_template]' 	=> array( 'hide' ),
        ),
      ),
      'posts_per_page' => array(
        'type' => 'text',
        'label' => __( 'Posts Per Page', 'widget-form-fields-text-domain' ),
        'default' => '4'
      ),
    );

    parent::__construct(
      'so-orbit-query',
      __( 'Sputznik Orbit Query','siteorigin-widgets' ),
      array(
        'description' =>  __( 'Allows users to use orbit query functionalities without writing the orbit shortcodes.','siteorigin-widgets' ),
        'help'        =>  ''
      ),
      array(),
      $form_options,
      plugin_dir_path(__FILE__).'/widgets/so-orbit-query'
    );

  } // construct function ends here

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
    return apply_filters( 'so-orbit-query-templates', array( 'default' => 'Default' ) ); 
  }

  function get_db_templates(){
    $templates = get_posts( array(
			'post_type'		=> 'orbit-tmp',
			'post_status'	=> 'publish',
			'numberposts'	=> 20
		) );

    $data = array();
    foreach ( $templates as $template ) { $data[ $template->ID ] = $template->post_title; }
    return $data;
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
