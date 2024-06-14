$(() => {
	var mobile = ($('#news-mobile-hidden').is(':visible')) ? false : true;

    $( window ).resize(() => {     
        mobile = ($('#news-mobile-hidden').is(':visible')) ? false : true;
        toogleHoverDir();
    });

	$('#news-effekt > li').hoverdir({hoverElem: '.news-effekt-text'});
	toogleHoverDir();

	var working = false;
	$('.news-effekt-controls #joOption_1').click(function() {
		if (!working) {
			working = true;
	        $(this).toggleClass('joActive');
	        $(this).next().toggleClass('joActive');
	        $('#news-effekt > li').hoverdir('rebuild', {hoverElem: '.news-effekt-text'});
	        $('#news-effekt').toggleClass('fixed');
	        setTimeout(function() {
				working = false;
			}, 300);
		} 
    });

    $('.news-effekt-controls #joOption_2').click(function() {
    	if (!working) {
			working = true;
	        $(this).toggleClass('joActive');
	        $(this).prev().toggleClass('joActive');
	        //$('#news-effekt > li').hoverdir('destroy');
	        $('#news-effekt > li').hoverdir('rebuild', {hoverElem: '.news-effekt-text'});
	        $('#news-effekt').toggleClass('fixed');
        	setTimeout(function() {
				working = false;
			}, 300);
		} 
    });

    function toogleHoverDir() {
    	if (mobile) {
        	$('#news-effekt > li').hoverdir('destroy');
        } else {
        	$('#news-effekt > li').hoverdir('rebuild', {hoverElem: '.news-effekt-text'});
        }
    }

    $('.showMore').click(function(e) {
    	e.preventDefault();
    	var that = $(this);
		var parent = $(that).closest('.joWithMore');
		var prevEl = $(that).parent().prev();

    	if (parent.hasClass('active')) {
    		prevEl.animate({maxHeight: '50px'}, 1000);
    	} else {
    		var copy = prevEl.clone();
    		copy.css('maxHeight', '5000px');
    		copy.appendTo(".joWithMore");
    		prevEl.animate({maxHeight: copy.height()}, 1000);
    		copy.remove();
    	}
    	parent.toggleClass('active');
    });

    ready = false;

    $(".jp-jplayer").each(function() {
    	var that = $(this);
    	that.jPlayer({
			ready: function (event) {
				ready = true;
				$(this).jPlayer("setMedia", {
					mp3: that.data('src')
				});
			},
			pause: function() {
				$(this).jPlayer("clearMedia");
			},
			error: function(event) {
				if (ready && event.jPlayer.error.type === $.jPlayer.error.URL_NOT_SET) {
					// Setup the media stream again and play it.
					$(this).jPlayer("setMedia", {mp3: that.data('src')}).jPlayer("play");
				}
			},
			swfPath: "./jqueryJplayerSwf",
			supplied: "mp3",
			preload: "none",
			wmode: "window",
			useStateClassSkin: true,
			autoBlur: false,
			keyEnabled: true,
			cssSelectorAncestor: that.data('container')
		});
    });

    $(".jp-audio-stream:not(.jp-state-playing) .jp-play").click(function() {
    	$('.jp-state-playing').not(this).prev().jPlayer("pause");
    });
});

var player;
function onlyOnePlayer(id) {
	var _this = document.getElementById(id); 
	if (player && player != _this) {
		player.pause();
		_this.play();
	}
	player = _this;
}