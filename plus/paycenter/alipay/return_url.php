<?php
require_once (dirname(__FILE__) . "/../../../include/common.inc.php");
require_once DEDEINC.'/shopcar.class.php';
require_once DEDEINC.'/memberlogin.class.php';
require_once DEDEROOT.'/data/sys_pay.cache.php';
require_once(dirname(__FILE__)."/alipay_config.php");
require_once(dirname(__FILE__)."/alipay_notify.php");
$cfg_ml = new MemberLogin();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0) $burl = $cfg_basehost."/member/control.php";
$cart 	= new MemberShops();
$cart->MakeOrders();
else $burl = "javascript:;";
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
$verify_result = $alipay->return_verify();

//获取支付宝的反馈参数
$dingdan					= $_GET['out_trade_no'];		//获取订单号
$total_fee				= $_GET['total_fee'];    		//获取总价格
 
$receive_name    	= $_GET['receive_name'];  	//获取收货人姓名
$receive_address 	= $_GET['receive_address']; //获取收货人地址
$receive_zip     	= $_GET['receive_zip'];  		//获取收货人邮编
$receive_phone   	= $_GET['receive_phone']; 	//获取收货人电话
$receive_mobile  	= $_GET['receive_mobile']; 	//获取收货人手机

if($verify_result) {
	//支付成功
	$dsql = new DedeSql(false);
	$sql = "UPDATE `#@__shops_orders` SET `state`='1' WHERE `oid`='$dingdan' AND `userid`='".$cfg_ml->M_ID."';";
	if($dsql->ExecuteNoneQuery($sql)){
			$dsql->Close();			
			ShowMsg("支付成功!","javascript:;");
			log_result("verify_success"); //将验证结果存入文件
			exit;
	}else{
			$dsql->Close();
			ShowMsg("支付失败","javascript:;");
			exit;
	}	
}
else  {
	$msg = "支付失败."; 
	ShowMsg($msg,"javascript:;");
	//这里放入你自定义代码，这里放入你自定义代码,比如根据不同的trade_status进行不同操作
	log_result ("verify_failed");
	exit;
}
//日志消息,把支付宝反馈的参数记录下来
function  log_result($word) { 
	$fp = fopen("log.txt","a");	
	flock($fp, LOCK_EX) ;
	fwrite($fp,$word."：执行日期：".strftime("%Y%m%d%H%I%S",time())."\t\n");
	flock($fp, LOCK_UN); 
	fclose($fp);
}	
?>