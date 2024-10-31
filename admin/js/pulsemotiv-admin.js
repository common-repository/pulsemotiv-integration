(function( pulse4wp ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	pulse4wp(function () {
		pulse4wp('button.copyClick').on('click', function () {
			debugger;
			var buttonText = pulse4wp(this).text();
			var copiedText = 'Copied!';
			var main = pulse4wp(this).closest('tr');
			var textElement = pulse4wp(main).find("span.pulseCode");
			var tempInput = document.createElement('input');
			console.log(pulse4wp(textElement).text());
			tempInput.value = pulse4wp(textElement).text();
			document.body.appendChild(tempInput);
			tempInput.select();
			document.execCommand("copy");
			pulse4wp(this).text(copiedText);
			setTimeout(function () {
				pulse4wp('button.copyClick').text(buttonText);
			}, 3000);
			tempInput.remove();
		});
	});

})( jQuery );
