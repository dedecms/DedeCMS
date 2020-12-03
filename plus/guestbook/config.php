<?php 
$needFilter = true;
require(dirname(__FILE__)."/../../include/config_base.php");

if(empty($gotopagerank)) $gotopagerank="";
if($gotopagerank=="admin")
{
	require(dirname(__FILE__)."/../../include/inc_userlogin.php");
	$cuserLogin = new userLogin();
	CheckPurview('plus_留言簿模块');
}

//设置为 0,表示留言需要审核
//如果设置为 1 ,则留言不需要审核就能显示
if($cfg_feedbackcheck=='是') $needCheck = 0;
else $needCheck = 1;

function trimMsg($msg,$gtype=0)
{
	$msg = htmlspecialchars(trim($msg));
	if($gtype==1){
		$msg = nl2br($msg);
		$msg = str_replace("  ","&nbsp;&nbsp;",$msg);
	}
	return $msg;
}
?>