<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/com_config.php");

$dsql = new DedeSql(false);

if(empty($pagesize)) $pagesize = 5;
if(empty($pageno)) $pageno = 1;
if(empty($dopost)) $dopost = '';
if(empty($orderby)) $orderby = 'pubdate';

//重载列表
if($dopost=='getlist'){
	PrintAjaxHead();
	GetList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}elseif($dopost=='del')
{//删除订单
	if(!empty($id))
	{
		$id = ereg_replace("[^0-9]","",$id);
		$dsql->ExecuteNoneQuery("Delete From #@__jobs where id='$id' And memberID='".$cfg_ml->M_ID."'; ");
	}elseif(!empty($ids))
	{
		$ids = explode(',',$ids);
		$idsql = "";
		foreach($ids as $id)
		{
			$d = ereg_replace("[^0-9]","",$id);
			if(empty($id)) continue;
			if($idsql=="") $idsql .= " id='$id' ";
			else $idsql .= " Or id='$id' ";
		}
		if($idsql!="")
		{
			$dsql->ExecuteNoneQuery("Delete From #@__jobs where ($idsql) And memberID='".$cfg_ml->M_ID."'; ");
		}
	}
	PrintAjaxHead();
	GetList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}else{
	$row = $dsql->GetOne("Select count(*) as dd From #@__jobs where memberID='".$cfg_ml->M_ID."'; ");
	$totalRow = $row['dd'];
	include(dirname(__FILE__)."/templets/joblist.htm");
	$dsql->Close();
}

//获得特定的关键字列表
//---------------------------------
function GetList($dsql,$pageno,$pagesize,$orderby='pubdate'){
	global $cfg_phpurl,$cfg_ml;
	$jobs = array();
	$start = ($pageno-1) * $pagesize;

  $dsql->SetQuery("Select * From #@__jobs where memberID='".$cfg_ml->M_ID."' order by $orderby desc limit $start,$pagesize ");
	$dsql->Execute();
  while($row = $dsql->GetArray()){
	$row['endtime'] = @ceil(($row['endtime']-$row['pubdate'])/86400);
	if($row['salaries'] == 0){
		$row['salaries'] = '薪酬面议';
	}
    $jobs[] = $row;
   }
	foreach($jobs as $job)
	{
		//模板文件
		include(dirname(__FILE__)."/templets/job.htm");
	}
}

function PrintAjaxHead(){
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	header("Content-Type: text/html; charset=utf-8");
}

?>

