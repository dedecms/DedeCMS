<?php
require_once(dirname(__FILE__)."/config_space.php");
require_once(dirname(__FILE__)."/../include/inc_memberlogin.php");
$cfg_ml = new MemberLogin();

$svali = GetCkVdValue();
if(strtolower($vdcode)!=$svali || $svali==""){
  ShowMsg("验证码错误！","-1");
  exit();
}

$touid = trim(ereg_replace("[^0-9]","",$touid));
if(empty($touid)){
	ShowMsg("参数错误！","-1");
  exit();
}

if(strlen($products)<2){
	ShowMsg("你的内容太短！","-1");
  exit();
}

$company = cn_substr(html2text($company),100);
$username = cn_substr(html2text($username),20);
$phone = cn_substr(html2text($phone),12);
$fax = cn_substr(html2text($fax),12);
$email = cn_substr(html2text($fax),50);
$qq = cn_substr(html2text($qq),50);
$msn = cn_substr(html2text($msn),50);
$address = cn_substr(html2text($address),200);
$products = cn_substr(html2text($products),200);
$nums = trim(ereg_replace("[^0-9]","",$nums));
$content = html2text($content);
$userip = getip();
$dateline = mytime();

$inquery = "
   INSERT INTO #@__orders(touid,company,username,phone,fax,email,qq,msn,address,products,nums,content,ip,dateline,status)
   VALUES ('$touid','$company','$username','$phone','$fax','$email','$qq','$msn','$address','$products','$nums', '$content','$userip','$dateline','0');
";
//echo $inquery;exit;
$dsql = new DedeSql(false);
if($dsql->ExecuteNoneQuery($inquery)){
	ShowMsg("成功提交你的订单！","-1");
}else{
	ShowMsg("订单提交失败！","-1");
}
$dsql->Close();



?>