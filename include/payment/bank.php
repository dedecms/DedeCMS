<?php
if(!defined('DEDEINC')) exit('Request Error!');
/**
 * 银行汇款/转帐接口
 */

/**
 * 类
 */
class bank
{
	/**
   * 构造函数
   *
   * @access  public
   * @param
   *
   * @return void
   */
  function bank()
  {
  }

  function __construct()
  {
      $this->bank();
  }

  /**
   * 提交函数
   */
  function GetCode($order,$payment)
  {
  	require_once DEDEINC.'/shopcar.class.php';
  	$cart 	= new MemberShops();
  	$cart->clearItem();
		$cart->MakeOrders();
	  if($payment=="member") $button="您可以 <a href='/'>返回首页</a> 或去 <a href='/member/operation.php'>会员中心</a>";
    else $button="您可以 <a href='/'>返回首页</a> 或去 <a href='../member/shops_products.php?oid=".$order."'>查看订单</a>";
    return $button;
  }
}

?>