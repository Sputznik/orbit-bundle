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
			$el							: null,
			btn_text				: '+ Add Item',
			close_btn_text	: '&times;',
			list_id					: 'orbit-choices-list',
			list_item_types	: {},
			list_item_id		: 'orbit-choice-item',
			init						: function(){},
			addItem					: function(){},
			reorder 				: function(){}
		}, options )
	};

	// CHECK IF NESTED TYPES ARE PRESENT
	self.hasNestedTypes = function(){
		return Object.keys( self.options.list_item_types ).length;
	};

	// CREATE A NESTED BUTTONS DROPDOWN
	self.createNestedButtons = function(){
		self.$list_types_wrapper = self.createField({
			element	: 'div',
			attr 		: {
				class : 'orbit-sections-parent'
			},
			append 	: self.options.$el
		});

		// RECHANGE THE BUTTON PLACEMENT
		self.$btn.appendTo( self.$list_types_wrapper );

		self.$list_types = self.createField({
			element	: 'ul',
			attr 		: {
				class : 'orbit-sections-dropdown'
			},
			append 	: self.$list_types_wrapper
		});
		// CREATE CHILD ITEM FOR THESE TYPES
		for( type in self.options.list_item_types ){
			var $item = self.createField({
				element	: 'li',
				html		: '<button data-type="' + type + '" class="button">' + self.options.list_item_types[ type ] + '</button>',
				append 	: self.$list_types
			});

			$item.find('button').click( function( ev ){
				ev.preventDefault();

				var $btn 	= jQuery( ev.target ),
					data 		= {
						type: $btn.data('type')
					};
				self.addItem( data );
			});
		}

		self.$list_types.hide();
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

		// TRIGGER: SO THAT THE ITEMS IN THE LIST CAN BE SORTABLE
		self.$list.sortable({
			stop: function( event, ui ){
				self.reorder();
			}
		});

		// BUTTON THAT ADDS THE REPEATER ITEM
		self.$btn = self.createField({
			element	: 'button',
			attr: {
				class: 'button btn-add'
			},
			html	: self.options.btn_text,
			append	: self.options.$el
		});

		// LIST TYPES - nestd buttons
		if( self.hasNestedTypes() ){
			self.createNestedButtons();
		}

		self.$btn.click( function( ev ){
			ev.preventDefault();
			// SHOW THE INLINE BUTTONS IF THERE ARE ANY
			if( self.hasNestedTypes() ){
				self.$list_types.toggle('slide');
			}
			else{
				self.addItem();
			}
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

		if( field['prepend'] ){
			$form_field.prependTo( field['prepend'] );
		}

		if( field['html'] ){
			$form_field.html( field['html'] );
		}

		return $form_field;

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

	/*
	* CREATE BASIC MARKUP FOR COLLAPSIBLE ITEM
	* HEADER & CONTENT AREA
	*/
	self.addCollapsibleItem = function( $list_item, $closeButton ){

		// CREATE NEAT HEADER AREA FOR THE ITEM
		var $header = self.createField({
			element	: 'div',
			attr	: {
				'class'	: 'list-header'
			},
			append	: $list_item
		});

		// CREATE NEAT CONTENT AREA FOR THE ITEM
		var $content = self.createField({
			element	: 'div',
			attr	: {
				'class'	: 'list-content'
			},
			append	: $list_item
		});

		// APPEND THE CLOSE BUTTON TO THE LIST CONTENT
		$closeButton.appendTo( $content );

		// BUTTON THAT COLLAPSES THE ENTIRE LIST
		var $collapseBtn = self.createField({
			element	: 'button',
			attr	: {
				class : 'orbit-collapse'
			},
			append 	: $header
		});

		// ON CLICK OF COLLAPSE BUTTON, TOGGLE THE CONTENT AREA
		$collapseBtn.click( function( ev ){
			ev.preventDefault();
			$content.slideToggle();
		});
		$collapseBtn.click();

	};

	/*
	* TINYMCE EDITOR
	* DEPENDENCY TO ENQUEUE WP EDITOR ASSETS
	*/
	self.createRichText = function( field ){

		var $wrapper = self.createField({
			element	: 'div',
			attr	: {
				class : 'orbit-admin-text'
			},
			append	: field['append']
		});

		var $textarea = self.createField( {
			element	: 'textarea',
			attr 		: {
				id 		: field['attr']['id'] ? field['attr']['id'] : 'sample-id',
				name	: field['attr']['name']
			},
			html		: field['html'] ? field['html'] : "",
			append : $wrapper
		} );

		$textarea.css({width:'100%'});

		// INITIALIZE WP EDITOR FOR THE TEXTAREA
		wp.editor.initialize( field['attr']['id'], { tinymce: {height: 300}, quicktags: true } );

		return $wrapper;

	};

	/*
	* BOOLEAN FIELD
	*/
	self.createBooleanField = function( field ){

		var $label = self.createField({
			element	: 'label',
			append	: field['append'],
			html	: field['label']
		});

		var $booleanField = self.createField({
			element	: 'input',
			attr	: {
				type	: 'checkbox',
				name	: field['attr']['name'],
				checked	: field['attr']['checked'],
				value	: 1
			},
			prepend	: $label
		});

		return $label;

	};

	/*
	* INPUT TEXT FIELD
	*/
	self.createInputTextField = function( field ){

		var $wrapper = self.createField({
			element	: 'div',
			attr	: {
				class : 'orbit-admin-text'
			},
			append	: field['append']
		});

		var $label = self.createField({
			element	: 'label',
			append	: $wrapper,
			html		: field['label']
		});

		var $inputField = self.createField({
			element	: 'input',
			attr	: {
				type				: 'text',
				name				: field['attr']['name'],
				placeholder	: field['attr']['placeholder'],
				value				: field['value']
			},
			append	: $wrapper
		});

		if( field['help'] != undefined ){

			var $help = self.createField({
				element	: 'p',
				attr		: {
					class : 'help'
				},
				append	: $wrapper,
				html		: field['help']
			});

		}

		$wrapper.val = function( value ){
			$inputField.val( value );
		};

		return $wrapper;

	};

	/*
	* TEXTAREA FIELD
	*/
	self.createTextareaField = function( field ){

		var $wrapper = self.createField({
			element	: 'div',
			attr	: {
				class : 'orbit-admin-text'
			},
			append	: field['append']
		});

		var $label = self.createField({
			element	: 'label',
			append	: $wrapper,
			html		: field['label']
		});

		var $textarea = self.createField({
			element	: 'textarea',
			attr	: {
				name		: field['attr']['name'],
			},
			html	: field['value'],
			append	: $wrapper
		});

		if( field['help'] != undefined ){

			var $help = self.createField({
				element	: 'p',
				attr		: {
					class : 'help'
				},
				append	: $wrapper,
				html		: field['help']
			});

		}

		return $wrapper;

	};

	/*
	* DROPDOWN FIELD
	*/
	self.createDropdownField = function( field ){

		var $wrapper = self.createField({
			element	: 'div',
			attr	: {
				class : 'orbit-admin-dropdown'
			},
			append	: field['append']
		});

		var $label = self.createField({
			element	: 'label',
			append	: $wrapper,
			html	: field['label']
		});

		var $select = self.createField({
			element	: 'select',
			attr	: {
				name : field['attr']['name']
			},
			append	: $wrapper,
		});

		// ADD ONE OPTION TO THE SELECT DROPDOWN
		$wrapper.addOption = function( slug, value ){

			var $option = self.createField({
				element	: 'option',
				attr	: {
					value : slug
				},
				html	: value,
				append	: $select
			});

		};

		// SET THE ENTIRE OPTIONS FOR THE SELECT DROPDOWN
		$wrapper.setOptions = function( options ){
			// FIRST REMOVE ALL THE CURRENT OPTIONS THAT ARE THERE
			$select.find('option').remove();
			for( slug in options ){
				$wrapper.addOption( slug, options[slug] );
			}
		}

		$wrapper.selectOption = function( slug ){
			$select.val( slug );
		};

		if( field['options'] ){
			$wrapper.setOptions( field['options'] );
		}


		if( field['value'] ){
			$wrapper.selectOption( field['value'] );
		}

		return $wrapper;
	};

	self.reorder = function(){
		self.options.reorder( self );
	};

	self.init();

	return self;
};
