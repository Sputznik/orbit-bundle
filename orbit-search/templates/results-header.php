<div class='orbit-results-header <?php if( $this->has_sorting( $post_id ) ) _e( 'has-sorting' );?>'>
  <div class='orbit-results-header-top'>
    <h3 class='orbit-results-heading'><?php _e( sprintf( $filter_header['results_heading'], $total_posts ) );?></h3>
    <?php
      // LIST OF TERMS FROM THE TAXONOMIES SELECTED IN THE BACKEND
      if( isset( $_GET ) && count( $_GET ) ){
        $taxonomies = isset( $filter_header['taxonomies'] ) ? $filter_header['taxonomies'] : array();
        foreach( $taxonomies as $taxonomy_slug ){
          $taxonomy = get_taxonomy( $taxonomy_slug );
          $terms_list = $orbit_wp->getPostsTerms( $taxonomy_slug, $posts, $orbit_wp_query->query );
          if( count( $terms_list ) ){
            echo "<div class='orbit-terms-count'><b>" . $taxonomy->label . "</b>: " . implode( ', ', $terms_list ) . "</div>";
          }
        }
      }
    ?>
  </div>
  <?php $this->sorting_dropdown( $post_id ); ?>
</div>
