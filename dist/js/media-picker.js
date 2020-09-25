jQuery.fn.orbit_media_picker = function(){

	return this.each(function(){

		var $el 	= jQuery( this ),
			$input 	= $el.find( 'input[type=text]' );
			$btn 		= $el.find( 'button' );

		$btn.click( function( ev ){
			ev.preventDefault();

			var custom_uploader = wp.media.frames.file_frame = wp.media({
				title: $btn.val(),
				button: {
					text: 'Choose Image'
				},
				multiple: false
			});

			custom_uploader.on('select', function() {
				attachment = custom_uploader.state().get('selection').first().toJSON();

				/* UPDATE THE VALUES OF THE FORM */
				$input.val( attachment.url );

			});

			custom_uploader.open();

		} );


	});
};

jQuery( document ).ready( function(){
	jQuery('[data-behaviour~=orbit-media-picker]').orbit_media_picker();
});
