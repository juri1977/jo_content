$(function() {
    $('body').on('click', '.filter-select-container .category-item', function() {
        var $that = $(this);
        var kat = $that.data('kat');
        if ('' != kat) {
            var $events = $('.c10.events .day-item');
            $('.c10.events .event-item').removeClass('d-none');
            $events.removeClass('d-none');

            if ($that.hasClass('active')) {
                $that.removeClass('active');
                return true;
            } else {
                $that.parent().find('.category-item').not(this).removeClass('active');
                $that.addClass('active');
            }

            var $other = $events.filter(function(i) {
                $kat_arr = ($(this).data('kat')).split(',');
                return !$kat_arr.includes(kat);
            });

            if ($other.length) {
                $other.addClass('d-none');
                $other.parent().filter(function(i) {
                    return $(this).children('.day-item:not(.d-none)').length == 0;
                }).parent().addClass('d-none');
            }
        }
    });
});