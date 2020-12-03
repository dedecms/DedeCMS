<?php
require_once(dirname(__FILE__).'/config.php');
CheckRank(0,0);
$dopost = isset($dopost) ? trim($dopost) : '';
if($dopost == '')
{
	$mtypearr = array();
	$query = "select * from #@__mtypes where mid='{$cfg_ml->M_ID}'";
	$dsql->SetQuery($query);
	$dsql->Execute();
	while($row = $dsql->GetArray())
	{
		$mtypearr[] = $row;
	}
	$tpl = new DedeTemplate();
	$tpl->LoadTemplate(DEDEMEMBER.'/templets/mtypes.htm');
	$tpl->Display();
}
elseif ($dopost == 'add')
{
	$mtypename = HtmlReplace(trim($mtypename));
	if(strlen($mtypename) > 40 || strlen($mtypename) < 3)
	{
		ShowMsg('分类名称必须大于两个字少于40个字', '-1');
		exit();
	}
	$query = "insert into #@__mtypes (mtypename, mid) values ('$mtypename', '$cfg_ml->M_ID');";
	if($dsql->ExecuteNoneQuery($query))
	{
		ShowMsg('增加分类成功', 'mtypes.php');
	}
	else
	{
		ShowMsg('增加分类失败', '-1');
	}

}
elseif ($dopost == 'save')
{
	if(isset($mtypeidarr) && is_array($mtypeidarr))
	{
		$delids = '0';
		$mtypeidarr = array_filter($mtypeidarr, 'is_numeric');
		foreach($mtypeidarr as $delid)
		{
			$delids .= ','.$delid;
			unset($mtypename[$delid]);
		}
		$query = "delete from #@__mtypes where mtypeid in ($delids) and mid='$cfg_ml->M_ID';";
		$dsql->ExecNoneQuery($query);
	}
	foreach ($mtypename as $id => $name)
	{
		$name = HtmlReplace($name);
		$query = "update #@__mtypes set mtypename='$name' where mtypeid='$id' and mid='$cfg_ml->M_ID'";
		$dsql->ExecuteNoneQuery($query);
	}
	ShowMsg('分类修改完成','mtypes.php');
}

?>