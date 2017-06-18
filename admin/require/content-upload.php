<?php

$allowed = array("jpg", "jpeg", "png", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt", "rtf", "pdf");

foreach ($allowed as $format) {
	$formats .= $format . ", ";
}

$page["content"] .= '<div id="upload" class="tab-pane fade'. $div_files .'"><p>Tento modul umožňuje nahrávat soubory na server. Z důvodu omezení na některých serverech nelze nahrávat více souborů současně.</p><p>Toto nahrávátko je určeno koncovým editorům stránek a nehodí se pro nahrávání větších objemů dat. V takovém přípdadě použijte FTP.</p>
<br><p>Povolené formáty jsou: '. substr($formats, 0, -2) .'</p><p>Maximální podporovaná velikost: '. (max_file_upload() / 1048576) .' MB</p><br>';

do {
	
	if(ini_get('file_uploads') != 1) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Nahrávání souborů je na tomto serveru zakázáno zcela!</strong></div>';
	}
	
	$dirs = array_filter(glob('upload/*'), 'is_dir');
	$folders = '<option>upload/</option>';
	foreach ($dirs as $dir) {
		$folders .= '<option>'. $dir .'/</option>';
	}
	
	if (isset($_POST["submit"])) do {
		
		if (!in_array(substr($_POST["folder"], 0, -1), $dirs) and $_POST["folder"] != "upload/") {
			$page["content"] .= '<div class="alert alert-danger"><strong>Zvolena neexistující složka!</strong></div>';
			break;
		}
		
		$target_dir = "upload/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		
		if ($_FILES["fileToUpload"]["size"] > max_file_upload()) {
			$page["content"] .= '<div class="alert alert-danger"><strong>Soubor je moc velký!</strong></div>';
			break;
		}
		if ($_FILES["fileToUpload"]["size"] == 0) {
			$page["content"] .= '<div class="alert alert-danger"><strong>Soubor je nulový!</strong></div>';
			break;
		}
		if (file_exists($target_file)) {
			$page["content"] .= '<div class="alert alert-danger"><strong>Soubor už existuje!</strong></div>';
			break;
		}
		if(!in_array(pathinfo($target_file, PATHINFO_EXTENSION), $allowed)) {
			$page["content"] .= '<div class="alert alert-danger"><strong>Soubor má nepovolený typ!</strong></div>';
			break;
		}

		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			$page["content"] .= '<div class="alert alert-success"><strong>Soubor byl úspěšně nahrán</strong></div>';
		} else {
			$page["content"] .= '<div class="alert alert-danger"><strong>Nastal problém při ukládání souboru!</strong></div>';
		}
	
	} while (0);
	
	if (!isset($_GET["files"])) {
		$add = "&files";
	}
	$page["content"] .= '<form action="'. santise($_SERVER["REQUEST_URI"]) . $add .'" method="post" enctype="multipart/form-data"><table><tr><td>Zvolte soubor:</td><td><input type="file" name="fileToUpload" id="fileToUpload"></td></tr><tr><td>Zvolte složku:</td><td><select name="folder">'. $folders .'</select></td></tr><tr><td><input type="submit" value="Nahrát" name="submit"></td></tr></table></form>';
	
} while (0);

$page["content"] .= '</div>';