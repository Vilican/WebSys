<?php

if (!isset($_SESSION["id"]) or $_SESSION["id"] != 0) {
	
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
	
} else {

	require "require/sys-logic.php";
	
	$settings = $mysql->query("SELECT * FROM `settings`");
	while ($setting = $settings->fetch_assoc()) {
		$sys[$setting["setting"]] = $setting["value"];
	}
	
	switch ($sys["license"]) {
		case 0:
			$lic_notice = '<div class="alert alert-warning"><p><strong>Nelicencovaný systém (FREE)</strong></p><p>a) pouze nekomerční použití<br>b) musí zobrazovat zpětný odkaz</p></div>';
			break;
		case 1:
			$lic_notice = '<div class="alert alert-info"><p><strong>Licencovaný systém (PLUS)</strong></p><p>a) komerční použití je povoleno<br>b) musí zobrazovat zpětný odkaz</p></div>';
			break;
		case 2:
			$lic_notice = '<div class="alert alert-success"><p><strong>Licencovaný systém (PRO)</strong></p><p>a) komerční použití je povoleno<br>b) zpětný odkaz není požadován</p></div>';
			break;
	}

	if ($sys["license"] == 2) {
		$whitelabel = '<div class="checkbox"><label><input type="checkbox" name="whitelabel"' . parse_to_checkbox($sys["whitelabel"]) . '> Skrýt zpětné odkazy <span class="label label-warning">PRO</span></label></div>';
	}
	
	$page["title"] = 'Nastavení systému';
	
	$page["content"] = $message .'<form method="post">
	<button type="submit" name="save" class="btn btn-default">Uložit všechny změny</button>
	<br /><br />
	<ul class="nav nav-inpage nav-tabs">
		<li class="active"><a data-toggle="tab" href="#basic">Základní</a></li>
		<li><a data-toggle="tab" href="#posts">Příspěvky</a></li>
		<li><a data-toggle="tab" href="#users">Uživatelé</a></li>
		<li><a data-toggle="tab" href="#security">Bezpečnost</a></li>
		<li><a data-toggle="tab" href="#design">Design</a></li>
		<li><a data-toggle="tab" href="#license">Licence</a></li>
		<li><a data-toggle="tab" href="#sys">O systému</a></li>
	</ul>
	<div class="tab-content">
		<div id="basic" class="tab-pane fade in active">
			<div class="row col-lg-6 col-sm-9 col-xs-12">
				<div class="form-group">
					<label class="control-label" for="title">Název:</label>
					<input type="text" class="form-control" id="title" name="title" value="' . $sys["title"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="author">Autor:</label>
					<input type="text" class="form-control" id="author" name="author" value="' . $sys["author"] . '">
				</div>
			</div>
		</div>
		<div id="posts" class="tab-pane fade">
			<div class="row col-lg-6 col-sm-9 col-xs-12">
				<div class="checkbox">
					<label><input type="checkbox" name="anonymousposts"' . parse_to_checkbox($sys["anonymousposts"]) . '> Povolit nepřihlášeným psát</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="flags"' . parse_to_checkbox($sys["flags"]) . '> Povolit nahlašování</label>
				</div>
				<div class="form-group">
					<label class="control-label" for="paging">Max. příspěvků na stránce:</label>
					<input type="text" class="form-control" id="paging" name="paging" value="' . $sys["paging"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="authoredittime">Počet sekund od odeslání příspěvku, kdy ho může autor upravit/smazat:</label>
					<input type="text" class="form-control" id="authoredittime" name="authoredittime" value="' . $sys["authoredittime"] . '">
				</div>
			</div>
		</div>
		<div id="users" class="tab-pane fade">
			<div class="row col-lg-6 col-sm-9 col-xs-12">
				<div class="checkbox">
					<label><input type="checkbox" name="regallowed"' . parse_to_checkbox($sys["regallowed"]) . '> Povolit registraci</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="lostpass"' . parse_to_checkbox($sys["lostpass"]) . '> Povolit obnovu hesla</label>
				</div>
			</div>
		</div>
		<div id="security" class="tab-pane fade">
			<div class="row col-lg-6 col-sm-9 col-xs-12">
				<div class="checkbox">
					<label><input type="checkbox" name="stricthttps"' . parse_to_checkbox($sys["stricthttps"]) . '> Požadovat HTTPS</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="restrictorigin"' . parse_to_checkbox($sys["restrictorigin"]) . '> Skrýt referrer</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="twofactor_gauth"' . parse_to_checkbox($sys["twofactor_gauth"]) . '> Povolit dvojfaktorovou autentizaci (Google Authenticator)</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="twofactor_yubi"' . parse_to_checkbox($sys["twofactor_yubi"]) . '> Povolit dvojfaktorovou autentizaci (YubiKey)</label>
				</div>
				<div class="form-group">
					<label class="control-label" for="yubi_url">YubiKey autentizační server:</label>
					<input type="text" class="form-control" id="yubi_url" name="yubi_url" value="' . $sys["yubi_url"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="yubi_id">YubiKey ID:</label>
					<input type="text" class="form-control" id="yubi_id" name="yubi_id" value="' . $sys["yubi_id"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="yubi_key">YubiKey API klíč:</label>
					<input type="text" class="form-control" id="yubi_key" name="yubi_key" value="' . $sys["yubi_key"] . '">
				</div>
			</div>
		</div>
		<div id="design" class="tab-pane fade">
			<div class="row col-lg-6 col-sm-9 col-xs-12">
				<div class="form-group">
					<label class="control-label" for="slidewidth">Šířka posuvníku:</label>
					<input type="text" class="form-control" id="slidewidth" name="slidewidth" value="' . $sys["slidewidth"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="slidecolor">Barva posuvníku:</label>
					<input type="text" class="form-control" id="slidecolor" name="slidecolor" value="' . $sys["slidecolor"] . '">
				</div>
				' . $whitelabel . '
			</div>
		</div>
		<div id="license" class="tab-pane fade">
			<div class="row col-lg-6 col-sm-9 col-xs-12">
				' . $lic_notice . '
				<div class="checkbox">
					<label><input type="checkbox" name="licreload"> Při uložení obnovit licenci</label>
				</div>
			</div>
		</div>
		<div id="sys" class="tab-pane fade">
			<div class="row col-lg-6 col-sm-9 col-xs-12">
				<p>WebSys verze 1.0 <span class="text-danger">BETA</span></p>
				<p>Vytvořil Matyáš Koc</p>
				<p>Systém je povoleno využívat jen v souladu s licencí</p>
			</div>
		</div>
	</div>
	<input type="hidden" name="csrf" value="'. generate_csrf() .'">
</form>';

}