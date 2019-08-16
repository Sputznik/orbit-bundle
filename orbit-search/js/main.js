/*
* NESTED DROPDOWN
*/
jQuery( '[data-behaviour~="orbit-nested-dropdown"]' ).each( function(){
	
	var $el             = jQuery( this ),
		$cats_dropdown    = $el.find( '.cats select' ),
		$subcats_dropdown = $el.find( '.subcats select' ),
		$cloneSubDropdown = $el.find( '.subcats select' ).clone();  // Clones all subcats from dropdown

	function updateSubDropdown(){

		var currentCategoryValue = $cats_dropdown.val();

		$subcats_dropdown.find( 'option' ).remove();

		var $options;

		if( currentCategoryValue > 0 ){
			$options = $cloneSubDropdown.find( 'option[data-parent~="' + currentCategoryValue + '"]' ).clone();

			var $defaultOption = jQuery( document.createElement( 'option' ) );
			$defaultOption.val( 0 );
			$defaultOption.html('Select');
			$defaultOption.appendTo( $subcats_dropdown );
		}
		else{
			$options = $cloneSubDropdown.find('option').clone();
			$options.first().val(0);
		}

		$options.appendTo( $subcats_dropdown );

		$subcats_dropdown.val(0);

	}

	// change subservices when the main service is changed
	$cats_dropdown.change( function( ev ){

		updateSubDropdown();

	});

});

/*
* MULTIPLE TEXT FIELDS
*/
jQuery('[data-behaviour~=multiple-text]').each( function(){
	var $el = jQuery( this ),
		count = 0,
		field = $el.data('field');

	function init(){

		var $wrapper = jQuery( document.createElement('div') );
		$wrapper.addClass('wrapper');
		$wrapper.appendTo( $el );

		var $wrapperButton = jQuery( document.createElement('div') );
		$wrapperButton.addClass('wrapperButton');
		$wrapperButton.appendTo( $el );

		createAddButton();

		createSingleField();
	}


	function createSingleField(){

		var $parent = jQuery( document.createElement('div') );
		$parent.addClass('multi-field-wrapper');
		$parent.appendTo( $el.find('.wrapper') );

		var $input = jQuery( document.createElement('input') );
		$input.attr( 'type', 'text' );
		$input.attr( 'placeholder', field['label'] ? field['label'] :  "" );
		$input.attr( 'name', field['name'] + '[]' );
		$input.appendTo( $parent );
	};

	function createAddButton(){

		var $btn = jQuery( document.createElement('button') );
		$btn.attr( 'type', 'button' );
		$btn.addClass('add-btn');
		$btn.html( field['btn_text'] ? field['btn_text'] : 'Add Another');
		$btn.appendTo( $el.find('.wrapperButton') );

		$btn.click( function(){
			count++;
			//checks the total number of file fields
			var countImage = jQuery('.multi-field-wrapper').length;
				if( count <= 4 ){
						createSingleField();
				}
		});

	};

	init();

});

/*
* MULTIPLE IMAGE UPLOADS
*/
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

			// EMPTY THE EXISTING IMAGES
			$images_list.html('');

			jQuery.each( input.files, function( j, single_file ){
				var reader = new FileReader();

				reader.onload = function (e) { addImage( e.target.result ); }

				// ONLY ADD IMAGES
				if( single_file.type.indexOf( 'image' ) !== -1 ){ reader.readAsDataURL( single_file ); }
			});
		}
  }

	$el.change( function(){ readURL( this ); });
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




$.fn.ajax_form_submit = function(options){
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
