<?php 
require_once(dirname(__FILE__)."/config.php");
if($cfg_ml->IsLogin()){
	ShowMsg("你已经登陆系统，无需注册新用户！","index.php");
	exit();
}
if($cfg_pp_isopen==1 && $cfg_pp_regurl!=''){
	 header("Location:{$cfg_pp_regurl}");
	 exit();
}
require_once(dirname(__FILE__)."/templets/reg_new.htm");
?>