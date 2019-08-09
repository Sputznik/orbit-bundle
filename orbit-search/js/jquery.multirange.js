$(document).ready(function(){

  jQuery('[data-behaviour="multirange"]').each(function(){

    var $range = jQuery(this),
      $input    = $range.find( 'input[type=range]' ),
      $minLabel = $range.find( '.min-label' ),
      $maxLabel = $range.find( '.max-label' ),
      $checkboxes = $range.parent().find( '.multirange-checkboxes input[type=checkbox]' );




    function createLabels(){
      $minLabel.prependTo( $range );
      $minLabel.addClass( 'minLabel' );

      $maxLabel.appendTo( $range );
      $maxLabel.addClass( 'maxLabel' );
    }

    function updateCheckboxes( minValue, maxValue ){

      $checkboxes.prop( 'checked', false );

      $checkboxes.each( function(){
        var $checkbox = jQuery( this ),
          value       = $checkbox.val();

        if( value >= minValue && value <=maxValue ){
          $checkbox.prop( 'checked', true );

        }
      });
    }

    function updateLabels(){
      var range   = $range.attr( 'data-range' ),
        ranges    = range.split( ',' );
        steps     = ranges[1] - ranges[0];
        minValue  = $range.attr( 'data-min' ),
        maxValue    = $range.attr( 'data-max' );

      minLabel = parseInt( ( minValue * steps ) / 100 ) + parseInt( ranges[0] );
      maxLabel = parseInt( ( maxValue * steps ) / 100 ) + parseInt( ranges[1] ) - parseInt( steps );

      $minLabel.html( minLabel );
      $maxLabel.html( maxLabel );

      if( ! ( minValue == 0 && maxValue ==  100 ) ){
        updateCheckboxes( minLabel, maxLabel );
      }
      else{
        // RESET THE CHECKBOXES WHEN MINVALUE=0 & MAXVALUE=100
        $checkboxes.prop( 'checked', false );
      }

    }

    function debounce(func, wait, immediate) {
    	var timeout;
    	return function() {
    		var context = this, args = arguments;
    		var later = function() {
    			timeout = null;
    			if (!immediate) func.apply(context, args);
    		};
    		var callNow = immediate && !timeout;
    		clearTimeout(timeout);
    		timeout = setTimeout(later, wait);
    		if (callNow) func.apply(context, args);
    	};
    };

    //createLabels();
    updateLabels();

    $input.on( 'range:updated', function(){

      var update = debounce(function() {
        updateLabels();
      }, 250);

      update();

    } );

    $range.closest( 'form' ).on( 'reset', function(){
			setTimeout( function(){

        // set the ranges to its respective ends
        $range.find( 'input[type=range].multirange.original' ).val( 0 );
        $range.find( 'input[type=range].multirange.ghost' ).val( 100 );

        $range.attr( 'data-min', 0 );
        $range.attr( 'data-max', 100 );


        // updates the blue color from end to end
        $range.find( 'input[type=range].multirange.ghost' ).attr( 'style', '--low:1%; --high:99%;');


			}, 1);

    } );



  });
});
