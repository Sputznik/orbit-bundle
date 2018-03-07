<ul id="<?php _e( $atts['id'] );?>" data-target="<?php _e('li.orbit-article-db');?>" data-url="<?php _e( $atts['url'] );?>" class="orbit-article-<?php _e( $atts['style_id'] );?> orbit-articles-db">
	<?php while( $this->query->have_posts() ) : $this->query->the_post();?>
	<li class='orbit-article-db'>
	<?php 
			
		if( isset( $atts['style_id'] ) ){
			
			global $orbit_templates;	
			$orbit_templates->print_template( $atts['style_id'] );
		
		}
	?>
	</li>
	<?php endwhile;?>
</ul>