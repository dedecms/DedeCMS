<?php 
require_once(dirname(__FILE__)."/config.php");

if($cfg_ml->M_ID>0){
	if(!empty($gourl)) ShowMsg("你已经登录，请不要重复登录！",$gourl);
	else ShowMsg("你已经登录，请不要重复登录！","-1");
	exit();
}

if($cfg_pp_isopen==1 && $cfg_pp_loginurl!=''){
	header("Location:{$cfg_pp_loginurl}");
	exit();
}
require_once(dirname(__FILE__)."/templets/login.htm");
if(isset($dsql) && $dsql) $dsql->Close();
?>