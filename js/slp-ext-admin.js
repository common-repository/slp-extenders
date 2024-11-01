/*****************************************************************
 * file: slp-ext-admin.js
 *
 *****************************************************************/

jQuery(document).ready(function($) {
    jQuery('#add--start_date--, #add--end_date--, #update--start_date--, #update--end_date--').each(function () {
    	var $element = jQuery(this);
	    slpInitSingleDatepicker( $element ) ;
    });
});

function slpInitSingleDatepicker( $element ) {
	var inputId = $element.attr( 'id' ) ? $element.attr( 'id' ) : '',
		optionsObj = {
			format: 'Y-m-d H:i',
		};
	optionsObj.format = $element.parent().find( '.slp-ext-datetime-format' ).text();

	$element.datetimepicker(optionsObj);

	// We give the input focus after selecting a date which differs from default datetimepicker behavior; this prevents
	// users from clicking on the input again to open the datetimepicker. Let's add a manual click event to handle this.
	if( $element.is( ':input' ) ) {
		$element.click( function() {
			$element.datetimepicker( 'show' );
		} );
	}
}
