<?php
if(!defined('DEDEINC')) exit("403 Forbidden!");
//推荐圈子
$_COMMON['recommend'] = array();
$db->SetQuery("SELECT G.*,M.uname,M.userid FROM #@__groups AS G LEFT JOIN #@__member AS M ON G.uid=M.mid WHERE G.isindex=1 ORDER BY G.stime DESC LIMIT 0,6");
$db->Execute();
while($row = $db->GetArray())
{
	$row['hidden'] = $row['ishidden'] ? '私有' : '公开';
	$row['hidden_des'] = $row['ishidden'] ? '好友申请加入' : '允许任何人加入';
	$row['groupimg'] = empty($row['groupimg']) ? 'images/common/defaultpic.gif' : $row['groupimg'];
	$_COMMON['recommend'][] = $row;
}

//新圈子
$_COMMON['rew_group'] = array();
$addsql = isset($id) && is_numeric($id) ? ' WHERE G.groupid<>'.$id : '';
$db->SetQuery("SELECT G.*,M.uname,M.userid FROM #@__groups AS G LEFT JOIN #@__member AS M ON G.uid=M.mid $addsql ORDER BY G.stime DESC LIMIT 0,6");
$db->Execute();
while($row = $db->GetArray())
{
	$row['hidden'] = $row['ishidden'] ? '私有' : '公开';
	$row['hidden_des'] = $row['ishidden'] ? '好友申请加入' : '允许任何人加入';
	$row['groupimg'] = empty($row['groupimg']) ? 'images/common/defaultpic.gif' : $row['groupimg'];
	$_COMMON['rew_group'][] = $row;
}
?>