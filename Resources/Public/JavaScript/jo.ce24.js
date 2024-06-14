$(() => {
	var datepicker = $('.datepicker');
	if (datepicker.length && typeof eventdates != 'undefined') {
		var eventdates2 = JSON.parse(eventdates);
		$.fn.datepicker.dates['de'] = {
		    days: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
		    daysShort: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
		    daysMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
		    months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
		    monthsShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
		    today: "Heute",
		    monthsTitle: "Monate",
		    clear: "Löschen",
		    weekStart: 1,
		    format: "dd.mm.yyyy"
		};
	    $('.datepicker').datepicker({
		    maxViewMode: 0,
			locale: 'de',
		    language: 'de',
		    keyboardNavigation: false,
		    forceParse: false,
		    calendarWeeks: true,
		    todayHighlight: true,
		    startDate: new Date(),
		    beforeShowDay: (date) => {
		    	if (typeof eventdates2[date.getFullYear()] != 'undefined' && typeof eventdates2[date.getFullYear()][date.getMonth() + 1] != 'undefined' && typeof eventdates2[date.getFullYear()][date.getMonth()+1][date.getDate()] != 'undefined') {
		    		var tmp_date = eventdates2[date.getFullYear()][date.getMonth() + 1][date.getDate()];
		    		return {
	              		tooltip: tmp_date['header'],
	              		classes: 'active',
	              		enabled: true
	                };
		    	} else {
		    		return {
	              		enabled: false
	                };
		    	}
	        },
	  	}).on('changeDate', (e) => {
			var date = e.date;
			if (typeof eventdates2[date.getFullYear()] != 'undefined' && typeof eventdates2[date.getFullYear()][date.getMonth() + 1] != 'undefined' && typeof eventdates2[date.getFullYear()][date.getMonth()+1][date.getDate()] != 'undefined') {
	    		window.location.href = eventdates2[date.getFullYear()][date.getMonth() + 1][date.getDate()]['uri'];
	    	}
	  	});

	}
});
