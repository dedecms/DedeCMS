<?php
require_once(dirname(__FILE__)."/config.php");
$db = new DedeSql(false);
if(empty($action)){
	$smalltypes = array();
	$sql = "select typename, ID, smalltypes from #@__arctype where smalltypes<>''";
	$db->SetQuery($sql);
	$db->Execute();
	$typesinfo = $types =array();
	while($row = $db->GetArray())
	{
		$row['smalltypes'] = explode(',',$row['smalltypes']);
		foreach($row['smalltypes'] as $smalltypeid)
		{
			$typesinfo[$smalltypeid][] = $row['typename'];
			$types[$smalltypeid][] = $row['ID'];
		}
	}
	$sql = "select * from #@__smalltypes order by disorder asc, id asc";
	$db->SetQuery($sql);
	$db->Execute();
	while($smalltype = $db->GetArray())
	{
		$smalltype['types'] = $smalltype['relatetype'] = '';
		if(!empty($typesinfo[$smalltype['id']]) && is_array($typesinfo[$smalltype['id']]))
		{
			$smalltype['relatetype'] = implode(', ', $typesinfo[$smalltype['id']]);
			$smalltype['types'] = urlencode(implode(', ', $types[$smalltype['id']]));
		}
		$smalltypes[] = $smalltype;
	}
	include(dirname(__FILE__)."/templets/smalltype.htm");
/*
function add()
*/
}elseif($action == 'add')
{
	$name = trim($name);
	if($name == '' ) {
		ShowMsg('小分类名称不能为空，将返回小分类管理页面','smalltype.php');
		exit();
	}
	$disorder = intval($disorder);
	$disorder = max(0, $disorder);
	$description = trim($description);
	$sql = "insert into #@__smalltypes (name, disorder, description) values ('$name', $disorder, '$description');";
	$db->SetQuery($sql);
	if($db->ExecuteNoneQuery())
	{
		ShowMsg('添加小分类成功，将返回小分类管理页面','smalltype.php');
		exit();
	}else
	{
		ShowMsg('更新小分类失败，将返回小分类管理页面','smalltype.php');
		exit();
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
			$errinfo .= "id为 $key 的小分类名称为空，未更新该条记录<br>";
			continue;
		}
		$sql = "update #@__smalltypes set disorder=$disorder, name='$names[$key]', description='$descriptions[$key]' where id=$key";
		$db->SetQuery($sql);
		if(!$db->ExecuteNoneQuery())
		{
			$errinfo .= $sql."\n";
		}
	}
	if(trim($errinfo)  != '' )
	{
		ShowMsg($errinfo,'smalltype.php');
		exit();
	}else
	{
		ShowMsg('更新小分类成功，将返回小分类管理页面','smalltype.php');
		exit;
	}
/*
function delete()
*/
}elseif($action == 'delete')
{
		$id = intval($id);
		if($id < 1) {
			ShowMsg('小分类编号不正确，将返回小分类管理页面','sectors.php');
			exit();
		}else {
			$sql = "delete from #@__smalltypes where id=$id";
			$db->SetQuery($sql);
			if($db->ExecuteNoneQuery())
			{
				if($types != '')
				{
					$sql = "select ID, smalltypes from #@__arctype where ID in ($types)";
					$db->SetQuery($sql);
					$db->Execute();
					while($row = $db->GetArray()){
						$row['smalltypes'] = explode(',',$row['smalltypes']);
						foreach($row['smalltypes'] as $key => $value)
						{
							if($value == $id){
								unset($row['smalltypes'][$key]);
							}
						}
						$smalltypes = implode(',',$row['smalltypes']);

						$sql = "update #@__arctype set smalltypes='$smalltypes' where ID=$row[ID];";
						$db->SetQuery($sql);
						$db->ExecuteNoneQuery();
					}
				}
				ShowMsg('删除小分类成功，将返回小分类管理页面', 'smalltype.php');
				exit();
			}else
			{
				ShowMsg('删除小分类失败，将返回小分类管理页面 ','smalltype.php');
				exit();
			}
		}
}
ClearAllLink();
?>