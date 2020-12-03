<?php
require_once(dirname(__FILE__)."/../../../include/common.inc.php");
require_once DEDEDATA.'/sys_pay.cache.php';
require_once(DEDEINC."/memberlogin.class.php");
include_once 'yeepay_config.php';	
$cfg_ml = new MemberLogin();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
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


function success_db($buyid){
	global $dsql,$cfg_ml,$r3_Amt;
	$money = floor($r3_Amt);
	//获取订单信息，检查订单的有效性
	$row = $dsql->GetOne("Select * From #@__member_operation where buyid='$buyid' ");
	if(!is_array($row)||$row['sta']==2)
	{
		if(isset($row['sta']))
		{
			ShowMsg($row['oldinfo'],"javascript:;");
			exit();
		}
		else
		{
			ShowMsg('订单不存在!',"javascript:;");
			exit();
		}
	}
	if($money != $row['money'])
	{
		ShowMsg('交易信息被篡!',"javascript:;");
		exit;
	}
	
	$mid = $row['mid'];
	$pid = $row['pid'];
	
	//更新交易状态为已付款
	$dsql->ExecuteNoneQuery("Update #@__member_operation set sta=1 where buyid='$buyid' ");
	
	//-------------------------------------------
	//会员产品
	//-------------------------------------------
	if($row['product']=='member')
	{
		$row = $dsql->GetOne(" Select rank,exptime From #@__member_type where aid='{$row['pid']}' ");
		$rank = $row['rank'];
		$exptime = $row['exptime'];
		$equery =  " Update #@__member set
								membertype='$rank',exptime='$exptime',uptime='".time()."' where mid='$mid' ";
		$dsql->ExecuteNoneQuery($equery);
		
		//更新交易状态为已关闭
		$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='会员升级成功！' where buyid='$buyid' ");
	}
	//点卡产品
	else if($row['product']=='card')
	{
		$row = $dsql->GetOne("Select cardid From #@__moneycard_record where ctid='$pid' And isexp='0' ");
		
		//如果找不到某种类型的卡，直接为用户增加金币
		if(!is_array($row))
		{
			$nrow = $dsql->GetOne("Select num From  #@__moneycard_type where tid='$pid' ");
			$dnum = $nrow['num'];
			$equery =  " Update #@__member set money=money+".$dnum." where mid='$mid' ";
			$dsql->ExecuteNoneQuery($equery);
		
			//更新交易状态为已关闭
			$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='直接充值了 {$dnum} 金币到帐号！' where buyid='$buyid' ");
			exit();
		}
		else
		{
			$cardid = $row['cardid'];
			$dsql->ExecuteNoneQuery(" Update #@__moneycard_record set uid='$mid',isexp='1',utime='".time()."' where cardid='$cardid' ");
		
			//更新交易状态为已关闭
			$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='充值密码：{$cardid}' where buyid='$buyid' ");
		}
	}
	return NULL;
}
?>