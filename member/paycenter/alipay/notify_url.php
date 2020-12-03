<?php
require_once(dirname(__FILE__)."/../../../include/common.inc.php");
require_once DEDEDATA.'/sys_pay.cache.php';
require_once(DEDEINC."/memberlogin.class.php");
require_once(dirname(__FILE__)."/alipay_config.php");
require_once(dirname(__FILE__)."/alipay_notify.php");
$cfg_ml = new MemberLogin();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0)
{
	$burl = $cfg_basehost."/member/control.php";
}
else
{
	$burl = "javascript:;";
}
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
$verify_result = $alipay->notify_verify();
if($verify_result)
{

	//获取支付宝的反馈参数
	$dingdan=$_POST['out_trade_no'];    //获取支付宝传递过来的订单号
	$total=$_POST['total_fee'];    //获取支付宝传递过来的总价格
	$receive_name    =$_POST['receive_name'];   //获取收货人姓名
	$receive_address =$_POST['receive_address']; //获取收货人地址
	$receive_zip     =$_POST['receive_zip'];  //获取收货人邮编
	$receive_phone   =$_POST['receive_phone']; //获取收货人电话
	$receive_mobile  =$_POST['receive_mobile']; //获取收货人手机
	$trade_status=$_POST['trade_status'];    //获取支付宝反馈过来的状态,根据不同的状态来更新数据库 WAIT_BUYER_PAY(表示等待买家付款);WAIT_SELLER_SEND_GOODS(表示买家付款成功,等待卖家发货);WAIT_BUYER_CONFIRM_GOODS(卖家已经发货等待买家确认);TRADE_FINISHED(表示交易已经成功结束)
	if($_POST['trade_status'] == 'TRADE_FINISHED')
	{

		//支付成功
		$buyid = $dingdan;

		//获取订单信息，检查订单的有效性
		$row = $dsql->GetOne("Select * From #@__member_operation where buyid='$buyid' ");
		if(!is_array($row)||$row['sta']==2)
		{
			$oldinfo = $row['oldinfo'];
			exit("success");
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
								membertype='$rank',exptime='$exptime',uptime='".time()."' where ID='$mid' ";
			$dsql->ExecuteNoneQuery($equery);

			//更新交易状态为已关闭
			$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='会员升级成功！' where buyid='$buyid' ");
			$dsql->Close();
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
		echo "success";
	}
	log_result("verify_success"); //将验证结果存入文件
}
else
{
	echo "fail";
	//这里放入你自定义代码，这里放入你自定义代码,比如根据不同的trade_status进行不同操作
	log_result ("verify_failed");
}

function  log_result($word)
{
	$fp = fopen("log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,$word."：执行日期：".strftime("%Y%m%d%H%I%S",time())."\t\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}
?>