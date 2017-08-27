<?php

do {
	
	if (!has_access("admin_roles")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
		
	$page["title"] = 'Správa skupin - přidání';
	
	if (isset($_POST["submit"])) {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message .= "Nesouhlasí CSRF token - to může znamenat pokus o útok!<br>";
			$err = true;
		}
		
		if (empty($_POST["rolename"])) {
			$message .= "Název role je prázdný!<br>";
			$err = true;
		}

		if (!is_numeric($_POST["level"]) or $_POST["level"] < 1 or $_POST["level"] > 1000) {
			$message .= "Úroveň není číslo 1 až 1000!<br>";
			$err = true;
		}
		
		if (empty($_POST["color"])) {
			$message .= "Barva je povinná!<br>";
			$err = true;
		}
		
		if ($err) {
			$message = '<div class="alert alert-danger"><p><strong>Při úpravě došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
		} else {
			
			if (parse_from_checkbox($_POST["reviewdel"]) or parse_from_checkbox($_POST["reviewflags"]) or parse_from_checkbox($_POST["reviewart"])) {
				$review_access = "1";
			} else {
				$review_access = "0";
			}
			
			$mysql->query("INSERT INTO `roles`(
			`rolename`,
			`level`,
			`color`,
			`access_addpost`,
			`access_flag`,
			`access_nocaptcha`,
			`access_posts_edit`,
			`access_posts_delete`,
			`access_posts_delete_permanent`,
			`access_posts_showip`,
			`access_thread_create`,
			`access_thread_edit`,
			`access_thread_delete`,
			`access_admin`,
			`access_admin_content`,
			`access_admin_content_edit`,
			`access_admin_content_edit_all`,
			`access_admin_content_changeeditor`,
			`access_admin_content_delete`,
			`access_admin_content_delete_all`,
			`access_admin_content_sethome`,
			`access_admin_content_articles_edit`,
			`access_admin_content_articles_edit_all`,
			`access_admin_content_articles_edit_autoapprove`,
			`access_admin_content_articles_edit_approved`,
			`access_admin_content_articles_changeeditor`,
			`access_admin_content_articles_delete`,
			`access_admin_content_articles_delete_all`,
			`access_admin_content_boxes`,
			`access_admin_content_upload`,
			`access_admin_content_review`,
			`access_admin_content_review_deleted`,
			`access_admin_content_review_flags`,
			`access_admin_content_review_articles`,
			`access_admin_users`,
			`access_admin_users_view`,
			`access_admin_users_add`,
			`access_admin_users_edit`,
			`access_admin_users_pass`,
			`access_admin_users_delete`,
			`access_admin_roles`
			) VALUES (
			". $mysql->quote($_POST["rolename"]) .",	
			". $mysql->quote($_POST["level"]) .",	
			". $mysql->quote($_POST["color"]) .",	
			". parse_from_checkbox($_POST["addposts"]) .",	
			". parse_from_checkbox($_POST["flagposts"]) .",	
			". parse_from_checkbox($_POST["nocaptcha"]) .",
			". parse_from_checkbox($_POST["editposts"]) .",
			". parse_from_checkbox($_POST["deleteposts"]) .",
			". parse_from_checkbox($_POST["removeposts"]) .",
			". parse_from_checkbox($_POST["seeips"]) .",
			". parse_from_checkbox($_POST["createtopic"]) .",
			". parse_from_checkbox($_POST["edittopics"]) .",	
			". parse_from_checkbox($_POST["deletetopics"]) .",
			". parse_from_checkbox($_POST["enteradmin"]) .",	
			". parse_from_checkbox($_POST["entercontent"]) .",
			". parse_from_checkbox($_POST["editpages"]) .",
			". parse_from_checkbox($_POST["editallpages"]) .",
			". parse_from_checkbox($_POST["changeeditor"]) .",
			". parse_from_checkbox($_POST["deletepages"]) .",
			". parse_from_checkbox($_POST["deleteallpages"]) .",
			". parse_from_checkbox($_POST["sethome"]) .",
			". parse_from_checkbox($_POST["editart"]) .",
			". parse_from_checkbox($_POST["editallart"]) .",
			". parse_from_checkbox($_POST["autoapprove"]) .",
			". parse_from_checkbox($_POST["editapproved"]) .",
			". parse_from_checkbox($_POST["changeartauthor"]) .",
			". parse_from_checkbox($_POST["deleteart"]) .",
			". parse_from_checkbox($_POST["deleteallart"]) .",
			". parse_from_checkbox($_POST["boxes"]) .",
			". parse_from_checkbox($_POST["upload"]) .",
			". $review_access .",
			". parse_from_checkbox($_POST["reviewdel"]) .",
			". parse_from_checkbox($_POST["reviewflags"]) .",
			". parse_from_checkbox($_POST["reviewart"]) .",
			". parse_from_checkbox($_POST["enterusers"]) .",
			". parse_from_checkbox($_POST["seeuser"]) .",
			". parse_from_checkbox($_POST["adduser"]) .",
			". parse_from_checkbox($_POST["edituser"]) .",
			". parse_from_checkbox($_POST["resetpass"]) .",
			". parse_from_checkbox($_POST["deluser"]) .",
			". parse_from_checkbox($_POST["admingroups"]) ."
			)");
			$groupid = $mysql->insert_id();
			header("Location: admin.php?p=groups-edit&id=". $groupid);
			die();
			
		}
		
	}
	
	$page["content"] .= $message .'<form method="post"><table style="border-spacing:10px">
	
	<tr><td colspan="2"><span class="label label-success">Nastavení skupiny</span></td></tr>
	<tr><td>Název skupiny:</td><td><input type="text" name="rolename" value="'. santise($_POST["rolename"]) .'" class="form-control"></td></tr>
	<tr><td>Úroveň:</td><td><input type="text" name="level" value="'. santise($_POST["level"]) .'" class="form-control"> (1 až 1000)</td></tr>
	<tr><td>Barva:</td><td><input type="text" name="color" value="'. santise($_POST["color"]) .'" class="form-control"></td></tr>
	
	<tr><td colspan="2"><span class="label label-success">Standartní oprávnění</span></td></tr>
	<tr><td>Může přidávat příspěvky:</td><td><input type="checkbox" name="addposts" class="form-control" style="width:8%"></td></tr>
	<tr><td>Může nahlašovat příspěvky:</td><td><input type="checkbox" name="flagposts" class="form-control" style="width:8%"></td></tr>
	<tr><td>Vytvářet témata:</td><td><input type="checkbox" name="createtopic" class="form-control" style="width:8%"></td></tr>
	
	<tr><td colspan="2"><span class="label label-warning">Moderační oprávnění</span></td></tr>
	<tr><td>Nevyžadovat CAPTCHA:</td><td><input type="checkbox" name="nocaptcha" class="form-control" style="width:8%"></td></tr>
	<tr><td>Upravovat příspěvky:</td><td><input type="checkbox" name="editposts" class="form-control" style="width:8%"></td></tr>
	<tr><td>Mazat příspěvky:</td><td><input type="checkbox" name="deleteposts" class="form-control" style="width:8%"></td></tr>
	<tr><td>Odstraňovat příspěvky:</td><td><input type="checkbox" name="removeposts" class="form-control" style="width:8%"></td></tr>
	<tr><td>Vidět IP adresy:</td><td><input type="checkbox" name="seeips" class="form-control" style="width:8%"></td></tr>
	<tr><td>Upravovat témata:</td><td><input type="checkbox" name="edittopics" class="form-control" style="width:8%"></td></tr>
	<tr><td>Mazat témata:</td><td><input type="checkbox" name="deletetopics" class="form-control" style="width:8%"></td></tr>
	
	<tr><td colspan="2"><span class="label label-warning">Editorská oprávnění</span></td></tr>
	<tr><td>Vstup do administrace:</td><td><input type="checkbox" name="enteradmin" class="form-control" style="width:8%"></td></tr>
	<tr><td>Vstup do správy obsahu:</td><td><input type="checkbox" name="entercontent" class="form-control" style="width:8%"></td></tr>
	<tr><td>Editovat své stránky:</td><td><input type="checkbox" name="editpages" class="form-control" style="width:8%"></td></tr>
	<tr><td>Mazat své stránky:</td><td><input type="checkbox" name="deletepages" class="form-control" style="width:8%"></td></tr>
	<tr><td>Editovat své články:</td><td><input type="checkbox" name="editart" class="form-control" style="width:8%"></td></tr>
	<tr><td>Nevyžadovat schválení článků:</td><td><input type="checkbox" name="autoapprove" class="form-control" style="width:8%"></td></tr>
	<tr><td>Editovat své schválené články:</td><td><input type="checkbox" name="editapproved" class="form-control" style="width:8%"></td></tr>
	<tr><td>Mazat své články:</td><td><input type="checkbox" name="deleteart" class="form-control" style="width:8%"></td></tr>
	<tr><td>Nahrávat soubory:</td><td><input type="checkbox" name="upload" class="form-control" style="width:8%"></td></tr>
	
	<tr><td colspan="2"><span class="label label-warning">Správcovská oprávnění</span></td></tr>
	<tr><td>Editovat všechny stránky:</td><td><input type="checkbox" name="editallpages" class="form-control" style="width:8%"></td></tr>
	<tr><td>Mazat všechny stránky:</td><td><input type="checkbox" name="deleteallpages" class="form-control" style="width:8%"></td></tr>
	<tr><td>Měnit editora stránky:</td><td><input type="checkbox" name="changeeditor" class="form-control" style="width:8%"></td></tr>
	<tr><td>Editovat všechny články:</td><td><input type="checkbox" name="editallart" class="form-control" style="width:8%"></td></tr>
	<tr><td>Mazat všechny stránky:</td><td><input type="checkbox" name="deleteallart" class="form-control" style="width:8%"></td></tr>
	<tr><td>Měnit autora článku:</td><td><input type="checkbox" name="changeartauthor" class="form-control" style="width:8%"></td></tr>
	<tr><td>Nastavit hlavní stránku:</td><td><input type="checkbox" name="sethome" class="form-control" style="width:8%"></td></tr>
	<tr><td>Upravovat boxy:</td><td><input type="checkbox" name="boxes" class="form-control" style="width:8%"></td></tr>
	<tr><td>Revidovat smazané příspěvky:</td><td><input type="checkbox" name="reviewdel" class="form-control" style="width:8%"></td></tr>
	<tr><td>Revidovat nahlášené příspěvky:</td><td><input type="checkbox" name="reviewflags" class="form-control" style="width:8%"></td></tr>
	<tr><td>Schvalovat články:</td><td><input type="checkbox" name="reviewart" class="form-control" style="width:8%"></td></tr>
	<tr><td>Vstup do správy uživatelů:</td><td><input type="checkbox" name="enterusers" class="form-control" style="width:8%"></td></tr>
	<tr><td>Přidat uživatele:</td><td><input type="checkbox" name="adduser" class="form-control" style="width:8%"></td></tr>
	<tr><td>Detail uživatele:</td><td><input type="checkbox" name="seeuser" class="form-control" style="width:8%"></td></tr>
	<tr><td>Upravit uživatele:</td><td><input type="checkbox" name="edituser" class="form-control" style="width:8%"></td></tr>
	
	<tr><td colspan="2"><span class="label label-danger">Administrační oprávnění</span></td></tr>
	<tr><td>Smazat uživatele:</td><td><input type="checkbox" name="deluser" class="form-control" style="width:8%"></td></tr>
	<tr><td>Změnit heslo uživatele:</td><td><input type="checkbox" name="resetpass" class="form-control" style="width:8%"></td></tr>
	<tr><td>Spravovat skupiny a práva:</td><td><input type="checkbox" name="admingroups" class="form-control" style="width:8%"></td></tr>

	<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Uložit změny" class="btn btn-default"></td></tr>
	</table><input type="hidden" name="csrf" value="'. generate_csrf() .'"></form>';
	
} while (0);