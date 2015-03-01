$(function(){
	if( $.fn.chat ) {
		$.fn.chat('settings',{
			strings: {
				noComments: 'Ei vielä yhtään viestiä',
				noMoreComments: 'Viestejä ei ole enempää',
				sendError: 'Viestin lähettäminen epäonnistui.',
				reply: 'vastaa',
				cancelReply: 'peruuta',
				nicknameInput: 'Valitse nimimerkki',
				msgPlaceholder: 'Kirjoita uusi viesti',
				msgMaxLengthReached: 'Viestin maksimipituus on täynnä',
				admin: {
					pinMessage: 'Korosta tämä viesti',
					unpinMessage: 'Poista viestin korostus',
					removeMessage: 'Poista viesti',
					restoreMessage: 'Palauta viesti',
					approveMessage: 'Hyväksy viesti',
					confirmRemoveMessage: 'Poistetaanko tämä viesti?'
				}
			}
		});
	}
	
	$.datepicker.regional['fi'] = {
		closeText: 'Sulje',
		prevText: '&laquo;Edellinen',
		nextText: 'Seuraava&raquo;',
		currentText: 'Tänään',
		monthNames: ['Tammikuu','Helmikuu','Maaliskuu','Huhtikuu','Toukokuu','Kesäkuu','Heinäkuu','Elokuu','Syyskuu','Lokakuu','Marraskuu','Joulukuu'],
		monthNamesShort: ['Tammi','Helmi','Maalis','Huhti','Touko','Kesä','Heinä','Elo','Syys','Loka','Marras','Joulu'],
		dayNamesShort: ['Su','Ma','Ti','Ke','To','Pe','La'],
		dayNames: ['Sunnuntai','Maanantai','Tiistai','Keskiviikko','Torstai','Perjantai','Lauantai'],
		dayNamesMin: ['Su','Ma','Ti','Ke','To','Pe','La'],
		weekHeader: 'Vk',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['fi']);
	
	if( $.timeago ) {
		$.timeago.settings.strings = {
			prefixAgo: null,
			prefixFromNow: null,
			suffixAgo: "sitten",
			suffixFromNow: "",
			seconds: "alle minuutti",
			minute: "noin minuutti",
			minutes: "%d minuuttia",
			hour: "noin tunti",
			hours: "noin %d tuntia",
			day: "päivä",
			days: "%d päivää",
			month: "noin kuukausi",
			months: "%d kuukautta",
			year: "noin vuosi",
			years: "%d vuotta",
			numbers: []
		};
	}
});