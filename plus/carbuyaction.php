<?php
require_once (dirname(__FILE__) . "/../include/common.inc.php");
define('_PLUS_TPL_', DEDEROOT.'/templets/plus');
require_once DEDEINC.'/dedetemplate.class.php';
require_once DEDEINC.'/shopcar.class.php';
require_once DEDEINC.'/memberlogin.class.php';
require_once DEDEROOT.'/data/sys_pay.cache.php';

if($cfg_mb_open=='N')
{
	ShowMsg("系统关闭了会员功能，因此你无法访问此页面！","javascript:;");
	exit();
}
$payment = 'none';
if(isset($pd_encode) && isset($pd_verify) && md5("payment".$pd_encode.$cfg_cookie_encode) == $pd_verify)
{
	parse_str(mchStrCode($pd_encode,'DECODE'),$mch_Post);
	$payment = 'ready';
	foreach($mch_Post as $k => $v) $$k = $v;
}

$pr_encode = '';
foreach($_REQUEST as $key => $val)
{
	$pr_encode .= $pr_encode ? "&$key=$val" : "$key=$val";
}
$pr_encode = str_replace('=', '', mchStrCode($pr_encode));
$pr_verify = md5("payment".$pr_encode.$cfg_cookie_encode);

$cfg_ml = new MemberLogin();
$cart 	= new MemberShops();

//获得购物车内商品,返回数组
$Items = $cart->getItems();
if(empty($Items))
{
	ShowMsg("抱歉,请不要重复提交!","javascript:;");
	exit();
}

$OrdersId = $cart->OrdersId;		//本次记录的订单号
$CartCount 	= $cart->cartCount();	//商品总数
$priceCount	= $cart->priceCount();//该订单总价格

