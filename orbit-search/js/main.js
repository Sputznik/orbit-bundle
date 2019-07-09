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

	jQuery('[data-behaviour~=bt-dropdown-checkboxes]').orbit_dropdown_checkboxes();

	// jQuery( '[data-behaviour="orbit-search"] form' ).submit(function( event ){
	// 	event.preventDefault();
	// });

	$( '[data-behaviour="orbit-search"] form' ).on( "submit", function( event ) {
  event.preventDefault();
  console.log( $( ' [data-behaviour="orbit-search"] form ' ).serialize() );
});

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

	var $el = jQuery( this ),
		$form = $el.find( 'form' );
		$results = $el.find( '.orbit-search-results' );

	$form.submit(function(event){
		event.preventDefault();

		$el.css({opacity:0.5});

		$form.ajax_form_submit({
			'success':function(data){

				$el.css({ opacity:1 });

				var $response = jQuery( data );

				var html = $response.find('.orbit-search-results').html();

				$results.html( html );

				$("html, body").animate({ scrollTop: 0 }, "slow");
					return false;
			}
		});
	});


});
