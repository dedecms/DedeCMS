<?php
require_once (dirname(__FILE__) . "/../../../include/common.inc.php");
require_once DEDEINC.'/shopcar.class.php';
require_once DEDEINC.'/memberlogin.class.php';
require_once DEDEROOT.'/data/sys_pay.cache.php';
include_once(dirname(__FILE__).'/tenpay_config.php');

import_request_variables("gpc", "frm_");
$cfg_ml = new MemberLogin(); 
$cart 	= new MemberShops();
$cart->MakeOrders();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0) $burl = $cfg_basehost."/member/control.php";
else $burl = "javascript:;";
/*取返回参数*/
$strCmdno			= $frm_cmdno;
$strPayResult		= $frm_pay_result;
$strPayInfo		= $frm_pay_info;
$strBillDate		= $frm_date;
$strBargainorId	= $frm_bargainor_id;
$strTransactionId	= $frm_transaction_id;
$strSpBillno		= $frm_sp_billno;
$strTotalFee		= $frm_total_fee;
$strFeeType		= $frm_fee_type;
$strAttach			= $frm_attach;
$strMd5Sign		= $frm_sign;
/*返回值定义*/
$iRetOK       = 0;		// 成功
$iInvalidSpid = 1;		// 商户号错误
$iInvalidSign = 2;		// 签名错误
$iTenpayErr	  = 3;		// 财付通返回支付失败

/*验签*/
$strResponseText  = "cmdno=" . $strCmdno . "&pay_result=" . $strPayResult . 
		                "&date=" . $strBillDate . "&transaction_id=" . $strTransactionId .
			              "&sp_billno=" . $strSpBillno . "&total_fee=" . $strTotalFee .
			              "&fee_type=" . $strFeeType . "&attach=" . $strAttach .
			              "&key=" . $strSpkey;
$strLocalSign = strtoupper(md5($strResponseText));     
  
if( $strLocalSign  != $strMd5Sign)
{
    $msg = "验证MD5签名失败."; 
		ShowMsg($msg,"javascript:;");
		exit;
}  
  
if( $strSpid != $strBargainorId )
{
    $msg = "错误的商户号."; 
		ShowMsg($msg,"javascript:;");
		exit;
}

if( $strPayResult != "0" ){
    $msg = "支付失败."; 
		ShowMsg($msg,"javascript:;");
		exit;
}

//支付成功
$dsql = new DedeSql(false);

//获取订单信息，检查订单的有效性
$row = $dsql->GetOne("Select state From #@__shops_orders where oid='$strSpBillno' ");
if($row['state'] > 0){
	$msg = "付款已经完成！，系统返回信息( $buyid ) <br><br> <a href='control.php'>返回主页</a> ";
	ShowMsg($msg,"javascript:;");
	$dsql->Close();
	exit();
}

$sql = "UPDATE `#@__shops_orders` SET `state`='1' WHERE `oid`='$strSpBillno' AND `userid`='".$cfg_ml->M_ID."';";
if($dsql->ExecuteNoneQuery($sql)){
	$dsql->Close();			
	ShowMsg("支付成功!","javascript:;");
	exit;
}else{
	$dsql->Close();
	ShowMsg("支付失败","javascript:;");
	exit;
}	
?>