<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
require_once(DEDEINC."/dedetag.class.php");
require_once(DEDEINC."/channelunit.func.php");

/*----------------------------------
function C____ChannelUnit();
-----------------------------------*/
class ChannelUnit
{
	var $ChannelInfos;
	var $ChannelFields;
	var $AllFieldNames;
	var $ChannelID;
	var $ArcID;
	var $dsql;
	var $SplitPageField;

	//php5构造函数
	function __construct($cid,$aid=0)
	{
		$this->ChannelInfos = '';
		$this->ChannelFields = '';
		$this->AllFieldNames = '';
		$this->SplitPageField = '';
		$this->ChannelID = $cid;
		$this->ArcID = $aid;
		$this->dsql = $GLOBALS['dsql'];
		$this->ChannelInfos = $this->dsql->GetOne(" Select * from `#@__channeltype` where id='$cid' ");
		if(!is_array($this->ChannelInfos))
		{
			echo '读取频道信息失败，无法进行后续操作！';
			exit();
		}
		$dtp = new DedeTagParse();
		$dtp->SetNameSpace('field','<','>');
		$dtp->LoadSource($this->ChannelInfos['fieldset']);
		if(is_array($dtp->CTags))
		{
			$tnames = Array();
			foreach($dtp->CTags as $ctag)
			{
				$tname = $ctag->GetName();
				if(isset($tnames[$tname]))
				{
					break;
				}
				$tnames[$tname] = 1;
				if($this->AllFieldNames!='')
				{
					$this->AllFieldNames .= ','.$tname;
				}
				else
				{
					$this->AllFieldNames .= $tname;
				}
				if(is_array($ctag->CAttribute->Items))
				{
					$this->ChannelFields[$tname] = $ctag->CAttribute->Items;
				}
				$this->ChannelFields[$tname]['value'] = '';
				$this->ChannelFields[$tname]['innertext'] = $ctag->GetInnerText();
				if(empty($this->ChannelFields[$tname]['itemname']))
				{
					$this->ChannelFields[$tname]['itemname'] = $tname;
				}
				if($ctag->GetAtt('page')=='split')
				{
					$this->SplitPageField = $tname;
				}
			}
		}
		$dtp->Clear();
	}

	function ChannelUnit($cid,$aid=0)
	{
		$this->__construct($cid,$aid);
	}

	//设置档案ID
	function SetArcID($aid)
	{
		$this->ArcID = $aid;
	}

	//处理某个字段的值
	function MakeField($fname,$fvalue,$addvalue='')
	{
		if($fvalue=='')
		{
			$fvalue = $this->ChannelFields[$fname]['default'];
		}

		//处理各种数据类型
		$ftype = $this->ChannelFields[$fname]['type'];
		if($ftype=='text')
		{
			$fvalue = HtmlReplace($fvalue);
		}
		else if($ftype=='textdata')
		{
			if(!is_file($GLOBALS['cfg_basedir'].$fvalue))
			{
				return '';
			}
			$fp = fopen($GLOBALS['cfg_basedir'].$fvalue,'r');
			$fvalue = '';
			while(!feof($fp))
			{
				$fvalue .= fgets($fp,1024);
			}
			fclose($fp);
		}
		else if($ftype=='addon')
		{
			$foldvalue = $fvalue;
			$tmptext = GetSysTemplets("channel_addon.htm");
			$fvalue  = str_replace('~link~',$foldvalue,$tmptext);
			$fvalue  = str_replace('~phpurl~',$GLOBALS['cfg_phpurl'],$fvalue);
		}
		else if(file_exists(DEDEINC.'/taglib/channel/'.$ftype.'.lib.php'))
		{
			include_once(DEDEINC.'/taglib/channel/'.$ftype.'.lib.php');
			$func = 'ch_'.$ftype;
			$fvalue = $func($fvalue,$addvalue,$this,$fname);
		}
		return $fvalue;
	}

	//关闭所占用的资源
	function Close()
	{
	}

}//End  class ChannelUnit
?>