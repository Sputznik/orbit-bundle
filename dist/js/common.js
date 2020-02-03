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
