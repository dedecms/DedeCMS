<?php
require_once (dirname(__FILE__) . "/../../../include/common.inc.php");
require_once DEDEINC.'/memberlogin.class.php';
require_once DEDEROOT.'/data/sys_pay.cache.php';
require_once(dirname(__FILE__)."/nps_config.php");
require_once DEDEINC.'/shopcar.class.php';

$cart 	= new MemberShops();
$cart->MakeOrders();
$cfg_ml = new MemberLogin(); 
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0) $burl = $cfg_basehost."/member/control.php";
else $burl = "javascript:;";
if(empty($_POST['m_orderid'])){
	echo "非法访问！";
	exit();
}

$memberid	= $m_ocomment;			//备注 这里是返回站内的会员编号
$buyid		= ereg_replace("[^-0-9A-Za-z]","",$m_orderid);   //商家订单号
$mState		=	$_POST['m_status'];//支付状态2成功,3失败
$OrderInfo	=	$OrderMessage;  //订单加密信息
$signMsg 	=	$Digest;				   //密匙
//接收新的md5加密认证
$newmd5info	=	$newmd5info;
$digest = strtoupper(md5($OrderInfo.$cfg_merpassword));

//本地的校对密钥
$newtext = $m_id.$m_orderid.$m_oamount.$cfg_merpassword.$mState;
$myDigest = strtoupper(md5($newtext));
$mysign == md5($cfg_merchant.$buyid.$money.$success.$cfg_merpassword);
//--------------------------------------------------------

//签名正确
if($digest == $signMsg && $mState==2){
	$OrderInfo = HexToStr($OrderInfo);
	if($newmd5info == $myDigest) //md5密匙认证
	{
		$dsql = new DedeSql(false);
    //获取订单信息，检查订单的有效性
    $row = $dsql->GetOne("Select state From #@__shops_orders where oid='$buyid' ");
    if($row['state'] > 0){
		  $msg = "付款已经完成！，系统返回信息( $buyid ) <br><br> <a href='control.php'>返回主页</a> ";
		  ShowMsg($msg,"javascript:;");
		  $dsql->Close();
		  exit();
	  }
		$sql = "UPDATE `#@__shops_orders` SET `state`='1' WHERE `oid`='$buyid' AND `userid`='".$cfg_ml->M_ID."';";
		if($dsql->ExecuteNoneQuery($sql)){
			$dsql->Close();
			ShowMsg("支付成功!","javascript:;");
			exit;
		}else{
			$dsql->Close();
			ShowMsg("支付失败","javascript:;");
			exit;
		}
  }else{
  	ShowMsg("交易密钥错误，请与管理员联系！",$burl);
	  exit();
  }
}else{
	ShowMsg("交易密钥错误，请与管理员联系！",$burl);
	exit();
}
?>
