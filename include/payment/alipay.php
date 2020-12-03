<?php
if(!defined('DEDEINC')) exit('Request Error!');
/**
 * 支付宝接口类
 */
class Alipay
{
  var $dsql;
  /**
   * 构造函数
   *
   * @access  public
   * @param
   *
   * @return void
   */
  function Alipay()
  {
  	global $dsql;
		$this->dsql = $dsql;
  }

  function __construct()
  {
      $this->Alipay();
  }

  /**
   * 生成支付代码
   * @param   array   $order      订单信息
   * @param   array   $payment    支付方式信息
   */
  function GetCode($order, $payment)
  {
  	global $cfg_basehost,$cfg_soft_lang;
    $charset = $cfg_soft_lang;

    $real_method = $payment['alipay_pay_method'];

    switch ($real_method){
      case '0':
        $service = 'trade_create_by_buyer';
        break;
      case '1':
        $service = 'create_partner_trade_by_buyer';
        break;
      case '2':
        $service = 'create_direct_pay_by_user';
        break;
    }

    $agent = 'C4335994340215837114';

    $parameter = array(
      'agent'             => $agent,
      'service'           => $service,
      'partner'           => $payment['alipay_partner'],
      //'partner'           => ALIPAY_ID,
      '_input_charset'    => $charset,
      'notify_url'        => $cfg_basehost."/plus/carbuyaction.php?dopost=return&code=".$payment['code'],
      'return_url'        => $cfg_basehost."/plus/carbuyaction.php?dopost=return&code=".$payment['code'],
      /* 业务参数 */
      'subject'           => "支付订单号:".$order['out_trade_no'],
      'out_trade_no'      => $order['out_trade_no'],
      'price'             => $order['price'],
      'quantity'          => 1,
      'payment_type'      => 1,
      /* 物流参数 */
      'logistics_type'    => 'EXPRESS',
      'logistics_fee'     => 0,
      'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
      /* 买卖双方信息 */
      'seller_email'      => $payment['alipay_account']
    );

    ksort($parameter);
    reset($parameter);
    
    $param = '';
    $sign  = '';

    foreach ($parameter AS $key => $val)
    {
      $param .= "$key=" .urlencode($val). "&";
      $sign  .= "$key=$val&";
    }

    $param = substr($param, 0, -1);
    $sign  = substr($sign, 0, -1). $payment['alipay_key'];
  
    $button = '<div style="text-align:center"><input type="button" onclick="window.open(\'https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.md5($sign).'&sign_type=MD5\')" value="立即使用alipay支付宝支付"/></div>';

    /* 清空购物车 */
		require_once DEDEINC.'/shopcar.class.php';
  	$cart 	= new MemberShops();
    $cart->clearItem();
		$cart->MakeOrders();
    return $button;
  }

  /**
   * 响应操作
   */
  function respond()
  {

    if (!empty($_POST))
    {
      foreach($_POST as $key => $data)
      {
          $_GET[$key] = $data;
      }
    }
    
    /* 引入配置文件 */
	$code = preg_replace( "#[^0-9a-z-]#i", "", $_GET['code'] );
    require_once DEDEDATA.'/payment/'.$code.'.php';
     
    /* 取得订单号 */
    $order_sn = trim($_GET['out_trade_no']);
    
    /*判断订单类型*/
    if(preg_match ("/S-P[0-9]+RN[0-9]/",$order_sn)) {
	    //检查支付金额是否相符
			$row = $this->dsql->GetOne("SELECT * FROM #@__shops_orders WHERE oid = '{$order_sn}'");
			if ($row['priceCount'] != $_GET['total_fee'])
			{
			    return $msg = "支付失败，支付金额与商品总价不相符!";
			}
			$ordertype="goods";
		}elseif(preg_match ("/M[0-9]+T[0-9]+RN[0-9]/",$order_sn)){
			$row = $this->dsql->GetOne("SELECT * FROM #@__member_operation WHERE buyid = '{$order_sn}'");
			
			//获取订单信息，检查订单的有效性
			if(!is_array($row)||$row['sta']==2) return $msg = "您的订单已经处理，请不要重复提交!";
		  elseif($row['money'] != $_GET['total_fee']) return $msg = "支付失败，支付金额与商品总价不相符!";
		  
			$ordertype = "member";
			$product =	$row['product'];
			$pname= $row['pname'];
			$pid=$row['pid'];
			
		}else{	
			return $msg = "支付失败，您的订单号有问题!";
		}

    /* 检查数字签名是否正确 */
    ksort($_GET);
    reset($_GET);
    
    $sign = '';
    foreach ($_GET AS $key=>$val)
    {
      if ($key != 'sign' && $key != 'sign_type' && $key != 'code' && $key != 'dopost')
      {
      	$sign .= "$key=$val&";
      }
    }
    
    $sign = substr($sign, 0, -1).$payment['alipay_key'];
    
    if (md5($sign) != $_GET['sign'])
    {
    	return 	$msg = "支付失败!";
    }
    
    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS' || $_GET['trade_status'] == 'TRADE_SUCCESS')
    {
    	if($ordertype=="goods"){ 
    	  if($this->success_db($order_sn))  return $msg = "支付成功!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
     		else  return $msg = "支付失败!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
     	}elseif($ordertype=="member") {
      	if($this->success_mem($order_sn,$pname,$product,$pid))  return $msg = "支付成功!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
     		else  return $msg = "支付失败!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
     	}
    }else{
    	$this->log_result ("verify_failed");
      return 	$msg = "支付失败!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
    }
  }
  
