<?php
// examples: $key = array_search('alipay',$payment_select);
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Data');
$config_file = DEDEDATA.'/sys_pay.cache.php';
$message = '<font color="green">√</font>支付接口配置文件(/data/sys_pay.cache.php)可写.WINDOWS主机不能正常检测可写权限时,请设该文件可写权限!';
$string	= '';
@touch($config_file);
if(!is_writable($config_file) || !file_exists($config_file))
{
	$message = '<font color="red">×</font>请检查目录/data/sys_pay.cache.php下文件是否有可写权限(0777)!';
}

include_once DEDEDATA.'/sys_pay.cache.php';

if(isset($dopost) && $dopost == 'save')
{
	if(!isset($payment_select) || empty($payment_select)){
		ShowMsg("选择错误的支付接口类型!","javascript:;");
		exit();
	}
	
	if(!isset($payment_userid) || empty($payment_userid)){
		ShowMsg("请填写正确的商户号!","javascript:;");
		exit();
	}
	
	$payment_select = mch_array_string($_POST['payment_select']);	
	
	$payment_userid = mch_array_string($_POST['payment_userid']);
	
	$payment_email = mch_array_string($_POST['payment_email']);
	
	$payment_key = mch_array_string($_POST['payment_key']);
	
	$payment_exp = mch_array_string($_POST['payment_exp']);

	if( $content = file_get_contents($config_file) ){
	
		$content = insert_mch_config($content, '/\$payment_select \= array\(.*?\);/i', '$payment_select = array('.$payment_select.');');
		
		$content = insert_mch_config($content, '/\$payment_userid \= array\(.*?\);/i', '$payment_userid = array('.$payment_userid.');');
		
		$content = insert_mch_config($content, '/\$payment_key \= array\(.*?\);/i', '$payment_key = array('.$payment_key.');');
		
		$content = insert_mch_config($content, '/\$payment_exp \= array\(.*?\);/i', '$payment_exp = array('.$payment_exp.');');
		
		$content = insert_mch_config($content, '/\$payment_email \= array\(.*?\);/i', '$payment_email = array('.$payment_email.');');

		$fp = fopen($config_file,"w+");
		fwrite($fp,$content);
		fclose($fp);
		ShowMsg("成功更改支付接口设置!","javascript:;");
		exit();
	
	}else{
		ShowMsg("读取配置文件失败!","javascript:;");
		exit();
	}

}


function insert_mch_config($s, $find, $replace) {
	if(preg_match($find, $s)) {
		$s = preg_replace($find, $replace, $s);
	} else {
		// 插入到最后一行
		$s .= "\r\n".$replace;
	}
	return $s;	
}

//格式化数组到字符串
function mch_array_string($arr){
	if(empty($arr)) return '';
	$string = array();
	foreach($arr as $k => $val){
		$k = (!is_numeric($k)) ? "'$k'" : $k;
		$string[] = "$k => \"".addslashes($val)."\"";
	}
	return implode(',',$string);
}

include DedeInclude('templets/sys_info_pay.htm');
?>