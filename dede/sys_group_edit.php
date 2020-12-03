<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Group');
$dsql = new DedeSql(false);
if(empty($dopost)) $dopost = "";
if($dopost=='save')
{
	if($rank==10){
		ShowMsg("超级管理员的权限不允许更改!","sys_group.php");
	  $dsql->Close();
	  exit();
	}
	$purview = "";
	if(is_array($purviews)){
	  foreach($purviews as $p){
		  $purview .= "$p ";
	  }
	  $purview = trim($purview);
  }
	$dsql->ExecuteNoneQuery("Update #@__admintype set typename='$typename',purviews='$purview' where rank='$rank'");
	$dsql->Close();
	ShowMsg("成功更改用户组的权限!","sys_group.php");
	exit();
}
else if($dopost=='del')
{
	$dsql->ExecuteNoneQuery("Delete From #@__admintype where rank='$rank' And system='0';");
  ShowMsg("成功删除一个用户组!","sys_group.php");
	$dsql->Close();
	exit();
}
$groupRanks = Array();
$groupSet = $dsql->GetOne("Select * From #@__admintype where rank='".$rank."'");
$groupRanks = explode(' ',$groupSet['purviews']);

//检查是否已经有此权限
function CRank($n){
	global $groupRanks;
	if(in_array($n,$groupRanks)) return ' checked';
	else  return '';
}

require_once(dirname(__FILE__)."/templets/sys_group_edit.htm");


ClearAllLink();
?>