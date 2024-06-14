$(function() {
	var mobile = ($('#mobile-hidden').is(':visible')) ? false : true;

	var $img = $('.c34-frame-item img');

	if ($img.length) {
		$img.click(function() {
			var $that = $(this);
			var $parent = $that.parent();
			var div = $parent.data('iframe');
			div = div.trim();

			div = div.substring(1);
			div = div.substring(0, div.length - 1);

			div = div.replaceAll('\\/', '/');
			div = div.replaceAll('\\"', '"');

			$that.parent().html(div);

			/*
			var $iframe = $parent.find('iframe');

			if ($iframe.length) {
				console.log($iframe.attr('src'));
				$iframe.attr('src', $iframe.attr('src'));
				console.log($iframe.attr('src'));
			}
			*/

			// $img.replaceWith($(div));
		});
	}
});
