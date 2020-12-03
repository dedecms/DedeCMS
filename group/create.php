<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: create.php,v 1.1 2009/08/04 04:07:29 blt Exp $
 */

require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/oxwindow.class.php");
require_once(DEDEINC."/image.func.php");
require_once(DEDEMEMBER.'/inc/inc_archives_functions.php');

$action = isset($action) ? trim($action) : '';

if(!$cfg_ml->IsLogin())
{
	ShowMsg("你尚未登录或已经超时！",$cfg_member_dir."/login.php?gourl=".urlencode(GetCurUrl()));
	exit();
}
if(!isset($cfg_group_creators))
{
	$cfg_group_creators = 0; //积分条件全局
}
if(!isset($cfg_group_max))
{
	$cfg_group_max = 0;			 //用户可建圈子数全局
}

//对积分要求
if($cfg_ml->M_Scores < $cfg_group_creators)
{
	ShowMsg("积分小于{$cfg_group_creators}!还没达到创建圈子积分条件.","-1");
	exit();
}

//声明连接数据数据类
$rs = $db->GetOne("SELECT COUNT(*) AS c FROM #@__groups WHERE uid='".$cfg_ml->M_ID."'");
if( $rs['c'] >= $cfg_group_max && $cfg_group_max > 0)
{
	ShowMsg("超过创建圈子最大数{$cfg_group_max}个!.","-1");
	exit();
}
$title = "创建圈子";

/*------------
function SaveGroupinfo();
--------------*/
if($action=="save")
{
 	$groupname = cn_substrR($groupname,16);
	if(strlen($groupname)<2||strlen($groupname)>20)
	{
		ShowMsg("圈子名称过短!,在2-8个字内.","-1");
		exit();
	}
	$storeid = ereg_replace("[^0-9]","",$store);
	$description = cn_substrR($des,100);
	if(strlen($description)<5||strlen($description)>200)
	{
		ShowMsg("圈子说明在5-100个字符范围内!.","-1");
		exit();
	}
	$row = $db->GetOne("SELECT tops FROM #@__store_groups WHERE storeid='{$storeid}'");
	if($row['tops'] >0 )
	{
		$rootstoreid = $row['tops'];
	}
	else
	{
		$rootstoreid = $storeid;
	}

	//处理上传的缩略图
	$litpic = MemberUploads('litpic','',$cfg_ml->M_ID,'image','',100,70,false);
	if($litpic!='')
	{
		SaveUploadInfo($title,$litpic,1);
	}

	$smalltype = 0;
	$SetQuery = "INSERT INTO #@__groups(uid,groupname,des,groupimg,rootstoreid,storeid,creater,ismaster,smalltype,stime) ";
	$SetQuery .= "VALUES('".$cfg_ml->M_ID."','".$groupname."','".$des."','".$litpic."','$rootstoreid','$storeid','".$cfg_ml->M_UserName."','".$cfg_ml->M_UserName."','".$smalltype."','".time()."');";
	if($db->ExecuteNoneQuery($SetQuery))
	{
		$id = $db->GetLastID();
		if($rootstoreid == $storeid)
		{
			$db->ExecuteNoneQuery("UPDATE #@__store_groups SET nums=nums+1 WHERE storeid='$rootstoreid';");
		}
		else
		{
			$db->ExecuteNoneQuery("UPDATE #@__store_groups SET nums=nums+1 WHERE storeid='$rootstoreid';");
			$db->ExecuteNoneQuery("UPDATE #@__store_groups SET nums=nums+1 WHERE storeid='$storeid';");
		}
		$SetQuery = "INSERT INTO #@__group_user(uid,username,gid,jointime,isjoin) VALUES('".$cfg_ml->M_ID."','".$cfg_ml->M_UserName."','$id','".time()."',1);";
		if($db->ExecuteNoneQuery($SetQuery))
		{
			Upcountgroups($id);
		}
		ShowMsg("成功创建圈子！","group.php?id={$id}");
		exit();
	}
	else
	{
		echo $db->GetError();
	}
}

//类目递归
$db->SetQuery("SELECT * FROM #@__store_groups WHERE tops=0 ORDER BY orders ASC");
$db->Execute(1);
$option = '';
while($rs = $db->GetArray(1))
{
	$option .= "<option value='".$rs['storeid']."'>".$rs['storename']."</option>\n";
	$v = $rs['storeid'];
	$db->SetQuery("SELECT * FROM #@__store_groups WHERE tops='{$v}' ORDER BY orders ASC");
	$db->Execute(2);
	while($rs = $db->GetArray(2))
	{
		$option .= "<option value='".$rs['storeid']."'>--".$rs['storename']."</option>\n";
	}
}
require_once(DEDEGROUP."/templets/create.htm");

?>