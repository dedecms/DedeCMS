<?php 
require_once(dirname(__FILE__)."/config.php");
if($cfg_pp_isopen==1 && $cfg_pp_loginurl!=''){
	header("Location:{$cfg_pp_loginurl}");
	exit();
}
require_once(dirname(__FILE__)."/templets/login.htm");
if(isset($dsql) && is_object($dsql)) $dsql->Close();
?>