/*global myLightboxParams */
jQuery( function( $ ) {

	var args = {
		rel:     'gallery',
		current: false
	};

	args = $.extend( args, myLightboxParams.i18n, myLightboxParams.options );

	// Carrega o Colorbox para todos os .jpg, .png e .gifs no corpo dos posts e páginas.
	$( 'a[href$=".jpg"], a[href$=".png"], a[href$=".gif"]', '.hentry' )
		.colorbox( args );
});
