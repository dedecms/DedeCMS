<?php
define("DE_ItemEcode",'Shop_De_');//识别购物车Cookie前缀,非开发人员请不要随意更改!

class MemberShops
{
	var $OrdersId;
	var $productsId;

	//php5构造函数PHP>=5.0
	function __construct()
	{
		$this->OrdersId = $this->getCookie("OrdersId");
		if(empty($this->OrdersId))
		{
			$this->OrdersId = $this->MakeOrders();
		}
	}

	//构造类成员PHP<5.0
	function MemberShops()
	{
		$this->__construct();
	}

	//创建一个专有订单编号
	function MakeOrders()
	{
		$this->OrdersId = 'S-P'.time().'RN'.mt_rand(100,999);
		$this->deCrypt($this->saveCookie("OrdersId",$this->OrdersId));
		return $this->OrdersId;
	}

	//添加一个商品编号及信息
	function addItem($id,$value)
	{
		$this->productsId = DE_ItemEcode.$id;
		$this->saveCookie($this->productsId,$value);
	}

	//删去一个带编号的商品
	function delItem($id)
	{
		$this->productsId = DE_ItemEcode.$id;
		setcookie($this->productsId, "", time()-3600000,"/");
	}

	//清空购物车商品
	function clearItem()
	{
		foreach($_COOKIE as $key => $vals)
		{
			if(ereg(DE_ItemEcode,$key))
			{
				setcookie($key, "", time()-3600000,"/");
			}
		}
		return 1;
	}

	//得到订单记录
	function getItems()
	{
		$Products = array();
		foreach($_COOKIE as $key => $vals)
		{
			if(ereg(DE_ItemEcode,$key) && ereg("[^_0-9a-z]",$key))
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

	//得到指定商品信息
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

	//获得购物车中的商品数
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

	//获得购物车中的总金额
	function priceCount()
	{
		$price = 0.00;
		foreach($_COOKIE as $key => $vals)
		{
			if(ereg(DE_ItemEcode,$key))
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
		srand((double)microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++)
		{
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
		}
		return base64_encode($this->setKey($tmp));
	}

	//解密接口字符串
	function deCrypt($txt)
	{
		$txt = $this->setKey(base64_decode($txt));
		$tmp = '';
		for ($i = 0; $i < strlen($txt); $i++)
		{
			$tmp .= $txt[$i] ^ $txt[++$i];
		}
		return $tmp;
	}

	//处理加密数据
	function setKey($txt)
	{
		global $cfg_cookie_encode;
		$encrypt_key = md5(strtolower($cfg_cookie_encode));
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++)
		{
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
		}
		return $tmp;
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
?>