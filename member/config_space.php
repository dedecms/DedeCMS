<?php 
$cfg_needFilter = true;
require_once(dirname(__FILE__)."/../include/config_base.php");

$cfg_egstr = "[\|\"\s\*\.\?\(\)\$ ;,'%]";

//检查是否开放会员功能
if($cfg_mb_open=='N'){
	ShowMsg("系统关闭了会员功能，因此你无法访问此页面！","javascript:;");
	exit();
}
//积分对应头衔
function Gethonor($scores){
	global $dsql;
	if(!isset($dsql) || !is_object($dsql)) $dsql = new DedeSql(false);
	$rs = $dsql->GetOne("Select titles,icon From #@__scores where integral<={$scores} order by integral desc");
	if(is_array($rs)) return $rs['titles']."#|".$rs['icon'];
	else return "未授衔#|1";
}
?>