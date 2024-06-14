var inJoMain = inJoMain ? inJoMain : false;
$(() => {
	if (!inJoMain) initJoMain();

	function initJoMain() {
	    $('body').click('.aw .clicable .inView', (e) => {
	    	container1 = $('.container-1-lmb.slide');
	    	container2 = $('.container-2-lmb.slide');
	    	container3 = $('.container-3-lmb.slide');
	    	container4 = $('.container-4-lmb.slide');

	    	item = $(e.target);
	    	console.log(item.parent().parent());

	    	if (item.parent().hasClass('third') == true ) {
		    	if (item.hasClass('slide_item') == true ) {
		    		if (item.hasClass('clicked') == true ) {
						item.removeClass('clicked');
		    		} else {
		    			item.addClass('clicked');
		    		}	    	
			    	var time = 50;
			    	setTimeout(() => {
			    	container4.addClass('active');

			    	$('.container-4-lmb.slide .aw').each((i, v) => {
					setTimeout(() => {
						$(v).addClass('inView');
					}, time);

					time += 100;
					});
			    }, 250);}
	    	}
	    	if (item.parent().hasClass('second') == true ) {
		    	if (item.hasClass('slide_item') == true ) {
		    		if (item.hasClass('clicked') == true ) {
						item.removeClass('clicked');
		    		} else {
		    			item.addClass('clicked');
		    		}	    	    	
			    	container1.removeClass('col-md-6');
			    	container1.addClass('col-md-3');
			    	container2.removeClass('col-md-6');
			    	container2.addClass('col-md-3');
			    	var time = 50;

			    	setTimeout(() => {
						container3.addClass('active');
						$('.container-3-lmb.slide .aw').each((i, v) => {
						setTimeout(() => {
							$(v).addClass('inView');
						}, time);
						time += 100;
						});
					}, 350);
				}
	    	}
	    	if (item.parent().hasClass('first') == true ) {
		    	if (item.hasClass('slide_item') == true ) {
		    		if (item.hasClass('clicked') == true ) {
						item.removeClass('clicked');
		    		} else {
		    			item.addClass('clicked');
		    		}
			    	container1.removeClass('center');
			    	var time = 50;
			    	setTimeout(() => {
						container2.addClass('active');
						$('.container-2-lmb.slide .aw').each((i, v) => {
						setTimeout(() => {
							$(v).addClass('inView');
						}, time);

						time += 100;
						});
					}, 350);
				}
	    	}

	    });

		if ($('.jo-form').length) $('.jo-form')[0].reset();

		var user = '';
		var nav = $('.header-container');
		var lastScr = 0, d = 0;
		var dtPos = 0;

		$('.container-lmb, .joMGDetail').scroll(function(event) {
			var that = $(this);
			var scr = that.scrollTop();
			var navOuterHight = nav.outerHeight();
			var iconbar = $('.iconbar');
			if (Math.abs(lastScr - scr) > d) {
				if (scr > lastScr && scr > navOuterHight) {
					// Scroll Down
					//nav.css('top', '-' + navOuterHight + 'px');
					nav.addClass('hide');
					if (iconbar.hasClass('inView') || that.hasClass('joMGDetail')) {
						iconbar.addClass('hide');
						$('.next-prev-container.iconbar-active').removeClass('iconbar-active');
					}
				} else {
					// Scroll Up
					//if(scr + $('.container-lmb').height() < $(document).height()) {
						//nav.css('top', 0);
						nav.removeClass('hide');
						if (iconbar.hasClass('inView') || that.hasClass('joMGDetail')) {
							$('.next-prev-container').addClass('iconbar-active');
							iconbar.removeClass('hide');
						}
					//}
				}
			}
			lastScr = scr;
		});

		$('#person .next-container').click(function() {
			user = $('#person #person-1').val();
			$('.question-heading').each(function(i, v) {
				var label = $(this).data('label');
				var label_usr = $(this).data('labeluser');
				if (typeof user !== 'undefined' && user != '' && typeof label_usr !== 'undefined' && label_usr != '') {
					$(this).html(label_usr.replace('{user}', user));
				} else {
					$(this).html(label);
				}
			});
		});

		var ajaxloader = $('#joAjaxloader');
		var backgroundImg = $('.background-lmb');
		var startSymbol = $('.start_symbol');
		var startSymbolWeiss = $('.start_symbol_weiss');
		var containerBg = $('.container-start-lmb');
		//var container1 = $('.container-1-lmb.slide');
		var container1 = $('.container-lmb.slide').first();
		var container2 = $('.container-2-lmb.slide');
		var container3 = $('.container-third-lmb.slide');
		var container4 = $('.container-fourth-lmb.slide');
		var container5 = $('.container-fifth-lmb.slide');
		var container6 = $('.container-preview.slide');
		var nextPrevCon = $('.next-prev-container');

		
		containerBg.addClass('blur');
		container1.addClass('active');
	

		var time = 50;
		$('.container-1-lmb.slide .first.aw').each((i, v) => {
			setTimeout(() => {
				$(v).addClass('inView');
			}, time);
			time += 100;
		});

		$('.next-prev-container .back-label').click(function() {
			if (!$(this).parent().hasClass('notclickable')) {
				$('.container-lmb.active .back-container').trigger('click');
				if ($('.container-lmb.active .next-container').length) {
					$('.next-prev-container').addClass('next-active');
				} else {
					$('.next-prev-container').removeClass('next-active');
				}
				$('.iconbar.hide').removeClass('hide');
				$('.header-container.hide').removeClass('hide');
			}
		});

		var containerLMB = $('.container-lmb:not(.jomultiple)');
		containerLMB.each(function(i, v) {
			var that = $(this);
			var clicable = that.find('.clicable');
			var curr_num = that.data('number');
			var next_num = parseInt(curr_num)+1;
			
			if (clicable.length && clicable.find('.text-input').length == 0) {
				clicable.off('click');
				clicable.on('click', function() {
					if (i == 0 && !nextPrevCon.hasClass('active')) {
						nextPrevCon.addClass('active back-active');
					}
					var target = $(this).data('jotarget');
					if (typeof target !== 'undefined' && target != '') {
						if (target == '#finish') {
							$(this).closest('.jo-form').submit();
							return 0;
						}
						if ($(target).length) next_num = $(target).data('number');
					}
					$('.container-' + curr_num + '-lmb.slide.active').addClass('slideout fadeout');
					$('.container-' + curr_num + '-lmb.slide').removeClass('active');
					$('.container-' + next_num + '-lmb').addClass('active');
					time = 100;
					$('.container-' + next_num + '-lmb.slide .aw').each((i, v) => {
						setTimeout(() => {
							$(v).addClass('inView');
						}, time);
						time += 100;
					});

					if ($('.container-' + next_num + '-lmb').hasClass('hasIconbar')) {
						//setTimeout(function(){
							$('.iconbar').addClass('inView');
						//}, time);
						$('.next-prev-container.back-active').addClass('iconbar-active');
					} else {
						$('.iconbar').removeClass('inView');
						$('.next-prev-container.back-active').removeClass('iconbar-active');
					}

					if (that.attr('id') == 'experience') {
						var selected = that.find('input:checked');
						if (selected.length) {
							$('.iconbar .' + selected.val()).addClass('isselected');
							$('.topic .topic-text').text(selected.parent().find('label').text());
						}
					}

					if (that.attr('id') == 'thema') {
						var selected = that.find('input:checked');
						if (selected.length) load_thema(selected);
					}

					if (i === (containerLMB.length - 1)) {
						container6.addClass('active');
						setTimeout(() => {
							$('.start').addClass('inViewTop');
						}, 100);
						setTimeout(() => {
							$('.topic').addClass('inView');
						}, 200);
		          	}
					if ($('.container-' + next_num + '-lmb').find('.next-container.next-active').length) {
						$('.next-prev-container').addClass('next-active');
					} else {
						$('.next-prev-container').removeClass('next-active');
					}
				});
			}
		});

		function load_thema(selected) {
			var thema_select = $('#thema_select');
			if (thema_select.length) {
				thema_select.find('.thema-block:not(.d-none)').remove();
				var thema_container = thema_select.find('.thema-container .thema-content');
				var thema_block = thema_select.find('.thema-block.d-none');
				var href = themaEid.attr('href');

				themaList = '';
				selected.each((i, val) => {
					if (i != 0) themaList += ',';
					themaList += $(val).val();
				});

				var checked = $('.jo-form').find('input:checked');

				var arr = {};

				//var pattern = new RegExp(/\[(.*?)\]/);
				checked.each((i, v) => {
					var el = $(v);
					//console.log(pattern.test(el.attr('name')));
					//console.log(pattern.exec(el.attr('name')));
					//arr[pattern.test(el.attr('name'))] = el.val();
					arr[el.attr('name')] = el.val();
				});

				arr['action'] = 'load';
				arr['themaList'] = themaList;

				$.get(href, arr).done((data) => {
					if (data == 'empty') {

					} else {
						var json = JSON.parse(data);
						$.each(json, (i, val) => {
							var clone = thema_block.clone();
							//clone.find('.thema.thema-item').css('background-image', 'url("' + val.img + '")').html(val.title);
							clone.find('.thema.thema-item').css('background-image', 'url("' + val.img + '")').data('type', i);
							clone.find('.thema-item-title').html(val.title);
							var clone2 = clone.find('.thema.obj-item').remove();
							$.each(val.objects, (i2, val2) => {
								var clone3 = clone2.clone();
								//clone3.find('.title').css('background-image', 'url("' + val2.img + '")').html(val2.title);
								clone3.find('.title').css('background-image', 'url("' + val2.img + '")');
								clone3.find('.obj-btn.kick').attr('data-id', val2.id);
								clone3.find('.obj-item-title').html(val2.id);
								clone3.find('.obj-btn.load').attr('data-desc', val2.desc).attr('data-title', val2.title).attr('data-objnr', val2.obj_nr).attr('data-time', val2.time).attr('data-id', val2.id);
								clone3.find('input').val(val2.id);
								clone.append(clone3);
							});
							clone.removeClass('d-none');
							thema_container.append(clone);
						});
					}
				}).fail((error) => {
					console.log('failed');
				    console.log(error);
				    working = false;
				});
			}
		}

		var containerLMB2 = $('.container-lmb.jomultiple');
		containerLMB2.each(function(i, v) {
			var clicable = $(this).find('.clicable');
			if (clicable.length) {
				clicable.off('click');
				clicable.on('click', function() {
					if ($(this).parent().find('input:checked').length) {
						//$(this).parent().find('.next-container').show();
						$('.next-prev-container').addClass('next-active');
					} else {
						//$(this).parent().find('.next-container').hide();
						$('.next-prev-container').removeClass('next-active');
					}
				});
			}
		});

		$('.next-prev-container .next-label').click(function() {
			if (!$(this).parent().hasClass('notclickable')) {
				$('.container-lmb.active .next-container').trigger('click');
				if ($('.container-lmb.active').find('.next-container.next-active').length) {
					$('.next-prev-container').addClass('next-active');
				} else {
					$('.next-prev-container').removeClass('next-active');	
				}
				$('.iconbar.hide').removeClass('hide');
				$('.header-container.hide').removeClass('hide');
				//if(!$('.container-lmb.active').find('.thema-container').length) {
				//}
			}
		});

		$('.container-lmb .next-container').click(function() {
			if ($(this).closest('.container-lmb').attr('id') == 'thema_select') {
				$(this).closest('form').submit();
			} else {
				var curr_num = $(this).closest('.container-lmb').data('number');
				var next_num = parseInt(curr_num)+1;

				$('.container-' + curr_num + '-lmb.slide.active').addClass('slideout fadeout');
				$('.container-' + curr_num + '-lmb.slide').removeClass('active');
				$('.container-' + next_num + '-lmb').addClass('active');
				time = 100;
				$('.container-' + next_num + '-lmb.slide .aw').each((i, v) => {
					setTimeout(() => {
						$(v).addClass('inView');
					}, time);
					time += 100;
				});

				/* if($('.container-' + next_num + '-lmb .iconbar').length) {
					setTimeout(function(){
						$('.container-' + next_num + '-lmb .iconbar').addClass('inView');
					}, time);
				} */

				if ($('.container-' + next_num + '-lmb').hasClass('hasIconbar')) {
					//setTimeout(function(){
						$('.iconbar').addClass('inView');
					//}, time);
					$('.next-prev-container.back-active').addClass('iconbar-active');
				} else {
					$('.iconbar').removeClass('inView');
					$('.next-prev-container.back-active').removeClass('iconbar-active');
				}

				if ($(this).closest('.container-lmb').attr('id') == 'thema') {
					var selected = $(this).closest('.container-lmb').find('input:checked');
					if (selected.length) load_thema(selected);
				}
			}
		});

		$('.back-container').click(function() {
			var next_num = 0;
			var target = $(this).data('jotarget');
			if (typeof target !== 'undefined' && target != '') {
				var el = $(target);
				if (el.length) {
					$(this).closest('.container-lmb').addClass('slideout fadeout').removeClass('active');
					el.removeClass('slideout fadeout').addClass('active').nextAll().removeClass('slideout fadeout').find('input:checked').prop('checked', false);
					time = 100;
					el.find('.aw').each((i, v) => {
						setTimeout(() => {
							$(v).addClass('inView');
						}, time);
						time += 100;
					});

					/* if(el.find('.iconbar').length) {
						setTimeout(function(){
							el.find('.iconbar').addClass('inView');
						}, time);
					} */

					if (el.hasClass('hasIconbar')) {
						$('.iconbar').addClass('inView');
						$('.next-prev-container.back-active').addClass('iconbar-active');
					} else {
						$('.iconbar').removeClass('inView');
						$('.next-prev-container.back-active').removeClass('iconbar-active');
					}

					if (el.hasClass('hasBack')) {
						$('.next-prev-container').addClass('active');
					} else {
						$('.next-prev-container').removeClass('active');
					}
				}
			}
		});
		
		$('.container-lmb').on('click', '.thema-container:not(.showInfo) .thema.obj-item', function() {
			//$(this).find('.kick-load-container').toggleClass('active');
			$(this).toggleClass('active');
		});

		$('.container-lmb').on('click', '.thema-container.showInfo .thema.obj-item', () => {
			$('.container-lmb').find('.thema-info-back .back-btn').trigger('click');
		});

		$('.container-lmb').on('swiperight', function() {
			var back = $(this).find('.back-container');
			var thema = $(this).find('.thema-container');
			if (back.length && !thema.hasClass('showInfo')) {
				back.trigger('click');
				if ($('.container-lmb.active .next-container').length) {
					$('.next-prev-container').addClass('next-active');
				} else {
					$('.next-prev-container').removeClass('next-active');
				}
			}
		});

		$('.container-lmb').on('click', '.thema-container:not(.showInfo) .load', function() {
			$(this).closest('.container-lmb').addClass('showingInfo');
			var info_content = $(this).closest('.thema-container').addClass('showInfo').find('.thema-info-content');
			info_content.find('.thema-info-img').css('background-image', $(this).closest('.thema.obj-item').find('.title.active').css('background-image'));
			info_content.find('.thema-info-title').html($(this).data('title'));
			info_content.find('.thema-info-text').html($(this).data('desc'));
			info_content.find('.thema-info-objnr').html('<span class="thema-info-objnr-btn"><span class="objnr-icon"></span>' + $(this).data('id') + '</span>');
			info_content.find('.thema-info-time').html('Dauer: ' + $(this).data('time'));
			$(this).addClass('active');
			lastClicked = $(this);
			$('.next-prev-container').addClass('notclickable');
		});

		$('.thema-info-kick').click(function() {
			$(this).prev('.thema-info-back').trigger('click');
			lastClicked.next('.kick').trigger('click');
		});

		$('.container-lmb').on('click', '.thema-container.showInfo .thema-info-back .back-btn', function() {
			$(this).closest('.container-lmb').removeClass('showingInfo');
			var info_content = $(this).closest('.thema-container').removeClass('showInfo').find('.thema-info-content');
			//info_content.find('.thema-info-title').html('');
			//info_content.find('.thema-info-text').html('');
			$('.next-prev-container').removeClass('notclickable');
			info_content.find('.load.active').addClass('active');
		});
	}
});
