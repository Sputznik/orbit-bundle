<?php if($atts['pagination'] != '0'):?>
<div class='orbit-btn-load-parent'>
	<button data-behaviour='oq-ajax-loading' data-list="<?php _e('#'.$atts['id']);?>" class="load-more" type="button">
		<?php _e( 'Load More', 'orbit-bundle' );?>
	</button>
</div>
<?php endif;?>
