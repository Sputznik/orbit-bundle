jQuery.fn.orbit_repeater = function(){

	return this.each(function() {
		   
		var $el 			= jQuery(this),
			slug			= $el.data('slug'),
			slugs			= $el.data('slugs').split(','),
			$hidden_item	= $el.find('.hidden-item'),
			$nested_fields 	= $el.find('.nested-fields');
		
		$el.find('[data-behaviour~=clone]').click( function(){
			
			var $item = jQuery( document.createElement('div') );
			$item.addClass('item');
			$item.css( { border: "#ddd solid 1px", padding: "0 20px", marginBottom: "20px" } );
			$item.html( $hidden_item.html() );
			
			var count = $nested_fields.find('.item').length;
			
			// Iterate through each list of slugs
			for( i=0; i<slugs.length; i++ ){
				$item.find('[name=' + slugs[i] + ']').attr('name', slug + '[' + count + '][' + slugs[i] + ']' );
			}
			
			$item.appendTo( $nested_fields );
			
		});
			
	});
};



jQuery( document ).on( 'ready', function(){
	
	jQuery('[data-behaviour~=orbit-repeater]').orbit_repeater();
} );
