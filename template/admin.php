<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Administrace | <?php echo $sys["title"]; ?></title>
<!--[if lt IE 9]>
    <script src="template/js/html5shiv.js"></script>
    <script src="template/js/respond.js"></script>
    <![endif]-->
<link href="template/css/theme.css" rel="stylesheet">
</head>
<body>
<div class="navbar navbar-fixed-top" role="navigation">
<div class="header">
<h1 class="container"><?php echo $sys["title"]; ?> - administrace</h1>
</div>
<div class="container navb">
<div class="navbar-header">
<button type="button" class="navbar-toggle bborder" data-toggle="collapse" data-target=".navbar-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
</div>
<div class="collapse navbar-collapse">
<ul class='nav navbar-nav'>
<?php echo admin_menu(); ?>
</ul>
</div>
</div>
</div>
<div class="container">
<div class="col-xs-12 col-sm-12 well">
<div class="row">
<h2 class="notopmargin"><?php echo $page["title"]; ?></h2>
</div>
<div class="row">
<?php echo $page["content"]; ?>
</div>
</div>
<hr class="ft">
<footer>
<p class="footer">
<?php if (!$unbranded) { echo 'Vytvořeno pomocí <a href="https://websys.sufix.cz">WebSys</a>'; } ?>
</p>
</footer>
</div>
<script src="template/js/jquery.js"></script>
<script src="template/js/bootstrap.js"></script>
<script src="template/js/offcanvas.js"></script>
<script src="template/js/nicescroll.js"></script>
<script>jQuery(document).ready(function() { jQuery('html').niceScroll({cursorcolor:'<?php echo $sys["slidecolor"]; ?>',cursorwidth: <?php echo $sys["slidewidth"]; ?>,zindex: 100}); });</script>
<?php if ($ckeditor) { echo '<script src="lib/ckeditor/ckeditor.js"></script>
<script>CKEDITOR.replace("content");</script>'; } ?>

</body>
</html>