<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/com_config.php");
CheckRank(0,0);

$dsql = new DedeSql(false);

if(empty($pagesize)) $pagesize = 5;
if(empty($pageno)) $pageno = 1;
if(empty($dopost)) $dopost = '';
if(empty($orderby)) $orderby = 'orderid';

//重载列表
if($dopost=='getlist'){
	PrintAjaxHead();
	GetList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}elseif($dopost=='del')
{//删除订单
	if(!empty($orderid))
	{
		$orderid = ereg_replace("[^0-9]","",$orderid);
		$dsql->ExecuteNoneQuery("Delete From #@__orders where orderid='$orderid' And touid='".$cfg_ml->M_ID."'; ");
	}elseif(!empty($ids))
	{
		$ids = explode(',',$ids);
		$idsql = "";
		foreach($ids as $orderid)
		{
			$orderid = ereg_replace("[^0-9]","",$orderid);
			if(empty($orderid)) continue;
			if($idsql=="") $idsql .= " orderid='$orderid' ";
			else $idsql .= " Or orderid='$orderid' ";
		}
		if($idsql!="")
		{
			$dsql->ExecuteNoneQuery("Delete From #@__orders where ($idsql) And touid='".$cfg_ml->M_ID."'; ");
		}
	}
	PrintAjaxHead();
	GetList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}elseif($dopost == 'dealorder')
{//标记订单为已处理状态
	if(!empty($orderid))
	{
		$orderid = ereg_replace("[^0-9]","",$orderid);
		$dsql->ExecuteNoneQuery("update #@__orders set status=2 where orderid=$orderid And touid=".$cfg_ml->M_ID.";");
	}elseif(!empty($ids))
	{
		$ids = explode(',',$ids);
		$idsql = "";
		foreach($ids as $orderid)
		{
			$orderid = ereg_replace("[^0-9]","",$orderid);
			if(empty($orderid)) continue;
			if($idsql=="") $idsql .= " orderid='$orderid' ";
			else $idsql .= " Or orderid='$orderid' ";
		}
		if($idsql!="")
		{
			$dsql->ExecuteNoneQuery("update #@__orders set status=2 where ($idsql) And touid='".$cfg_ml->M_ID."'; ");
		}
	}
	PrintAjaxHead();
	GetList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}else{
	$row = $dsql->GetOne("Select count(*) as dd From #@__orders where touid='".$cfg_ml->M_ID."'; ");
	$totalRow = $row['dd'];
	include(dirname(__FILE__)."/templets/myorder.htm");
	$dsql->Close();
}

//获得特定的关键字列表
//---------------------------------
function GetList($dsql,$pageno,$pagesize,$orderby='orderid'){
	global $cfg_phpurl,$cfg_ml;
	$orders = array();
	$start = ($pageno-1) * $pagesize;
  $dsql->SetQuery("Select * From #@__orders where touid='".$cfg_ml->M_ID."' order by $orderby desc limit $start,$pagesize ");
	$dsql->Execute();
  while($row = $dsql->GetArray()){
    $row['content'] = ereg_replace("[ \t\r]"," ",$row['content']);
    $row['content'] = str_replace("  ","　",$row['content']);
    $row['content'] = str_replace("\n","<br>\n",$row['content']);
    if($row['status'] == 1)
    {
    	$row['status'] = '未处理';
    }else
    {
    	$row['status'] = '已处理';
    }

    $orders[] = $row;
   }
	foreach($orders as $order)
	{
		//模板文件
		include(dirname(__FILE__)."/templets/order.htm");
	}
}

function PrintAjaxHead(){
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	header("Content-Type: text/html; charset=utf-8");
}

?>