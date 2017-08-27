<?php

do {
	
	if (!has_access("admin_roles")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
		
	$page["title"] = 'Správa skupin - úprava';
	
	$role = $mysql->query("SELECT * FROM `roles` WHERE `role_id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($role->num_rows == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Tato role neexistuje!</strong></div>';
		break;
	}
	
	$role = $role->fetch_assoc();
	
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
			
			$mysql->query("UPDATE `roles` SET
			`rolename` = ". $mysql->quote($_POST["rolename"]) .",
			`level` = ". $mysql->quote($_POST["level"]) .",
			`color` = ". $mysql->quote($_POST["color"]) .",
			`access_addpost` = ". parse_from_checkbox($_POST["addposts"]) .",
			`access_flag` = ". parse_from_checkbox($_POST["flagposts"]) .",
			`access_nocaptcha` = ". parse_from_checkbox($_POST["nocaptcha"]) .",
			`access_posts_edit` = ". parse_from_checkbox($_POST["editposts"]) .",
			`access_posts_delete` = ". parse_from_checkbox($_POST["deleteposts"]) .",
			`access_posts_delete_permanent` = ". parse_from_checkbox($_POST["removeposts"]) .",
			`access_posts_showip` = ". parse_from_checkbox($_POST["seeips"]) .",
			`access_thread_create` = ". parse_from_checkbox($_POST["createtopic"]) .",
			`access_thread_edit` = ". parse_from_checkbox($_POST["edittopics"]) .",
			`access_thread_delete` = ". parse_from_checkbox($_POST["deletetopics"]) .",
			`access_admin` = ". parse_from_checkbox($_POST["enteradmin"]) .",
			`access_admin_content` = ". parse_from_checkbox($_POST["entercontent"]) .",
			`access_admin_content_edit` = ". parse_from_checkbox($_POST["editpages"]) .",
			`access_admin_content_edit_all` = ". parse_from_checkbox($_POST["editallpages"]) .",
			`access_admin_content_changeeditor` = ". parse_from_checkbox($_POST["changeeditor"]) .",
			`access_admin_content_delete` = ". parse_from_checkbox($_POST["deletepages"]) .",
			`access_admin_content_delete_all` = ". parse_from_checkbox($_POST["deleteallpages"]) .",
			`access_admin_content_sethome` = ". parse_from_checkbox($_POST["sethome"]) .",
			`access_admin_content_articles_edit` = ". parse_from_checkbox($_POST["editart"]) .",
			`access_admin_content_articles_edit_all` = ". parse_from_checkbox($_POST["editallart"]) .",
			`access_admin_content_articles_edit_autoapprove` = ". parse_from_checkbox($_POST["autoapprove"]) .",
			`access_admin_content_articles_edit_approved` = ". parse_from_checkbox($_POST["editapproved"]) .",
			`access_admin_content_articles_changeeditor` = ". parse_from_checkbox($_POST["changeartauthor"]) .",
			`access_admin_content_articles_delete` = ". parse_from_checkbox($_POST["deleteart"]) .",
			`access_admin_content_articles_delete_all` = ". parse_from_checkbox($_POST["deleteallart"]) .",
			`access_admin_content_boxes` = ". parse_from_checkbox($_POST["boxes"]) .",
			`access_admin_content_upload` = ". parse_from_checkbox($_POST["upload"]) .",
			`access_admin_content_review` = ". $review_access .",
			`access_admin_content_review_deleted` = ". parse_from_checkbox($_POST["reviewdel"]) .",
			`access_admin_content_review_flags` = ". parse_from_checkbox($_POST["reviewflags"]) .",
			`access_admin_content_review_articles` = ". parse_from_checkbox($_POST["reviewart"]) .",
			`access_admin_users` = ". parse_from_checkbox($_POST["enterusers"]) .",
			`access_admin_users_view` = ". parse_from_checkbox($_POST["seeuser"]) .",
			`access_admin_users_add` = ". parse_from_checkbox($_POST["adduser"]) .",
			`access_admin_users_edit` = ". parse_from_checkbox($_POST["edituser"]) .",
			`access_admin_users_pass` = ". parse_from_checkbox($_POST["resetpass"]) .",
			`access_admin_users_delete` = ". parse_from_checkbox($_POST["deluser"]) .",
			`access_admin_roles` = ". parse_from_checkbox($_POST["admingroups"]) ."
			WHERE `role_id` = ". $mysql->quote($_GET["id"]) .";");

			header("Location: admin.php?p=groups-edit&id=". santise($_GET["id"]));
			die();
			
		}
		
	}
	
	$page["content"] .= $message .'<form method="post"><table style="border-spacing:10px">
	
	<tr><td colspan="2"><span class="label label-success">Nastavení skupiny</span></td></tr>
	<tr><td>Název skupiny:</td><td><input type="text" name="rolename" value="'. restore_value($role["rolename"] , santise($_POST["rolename"])) .'" class="form-control"></td></tr>
	<tr><td>Úroveň:</td><td><input type="text" name="level" value="'. restore_value($role["level"], santise($_POST["level"])) .'" class="form-control"> (1 až 1000)</td></tr>
	<tr><td>Barva:</td><td><input type="text" name="color" value="'. restore_value($role["color"], santise($_POST["color"])) .'" class="form-control"></td></tr>
	
	<tr><td colspan="2"><span class="label label-success">Standartní oprávnění</span></td></tr>
	<tr><td>Může přidávat příspěvky:</td><td><input type="checkbox" name="addposts" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_addpost"]) .'></td></tr>
	<tr><td>Může nahlašovat příspěvky:</td><td><input type="checkbox" name="flagposts" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_flag"]) .'></td></tr>
	<tr><td>Vytvářet témata:</td><td><input type="checkbox" name="createtopic" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_thread_create"]) .'></td></tr>
	
	<tr><td colspan="2"><span class="label label-warning">Moderační oprávnění</span></td></tr>
	<tr><td>Nevyžadovat CAPTCHA:</td><td><input type="checkbox" name="nocaptcha" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_nocaptcha"]) .'></td></tr>
	<tr><td>Upravovat příspěvky:</td><td><input type="checkbox" name="editposts" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_posts_edit"]) .'></td></tr>
	<tr><td>Mazat příspěvky:</td><td><input type="checkbox" name="deleteposts" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_posts_delete"]) .'></td></tr>
	<tr><td>Odstraňovat příspěvky:</td><td><input type="checkbox" name="removeposts" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_posts_delete_permanent"]) .'></td></tr>
	<tr><td>Vidět IP adresy:</td><td><input type="checkbox" name="seeips" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_posts_showip"]) .'></td></tr>
	<tr><td>Upravovat témata:</td><td><input type="checkbox" name="edittopics" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_thread_edit"]) .'></td></tr>
	<tr><td>Mazat témata:</td><td><input type="checkbox" name="deletetopics" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_thread_delete"]) .'></td></tr>
	
	<tr><td colspan="2"><span class="label label-warning">Editorská oprávnění</span></td></tr>
	<tr><td>Vstup do administrace:</td><td><input type="checkbox" name="enteradmin" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin"]) .'></td></tr>
	<tr><td>Vstup do správy obsahu:</td><td><input type="checkbox" name="entercontent" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content"]) .'></td></tr>
	<tr><td>Editovat své stránky:</td><td><input type="checkbox" name="editpages" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_edit"]) .'></td></tr>
	<tr><td>Mazat své stránky:</td><td><input type="checkbox" name="deletepages" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_delete"]) .'></td></tr>
	<tr><td>Editovat své články:</td><td><input type="checkbox" name="editart" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_articles_edit"]) .'></td></tr>
	<tr><td>Nevyžadovat schválení článků:</td><td><input type="checkbox" name="autoapprove" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_articles_edit_autoapprove"]) .'></td></tr>
	<tr><td>Editovat své schválené články:</td><td><input type="checkbox" name="editapproved" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_articles_edit_approved"]) .'></td></tr>
	<tr><td>Mazat své články:</td><td><input type="checkbox" name="deleteart" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_articles_delete"]) .'></td></tr>
	<tr><td>Nahrávat soubory:</td><td><input type="checkbox" name="upload" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_upload"]) .'></td></tr>
	
	<tr><td colspan="2"><span class="label label-warning">Správcovská oprávnění</span></td></tr>
	<tr><td>Editovat všechny stránky:</td><td><input type="checkbox" name="editallpages" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_edit_all"]) .'></td></tr>
	<tr><td>Mazat všechny stránky:</td><td><input type="checkbox" name="deleteallpages" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_delete_all"]) .'></td></tr>
	<tr><td>Měnit editora stránky:</td><td><input type="checkbox" name="changeeditor" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_changeeditor"]) .'></td></tr>
	<tr><td>Editovat všechny články:</td><td><input type="checkbox" name="editallart" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_articles_edit_all"]) .'></td></tr>
	<tr><td>Mazat všechny stránky:</td><td><input type="checkbox" name="deleteallart" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_articles_delete_all"]) .'></td></tr>
	<tr><td>Měnit autora článku:</td><td><input type="checkbox" name="changeartauthor" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_articles_changeeditor"]) .'></td></tr>
	<tr><td>Nastavit hlavní stránku:</td><td><input type="checkbox" name="sethome" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_sethome"]) .'></td></tr>
	<tr><td>Upravovat boxy:</td><td><input type="checkbox" name="boxes" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_boxes"]) .'></td></tr>
	<tr><td>Revidovat smazané příspěvky:</td><td><input type="checkbox" name="reviewdel" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_review_deleted"]) .'></td></tr>
	<tr><td>Revidovat nahlášené příspěvky:</td><td><input type="checkbox" name="reviewflags" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_review_flags"]) .'></td></tr>
	<tr><td>Schvalovat články:</td><td><input type="checkbox" name="reviewart" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_content_review_articles"]) .'></td></tr>
	<tr><td>Vstup do správy uživatelů:</td><td><input type="checkbox" name="enterusers" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_users"]) .'></td></tr>
	<tr><td>Přidat uživatele:</td><td><input type="checkbox" name="adduser" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_users_add"]) .'></td></tr>
	<tr><td>Detail uživatele:</td><td><input type="checkbox" name="seeuser" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_users_view"]) .'></td></tr>
	<tr><td>Upravit uživatele:</td><td><input type="checkbox" name="edituser" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_users_edit"]) .'></td></tr>
	
	<tr><td colspan="2"><span class="label label-danger">Administrační oprávnění</span></td></tr>
	<tr><td>Smazat uživatele:</td><td><input type="checkbox" name="deluser" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_users_delete"]) .'></td></tr>
	<tr><td>Změnit heslo uživatele:</td><td><input type="checkbox" name="resetpass" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_users_pass"]) .'></td></tr>
	<tr><td>Spravovat skupiny a práva:</td><td><input type="checkbox" name="admingroups" class="form-control" style="width:8%"'. parse_to_checkbox($role["access_admin_roles"]) .'></td></tr>

	<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Uložit změny" class="btn btn-default"></td></tr>
	</table><input type="hidden" name="csrf" value="'. generate_csrf() .'"></form>';
	
} while (0);