$(function() {
	var mobile = ($('#mobile-hidden').is(':visible')) ? false : true;

	var ajaxloader = $('#joAjaxloader');
	/*var kulturContainer = $(".joKultur");
	
	checkTop = function() {
		if($(document).scrollTop() === 0) {
			kulturContainer.addClass('joScrolledTop');
			//$('.ui-widget-content.ui-autocomplete').css('top', parseInt($('.ui-widget-content.ui-autocomplete').css('top')) + 77 + 'px');
			//kulturContainer.find('.content_wrap').css('padding-top', '0');
		} else {
			if(kulturContainer.hasClass('joScrolledTop')) {
				//var h = kulturContainer.find('.joFixed').height();
				kulturContainer.removeClass('joScrolledTop');
				//$('.ui-widget-content.ui-autocomplete').css('top', parseInt($('.ui-widget-content.ui-autocomplete').css('top')) - 77 + 'px');
				//kulturContainer.find('.content_wrap').css('padding-top', h);
			}
		}
	}

	mobileTop = function() {
		if(mobile) {
        	kulturContainer.removeClass('joScrolledTop');
        	//var h = kulturContainer.find('.logo2').height();
			//$('.ui-widget-content.ui-autocomplete').css('top', parseInt($('.ui-widget-content.ui-autocomplete').css('top')) - 77 + 'px');
			//kulturContainer.find('.content_wrap').css('padding-top', h);
        } else {
        	checkTop();
        }
	} 

	$(document).scroll(function() {
		if(!mobile) {
			checkTop();
		}
	});*/

	$(window).resize(function() {     
        mobile = ($('#mobile-hidden').is(':visible')) ? false : true;
        //mobileTop();
    });

    //mobileTop();
	//checkTop();

	/* menu status merken - start */
	if(window.localStorage.getItem('menuHidden') !== null && window.localStorage.getItem('menuHidden') === '1'){
		$($('[data-jotarget]').data('jotarget')).hide();
	}

	$('[data-jotarget]').on('click', function(ev) {
		ev.preventDefault();
		var element = $($(this).data('jotarget'));
		if(element.length) {
			if(element.is(':hidden')) {
				element.slideDown(300);
				window.localStorage.setItem('menuHidden', 0);
			} else {
				element.slideUp(300);
				window.localStorage.setItem('menuHidden', 1);
			}
		}
	})
	/* menu status merken - ende */

	function joInArray(value, arr){
		var toreturn = -1;

		$.each(arr, function(i, val) {
			if(i == value) {
				toreturn = i;
				return;
			}
		});

  		return toreturn;
	}
	
	getContent = function(url) {
        return $.ajax({
            'type': "GET",
            'url': url,
            'success': function(data){
                return data;
            }
        });
    }

    messageWriter = function(message) {
	    var container = $('.typo3-messages');
	    if(container.length) {
	        container.remove();
	    }

	    container = '<ul class="typo3-messages"><li class="alert alert-success"><p class="alert-message">' + message + '</p></li></ul>';
	    $('body').append(container);
	}

	$('.joStruktureRek-container .joOpener').click(function() {
		$(this).parent().parent().find('.joOpener.active').not(this).removeClass('active').next('.joStruktureLine-container').hide().next('.joStruktureRek-Menu').hide();
		var height = $(this).offset().top - $('.joStruktureRek-container > .joStruktureRek-Menu').offset().top - $('.joStruktureRek-container > .joStruktureRek-Menu').scrollTop() - 2;
		$(this).toggleClass('active').next('.joStruktureLine-container').toggle().height(height).next('.joStruktureRek-Menu').toggle();
	});

	$('.joStruktureRek-container .joCheck').click(function() {
		$(this).parent().toggleClass('active');
	});


	var aucotompleteInput = $('.joAutocomplete');
	if(aucotompleteInput) {
	    aucotompleteInput.on('keydown', function (event) {
	        // added posibility to use RIGHT key in the list
	        if (event.keyCode === $.ui.keyCode.RIGHT && $(this).autocomplete("instance").menu.active) {
	            event.preventDefault();
	            $($(this).autocomplete("instance").menu.active).trigger("click");
	        }
	        if (event.keyCode === $.ui.keyCode.ENTER) {
	            event.preventDefault();
	            /* var form = $('form[name="suche"]');
	            form.find('input.search_box').val($(this).val());
	            form.submit(); */
	            var input = $(this).prev('.entitySearchUrl');
	            if(input.length) {
	            	window.location.href = input.val().replace('TEXT', $(this).val());
	            }
	        }
	    }).autocomplete({
	        source: function (request, response) {
	        	var id = $(this.element).data('id');
	        	var url = $(this.element).parent().find('.entitySearchItemsUrl').val();
	        	var handler = $(this.element).data('handler');
	            // var url = '/index.php?id=' + id + '&eID=museo&action=suggest&handler=suggestentity&q=' + request.term;
	            //$.post(url, {'tx_jomuseo_pi1009%5Bq%5D': request.term} ).done(function(data, status, xhr) {
	            $.post(url, {q: request.term, handler: handler, id: id} ).done(function(data, status, xhr) {
	            	var res = ['Es wurde kein Eintrag gefunden.'];
	            	try {
	            		res2 = JSON.parse(data);

	            		if (res2.length) {
	            			res = res2;	
	            		}
	            	} catch(e) {}
	            	response(res);
	            });
	            /*
	            $.when($.get(url)).then(function(data, status, xhr) {
	            	var res = ['Es wurde kein Eintrag gefunden.'];
	            	try {
	            		res = JSON.parse(data);
	            	} catch(e) {}
	            	response(res);
	            });
	            */
	        },
	        focus: function () {
	            // prevent value inserted on focus
	            return false;
	        },
	        select: function (event, ui) {
	        	if(ui.item.link) {
	            	window.location.href = ui.item.link;
	        	}
	            return false;
	        },
	        open: function () {
	            // set the list width to input width
	            //$(this).autocomplete("widget").width($(this).outerWidth());
	        }
	    });

	    // query to avoid error output if autocomplete don't exist
	    if (aucotompleteInput.autocomplete("instance") != undefined) {
	        // mark the current typed text in the suggestion list
	        aucotompleteInput.autocomplete("instance")._renderItem = function (ul, item) {
	            var currentTerm = this.term;
	            var markedTerm = item.label.replace(currentTerm, "<span style='font-weight: 900;'>" +
	                currentTerm + "</span>");
	            return $("<li></li>").append("<div>" + markedTerm + "</div>").appendTo(ul);
	        };
	    }
	}
	/*
		var $control_sorting = $('div:not(.facette_dropdown) .item_sorting');
    	if ($control_sorting.length) $control_sorting.buildSelect('Sortierung');
	*/
	function joSliderHierarchie(containerSelector, mobile, spalten) {
        var container = $(containerSelector);
        var pane;
        var items;
        var width;
        var itemWidth;
        var spalten = spalten;
        var left = 0;
        var prev = container.prev('.joSliderShow-prev');
    	var next = container.next('.joSliderShow-next');
        var mobile = mobile;
        var id;
        var _this = this;

        _this.refreshContainer = function() {
            container = $(container.selector);
        }

        _this.updateItemWidth = function() {
            itemWidth = pane.find('.items').first().outerWidth();
        }

        _this.paneWidthReset = function() {
        	if (!mobile) {
	            var length = pane.find('.joSliderShowItems:visible').length;
	            pane.css('width', itemWidth * length + 'px');
            }
        }

        _this.fullreset = function() {
            width = container.width();
            _this.updateItemWidth();
            _this.paneWidthReset();
            _this.setDisplay('');
        }

        _this.getId = function() {
            return id;
        }

        _this.getSpalten = function() {
            return spalten;
        }

        _this.setSpalten = function(num) {
            spalten = num;
        }

        _this.getMobile = function() {
            return spalten;
        }

        _this.setMobile = function(bool) {
            mobile = bool;
        }

        _this.getItemWidth = function() {
            return itemWidth;
        }

        _this.setDisplay = function(trigger) {
        	if (!mobile) {
	            if(left < 0) {
	                prev.show();
	            } else {
	                prev.hide();
	            }
	            var panewidth = pane.width() + left;
	            if(panewidth <= width) {
	                next.hide();
	            } else {
	                next.show();
	            }
	            if(trigger == 'next') {
	            	_this.triggerNext();
	            } else if(trigger == 'prev') {
	            	num = spalten - ((pane.width() + left) / itemWidth);
	            	if(num > 0) {
	            		_this.triggerPrev();
	            	} else {
	            		setTimeout(function(){
	            			_this.nicescrollReset();
	            		}, 50);
	            	}
	            }
	        }
        }

        _this.nicescrollReset = function() {
        	//$('.hierachical .items > ul').getNiceScroll().hide();
        	if(jQuery().niceScroll) {
	        	container.find('.items > ul').getNiceScroll().hide();
	        }
        	//$('.nicescroll-rails').hide();
        	var num = Math.abs(Math.floor(left / itemWidth));
            var elements = container.find('.items:visible > ul').slice(num, num + spalten);
            if(jQuery().niceScroll) {
	            elements.getNiceScroll().resize();
	            elements.getNiceScroll().show();
	        }
        }

        _this.move = function() {
        	if (!mobile) {
	        	setTimeout(function(){
		        	//$('.hierachical .items > ul').getNiceScroll().hide();
		        	if(jQuery().niceScroll) {
			        	container.find('.items > ul').getNiceScroll().hide();
			        }
		            pane.css('transform', 'translate3d(' + left + 'px, 0px, 0px)');
		            setTimeout(function(){
		                _this.nicescrollReset();
		            }, 1000);
		        	_this.setDisplay('');
	        	}, 50);
	        }
        }

        //prev.off();
        prev.click(function() {
        	if (!mobile) {
	        	num = spalten - ((pane.width() + left) / itemWidth);
	        	if(num <= 0) {
	        		num = 1;
	        	}
	            left += itemWidth * num;
	            if(left > 0) {
	            	left = 0;
	            }
	            _this.move();
	        }
        });

        next.click(function() {
        	if (!mobile) {
	            left -= itemWidth;
	            _this.move();
	        }
        });

        _this.triggerPrev = function() {
            if(prev.is(':visible')) {
                prev.trigger('click');
            }
        }

        _this.triggerNext = function() {
            if(next.is(':visible')) {
                next.trigger('click');
            }
        }

        _this.init = function() {
        	id = container.closest('.hierachical').attr('id');
            pane = container.find('.joSliderShowPane');
            width = container.outerWidth();
            _this.fullreset();

            if(pane.is(':visible') && !mobile) {
            	num = (pane.width() / itemWidth) - spalten;
            	if(num > 0) {
            		left -= itemWidth * num;
            		_this.move();
            	}
            }
        }

        _this.init();
    }

    var joSlider = [];
    $('.joSliderShow-container').each(function() {
    	var sliderId = $(this).closest('.hierachical').attr('id');
    	joSlider[sliderId] = new joSliderHierarchie(this, mobile, 4);
    });

    $('body').on('click', '.hierachical .joOpener-container .joOpener', function(e) {
        e.preventDefault();
        var that = $(this);
        ajaxloader.show();

        var id = that.data('id');
        var el = $('#'+id);
        var sliderNum = that.closest('.hierachical').attr('id');
        if(el.length) {
        	var item = that.closest('.items');
        	if(el.is(':visible')) {
           		closeOtherItems(item);
        		el.hide();

       			el.removeClass('isLast');
       			//$('.joSliderShow-container .joSliderShowPane .joSliderShowItems:visible:last').addClass('isLast');
       			el.parent().find('.joSliderShowItems:visible:last').addClass('isLast');

        		that.removeClass('active');
        		that.closest('li').removeClass('joCheckOpen');
        		joSlider[sliderNum].paneWidthReset();
	            joSlider[sliderNum].setDisplay('prev');
	            if(jQuery().niceScroll) {
		            el.find('ul').getNiceScroll().hide();
		        }
        	} else {
        		closeOtherItems(item);

       			//$('.joSliderShow-container .joSliderShowPane .joSliderShowItems.isLast').removeClass('isLast');
       			el.parent().find('.joSliderShowItems.isLast').removeClass('isLast');
       			el.addClass('isLast');

        		el.show();
        		that.removeClass('active');
        		that.addClass('active');
        		that.closest('li').addClass('joCheckOpen');
	            joSlider[sliderNum].paneWidthReset();
	            joSlider[sliderNum].setDisplay('next');
	            if(jQuery().niceScroll) {
		            el.find('ul').getNiceScroll().show();
		            el.find('ul').getNiceScroll().resize();
		        }
        	}
        	ajaxloader.hide();
        } else {
        	var url = that.data('href') ? that.data('href') : that.attr('href');
	        $.when($.get(url)).then(function(data, status, jqXHR) {

	            var item = that.closest('.items');

	            closeOtherItems(item);

	            item.after(data);

	            var newItem = item.next('.joSubHierarchy');
	            newItem.attr('id', id);
		        if(!mobile && jQuery().niceScroll) {
		        	newItem.find('ul').niceScroll({cursorcolor:"rgba(255,255,255,0.5)",autohidemode:false,enableobserver:false});
		        }

            	//$('.joSliderShow-container .joSliderShowPane .joSliderShowItems.isLast').removeClass('isLast');
            	newItem.parent().find('.joSliderShowItems.isLast').removeClass('isLast');
            	newItem.addClass('isLast');

                joSlider[sliderNum].paneWidthReset();
                joSlider[sliderNum].setDisplay('next');

	            ajaxloader.hide();
	            that.addClass('active');

	            that.closest('li').addClass('joCheckOpen');
	        }, function(jqXHR, status, errorText) {
	            console.log(jqXHR);
	            messageWriter(status + '<br>' + errorText + '<br>' + 'Upps, etwas ging schief. Bitte aktualisieren Sie die Seite und versuchen Sie es nochmal.');
	            ajaxloader.hide();
	        });
        }
    });

    closeOtherItems = function(item) {
    	var that = $(item);
    	var level = parseInt(that.data('level'));
		if(level) {
			var otherItems = that.parent().find('.joSubHierarchy').filter(function() { return parseInt($(this).data('level')) > level && $(this).is(':visible'); });
        	otherItems.hide().find('.joOpener.active').removeClass('active');
        	otherItems.find('.joCheckOpen').removeClass('joCheckOpen');
        	if(jQuery().niceScroll) {
	        	otherItems.find('ul').getNiceScroll().hide();
	        }
        }
        that.find('.joOpener.active').removeClass('active');
    	that.find('.joCheckOpen').removeClass('joCheckOpen');
    }

	if(!mobile && jQuery().niceScroll) {
		$('.hierachical .items > ul').niceScroll({cursorcolor:"rgba(255,255,255,0.5)",autohidemode:false,enableobserver:false});
    }

    // activate and deactivate nicescroll
	$('.joControls a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var id = $(this).attr('href');
        var items = $(id).find('.items > ul');
        if(jQuery().niceScroll) {
	        $('.hierachical .items > ul').getNiceScroll().hide();
	        items.getNiceScroll().show();
	        items.getNiceScroll().resize();
	    }

        if(joSlider[id.substring(1)]) {
			joSlider[id.substring(1)].fullreset();
        }
	});

    $(window).resize(function() {
        $.each(joSlider, function(i, val) {
    		val.setMobile(mobile);
    	});

        if(jQuery().niceScroll) {
	        if(mobile) {
	        	$('.hierachical .items > ul').getNiceScroll().remove();
	        } else {
	        	$('.hierachical .items > ul').niceScroll({cursorcolor:"rgba(255,255,255,0.5)",autohidemode:false,enableobserver:false});
	        }
	    }
    });

    var working = false;
	var ajaxloader = $('#joAjaxloader');
	$('.joAjaxLink').click(function(e) {
		e.preventDefault();
		if(!working) {
			working = true;
			var _that = $(this);
			
			if(_that.next('.joAjaxContainer').children().length != 0) {
				_that.parent().toggleClass('active');
				_that.next('.joAjaxContainer').slideToggle();
				working = false;
			} else {
				ajaxloader.show();
				$.when($.get(_that.attr('href'))).then(function(data, status, jqXHR) {
					_that.next('.joAjaxContainer').hide().html(data).slideDown();
					ajaxloader.hide();
					_that.parent().addClass('active');
					working = false;
		    	}, function(jqXHR, status, errorText) {
					console.log(jqXHR);
		    		ajaxloader.hide();
		    		working = false;
		    	});
			}
		}
	});

	var _btnTop = $('.back-to-top');
	var footerHeight = $('.footer-outer').outerHeight();
	if(typeof TYPO3 != 'undefined' && typeof TYPO3.settings != 'undefined' && typeof TYPO3.settings.TS != 'undefined' && typeof TYPO3.settings.TS.scrtop != 'undefined' && TYPO3.settings.TS.scrtop == 'nojump') {
		footerHeight = 0;
	}

	$(window).scroll(function () {
		if (!_btnTop.hasClass('custom')) {
			var scrolltop = $(this).scrollTop();
			if (scrolltop > 100) {
				if(!_btnTop.hasClass('active'))_btnTop.addClass('active');
			} else {
				if(_btnTop.hasClass('active'))_btnTop.removeClass('active');
			}

			if(scrolltop + $(window).height() > $(document).height() - footerHeight) {
	       		_btnTop.css('bottom', footerHeight + 'px');
		   	} else {
		   		_btnTop.css('bottom', '0');
		   	}
		}
	});

	_btnTop.click(function () {
		$('body,html').animate({
			scrollTop: 0
		}, 800);
		return false;
	});

	/*var search_box = $('.search_box');
	if(search_box.length) {
		search_box.before('<div id="typeText-container">Bestand durchsuchen \"<span id="typeText"></span>\"</div>');

		var typed3 = new Typed('#typeText', {
		    strings: ['Hallo', 'Welt', 'Jena', 'Weimar', 'Thüringen', 'Luther', 'Kultur', 'Chronik'],
		    typeSpeed: 50,
		    backSpeed: 50,
		    backDelay: 2000,
		    startDelay: 2000,
		    loop: 5
	  	});

		var typeText = $("#typeText-container");
		var searchbox = $('.search_box');
		typeText.click(function() {
			$(this).hide();
			searchbox.focus();
		});
		searchbox.focusout(function() {
			typeText.show();
		});
	} */

	$('.joControls').on('click', '.c_button.active.show', function() {
		var that = $(this);
		setTimeout(function() {
			that.removeClass('active show');
			$(that.attr('href')).removeClass('active show');
			if(jQuery().niceScroll) {
				$('.hierachical .items > ul').getNiceScroll().hide();
			}
		}, 100);
	});

	//$('body').popover({selector: '[data-toggle=popover]', trigger: 'click', html: true});

	$('body').popover({selector: '[data-toggle=joTeiPopover]', trigger: 'click', html: true}).on('show.bs.popover', function(e) {
        $('[data-toggle=joTeiPopover]').not(e.target).popover('hide');
        $('.popover').remove();
    });

	if($.fn.readmore) {
		$('.newslist .item .bodytext').readmore({
	  		speed: 500,
	  		lessLink: '<a class="joNewsMoreLess" href="#">Weniger...</a>',
	  		moreLink: '<a class="joNewsMoreLess" href="#">Mehr erfahren...</a>'
		});
	}

	var joCarousel = $("#joCarousel");

	if(joCarousel.length && $.fn.swiperight && $.fn.swipeleft) {
		joCarousel.swiperight(function() {
	  		$(this).carousel('prev');
	    });
		joCarousel.swipeleft(function() {
	  		$(this).carousel('next');
		});
	}

	if(window.location.href.indexOf('HisBest_cbu_00029633') >= 0) {
		$('.showTei').first().trigger('click');
	}

	$('[data-toggle="jostab"]').click(function() {
		var $that = $(this);

		if($that.hasClass('active')) return false;

		function closeOther() {
			var dfd = $.Deferred();

			var $jotab = $('[data-toggle="jostab"]').not($that);

			if($jotab.length) {
				$jotab.each(function(i,v) {
					var $target = $($(this).data('target'));

					if($target.length) $target.removeClass('active').fadeOut(400, function() { if(i == length) dfd.resolve(); });
					else dfd.resolve();

					$(this).removeClass('active');
				});
			} else {
				dfd.resolve();
			}

			return dfd.promise();
		}

		$that.addClass('active');

		$.when(closeOther()).done(function(data) {
			var $target = $($that.data('target'));

			if($target.length) $target.addClass('active').fadeIn();
		});
	});

	/*
    var $control = $('.facette_dropdown .joControls');
    if ($control.length) {
        if (!$control.find('.mb-opt').length) {
            $control.prepend('<li class="mb-selected d-block"><span>Bitte wählen</span></li><li class="mb-opt selected d-none"><span>Filter</span></li>');
        }
        
        var $allItems = $control.find('li');

        $control.click(function() {
            $(this).toggleClass('open');
            setTimeout(() => {
                $(this).find('.mb-selected .c_button.active.show').removeClass('active show');
            }, 100);
        });

        $allItems.click(function() {
            var $that = $(this);
            if ($that.parent().hasClass('open') && !$that.hasClass('mb-selected')) {
                if ($that.hasClass('selected')) {

                } else {
                    $allItems.removeClass('selected');
                    $that.addClass('selected');
                    $control.find('.mb-selected').html($that.html()).promise().done(function() {
                        $control.find('.mb-selected .c_button').off();
                    });
                }
            }
        });

        $('.facette_dropdown .joControls li.mb-opt').click(function() {
            $('.fa01 .c_button.active.show').removeClass('active show');
            $('.fa01 .tab-pane.active.show').removeClass('active show');
            if (jQuery().niceScroll) $('.hierachical .items > ul').getNiceScroll().hide();
        });
    }
    */

    /*
    var $control_sorting = $('.facette_dropdown .item_sorting');
    if ($control_sorting.length) {
    	if (!$control_sorting.find('.mb-opt').length) {
            $control_sorting.prepend('<li class="mb-selected d-block"><span>Bitte wählen</span></li><li class="mb-opt selected d-none"><span>Sortierung</span></li>');
        }

        var $allItems = $control.find('li');

        $control.click(function() {
            $(this).toggleClass('open');
            setTimeout(() => {
                $(this).find('.mb-selected .c_button.active.show').removeClass('active show');
            }, 100);
        });

        $allItems.click(function() {
            var $that = $(this);
            if ($that.parent().hasClass('open') && !$that.hasClass('mb-selected')) {
                if ($that.hasClass('selected')) {

                } else {
                    $allItems.removeClass('selected');
                    $that.addClass('selected');
                    $control.find('.mb-selected').html($that.html()).promise().done(function() {
                        $control.find('.mb-selected .c_button').off();
                    });
                }
            }
        });
    }
    */
});


/* $(document).ajaxComplete(function() {
	$('[data-toggle="popover"]').popover({html: true});
}); */
