$(function() {
	//###########################################################################
	//Video Player Abschnitt ####################################################
	//###########################################################################

	async function digestMessage(message) {
		const msgUint8 = new TextEncoder().encode(message);                           // encode as (utf-8) Uint8Array
		const hashBuffer = await crypto.subtle.digest('SHA-256', msgUint8);           // hash the message
		const hashArray = Array.from(new Uint8Array(hashBuffer));                     // convert buffer to byte array
		const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join(''); // convert bytes to hex string
		return hashHex;
	}

	//Hash berechnung (sha256) aus ascii Zeichenkette
	function sha256(ascii) {
		function rightRotate(value, amount) {
			return (value>>>amount) | (value<<(32 - amount));
		};

		var mathPow = Math.pow;
		var maxWord = mathPow(2, 32);
		var lengthProperty = 'length'
		var i, j; // Used as a counter across the whole file
		var result = ''

		var words = [];
		var asciiBitLength = ascii[lengthProperty]*8;

		//* caching results is optional - remove/add slash from front of this line to toggle
		// Initial hash value: first 32 bits of the fractional parts of the square roots of the first 8 primes
		// (we actually calculate the first 64, but extra values are just ignored)
		var hash = sha256.h = sha256.h || [];
		// Round constants: first 32 bits of the fractional parts of the cube roots of the first 64 primes
		var k = sha256.k = sha256.k || [];
		var primeCounter = k[lengthProperty];
		/*/
		var hash = [], k = [];
		var primeCounter = 0;
		//*/

		var isComposite = {};
		for (var candidate = 2; primeCounter < 64; candidate++) {
			if (!isComposite[candidate]) {
				for (i = 0; i < 313; i += candidate) {
					isComposite[i] = candidate;
				}
				hash[primeCounter] = (mathPow(candidate, .5)*maxWord)|0;
				k[primeCounter++] = (mathPow(candidate, 1/3)*maxWord)|0;
			}
		}

		ascii += '\x80' // Append Ƈ' bit (plus zero padding)
		while (ascii[lengthProperty]%64 - 56) ascii += '\x00' // More zero padding
		for (i = 0; i < ascii[lengthProperty]; i++) {
			j = ascii.charCodeAt(i);
			if (j>>8) return; // ASCII check: only accept characters in range 0-255
			words[i>>2] |= j << ((3 - i)%4)*8;
		}
		words[words[lengthProperty]] = ((asciiBitLength/maxWord)|0);
		words[words[lengthProperty]] = (asciiBitLength)

		// process each chunk
		for (j = 0; j < words[lengthProperty];) {
			var w = words.slice(j, j += 16); // The message is expanded into 64 words as part of the iteration
			var oldHash = hash;
			// This is now the undefinedworking hash", often labelled as variables a...g
			// (we have to truncate as well, otherwise extra entries at the end accumulate
			hash = hash.slice(0, 8);

			for (i = 0; i < 64; i++) {
				var i2 = i + j;
				// Expand the message into 64 words
				// Used below if
				var w15 = w[i - 15], w2 = w[i - 2];

				// Iterate
				var a = hash[0], e = hash[4];
				var temp1 = hash[7]
					+ (rightRotate(e, 6) ^ rightRotate(e, 11) ^ rightRotate(e, 25)) // S1
					+ ((e&hash[5])^((~e)&hash[6])) // ch
					+ k[i]
					// Expand the message schedule if needed
					+ (w[i] = (i < 16) ? w[i] : (
							w[i - 16]
							+ (rightRotate(w15, 7) ^ rightRotate(w15, 18) ^ (w15>>>3)) // s0
							+ w[i - 7]
							+ (rightRotate(w2, 17) ^ rightRotate(w2, 19) ^ (w2>>>10)) // s1
						)|0
					);
				// This is only used once, so *could* be moved below, but it only saves 4 bytes and makes things unreadble
				var temp2 = (rightRotate(a, 2) ^ rightRotate(a, 13) ^ rightRotate(a, 22)) // S0
					+ ((a&hash[1])^(a&hash[2])^(hash[1]&hash[2])); // maj

				hash = [(temp1 + temp2)|0].concat(hash); // We don't bother trimming off the extra ones, they're harmless as long as we're truncating when we do the slice()
				hash[4] = (hash[4] + temp1)|0;
			}

			for (i = 0; i < 8; i++) {
				hash[i] = (hash[i] + oldHash[i])|0;
			}
		}

		for (i = 0; i < 8; i++) {
			for (j = 3; j + 1; j--) {
				var b = (hash[i]>>(j*8))&255;
				result += ((b < 16) ? 0 : '') + b.toString(16);
			}
		}
		return result;
	}

	//URL Erzeugen
	async function URLGenerator(ID, Name, BaseURL, thumb) {
		var Eingabe  = ID + "_" + decodeURI(Name);
		//console.log("Eingabe: " + Eingabe)
		//SHA256 berechnen, ergebnis in digest
		var Output = sha256(Eingabe);
		// var Output = await digestMessage(Eingabe);
		//Hash zu großbuchstaben konvertieren
		var CutZero = Output.toUpperCase();
		//console.log("Sha-256: " + CutZero)
		//Anführende Nullen im HASH code entfernen
		do {
			if (CutZero.substr(0,1) == "0") {
				CutZero = CutZero.substr(1,CutZero.length - 1);
			}
		} while(CutZero.substr(0,1) == "0")
		//hash an dbt-link anfügen
		if (BaseURL === undefined || BaseURL === "") {
			BaseURL = '';
		}
		if (thumb) {
			return BaseURL + "/rsc/media/thumb/" + CutZero + "/" + Name.substring(0, Name.lastIndexOf(".")) + "-thumb-01.jpg";
		} else {
			return BaseURL + "/rsc/media/sources/" + CutZero;
		}
	}

	function parseURL(elm){
		const regex = /(https*:\/\/[^\/]+)\/.+\/(.+_derivate_\d+)\/(.+)/;
		const match = $(elm).attr('data-streamingURL').match(regex);
		if (match.length > 3) {
			$(elm).attr('data-baseurl', match[1]);
			$(elm).attr('data-derivateid', match[2]);
			$(elm).attr('data-filename', match[3]);
		}
	}

	//Video Player Source URLs Laden
	$(".thulb-ps-player").each(function( index, element ) {
		if($(element).find(".thulb-ps-player-source-container").length > 0) {
			//Player starten
			videojs($(element).find('.thulb-ps-player-video').get(0));
			//Player ID holen für Zugriff
			var Player = videojs($(element).find('.thulb-ps-player-video').get(0));
			//Für jedes Video im Container die dbt-Server Video Source URL erzeugen
			$(element).find($(".thulb-ps-player-source-container")).each(function( index ) {
				if ($( this ).attr('data-src').length < 1) {
					if ($(this).attr('data-streamingURL')) {
						parseURL($(this));
					}
					//Derivateid und Filename aus .thulb-ps-player-source-container holen
					var Derivateid = $( this ).attr('data-derivateid');
					var Filename = $( this ).attr('data-filename');
					var BaseURL = $( this ).attr('data-baseurl');
					//Erzeugen der URL für Zugriff auf dbt-Server
					var URL = URLGenerator(Derivateid, Filename, BaseURL, false).then((URL) => {
						//Zugriff auf dbt-Server und holen der Video sources
						var DBTSources = $.ajax({type: "GET", dataType:"html", url: URL, async: false}).responseText;
						var output = $($.parseXML(DBTSources));

						//Einfügen der URLs in die Container
						$(this).attr("data-src", output.find("source[type='application/x-mpegURL']").attr("src"));
						//Zuweisung der source URL an den Player
						if (index === 0) {
							Player.src(output.find("source[type='application/x-mpegURL']").attr("src"));
							URLGenerator(Derivateid, Filename, BaseURL, true).then((thumbURL) => {
								Player.poster(thumbURL);
							});
						}
					});
				}
				else {
					Player.src($( this ).attr('data-src'));
				}
				if ($( this ).attr('data-sub-de')) {
					Player.addRemoteTextTrack({src: $( this ).attr('data-sub-de'), srclang:'de', label:'Deutsch'}, false);
				}
				if ($( this ).attr('data-sub-en')) {
					Player.addRemoteTextTrack({src: $( this ).attr('data-sub-en'), srclang:'en', label:'Englisch'}, false);
				}
			});
		}
	});

});
