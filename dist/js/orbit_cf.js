jQuery.fn.orbit_repeater_cf = function(){

	return this.each(function() {

		var $el 	= jQuery(this),
			slug		= $el.data('slug'),
			rows		= $el.data('rows'),
			rules 	= $el.data('rules'),
			fields	= $el.data('fields');

		var repeater = ORBIT_REPEATER( {
			$el							: $el,
			btn_text				: '+ Add Custom Field',
			close_btn_text	: 'Delete Field',
			init	: function( repeater ){

				/*
				* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE CHOICE, HIDDEN FIELD AND THE ADD BUTTON
				*/

				// ITERATE THROUGH EACH VALUES IN THE DB
				if( rows != undefined ){
					jQuery.each( rows, function( i, row ){
						repeater.addItem( row );
					});
				}
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

				repeater.addCollapsibleItem( $list_item, $closeButton );

				var $header = $list_item.find( '.list-header' );
				var $content = $list_item.find( '.list-content' );

				var $cf_name= repeater.createField({
					element : 'label',
					attr:{
						'name' : 'customfield[' + repeater.count + ']'
					},
					html: 'Custom Field '+(repeater.count+1),
					append: $header
				});

				function getSlug( field_slug ){
					return slug + "[" + $list_item.data('count') + "]" + "[" + field_slug + "]";
				}

				function getFieldElement( field_slug ){
					var slug = getSlug( field_slug );
					return $list_item.find( '[name="' + slug + '"]' );
				}

				function getFieldContainer( field_slug ){
					return $list_item.find( '.orbit-field.orbit-field-' + field_slug );
				}

				$list_item.data( 'count', repeater.count );

				jQuery.each( fields, function( field_slug, field ){

					field.label = field.text;

					field.slug = getSlug( field_slug );

					field.value = undefined;

					if( row[ field_slug ] != undefined ){ field.value = row[ field_slug ]; }

					field.attr = {
						name: field.slug
					};

					var $containerField = repeater.createField({
						element	: 'div',
						attr	: {
							'class'	: 'orbit-field orbit-field-' + field_slug,
						},
						append	: $content
					});

					field.append = $containerField;

					switch( field.type ){

						case 'dropdown':
							repeater.createDropdownField( field );
							break;

						case 'text':
							repeater.createInputTextField( field );
							break;

						case 'textarea':
							repeater.createTextareaField( field );
							break;

						case 'repeater-options':
							var $cf_options = repeater.createField({
								element	: 'div',
								attr	: {
									'data-behaviour' 	: 'orbit-repeater-cf',
									'data-atts'       : JSON.stringify( row['options'] ? row['options'] : [] )
								},
								append	: $containerField
							});
							$cf_options.repeater_options( field.slug );
							break;
					}

				});

				function checkForRules(){

					// ITERATE THROUGH THE RULES
					jQuery.each( rules, function( target_slug, rule ){

						var $target = getFieldContainer( target_slug );

						jQuery.each( rule, function( action, deps_arr ){

							var flag = false;
							jQuery.each( deps_arr, function( dep_field_slug, allowed_values ){
								var $dep_field = getFieldElement( dep_field_slug );
								if( jQuery.inArray( $dep_field.val(), allowed_values ) != -1 ){ flag = true; }
							});	// ITERATE THROUGH DEPENDANT ELEMENTS

							// FLAG FULFILLS OR CONDITION IN THE DEPENDANT FIELDS
							if( flag ){
								if( action == 'hide' ){ $target.hide(); }
								else{ $target.show(); }
							}
						});	// ITERATE THROUGH ACTIONS
					});
				}

				// CHECK FOR RULES WHENEVER A VALUE IS CHANGED
				$list_item.on( 'change', function(){
					checkForRules();
				});
				// DEFAULT CHECK WHEN THE LIST ITEM IS CREATED
				checkForRules();

				$closeButton.click( function( ev ){
					ev.preventDefault();
					if( confirm( 'Are you sure you want to remove this?' ) ){
						// IF PAGE ID IS NOT EMPTY THAT MEANS IT IS ALREADY IN THE DB, SO THE ID HAS TO BE PUSHED INTO THE HIDDEN DELETED FIELD
						$list_item.remove();
					}

				});


			},

		} );




	});

};



jQuery( document ).on( 'ready', function(){
	jQuery('[data-behaviour~=orbit-repeater-cf]').orbit_repeater_cf();
} );
