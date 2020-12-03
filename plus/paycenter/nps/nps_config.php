<?php
//nps 网上支付接口
//商户号
$cfg_merchant = $payment_userid[1];
//商户密钥
$cfg_merpassword = $payment_key[1];
//商户邮箱
$s_eml =	$payment_email[1];
//接口地址
$payment_url = 'https://payment.nps.cn/PHPReceiveMerchantAction.do';
?>