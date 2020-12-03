<?php
require_once(dirname(__FILE__)."/../../../include/common.inc.php");
require_once DEDEDATA.'/sys_pay.cache.php';
require_once(DEDEINC."/memberlogin.class.php");
import_request_variables("gpc", "frm_");
$cfg_ml = new MemberLogin();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0)
{
	$burl = $cfg_basehost."/member/control.php";
}
else
{
	$burl = "javascript:;";
}

/*这里替换为您的实际商户号*/
$strSpid    = $payment_userid[0];

/*strSpkey是32位商户密钥, 请替换为您的实际密钥*/
$strSpkey   = $payment_key[0];

/*取返回参数*/
$strCmdno			= $frm_cmdno;
$strPayResult		= $frm_pay_result;
$strPayInfo		= $frm_pay_info;
$strBillDate		= $frm_date;
$strBargainorId	= $frm_bargainor_id;
$strTransactionId	= $frm_transaction_id;
$strSpBillno		= $frm_sp_billno;
$strTotalFee		= $frm_total_fee;
$strFeeType		= $frm_fee_type;
$strAttach			= $frm_attach;
$strMd5Sign		= $frm_sign;

/*返回值定义*/
$iRetOK       = 0;		// 成功
$iInvalidSpid = 1;		// 商户号错误
$iInvalidSign = 2;		// 签名错误
$iTenpayErr	  = 3;		// 财付通返回支付失败

/*验签*/

$strResponseText  = "cmdno=" . $strCmdno . "&pay_result=" . $strPayResult .
"&date=" . $strBillDate . "&transaction_id=" . $strTransactionId .
"&sp_billno=" . $strSpBillno . "&total_fee=" . $strTotalFee .
"&fee_type=" . $strFeeType . "&attach=" . $strAttach .
"&key=" . $strSpkey;

$strLocalSign = strtoupper(md5($strResponseText));

if( $strLocalSign  != $strMd5Sign)
{
	$msg = "验证MD5签名失败.";
	ShowMsg($msg,"javascript:;");
	exit;
}

if( $strSpid != $strBargainorId )
{
	$msg = "错误的商户号.";
	ShowMsg($msg,"javascript:;");
	exit;
}

if( $strPayResult != "0" )
{
	$msg = "支付失败.";
	ShowMsg($msg,"javascript:;");
	exit;
}

//支付成功
$buyid = $strSpBillno;
//获取订单信息，检查订单的有效性
$row = $dsql->GetOne("Select * From #@__member_operation where buyid='$buyid' ");
if(!is_array($row)||$row['sta']==2)
{
	$oldinfo = $row['oldinfo'];
	$msg = "本交易已经完成！，系统返回信息( $oldinfo ) <br><br> <a href='$burl' target='_bank'>返回主页</a> ";
	ShowMsg($msg,"javascript:;");
	$dsql->Close();
	exit();
}
$mid = $row['mid'];
$pid = $row['pid'];

//更新交易状态为已付款
$dsql->ExecuteNoneQuery("Update #@__member_operation set sta=1 where buyid='$buyid' ");

//-------------------------------------------
//会员产品
//-------------------------------------------
if($row['product']=='member')
{
	$row = $dsql->GetOne(" Select rank,exptime From #@__member_type where aid='{$row['pid']}' ");
	$rank = $row['rank'];
	$exptime = $row['exptime'];
	$equery =  " Update #@__member set
							membertype='$rank',exptime='$exptime',uptime='".time()."' where mid='$mid' ";
	$dsql->ExecuteNoneQuery($equery);

	//更新交易状态为已关闭
	$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='会员升级成功！' where buyid='$buyid' ");
	ShowMsg("成功完成交易！",$burl);
	exit();
}

//点卡产品
else if($row['product']=='card')
{
	$row = $dsql->GetOne("Select cardid From #@__moneycard_record where ctid='$pid' And isexp='0' ");

	//如果找不到某种类型的卡，直接为用户增加金币
	if(!is_array($row)){
		$nrow = $dsql->GetOne("Select num From  #@__moneycard_type where tid='$pid' ");
		$dnum = $nrow['num'];
		$equery =  " Update #@__member set money=money+".$dnum." where mid='$mid' ";
		$dsql->ExecuteNoneQuery($equery);

		//更新交易状态为已关闭
		$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='直接充值了 {$dnum} 金币到帐号！' where buyid='$buyid' ");
		ShowMsg("由于此点卡已经卖完，系统直接为你的帐号增加了：{$dnum} 个金币！",$burl);
		exit();
	}
	else
	{
		$cardid = $row['cardid'];
		$dsql->ExecuteNoneQuery(" Update #@__moneycard_record set uid='$mid',isexp='1',utime='".time()."' where cardid='$cardid' ");

		//更新交易状态为已关闭
		$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='充值密码：{$cardid}' where buyid='$buyid' ");
		ShowMsg("交易成功！<a href='$burl' target='_bank'><u>[返回]</u></a><br> 充值密码：{$cardid}","javascript:;");
		exit();
	}
}
?>