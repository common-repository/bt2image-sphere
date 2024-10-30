jQuery(function($) {
	$(document).ready(function() {
		$('.spherecontainer').each( function(){
			new Photosphere($(this).data('spheresource')).loadPhotosphere(this);
		});
	});
});
