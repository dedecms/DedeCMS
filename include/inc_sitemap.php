<?php 
//class SiteMap
//--------------------------------
require_once(dirname(__FILE__)."/config_base.php");
require_once(dirname(__FILE__)."/inc_channel_unit_functions.php");
class SiteMap
{
	var $dsql;
	var $artDir;
	var $baseDir;
	//-------------
	//php5构造函数
	//-------------
	function __construct()
 	{
		$this->idCounter = 0;
		$this->artDir = $GLOBALS['cfg_arcdir'];
		$this->baseDir = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_basedir'];
		$this->idArrary = "";
		$this->dsql = new DedeSql(false);
  }
	function SiteMap()
	{
		$this->__construct();
	}
	//------------------
	//清理类
	//------------------
	function Close()
	{
		$this->dsql->Close();
	}
	//---------------------------
	//获取网站地图
	//$maptype = "site" 或 "rss"
	//---------------------------
	function GetSiteMap($maptype="site")
	{
		$mapString = "<style>.mdiv{ margin:0px;margin-bottom:10px;padding:3px; }</style><div>";
		if($maptype=="rss") $this->dsql->SetQuery("Select ID,typedir,isdefault,defaultname,typename,ispart,namerule2 From #@__arctype where ishidden<>1 And reID=0 And ispart<2 order by sortrank");
		else $this->dsql->SetQuery("Select ID,typedir,isdefault,defaultname,typename,ispart,namerule2 From #@__arctype where reID=0 And ishidden<>1 order by sortrank");
		$this->dsql->Execute(0);
		while($row=$this->dsql->GetObject(0))
		{	 
			if($maptype=="site") $typelink = GetTypeUrl($row->ID,MfTypedir($row->typedir),$row->isdefault,$row->defaultname,$row->ispart,$row->namerule2);
			else $typelink = $GLOBALS['cfg_plus_dir']."/rss/".$row->ID.".xml";
      $mapString .= "<div><a href='$typelink'><b>".$row->typename."</b></a></div>\r\n";
			$mapString .= $this->LogicListAllSunType($row->ID,$maptype,0);
		}
		$mapString .= "</div>";
		return $mapString;
	}
	//获得子类目的递归调用
	function LogicListAllSunType($ID,$maptype,$pd)
	{
		$fid = $ID;
		$mapString = "";
		$pd = $pd + 15;
		if($maptype=="rss") $this->dsql->SetQuery("Select ID,typedir,isdefault,defaultname,typename,ispart,namerule2 From #@__arctype where reID='".$ID."' And ishidden<>1 And ispart<2 order by sortrank");
		else $this->dsql->SetQuery("Select ID,typedir,isdefault,defaultname,typename,ispart,namerule2 From #@__arctype where reID='".$ID."' And ishidden<>1 order by sortrank");
		$this->dsql->Execute($fid);
		$mapString .= "<div style='margin-left:{$pd}px'>";
		while($row=$this->dsql->GetObject($fid))
		{
			 if($maptype=="site") $typelink = GetTypeUrl($row->ID,MfTypedir($row->typedir),$row->isdefault,$row->defaultname,$row->ispart,$row->namerule2);
			 else $typelink = $GLOBALS['cfg_plus_dir']."/rss/".$row->ID.".xml";
			 
			 $lastLink = " <a href='$typelink'>".$row->typename."</a> ";
			 $mapString .= $lastLink;
			 $mok = $this->LogicListAllSunType($row->ID,$maptype,$pd);
			 if(ereg("<a",$mok)){
			 	  //$mapString = str_replace($lastLink,"<div style='margin-left:{$pd}px'>$lastLink",$mapString);
			 	  $mapString .= $mok;
			 }
		}
		$mapString .= "</div>\r\n";
		return $mapString;
	}
}
?>