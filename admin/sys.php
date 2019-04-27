<?php

if (!isset($_SESSION["id"]) or $_SESSION["id"] != 0) {
	
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
	
} else {

	require "require/sys-logic.php";
	
	$settings = $mysql->query("SELECT * FROM `settings`");
	while ($setting = $settings->fetch_assoc()) {
		$sys[$setting["setting"]] = $setting["value"];
	}
	
	if ($sys["license"] >= 1) {
        $lic_notice = '<div class="alert alert-success"><p><strong>Licencovaný systém (PRO)</strong></p><p>a) komerční použití je povoleno<br>b) zpětný odkaz není požadován</p></div>';
	} else {
        $lic_notice = '<div class="alert alert-warning"><p><strong>Nelicencovaný systém (FREE)</strong></p><p>a) pouze nekomerční použití<br>b) musí zobrazovat zpětný odkaz</p></div>';
    }

	if ($sys["license"] >= 2) {
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
					<label class="control-label" for="bodybackground">Barva pozadí:</label>
					<input type="text" class="form-control" id="bodybackground" name="bodybackground" value="' . $sys["bodybackground"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="bodytxtcolor">Barva textu:</label>
					<input type="text" class="form-control" id="bodytxtcolor" name="bodytxtcolor" value="' . $sys["bodytxtcolor"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="headercolortop">Horní barva přechodu:</label>
					<input type="text" class="form-control" id="headercolortop" name="headercolortop" value="' . $sys["headercolortop"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="headercolorbottom">Dolní barva přechodu:</label>
					<input type="text" class="form-control" id="headercolorbottom" name="headercolorbottom" value="' . $sys["headercolorbottom"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="navcolor">Barva nabídky:</label>
					<input type="text" class="form-control" id="navcolor" name="navcolor" value="' . $sys["navcolor"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="navtextcolor">Barva odkazů nabídky:</label>
					<input type="text" class="form-control" id="navtextcolor" name="navtextcolor" value="' . $sys["navtextcolor"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="navactivecolor">Barva aktivního odkazu nabídky:</label>
					<input type="text" class="form-control" id="navactivecolor" name="navactivecolor" value="' . $sys["navactivecolor"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="titlecolor">Barva nadpisů:</label>
					<input type="text" class="form-control" id="titlecolor" name="titlecolor" value="' . $sys["titlecolor"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="wellcolor">Pozadí rámečků:</label>
					<input type="text" class="form-control" id="wellcolor" name="wellcolor" value="' . $sys["wellcolor"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="wellborder">Barva ohraničení rámečků:</label>
					<input type="text" class="form-control" id="wellborder" name="wellborder" value="' . $sys["wellborder"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="hrcolor">Barva předělovacích čar:</label>
					<input type="text" class="form-control" id="hrcolor" name="hrcolor" value="' . $sys["hrcolor"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="submenucaretcolor">Barva šipky u podmenu:</label>
					<input type="text" class="form-control" id="submenucaretcolor" name="submenucaretcolor" value="' . $sys["submenucaretcolor"] . '">
				</div>
				<div class="form-group">
					<label class="control-label" for="slidewidth">Barva odkazů:</label>
					<input type="text" class="form-control" id="linkcolor" name="linkcolor" value="' . $sys["linkcolor"] . '">
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
			<div class="row col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">WebSys v1.1 Boreas <span class="label label-success">stable</span></div>
					<div class="panel-body">
						<p>Vytvořil Matyáš Koc</p>
						<p>Systém je povoleno využívat jen v souladu s <a href="https://websys.sufix.cz/index.php?p=lic">aktivní licencí</a></p>
					</div>
				</div>
				<p>Byly použity následující moduly:</p>
				<table class="table table-hover table-hover table-bordered table-responsive">
					<thead>
						<tr>
							<th>Jméno modulu</th>
							<th>Autor</th>
							<th>Licence</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Bootstrap</td>
							<td>Twitter, Inc.</td>
							<td>MIT</td>
						</tr>
						<tr>
							<td>CKEditor</td>
							<td>CKSource</td>
							<td>MPL</td>
						</tr>
						<tr>
							<td>HTMLDiff</td>
							<td>Rashid Mohamad</td>
							<td>GPL v2.0</td>
						</tr>
						<tr>
							<td>HTML5 Shiv</td>
							<td>Alexander Farkas</td>
							<td>MIT</td>
						</tr>
						<tr>
							<td>jQuery</td>
							<td>JS Foundation</td>
							<td>MIT</td>
						</tr>
						<tr>
							<td>LaziestLoader</td>
							<td>Josh Williams</td>
							<td>MIT</td>
						</tr>
						<tr>
							<td>Magnific Popup</td>
							<td>Dmitry Semenov</td>
							<td>MIT</td>
						</tr>
						<tr>
							<td>PHPGangsta_GoogleAuthenticator</td>
							<td>Michael Kliewe</td>
							<td>MIT</td>
						</tr>
						<tr>
							<td>PHPMailer</td>
							<td>Marcus Bointon</td>
							<td>LGPL</td>
						</tr>
						<tr>
							<td>Respond.js</td>
							<td>Scott Jehl</td>
							<td>MIT</td>
						</tr>
						<tr>
							<td>YubiAuth</td>
							<td>Matyáš Koc</td>
							<td>CC BY-NC-SA (MIT pro WebSys)</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<input type="hidden" name="csrf" value="'. generate_csrf() .'">
</form>';

}