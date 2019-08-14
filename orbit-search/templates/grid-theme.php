<div class="orbit-search-grid">
  <div class="orbit-search-filters">
    <div class="orbit-search-filters-box">
      <span class='filters-title'>
        <i class="fa fa-filter"></i>
        <?php _e( isset( $filter_settings['filter_heading'] ) ? $filter_settings['filter_heading'] : "" );?>
      </span>
      <a class="orbit-btn-close" href="#">Close</a>
      <?php $this->filters_form( $form );?>
    </div>
  </div>
  <div class="orbit-right-col">
    <div class="orbit-search-header">
      <div class="orbit-search-header-top">
        <div class="orbit-search-header-left">
          <a class="orbit-open-filters" href="#orbit-search-modal">
            <i class="fa fa-filter"></i>
            <?php _e( isset( $filter_settings['filter_heading'] ) ? $filter_settings['filter_heading'] : "" );?>
          </a>
        </div>
        <div class="orbit-search-header-middle"><?php _e( $this->results_title( $filter_header, $total_posts ) );?></div>
        <div class="orbit-search-header-right"><?php $this->sorting_dropdown( $form->ID ); ?></div>
      </div>
      <div class="orbit-search-header-bottom">
        <?php
          $results_inline_terms = $this->results_inline_terms( $filter_header, $posts );
          if( $results_inline_terms ):
        ?>
        <div class="orbit-search-inline-terms"><?php echo $results_inline_terms;?></div>
        <?php endif;?>
      </div>
    </div>
    <div class='orbit-search-results' style="margin-top: 30px;padding:20px;">
      <?php echo $results_html;?>
    </div>
  </div>
</div>
<style>



  .orbit-search-grid .orbit-search-filters{ display: none; }
  .orbit-search-grid.filters-visible .orbit-search-filters{ display: block; }

  .orbit-search-grid .orbit-search-filters{
    position: fixed;
    background: #000000dd;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    padding: 20px;
    z-index: 4;
  }

  .orbit-search-grid .orbit-search-filters-box{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate( -50%, -50% );
    padding: 70px 20px 20px;
    background: #fff;
    box-shadow: 4px 4px 20px rgba(0,0,0,.2);
    width: 100%;
    max-width: 320px;
  }




  .orbit-search-grid .orbit-search-filters-box::before{
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    height: 50px;
    width: 100%;
    background-color: #41307c;
    z-index: 2;
  }
  .orbit-search-grid .orbit-search-filters span.filters-title{
    position: absolute;
    z-index: 3;
    color: #fff;
    top: 15px;

  }

  .orbit-search-grid .orbit-search-filters a[href].orbit-btn-close{
    position: absolute;
    top: 0;
    right: 0;
    height: 50px;
    line-height: 50px;
    width: 60px;
    color: #fff;
    font-size: 1.3rem;
    text-align: center;
    background: #32255f;
    -webkit-transition: opacity 0.3s;
    -moz-transition: opacity 0.3s;
    transition: opacity 0.3s;
    z-index: 3;
  }

  .orbit-search-header-top{
    border: #eee solid 1px;
    border-left: none;
    border-right: none;
    padding: 15px;
    color: #555;

    display: grid;
    grid-template-areas: "filter . sort" "title title title";
    grid-gap: 10px;
    grid-template-columns: 140px 1fr 140px;
  }

  .orbit-search-header-top .orbit-form-group{ margin-bottom: 0; }
  .orbit-search-header-top .orbit-form-group select{
    padding: 5px;
    font-size: smaller;
  }

  .orbit-search-header-left{
    grid-area: filter;
  }
  .orbit-search-grid.filters-visible .orbit-search-header-left{ display: none;}


  .orbit-search-header-left a[href]{
    border: #aaa solid 1px;
    padding: 7px 10px;
    display: inline-block;
    font-size: smaller;
  }

  .orbit-search-header-middle{
    text-transform: uppercase;
    grid-area: title;
  }

  .orbit-search-header-right{
    grid-area: sort;
  }

  .orbit-search-inline-terms{
    background: #f9f9f9;
    padding: 10px;
    text-align: center;
  }

  .orbit-search-inline-terms .orbit-terms-count{
    margin-bottom: 10px;
  }

  .orbit-search-inline-terms .orbit-terms-count a[href]{
    display: inline-block;
    font-size: 12px;
    padding: 5px;
    margin: 5px;
    border: #555 solid 1px;
    color: #555;
    border-radius: 5px;
  }
  .orbit-search-inline-terms .orbit-terms-count span.colon,
  .orbit-search-inline-terms .orbit-terms-count span.comma{
    display: none;
  }

  .orbit-search-grid.is-fixed .orbit-search-header{
    position: fixed;
    top: 50px;
    width: 100%;
    background: #fff;
    max-width: 1200px;
    padding-top: 30px;
    left: 0;
  }

  @media( min-width:960px ){
    .orbit-search-grid.filters-visible{
      display: grid;
      grid-template-columns: 250px 1fr;
    }


    .orbit-search-grid.is-fixed .orbit-search-header{
      left: auto;
    }

    .orbit-search-grid.filters-visible.is-fixed .orbit-search-header{
      max-width: 900px;
    }

    .orbit-search-grid .orbit-search-filters{
      position: relative;
      background: none;
      padding: 0;
    }
    .orbit-search-grid .orbit-search-filters-box{
      transform: none;
      position: relative;
      left: auto;
      top: auto;
      max-width: none;
    }
    .orbit-search-grid.is-fixed .orbit-search-filters-box{
      position: fixed;
      width: 250px;
      z-index: 4;
    }
    .orbit-search-header-top{
      display: grid;
      grid-template-areas: "filter title sort";
      grid-template-columns: 140px 1fr 140px;
      padding: 15px 0;
    }
    .orbit-search-header-middle{
      padding-top: 7px;
      text-align: center;
    }
  }
</style>
