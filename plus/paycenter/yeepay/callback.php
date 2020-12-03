<?php
require_once(dirname(__FILE__)."/../../../include/common.inc.php");
require_once DEDEINC.'/shopcar.class.php';
require_once DEDEDATA.'/sys_pay.cache.php';
require_once DEDEINC.'/memberlogin.class.php';
include_once(dirname(__FILE__).'/yeepay_config.php');
$cfg_ml = new MemberLogin();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
$cart 	= new MemberShops();
$cart->MakeOrders();
#	只有支付成功时易宝支付才会通知商户.
##支付成功回调有两次，都会通知到在线支付请求参数中的p8_Url上：浏览器重定向;服务器点对点通讯.

#	解析返回参数.
$return = getCallBackValue($r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$hmac);

#	判断返回签名是否正确（True/False）
$bRet = CheckHmac($r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$hmac);
#	以上代码和变量不需要修改.
#	校验码正确.
if($bRet)
{
	if($r1_Code=="1")
	{
		
	#	需要比较返回的金额与商家数据库中订单的金额是否相等，只有相等的情况下才认为是交易成功.
	#	并且需要对返回的处理进行事务控制，进行记录的排它性处理，防止对同一条交易重复发货的情况发生.      	  	
		if($r9_BType=="1")
		{
			success_db($r6_Order);
		}
		elseif($r9_BType=="2")
		{
			#如果需要应答机制则必须回写流,以success开头,大小写不敏感.
			echo "success";
			success_db($r6_Order);
		}
		elseif($r9_BType=="3")
		{ 
			success_db($r6_Order); 
		}
	ShowMsg('支付成功!',"javascript:;");
	exit;
	}
	
}
else
{
	ShowMsg('交易信息被篡!',"javascript:;");
	exit;
}

function success_db($buyid)
{
	global $dsql,$cfg_ml,$r3_Amt;
	$money = floor($r3_Amt);
	//获取订单信息，检查订单的有效性
	$row = $dsql->GetOne("Select state From #@__shops_orders where oid='$buyid' ");
	if($row['state'] > 0)
	{
		return 1;
	}
	
	$sql = "UPDATE `#@__shops_orders` SET `state`='1' WHERE `oid`='$buyid' AND `userid`='".$cfg_ml->M_ID."';";
	if($dsql->ExecuteNoneQuery($sql))
	{
		return 1;
	}
	else
	{
		return 0;
	}	
	return 0;
}
?>