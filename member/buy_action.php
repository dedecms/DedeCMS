<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once DEDEDATA.'/sys_pay.cache.php';
require_once DEDEINC.'/dedetemplate.class.php';

$product = isset($product) ? trim($product) : '';
$mid = $cfg_ml->M_ID;
$ptype = '';
$pname = '';
$price = '';

if(isset($pd_encode) && isset($pd_verify) && md5("payment".$pd_encode.$cfg_cookie_encode) == $pd_verify)
{

	parse_str(mchStrCode($pd_encode,'DECODE'),$mch_Post);
	foreach($mch_Post as $k => $v) $$k = $v;
	$row  = $dsql->GetOne("SELECT * FROM #@__member_operation WHERE mid='$mid' And sta=0 AND product='$product'");
	if(!isset($row['buyid']))
	{
		ShowMsg("请不要重复提交表单!", 'javascript:;');
		exit();
	}
	$buyid = $row['buyid'];

}
else
{
	$buyid = '';
	$mtime = time();	
	$buyid = 'M'.$mid.'T'.$mtime.'RN'.mt_rand(100,999);
	//删除用户旧的未付款的同类记录
	if(!empty($product))	
	{
		$dsql->ExecuteNoneQuery("Delete From #@__member_operation where mid='$mid' And sta=0 And product='$product'");
	}
}

if(empty($product))
{
	ShowMsg("请选择一个产品!", 'javascript:;');
	exit();
}

$pid = isset($pid) && is_numeric($pid) ? $pid : 0;
if($product=='member')
{
	$ptype = "会员升级";
	$row = $dsql->GetOne("Select * From #@__member_type where aid='{$pid}'");
	if(!is_array($row))
	{
		ShowMsg("无法识别你的订单！", 'javascript:;');
		exit();
	}
	$pname = $row['pname'];
	$price = $row['money'];
}
elseif($product == 'card')
{
	$ptype = "点卡购买";
	$row = $dsql->GetOne("Select * From #@__moneycard_type where tid='{$pid}'");
	if(!is_array($row))
	{
		ShowMsg("无法识别你的订单！", 'javascript:;');
		exit();
	}
	$pname = $row['pname'];
	$price = $row['money'];
}

if(!isset($online_payment))
{	
	$inquery = "
   INSERT INTO #@__member_operation(`buyid` , `pname` , `product` , `money` , `mtime` , `pid` , `mid` , `sta` ,`oldinfo`)
   VALUES ('$buyid', '$pname', '$product' , '$price' , '$mtime' , '$pid' , '$mid' , '0' , '$ptype');
	";
	$isok = $dsql->ExecuteNoneQuery($inquery);
	if(!$isok)
	{
		echo "数据库出错，请重新尝试！".$dsql->GetError();
		exit();
	}
	if($price=='')
	{
		echo "无法识别你的订单！";
		exit();
	}
	
	$price = sprintf("%01.2f", $price);
	
	$payment_list = array();
	foreach($payment_select as $k => $val)
	{
		$temp_arr['name'] = $cfg_pay_info['name'][$k];
		$temp_arr['logo'] = 'images/pay/'.$cfg_pay_info['logo'][$k];
		$temp_arr['des']	= $cfg_pay_info['des'][$k];
		$temp_arr['value'] = $val;
		$temp_arr['exp'] = sprintf("%01.2f", $price*$payment_exp[$k]);
		$payment_list[] = $temp_arr;
	}
	
	$pr_encode = '';
	foreach($_REQUEST as $key => $val)
	{
		$pr_encode .= $pr_encode ? "&$key=$val" : "$key=$val";
	}
	
	$pr_encode = str_replace('=', '', mchStrCode($pr_encode));
	
	$pr_verify = md5("payment".$pr_encode.$cfg_cookie_encode);
	
	$temp_arr = NULL;
	$tpl = new DedeTemplate();
	$tpl->LoadTemplate(DEDEMEMBER.'/templets/buy_action_payment.htm');
	$tpl->Display();
	
}else{
	if(!in_array($online_payment,$payment_select))
	{
		ShowMsg("支付接口无效,或没开启！", 'javascript:;');
		exit();
	}
	require_once DEDEMEMBER.'/inc/config_pay_'.$online_payment.'.php';
}

?>