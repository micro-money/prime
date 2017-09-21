<?php

/*    */

$content = ob_get_contents(); //      
ob_end_clean(); //   
//header("Content-Type: text/html; charset=UTF-8");

//   
//dd($content);
e( $page['template'], $content );
//print_r($page);
mysql_close(); die();