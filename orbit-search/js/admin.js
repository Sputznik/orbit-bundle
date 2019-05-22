jQuery(document).ready(function(){
  jQuery('[data-behaviour="orbit-admin-filters"]').each(function(){

    var $el = jQuery( this ),
      atts  = $el.data( 'atts' );

    console.log( atts );

    var repeater = SPACE_REPEATER( {
			$el				      : $el,
			btn_text		    : '+ Add Filter',
			close_btn_text	: 'Delete Filter',
      list_id         : 'orbit-repeater-list',
      list_item_id	  : 'orbit-repeater-filter',
			init	: function( repeater ){




			},
			addItem	: function( repeater, $list_item, $closeButton, page ){

				/*
				* ADD LIST ITEM TO THE UNLISTED LIST
				* TEXTAREA: page TITLE
				* HIDDEN: page ID
				* HIDDEN: page COUNT
				*/

				if( page == undefined || page['ID'] == undefined ){
					page = { ID : 0 };
				}

				// CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT
				repeater.addCollapsibleItem( $list_item, $closeButton );

				var $header = $list_item.find( '.list-header' );
				var $content = $list_item.find( '.list-content' );

				// PAGE TITLE
				var $textarea = repeater.createField({
					element	: 'textarea',
					attr	: {
						'data-behaviour': 'space-autoresize',
						'placeholder'	: 'Type Page Title Here',
						'name'			: 'pages[' + repeater.count + '][title]',
						'value'			: 'Page ' + ( repeater.count + 1 )
					},
					append	: $header
				});
				//$textarea.space_autoresize();
				if( page['title'] ){ $textarea.val( page['title'] ); }

//////////////create filter fields////////////////////

        //Label
        var $label_text = repeater.createField({
          element : 'label',
          attr    : {
            'class' : 'filter-label'
          },
          html    : 'Filter Label',
          append  : $content
        });

        //Label field
        var $filter_text = repeater.createField({
          element	: 'input',
          attr	: {
            'type'  : 'text',
            'placeholder' : 'Filter Label',
            'class' : 'filters label'
          },
          append	: $content
        });

        //Filter form style
        var $form_field = repeater.createDropdownField({
          attr    : {

          },
          options : atts['form'],
          append	: $content,
          label   : 'Form Field'
        });

        var $filter_type = repeater.createDropdownField({
					attr	:  {
					//	name	: parent_name + '[rules]['+ repeater.count +'][action]',
					},
          options : atts['types'],
					//value	: rule['action'],
					append	: $content,
					label	: 'Filter by'
				});

        //Filter typeVAL
        var $filter_typeval = repeater.createDropdownField({
          attr	: {},
          options : {},
          append	: $content,
          label   : 'Filter Value'
        });

        // OPTIONS OF FILTER TYPE BY VALUE ARE RESET BASED ON THE VALUE SELECTED IN FILTER TYPE
        function updateOptionsForFilterTypeValue(){
          var type = $filter_type.find('select').val(),
            options = atts[ type + '_options' ];
          $filter_typeval.setOptions( options );
        }

        // ON CHANGE OF FILTER TYPE TRIGGER AN UPDATE IN OPTIONS OF FILTER TYPE BY VALUE
        $filter_type.find('select').change(function(){
          updateOptionsForFilterTypeValue();
        });
        updateOptionsForFilterTypeValue();  // SET OPTIONS FOR THE FIRST LOAD

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
				repeater.$list.find( '[data-behaviour~=space-rank]' ).each( function(){
					var $hiddenRank = jQuery( this );
					$hiddenRank.val( rank );
					rank++;
				});
			},
		} );


  });
});
