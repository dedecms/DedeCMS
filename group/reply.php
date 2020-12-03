<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: reply.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/config.php");
include_once DEDEGROUP."/global.inc.php";

$tid = isset($tid) && is_numeric($tid) ? $tid : 0;
$pid = isset($pid) && is_numeric($pid) ? $pid : 0;
$action = isset($action) ? trim($action) : '';
$do = isset($do) ? trim($do) : '';
$message = isset($message) ? trim($message) : '';
if(!$cfg_ml->IsLogin())
{
	ShowMsg("未登录前不充许该操作！","-1");
	exit();
}
if($id < 1 || $tid < 1)
{
	ShowMsg("错误,未定义的操作！","-1");
	exit();
}
if($cfg_group_click==1)
{
	$row = $db->GetOne("SELECT * FROM #@__group_user WHERE gid='$id' AND uid='".$cfg_ml->M_ID."' AND isjoin='1'");
	if(!is_array($row))
	{
		ShowMsg("错误,您不是该圈子成员或未通过审核！","-1");
		exit();
	}
}
//贴子内容信息
$row = $db->GetOne("SELECT * FROM #@__group_threads WHERE tid='$tid'");
$subjects = $row['subject'];
$typeids = $row['smalltype'];
$uid = 0;
if($pid > 0)
{
	$row = $db->GetOne("SELECT * FROM #@__group_posts WHERE pid='$pid'");
	if(!isset($row['tid']))
	{
		ShowMsg("错误,记录不存在！","-1");
		exit();
	}
	$tid = $row['tid'];
	$first   = $row['first'];
	$message = $row['message'];
	$uid = $row['authorid'];
}

$message = preg_replace("'<div style=\"color:#ccc;\" id=\"lastedit\">.*?</div>'is","",$message);
if($action=="save")
{
	$svali = GetCkVdValue();
	if(strtolower($vdcode)!=$svali || $svali=="")
	{
		ShowMsg("认证码错误！","-1");
		exit();
	}
	
	if($pid > 0)
	{
		ShowMsg("发帖不充许回复！","-1");
		exit();
	}
	
	if(!isset($_POST['subject']))
	{
		$subject = '';
	}
	$subject = cn_substrR(trim(HtmlReplace($_POST['subject'],2)),80);
	$threads = eregi_replace("<(iframe|script)","",$threads);

	//V.5.0.0 for Groups max post threads words
	if(!isset($cfg_group_words))
	{
		$cfg_group_words = 1000;
	}
	if(CountStrLen($threads)<3||CountStrLen($threads)>$cfg_group_words)
	{
		ShowMsg("主题内容字数应该在3-1000个汉字！","-1");
		exit();
	}
	if(ereg("$cfg_notallowstr",$subject)||ereg("$cfg_notallowstr",$threads))
	{
		ShowMsg("含有非法字符!","-1");
		exit();
	}
	$subject = preg_replace("/$cfg_replacestr/","***",$subject);
	$threads = preg_replace("/$cfg_replacestr/","***",$threads);
	$threads = str_replace('\n','<br>',$threads);
	$threads = preg_replace("'<div style=\"color:#ccc;\" id=\"lastedit\">.*?</div>'is","",$threads);
	$userip = GetIP();
	$SetQuery = "INSERT INTO #@__group_posts(gid,tid,first,author,authorid,subject,dateline,message,useip) ";
	$SetQuery .= "VALUES('$id','$tid',0,'".$cfg_ml->M_UserName."','".$cfg_ml->M_ID."','$subject','".time()."','$threads','$userip');";
	if($db->ExecuteNoneQuery($SetQuery))
	{
		$uid = $cfg_ml->M_ID;
		$db->ExecuteNoneQuery("UPDATE #@__group_threads SET lastpost='".time()."',lastposter='".$cfg_ml->M_UserName."',replies=replies+1 WHERE tid='$tid';");
		Upcontuserpost($id,$uid,"replies");
		UpcontReplies($tid,$id);
	}
	ShowMsg("成功添加回复话题！","viewthread.php?id=$id&tid=$tid");
	exit();
}
else if($action=="edit" && ($ismaster||$cfg_ml->M_ID==$uid))
{
	$svali = GetCkVdValue();
	if(strtolower($vdcode)!=$svali || $svali=="")
	{
		ShowMsg("认证码错误！","-1");
		exit();
	}
	$subject = cn_substrR(trim(HtmlReplace($subject, 2)),80);
	if(CountStrLen($subject)>80)
	{
		ShowMsg("主题字数应该在3-80个汉字！","-1");
		exit();
	}
	$threads = cn_substrR(eregi_replace("<(iframe|script)","",$threads),2000);
	if(CountStrLen($threads)<3||CountStrLen($threads)>$cfg_group_words)
	{
		ShowMsg("主题内容字数应该在3-{$cfg_group_words}个汉字！","-1");
		exit();
	}
	if(empty($threads))
	{
		$threads = $message;
	}
	if(ereg("$cfg_notallowstr",$subject)||ereg("$cfg_notallowstr",$threads))
	{

		ShowMsg("含有非法字符!.","-1");
		exit();
	}
	if($first)
	{
		$types = ereg_replace("[^0-9]","",$types);
		if($types<1)
		{
			$types = 0;
		}
		if(strlen($subject)<3)
		{
			ShowMsg("主题字数应该在3-80个汉字！","-1");
			exit();
		}
	}
	$subject = preg_replace("/$cfg_replacestr/","***",$subject);
	$threads = preg_replace("/$cfg_replacestr/","***",$threads);
	$threads .= "<div style=\"color:#ccc;\" id=\"lastedit\">最后由:".$cfg_ml->M_UserName."编辑过.时间:".date('Y-m-d h:i:s',time())."</div>";
	$userip = GetIP();
	if($first)
	{
		$db->ExecuteNoneQuery("UPDATE #@__group_threads SET smalltype='$types',subject='$subject' WHERE tid='$tid';");
	}
	$db->ExecuteNoneQuery("UPDATE #@__group_posts SET subject='$subject',message='$threads' WHERE pid='$pid';");

	ShowMsg("成功修改贴子！","viewthread.php?id=$id&tid=$tid");
	exit();
}
if($do=="form")
{
	require_once(GROUP_TPL.'/reply.html');
}
else if($do=="edit")
{
	require_once(GROUP_TPL.'/reply_edit.html');
}
?>