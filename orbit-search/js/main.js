/*
* NESTED DROPDOWN
*/

jQuery( '[data-behaviour~="orbit-nested-dropdown"]' ).each( function(){

	var $el             = jQuery( this ),
		$form 						= $el.closest( 'form' ),
		$cats_dropdown    = $el.find( '.cats select' ),
		$subcats_dropdown = $el.find( '.subcats select' ),
		$cloneSubDropdown = $el.find( '.subcats select' ).clone();  // Clones all subcats from dropdown

	function updateSubDropdown( defaultValue ){
		var currentCategoryValue = $cats_dropdown.val();

		var subcat_value = defaultValue ? defaultValue : 0;

		$subcats_dropdown.find( 'option' ).remove();

		var $options;

		if( currentCategoryValue > 0 ){
			$options = $cloneSubDropdown.find( 'option[data-parent~="' + currentCategoryValue + '"]' ).clone();

			var $defaultOption = jQuery( document.createElement( 'option' ) );
			$defaultOption.val( 0 );
			$defaultOption.html('Select');
			$defaultOption.appendTo( $subcats_dropdown );

			$options.appendTo( $subcats_dropdown );

			$subcats_dropdown.val( subcat_value );

			$subcats_dropdown.show();
		}
		else{
			$subcats_dropdown.hide();
		}
	}





	// change subservices when the main service is changed
	$cats_dropdown.change( function( ev ){
		updateSubDropdown();
	});

	/*
	* CHANGE THE NAME OF THE PARENT DROPDOWN IF THE CHILD HAS BEEN SELECTED
	* SO THAT THE QUERY IS DONE SOLELY BASED ON THE CHILD
	*/
	$form.submit( function( ev ){

		var subcat_value = parseInt( $subcats_dropdown.val() );

		if( subcat_value ){
			var taxonomy = $cats_dropdown.attr( 'name' ).replace( 'tax_', '' ).replace( '[]', '' ),
				fieldName	 = 'parent_' + taxonomy;
			$cats_dropdown.attr( 'name', fieldName );
			//alert( fieldName );
		}
	} );

	// ON THE INITIAL LOAD HIDE THE DROPDOWN
	if( !$cats_dropdown.val() ){
		$subcats_dropdown.hide();
	}
	else{
		var temp_subcat_val = $subcats_dropdown.val();

		temp_subcat_val = temp_subcat_val ? temp_subcat_val : 0;

		updateSubDropdown( temp_subcat_val );
	}
});

jQuery('[data-behaviour~="orbit-nested-dropdown-checkboxes"]').each(function(){

	var $el 					= jQuery(this),
		$form 					= $el.closest( 'form' ),
		$cats_dropdown  = $el.find( '.cats select' ),
		$subcats 				= $el.find( '.subcats' ),
		$subcats_menu		= $el.find( '.subcats .orbit-dropdown-menu' );

	function updateSubDropdown(){
		var currentCategoryValue = $cats_dropdown.val();

		if( currentCategoryValue > 0 ){
			$subcats_menu.find('li.checkbox').hide();
			$subcats_menu.find('li.checkbox[data-parent~="' + currentCategoryValue + '"]').show();
			$subcats.show();
		}
		else{
			$subcats.hide();
			//$subcats_menu.find('li.checkbox').show();
		}
	}

	$cats_dropdown.change( function( ev ){
		updateSubDropdown();
	});

	/*
	* CHANGE THE NAME OF THE PARENT DROPDOWN IF THE CHILD HAS BEEN SELECTED
	* SO THAT THE QUERY IS DONE SOLELY BASED ON THE CHILD
	*/
	$form.submit( function( ev ){
		var currentCategoryValue = $cats_dropdown.val();

		// CONDITION FOR CHECKING IF THE CHILD IS SELECTED
		if( $subcats_menu.find('li.checkbox[data-parent~="' + currentCategoryValue + '"] input[type=checkbox]:checked').length ){
			var taxonomy = $cats_dropdown.attr( 'name' ).replace( 'tax_', '' ).replace( '[]', '' ),
				fieldName	 = 'parent_' + taxonomy;
			$cats_dropdown.attr( 'name', fieldName );
		}
	} );

	updateSubDropdown();

});



	jQuery('[data-behaviour~=typeahead]').each(function(){

		var el = jQuery(this);

		el.typeahead({
			name: el.attr('name'),
			minLength:0,
			local: JSON.parse(el.attr('data-arr'))
		});

	});

	jQuery('[data-behaviour~=bt-dropdown-checkboxes]').orbit_dropdown_checkboxes();




