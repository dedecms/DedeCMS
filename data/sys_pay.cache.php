<?php
if(!defined('DEDEINC')) exit("403 Forbidden!");

$payment_select = array(0 => "tenpay",2 => "alipay",3 => "cbpayment",4 => "yeepay");
$payment_userid = array(0 => "",1 => "",2 => "",3 => "",4 => "");
$payment_key = array(0 => "",1 => "",2 => "",3 => "",4 => "");
$payment_curpay = array(0 => 'CNY',1 => 'CNY',2 => 'CNY',3 => 'CNY',4 => 'CNY');
$payment_exp = array(0 => "0.01",1 => "0.00",2 => "0.01",3 => "0.01",4 => "0.00");
$payment_email = array(0 => "webmaster@admin.com",1 => "webmaster@admin.com",2 => "webmaster@admin.com",3 => "webmaster@admin.com",4 => "webmaster@admin.com");

$cfg_pay_info 	= array(
	'name' => array('腾讯财付通','NPS 网上支付系统','支付宝','网银在线','易宝支付'),	
	'type' => array('tenpay','nps','alipay','cbpayment','yeepay'),	
	'logo' => array('tenpay.jpg','nps.gif','alipay.jpg','cbpayment.gif','yeepay.gif'),	
	'reg'	 => array(
		'http://union.tenpay.com/mch/mch_register.shtml?posid=22&actid=84&opid=50&whoid=31&sp_suggestuser=1202347401',
		'http://www.nps.cn/',
		'http://www.alipay.com/',
		'http://merchant3.chinabank.com.cn/register.do',
		'https://www.yeepay.com/selfservice/requestRegister.action'
	),
	'des' => array(
		'财付通是腾讯公司为促进中国电子商务的发展需要，满足互联网用户价值需求，针对网上交易安全而精心推出的一系列服务。',
		'NPS(Network Payment System)是电子商务中网上支付的交易平台,是连接消费者、商家和金融机构的桥梁,实现了Internet上的支付、资金清算、查询统计等功能。',
		'支付宝网站(www.alipay.com)是国内先进的网上支付平台，由全球最佳B2B公司阿里巴巴公司创办，致力于为网络交易用户提供优质的安全支付服务。',
		'网银在线通过整合各家银行的支付接口,为商户提供安全、便捷、稳定、易用的电子商务支付解 决方案。',
		'首批通过国家信息安全系统认证、获得企业信用等级AAA级证书、注册资本1亿元。1%手续费、0年费、支持上百种银行卡、神州行卡支付及游戏点卡支付。网上签约、轻松结算、7X24小时客户服务、共享千万优质会员资源。'
	)	
);

function mchStrCode($string,$action='ENCODE')
{
	$key	= substr(md5($_SERVER["HTTP_USER_AGENT"].$GLOBALS['cfg_cookie_encode']),8,18);
	$string	= $action == 'ENCODE' ? $string : base64_decode($string);
	$len	= strlen($key);
	$code	= '';
	for($i=0; $i<strlen($string); $i++)
	{
		$k		= $i % $len;
		$code  .= $string[$i] ^ $key[$k];
	}
	$code = $action == 'DECODE' ? $code : base64_encode($code);
	return $code;
}