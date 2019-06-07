(function ($) {

	$.fn.orbit_slides = function(){

		return this.each(function() {

			var $el 		= jQuery( this );

      // GET THE TOTAL NUMBER OF SLIDES
			function totalSlides(){
				return parseInt( $el.find('.orbit-slide').length );
			}
      
			// GET CURRENT SLIDE IN THE LIST OF SLIDES THAT IS ACTIVE
			function getCurrentSlide(){
				return $el.find('.orbit-slide.active');
			}

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

			// INITIALIZE
			function init(){

        // ASSIGN SLIDE ID TO EACH SLIDE
				$el.find('.orbit-slide').each( function( i, slide ){
          jQuery( slide ).attr( 'data-slide', i );
          if( i == 0 ){ jQuery( slide ).addClass('active'); }
        });
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

			}

			$el.find('[data-behaviour~=orbit-slide-next]').click( function( ev ){

				ev.preventDefault();

				var $slide 		= getCurrentSlide(),
					$nextSlide	= getNextSlide();

				$slide.trigger('orbit:beforeNextTransition');

				if( $slide.data( 'slide-disable' ) != '1' ){
					slideTransition( $slide, $nextSlide );
				}

			});

			$el.find('[data-behaviour~=orbit-slide-prev]').click( function( ev ){

				ev.preventDefault();

				var $slide 		= getCurrentSlide(),
					$prevSlide	= getPreviousSlide();

				slideTransition( $slide, $prevSlide );

			});

			init();


		});

	};








}(jQuery));

jQuery( '[data-behaviour~=orbit-slides]' ).orbit_slides();
