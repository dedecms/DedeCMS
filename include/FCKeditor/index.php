<?php
error_reporting(E_ALL);
include('fckeditor.php') ;
$sBasePath = $_SERVER['PHP_SELF'] ;
$fck = new FCKeditor('FCKeditor1') ;
$fck->BasePath	= '' ;
$fck->Width		= '100%' ;
$fck->Height		= "500" ;
$fck->ToolbarSet	= "Default" ;
$fck->Create() ;
?>