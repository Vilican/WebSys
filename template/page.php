<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?php echo $page["description"]; ?>">
<meta name="author" content="<?php echo $sys["author"]; ?>">
<?php if (!$unbranded) { echo '<meta name="generator" content="WebSys">
'; }?>
<title><?php echo $page["title"]; ?> | <?php echo $sys["title"]; ?></title>
<!--[if lt IE 9]>
    <script src="template/js/html5shiv.js"></script>
    <script src="template/js/respond.js"></script>
    <![endif]-->
<link href="template/css/theme.css" rel="stylesheet">
<style>body{background:<?php echo $sys["bodybackground"]; ?>;color:<?php echo $sys["bodytxtcolor"]; ?>}.header{background:linear-gradient(180deg, <?php echo $sys["headercolortop"]; ?>, <?php echo $sys["headercolorbottom"]; ?>)}h1,h2{color:<?php echo $sys["titlecolor"]; ?> !important}.navb .container, .navbar{background:<?php echo $sys["navcolor"]; ?>}.dropdown-menu, .dropdown-menu>li>a:focus, .dropdown-menu>li>a:hover{background-color:<?php echo $sys["navcolor"]; ?>}hr{border-top: 1px solid <?php echo $sys["hrcolor"]; ?>}.well{background-color:<?php echo $sys["wellcolor"]; ?>;border: 1px solid <?php echo $sys["wellborder"]; ?> !important}.nav>li>a{color:<?php echo $sys["navtextcolor"]; ?>}.nav .caret, .nav .caret a:hover{border-top-color: <?php echo $sys["navtextcolor"]; ?> !important;border-bottom-color: <?php echo $sys["navtextcolor"]; ?> !important}.dropdown-menu:before{border-bottom: 5px solid <?php echo $sys["submenucaretcolor"]; ?>}.act{color:<?php echo $sys["navactivecolor"]; ?> !important}</style>
</head>
<body>
<div class="navbar" role="navigation">
<div class="header">
<noscript><div class="alert alert-danger txt-center"><strong>Nemáte zapnutý JavaScript. Aby stránky správně fungovaly, musíte si ho zapnout. <a href="http://www.enable-javascript.com/cz/" target="_blank" rel="noopener">Návod k zapnutí >></a></strong></div></noscript>
<?php if (!file_exists("template/img/header.png")) { echo '<h1 class="container">'. $sys["title"] . '</h1>'; } else { echo '<img class="header-image" src="template/img/header.png" alt="'. $sys["title"] .'">'; } ?>
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
<?php echo menu(); ?>
</ul>
</div>
</div>
</div>
<div class="container">
<div class="row row-offcanvas row-offcanvas-right">
<div class="col-xs-12 col-sm-9 well">
<div class="row">
<h2 class="notopmargin"><?php echo $page["title"]; ?></h2>
</div>
<div class="row">
<?php echo $page["content"]; ?>
</div>
</div>
<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
<?php echo boxes(); ?>
</div>
</div>
<hr class="ft">
<footer>
<p class="footer">
Vytvořil <?php echo $sys["author"]; ?><?php if (!$unbranded) { echo ' pomocí <a href="https://websys.sufix.cz">WebSys</a>'; }?>
</p>
</footer>
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