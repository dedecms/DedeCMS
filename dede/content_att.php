<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Att');
if(empty($dopost))
{
	$dopost = "";
}

//保存更改
if($dopost=="save")
{
	$startID = 1;
	$endID = $idend;
	for(;$startID<=$endID;$startID++)
	{
		$query = "";
		$att = ${"att_".$startID};
		$attname = ${"attname_".$startID};
		$query = "update `#@__arcatt` set attname='$attname' where att='$att'";
		$dsql->ExecuteNoneQuery($query);
	}
	echo "<script> alert('成功更新自定文档义属性表！'); </script>";
}
include DedeInclude('templets/content_att.htm');

?>