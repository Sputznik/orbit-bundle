jQuery.fn.repeater_sort = function(){

  return this.each(function() {

    var $el  = jQuery( this ),
        data = $el.data( 'atts' );

    var repeater = ORBIT_REPEATER( {
			$el				      : $el,
			btn_text		    : '+ Add Column',
			close_btn_text	: 'Delete Column',
      list_id         : 'orbit-slide-repeater-list',
      list_item_id	  : 'orbit-slide-repeater',
      list_item_types : data['sections'],
			init	: function( repeater ){

        // ITERATE THROUGH EACH PAGES IN THE DB
        jQuery.each( data['db'], function( i, filter ){
          if( filter != undefined && filter['type'] != undefined ){
            repeater.addItem( filter );
          }
        });

			},
			addItem	: function( repeater, $list_item, $closeButton, filter ){

				/*
				* ADD LIST ITEM TO THE UNLISTED LIST
				* TEXTAREA: page TITLE
				* HIDDEN: page ID
				* HIDDEN: page COUNT
				*/

        if( filter == undefined || filter['type'] == undefined ){
					filter = { type : 'tax' };
				}

        // CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT
				repeater.addCollapsibleItem( $list_item, $closeButton );

				var $header           = $list_item.find( '.list-header' ),
				    $content          = $list_item.find( '.list-content' ),
            filter_type_text  = data['sections'][ filter['type'] ];


        $list_item.data( 'count', repeater.count );
        $list_item.data( 'type', filter_type_text );

        // LABEL
				var $textarea = repeater.createField({
					element	: 'textarea',
					attr	: {
						'data-behaviour': 'space-autoresize',
						'placeholder'	: 'Type Label Here',
						'name'			: getAttrName( 'label' ),
						'value'			: 'Label ' + ( repeater.count + 1 )
					},
					append	: $header
				});
				if( filter['label'] ){ $textarea.val( filter['label'] ); }


        var $hidden = repeater.createField({
          element : 'input',
          attr    : {
            type : 'hidden',
            name : getAttrName( 'type' ),
            value: filter['type']
          },
          append  : $content
        });



        function getAttrName( slug ){
          var common_name = 'orbit_export_csv_cols[' + $list_item.data('count') + ']';
          return common_name + '[' + slug + ']';
        }

        // REUSABLE HELPER FUNCTION TO CREATE DROPDOWN FIELD
        function createDropdownField( field ){
          var $field = repeater.createDropdownField({
            attr	: { name	: getAttrName( field['slug'] ) },
            value   : filter[ field['slug'] ] ? filter[ field['slug'] ] : '',
            options : field['options'],
            append	: field['append'],
            label   : field['label']
          });
          if( filter[ field['slug'] ] != undefined ){ $field.selectOption( filter[ field['slug'] ] ); }
          return $field;
        }

        // REUSABLE HELPER FUNCTION TO CREATE INPUT TEXT FIELD
        function createTextField( field ){
          var $field = repeater.createInputTextField({
            label : field['label'] ? field['label'] : '',
            attr  : {
              placeholder  : field['placeholder'] ? field['placeholder'] : '',
              name	       : getAttrName( field['slug'] )
            },
            help    : field['help'] ? field['help'] : '',
            append  : field['append']
          });
          if( filter[ field['slug'] ] != undefined ){ $field.val( filter[ field['slug'] ] ); }
          return $field;
        }

        switch( filter['type'] ){

          case 'post':

            var $postfield = createDropdownField( {
              label   : 'Choose Post Field',
              slug    : 'field',
              options : data['post_options'],
              append  : $content
            } );
            break;

          case 'tax':
            var $term = createDropdownField( {
              label   : 'Choose Term',
              slug    : 'field',
              options : data['tax_options'],
              append  : $content
            } );
            break;

          case 'cf':
            var $metafield = createTextField( {
              label       : 'Slug of the Metafield',
              slug        : 'field',
              placeholder : "Metafield Slug",
              append      : $content
            } );
            break;

        }

        var $order = createDropdownField( {
          label       : 'Order',
          slug        : 'order',
          options     : {
            'ASC'   : 'Ascending',
            'DESC'  : 'Descending'
          },
          append      : $content
        } );


        // CLOSE EVENT ON CLICK OF THE BUTTON
        $closeButton.click( function( ev ){
					ev.preventDefault();
					if( confirm( 'Are you sure you want to remove this?' ) ){
						// IF PAGE ID IS NOT EMPTY THAT MEANS IT IS ALREADY IN THE DB, SO THE ID HAS TO BE PUSHED INTO THE HIDDEN DELETED FIELD
						$list_item.remove();
					}
				});

      },
			reorder: function( repeater ){
				/*
				* REORDER LIST
				*/
        /*
				var rank = 0;
				repeater.$list.find( '[data-behaviour~=orbit-form-slide]' ).each( function(){
					var $hiddenRank = jQuery( this );
					$hiddenRank.val( rank );
					rank++;
				});
        */
			},
		} );//orbit-repeater

  });
};
jQuery(document).ready(function(){
  jQuery('[data-behaviour="orbit-sort"]').repeater_sort();
});
