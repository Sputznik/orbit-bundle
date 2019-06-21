jQuery.fn.repeater_columns = function(){

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
          if( filter != undefined && filter['label'] != undefined && filter['type'] != undefined ){
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

        console.log( filter );

        if( filter == undefined || filter['type'] == undefined ){
					filter = { type : 'tax' };
				}

        // CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT
				repeater.addCollapsibleItem( $list_item, $closeButton );

				var $header           = $list_item.find( '.list-header' ),
				    $content          = $list_item.find( '.list-content' ),
            filter_type_text  = data['sections'][ filter['type'] ],
            common_name       = 'orbit_export_csv_cols[' + repeater.count + ']';

        // TEXTAREA FOR FORM FIELD LABEL
    		var $textarea = repeater.createField({
    			element	: 'textarea',
    			attr	: {
    				//'data-behaviour': 'space-autoresize',
    				'placeholder'	  : 'Type Form Field Name Here',
    				'name'			    : common_name + '[label]',
    				'value'			    : filter_type_text + ' ' + ( repeater.count + 1 )
    			},
    			append	: $header
    	   });
    		//$textarea.space_autoresize();
    		if( filter['label'] ){ $textarea.val( filter['label'] ); }

        // BUBBLE FIELD THAT IDENTIFIES THE REPEATER FIELD ITEM
        var $bubble = repeater.createField({
          element : 'div',
          attr    : {
            class : 'orbit-bubble'
          },
          html    : filter_type_text,
          append  : $header
        });

        var $hidden = repeater.createField({
          element : 'input',
          attr    : {
            type : 'hidden',
            name : common_name + '[type]',
            value: filter['type']
          },
          append  : $content
        });

        // REUSABLE HELPER FUNCTION TO CREATE DROPDOWN FIELD
        function createDropdownField( field ){
          var $field = repeater.createDropdownField({
            attr	: {
              name	: common_name + '[' + field['slug'] + ']'
            },
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
              placeholder : field['placeholder'] ? field['placeholder'] : '',
              name        : common_name + '[' + field['slug'] +']'
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
  jQuery('[data-behaviour="orbit-export"]').repeater_columns();
});
