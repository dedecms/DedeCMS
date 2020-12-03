<?php
require_once(dirname(__FILE__).'/config.php');
CheckPurview('sys_Keyword');
require_once(DEDEINC.'/datalistcp.class.php');
$timestamp = time();
if(empty($tag))
{
	$tag = '';
}

if(empty($action))
{
	$orderby = empty($orderby) ? 'id' : eregi_replace('[^a-z]','',$orderby);
	$orderway = isset($orderway) && $orderway == 'asc' ? 'asc' : 'desc';
	if(!empty($tag))
	{
		$where = " where tag like '%$tag%'";
	}
	else
	{
		$where = '';
	}
	$neworderway = ($orderway == 'desc' ? 'asc' : 'desc');
	$query = "Select * from `#@__tagindex` $where order by $orderby $orderway";
	$dlist = new DataListCP();
	$tag = stripslashes($tag);
	$dlist->SetParameter("tag",$tag);
	$dlist->SetParameter("orderway",$orderway);
	$dlist->SetParameter("orderby",$orderby);
	$dlist->pageSize = 20;
	$dlist->SetTemplet(DEDEADMIN."/templets/tags_main.htm");
	$dlist->SetSource($query);
	$dlist->Display();
	exit();
}

/*
function update()
*/
else if($action == 'update')
{
	$tid = (empty($tid) ? 0 : intval($tid) );
	if(empty($tid))
	{
		ShowMsg('没有选择要删除的tag!','-1');
		exit();
	}
	$query = "update `#@__tagindex` set `count`='$count' where id='$tid' ";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("成功保存标签的点击信息!", 'tags_main.php');
	exit();
}

/*
function delete()
*/
else if($action == 'delete')
{
	if(@is_array($ids))
	{
		$stringids = implode(',', $ids);
	}
	elseif(!empty($ids))
	{
		$stringids = $ids;
	}
	else
	{
		ShowMsg('没有选择要删除的tag','-1');
		exit();
	}
	$query = "delete from `#@__tagindex` where id in ($stringids)";
	if($dsql->ExecuteNoneQuery($query))
	{
		$query = "delete from `#@__taglist` where tid in ($stringids)";
		$dsql->ExecuteNoneQuery($query);
		ShowMsg("删除tags[ $stringids ]成功", 'tags_main.php');
	}
	else
	{
		ShowMsg("删除tags[ $stringids ]失败", 'tags_main.php');
	}
	exit();
}

/*
function fetch()
*/
else if($action == 'fetch')
{
	$wheresql = '';
	$start = isset($start) && is_numeric($start) ? $start : 0;
	$where = array();
	if(isset($startaid) && is_numeric($startaid) && $startaid > 0)
	{
		$where[] = " id>$startaid ";
	}
	else
	{
		$startaid = 0;
	}
	if(isset($endaid) && is_numeric($endaid) && $endaid > 0)
	{
		$where[] = " id<$endaid ";
	}
	else
	{
		$endaid = 0;
	}
	if(!empty($where))
	{
		$wheresql = " where arcrank>-1 and ".implode(' and ', $where);
	}
	$query = "select id as aid,arcrank,typeid,keywords from `#@__archives` $wheresql limit $start, 100";
	$dsql->setquery($query);
	$dsql->execute();
	$complete = true;
	while($row = $dsql->getarray())
	{
		$aid = $row['aid'];
		$typeid = $row['typeid'];
		$arcrank = $row['arcrank'];
		$row['keywords'] = trim($row['keywords']);
		if($row['keywords']!='' && !ereg(',',$row['keywords']))
		{
			$keyarr = explode(' ', $row['keywords']);
		}
		else
		{
			$keyarr = explode(',', $row['keywords']);
		}
		foreach($keyarr as $keyword)
		{
			$keyword = trim($keyword);
			if($keyword != '' && strlen($keyword)<13 )
			{
				$keyword = addslashes($keyword);
				$row = $dsql->getone("select id from `#@__tagindex` where tag like '$keyword'");
				if(is_array($row))
				{
					$tid = $row['id'];
					$query = "update `#@__tagindex` set `total`=`total`+1 where id='$tid' ";
					$dsql->ExecuteNoneQuery($query);
				}
				else
				{
					$query = " Insert Into `#@__tagindex`(`tag`,`count`,`total`,`weekcc`,`monthcc`,`weekup`,`monthup`,`addtime`) values('$keyword','0','1','0','0','$timestamp','$timestamp','$timestamp');";
					$dsql->ExecuteNoneQuery($query);
					$tid = $dsql->GetLastID();
				}
				$query = "replace into `#@__taglist`(`tid`,`aid`,`typeid`,`arcrank`,`tag`) values ('$tid', '$aid', '$typeid','$arcrank','$keyword'); ";
				$dsql->ExecuteNoneQuery($query);
			}
		}
		$complete = false;
	}
	if($complete)
	{
		ShowMsg("tags获取完成", 'tags_main.php');
		exit();
	}
	$start = $start + 100;
	$goto = "tags_main.php?action=fetch&startaid=$startaid&endaid=$endaid&start=$start";
	ShowMsg('继续获取tags ...', $goto, 0, 500);
	exit();
}

?>