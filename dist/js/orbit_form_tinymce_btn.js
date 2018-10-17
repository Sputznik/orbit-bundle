
var orbit;

if( typeof window.orbit == 'undefined' ){
	orbit = {};
}
else{
	orbit = window.orbit;
}

orbit.views.filterBtn = orbit.views.editorBtn.extend({
	id: 'orbit_filter_view',
	template: wp.template('orbit-filter'),
	defaultModelValues: function(){
		return { type: 'tax', typeval: 'category', form: 'checkbox', placeholder: '', options: '', label: '' };
	},
	ajaxShortcodeCallback: function( html ){
		
		/* AJAX CALLBACK REQUEST */
		this.$el.find('.orbit_filter_display').html( html );
		
	},
	shortcodeText: function(){ return 'orbit_filter'; },
	getShortcodeParam: function( name, value ){
		
		/* SEPERATE EACH LINE INTO ARRAY AND THEN JOIN BY COMMA */
		if( 'options' == name ){
			value = value.split('\n').join(',');
		}
		
		return value;
	},
	getFormNamesArr: function(){
		return ['type', 'typeval', 'form', 'placeholder', 'label', 'options'];
	},
	
	btnSettings: function(){
		return {
			slug	: 'orbitfilter',
			icon	: 'icon dashicons-search',
			tooltip	: 'Create Orbit Filter for Search',
			text	: ''
		};
	},
});

jQuery( document ).on( 'ready', function(){
	
	/* INSTANTIATE THE OBJECT OF THE ORBIT FILTER VIEW */
	new orbit.views.filterBtn();
} );
