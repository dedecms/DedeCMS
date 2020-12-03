<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('temp_One');
if(empty($dopost))
{
	$dopost = "";
}
if($dopost=="save")
{
	require_once(DEDEINC."/arc.partview.class.php");
	$uptime = time();
	$body = str_replace('&quot;','\\"',$body);
	$filename = ereg_replace("^/","",$nfilename);
	if($likeid=='')
	{
		$likeid = $likeidsel;
	}
	$row = $dsql->GetOne("Select filename From `#@__sgpage` where likeid='$likeid' And filename like '$filename' ");
	if(is_array($row))
	{
		ShowMsg("已经存在相同的文件名，请更改为其它文件名！","-1");
		exit();
	}
	$inQuery = "Insert Into `#@__sgpage`(title,keywords,description,template,likeid,ismake,filename,uptime,body)
	 Values('$title','$keywords','$description','$template','$likeid','$ismake','$filename','$uptime','$body'); ";
	if(!$dsql->ExecuteNoneQuery($inQuery))
	{
		ShowMsg("增加页面失败，请检内容是否有问题！","-1");
		exit();
	}
	$id = $dsql->GetLastID();
	include_once(DEDEINC."/arc.sgpage.class.php");
	$sg = new sgpage($id);
	$sg->SaveToHtml();
	ShowMsg("成功增加一个页面！","templets_one.php");
	exit();
}
$row = $dsql->GetOne("Select max(aid) as aid From `#@__sgpage`  ");
$nowid = is_array($row) ? $row['aid']+1 : '';
include_once(DEDEADMIN."/templets/templets_one_add.htm");

?>