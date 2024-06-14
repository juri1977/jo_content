$(() => {
  $('.frame-type-jocontent_c11 .list_view_1 li').click(function() {
    $(this).parent().find('.active').not(this).removeClass('active');
    $(this).toggleClass('active');
  });

  $('.frame-type-jocontent_c11 .list_view_1 li a').click(function(e) {
    e.stopPropagation();
  });

  var $sl_slider = $('.c11-slide-show');
  if ($sl_slider.length && jQuery().slick) {
    $sl_slider.slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true,
      dots: true,
    });
  }

  var $sl_slider_sub = $('.c11-slide-subobjects');
  if ($sl_slider_sub.length && jQuery().slick) {
    $sl_slider_sub.slick({
      slidesToShow: 3,
      slidesToScroll: 3,
      arrows: true
    });
  }
  
  var $longdesc = $('.descriptionlong>p');

  if ($longdesc.length > 1) {
    $longdesc.slice(1).wrapAll('<div id="descriptionlong_collapse" class="collapse"><div></div></div>').promise().done(function(e) {
      var mehrText = $('.descriptionlong').data('more');
      var wenigerText = $('.descriptionlong').data('less');
      $('.descriptionlong > #descriptionlong_collapse').after('<div class="descriptionlong-btn-wrap"><button class="descriptionlong-btn collapsed" type="button" data-toggle="collapse" data-target="#descriptionlong_collapse" aria-expanded="false" aria-controls="descriptionlong_collapse"><span class="c-more">' + mehrText + '</span><span class="c-less">' + wenigerText + '</span></button></div>');
    });
  }

  var $vidbtn = $('.c11-angebote .play-meta-audio');
  if ($vidbtn.length) {
    $vidbtn.click(function() {
      var $that = $(this);
      var $parent = $that.closest('.angebote-item');

      $parent.removeClass('show-video');
      $parent.find('.meta-video').trigger('pause');
      $parent.find('.play-meta-video').removeClass('playing');
      

      if ($that.hasClass('playing')) {
        $that.removeClass('playing');
        $parent.removeClass('show-audio');
        $parent.find('.meta-audio').trigger('pause');
      } else {
        $that.addClass('playing');
        $parent.addClass('show-audio');
        $parent.find('.meta-audio').trigger('play');
      }
    });
  }

  var $vidbtn = $('.c11-angebote .play-meta-video');
  if ($vidbtn.length) {
    $vidbtn.click(function() {
      var $that = $(this);
      var $parent = $that.closest('.angebote-item');

      $parent.removeClass('show-audio');
      $parent.find('.meta-audio').trigger('pause');
      $parent.find('.play-meta-audio').removeClass('playing');
      

      if ($that.hasClass('playing')) {
        $that.removeClass('playing');
        $parent.removeClass('show-video');
        $parent.find('.meta-video').trigger('pause');
      } else {
        $that.addClass('playing');
        $parent.addClass('show-video');
        $parent.find('.meta-video').trigger('play');
      }
    });
  }

  $('body').on('click', '.c11-zitierlink a', function(e) {
    e.preventDefault();
    var $that = $(this);
    $that.removeClass('success').removeClass('fail');
    var tmp = document.createElement('textarea');
    tmp.style.position = 'absolute';
    tmp.style.opacity = '0';
    tmp.value = $that.attr('href');
    document.body.appendChild(tmp);
    tmp.select();
    var status = document.execCommand('copy');
    document.body.removeChild(tmp);
    if (status) {
        $that.addClass('success');
    } else {
        $that.addClass('fail');
    }
});

});
