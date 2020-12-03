<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_SoftConfig');
if(empty($dopost))
{
	$dopost = '';
}

//保存
if($dopost=="save")
{
	$query = "UPDATE `#@__softconfig` SET
   		downtype = '$downtype' ,
   		gotojump='$gotojump' ,
   		ismoresite = '$ismoresite',
   		islocal = '$islocal',
   		sites = '$sites',
   		downmsg = '$downmsg' ";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg('成功保存参数！','');
}

//读取参数
$row = $dsql->GetOne("select * From `#@__softconfig` ");
if(!is_array($row))
{
	$dsql->ExecuteNoneQuery("INSERT INTO `#@__softconfig`(`downtype` , `ismoresite` ,`islocal`, `gotojump` , `sites` , `downmsg` )
	VALUES ('1', '0','1', '0', '' ,'$downmsg'); ");
	$row['downtype']   = 1;
	$row['ismoresite'] = 0;
	$row['islocal']    = 1;
	$row['gotojump']   = 0;
	$row['sites']      = '';
	$row['downmsg']    = '';
}
include DedeInclude('templets/soft_config.htm');

?>