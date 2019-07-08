$(document).ready(function(){

  jQuery('[data-behaviour="multirange"]').each(function(){

    var $range = jQuery(this),
      $input    = $range.find( 'input[type=range]' ),
      $minLabel = jQuery(document.createElement( 'div' ) ),
      $maxLabel = jQuery(document.createElement( 'div' ) ),
      $checkboxes = jQuery( '.multirange-checkboxes input[type=checkbox]' );




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

      minValue = parseInt( ( minValue * steps ) / 100 ) + parseInt( ranges[0] );
      maxValue = parseInt( ( maxValue * steps ) / 100 ) + parseInt( ranges[1] ) - parseInt( steps );

      $minLabel.html( minValue );
      $maxLabel.html( maxValue );

      updateCheckboxes( minValue, maxValue );
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

    createLabels();
    updateLabels();

    $input.on( 'range:updated', function(){

      var update = debounce(function() {
        updateLabels();
      }, 250);

      update();

    } );




  });
});
