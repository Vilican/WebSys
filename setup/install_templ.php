<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Instalace WebSys</title>
<!--[if lt IE 9]>
    <script src="template/js/html5shiv.js"></script>
    <script src="template/js/respond.js"></script>
    <![endif]-->
<link href="../template/css/theme.css" rel="stylesheet">
</head>
<body>
<div class="navbar" role="navigation">
<div class="header">
<noscript><div class="alert alert-danger txt-center"><strong>Nemáte zapnutý JavaScript. Aby stránky správně fungovaly, musíte si ho zapnout. <a href="http://www.enable-javascript.com/cz/" target="_blank" rel="noopener">Návod k zapnutí >></a></strong></div></noscript>
<h1 class="container">Instalace WebSys</h1>
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

</ul>
</div>
</div>
</div>
<div class="container">
<div class="row row-offcanvas row-offcanvas-right">
<div class="row">
<h2 class="notopmargin">WebSys verze 1.1 (Boreas)</h2>
</div>
<div class="row">
<form method="post">
<input type="submit" name="install" class="btn btn-warning" value="Čistá instalace (data v databázi se mohou smazat)">
<input type="submit" name="update-from10" class="btn btn-primary" value="Aktualizace z 1.0 (Ares)">
</form>
</div>
</div>
</div>
<script src="template/js/jquery.js"></script>
<script src="template/js/bootstrap.js"></script>
<script src="template/js/offcanvas.js"></script>
<?php if ($jspost) { echo '<script src="template/js/post.js"></script>'; } ?>
<?php if ($jsthread) { echo '<script src="template/js/thread.js"></script>'; } ?>
<?php if ($galery) { echo '<link rel="stylesheet" href="template/css/magnific.css" type="text/css" media="screen">
<script type="text/javascript" src="lib/laziestloader.js"></script>
<script type="text/javascript" src="template/js/magnific.js"></script>
<script type="text/javascript">$(".popup-gallery").ready(function(){$(".popup-gallery").magnificPopup({delegate: "a",type:"image",mainClass:"mfp-img-mobile",tLoading:"Načítání ...",gallery:{enabled:true,navigateByImgClick:true,preload: [0,1] }});});</script>
<script type="text/javascript">$(".gal-mini").laziestloader({threshold:200});</script>'; } ?>
</body>
</html>