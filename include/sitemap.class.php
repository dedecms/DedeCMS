<?php
//class SiteMap
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
require_once(DEDEINC."/channelunit.func.php");

class SiteMap
{
	var $dsql;
	var $artDir;
	var $baseDir;

	//php5构造函数
	function __construct()
	{
		$this->idCounter = 0;
		$this->artDir = $GLOBALS['cfg_arcdir'];
		$this->baseDir = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_basedir'];
		$this->idArrary = "";
		$this->dsql = $GLOBALS['dsql'];
	}

	function SiteMap()
	{
		$this->__construct();
	}

	//清理类
	function Close()
	{
	}

	//获取网站地图
	//$maptype = "site" 或 "rss"
	function GetSiteMap($maptype="site")
	{
		$mapString = "";
		if($maptype=="rss")
		{
			$this->dsql->SetQuery("Select id,typedir,isdefault,defaultname,typename,ispart,namerule2,moresite,siteurl,sitepath From #@__arctype where ishidden<>1 And reid=0 And ispart<>2 order by sortrank");
		}
		else
		{
			$this->dsql->SetQuery("Select id,typedir,isdefault,defaultname,typename,ispart,namerule2,siteurl,sitepath,moresite,siteurl,sitepath From #@__arctype where reid=0 And ishidden<>1 order by sortrank");
		}
		$this->dsql->Execute(0);
		while($row=$this->dsql->GetObject(0))
		{
			if($maptype=="site")
			{
				$typelink = GetTypeUrl($row->id,MfTypedir($row->typedir),$row->isdefault,$row->defaultname,$row->ispart,$row->namerule2,$row->moresite,$row->siteurl,$row->sitepath);
			}
			else
			{
				$typelink = $GLOBALS['cfg_cmsurl']."/data/rss/".$row->id.".xml";
			}
			$mapString .= "<div class=\"linkbox\">\r\n<h3><a href='$typelink'>".$row->typename."</a></h3>";
			$mapString .= "\t<ul class=\"f6\">\t\t\r".$this->LogicListAllSunType($row->id,$maptype)."\t\n</ul></div>\r\n";
			/*
			$mapString .= "<tr><td width='17%' align='center' bgcolor='#FAFEF1'>";
			$mapString .= "<a href='$typelink'><b>".$row->typename."</b></a>";
			$mapString .= "</td><td width='83%' bgcolor='#FFFFFF'>";
			$mapString .= $this->LogicListAllSunType($row->id,$maptype);
			$mapString .= "</td></tr>";
			*/
		}
		return $mapString;
	}

	//获得子类目的递归调用
	function LogicListAllSunType($id,$maptype)
	{
		$fid = $id;
		$mapString = "";
		if($maptype=="rss")
		{
			$this->dsql->SetQuery("Select id,typedir,isdefault,defaultname,typename,ispart,namerule2,moresite,siteurl,sitepath From #@__arctype where reid='".$id."' And ishidden<>1 And ispart<>2 order by sortrank");
		}
		else
		{
			$this->dsql->SetQuery("Select id,typedir,isdefault,defaultname,typename,ispart,namerule2,moresite,siteurl,sitepath From #@__arctype where reid='".$id."' And ishidden<>1 order by sortrank");
		}
		$this->dsql->Execute($fid);
		while($row=$this->dsql->GetObject($fid))
		{
			if($maptype=="site")
			{
				$typelink = GetTypeUrl($row->id,MfTypedir($row->typedir),$row->isdefault,$row->defaultname,$row->ispart,$row->namerule2,$row->moresite,$row->siteurl,$row->sitepath);
			}
			else
			{
				$typelink = $GLOBALS['cfg_cmsurl']."/data/rss/".$row->id.".xml";
			}
			$mapString .= "<li><a href='$typelink'>".$row->typename."</a></li>\n\t\t";
			$mapString .= $this->LogicListAllSunType($row->id,$maptype);
		}
		return $mapString;
	}
}
?>