<?php

do {
	
	if (!has_access("admin_content")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu';
	
	if (isset($_POST["type"])) {
		header("Location: admin.php?p=content-new-page&type=". santise($_POST["type"]));
		die();
	}
	
	foreach ($page_types as $id => $label) {
		$options .= '<option value="'. $id .'">'. $label .'</option>';
	}
	
	$page["content"] .= '<ul class="nav nav-inpage nav-tabs">';

	if (has_access("admin_content_edit") or has_access("admin_content_edit_all") or has_access("admin_content_changeeditor") or has_access("admin_content_delete") or has_access("admin_content_delete_all")) {
		$page["content"] .= '<li><a data-toggle="tab" href="#pages">Stránky</a></li>';
		$pgedit = true;
	}
	
	if (has_access("admin_content_articles_edit") or has_access("admin_content_articles_edit_all") or has_access("admin_content_articles_changeeditor") or has_access("admin_content_articles_delete") or has_access("admin_content_articles_delete_all")) {
		$page["content"] .= '<li><a data-toggle="tab" href="#articles">Články</a></li>';
		$artedit = true;
	}

	if (has_access("admin_content_boxes")) {
		if (isset($_GET["boxes"])) {
			$li_boxes = ' class="active"';
			$div_boxes = ' in active';
		}
		$page["content"] .= '<li'. $li_boxes .'><a data-toggle="tab" href="#boxes">Boxy</a></li>';
		$boxedit = true;
	}

	if (has_access("admin_content_upload")) {
		if (isset($_GET["files"])) {
			$li_files = ' class="active"';
			$div_files = ' in active';
		}
		$page["content"] .= '<li'. $li_files .'><a data-toggle="tab" href="#upload">Nahrát</a></li>';
		$upload = true;
	}
	
	if (has_access("admin_content_review")) {
		$page["content"] .= '<li><a data-toggle="tab" href="#review">Revize</a></li>';
		$rev = true;
	}
	
	$page["content"] .= '</ul><div class="tab-content">';
	
	if ($pgedit) {
		require "admin/require/content-pg.php";
	}
	
	if ($artedit) {
		require "admin/require/content-art.php";
	}
	
	if ($boxedit) {
		require "admin/require/content-box.php";
	}
	
	if ($upload) {
		require "admin/require/content-upload.php";
	}
	
	if ($rev) {
		require "admin/require/content-rev.php";
	}
	
	$page["content"] .= '</div>';
	
} while (0);