<?php

if (!$_SESSION["access_admin_content"] > 0) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
} else {

$page["title"] = 'Difftest';

require "lib/diff.php";

$string_old = '<!DOCTYPE html>
<html>
<body>

<div>
  <h2>London</h2>
  <h2>London</h2>
  <p>London is the capital city of England. It is the most populous city in the Kingdom, with a metropolitan area of over 13 million inhabitants.</p>
  <p>Standing on the River Thames, London has been a major settlement for two millennia, its history going back to its founding by the Romans, who named it Londinium.</p>
</div> 

</body>
</html>';

$string_new = '<!DOCTYPE html>
<html>
<body>

<div>
  <h2>London</h2>
  <p>London is the capital city of England. It is the most populous city in the United, with a metropolitan area of over 13 million inhabitants.</p>
  <p>Standing on the River Thames, London has been a major settlement for two</p>
  <p>Standing on the River Thames, London has been a major settlement for two millennia, its history going back to its founding by the Romans, who named it Londinium.</p>
</div> 

</body>
</html>';

$diff = new HtmlDiff($string_old, $string_new);
$diff->build();

$page["content"] .= $diff->getDifference();

} ?>