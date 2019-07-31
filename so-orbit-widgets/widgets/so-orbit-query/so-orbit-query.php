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
  // public $templates = array();
  function __construct(){
    // $post_types = $this->post_types();
    // print_r( $post_types );
    $form_options = array(
      'post_type' => array(
          'type' => 'checkboxes',
          'label' => __( 'Choose post type', 'siteorigin-widgets' ),
          'default' => false,
          'options' => $this->post_types()
      ),
      'style' => array(
          'type' => 'select',
          'label' => __( 'Choose Template Style', 'siteorigin-widgets' ),
          'default' => 'select',
          'options' => $this->templates()
        ),
        'style_id' => array(
            'type' => 'select',
            'label' => __( 'Choose Style', 'siteorigin-widgets' ),
            'default' => '',
            'options' => $this->templateId()
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
  function post_types(){
    $types = get_post_types( array( 'public' => true ), 'names' );;
    // print_r(  $types );
    // wp_die();
    return $types;
  }

  function templates(){
    $templates = array(
      'select' => 'Select',
      'db' =>  'db',
    );
    return $templates;
  }

  function templateId(){

    $temp = array();
    $args = array(
      'post_type' => 'orbit-tmp',
      'posts_per_page'  =>  -1
    );
    $query = new WP_Query( $args );

    foreach ( $query->posts as $key => $value) {
      $temp[$value->ID] = $value->post_title;
    }

    return $temp;
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

  ?>
<script>
console.log( '<?php _e( $widget_id );?>' );
</script>
