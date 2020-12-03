<?php
if(!defined('DEDEINC') || !isset($id) && !is_numeric($id)) exit("403 Forbidden!");
if($id < 1)
{
	ShowMsg("错误,未定义的操作！","-1");
	exit();
}
$rs = $_GROUPS = $db->GetOne("SELECT * FROM #@__groups WHERE groupid='$id'");
if(!is_array($rs))
{
	ShowMsg("圈子不存在,或被删除！","-1");
	exit();
}
else if($rs['ishidden'])
{
	ShowMsg("圈子被管理员屏蔽中！","-1");
	exit();
}

$row = $db->GetOne("SELECT storename FROM #@__store_groups WHERE storeid='$rs[storeid]'");

$_GROUPS['hidden'] = $_GROUPS['ishidden'] ? '私有' : '公开';
$_GROUPS['store_name'] = is_array($row) ? $row['storename'] : '所有';
$_GROUPS['time'] = MyDate('Y-m-d',$_GROUPS['stime']);
$_GROUPS['topics'] = $_GROUPS['members'];
$_GROUPS['views'] = $_GROUPS['albums'] = $_GROUPS['threads'] = $_GROUPS['groupArticle'] = $_GROUPS['members'] = $_GROUPS['guestbook'] = $_GROUPS['admins'] = array();

$row = $db->GetOne("SELECT face,uname,userid FROM #@__member WHERE mid='$rs[uid]'");
if(is_array($row))
{
	$_GROUPS['face'] = $row['face'];
	$_GROUPS['uname'] = $row['uname'];
	$_GROUPS['userid'] = $row['userid'];
}

$row = $db->GetOne("SELECT creater FROM #@__groups WHERE groupid='$id' AND uid=".$cfg_ml->M_ID);
if(is_array($row))
{
	if($row['creater']!=$cfg_ml->M_UserName)
	{
		$db->ExecuteNoneQuery("UPDATE #@__groups SET creater='".$cfg_ml->M_UserName."' WHERE groupid='$id';");
	}
	unset($row);
}
$db->ExecuteNoneQuery("UPDATE #@__groups SET hits=hits+1 WHERE groupid='$id';");
$title = $rs['groupname'];
$icon  = $rs['groupimg'];
if(!$icon)
{
	$icon = "images/group_mainlist00.gif";
}
$des = $rs['des'];
if(empty($des))
{
	$des = "本圈还没有介绍说明.";
}

$userid	= $rs['uid'];
$hits    = $rs['hits'];
$master = $rs['ismaster'];
$creater = $rs['creater'];
//成员数
$members = $rs['members'];

//检测管理员
$ismaster = 0;
$masters    = @explode(",",$master);
$ismaster	 = in_array($cfg_ml->M_UserName,$masters);
if(empty($cfg_ml->M_UserName))
{
	$ismaster = 0;
}
if($userid == $cfg_ml->M_ID)
{
	$ismaster = 1;
}
$_temps = array();
foreach($masters as $k)
{
	$row = $db->GetOne("SELECT face,uname,userid,mid FROM #@__member WHERE userid='$k'");
	if(is_array($row))
	{
		$_GROUPS['admins'][] = $row;
		$_temps[] = $row['mid'];
	}
}
$_GROUPS['admin_ids'] = !empty($_temps) ? implode(",", $_temps) : 0;

//圈子成员
$mids = array();
$db->SetQuery("SELECT G.posts,G.replies,G.jointime,G.isjoin,M.face,M.uname,M.userid,M.mid FROM #@__group_user AS G LEFT JOIN #@__member AS M ON G.uid=M.mid WHERE G.gid='$id' AND G.isjoin=1 ORDER BY G.jointime DESC LIMIT 0,20");
$db->Execute();
while($row = $db->GetArray())
{
	$mids[] = $row['mid'];
	$row['face'] = empty($row['face']) ? 'images/common/noavatar.gif' : $row['face'];
	$_GROUPS['members'][] = $row;
}
$_GROUPS['_vars']['mids'] = implode(",", $mids);
$_GROUPS['_vars']['mids'] = empty($_GROUPS['_vars']['mids']) ? 0 : $_GROUPS['_vars']['mids'];

