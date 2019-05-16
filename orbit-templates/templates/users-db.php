<?php global $orbit_templates;?>
<?php if( isset( $atts['style_id'] ) ):?>
<ul class="<?php $orbit_templates->print_template_class( $atts['style_id'] );?>">
<?php foreach ( $this->query->results as $user ):$orbit_templates->set_user( $user );?>
	<li><?php $orbit_templates->print_template( $atts['style_id'] );?></li>
<?php endforeach;?>
</ul>
<?php endif;?>
