$(function() {
	var mobile = ($('#mobile-hidden').is(':visible')) ? false : true;

	if (!mobile) {
		if ($('.c33-overlay').length) {
			var i = 1;
			$('.c33-overlay svg polygon').each(function() {
				var height = this.getBoundingClientRect().width + 200;

				if (i == 1) {
					$(this).css('transform', 'translate(' + height + 'px, ' + height + 'px)');
					i++;
				} else if (i == 2) {
					$(this).css('transform', 'translate(-' + height + 'px, -' + height + 'px)');
					i--;
				}
			}).promise().done(function() {
				setTimeout(function() {
					$('.c33-overlay').addClass('active').promise().done(function() {
						$('.c33-gallery').addClass('show');
						setTimeout(function() {
							/*
							$('.c33-overlay').addClass('hidden');
							*/
							$('.c33-overlay').fadeOut(2000, function() {
								$('.c33-gallery').addClass('ready');
							});
						}, 1000);
					});
				}, 400);
			});
		}
	}

});
