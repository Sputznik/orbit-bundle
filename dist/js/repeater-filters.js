jQuery.fn.repeater_filters = function(){

	return this.each(function() {

    var $el = jQuery( this ),
      atts  = $el.data( 'atts' );

    console.log( atts );

    var repeater = ORBIT_REPEATER( {
			$el				      : $el,
			btn_text		    : '+ Add Filter',
			close_btn_text	: 'Delete Filter',
      list_id         : 'orbit-repeater-list',
      list_item_id	  : 'orbit-repeater-filter',
			list_item_types : atts['sections'],
			init	: function( repeater ){

        // ITERATE THROUGH EACH PAGES IN THE DB
        jQuery.each( atts.db, function( i, filter ){
          if( filter['label'] != undefined && filter['type'] != undefined ){ repeater.addItem( filter ); }
        });

      },
			addItem	: function( repeater, $list_item, $closeButton, filter ){

				/*
				* ADD LIST ITEM TO THE UNLISTED LIST
				* TEXTAREA: page TITLE
				* HIDDEN: page ID
				* HIDDEN: page COUNT
				*/

        if( filter == undefined ){ filter = { label : '', type : 'tax' }; }

				// CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT
				repeater.addCollapsibleItem( $list_item, $closeButton );

				var $header = $list_item.find( '.list-header' );
				var $content = $list_item.find( '.list-content' );


				if( filter['type'] == 'tax' ){
					atts['forms'] = jQuery.extend( atts['forms'], atts['tax_forms'] );

					console.log( atts['forms'] );
					
				}



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

				// BUBBLE FIELD THAT IDENTIFIES THE REPEATER FIELD ITEM
        var $bubble = repeater.createField({
          element : 'div',
          attr    : { class : 'orbit-bubble' },
          html    : atts['sections'][ filter['type'] ],
          append  : $header
        });

				// MAIN FILTER TYPE FIELD
				var $filter_type = repeater.createField({
					element	: 'input',
					attr	:  {
						type	: 'hidden',
						name	: getAttrName( 'type' ),
						value : filter['type'] ? filter['type'] : '',
					},
					append	: $content,
				});

				// HIDE LABEL HERE
				var $hide_label = createBooleanField( {
					label 	: 'Hide Label',
					slug		: 'hide_label',
					append	: $content
				} );

				// CHOOSE FORM FIELD - DROPDOWN, TEXT, RADIO, CHECKBOX ETC
        var $form_field = repeater.createDropdownField({
          attr    : { name	: getAttrName( 'form' ) },
          value   : filter['form'] ? filter['form'] : '',
          options : atts['forms'],
          append	: $content,
          label   : 'Form Field'
        });

				//	Filter typevalue - terms for taxonomy & year, after & before queries for postdate
        var $filter_typeval = repeater.createDropdownField({
          attr	: { name : getAttrName( 'typeval' ) },
          options : atts[ filter['type'] + '_options' ],
          append	: $content,
          label   : 'Filter Value'
        });
				// DEFAULT VALUE COMING FROM THE DB
        if( filter['typeval'] ){ $filter_typeval.selectOption( filter['typeval'] ); }


				// COMMON FIELDS END HERE
				switch( filter['type'] ){
					case 'tax':

						// 	BOOLEAN FIELD - IF CHECKED WILL SHOW ALL THE EMPTY TERMS
						var $tax_hide_empty = createBooleanField( {
							label 	: 'Show empty terms',
							slug		: 'tax_show_empty',
							append	: $content
						} );

						break;

					case 'postdate':
						break;
				}

				// 	HELPER FUNCTION TO CREATE BOOLEAN FIELDS
				function createBooleanField( field ){
					var flag = false;
					if( filter && filter[ field.slug ] && filter[ field.slug ] > 0 ){ flag = true; }

	        return repeater.createBooleanField({
	          attr   :  {
	            name		: getAttrName( field.slug ),
	            checked	: flag,
	          },
	          label  :  field.label,
	          append :  field.append
	        });
				}

				function getAttrName( slug ){
					return 'orbit_filter[' + repeater.count + '][' + slug + ']'
				}

        //CREATE A HIDDEN FIELD
        var $hidden = repeater.createField({
          element	: 'input',
          attr	: {
            'type'	          : 'hidden',
            'value'				    : repeater.count,
            'data-behaviour' 	: 'orbit-rank',
            'name'				    : 'orbit_filter[' + repeater.count + '][order]'
          },
          append	: $list_item
        });

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
				var rank = 0;
				repeater.$list.find( '[data-behaviour~=orbit-rank]' ).each( function(){
					var $hiddenRank = jQuery( this );
					$hiddenRank.val( rank );
					rank++;
				});
			},
		} );

  });

};


jQuery(document).ready(function(){
  jQuery('[data-behaviour="orbit-admin-filters"]').repeater_filters();
});
