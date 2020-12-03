<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: ask_type.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:38 $
 */

require_once(dirname(__FILE__)."/config.php");
if(empty($action))
{
	$sectors = $topsectors = $subsectors = array();
	$sectorscache = '';
	$sql = "select * from `#@__asktype` order by disorder asc, id asc";
	$dsql->SetQuery($sql);
	$dsql->Execute();
	while($row = $dsql->GetArray())
	{
		if($row['reid'] == 0)
		{
			$topsectors[] = $row;
		}
		else
		{
			$subsectors[] = $row;
		}
	}
	foreach($topsectors as $topsector)
	{
		$sectors[] = $topsector;
		$sectorscache .= '<option value="'.$topsector['id'].'">|- '.$topsector['name'].'</option>';
		foreach($subsectors as $subsector)
		{
			if($subsector['reid'] == $topsector['id'])
			{
				$sectors[] = $subsector;
			}
		}
	}
	include(DEDEADMIN."/templets/ask_type.htm");
	exit();
}
else if($action == 'add')
{
	$name = trim($name);
	if($name == '')
	{
		ShowMsg('分类名称不能为空','ask_type.php');
		exit();
	}
	$sql = "insert into `#@__asktype`(name, reid) values('$name','$reid'); ";
	if($dsql->ExecuteNoneQuery($sql))
	{
		ShowMsg('添加分类成功，将返回分类管理页面','ask_type.php');
		exit();
	}
	else
	{
		ShowMsg('添加分类失败，将返回分类管理页面','ask_type.php');
		exit();
	}
}
elseif($action == 'edit')
{
	if(empty($step))
	{
		$sectorscache = '<option value="0">无(作为一级分类)</option>';
		$sql = "select * from `#@__asktype` where id='$id' ";
		$sector = $dsql->GetOne($sql);
		$sql = "select * from `#@__asktype` where reid=0 and id<>'$id' order by disorder asc, id asc";
		$dsql->SetQuery($sql);
		$dsql->Execute();
		while($topsector = $dsql->GetArray())
		{
			$check = '';
			if($sector['reid'] != 0 && $topsector['id'] == $sector['reid'])
			{
				$check = 'selected';
			}
			$sectorscache .= '<option value="'.$topsector['id'].'" '. $check.'>'.$topsector['name'].'</option>';
		}
		include(DEDEADMIN."/templets/ask_type.htm");
		exit();
	}
	else if($step == 2)
	{
		$sql = "update `#@__asktype` set name='$name', reid='$reid', disorder='$disorder' where id='$id' ";
		if($dsql->ExecuteNoneQuery($sql))
		{
			ShowMsg('编辑分类成功，将返回分类管理页面','ask_type.php');
			exit();
		}
		else
		{
			ShowMsg('编辑分类成功，将返回分类管理页面','ask_type.php');
			exit();
		}
	}
}
else if($action == 'update')
{
	$errinfo = '';
	foreach($disorders as $key => $disorder)
	{
		$sql = "update `#@__asktype` set disorder='$disorder', name='{$names[$key]}' where id='$key' ";
		if(!$dsql->ExecuteNoneQuery($sql))
		{
			$errinfo .= $sql."\n";
		}
	}
	if(trim($errinfo)  != '' )
	{
		ShowMsg($errinfo,'ask_type.php');
		exit();
	}
	else
	{
		ShowMsg('更新分类成功，将返回分类管理页面','ask_type.php');
		exit();
	}
}
elseif($action == 'delete')
{
	if(empty($step))
	{
		include(DEDEADMIN.'/templets/ask_type.htm');
		exit();
	}
	else if($step == 2)
	{
		$id = intval($id);
		if($id < 1)
		{
			ShowMsg('分类编号不正确，将返回分类管理页面','ask_type.php');
			exit();
		}
		else
		{
			$sql = "delete from `#@__asktype` where id='$id' or reid='$id' ";
			if($dsql->ExecuteNoneQuery($sql))
			{
				ShowMsg('删除分类成功，将返回分类管理页面', 'ask_type.php');
				exit();
			}
			else
			{
				ShowMsg('删除分类失败，将返回分类管理页面 ','ask_type.php');
				exit();
			}
		}
	}
}
else if($action == 'merge')
{
	$sourcetype = intval($sourcetype);
	$targettype = intval($targettype);
	$son = $dsql->getone("select * from `#@__asktype` where reid='$sourcetype' ");
	if(is_array($son))
	{
		showmsg('源分类有下级分类，不能合并','-1');
		exit();
	}
	$sourceinfo = $dsql->getone("select * from `#@__asktype` where id='$sourcetype' ");
	$targetinfo = $dsql->getone("select * from `#@__asktype` where id='$targettype' ");
	if(!is_array($sourceinfo) || !is_array($targetinfo))
	{
		showmsg('源分类或目标分类不正确','-1');
		exit();
	}
	if($sourceinfo['reid'] == 0) $field = 'tid';
	else $field = 'tid2';
	if($targetinfo['reid'] == 0)
	{
		$query = "update `#@__ask` set tid='{$targetinfo['id']}', tidname='{$targetinfo['name']}', tid2='0', tid2name='' where $field='{$sourceinfo['id']}' ";
		$query2 = "update `#@__askanswer` set tid='{$targetinfo['id']}', tid2='0' where $field='{$sourceinfo['id']}' ";
	}
	else
	{
		$retargetinfo = $dsql->getone("select * from `#@__asktype` where id='{$targetinfo['reid']}' ");
		$query = "update `#@__ask` set tid2='{$targetinfo['id']}', tid2name='{$targetinfo['name']}', tid='{$retargetinfo['id']}', tidname='{$retargetinfo['name']}' where $field='{$sourceinfo['id']}' ";
		$query2 = "update `#@__askanswer` set tid2='{$targetinfo['id']}', tid='{$retargetinfo['id']}' where $field='{$sourceinfo['id']}' ";
	}
	$query3 = "delete from `#@__asktype` where id='{$sourceinfo['id']} limit 1";
	$dsql->executenonequery($query);
	$dsql->executenonequery($query2);
	$dsql->executenonequery($query3);
	showmsg('分类合并成功','ask_type.php');
	exit();
}

?>