<?php

include("../../../wp-config.php");
//include("rbcommon.php");

$results_id = $_POST['results_div_id'];

$results = rbarticle_display_article(true);
$status = substr($results, 0, 1);
$content = addslashes(substr($results, 1));

if($status == 1)
	die("document.getElementById('$results_id').innerHTML = '$content'");
else
	die("var rbarticle_error = 1");

?>