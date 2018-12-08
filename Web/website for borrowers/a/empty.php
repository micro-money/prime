<?php 
$page=$GLOBALS['page']; $dr=$GLOBALS['dr'];
#print_r($_GET);
$mhead="Nothing was found."; if (isset($_GET['id']))  $mhead.=" Try to select <a href='/a/f/chdb?fil=-1&h=".$_SERVER['REQUEST_URI']."'>all countries</a> for search.";

/* -------------------------- ДОКУМЕНТ НЕ НАЙДЕН ------------ */ ob_start(); ?>
	<div class="container-fluid" style="margin-top: -20px;">
		<h2><?= $mhead ?></h2>
	</div>
<?php require $dr . '/coreold/render_view.php';