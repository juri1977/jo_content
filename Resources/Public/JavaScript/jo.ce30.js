$(function() {
	var $ajaxloader = $('#joAjaxloader');

	$('.eventSlider').slick({
		infinite: true,
		swipeToSlide: true,
		zIndex: 1000,
		slidesToShow: 3,
		slidesToScroll: 1,
        centerPadding: '20px',
		autoplay: false,
		autoplaySpeed: 5000,
		responsive: 
		[{
			breakpoint: 992,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1
			}
		},
		{
			breakpoint: 768,
			settings: {
			slidesToShow: 1,
			slidesToScroll: 1
			}
		}]
	});

	$('.eventOuterwrap').on('click', '.prev a, .next a, .joEventsMonate a', function(e) {
		e.preventDefault();
		var $that = $(this);
		var $con = $that.closest('.eventOuterwrap');
		var href = $that.attr('href');

		if ($con.length && typeof href != 'undefined' && href != '') {
			$ajaxloader.show();
			$.get(href).done((data) => {
				$ajaxloader.hide();
				$(data).hide();
				$con.fadeOut('400', () => {
					$con.html(data).promise().done(() => {
						$slider = $con.find('.eventSlider');
						if ($slider.length) {
							setTimeout(() => {
								$slider.slick({
									infinite: true,
									swipeToSlide: true,
									zIndex: 1000,
									slidesToShow: 3,
									slidesToScroll: 1,
							        centerPadding: '20px',
									/*autoplay: true,*/
									autoplaySpeed: 5000,
							        adaptiveHeight: true,
									responsive: 
									[{
										breakpoint: 1080,
										settings: {
										slidesToShow: 2,
										slidesToScroll: 1
										}
									},
									{
										breakpoint: 768,
										settings: {
										slidesToShow: 1,
										slidesToScroll: 1
										}
									}]
								});
							}, 300);
						}
						$con.fadeIn();
						if ($con.find('#ol4-mapdiv').length) initMap();
					});
				})
			});
		}
	});

	// Fold and unfold the accordion
	$('.frame-layout-4 header').click(function() {
		var $that = $(this);
		$('.frame-layout-4 header.active h2').removeClass('minus');
		$('.frame-layout-4 header.active').not($that).removeClass('active').next().slideToggle(250).promise().done(() => {
			$that.toggleClass('active').next().slideToggle(250);
			$('.frame-layout-4 header.active h2').addClass('minus');
		});
	});
	// Move joNewsBackLink out of his containing div
	// $(".joNewsBackLink").appendTo(".joNewsPlugin");
	// $(".joNewsBackLink").appendTo("#c836");
	$(".joNewsBackLink").appendTo(".joNewsDetail");
});