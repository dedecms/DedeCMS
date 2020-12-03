<?php
//note 清除变量
unset($GLOBALS, $_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);
//返回_GET,_POST,_COOKIE,_REQUEST值 访注入
function GetGPC($k, $var='R') {
	switch($var) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		case 'R': $var = &$_REQUEST; break;
	}
	if(isset($_POST['lang']) && $_POST['lang'] == 'utf-8' && isset($var[$k])){
		if(!function_exists('iconv')) exit('Not install iconv lib!');
		$var[$k] = iconv("UTF-8","GB2312//IGNORE",$var[$k]);	
	}	
	return isset($var[$k]) ? $var[$k] : NULL;
}

$safe_req = array(
	'reqURL_onLine','p0_Cmd','p1_MerId','p2_Order','p3_Amt','p4_Cur','p5_Pid','p6_Pcat',
	'p7_Pdesc','p8_Url','p9_SAF','pa_MP','pd_FrpId','pr_NeedResponse','hmac'
	);
foreach($safe_req as $k) $$k = GetGPC($k,'P');

?>
<html>
<head>
<title>To YeePay Page</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<body onload="document.yeepay.submit();">
<form name="yeepay" action="<?php echo $reqURL_onLine;?>" method="post">
<input type="hidden" name="p0_Cmd"					value="<?php echo $p0_Cmd?>">
<input type="hidden" name="p1_MerId"				value="<?php echo $p1_MerId?>">
<input type="hidden" name="p2_Order"				value="<?php echo $p2_Order?>">
<input type="hidden" name="p3_Amt"					value="<?php echo $p3_Amt?>">
<input type="hidden" name="p4_Cur"					value="<?php echo $p4_Cur?>">
<input type="hidden" name="p5_Pid"					value="<?php echo $p5_Pid?>">
<input type="hidden" name="p6_Pcat"					value="<?php echo $p6_Pcat?>">
<input type="hidden" name="p7_Pdesc"				value="<?php echo $p7_Pdesc?>">
<input type="hidden" name="p8_Url"					value="<?php echo $p8_Url?>">
<input type="hidden" name="p9_SAF"					value="<?php echo $p9_SAF?>">
<input type="hidden" name="pa_MP"					value="<?php echo $pa_MP?>">
<input type="hidden" name="pd_FrpId"				value="<?php echo $pd_FrpId?>">
<input type="hidden" name="pr_NeedResponse"	value="<?php echo $pr_NeedResponse?>">
<input type="hidden" name="hmac"					value="<?php echo $hmac?>">
</form>
</body>
</html>