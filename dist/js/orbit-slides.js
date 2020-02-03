(function ($) {

	$.fn.orbit_slides = function(){

		return this.each(function() {

			var $el 		= jQuery( this );

      // GET THE TOTAL NUMBER OF SLIDES
			function totalSlides(){ return parseInt( $el.find('.orbit-slide').length ); }

			// GET CURRENT SLIDE IN THE LIST OF SLIDES THAT IS ACTIVE
			function getCurrentSlide(){ return $el.find('.orbit-slide.active'); }

			// GET CURRENT SLIDE VALUE
			function getCurrentSlideValue(){ var $currentSlide = getCurrentSlide(); return $currentSlide.data('slide') + 1; }

			// FIND THE NEXT SLIDE TO THE ONE THAT IS CURRENTLY ACTIVE
			function getNextSlide(){
				var $currentSlide 		= getCurrentSlide(),
					currentSlideNumber 	= parseInt( $currentSlide.data('slide') ),
					nextSlideNumber 	= currentSlideNumber + 1;

				if( nextSlideNumber >= totalSlides() ){ nextSlideNumber = 0; }
				return $el.find( '[data-slide~=' + nextSlideNumber + ']' );
			}

			// FIND THE PREVIOUS SLIDE TO THE ONE THAT IS CURRENTLY ACTIVE
			function getPreviousSlide(){
				var $currentSlide 		= getCurrentSlide(),
					currentSlideNumber 	= parseInt( $currentSlide.data('slide') ),
					prevSlideNumber 	= currentSlideNumber - 1;

				if( prevSlideNumber < 0 ){ prevSlideNumber = totalSlides() - 1; }
				return $el.find( '[data-slide~=' + prevSlideNumber + ']' );
			}

			function createProgressBar(){
				var $progress = $el.find('.orbit-form-progress');

				// START CREATING ELEMENTS WITHIN THE PROGRESS ELEMENT
				var $progressText = jQuery( document.createElement( 'h5' ) );
				$progressText.appendTo( $progress );

	      var $progressBar = jQuery( document.createElement( 'div' ) );
	      $progressBar.addClass( 'orbit-progress-bar' );
	      $progressBar.appendTo( $progress );

	      var $bar = jQuery( document.createElement( 'div' ) );
	      $bar.addClass( 'bar' );
	      $bar.appendTo( $progressBar );
			}

			/*
			* SHOW THE OVERALL PROGRESS
			* UPDATES WHENEVER THE NAVIGATION BUTTONS OF THE FORM ARE CLICKED
			*/
			function updateProgress(){
				var $progress 		= $el.find('.orbit-form-progress'),
					$progressText 	= $progress.find( "h5" ),
	        $bar            = $progress.find( ".bar" );
	        totalSlidesNum  = totalSlides(),
	        currentSlideNum = getCurrentSlideValue(),
	        progress        = currentSlideNum * 100 / totalSlidesNum;

				// UPDATE PROGRESS TEXT
	      $progressText.html( "Step " + currentSlideNum + " of " + totalSlidesNum );

				// UPDATE PROGRESS BAR - ANIMATION
	      $bar.css({ width: progress + "%" });
			}



			/*
			*	TRANSITION OF SLIDE FROM CURRENT TO NEXT
			* 	SCROLL THE BODY TO THE TOP OF THE SLIDE
			*/
			function slideTransition( $currentSlide, $nextSlide ){

				$currentSlide.removeClass('active');
				$nextSlide.addClass('active');

				$([document.documentElement, document.body]).animate({
					scrollTop: $el.offset().top - 100
				}, 1000);

				$nextSlide.trigger('orbit:afterTransition');

				updateProgress();

			}

			/*
			* EVENT TRIGGERED WHEN THE NEXT NAVIGATION BUTTON IS CLICKED
			*/
			$el.find('[data-behaviour~=orbit-slide-next]').click( function( ev ){

				ev.preventDefault();

				var $slide 		= getCurrentSlide(),
					$nextSlide	= getNextSlide();

				$slide.trigger('orbit:beforeNextTransition');

				// BEFORE GOING TO THE NEXT SLIDE CHECK IF THE NEXT SLIDE CAN BE ACCESSIBLE
				// EVENTS SUCH AS VALIDATION CAN BE HANDLED IN THIS PERIOD
				if( $slide.data( 'slide-disable' ) != '1' ){ slideTransition( $slide, $nextSlide ); }

			});

			/*
			* EVENT TRIGGERED WHEN THE PREVIOUS NAVIGATION BUTTON IS CLICKED
			*/
			$el.find('[data-behaviour~=orbit-slide-prev]').click( function( ev ){

				ev.preventDefault();

				var $slide 		= getCurrentSlide(),
					$prevSlide	= getPreviousSlide();

				slideTransition( $slide, $prevSlide );

			});

			// SHOW ERROR MESSAGE
			function errorMessage( message ){
				$el.find('.orbit-form-alert').html( message );
		    $el.find('.orbit-form-alert').show();
		  }

			// VALIDATE PARTIAL FORM WITHIN THE ACTIVE SLIDE
			function validatePartialForm( $slide ){

				var flag 	    = true,
		      errorText   = "Required fields should be filled.",
					fields 	    = $slide.find(".orbit-field-required:not(.hide) input, .orbit-field-required:not(.hide) select, .orbit-field-required:not(.hide) textarea").serializeArray();

				// SINGULAR FIELDS
				$.each( fields, function( i, field ){
					if( !field.value || field.value == "0" ){
		        errorMessage( errorText );
						flag = false;
		      }
		    });

				// SEPERATE CASE FOR CHECKBOXES
		    $slide.find( '.orbit-field-required input[type=checkbox]' ).each( function( i, el ){
		      var $el       = jQuery( el ),
		        $parent     = $el.closest('.orbit-field-required'),
		        num_checked = $parent.find('input[type="checkbox"]:checked').length;

		      if( num_checked <= 0 ){
		        errorMessage( errorText );
		        flag = false;
		      }

		    });

		    return flag;
			}

			// PARTIAL FORM VALIDATION EACH TIME THE NEXT BUTTON IS CLICKED
		  $el.on('orbit:beforeNextTransition', function( ev ){

		    $el.find('.orbit-form-alert').hide();

		    var $slide = getCurrentSlide(),
							flag = validatePartialForm( $slide );

		    $slide.data('slide-disable', '1');

				if( flag ){ $slide.data('slide-disable', '0'); }

		  });

			// FORM SUBMISSION
			$el.closest( 'form' ).submit( function(event){

		    $el.find('.orbit-form-alert').hide();

		    var $slide = getCurrentSlide(),
							flag = validatePartialForm( $slide );

				if( !flag ){  event.preventDefault(); }

			} );

			// INITIALIZE
			function init(){

				// ASSIGN SLIDE ID TO EACH SLIDE
				$el.find('.orbit-slide').each( function( i, slide ){
          jQuery( slide ).attr( 'data-slide', i );
          if( i == 0 ){ jQuery( slide ).addClass('active'); }
        });

				// CREATE PROGRESS BAR FOR THE MULTIPART FORM
				createProgressBar();
				updateProgress();

				// HIDE FORM ALERT ON LOAD
				$el.find('.orbit-form-alert').hide();
			}

			init();


		});

	};

}(jQuery));

jQuery('document').ready(function(){
	jQuery( '[data-behaviour~=orbit-slides]' ).orbit_slides();
});
