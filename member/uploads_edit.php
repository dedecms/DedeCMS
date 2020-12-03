<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");
$aid = isset($aid) && is_numeric($aid) ? $aid : 0;
if(empty($dopost))
{
	$dopost = '';
}
$arow = $dsql->GetOne("Select * From `#@__uploads` where aid='$aid ';");
if(!is_array($arow))
{
		ShowMsg('附件不存在', '-1');
		exit();
}
if($arow['mid']!=$cfg_ml->M_ID)
{
		ShowMsg("你没有修改这个附件的权限！","-1");
		exit();
}
if($dopost=='')
{
	include(DEDEMEMBER."/templets/uploads_edit.htm");
	exit();
}
else if($dopost=='save')
{
	$title = HtmlReplace($title,2);
	if($mediatype==1) $utype = 'image';
	else if($mediatype==2)
	{
		$utype = 'flash';
	}
	else if($mediatype==3)
	{
		$utype = 'media';
	}
	else
	{
		$utype = 'addon';
	}
	$title = HtmlReplace($title,2);
	$exname = ereg_replace("(.*)/","",$oldurl);
	$exname = ereg_replace("\.(.*)$","",$exname);
	if( !ereg("^".$cfg_user_dir."/".$cfg_ml->M_ID."/", $oldurl) ) { exit('没权限！'); }
	$filename = MemberUploads('addonfile',$oldurl,$cfg_ml->M_ID,$utype,$exname,-1,-1,true);
	SaveUploadInfo($title,$filename,$mediatype);
	ShowMsg("成功修改文件！","uploads_edit.php?aid=$aid");
}
?>