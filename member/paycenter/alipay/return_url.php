<?php
require_once(dirname(__FILE__)."/../../../include/common.inc.php");
require_once DEDEDATA.'/sys_pay.cache.php';
require_once(DEDEINC."/memberlogin.class.php");
require_once(dirname(__FILE__)."/alipay_config.php");
require_once(dirname(__FILE__)."/alipay_notify.php");
$cfg_ml = new MemberLogin();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0) $burl = $cfg_basehost."/member/control.php";
else $burl = "javascript:;";
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
$verify_result = $alipay->return_verify();
//获取支付宝的反馈参数
$dingdan			=$_GET['out_trade_no'];   //获取订单号
$total_fee		=$_GET['total_fee'];    //获取总价格
$receive_name    =$_GET['receive_name'];   //获取收货人姓名
$receive_address =$_GET['receive_address']; //获取收货人地址
$receive_zip     =$_GET['receive_zip'];  //获取收货人邮编
$receive_phone   =$_GET['receive_phone']; //获取收货人电话
$receive_mobile  =$_GET['receive_mobile']; //获取收货人手机
if($verify_result)
{

	//支付成功
	$buyid = $dingdan;

	//获取订单信息，检查订单的有效性
	$row = $dsql->GetOne("Select * From #@__member_operation where buyid='$buyid' ");
	if(!is_array($row)||$row['sta']==2)
	{
		$oldinfo = $row['oldinfo'];
		$msg = "本交易已经完成！，系统返回信息( $oldinfo ) <br><br> <a href='$burl' target='_bank'>返回主页</a> ";
		ShowMsg($msg,"javascript:;");
		$dsql->Close();
		exit();
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
		$equery =  " Update #@__member set membertype='$rank',exptime='$exptime',uptime='".time()."' where mid='$mid' ";
		$dsql->ExecuteNoneQuery($equery);

		//更新交易状态为已关闭
		$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='会员升级成功！' where buyid='$buyid' ");
		$dsql->Close();
		ShowMsg("成功完成交易！",$burl);
		exit();
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
			ShowMsg("由于此点卡已经卖完，系统直接为你的帐号增加了：{$dnum} 个金币！",$burl);
			exit();
		}
		else
		{
			$cardid = $row['cardid'];
			$dsql->ExecuteNoneQuery(" Update #@__moneycard_record set uid='$mid',isexp='1',utime='".time()."' where cardid='$cardid' ");

			//更新交易状态为已关闭
			$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='充值密码：{$cardid}' where buyid='$buyid' ");
			ShowMsg("交易成功！<a href='$burl' target='_bank'><u>[返回]</u></a><br> 充值密码：{$cardid}","javascript:;");
			exit();
		}
	}
	log_result("verify_success"); //将验证结果存入文件
}
else
{
	$msg = "支付失败.";
	ShowMsg($msg,"javascript:;");

	//这里放入你自定义代码，这里放入你自定义代码,比如根据不同的trade_status进行不同操作
	log_result ("verify_failed");
	exit;
}

//日志消息,把支付宝反馈的参数记录下来
function  log_result($word)
{
	$fp = fopen("log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,$word."：执行日期：".strftime("%Y%m%d%H%I%S",time())."\t\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}
?>