/*
function PostOrdersForm();				//填写订单信息
*/
if(!isset($do) || empty($do))
{
	$shops_deliveryarr = array();
	$dsql->SetQuery("SELECT pid,dname,price,des FROM #@__shops_delivery ORDER BY orders ASC");
	$dsql->Execute();
	while($row = $dsql->GetArray())
	{
		$shops_deliveryarr[] = $row;
	}
	
	$shops_paytypearr = array();
	$dsql->SetQuery("SELECT pid,paytype FROM #@__shops_paytype ORDER BY pid ASC");
	$dsql->Execute();
	$i = 0 ;
	while($row = $dsql->GetArray())
	{
		$row['checked'] = !$i ? ' checked="checked"' : '';
		$row['disabled'] = ($row['pid'] == 5) && ($cfg_ml->M_Money < $priceCount) ? ' disabled="disabled"' : '';
		$shops_paytypearr[] = $row;
		$i++;
	}
	unset($row);
	
	$dtp = new DedeTemplate();
	
	$carts = array(
		'orders_id' => $cart->OrdersId,
		'cart_count' => $cart->cartCount(),
		'price_count' => $cart->priceCount()
	);
	$dtp->Assign('carts',$carts);
	
	$dtp->LoadTemplate(_PLUS_TPL_.'/carbuyaction.htm');
	$dtp->Display();
	exit();
}
elseif($do == 'clickout')
{
	$svali = GetCkVdValue();
	if(strtolower(($vdcode)!=$svali || $svali=="") && $payment == 'none')
	{
		ShowMsg("验证码错误！","-1");
		exit();
	}
	if(empty($address))
	{
		ShowMsg("请填写收货地址！","-1");
		exit();
	}
	if(empty($postname))
	{
		ShowMsg("请填写收货人姓名！","-1");
		exit();
	}
	$paytype	= isset($paytype) && is_numeric($paytype) ? $paytype : 0;
	$pid		= isset($pid) && is_numeric($pid) ? $pid : 0;
	if($paytype < 1)
	{
		ShowMsg("请选择支付方式！","-1");
		exit();
	}
	if($pid < 1)
	{
		ShowMsg("请选择配送方式！","-1");
		exit();
	}
	$address 	= cn_substrR(trim($address),200);
	$des 			= cn_substrR($des,100);
	$postname = cn_substrR(trim($postname),15);
	$tel			= ereg_replace("[^-0-9,\/\| ]","",$tel);
	$zip			= ereg_replace("[^0-9]","",$zip);
	$email		= cn_substrR($email,255);
	if(empty($tel))
	{
		ShowMsg("请填写正确的收货人联系电话！","-1");
		exit();
	}
	if($zip<1 || $zip>999999)
	{
		ShowMsg("请填写正确的收货人邮政编码！","-1");
		exit();
	}

	//确认用户登录信息
	if($cfg_ml->IsLogin())
	{
		$userid = $cfg_ml->M_ID;
	}
	else
	{
		$username = trim($username);
		$password = trim($password);
		
		if(empty($username) || $password)
		{
			ShowMsg("请选登录！","-1",0,2000);
			exit();
		}
		
		$rs = $cfg_ml->CheckUser($username,$password);
		if($rs==0)
		{
			ShowMsg("用户名不存在！","-1",0,2000);
			exit();
		}
		else if($rs==-1)
		{
			ShowMsg("密码错误！","-1",0,2000);
			exit();
		}
		$userid = $cfg_ml->M_ID;
	}

	//取得配送手续费
	$rs = $dsql->GetOne("SELECT `price` FROM #@__shops_delivery WHERE pid='$pid' LIMIT 0,1");
	$dprice = $rs['price'] > 0 ? $rs['price'] : 0;
	unset($rs);
	//
	$ip = GetIP();
	$stime = time();
	//最后总计费用
	$lastpriceCount = sprintf("%01.2f", $priceCount+$dprice);

	$rows = $dsql->GetOne("SELECT `oid` FROM #@__shops_orders WHERE oid='$OrdersId' LIMIT 0,1");
	if(empty($rows['oid']))
	{
		$sql = "INSERT INTO `#@__shops_orders` (`oid`,`userid`,`cartcount`,`price`,`state`,`ip`,`stime`,`pid`,`paytype`,`dprice`,`priceCount`)
		VALUES ('$OrdersId','$userid','$CartCount','$priceCount','0','$ip','$stime','$pid','$paytype','$dprice','$lastpriceCount');";

		//更新订单
		if($dsql->ExecuteNoneQuery($sql))
		{
			foreach($Items as $key=>$val)
			{
				$val['price'] = str_replace(",","",$val['price']);
				$dsql->ExecuteNoneQuery("INSERT INTO `#@__shops_products` (`aid`,`oid`,`userid`,`title`,`price`,`buynum`)
				VALUES ('$val[id]','$OrdersId','$userid','$val[title]','$val[price]','$val[buynum]');");
			}
			$sql = "INSERT INTO `#@__shops_userinfo` (`userid`,`oid`,`consignee`,`address`,`zip`,`tel`,`email`,`des`)
			 VALUES ('$userid','$OrdersId','$postname','$address','$zip','$tel','$email','$des');
			";
			$dsql->ExecuteNoneQuery($sql);
		}
		else
		{
			ShowMsg("更新订单时出现错误！".$dsql->GetError(),"-1");
			exit();
		}
	}
	else
	{
		$sql = "UPDATE `#@__shops_orders`
		SET `cartcount`='$CartCount',`price`='$priceCount',`ip`='$ip',`stime`='$stime',pid='$pid',paytype='$paytype',dprice='$dprice',priceCount='$lastpriceCount'
		WHERE oid='$OrdersId' AND userid='$userid' ;";
		if($dsql->ExecuteNoneQuery($sql))
		{
			$sql = "UPDATE `#@__shops_userinfo`
			SET `consignee`='$postname',`address`='$address',`zip`='$zip',`tel`='$tel',`email`='$email',`des`='$des'
			WHERE oid='$OrdersId';";
			$dsql->ExecuteNoneQuery($sql);
		}
		else
		{
			echo $dsql->GetError();
			exit;
		}
		unset($sql);
	}
	//最后结算价格 = 最后统计价格
	$priceCount = sprintf("%01.2f", $lastpriceCount);
	//更新用户商品统计	
	$countOrders = $dsql->GetOne("SELECT SUM(cartcount) AS nums FROM #@__shops_orders WHERE userid='".$cfg_ml->M_ID."'");	
	$dsql->ExecuteNoneQuery("UPDATE #@__member_tj SET `shop`='".$countOrders['nums']."' WHERE mid='".$cfg_ml->M_ID."'");

	$rs = $dsql->GetOne("SELECT `paytype`,`des` FROM `#@__shops_paytype` WHERE pid='$paytype' ");
	if($paytype == 1)
	{
		/*
		function onlinePayment();
		网银支付时
		*/
		if(!isset($online_payment))
		{		
			$payment_list = array();
			foreach($payment_select as $k => $val)
			{
				$temp_arr['name'] = $cfg_pay_info['name'][$k];
				$temp_arr['logo'] = $cfg_cmspath.'/member/images/pay/'.$cfg_pay_info['logo'][$k];
				$temp_arr['des']	= $cfg_pay_info['des'][$k];
				$temp_arr['value'] = $val;
				$temp_arr['exp'] = sprintf("%01.2f", $priceCount*$payment_exp[$k]);
				$payment_list[] = $temp_arr;
			}
			$dtp = new DedeTemplate();
	
			$carts = array(
				'orders_id' => $cart->OrdersId,
				'cart_count' => $cart->CartCount(),
				'price_count' => $priceCount
			);
			
			$dtp->Assign('carts',$carts);
			
			$dtp->LoadTemplate(_PLUS_TPL_.'/shops_action_payment.htm');
			$dtp->Display();
			exit();
		}else{
			if(!in_array($online_payment,$payment_select))
			{
				ShowMsg("支付接口无效,或没开启！", 'javascript:;');
				exit();
			}
			//清空购物车
			$cart->clearItem();
			$cart->MakeOrders();			
			require_once DEDEROOT.'/plus/paycenter/'.$online_payment.'/config_pay_'.$online_payment.'.php';		
		}
		exit();
	}
	elseif($paytype == 2)
	{
		/*
		function Arrival-pay();
		货到付款
		*/
		//清空购物车
		$cart->clearItem();
		$cart->MakeOrders();
		ShowMsg("下单成功,等待商家发货！","../member/shops_products.php?oid=".$OrdersId);
		exit();
	}
	elseif($paytype == 3)
	{
		/*
		function Bank-pay();
		银行转帐
		*/
		//清空购物车
		$cart->clearItem();
		$cart->MakeOrders();
		$dtp = new DedeTemplate();
		$dtp->Assign('banks',$rs);
		$dtp->LoadTemplate(_PLUS_TPL_.'/shops_bank.htm');
		$dtp->Display();
		exit();
	}
	elseif($paytype == 4)
	{
		/*
		function Post-pay();
		邮政汇款
		*/
		//清空购物车
		$cart->clearItem();
		$cart->MakeOrders();
		$dtp = new DedeTemplate();
		$dtp->Assign('banks',$rs);
		$dtp->LoadTemplate(_PLUS_TPL_.'/shops_bank.htm');
		$dtp->Display();
		exit();
	}
	elseif($paytype == 5)
	{
		/*
		function Point-pay();
		点数购买
		*/
		$members = $dsql->GetOne("SELECT `money` FROM #@__member WHERE mid='".$cfg_ml->M_ID."'");
		if($members['money'] < $priceCount)
		{
			ShowMsg("支付失败点数不够！","-1");
			exit();
		}
		if($dsql->ExecuteNoneQuery("UPDATE `#@__shops_orders` SET `state`='1' WHERE `oid`='$OrdersId' AND `userid`='".$cfg_ml->M_ID."' AND `state`<1"))
		{
			//清空购物车
			$cart->clearItem();
			$cart->MakeOrders();
			$res = $dsql->ExecuteNoneQuery("UPDATE #@__member SET money=money-$priceCount WHERE mid='".$cfg_ml->M_ID."'");
			ShowMsg("下单,支付成功,等待商家发货！","../member/shops_products.php?oid=".$OrdersId);
			exit();
		}
		else
		{
			ShowMsg("支付失败,请联系管理员！","-1");
			exit();
		}
	}
	exit();
}
?>