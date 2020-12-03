<?
session_start();
require_once("../dede/config_base.php");
require_once("../dede/inc_typelink.php");
function getFileName($stime,$ID,$typedir,$rank)
{
	$tl = new typeLink();
	return $tl->getFileName($ID,$typedir,$stime,$rank);
}
//--检查用户是否有权限-----
function CheckUser($rank)
{
	if($rank>1)
	{
		if(isset($_COOKIE["cookie_rank"]))
		{
			if($_COOKIE["cookie_rank"]>=$rank) return 1;
		}
		else if(session_is_registered("dede_admin_id"))
			return 1;
		else
			return 0;
	}
	else
	{
		return 1;
	}	
}
?>