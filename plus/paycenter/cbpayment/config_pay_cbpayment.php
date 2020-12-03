<?php
require_once(dirname(__FILE__)."/cbpayment_config.php");
if($payment_exp[3] < 0) $payment_exp[3] = 0;
$piice_ex = $priceCount*$payment_exp[3];
$v_oid = trim($OrdersId); //订单号
if($piice_ex > 0) $priceCount = $priceCount+$piice_ex;
$v_amount = sprintf("%01.2f", $priceCount);	//支付金额                 

$text = $v_amount.$v_moneytype.$v_oid.$v_mid.$v_url.$key;        //md5加密拼凑串,注意顺序不能变
$v_md5info = strtoupper(md5($text));                             //md5函数加密并转化成大写字母

$remark1 = "支付订单号:".$v_oid;//备注字段1
$remark2 = "订单总价格:".$v_amount."元";//备注字段2

$v_rcvname   = '站长';		// 收货人
$v_rcvaddr   = '深圳';		// 收货地址
$v_rcvtel    = '0755-83791960';		// 收货人电话
$v_rcvpost   = '100080';		// 收货人邮编
$v_rcvmobile = '13838384381';		// 收货人手机号

/*
$v_ordername   = $postname;	// 订货人姓名
$v_orderaddr   = $address;	// 订货人地址
$v_ordertel    = $tel;	// 订货人电话
$v_orderpost   = $zip;	// 订货人邮编
$v_orderemail  = $email;	// 订货人邮件
$v_ordermobile = 13838384581;	// 订货人手机号
*/

$strRequestUrl = $v_post_url.'?v_mid='.$v_mid.'&v_oid='.$v_oid.'&v_amount='.$v_amount.'&v_moneytype='.$v_moneytype
	.'&v_url='.$v_url.'&v_md5info='.$v_md5info.'&remark1='.$remark1.'&remark2='.$remark2;

echo '<html>
<head>
	<title>转到网银在线支付页面</title>
</head>
<body onLoad="document.cbpayment.submit();">
	<form name="cbpayment" action="'.$strRequestUrl.'" method="post">
	</form>
</body>
</html>';
exit;