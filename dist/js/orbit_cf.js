jQuery.fn.orbit_repeater = function(){
	
	return this.each(function() {
		
		var $el 	= jQuery(this),
			slug	= $el.data('slug'),
			rows	= $el.data('rows'),
			fields	= $el.data('fields');
		
		var repeater = ORBIT_REPEATER( {
			$el		: $el,
			btn_text: '+ Add Custom Field',
			init	: function( repeater ){
				
				/*
				* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE CHOICE, HIDDEN FIELD AND THE ADD BUTTON
				*/
				
				// ITERATE THROUGH EACH CHOICES IN THE DB
				jQuery.each( rows, function( i, row ){
					
					repeater.addItem( row );
					
				});
				
			},
			addItem	: function( repeater, $list_item, $closeButton, row ){
				
				/*
				* ADD LIST ITEM TO THE UNLISTED LIST 
				* TEXTAREA: CHOICE TITLE
				* HIDDEN: CHOICE ID
				* HIDDEN: CHOICE COUNT
				*/
				
				
				if( row == undefined ){
					row = {};
				}
				
				jQuery.each( fields, function( field_slug, field ){
					
					field.slug = slug + "[" + repeater.count + "]" + "[" + field_slug + "]";
					
					field.value = undefined;
					
					if( row[ field_slug ] != undefined ){
						field.value = row[ field_slug ];
					}
					
					var $containerField = repeater.createField({
						element	: 'div',
						attr	: {
							'class'	: 'orbit-field',
						},
						append	: $list_item
					});
						
					
					if( field.type == 'dropdown' ){
						repeater.createDropdown( field, $containerField );
					}
					else if( field.type == 'text' ){
						repeater.createInputTextfield( field, $containerField );
					}
					else if( field.type == 'textarea' ){
						repeater.createTextarea( field, $containerField );
					}
					
					
				});
				
				$closeButton.click( function( ev ){
					ev.preventDefault();
					
					$list_item.remove();
				});
				
			},
			
		} );
		
		
		
		
	});
	
};



jQuery( document ).on( 'ready', function(){
	
	jQuery('[data-behaviour~=orbit-repeater]').orbit_repeater();
} );