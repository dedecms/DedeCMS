<?php
require_once(dirname(__FILE__)."/config.php");
$db = new DedeSql(false);
if(empty($action)){
	$sectors = $topsectors = $subsectors = array();
	$sectorscache = '';
	$sql = "select * from #@__sectors order by disorder asc, id asc";
	$db->SetQuery($sql);
	$db->Execute();
	while($row = $db->GetArray())
	{
		if($row['reid'] == 0) {
			$topsectors[] = $row;
		}else {
			$subsectors[] = $row;
		}
	}
	foreach($topsectors as $topsector)
	{
		$sectors[] = $topsector;
		$sectorscache .= '<option value="'.$topsector['id'].'">|- '.$topsector['name'].'</option>';
		foreach($subsectors as $subsector)
		{
			if($subsector['reid'] == $topsector['id']) {
				$sectors[] = $subsector;
			}
		}
	}
	include(dirname(__FILE__)."/templets/sectors.htm");
/*
function add()
*/
}elseif($action == 'add')
{
	$name = trim($name);
	if($name == '' ) {
		ShowMsg('行业名称不能为空，将返回行业管理页面','sectors.php');
		exit;
	}
	$reid = intval($reid);
	$reid = max(0, $reid);
	$sql = "insert into #@__sectors (name, reid) values ('$name', $reid);";
	$db->SetQuery($sql);
	if($db->ExecuteNoneQuery()) {
		ShowMsg('添加行业成功，将返回行业管理页面','sectors.php');
		exit;
	}else {
		ShowMsg('更新行业失败，将返回行业管理页面','sectors.php');
		exit;
	}
/*
function edit()
*/
}elseif($action == 'edit')
{
	if($step != 2)
	{
		$sectorscache = '<option value="0">无(作为一级行业)</option>';
		$sql = "select * from #@__sectors where id=$id";
		$db->SetQuery($sql);
		$sector = $db->GetOne();
		$sql = "select * from #@__sectors where reid=0 and id!=$id order by disorder asc, id asc";
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
		include(dirname(__FILE__)."/templets/sectors.htm");

	}else{
		$name = trim($name);
		if($name == '' ){
			ShowMsg('行业名称不能为空，将返回行业管理页面','sectors.php');
			exit;
		}
		$reid = intval($reid);
		$disorder = intval($disorder);
		$reid = max(0, $reid);
		$disorder = max(0, $disorder);
		$sql = "update #@__sectors set name='$name', reid=$reid, disorder=$disorder where id=$id";
		$db->SetQuery($sql);
		if($db->ExecuteNoneQuery()) {
			ShowMsg('编辑行业成功，将返回行业管理页面','sectors.php');
			exit;
		}else {
			ShowMsg('编辑行业成功，将返回行业管理页面','sectors.php');
			exit;
		}
	}
/*
function update()
*/
}elseif($action == 'update')
{
	$errinfo = '';
	foreach($disorders as $key => $disorder)
	{
		$names[$key] = trim($names[$key]);
		if($names[$key] == '' ){
			$errinfo .= "id为 $key 的行业名称为空，未更新该条记录<br>";
			continue;
		}
		$sql = "update #@__sectors set disorder=$disorder, name='$names[$key]' where id=$key";
		$db->SetQuery($sql);
		if(!$db->ExecuteNoneQuery()) {
			$errinfo .= $sql."\n";
		}
	}
	if(trim($errinfo)  != '' ) {
		ShowMsg($errinfo,'sectors.php');
		exit;
	}else {
		ShowMsg('更新行业成功，将返回行业管理页面','sectors.php');
		exit;
	}
/*
function delete()
*/
}elseif($action == 'delete')
{
	if($step != 2) {
		include(dirname(__FILE__)."/templets/sectors.htm");
	}else {
		$id = intval($id);
		if($id < 1) {
			ShowMsg('行业编号不正确，将返回行业管理页面','sectors.php');
			exit;
		}else {
			$sql = "delete from #@__sectors where id=$id or reid=$id";
			$db->SetQuery($sql);
			if($db->ExecuteNoneQuery()) {
				ShowMsg('删除行业成功，将返回行业管理页面', 'sectors.php');
				exit;
			}else {
				ShowMsg('删除行业失败，将返回行业管理页面 ','sectors.php');
				exit;
			}
		}
	}

}

ClearAllLink();
?>