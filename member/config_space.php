<?
$needFilter = true;
require_once(dirname(__FILE__)."/../include/config_base.php");

//检查是否开放会员功能
//-------------------------------

if($cfg_mb_open=='否'){
	ShowMsg("系统关闭了会员功能，因此你无法访问此页面！","javascript:;");
	exit();
}

?>