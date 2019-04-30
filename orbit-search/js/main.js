
	jQuery('[data-behaviour~=typeahead]').each(function(){

		var el = jQuery(this);

		el.typeahead({
			name: el.attr('name'),
			minLength:0,
			local: JSON.parse(el.attr('data-arr'))
		});




	});



	jQuery('[data-behaviour~=bt-dropdown-checkboxes]').each(function(){

		var $el = jQuery(this);

		jQuery( document ).on("click", function( event ){
    	if( $el !== event.target && !$el.has( event.target ).length ){
        $el.removeClass('open');
      }
    });

		$el.find('button').click( function(){

			if( !$el.hasClass('open') ){
				// CLOSE OTHER DROPDOWNS THAT ARE OPEN
				jQuery('[data-behaviour~=bt-dropdown-checkboxes].open').removeClass('open');
			}

			$el.toggleClass('open');


		});


	});
