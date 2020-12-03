<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
if(empty($dopost))
{
	$dopost = '';
}

if($dopost=='save')
{
	$validate = isset($validate) ? strtolower(trim($validate)) : '';
	$svali = GetCkVdValue();
	if($validate=='' || $validate!=$svali)
	{
		ShowMsg('验证码不正确!','-1');
		exit();
	}
	$msg = htmlspecialchars($msg);
	$email = htmlspecialchars($email);
	$webname = htmlspecialchars($webname);
	$url = htmlspecialchars($url);
	$logo = htmlspecialchars($logo);
	$typeid = intval($typeid);
	$dtime = time();
	$query = "Insert Into `#@__flink`(sortrank,url,webname,logo,msg,email,typeid,dtime,ischeck)
                    Values('50','$url','$webname','$logo','$msg','$email','$typeid','$dtime','0')";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg('成功增加一个链接，但需要审核后才能显示!','-1',1);
}

//显示模板(简单PHP文件)
include_once($cfg_basedir.$cfg_templets_dir."/plus/flink-list.htm");

?>