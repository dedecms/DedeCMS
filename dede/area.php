<?php
require_once(dirname(__FILE__)."/config.php");
$db = new DedeSql(false);
if(empty($action)){
	$sectors = $topsectors = $subsectors = array();
	$sectorscache = '';

	$sql = "select * from #@__area order by disorder asc, id asc";
	$db->SetQuery($sql);
	$db->Execute();
	while($row = $db->GetArray())
	{
		//print_r($row);
		if($row['reid'] == 0)
		{
			$topsectors[] = $row;
		}else
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
	include(dirname(__FILE__)."/templets/area.htm");
}elseif($action == 'add')
{
	$sql = "insert into #@__area (name, reid) values ('$name', $reid);";
	$db->SetQuery($sql);
	if($db->ExecuteNoneQuery())
	{
		ShowMsg('添加地区成功，将返回地区管理页面','area.php');
	}else
	{
		ShowMsg('更新地区失败，将返回地区管理页面','area.php');
	}

}elseif($action == 'edit')
{
	if(empty($step)){
		$sectorscache = '<option value="0">无(作为一级地区)</option>';
		$sql = "select * from #@__area where id=$id";
		$db->SetQuery($sql);
		$sector = $db->GetOne();
		$sql = "select * from #@__area where reid=0 and id!=$id order by disorder asc, id asc";
		$db->SetQuery($sql);
		$db->Execute();
		while($topsector = $db->GetArray())
		{
			$check = '';
			if($sector['reid'] != 0 && $topsector['id'] == $sector['reid'])
			{
				$check = 'selected';
			}
			$sectorscache .= '<option value="'.$topsector['id'].'" '. $check.'>'.$topsector['name'].'</option>';
		}
		include(dirname(__FILE__)."/templets/area.htm");

	}elseif($step == 2){
		$sql = "update #@__area set name='$name', reid=$reid, disorder=$disorder where id=$id";
		$db->SetQuery($sql);
		if($db->ExecuteNoneQuery())
		{
			ShowMsg('编辑地区成功，将返回地区管理页面','area.php');
		}else
		{
			ShowMsg('编辑地区成功，将返回地区管理页面','area.php');
		}
	}

}elseif($action == 'update')
{
	//print_r($names);exit;
	$errinfo = '';
	foreach($disorders as $key => $disorder)
	{
		$sql = "update #@__area set disorder=$disorder, name='$names[$key]' where id=$key";
		$db->SetQuery($sql);
		if(!$db->ExecuteNoneQuery())
		{
			$errinfo .= $sql."\n";
		}
	}
	if(trim($errinfo)  != '' )
	{
		ShowMsg($errinfo,'area.php');
	}else
	{
		ShowMsg('更新地区成功，将返回地区管理页面','area.php');
	}

}elseif($action == 'delete')
{
	if(empty($step))
	{
		include(dirname(__FILE__)."/templets/area.htm");
	}elseif($step == 2)
	{
		$id = intval($id);
		if($id < 1)
		{
			ShowMsg('地区编号不正确，将返回地区管理页面','area.php');
		}else
		{
			$sql = "delete from #@__area where id=$id or reid=$id";
			$db->SetQuery($sql);
			if($db->ExecuteNoneQuery())
			{
				ShowMsg('删除地区成功，将返回地区管理页面', 'area.php');
			}else
			{
				ShowMsg('删除地区失败，将返回地区管理页面 ','area.php');
			}
		}
	}

}else{

}


ClearAllLink();
?>