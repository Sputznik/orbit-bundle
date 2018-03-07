
var orbit;

if( typeof window.orbit == 'undefined' ){
	orbit = {};
}
else{
	orbit = window.orbit;
}

orbit.views.query = orbit.views.editorBtn.extend({
	id: 'orbit_filter_view',
	template: wp.template('orbit-query'),
	defaultModelValues: function(){
		return { post_type: 'post', posts_per_page: '10', style_id: '0', style: 'db' };
	},
	ajaxShortcodeCallback: function( html ){
		
		/* AJAX CALLBACK REQUEST */
		this.$el.find('.orbit_filter_display').html( html );
		
	},
	shortcodeText: function(){ return 'orbit_query'; },
	getShortcodeParam: function( name, value ){
		
		/* SEPERATE EACH LINE INTO ARRAY AND THEN JOIN BY COMMA */
		if( 'post_type' == name ){
			value = value.split('\n').join(',');
		}
		
		return value;
	},
	
	getFormNamesArr: function(){
		return ['post_type', 'posts_per_page', 'style_id'];
	},
	
	btnSettings: function(){
		return {
			slug	: 'orbitfilter',
			icon	: 'icon dashicons-portfolio',
			tooltip	: 'Create Orbit Query',
			text	: ''
		};
	},
});

jQuery( document ).on( 'ready', function(){
	
	/* INSTANTIATE THE OBJECT OF THE ORBIT FILTER VIEW */
	new orbit.views.query();
} );
