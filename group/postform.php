<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: postform.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/config.php");
include_once DEDEGROUP."/global.inc.php";
$action = isset($action) ? trim($action) : '';
if(!$cfg_ml->IsLogin())
{
	ShowMsg("未登录前不充许该操作！","-1");
	exit();
}

if($id < 1)
{
	ShowMsg("错误,未定义的操作！","-1");
	exit();
}

if($cfg_group_click == 1)
{
	$row = $db->GetOne("SELECT * FROM #@__group_user WHERE gid='$id' AND uid='".$cfg_ml->M_ID."'");
	if(!is_array($row))
	{
		ShowMsg("错误,您还不能在该圈子发贴,请申请成为该圈子会员！","-1");
		exit();
	}else if($row['isjoin']==0)
	{
		ShowMsg("错误,您还没有通过审核,请联系圈主！","-1");
		exit();
	}
}

if($action=="save")
{
	$svali = GetCkVdValue();
	if(strtolower($vdcode)!=$svali || $svali=="")
	{
		ShowMsg("认证码错误！","-1");
		exit();
	}
	if(!isset($types))
	{
		$types = 0;
	}
	$types = ereg_replace("[^0-9]","",$types);
	if($types<1)
	{
		$types = 0;
	}
	$subject = cn_substrR(trim(HtmlReplace($subject,2)),80);
	if(strlen($subject)<3||strlen($subject)>80)
	{
		ShowMsg("主题字数应该在3-80个汉字！","-1");
		exit();
	}
	
	$threads = eregi_replace("<(iframe|script)","",$_POST['threads']);
	//V.5.0.0 for Groups max post threads words
	if(!isset($cfg_group_words))
	{
		$cfg_group_words = 1000;
	}
	if(strlen($threads)<3 || strlen($threads)>$cfg_group_words*2)
	{
		ShowMsg("主题内容字数应该在3-1000个汉字！","-1");
		exit();
	}
	if(ereg("$cfg_notallowstr",$subject)||ereg("$cfg_notallowstr",$threads))
	{
		ShowMsg("含有非法字符!.","-1");
		exit();
	}
	$subject = preg_replace("/$cfg_replacestr/","***",$subject);
	$threads = preg_replace("/$cfg_replacestr/","***",$threads);
	$userip = GetIP();
	$SetQuery = "INSERT INTO #@__group_threads(gid,smalltype,subject,author,authorid,dateline,lastpost,lastposter) ";
	$SetQuery .= "VALUES('$id','$types','$subject','".$cfg_ml->M_UserName."','".$cfg_ml->M_ID."','".time()."','".time()."','".$cfg_ml->M_UserName."');";
	if($db->ExecuteNoneQuery($SetQuery))
	{
		$tid = $db->GetLastID();
		$SetQuery = "INSERT INTO #@__group_posts(gid,tid,first,author,authorid,subject,dateline,message,useip) ";
		$SetQuery .= "VALUES('$id','$tid',1,'".$cfg_ml->M_UserName."','".$cfg_ml->M_ID."','$subject','".time()."','$threads','$userip');";
		if($db->ExecuteNoneQuery($SetQuery))
		{
			Upcountgroups($id);
			$uid = $cfg_ml->M_ID;
			Upcontuserpost($id,$uid,"post");
		}
		ShowMsg("成功发表一话题！","viewthread.php?id=$id&tid=$tid");
		exit();
	}
	else
	{
		echo $db->GetError();
		ShowMsg("出错了！","-1");
		exit();
	}
}
require_once(GROUP_TPL."/postform.html");
?>