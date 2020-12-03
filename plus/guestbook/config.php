<?
require(dirname(__FILE__)."/../../include/config_base.php");
if(empty($gotopagerank)) $gotopagerank="";
if($gotopagerank=="admin")
{
	require(dirname(__FILE__)."/../../include/inc_userlogin.php");
	$cuserLogin = new userLogin();
}

//设置为 0,表示留言需要审核
//如果设置为 1 ,则留言不需要审核就能显示
$needCheck = 0;

function trimMsg($msg,$gtype=0)
{
	$notallowstr="法轮|江泽民|她妈|它妈|他妈|你妈|fuck|去死|贱人|走光|偷拍|色情|激情|sex|操B";
	$msg = htmlspecialchars(trim($msg));
	if($gtype==1){
		$msg = nl2br($msg);
		$msg = str_replace("  ","&nbsp;&nbsp;",$msg);
	}
	$msg = eregi_replace($notallowstr,"***",$msg);
	return $msg;
}
?>