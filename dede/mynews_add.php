<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('plus_站内新闻发布');
if(empty($dopost))
{
	$dopost = "";
}
if($dopost=="save")
{
	$dtime = GetMkTime($sdate);
	$query = "Insert Into `#@__mynews`(title,writer,senddate,body)
	 Values('$title','$writer','$dtime','$body')";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("成功发布一条站内新闻！","mynews_main.php");
	exit();
}
include DedeInclude('templets/mynews_add.htm');

?>