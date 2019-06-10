	jQuery('[data-behaviour~=orbit-field-files]').each(function(){

		var $el = jQuery( this );

		var $images_list = jQuery( document.createElement('ul') );
		$images_list.addClass( 'orbit-images-preview orbit-list-inline' );
		$images_list.appendTo( $el.parent() );

		function addImage( src ){

			var $item = jQuery( document.createElement( 'li' ) );
			$item.appendTo( $images_list );

			var $img = jQuery( document.createElement( 'img' ) );
			$img.attr( 'src', src );
			$img.appendTo( $item );

		}

		function readURL(input) {
      if( input.files ){

				$images_list.html('');

				jQuery.each( input.files, function( j, single_file ){

					var reader = new FileReader();

	        reader.onload = function (e) {
						addImage( e.target.result );
					}

					if( single_file.type.indexOf( 'image' ) !== -1 ){
						reader.readAsDataURL( single_file );
					}
				});
			}
    }

		$el.change( function(){
			readURL( this );
		});
	});

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
