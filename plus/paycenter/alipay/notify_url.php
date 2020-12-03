<?php
require_once (dirname(__FILE__) . "/../../../include/common.inc.php");
require_once DEDEINC.'/memberlogin.class.php';
require_once DEDEDATA.'/sys_pay.cache.php';
require_once(dirname(__FILE__)."/alipay_config.php");
require_once(dirname(__FILE__)."/alipay_notify.php");

$cfg_ml = new MemberLogin();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0) $burl = $cfg_basehost."/member/control.php";
else $burl = "javascript:;";
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
$verify_result = $alipay->notify_verify();
if($verify_result) {
 //获取支付宝的反馈参数

  $dingdan	=	$_POST['out_trade_no'];    //获取支付宝传递过来的订单号
  $total		=	$_POST['total_fee'];    //获取支付宝传递过来的总价格

  $receive_name   	=	$_POST['receive_name'];   //获取收货人姓名
	$receive_address 	=	$_POST['receive_address']; //获取收货人地址
	$receive_zip     	=	$_POST['receive_zip'];  //获取收货人邮编
	$receive_phone   	=	$_POST['receive_phone']; //获取收货人电话
	$receive_mobile  	=	$_POST['receive_mobile']; //获取收货人手机

  $trade_status			=	$_POST['trade_status'];    //获取支付宝反馈过来的状态,根据不同的状态来更新数据库 WAIT_BUYER_PAY(表示等待买家付款);WAIT_SELLER_SEND_GOODS(表示买家付款成功,等待卖家发货);WAIT_BUYER_CONFIRM_GOODS(卖家已经发货等待买家确认);TRADE_FINISHED(表示交易已经成功结束)



	if($_POST['trade_status'] == 'TRADE_FINISHED') {
	  //支付成功
		$dsql = new DedeSql(false);
		$sql = "UPDATE `#@__shops_orders` SET `state`='1' WHERE `oid`='$dingdan' AND `userid`='".$cfg_ml->M_ID."';";
		if($dsql->ExecuteNoneQuery($sql)){
			$dsql->Close();
			echo "success";
		}else{
			$dsql->Close();
			echo "fail";
		}
	}

	log_result("verify_success"); //将验证结果存入文件
}else  {
	echo "fail";
	//这里放入你自定义代码，这里放入你自定义代码,比如根据不同的trade_status进行不同操作
	log_result ("verify_failed");
}
function  log_result($word) {
	$fp = fopen("log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,$word."：执行日期：".strftime("%Y%m%d%H%I%S",time())."\t\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}
?>