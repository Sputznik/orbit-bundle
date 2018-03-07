	
	jQuery('[data-behaviour~=typeahead]').each(function(){
		
		var el = jQuery(this);
		
		el.typeahead({
			name: el.attr('name'),
			minLength:0,
			local: JSON.parse(el.attr('data-arr'))
		});
		
		
		
		
	});
	
	
	