jQuery.fn.ajax_form_submit = function(options){
  var options = $.extend({
    success : function(data){},
  }, options);
  return this.each(function(){
    var form = $(this),
    	url = form.attr('action'),
			method = form.attr('method');

		$.ajax({'type':method,'url':url,'data':form.serialize(),'success':function(data){
      form.trigger('ajax_form:after', [form]);
      options.success(data);
    }});
  });
};

jQuery( '[data-behaviour="orbit-search"]' ).each( function(){

	var $el 				= jQuery( this ),
		$search_form 	= $el.find('.orbit-search-form'),
		$form 				= $el.find( 'form' ),
		$results 			= $el.find( '.orbit-search-results' );

	function removeURLParameter(url, parameter) {
		//prefer to use l.search if you have a location/link object
		var urlparts = url.split('?');
		if (urlparts.length >= 2) {
			var prefix = encodeURIComponent(parameter) + '=';
			var pars = urlparts[1].split(/[&;]/g);

			//reverse iteration as may be destructive
			for (var i = pars.length; i-- > 0;) {
				//idiom for string.startsWith
				if (pars[i].lastIndexOf(prefix, 0) !== -1) {
					pars.splice(i, 1);
				}
			}

			return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
		}
		return url;
	}

	function hasUrlParameters( url ){
		var urlparts = url.split('?');
		if( urlparts.length >= 2 ) return true;
		return false;
	}

	// ON THE CHANGE OF DROPDOWN VALUE OF SORTING, REDIRECT THE RESULTS
	$el.find('[data-behaviour~=orbit-sorting] select').change( function(){

		var $select = jQuery( this ),
			name_param = $select.attr( 'name' );

		var urlAfterRemoval = removeURLParameter( location.href, name_param );

		var query_param = hasUrlParameters( urlAfterRemoval ) ? "&" : "?";

		var url =  urlAfterRemoval + query_param + name_param + "=" + $select.val();

		// redirect to new url
		location.href = url;
	} );

	if( $el.hasClass('default-theme') ){

		// TO ONLY WORK FOR DEFAULT THEME
		function makeDefaultFormCollapsible(){
			var window_width			= jQuery( window ).width();
				$search_form_arrow 	= $search_form.find('.orbit-search-form-title .arrow-down');

			// hide the form on mobile and make it collapsible
			if( window_width < 768 ){
				// hide the form
				$form.hide();

				// trigger collapsible
				$search_form_arrow.click( function(){ $form.toggle('slide'); });
			}
		}

		makeDefaultFormCollapsible();
	}


	if( $el.hasClass( 'grid-theme' ) ){

		$el.find( 'a[href].orbit-btn-close' ).click( function( ev ){
			ev.preventDefault();
			$el.find( '.orbit-search-grid' ).removeClass('filters-visible');
		} );

		$el.find( 'a[href].orbit-open-filters' ).click( function( ev ){
			ev.preventDefault();
			$el.find( '.orbit-search-grid' ).addClass('filters-visible');
		} );

		var top = $el.find('.orbit-search-grid').offset().top - 120;

		$(window).scroll(function(e){

			var $target = $el.find('.orbit-search-grid');

			if( $(this).scrollTop() > top ){
		    $target.addClass( 'is-fixed' );
		  }
		  if( $(this).scrollTop() < top ){
		    $target.removeClass( 'is-fixed' );
		  }

		});

		var window_width = jQuery( window ).width();
		if( window_width > 960 ){
			$el.find('.orbit-search-grid').addClass( 'filters-visible' );
		}

	}





});