//圈子留言
$db->SetQuery("SELECT G.message,G.stime,G.title,M.userid,M.uname,M.face FROM #@__group_guestbook AS G LEFT JOIN #@__member AS M ON G.userid=M.mid WHERE G.gid='$id' ORDER BY G.stime DESC LIMIT 0,5");
$db->Execute();
while($row = $db->GetArray())
{
	$row['face'] = empty($row['face']) ? 'images/common/noavatar.gif' : $row['face'];
	$row['status'] = in_array($row['userid'],$masters) ? '<span style="color:red;">管理员</span>' : '普通成员';
	$_GROUPS['guestbook'][] = $row;
}
//圈子文章
include_once DEDEINC.'/channelunit.func.php';
$db->SetQuery("SELECT A.*,TP.typedir,TP.typename,TP.isdefault,TP.defaultname,TP.namerule,TP.namerule2,TP.ispart,TP.moresite,TP.siteurl,TP.sitepath 
FROM `#@__archives` as A LEFT JOIN `#@__arctype` TP ON A.typeid=TP.id WHERE A.mid IN({$_GROUPS['_vars']['mids']}) AND channel=1 ORDER BY A.pubdate DESC LIMIT 0,6");
$db->Execute();
while($row = $db->GetArray())
{	$row['arcurl'] = GetFileUrl($row['id'],$row['typeid'],$row['senddate'],$row['title'],$row['ismake'],
		$row['arcrank'],$row['namerule'],$row['typedir'],$row['money'],$row['filename'],$row['moresite'],$row['siteurl'],$row['sitepath']);
		
	$_GROUPS['groupArticle'][] = $row; 
}
//print_r ($_GROUPS['groupArticle']);die;
//圈子主题
$db->SetQuery("SELECT T.*,M.userid,M.uname FROM #@__group_threads AS T LEFT JOIN #@__member AS M ON T.authorid=M.mid WHERE T.gid='$id' AND T.closed=0 ORDER BY T.displayorder DESC, T.lastpost DESC LIMIT 0,15");
$db->Execute('threads');
while($row = $db->GetArray('threads'))
{
	$row['subject'] = cn_substr($row['subject'], 40);
	if($row['displayorder']) $row['subject'] .= "<img src=\"images/common/top.gif\" border=\"0\" align=\"absmiddle\" /> ";
	if($row['digest']) $row['subject'] .= "<img src=\"images/common/best.gif\" border=\"0\" align=\"absmiddle\" /> ";
	if($row['replies']>10) $row['subject'] .= "<img src=\"images/common/hot.gif\" border=\"0\" align=\"absmiddle\" /> ";	
	$temps = $db->GetOne("SELECT `name` FROM `#@__group_smalltypes` WHERE id='$row[smalltype]'");
	$row['type'] = is_array($temps) ? '['.$temps['name'].']' : '';
	$_GROUPS['threads'][] = $row;
}

//圈子图集
include_once DEDEINC.'/channelunit.func.php';
$db->SetQuery("SELECT arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath 
	FROM `#@__archives` arc LEFT JOIN `#@__arctype` tp ON arc.typeid=tp.id WHERE mid IN({$_GROUPS['_vars']['mids']}) AND channel=2 ORDER BY id DESC LIMIT 0,6");
$db->Execute();
while($row = $db->GetArray())
{
	$row['arcurl'] = GetFileUrl($row['id'],$row['typeid'],$row['senddate'],$row['title'],$row['ismake'],
		$row['arcrank'],$row['namerule'],$row['typedir'],$row['money'],$row['filename'],$row['moresite'],$row['siteurl'],$row['sitepath']);
	$row['litpic'] = empty($row['litpic']) ? 'images/common/defaultpic.gif' : $row['litpic'];
	$_GROUPS['albums'][] = $row;
}

//还常去
$db->SetQuery("SELECT G.groupname,G.groupid,G.des,G.groupimg FROM #@__group_user AS U LEFT JOIN #@__groups AS G ON U.gid=G.groupid WHERE U.uid IN({$_GROUPS['_vars']['mids']}) AND U.isjoin=1 AND U.gid<>$id LIMIT 0,6");
$db->Execute();
while($row = $db->GetArray())
{
	$row['groupimg'] = empty($row['groupimg']) ? 'images/common/defaultpic.gif' : $row['groupimg'];
	$_GROUPS['views'][] = $row;
}

$smalltype = ($rs['smalltype']) ? $rs['smalltype'] : 0;

//获得公告信息
$rs = $db->GetOne("SELECT uname,notice FROM #@__group_notice WHERE gid='$id' ORDER BY stime DESC");
if(!is_array($rs))
{
	$notice = "系统:本圈刚开张,大家多发贴.送人鲜花 手有余香,让我们为公益事业加油!";
}
else
{
	$notice = $rs['uname']."：".$rs['notice'];
}

//话题分类
$typeid = isset($typeid) && is_numeric($typeid) ? $typeid : 0;
$_COMMON['smalltypes'] = array();
if($smalltype)
{
	$temps = @explode(",",$smalltype);
	if(is_array($temps))
	{
		$temps = @array_filter($temps);
		$temps = @implode(",",$temps);
	}
	else
	{
		$temps = 0;
	}
	$temps = empty($temps) ? 0 : $temps;
	$db->SetQuery("SELECT `id`,`name` FROM `#@__group_smalltypes` WHERE `userid`=".$_GROUPS['uid']." AND `id` IN(".$temps.") ORDER BY `disorder` ASC LIMIT 0,6");
	$db->Execute();
	while($row = $db->GetArray())
	{
		$row['selected'] = ($typeid == $row['id']) ? 'style="color:red;"' : '';
		$_COMMON['smalltypes'][] = $row;
	}
}
unset($temps);


function _get_user_info($uid,$_field = 'uname')
{
	global $db;
	$row = $db->GetOne("SELECT * FROM #@__member WHERE mid='$uid'");
	if(isset($row[$_field]))
	{
		if($_field == 'face')
		{
			$row[$_field] = empty($row[$_field]) ? 'images/common/noavatar.gif' : $row[$_field];
		}
		return $row[$_field];
	}
	else return '';
}
include_once DEDEGROUP.'/templets.php';
?>