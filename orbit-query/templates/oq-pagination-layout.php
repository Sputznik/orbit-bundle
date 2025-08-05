<?php if( $atts['pagination'] != '0' ): ?>

  <?php if( $atts['pagination_style'] == 'default' ): ?>
    <div class='orbit-btn-load-parent'>
    	<button data-behaviour='oq-ajax-loading' data-list="<?php _e('#'.$atts['id']);?>" class="load-more" type="button">
    		<?php _e( 'Load More', 'orbit-bundle' );?>
    	</button>
    </div>
  <?php elseif( $atts['pagination_style'] == 'numbered' ): ?>
    <div class="orbit-numbered-pagination">
     <?php
       $GLOBALS['wp_query']->max_num_pages = $this->get_total_pages( $atts );

       the_posts_pagination(
         array(
           'mid_size'  => 1,
           'prev_text' => 'Previous',
           'next_text' => 'Next',
         ),
       );
     ?>
    </div>
  <?php endif; ?>

<?php endif;?>
