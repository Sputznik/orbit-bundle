<?php if( function_exists('coauthors_posts_links') ):?>
	<ul class="oq-coauthors-grid list-unstyled" id="<?php _e( $atts['id'] );?>">
	<?php foreach ( $this->query as $author ): ?>
	<li class="list-article">
		<a href="<?php _e( get_author_posts_url( $author->ID, $author->user_nicename ) ); ?>">
			<div class="author-avatar" style="background-image: url(<?php echo get_avatar_url( $author->ID );?>);"></div>
			<div class='orbit-user-name'>
				<?php _e( $author->display_name ); ?>
				<?php //_e( $author->description ); ?>
			</div>
		</a>
	</li>
	<?php endforeach;?>
	</ul>
<?php endif; ?>
