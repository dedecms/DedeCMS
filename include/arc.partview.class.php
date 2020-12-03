<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
require_once(DEDEINC."/channelunit.class.php");
require_once(DEDEINC."/typelink.class.php");

class PartView
{
	var $dsql;
	var $dtp;
	var $TypeID;
	var $Fields;
	var $TypeLink;
	var $pvCopy;
	var $refObj;

	//php5构造函数
	function __construct($typeid=0,$needtypelink=true)
	{
		global $_sys_globals;
		$this->TypeID = $typeid;
		$this->dsql = $GLOBALS['dsql'];
		$this->dtp = new DedeTagParse();
		$this->dtp->SetNameSpace("dede","{","}");
		$this->dtp->refObj = $this;

		if($needtypelink)
		{
			$this->TypeLink = new TypeLink($typeid);
			if(is_array($this->TypeLink->TypeInfos))
			{
				foreach($this->TypeLink->TypeInfos as $k=>$v)
				{
					if(ereg("[^0-9]",$k))
					{
						$this->Fields[$k] = $v;
					}
				}
			}
			$_sys_globals['curfile'] = 'partview';
			$_sys_globals['typename'] = $this->Fields['typename'];

			//设置环境变量
			SetSysEnv($this->TypeID,$this->Fields['typename'],0,'','partview');
		}
		SetSysEnv($this->TypeID,'',0,'','partview');
		$this->Fields['typeid'] = $this->TypeID;

		//设置一些全局参数的值
		foreach($GLOBALS['PubFields'] as $k=>$v)
		{
			$this->Fields[$k] = $v;
		}
	}

	//重新指定引入的对象
	function SetRefObj(&$refObj)
	{
		$this->dtp->refObj = $refObj;
		if(isset($refObj->TypeID))
		{
			$this->__construct($refObj->TypeID);
		}
	}

	//指定typelink对象给当前类实例
	function SetTypeLink(&$typelink)
	{
		$this->TypeLink = $typelink;
		if(is_array($this->TypeLink->TypeInfos))
		{
			foreach($this->TypeLink->TypeInfos as $k=>$v)
			{
				if(ereg("[^0-9]",$k))
				{
					$this->Fields[$k] = $v;
				}
			}
		}
	}

	//php4构造函数
	function PartView($typeid=0,$needtypelink=true)
	{
		$this->__construct($typeid,$needtypelink);
	}

	//设置要解析的模板
	function SetTemplet($temp,$stype="file")
	{
		if($stype=="string")
		{
			$this->dtp->LoadSource($temp);
		}
		else
		{
			$this->dtp->LoadTemplet($temp);
		}
		if($this->TypeID > 0)
		{
			$this->Fields['position'] = $this->TypeLink->GetPositionLink(true);
			$this->Fields['title'] = $this->TypeLink->GetPositionLink(false);
		}
		$this->ParseTemplet();
	}

	//显示内容
	function Display()
	{
		$this->dtp->Display();
	}

	//获取内容
	function GetResult()
	{
		return $this->dtp->GetResult();
	}

	/**
	 * 保存结果为文件
	 * @param string $filename
	 */
	function SaveToHtml($filename)
	{
		$this->dtp->SaveTo($filename);
	}

	/**
	 * 解析模板里的标签
	 */
	function ParseTemplet()
	{
		$GLOBALS['envs']['typeid'] = $this->TypeID;
		if($this->TypeID>0)
		{
			$GLOBALS['envs']['topid'] = GetTopid($this->TypeID);
		}
		else 
		{
			$GLOBALS['envs']['topid'] = 0;
		}
		if(isset($this->TypeLink->TypeInfos['reid']))
		{
			$GLOBALS['envs']['reid'] = $this->TypeLink->TypeInfos['reid'];
		}
		if(isset($this->TypeLink->TypeInfos['channeltype']))
		{
		  $GLOBALS['envs']['channelid'] = $this->TypeLink->TypeInfos['channeltype'];
		}
		MakeOneTag($this->dtp,$this); //这个函数放在 channelunit.func.php 文件中
	}

	/**
	 * 获得限定模型或栏目的一个指定文档列表
	 * 这个标记由于使用了缓存，并且处理数据是支持分表模式的，因此速度更快，但不能进行整站的数据调用
	 * @param string $templets
	 * @param int $typeid
	 * @param int $row
	 * @param int $col
	 * @param int $titlelen
	 * @param int $infolen
	 * @param int $imgwidth
	 * @param int $imgheight
	 * @param string $listtype
	 * @param string $orderby
	 * @param string $keyword
	 * @param string $innertext
	 * @param int $tablewidth
	 * @param int $arcid
	 * @param string $idlist
	 * @param int $channelid
	 * @param string $limit
	 * @param int $att
	 * @param string $order
	 * @param int $subday
	 * @param int $autopartid
	 * @param int $ismember
	 * @param string $maintable
	 * @param object $ctag
	 * @return array
	 */
	function GetArcList($templets='',$typeid=0,$row=10,$col=1,$titlelen=30,$infolen=160,
	$imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$keyword="",$innertext="",
	$tablewidth="100",$arcid=0,$idlist="",$channelid=0,$limit="",$att=0,$order='desc',$subday=0,
	$autopartid=-1,$ismember=0,$maintable='',$ctag='')
	{
		if(empty($autopartid))
		{
			$autopartid = -1;
		}
		if(empty($typeid))
		{
			$typeid=$this->TypeID;
		}
		if($autopartid!=-1)
		{
			$typeid = $this->GetAutoChannelID($autopartid,$typeid);
			if($typeid==0)
			{
				return "";
			}
		}

		if(!isset($GLOBALS['__SpGetArcList']))
		{
			require_once(dirname(__FILE__)."/inc/inc_fun_SpGetArcList.php");
		}
		return SpGetArcList($this->dsql,$templets,$typeid,$row,$col,$titlelen,$infolen,$imgwidth,$imgheight,
		$listtype,$orderby,$keyword,$innertext,$tablewidth,$arcid,$idlist,$channelid,$limit,$att,
		$order,$subday,$ismember,$maintable,$ctag);
	}

	//关闭所占用的资源
	function Close()
	{
	}
}//End Class
?>