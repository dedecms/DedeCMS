<?php 
require_once(dirname(__FILE__)."/config_base.php");
require_once(dirname(__FILE__)."/pub_dedetag.php");
require_once(dirname(__FILE__)."/inc_typelink.php");
require_once(dirname(__FILE__)."/inc_channel_unit_functions.php");
/******************************************************
//Copyright 2005-2007 by DedeCms.com itprato
//本类的用途是用于浏览频道RSS或对RSS生成静态文件
//最后修改日期 2007-3-27 By dedecms 用户 baijixing
******************************************************/
@set_time_limit(0);
class RssView
{
	var $dsql;
	var $TypeID;
	var $TypeLink;
	var $TypeFields;
	var $MaxRow;
	var $dtp;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($typeid,$max_row=50)
 	{
 		$this->TypeID = $typeid;
 		$this->dtp = new DedeTagParse();
 		$templetfiles = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/plus/rss.htm";
 		$this->dtp->LoadTemplate($templetfiles);
 		$this->dsql = new DedeSql(false);
 		$this->TypeLink = new TypeLink($typeid);
 		$this->TypeFields = $this->TypeLink->TypeInfos;
 		$this->MaxRow = $max_row;
 		$this->TypeFields['title'] = $this->TypeLink->GetPositionLink(false);
 		$this->TypeFields['title'] = ereg_replace("[<>]"," / ",$this->TypeFields['title']);
 		$this->TypeFields['typelink'] = $this->TypeLink->GetOneTypeUrl($this->TypeFields);
 		$this->TypeFields['powerby'] = $GLOBALS['cfg_powerby'];
 		$this->TypeFields['adminemail'] = $GLOBALS['cfg_adminemail'];
 		foreach($this->TypeFields as $k=>$v){
 		  $this->TypeFields[$k] = htmlspecialchars($v);
 		}
 		$this->ParseTemplet();
  }
  //php4构造函数
 	//---------------------------
 	function RssView($typeid,$max_row=50){
 		$this->__construct($typeid,$max_row);
 	}
 	//---------------------------
 	//关闭相关资源
 	//---------------------------
 	function Close()
 	{
 		$this->dsql->Close();
 		$this->TypeLink->Close();
 	}
 	//------------------
 	//显示列表
 	//------------------
 	function Display()
 	{
 		$this->dtp->Display();
 	}
 	//------------------
 	//开始创建列表
 	//------------------
 	function MakeRss()
 	{
 		$murl = $GLOBALS['cfg_plus_dir']."/rss/".$this->TypeID.".xml";
 		$mfile = $GLOBALS['cfg_basedir'].$murl;
 		$this->dtp->SaveTo($mfile);
 		return $murl;
 	}
 	//------------------
 	//解析模板
 	//------------------
 	function ParseTemplet()
 	{
 		foreach($this->dtp->CTags as $tid => $ctag)
 		{
 			if($ctag->GetName()=="field")
 			{ $this->dtp->Assign($tid,$this->TypeFields[$ctag->GetAtt('name')]); }
 			else if($ctag->GetName()=="rssitem"){
 				$this->dtp->Assign($tid,
 				  $this->GetArcList($ctag->GetInnerText())
 				);
 			}
 		}
 	}
 	//----------------------------------
  //获得文档列表
  //---------------------------------
  function GetArcList($innertext="")
  {
    $typeid=$this->TypeID;
		$innertext = trim($innertext);
		if($innertext=="") $innertext = GetSysTemplets("rss.htm");
		$orwhere = " #@__archives.arcrank > -1 ";
		$orwhere .= " And (".$this->TypeLink->GetSunID($this->TypeID,"#@__archives",$this->TypeFields['channeltype'])." Or #@__archives.typeid2='".$this->TypeID."') ";
		$ordersql=" order by #@__archives.senddate desc";
		//----------------------------
		$query = "Select #@__archives.ID,#@__archives.title,#@__archives.source,#@__archives.writer,#@__archives.typeid,#@__archives.ismake,#@__archives.money,
		#@__archives.description,#@__archives.pubdate,#@__archives.senddate,#@__archives.arcrank,#@__archives.click,
		#@__archives.litpic,#@__arctype.typedir,#@__arctype.typename,#@__arctype.isdefault,
		#@__arctype.defaultname,#@__arctype.namerule,#@__arctype.namerule2,#@__arctype.ispart,
		#@__arctype.siteurl 
		from #@__archives 
		left join #@__arctype on #@__archives.typeid=#@__arctype.ID
		where $orwhere $ordersql limit 0,".$this->MaxRow;
		$this->dsql->SetQuery($query);
		$this->dsql->Execute("al");
    $artlist = "";
    $dtp2 = new DedeTagParse();
 		$dtp2->SetNameSpace("field","[","]");
    $dtp2->LoadSource($innertext);
    while($row = $this->dsql->GetArray("al"))
    {
      //处理一些特殊字段
      if($row["litpic"]=="") $row["litpic"] = $GLOBALS["cfg_plus_dir"]."/img/dfpic.gif";
      $row["picname"] = $row["litpic"];
      $row["arcurl"] = $this->GetArcUrl($row["ID"],$row["typeid"],$row["senddate"],$row["title"],
                        $row["ismake"],$row["arcrank"],$row["namerule"],$row["typedir"],$row["money"]);
      $row["typeurl"] = $this->GetListUrl($row["typeid"],$row["typedir"],$row["isdefault"],$row["defaultname"],$row["ispart"],$row["namerule2"]);
      $row["info"] = $row["description"];
      $row["filename"] = $row["arcurl"];
      $row["stime"] = GetDateMK($row["pubdate"]);
      $row["image"] = "<img src='".$row["picname"]."' border='0'>";
      $row["fullurl"] = $row['siteurl'].$row["arcurl"];
      $row["phpurl"] = $GLOBALS["cfg_plus_dir"];
 		  $row["templeturl"] = $GLOBALS["cfg_templets_dir"];
 		  if($row["source"]=="") $row["source"] = $GLOBALS['cfg_webname'];
 		  if($row["writer"]=="") $row["writer"] = "秩名";
 		  
 		  foreach($row as $k=>$v){
 		  	$row[$k] = htmlspecialchars($v);
 		  }
      //---------------------------
      if(is_array($dtp2->CTags)){
       	foreach($dtp2->CTags as $k=>$ctag){
       		if(isset($row[$ctag->GetName()])) $dtp2->Assign($k,$row[$ctag->GetName()]);
       		else $dtp2->Assign($k,"");
       	}
      }
      $artlist .= $dtp2->GetResult()."\r\n";
     }
     $this->dsql->FreeResult("al");
     return $artlist;
  }
 	//--------------------------
 	//获得一个指定的频道的链接
 	//--------------------------
 	function GetListUrl($typeid,$typedir,$isdefault,$defaultname,$ispart,$namerule2)
  {
  	return GetTypeUrl($typeid,MfTypedir($typedir),$isdefault,$defaultname,$ispart,$namerule2);
  }
 	//--------------------------
 	//获得一个指定档案的链接
 	//--------------------------
 	function GetArcUrl($aid,$typeid,$timetag,$title,$ismake=0,$rank=0,$namerule="",$artdir="",$money=0)
  {
  	return GetFileUrl($aid,$typeid,$timetag,$title,$ismake,$rank,$namerule,$artdir,$money);
  }
}//End Class
?>