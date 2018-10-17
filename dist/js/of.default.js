var orbit = {
	models	: {},
	views	: {}
};

orbit.models.default = Backbone.Model.extend({});



orbit.views.editorBtn = wp.Backbone.View.extend({
	tagName: 'div',
	events: {
		'click .media-modal-close'		: 'close',
		'click #orbit_filter_backdrop'	: 'close',
		'submit form'					: 'formSubmit',
		'change form'					: 'formChanged'
	},
	defaultModelValues: function(){
		return {};
	},
	initialize: function(){
		
		var self = this;
		
		/* DEFAULT VALUES FOR THE MODEL */
		self.model = new orbit.models.default();
		self.model.set( self.defaultModelValues() );
		
		/* ON CHANGE, UPDATE THE ENTIRE VIEW */
		self.model.on("change", _.debounce(self.render, 300), self);
		
		
		/* APPEND TO THE BODY */
		this.$el.appendTo( $('body') );
		
		/* HIDE THE MODAL */
		this.close();
		
		/* RENDER THE ELEMENT FROM THE BACKBONE WP TEMPLATE */
		this.render();
		
		/* ADD THE EDITOR BUTTON */
		this.addEditorButton();
		
	},
	render: function(){
		
		/* UPDATE THE HTML WITH THE WP TEMPLATE & MODEL DATA */
		this.$el.html( this.template( this.model.toJSON() ) );
		
		/* DISPLAY THE RESULT OF THE SHORTCODE */
		this.ajaxShortcode();
		
		return this;
		
	},
	open: function( editor ){
		
		/* SAVE THE EDITOR */
		this.editor = editor;
		
		/* OPEN THE MODAL */
		this.$el.show();
	},
	close: function(){
		/* HIDE THE ELEMENT */
		this.$el.hide();
	},
	ajaxShortcode: function(){
		
		var self 		= this,
			shortcode 	= this.createShortcode();
		
		/* AJAX REQUEST */
		jQuery.ajax({
			method	: "GET",
			url		: ajaxurl + "?action=orbit_filter",
			data	: {	shortcode	: encodeURIComponent( shortcode ) }
		}).done( function( html ){
			
			/* AJAX CALLBACK REQUEST */
			self.ajaxShortcodeCallback( html );
			
		});
		
	},
	ajaxShortcodeCallback: function( html ){},
	getShortcodeParam: function( name, value ){ return value; },
	shortcodeText: function(){ return 'sample'; },
	createShortcode: function(){
		
		var self 		= this,
			shortcode 	= "[" + this.shortcodeText(),
			data		= this.model.toJSON();
		
		_.each( data, function( form_value, form_name ){
			
			/* CHECK IF THE FORM VALUE IS NOT EMPTY AND VALID */
			if( form_value && form_value.length ){
				shortcode += " " + form_name + "='" + self.getShortcodeParam( form_name, form_value ) + "'";
			}
		});
		
		
		shortcode += "]";
		
		return shortcode;
	},
	getFormNamesArr: function(){
		return [];
	},
	getFormValue: function( form_name ){
		return this.$el.find("[name="+ form_name +"]").val();
	},
	formChanged: function( ev ){
		
		/* TRIGGERED WHEN THE FORM CHANGES */
		
		var form_names_arr 	= this.getFormNamesArr(),
			self 			= this,
			data			= {};
		
		/* ITERATE FORM ITEMS */
		_.each( form_names_arr, function( form_name ){
			
			var form_value = self.getFormValue( form_name ); 
			
			/* CHECK IF THE FORM VALUE IS NOT EMPTY AND VALID */
			if( form_value && form_value.length ){
				data[form_name] = form_value;
			}
			
		});
		
		/* SET JSON DATA INTO THE MODEL */
		self.model.set( data );
		
	},
	formSubmit: function( ev ){
		
		/* PREVENT DEFAULT BEHAVIOUR */
		ev.preventDefault();
		
		/* CREATE SHORTCODE */
		var shortcode = this.createShortcode();
		
		/* ADD THE SHORTCODE TO THE EDITOR */
		this.editor.execCommand('mceInsertContent', false, shortcode );
		
		/* HIDE THE ORBIT FILTER MODAL */
		this.close();
	},
	btnSettings: function(){
		return {
			slug	: 'sample',
			icon	: 'image',
			tooltip	: 'Sample Tooltip',
			text	: ''
		};
	},
	addEditorButton: function(){
		
		/* ADD THE EDITOR BUTTON */
		
		var self = this;
		
		jQuery( document ).on( 'tinymce-editor-setup', function( event, editor ) {
			
			var btnSettings = self.btnSettings();
			
			editor.settings.toolbar1 += ',' + btnSettings['slug'];
	
			editor.addButton( btnSettings['slug'], {
				text	: btnSettings['text'],
				icon	: btnSettings['icon'],
				tooltip	: btnSettings['tooltip'],
				onclick	: function (event) {
					
					/* ON CLICK OF THE BUTTON, OPEN THE MODAL */
					self.open( editor );
					
				}
			});
			
			
		});
	}
});

if( typeof window.orbit == 'undefined' ){
	window.orbit = orbit;
}