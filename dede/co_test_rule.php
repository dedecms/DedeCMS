<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_collection.php");
if($nid=="") 
{
	ShowMsg("参数无效!","-1");	
	exit();
}
$co = new DedeCollection();
$co->Init();
$co->LoadFromDB($nid);
require_once(dirname(__FILE__)."/templets/co_test_rule.htm");
$co->Close();

ClearAllLink();
?>