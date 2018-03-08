<?php global $orbit_templates;?>
<?php if( isset( $atts['style_id'] ) ):?>
<ul id="<?php _e( $atts['id'] );?>" data-target="<?php _e('li.orbit-article-db');?>" data-url="<?php _e( $atts['url'] );?>" class="<?php $orbit_templates->print_template_class( $atts['style_id'] );?>">
	<?php while( $this->query->have_posts() ) : $this->query->the_post();?>
	<li class='orbit-article-db'><?php $orbit_templates->print_template( $atts['style_id'] );?></li>
	<?php endwhile;?>
</ul>
<?php endif;?>