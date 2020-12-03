<?php 
require_once(dirname(__FILE__)."/fckeditor.php");
$fck = new FCKeditor("test");
$fck->BasePath		= '/dedecmsv3/include/fckeditor2/' ;
$fck->Width		= '100%' ;
$fck->Height		= "500" ;
$fck->ToolbarSet	= "Default" ;
$fck->Value = "" ;
$fck->Create();
?>