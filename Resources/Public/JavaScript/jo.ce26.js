$(() => {
	var $fedItem = $('.fed-item');
	if ($fedItem.length) {
		$fedItem.click(function() {
			$(this).toggleClass('active');

			var $pers_con = $(this).closest('.joPers-content');
			var $act_items = $pers_con.find('.fed-item.active');
			var $inst_con = $pers_con.find('.inst-con');

			if (!$inst_con.length) return false;

			if (!$act_items.length) {
				$inst_con.find('.inst-item').not('.nothing').show();
				return false;
			}

			var stateArr = [];
			var typArr = [];
			$act_items.each(function(i, v) {
				var state = $(this).data('state');
				var typ = $(this).data('typ');

				if (typeof state !== 'undefined' && state != '') stateArr.push(state);

				if (typeof typ !== 'undefined' && typ != '') typArr.push(typ);
			});


			var $items = null;
			var $otherItems = null;

			if (stateArr.length != 0 && typArr.length == 0) {
				$items = $inst_con.find('.inst-item').filter(function() { 
					return typeof $(this).data('state') !== 'undefined' && stateArr.indexOf($(this).data('state')) != -1;
				});
				$otherItems = $inst_con.find('.inst-item').not($items);
			}

			if (stateArr.length == 0 && typArr.length != 0) {
				$items = $inst_con.find('.inst-item').filter(function() { 
					return typeof $(this).data('typ') !== 'undefined' && typArr.indexOf($(this).data('typ')) != -1;
				});
				$otherItems = $inst_con.find('.inst-item').not($items);
			}

			if (stateArr.length != 0 && typArr.length != 0) {
				$items = $inst_con.find('.inst-item').filter(function() { 
					return typeof $(this).data('state') !== 'undefined' && 
					stateArr.indexOf($(this).data('state')) != -1 && 
					$(this).data('typ') !== 'undefined' && 
					typArr.indexOf($(this).data('typ')) != -1;
				});
				$otherItems = $inst_con.find('.inst-item').not($items);
			}

			if ($otherItems != null) $otherItems.hide();

			if ($items != null && $items.length != 0) {
				$items.show();
			} else {
				$inst_con.find('.nothing').show();
			}
		});
	}
});

