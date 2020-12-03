<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: mygroup_notice.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/system/config.php");
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");
$menutype = 'mydede';

$gid = $id = isset($id) && is_numeric($id) ? $id : 0;
$action = isset($action) ? trim($action) : '';
if($id < 1)
{
	ShowMsg("含有非法操作!.","-1");
	exit();
}

//取出圈子信息
$_GROUPS = $row = $db->GetOne("SELECT uid,groupname FROM #@__groups WHERE groupid=".$id);
if(!is_array($row))
{
	ShowMsg("圈子不存在!","-1");
	exit();
}
if($cfg_ml->M_ID!=$row['uid'])
{
	ShowMsg("该圈子不在你的管辖范围内!","-1");
	exit();
}
$msg ='';
if($action=="edit")
{
	$notice = eregi_replace("<(iframe|script)","",$notice);
	$subject = cn_substrR(HtmlReplace($subject, 2),80);
	$userip = GetIP();
	if(empty($subject))
	{
		$msg = "请填写公告标题！";
	}
	else if(empty($notice)||CountStrLen($notice>100))
	{
		$msg = "请填写规定长度的公告内容！";
	}
	else
	{
		$SetQuery = "UPDATE #@__group_notice SET title='$subject',notice='$notice',stime='".time()."',ip='$userip' WHERE id='$nid';";
		$db->ExecuteNoneQuery($SetQuery);
		$msg = "已经更改公告!";
	}
	ShowMsg($msg, '');
}
else if($action=="add")
{
	$notice = eregi_replace("<(iframe|script)","",$notice);
	$subject = cn_substrR(HtmlReplace($subject,2),80);
	$userip = GetIP();
	if(empty($subject))
	{
		$msg = "请填写公告标题！";
	}
	else if(empty($notice)||CountStrLen($notice>100))
	{
		$msg = "请填写规定长度的公告内容！";
	}
	else
	{
		$SetQuery = "INSERT INTO #@__group_notice(uname,userid,title,notice,stime,gid,ip) VALUES('".$cfg_ml->M_UserName."','".$cfg_ml->M_ID."','".$subject."','".$notice."','".time()."','$id','".$userip."');";
		$db->ExecuteNoneQuery($SetQuery);
		$msg = "已经更改公告!";
	}
	ShowMsg($msg, '');
}

$SetQuery = '';

//公告信息
$row = $db->GetOne("SELECT `id`,`title`,`notice` FROM `#@__group_notice` WHERE `gid`=".$id);
if(is_array($row))
{
	$nid = $row['id'];
	$fromset = 'edit';
	$title = $row['title'];
	$notice = $row['notice'];
}
else
{
	$nid = 0;
	$fromset = 'add';
	$title = '';
	$notice = '';
}
unset($row);
require_once(_SYSTEM_."/mygroup_notice.htm");

function CountStrLen($str)
{
	global $cfg_soft_lang;
	if(strtolower(substr(trim($cfg_soft_lang),0,3)) == 'utf')
	{
		preg_match_all("/./su", $str, $m);
		return count($m[0]);
	}
	else
	{
		$ccLen=0;$ascLen=strlen($str);$ind=0;
		$hasCC=ereg("[xA1-xFE]",$str); //判断是否有汉字
		$hasAsc=ereg("[x01-xA0]",$str); //判断是否有ASCII字符
		if($hasCC && !$hasAsc)
		{
			return strlen($str)/2;//只有汉字的情况
		}
		if(!$hasCC && $hasAsc)
		{
			return strlen($str);//只有Ascii字符的情况
		}
		for($ind=0;$ind<$ascLen;$ind++)
		{
			if(ord(substr($str,$ind,1))>0xa0)
			{
				$ccLen++;
				$ind++;
			}
			else
			{
				$ccLen++;
			}
		}
		return $ccLen;
	}
}

?>