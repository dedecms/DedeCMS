<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: config.php,v 1.1 2009/08/04 04:07:29 blt Exp $
 */
$cfg_needFilter = true;
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC.'/memberlogin.class.php');
define('DEDEGROUP', DEDEROOT.'/group');
include_once DEDEGROUP.'/common.inc.php';
require_once(DEDEGROUP.'/language/group.zh.'.$cfg_soft_lang.'.inc');

//获得当前脚本名称，如果你的系统被禁用了$_SERVER变量，请自行更改这个选项
$dedeNowurl = "";
$s_scriptName="";
$dedeNowurl = GetCurUrl();
$dedeNowurls = explode("?",$dedeNowurl);
$s_scriptName = $dedeNowurls[0];
$cfg_ml = new MemberLogin();
$Honor = $cfg_ml->M_Honor;
if(empty($Honor))
{
	$Honor = "未授衔";
}

//计算中英文混合字符串的长度
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

//更新圈子统计
function Upcountgroups($id)
{
	global $db;
	$rs = $db->GetOne("SELECT * FROM #@__groups WHERE groupid='$id'");
	if(!is_array($rs))
	{
		return false;
	}

	//贴子数
	$threads = $db->GetOne("SELECT COUNT(*) as t FROM #@__group_threads WHERE gid='$id'");
	$threads = $threads['t'];

	//成员数
	$members = $db->GetOne("SELECT COUNT(*) as t FROM #@__group_user WHERE gid='$id'");
	$members = $members['t'];
	$db->ExecuteNoneQuery("UPDATE #@__groups SET threads='$threads',members='$members' WHERE groupid='$id';");
	return true;
}
//更新用户贴子统计
function Upcontuserpost($gid,$uid,$do='post')
{
	global $db;
	$rs = $db->GetOne("SELECT * FROM #@__groups WHERE groupid='$gid'");
	if(!is_array($rs))
	{
		return false;
	}
	$posts = "AND first='0'";
	if($do=="post")
	{
		$posts = " AND first='1'";
	}
	$threads = $db->GetOne("SELECT COUNT(*) as t FROM #@__group_posts WHERE gid='$gid' AND authorid='$uid' $posts");
	$threads = $threads['t'];
	$upfildes = "posts='$threads'";
	if($do=="replies")
	{
		$upfildes = "replies='$threads'";
	}
	$db->ExecuteNoneQuery("UPDATE #@__group_user SET $upfildes WHERE gid='$gid' AND uid='$uid';");
	return true;
}

//更新回复数目
function UpcontReplies($tid,$gid)
{
	global $db;
	$Replies = $db->GetOne("SELECT COUNT(*) as t FROM #@__group_posts WHERE gid='$gid' AND tid='$tid' AND first='0'");
	$Replies = $Replies['t'];
	$db->ExecuteNoneQuery("UPDATE #@__group_threads SET replies='{$Replies}' WHERE tid='$tid';");
	return true;
}

?>