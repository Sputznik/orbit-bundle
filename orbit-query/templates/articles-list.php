<ul id="<?php _e( $atts['id'] );?>" data-target="<?php _e('li.orbit-article-db');?>" data-url="<?php _e( $atts['url'] );?>" class="orbit-list">
	<?php while( $this->query->have_posts() ) : $this->query->the_post();?>
	<li class='orbit-article'>
		<?php echo do_shortcode( '[orbit_thumbnail_bg size="thumbnail"]' ); ?>
		<div class='orbit-article-post'>
			<div class='orbit-article-header'>
				<h3><a href="<?php echo do_shortcode('[orbit_link]')?>"><?php echo do_shortcode('[orbit_title]');?></a></h3>
				<div class="small">
					<a href='<?php echo do_shortcode('[orbit_author_link]');?>'><?php echo do_shortcode('[orbit_author]');?></a> on <?php echo do_shortcode('[orbit_date]');?>
				</div>
			</div>
			<div class="post-excerpt small"><?php echo do_shortcode('[orbit_excerpt]');?></div>
		</div>
	</li>
	<?php endwhile;?>
</ul>
