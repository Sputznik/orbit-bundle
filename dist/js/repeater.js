/*
* BASE CLASS FOR REPEATER FIELD
* @dependency: JQUERY
* LIST ELEMENT: THAT CONTAINS ALL THE REPEATER ITEMS
* BUTTON: THAT ADDS REPEATER ITEMS TO THE LIST
*/

var ORBIT_REPEATER = function( options ){
	
	var self = {
		count	: 0,		// KEEP A COUNT OF THE ITEMS THAT HAVE BEEN ADDED
		$list	: null,		// PARENT LIST THAT HOLDS THE CHOICES
		$btn 	: null,		// BUTTON THAT ADDS MORE BLANK CHOICES TO THE LIST
		options : jQuery.extend( {
			$el				: null,
			btn_text		: '+ Add Item',
			close_btn_text	: '&times;',
			list_id			: 'orbit-list',
			list_item_id	: 'orbit-item',
			init			: function(){},
			addItem			: function(){},
			reorder 		: function(){}
		}, options )
	};
	
	self.init = function(){
		
		// MAIN LIST THAT HOLDS THE LIST OF CHOICES
		self.$list = self.createField({
			element: 'ul',
			attr:{
				id	: self.options.list_id
			},
			append	: self.options.$el
		});
		self.$list.sortable({
			stop: function( event, ui ){
				self.reorder();
			}
		});
		
		// BUTTON THAT ADDS THE REPEATER ITEM
		self.$btn = self.createField({
			element	: 'button',
			attr: {
				class: 'button'
			},
			html	: self.options.btn_text,
			append	: self.options.$el
		});
		
		self.$btn.click( function( ev ){
			ev.preventDefault();
			self.addItem();
		});
		
		// FOR CUSTOM FUNCTIONALITY
		self.options.init( self );
		
	};
	
	self.createField = function( field ){
		
		var $form_field = jQuery( document.createElement( field['element'] ) );
		
		for( attr in field['attr'] ){
			$form_field.attr( attr, field['attr'][attr] );
		}
		
		if( field['append'] ){
			$form_field.appendTo( field['append'] );
		}
		
		if( field['html'] ){
			$form_field.html( field['html'] );
		}
		
		return $form_field;
		
	};
	
	
	
	self.createLabel = function( label, $container ){
		
		var $labelField = self.createField({
			element	: 'label',
			html	: label,
			append	: $container
		});
		
		return $labelField;
		
	};
	
	self.createInputTextfield = function( field, $container ){
		
		var $labelField = self.createLabel( field.text, $container );
		
		var $inputField = self.createField({
			element	: 'input',
			attr	: {
				name	: field.slug,
				
			},
			append	: $container
		});
		
		if( field.value != undefined ){
			$inputField.val( field.value );
		}
		
	};
	
	self.createTextarea = function( field, $container ){
		
		var $labelField = self.createLabel( field.text, $container );
		
		var $textField = self.createField({
			element	: 'textarea',
			attr	: {
				name		: field.slug,
				placeholder	: field.help != undefined ? field.help : ''
			},
			append	: $container
		});
		
		if( field.value != undefined ){
			$textField.html( field.value );
		}
	};
	
	self.createDropdown = function( field, $container ){
		
		var $labelField = self.createLabel( field.text, $container );
		
		var $selectField = self.createField({
			element	: 'select',
			attr	: {
				name	: field.slug,
			},
			append	: $container
		});
						
		jQuery.each( field.options, function( opt_val, opt_title ){
							
			var $optionField = self.createField({
				element	: 'option',
				attr	: {
					value	: opt_val,
				},
				html 	: opt_title,
				append	: $selectField
			});
							
		});
		
		if( field.value != undefined ){
			$selectField.val( field.value );
		}
		
	};
	
	/*
	* ADD LIST ITEM TO THE UNLISTED LIST 
	*/
	self.addItem = function( $data ){
		
		// CREATE PARENT LIST ITEM: LI
		var $list_item = self.createField({
			element	: 'li',
			attr	:{
				'class'	: self.options.list_item_id
			},
			append	: self.$list
		});
		
		// CLOSE BUTTON - TO REMOVE THE LIST ITEM
		var $button = self.createField({
			element	: 'button',
			attr	:{
				'class'	: 'orbit-close-btn'
			},
			html	: self.options.close_btn_text,
			append	: $list_item
		});
		
		self.options.addItem( self, $list_item, $button, $data );
		
		// INCREMENT COUNT AFTER AN ITEM HAS BEEN ADDED TO MAINTAIN THE ARRAY OF INPUT NAMES
		self.count++;
		
	};
	
	self.reorder = function(){
		self.options.reorder( self );
	};
	
	self.init();
	
	return self;
};