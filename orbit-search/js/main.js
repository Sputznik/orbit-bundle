
	jQuery('[data-behaviour~=typeahead]').each(function(){

		var el = jQuery(this);

		el.typeahead({
			name: el.attr('name'),
			minLength:0,
			local: JSON.parse(el.attr('data-arr'))
		});

	});

	jQuery('[data-behaviour~=orbit-search]').each(function(){

		var $el 				= jQuery(this),
			$search_form 	= $el.find('.orbit-search-form');

			function makeFormCollapsible(){
				var $form 						= $search_form.find('form'),
					window_width				= jQuery( window ).width();
					$search_form_arrow 	= $search_form.find('.orbit-search-form-title .arrow-down');

				// hide the form on mobile and make it collapsible
				if( window_width < 768 ){

					// hide the form
					$form.hide();

					// trigger collapsible
					$search_form_arrow.click( function(){
						$form.toggle('slide');
					});
				}
			}

			function init(){

				makeFormCollapsible();

			}

		init();

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
