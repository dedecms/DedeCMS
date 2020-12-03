<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
$dsql = new DedeSql(false);
$action = (empty($action) ? '' : $action);
if($action=='')
{
  $row = $dsql->GetOne("Select * From `#@__task`");
  if(!is_array($row))
  {
  	$ks = explode(',','usermtools,rmpwd,tasks,typeid,startid,endid,nodes,dotime,degree');
  	foreach($ks as $k) $row[$k] = '';
  	$row['dotime'] = '02:30:00';
  	$row['usermtools'] = '1';
  }
  require_once(dirname(__FILE__)."/templets/makehtml_task.htm");
  $dsql->Close();
  exit();
}
else if($action=='save')
{
	if(!is_array($tasks)){
		ShowMsg("你没选择需要操作的任务！","-1");
	  exit();
	}
	if(empty($rmpwd)){
		ShowMsg("远程管理密码不能为空！","-1");
	  exit();
	}
	if(eregi("[^0-9a-z@!]",$rmpwd)){
		ShowMsg("远程管理密码只能由 a-z 0-9 ! @ # 几种字符组成！","-1");
	  exit();
	}
	if(empty($startid)) $startid = 0;
	if(empty($endid)) $endid = 0;
	if(empty($typeid)) $typeid = 0;
	$dsql->ExecuteNoneQuery("Delete From `#@__task`");
	$taskss = '';
	if(is_array($tasks)) foreach($tasks as $v) $taskss .= ($taskss=='' ? $v : ','.$v);
	$inQuery = "Insert Into `#@__task` ( `id` , `usermtools` , `rmpwd` , `tasks` , `typeid` , `startid` , `endid` , `nodes` , `dotime` , `degree` ) 
                            VALUES ('1','$usermtools','$rmpwd','$taskss','$typeid','$startid','$endid','$nodes','$dotime','$degree' ) ;
  ";
  $dsql->ExecuteNoneQuery($inQuery);
	ShowMsg("成功更新计划任务配置！","makehtml_task.php");
	exit();
}
?>