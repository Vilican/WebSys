$("#editPost").submit( function () {
	$.ajax({
		type: "POST",
		data: $(this).serialize(),
		cache: false,
		url: "logic/editpost.php",
		success: function(data){
			switch (data) {
				case "0":
					alert("Jméno nesmí být prázdné!");
					break;
				case "1":
					alert("Jméno je moc dlouhé (max. 24 znaků)!");
					break;
				case "2":
					alert("Captcha nesprávně opsaný!");
					document.getElementsByName("captcha").value = null;
					break;
				case "3":
					alert("Příspěvek nesmí být prázdný!");
					break;
				case "4":
					alert("Příspěvek je moc dlouhý (max. 1024 znaků)!");
					break;
				case "5":
					alert("Chybný ukazatel nebo přístup odepřen!");
					break;
				case "6":
					alert("CSRF: chybný token; to může znamenat útok!");
					break;
				case "7":
					alert("Neexistující příspěvek!");
					break;
				case "8":
					alert("Přístup zamítnut!");
					break;
				case "9":
					var parts = window.location.search.substr(1).split("&");
					var $_GET = {};
					for (var i = 0; i < parts.length; i++) {
						var temp = parts[i].split("=");
						$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
					}
					window.location.href = 'index.php?p=' + $_GET['p'];
					break;
				default:
					alert("ERROR: Neznámá návratka!");
					break;
			}
		}
	});   
	return false;
});