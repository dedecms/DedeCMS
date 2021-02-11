<?php if (!defined('DEDEINC')) {exit("DedeCMS Error: Request Error!");
}
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
     * @access public
     * @param
     *
     * @return void
     */

    public function bank()
    {
    
    }

    public function __construct()
    {
        $this->bank();
    
    }

    /**
     * 设置回送地址
     */

    public function SetReturnUrl($returnurl = '')
    {
        return "";
    
    }

    /**
     * 提交函数
     */
    public function GetCode($order, $payment)
    {
        include_once DEDEINC . '/shopcar.class.php';
        $cart = new MemberShops();
        $cart->clearItem();
        $cart->MakeOrders();
        $button = "<a href='/'>返回首页</a>";
        return $button;
    
    }


} //End API
