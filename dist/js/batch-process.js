jQuery.fn.orbit_batch_process = function(){

	return this.each(function(){

		var $el 			= jQuery(this),
			atts 				= $el.data('atts'),
			batch_step 	= 1;

		function startProgress(){
			ajaxCall();
			$el.find( 'button' ).attr( 'disabled', 'disabled' );

			/* SHOW ELEMENTS */
			$el.find( '.progress-container' ).show();
			$el.find( '.logs-container' ).show();
		};

		function init(){

			// ELEMENT: TITLE
			if( atts.title ){
				var $title = jQuery( document.createElement('div') );
				$title.addClass( 'progress-title' );
				$title.html( atts.title );
				$title.appendTo( $el );
			}

			// ELEMENT: DESCRIPTION
			if( atts.desc ){
				var $desc = jQuery( document.createElement('div') );
				$desc.addClass( 'progress-desc' );
				$desc.html( atts.desc );
				$desc.appendTo( $el );
			}

			var $progress_container = jQuery( document.createElement('div') );
			$progress_container.addClass( 'progress-container' );
			$progress_container.html( '<div class="progress"></div>' );
			$progress_container.appendTo( $el );
			$progress_container.hide();

			if( atts.btn_text ){
				var $btn = jQuery( document.createElement('button') );
				$btn.addClass( 'progress-btn' );
				$btn.html( atts.btn_text );
				$btn.appendTo( $el );

				/* button click */
				$btn.click( function( ev ){
					ev.preventDefault();
					startProgress();
				});



			}

			var $result = jQuery( document.createElement('div') );
			$result.addClass( 'result' );
			$result.appendTo( $el );


			var $logs_container = jQuery( document.createElement('div') );
			$logs_container.addClass( 'logs-container' );
			$logs_container.html( '<h5>Logs</h5><ul class="logs"></ul>' );
			$logs_container.appendTo( $el );
			$logs_container.hide();

			// TRIGGER THE BUTTON
			if( atts.auto ){
				$btn.click();
			}
		}

		/* ADD LOG */
		function addLog( log ){
			var li = jQuery( document.createElement('li') );
			li.html( log );
			li.appendTo( $el.find('.logs') );
		};

		/* CSS PROGRESS */
		function updateProgress(){
			var width = ( ( batch_step-1 ) / atts.batches ) * 100;

			if( width > 100 ){ width = 100;}
			if( width < 0 ){ width = 0; }

			if( width == 100 ){
				$el.trigger("orbit_batch_process:complete");
				$el.find( '.result' ).html( atts['result'] );
			}

			$el.find( '.progress' ).animate({ width: width + '%' });
		};

		function getURLParams( obj ){
			var pairs = [];
    	for ( var prop in obj ) {
        if( !obj.hasOwnProperty( prop ) ) {
          continue;
        }
        pairs.push(prop + '=' + obj[prop]);
    	}
    	return pairs.join('&');
		}

		/* AJAX CALL */
		function ajaxCall(){

			// PREPARE THE DATA THAT NEEDS TO BE PASSES THROUGH THE AJAX CALL
			var data = atts.params;
			data['orbit_batch_action'] 	= atts.batch_action;
			data['orbit_batches']				= atts.batches;
			data['orbit_batch_step']		= batch_step;

			var url = $el.data('url') + '&' + getURLParams( data );

			// UPDATE THE PROGRESS IN THE BUTTON HTML
			$el.find('button').html( atts.btn_text + " " + ( batch_step ) + "/" + atts.batches );

			jQuery.ajax({
				'method'	: 'GET',
				'url'			: url,
				'error'		: function(){ alert( 'Error has occurred' ); },
				'success'	: function( html ){

					batch_step++;			// INCREMENT BATCH STEP

					addLog( html );		// ADD TO THE LOG FROM THE AJAX HTML RESPONSE

					updateProgress();	// UPDATE PROGRESS BAR

					/* CHECK IF BATCH STEP INCREMENT IS ITERATED */
					if( batch_step <= atts.batches ){

						ajaxCall();				// EXECUTE THE NEXT BATCH CALL

					}
				}
			});
		};

		console.log( atts );

		init();

	});
};



jQuery( document ).ready( function(){
	jQuery('[data-behaviour~=orbit-batch]').orbit_batch_process();
});
