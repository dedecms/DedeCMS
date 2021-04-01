<?php
define("DE_ItemEcode",'Shop_De_');//识别购物车Cookie前缀,非开发人员请不要随意更改!
/**
 * 购物车类
 *
 * @version        $Id: shopcar.class.php 2 20:58 2010年7月7日 $
 * @package        DedeCMS.Libraries
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
 // ------------------------------------------------------------------------
 /**
 * 会员购物车类
 *
 * @package          MemberShops
 * @subpackage       DedeCMS.Libraries
 * @link             http://www.dedecms.com
 */
class MemberShops
{
    var $OrdersId;
    var $productsId;

    function __construct()
    {
        $this->OrdersId = $this->getCookie("OrdersId");
        if(empty($this->OrdersId))
        {
            $this->OrdersId = $this->MakeOrders();
        }
    }

    function MemberShops()
    {
        $this->__construct();
    }

    /**
     *  创建一个专有订单编号
     *
     * @return    string
     */
    function MakeOrders()
    {
        $this->OrdersId = 'S-P'.time().'RN'.mt_rand(100,999);
        $this->deCrypt($this->saveCookie("OrdersId",$this->OrdersId));
        return $this->OrdersId;
    }

    /**
     *  添加一个商品编号及信息
     *
     * @param     string  $id  购物车ID
     * @param     string  $value  值
     * @return    void
     */
    function addItem($id, $value)
    {
        $this->productsId = DE_ItemEcode.$id;
        $this->saveCookie($this->productsId,$value);
    }

    /**
     *  删去一个带编号的商品
     *
     * @param     string  $id  购物车ID
     * @return    void
     */
    function delItem($id)
    {
        $this->productsId = DE_ItemEcode.$id;
        setcookie($this->productsId, "", time()-3600000,"/");
    }

    /**
     *  清空购物车商品
     *
     * @return    string
     */
    function clearItem()
    {
        foreach($_COOKIE as $key => $vals)
        {
            if(preg_match('/'.DE_ItemEcode.'/', $key))
            {
                setcookie($key, "", time()-3600000,"/");
            }
        }
        return 1;
    }

    /**
     *  得到订单记录
     *
     * @return    array
     */
    function getItems()
    {
        $Products = array();
        foreach($_COOKIE as $key => $vals)
        {
            if(preg_match("#".DE_ItemEcode."#", $key) && preg_match("#[^_0-9a-z]#", $key))
            {
                parse_str($this->deCrypt($vals), $arrays);
                $values = @array_values($arrays);
                if(!empty($values))
                {
                    $arrays['price'] = sprintf("%01.2f", $arrays['price']);
                    if($arrays['buynum'] < 1)
                    {
                        $arrays['buynum'] = 0;
                    }
                    $Products[$key] = $arrays;
                }
            }
        }
        unset($key,$vals,$values,$arrays);
        return $Products;
    }

    /**
     *  得到指定商品信息
     *
     * @param     string  $id  购物车ID
     * @return    array
     */
    function getOneItem($id)
    {
        $key = DE_ItemEcode.$id;
        if(!isset($_COOKIE[$key]) && empty($_COOKIE[$key]))
        {
            return '';
        }
        $itemValue = $_COOKIE[$key];
        parse_str($this->deCrypt($itemValue), $Products);
        unset($key,$itemValue);
        return $Products;
    }

    /**
     *  获得购物车中的商品数
     *
     * @return    int
     */
    function cartCount()
    {
        $Products = $this->getItems();
        $itemsCount = count($Products);
        $i = 0;
        if($itemsCount > 0)
        {
            foreach($Products as $val)
            {
                $i = $i+$val['buynum'];
            }
        }
        unset($Products,$val,$itemsCount);
        return $i;
    }

    /**
     *  获得购物车中的总金额
     *
     * @return    string
     */
    function priceCount()
    {
        $price = 0.00;
        foreach($_COOKIE as $key => $vals)
        {
            if(preg_match("/".DE_ItemEcode."/", $key))
            {
                $Products = $this->getOneItem(str_replace(DE_ItemEcode,"",$key));
                if($Products['buynum'] > 0 && $Products['price'] > 0)
                {
                    $price = $price + ($Products['price']*$Products['buynum']);
                }
            }
        }
        unset($key,$vals,$Products);
        return sprintf("%01.2f", $price);
    }

    //加密接口字符
    function enCrypt($txt)
    {
        return $this->mchStrCode($txt);
    }

    //解密接口字符串
    function deCrypt($txt)
    {
        return $this->mchStrCode($txt,'DECODE');
    }
    
    function mchStrCode($string, $operation = 'ENCODE') 
    {
        $key_length = 4;
        $expiry = 0;
        $key = md5($GLOBALS['cfg_cookie_encode']);
        $fixedkey = md5($key);
        $egiskeys = md5(substr($fixedkey, 16, 16));
        $runtokey = $key_length ? ($operation == 'ENCODE' ? substr(md5(microtime(true)), -$key_length) : substr($string, 0, $key_length)) : '';
        $keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
        $string = $operation == 'ENCODE' ? sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));

        $i = 0; $result = '';
        $string_length = strlen($string);
        for ($i = 0; $i < $string_length; $i++){
            $result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
        }
        if($operation == 'ENCODE') {
            return $runtokey . str_replace('=', '', base64_encode($result));
        } else {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$egiskeys), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        }
    }

    //串行化数组
    function enCode($array)
    {
        $arrayenc = array();
        foreach($array as $key => $val)
        {
            $arrayenc[] = $key.'='.urlencode($val);
        }
        return implode('&', $arrayenc);
    }

    //创建加密的_cookie
    function saveCookie($key,$value)
    {
        if(is_array($value))
        {
            $value = $this->enCrypt($this->enCode($value));
        }
        else
        {
            $value = $this->enCrypt($value);
        }
        setcookie($key,$value,time()+36000,'/');
    }

    //获得解密的_cookie
    function getCookie($key)
    {
        if(isset($_COOKIE[$key]) && !empty($_COOKIE[$key]))
        {
            return $this->deCrypt($_COOKIE[$key]);
        }
    }
}