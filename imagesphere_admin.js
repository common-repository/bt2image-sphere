jQuery(function($) {
	$(document).ready(function() {
			bt2is_resize_img();
			$('#insert-bt2is-button').click(bt2is_open_media_frame);
		});

	function bt2is_resize_img() {
		var img = $('#insert-bt2is-button img');
		var height = $('.wp-media-buttons-icon').height();
		var padding = (img.parent().height() - height) / 2;
		img.height(height).css('padding', padding+'px 0');
	}

	var bt2is_open_media_frame = (function() {
			var media_frame = null;
			return function() {
					if (media_frame === null) {
						var insert = $('#insert-bt2is-button').text();
						media_frame = wp.media({
								title: insert,
								library: {type: 'image'},
								multiple: false,
								button: {text: insert}
							});
						media_frame.on('select', function() {
								var id = media_frame.state().get('selection').first().toJSON().id;
								bt2is_insert_tag(id);
							});
					}
					media_frame.open();
					return false;
				};
		})();

	function bt2is_insert_tag(id) {
		var tag = '[bt2imagesphere id=' + id + ']';
		wp.media.editor.insert(tag);
	}
});
