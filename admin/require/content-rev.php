<?php

$page["content"] .= '<div id="review" class="tab-pane fade"><p>Jsou k dispozici tyto revizní fronty:</p>';

if (has_access("admin_content_review_deleted")) {
	
	$post_count = $mysql->query("SELECT `post_id` FROM `posts` WHERE `deleted` = 1;")->num_rows;
	
	if ($post_count > 0) {
		$badge = ' <span class="label label-danger">'. $post_count .'</span>';
		$btn_type = 'warning';
	} else {
		$badge = null;
		$btn_type = 'primary';
	}
	
	$page["content"] .= '<p><a href="admin.php?p=content-review-deleted" class="btn btn-'. $btn_type .'">Smazané příspěvky '. $badge .'</a></p>';
}

if (has_access("admin_content_review_flags")) {
	
	$flag_count = $mysql->query("SELECT DISTINCT(`post`) FROM `flags`;")->num_rows;
	
	if ($flag_count > 0) {
		$badge = ' <span class="label label-danger">'. $flag_count .'</span>';
		$btn_type = 'warning';
	} else {
		$badge = null;
		$btn_type = 'primary';
	}
	
	$page["content"] .= '<p><a href="admin.php?p=content-review-flags" class="btn btn-'. $btn_type .'">Nahlášené příspěvky '. $badge .'</a></p>';
}

if (has_access("admin_content_review_articles")) {
	
	$art_count = $mysql->query("SELECT `articles`.`id` FROM `articles` WHERE `approved` = 0;")->num_rows;
	
	if ($art_count > 0) {
		$badge = ' <span class="label label-danger">'. $art_count .'</span>';
		$btn_type = 'warning';
	} else {
		$badge = null;
		$btn_type = 'primary';
	}
	
	$page["content"] .= '<p><a href="admin.php?p=content-review-articles" class="btn btn-'. $btn_type .'">Neschválené články '. $badge .'</a></p>';
}
	
$page["content"] .= '</div>';