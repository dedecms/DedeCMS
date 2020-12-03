<?php 
require_once(dirname(__FILE__)."/config.php");
include_once DEDEINC.'/datalistcp.class.php';

function GetSta($sta,$oid)
{
	global $dsql;
	$row = $dsql->GetOne("SELECT paytype FROM #@__shops_orders WHERE oid='$oid'");
	if($sta==0)
	{
		if($row['paytype'] == 2){
			//货到付款
			return '货到付款';
		}elseif($row['paytype'] == 3){
			//银行付款
			return '未付款&gt;<a href="../plus/shops_bank.php?pid='.$row['paytype'].'" target="_blank">付款</a>';
		}elseif($row['paytype'] == 4){
			//邮政汇款
			return '未付款&gt;<a href="../plus/shops_bank.php?pid='.$row['paytype'].'" target="_blank">付款</a>';
		}elseif($row['paytype'] == 5){
			//扣点时
			return '未付款&gt;<a href="shops_point.php?oid='.$oid.'" target="_blank">付款</a>';
		}elseif($row['paytype'] == 1){
			//网银在线支付
			return '未付款&gt;<a href="../plus/shops_buyaction.php?oid='.$oid.'" target="_blank">付款</a>';
		}
	}
	elseif($sta==1)
	{
		return '已付款,等发货';
	}
	elseif($sta==2)
	{
		return '<a href="shops_products.php?do=ok&oid='.$oid.'">确认</a>';
	}
	else
	{
		return '已完成';
	}
}

$sql = "SELECT * FROM #@__shops_orders WHERE userid='".$cfg_ml->M_ID."' ORDER BY stime DESC";
$dl = new DataListCP();
$dl->pageSize = 20;
//这两句的顺序不能更换
$dl->SetTemplate(dirname(__FILE__)."/templets/shops_orders.htm");      //载入模板
$dl->SetSource($sql);            //设定查询SQL
$dl->Display();                  //显示
?>