<?php
include_once(dirname(__FILE__).'/tenpay_config.php');
$strCmdNo   = "1";
$strBillDate= date('Ymd');
/*商品名称*/
$strDesc    = $OrdersId;
/*用户QQ号码, 现在置为空串*/
$strBuyerId = "";
/*商户号*/
$strSaler   = $strSpid;
//支付手续费
if($payment_exp[0] < 0) $payment_exp[0] = 0;
$piice_ex = $priceCount*$payment_exp[0];
if($piice_ex > 0) $price = $priceCount+$piice_ex;
else $price = $priceCount;
//支付金额
$strTotalFee = $price*100;
$strSpBillNo = $OrdersId;

$strTransactionId = $strSpid . $strBillDate . time();
/*货币类型: 1 – RMB(人民币) 2 - USD(美元) 3 - HKD(港币)*/
$strFeeType  = "1";
/*财付通回调页面地址, 推荐使用ip地址的方式(最长255个字符)*/
$strRetUrl  = $cfg_basehost."/plus/paycenter/tenpay/notify_handler.php";
/*商户私有数据, 请求回调页面时原样返回*/
$strAttach  = "my_magic_string";
/*生成MD5签名*/
$strSignText = "cmdno=" . $strCmdNo . "&date=" . $strBillDate . "&bargainor_id=" . $strSaler .
	      "&transaction_id=" . $strTransactionId . "&sp_billno=" . $strSpBillNo .        
	      "&total_fee=" . $strTotalFee . "&fee_type=" . $strFeeType . "&return_url=" . $strRetUrl .
	      "&attach=" . $strAttach . "&key=" . $strSpkey;
$strSign = strtoupper(md5($strSignText));
  
/*请求支付串*/
$strRequest = "cmdno=" . $strCmdNo . "&date=" . $strBillDate . "&bargainor_id=" . $strSaler .        
"&transaction_id=" . $strTransactionId . "&sp_billno=" . $strSpBillNo .        
"&total_fee=" . $strTotalFee . "&fee_type=" . $strFeeType . "&return_url=" . $strRetUrl .        
"&attach=" . $strAttach . "&bank_type=" . $strBankType . "&desc=" . $strDesc .        
"&purchaser_id=" . $strBuyerId .        
"&sign=" . $strSign ;
$strRequestUrl = "https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi?".$strRequest;

if($cfg_soft_lang == 'utf-8')
{
	$strRequestUrl = utf82gb($strRequestUrl);	
	echo '<html>
	<head>
		<title>转到财付通支付页面</title>
	</head>
	<body onLoad="document.tenpay.submit();">
		<form name="tenpay" action="'.$cfg_basehost.'/plus/paycenter/tenpay/tenpay_gbk_page.php?strReUrl='.urlencode($strRequestUrl).'" method="post">
		</form>
	</body>
	</html>';
}else{
	echo '<html>
	<head>
		<title>转到财付通支付页面</title>
	</head>
	<body onLoad="document.tenpay.submit();">
		<form name="tenpay" action="'.$strRequestUrl.'" method="post">
		</form>
	</body>
	</html>';
}
exit;