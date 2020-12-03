<?php
require_once (dirname(__FILE__) . "/../../../include/common.inc.php");
require_once DEDEINC.'/shopcar.class.php';
require_once DEDEINC.'/memberlogin.class.php';
require_once DEDEROOT.'/data/sys_pay.cache.php';
require_once(dirname(__FILE__)."/cbpayment_config.php");
$cfg_ml = new MemberLogin(); 
$cart 	= new MemberShops();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0) $burl = $cfg_basehost."/member/control.php";
else $burl = "javascript:;";
$cart->MakeOrders();
$v_oid     =trim($_POST['v_oid']);       // 商户发送的v_oid定单编号   
$v_pmode   =trim($_POST['v_pmode']);    // 支付方式（字符串）   
$v_pstatus =trim($_POST['v_pstatus']);   //  支付状态 ：20（支付成功）；30（支付失败）
$v_pstring =trim($_POST['v_pstring']);   // 支付结果信息 ： 支付完成（当v_pstatus=20时）；失败原因（当v_pstatus=30时,字符串）； 
$v_amount  =trim($_POST['v_amount']);     // 订单实际支付金额
$v_moneytype  =trim($_POST['v_moneytype']); //订单实际支付币种    
$remark1   =trim($_POST['remark1' ]);      //备注字段1
$remark2   =trim($_POST['remark2' ]);     //备注字段2
$v_md5str  =trim($_POST['v_md5str' ]);   //拼凑后的MD5校验值  

$md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));

if ($v_md5str==$md5string)
{
	if($v_pstatus=="20"){
		$sql = "UPDATE `#@__shops_orders` SET `state`='1' WHERE `oid`='$v_oid' AND `userid`='".$cfg_ml->M_ID."';";
		if($dsql->ExecuteNoneQuery($sql)){
			ShowMsg("支付成功!","javascript:;");
			exit;
		}else{
			ShowMsg("支付失败","javascript:;");
			exit;
		}
	}else{
		ShowMsg("支付失败","javascript:;");
		exit;
	}
}else{
	ShowMsg("校验失败,数据可疑!","javascript:;");
	exit;
}
?>