<?php $backend_environment = TRUE; $ShowErr=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
if (!in_array($user['role'],$rolem)) {header("Location: /login?act=quit"); exit;}
/* ----------------------- ПАРАМЕТРЫ СТРАНИЦЫ ----------------------- */

# Переключение на другую базу данных
if (isset($_GET['fil'])) {
	$fil=$_GET['fil']; if (isset($countryid[$fil]) || $fil==-1) $_SESSION['fil']=$fil;
} 
$h="/a/staticlist"; if (isset($_GET['h'])) $h=$_GET['h'];
header("Location: ".$h); exit;