  /*处理物品交易*/
  function success_db($order_sn){
  	require_once DEDEINC.'/memberlogin.class.php';
  	$cfg_ml = new MemberLogin();
		$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
		
		/* 改变订单状态_支付成功 */
		$sql = "UPDATE `#@__shops_orders` SET `state`='1' WHERE `oid`='$order_sn' AND `userid`='".$cfg_ml->M_ID."'";
		if($this->dsql->ExecuteNoneQuery($sql)){
			$this->dsql->Close();
			$this->log_result("verify_success,订单号:".$order_sn); //将验证结果存入文件
			return 	true;
		}else{
			$this->dsql->Close();
			$this->log_result ("verify_failed,订单号:".$order_sn);//将验证结果存入文件
		  return 	false;
		}
  }
  
   /*处理点卡，会员升级*/
  function success_mem($order_sn,$pname,$product,$pid){
  	require_once DEDEINC.'/memberlogin.class.php';
  	$cfg_ml = new MemberLogin();
		$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
    
    //更新交易状态为已付款
		$sql = "UPDATE `#@__member_operation` SET `sta`='1' WHERE `buyid`='$order_sn' AND `mid`='".$cfg_ml->M_ID."'";
		$this->dsql->ExecuteNoneQuery($sql);

		/* 改变点卡订单状态_支付成功 */
		if($product=="card"){
			$row = $this->dsql->GetOne("Select cardid From #@__moneycard_record where ctid='$pid' And isexp='0' ");
			
			//如果找不到某种类型的卡，直接为用户增加金币
			if(!is_array($row))
			{
				$nrow = $this->dsql->GetOne("SELECT num FROM #@__moneycard_type WHERE pname = '{$pname}'");
				$dnum = $nrow['num'];
				$sql1 = "UPDATE `#@__member` SET `money`=money+'{$nrow['num']}' WHERE `mid`='".$cfg_ml->M_ID."'";
				$oldinf="直接充值了".$nrow['num']."金币到帐号！";
			}else{
				$cardid = $row['cardid'];
				$sql1=" Update #@__moneycard_record set uid='".$cfg_ml->M_ID."',isexp='1',utime='".time()."' where cardid='$cardid' ";
				$oldinf="充值密码：".$cardid;
			}
			//更新交易状态为已关闭
			$sql2=" Update #@__member_operation set sta=2,oldinfo='$oldinf' where buyid='$order_sn'";
			if($this->dsql->ExecuteNoneQuery($sql1) && $this->dsql->ExecuteNoneQuery($sql2)){
		    $this->dsql->Close();
		    $this->log_result("verify_success,订单号:".$order_sn); //将验证结果存入文件
			  return true;
			}else{
				$this->dsql->Close();
				$this->log_result ("verify_failed,订单号:".$order_sn);//将验证结果存入文件
			  return false;
			}
			
		/* 改变会员订单状态_支付成功 */
		}elseif($product=="member"){
			$row = $this->dsql->GetOne("Select rank,exptime From #@__member_type where aid='$pid' ");
			$rank = $row['rank'];
			$exptime = $row['exptime'];
			/*计算原来升级剩余的天数*/
			$rs = $this->dsql->GetOne("Select uptime,exptime From #@__member where mid='".$cfg_ml->M_ID."'");
			if($rs['uptime']!=0 && $rs['exptime']!=0 ) {
				$nowtime = time();
  			$mhasDay = $rs['exptime'] - ceil(($nowtime - $rs['uptime'])/3600/24) + 1;
  			$mhasDay=($mhasDay>0)? $mhasDay : 0;
			}
			$sql1 =  " Update #@__member set rank='$rank',exptime='$exptime'+'$mhasDay',uptime='".time()."' where mid='".$cfg_ml->M_ID."'";
			//更新交易状态为已关闭
			$sql2=" Update #@__member_operation set sta='2',oldinfo='会员升级成功!' where buyid='$order_sn' ";
			if($this->dsql->ExecuteNoneQuery($sql1) && $this->dsql->ExecuteNoneQuery($sql2)){
		    $this->dsql->Close();
		    $this->log_result("verify_success,订单号:".$order_sn); //将验证结果存入文件
			  return true;
			}else{
				$this->dsql->Close();
				$this->log_result ("verify_failed,订单号:".$order_sn);//将验证结果存入文件
			  return false;
			}
		}	
  }
  
  function  log_result($word) {
  	global $cfg_cmspath;
		$fp = fopen(dirname(__FILE__)."/../../data/payment/log.txt","a");
		flock($fp, LOCK_EX) ;
		fwrite($fp,$word.",执行日期:".strftime("%Y-%m-%d %H:%I:%S",time())."\r\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}
}
?>