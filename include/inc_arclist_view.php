<?
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/pub_db_mysql.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/inc_channel_unit.php");
require_once(dirname(__FILE__)."/../include/inc_arcpart_view.php");
/******************************************************
//Copyright 2004-2006 by DedeCms.com itprato
//本类的用途是用于浏览频道列表或对内容列表生成HTML
******************************************************/
@set_time_limit(0);
class ListView
{
	var $dsql;
	var $dtp;
	var $dtp2;
	var $TypeID;
	var $TypeLink;
	var $PageNo;
	var $TotalPage;
	var $TotalResult;
	var $PageSize;
	var $ChannelUnit;
	var $ListType;
	var $TempInfos;
	var $TypeFields;
	var $PartView;
	var $StartTime;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($typeid,$starttime=0)
 	{
 		$this->TypeID = $typeid;
 		$this->dsql = new DedeSql(false);
 		$this->dtp = new DedeTagParse();
 		$this->dtp->SetNameSpace("dede","{","}");
 		$this->dtp2 = new DedeTagParse();
 		$this->dtp2->SetNameSpace("field","[","]");
 		$this->TypeLink = new TypeLink($typeid);
 		$this->ChannelUnit = new ChannelUnit($this->TypeLink->TypeInfos['channeltype']);
 		$this->TypeFields = $this->TypeLink->TypeInfos;
 		$this->TypeFields['position'] = $this->TypeLink->GetPositionLink(true);
 		$this->TypeFields['title'] = $this->TypeLink->GetPositionLink(false);
 		$this->TypeFields['title'] = ereg_replace("[<>]"," / ",$this->TypeFields['title']);
 		$this->TypeFields['phpurl'] = $GLOBALS['cfg_plus_dir'];
 		$this->TypeFields['templeturl'] = $GLOBALS['cfg_templets_dir'];
 		$this->TypeFields['memberurl'] = $GLOBALS['cfg_member_dir'];
 		$this->TypeFields['powerby'] = $GLOBALS['cfg_powerby'];
 		$this->TypeFields['indexurl'] = $GLOBALS['cfg_indexurl'];
 		$this->TypeFields['indexname'] = $GLOBALS['cfg_indexname'];
 		$this->TypeFields['specurl'] = $GLOBALS['cfg_special'];
 		$this->TypeFields['webname'] = $GLOBALS["cfg_webname"];
 		$this->TypeFields['rsslink'] = $GLOBALS['cfg_extend_dir']."/rss/".$this->TypeID.".xml";
 		
 		if($starttime==0) $this->StartTime = 0;
 		else{
 			$this->StartTime = GetMkTime($starttime);
 		}
 		
 		$this->PartView = new PartView($typeid);

 		if($this->TypeLink->TypeInfos['ispart']!=2)
 		{
 			$this->CountRecord();
 			$tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$this->TypeLink->TypeInfos['templist'];
 		  $tempfile = str_replace("{tid}",$this->TypeID,$tempfile);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 		  if(!file_exists($tempfile)){
 	  	  $tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/default/list_default.htm";
 	    }
 		  if(!file_exists($tempfile)||!is_file($tempfile)){
 			  echo "模板文件：'".$tempfile."' 不存在，无法解析文档！";
 			  exit();
 		  }
 		  $this->dtp->LoadTemplate($tempfile);
 		  $this->TempInfos['tags'] = $this->dtp->CTags;
 		  $this->TempInfos['source'] = $this->dtp->SourceString;
 		  $ctag = $this->dtp->GetTag("page");
 		  if(!is_object($ctag)) $this->PageSize = 20;
 		  else{
 		    if($ctag->GetAtt("pagesize")!="") $this->PageSize = $ctag->GetAtt("pagesize");
        else $this->PageSize = 20;
      }
      $this->TotalPage = ceil($this->TotalResult/$this->PageSize);
 		}
  }
  //php4构造函数
 	//---------------------------
 	function ListView($typeid,$starttime=0){
 		$this->__construct($typeid,$starttime);
 	}
 	//---------------------------
 	//关闭相关资源
 	//---------------------------
 	function Close()
 	{
 		@$this->dsql->Close();
 		@$this->TypeLink->Close();
 		@$this->ChannelUnit->Close();
 	}
 	//------------------
 	//统计列表里的记录
 	//------------------
 	function CountRecord()
 	{
 		$this->TotalResult = -1;
 		if(isset($GLOBALS['TotalResult'])) $this->TotalResult = $GLOBALS['TotalResult'];
 		if(isset($GLOBALS['PageNo'])) $this->PageNo = $GLOBALS['PageNo'];
 		else $this->PageNo = 1;
 		
 		$addSql  = " arcrank > -1 ";
 		$addSql .= " And (".$this->TypeLink->GetSunID($this->TypeID,"#@__archives",$this->TypeFields['channeltype'])." Or #@__archives.typeid2='".$this->TypeID."') ";
 		if($this->StartTime>0) $addSql .= " And senddate>'".$this->StartTime."'";
 		
 		if($this->TotalResult==-1)
 		{
 			$cquery = "Select count(*) as dd From #@__archives where $addSql";
 			$row = $this->dsql->GetOne($cquery);
 			if(is_array($row)) $this->TotalResult = $row['dd'];
 			else $this->TotalResult = 0;
 		}
 	}
 	//------------------
 	//显示列表
 	//------------------
 	function Display()
 	{
 		if($this->TypeLink->TypeInfos['ispart']==1
 		   ||$this->TypeLink->TypeInfos['ispart']==2)
 		{
 			$this->DisplayPartTemplets();
 			return;
 		}
 		$this->ParseTempletsFirst();
 		$this->ParseDMFields($this->PageNo,0);
 	  $this->Close();
 		$this->dtp->Display();
 	}
 	//------------------
 	//开始创建列表
 	//------------------
 	function MakeHtml()
 	{
 		//创建封面模板文件
 		if($this->TypeLink->TypeInfos['isdefault']==-1)
 		{
 			echo "这个类目是动态类目！";
 			return "";
 	  }
 		else if($this->TypeLink->TypeInfos['ispart']==1
 		||$this->TypeLink->TypeInfos['ispart']==2)
 		{
 			$reurl = $this->MakePartTemplets();
 			$this->Close();
 			return $reurl;
 		}
 		//初步给固定值的标记赋值
 		$this->ParseTempletsFirst();
 		$totalpage = ceil($this->TotalResult/$this->PageSize);
 		if($totalpage==0) $totalpage = 1;
 		CreateDir($this->TypeFields['typedir']);
 		$murl = "";
 		for($this->PageNo=1;$this->PageNo<=$totalpage;$this->PageNo++)
 		{
 		  $this->ParseDMFields($this->PageNo,1);
 	    $makeFile = $this->GetMakeFileRule("list",$this->TypeFields['typedir'],"",$this->TypeFields['namerule2']);
 	    $makeFile = str_replace("{page}",$this->PageNo,$makeFile);
 	    $murl = $makeFile;
 	    $makeFile = $GLOBALS['cfg_basedir'].$makeFile;
 	    $this->dtp->SaveTo($makeFile);
 	    echo "成功创建：<a href='$murl' target='_blank'>$murl</a><br/>";
 	  }
 	  //如果列表启用封面文件，复制这个文件第一页
 	  if($this->TypeLink->TypeInfos['isdefault']==1 
 	    && $this->TypeLink->TypeInfos['ispart']==0)
 	  {
 	  	$onlyrule = $this->GetMakeFileRule("list",$this->TypeFields['typedir'],"",$this->TypeFields['namerule2']);
 	  	$onlyrule = str_replace("{page}","1",$onlyrule);
 	  	$list_1 = $GLOBALS['cfg_basedir'].$onlyrule;
 	  	$murl = $this->TypeFields['typedir']."/".$this->TypeFields['defaultname'];
 	  	$indexname = $GLOBALS['cfg_basedir'].$murl;
 	  	echo "复制：$onlyrule 为 ".$this->TypeFields['defaultname']." <br />";
 	  	copy($list_1,$indexname);
 	  }
 		$this->Close();
 		return $murl;
 	}
 	//------------------
 	//创建单独模板页面
 	//------------------
 	function MakePartTemplets()
 	{
 		$nmfa = 0;
 		$tmpdir = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir'];
 		if($this->TypeFields['ispart']==1)
 		{
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->TypeFields['tempindex']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			$tempfile = $tmpdir."/".$tempfile;
 			if(!file_exists($tempfile)){
 	  	  $tempfile = $tmpdir."/default/index_default.htm";
 	    }
 			$this->PartView->SetTemplet($tempfile);
 		}
 		else if($this->TypeFields['ispart']==2)
 		{
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->TypeFields['tempone']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			if(is_file($tmpdir."/".$tempfile)) $this->PartView->SetTemplet($tmpdir."/".$tempfile);
 			else{ $this->PartView->SetTemplet("这是没有使用模板的单独页！","string"); $nmfa = "1";}
 		}
 		CreateDir($this->TypeFields['typedir']);
 		$makeUrl = $this->GetMakeFileRule("index",$this->TypeFields['typedir'],$this->TypeFields['defaultname'],$this->TypeFields['namerule2']);
 		$makeUrl = ereg_replace("/{1,}","/",$makeUrl);
 		$makeFile = $GLOBALS['cfg_basedir'].$makeUrl;
 		if($nmfa==0) $this->PartView->SaveToHtml($makeFile);
 		else{
 			if(!file_exists($makeFile)) $this->PartView->SaveToHtml($makeFile);
 		}
 		$this->Close();
 		return $makeUrl;
 	}
 	//------------------
 	//显示单独模板页面
 	//------------------
 	function DisplayPartTemplets()
 	{
 		$nmfa = 0;
 		$tmpdir = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir'];
 		if($this->TypeFields['ispart']==1)
 		{
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->TypeFields['tempindex']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			$tempfile = $tmpdir."/".$tempfile;
 			if(!file_exists($tempfile)){
 	  	  $tempfile = $tmpdir."/default/index_default.htm";
 	    }
 			$this->PartView->SetTemplet($tempfile);
 		}
 		else if($this->TypeFields['ispart']==2)
 		{
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->TypeFields['tempone']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			if(is_file($tmpdir."/".$tempfile)) $this->PartView->SetTemplet($tmpdir."/".$tempfile);
 			else{ $this->PartView->SetTemplet("这是没有使用模板的单独页！","string"); $nmfa = 1; }
 		}
	  CreateDir($this->TypeFields['typedir']);
 		$makeUrl = $this->GetMakeFileRule("index",$this->TypeFields['typedir'],$this->TypeFields['defaultname'],$this->TypeFields['namerule2']);
 		$makeFile = $GLOBALS['cfg_basedir'].$makeUrl;
	  if($nmfa==0) $this->PartView->Display();
 		else{
 			if(!file_exists($makeFile)) $this->PartView->Display();
 			else include($makeFile);
 		}
	  $this->Close();
 	}
 	//--------------------------------
 	//解析模板，对固定的标记进行初始给值
 	//--------------------------------
 	function ParseTempletsFirst()
 	{
 	  //解析模板
 		//-------------------------
 		if(is_array($this->dtp->CTags))
 		{
 		 foreach($this->dtp->CTags as $tagid=>$ctag){
 			 $tagname = $ctag->GetName();
 			 if($tagname=="field") //类别的指定字段
 			 {
 					if(isset($this->TypeFields[$ctag->GetAtt('name')]))
 					  $this->dtp->Assign($tagid,$this->TypeFields[$ctag->GetAtt('name')]);
 					else
 					  $this->dtp->Assign($tagid,"");
 			 }
 			 else if($tagname=="channel")//下级频道列表
 			 {
 				  if($this->TypeID>0){
 				  	$typeid = $this->TypeID; $reid = $this->TypeLink->TypeInfos['reID'];
 				  }
 				  else{ $typeid = 0; $reid=0; }
 				  
 				  $this->dtp->Assign($tagid,
 				      $this->TypeLink->GetChannelList($typeid,
 				          $reid,
 				          $ctag->GetAtt("row"),
 				          $ctag->GetAtt("type"),
 				          $ctag->GetInnerText()
 				      )
 				  );
 			 }
 			 //自定义标记
 			 //-------------------
 			 else if($ctag->GetName()=="mytag")
 			 {
 				 $this->dtp->Assign($tagid,
 				   $this->PartView->GetMyTag(
 				     $this->TypeID,
 				     $ctag->GetAtt("name"),
 				     $ctag->GetAtt("ismake")
 				   )
 				 );
 			 }
 			 else if($tagname=="arclist"||$tagname=="artlist"||$tagname=="likeart"||$tagname=="hotart"
 			 ||$tagname=="imglist"||$tagname=="imginfolist"||$tagname=="coolart"||$tagname=="specart")
 			 { 
 			 	  $listtype = $ctag->GetAtt('type');
 			 	  //特定的文章列表
 				  $channelid = $ctag->GetAtt("channelid");
 				  if($tagname=="imglist"||$tagname=="imginfolist"){ $listtype = "image"; }
 				  else if($tagname=="specart"){ $channelid = -1; }
 				  else if($tagname=="coolart"){ $listtype = "commend"; }
 				  else{ $listtype = $ctag->GetAtt('type'); }
 				  
 				  //对相应的标记使用不同的默认innertext
 				  if(trim($ctag->GetInnerText())!="") $innertext = $ctag->GetInnerText();
 				  else if($tagname=="imglist"){
 				  	$innertext = GetSysTemplets("part_imglist.htm");
 				  	$listtype = 'image';
 				  }
 				  else if($tagname=="imginfolist"){
 				  	$innertext = GetSysTemplets("part_imginfolist.htm");
 				  	$listtype = 'image';
 				  }
 				  else $innertext = GetSysTemplets("part_arclist.htm");
 				  
 				  if($tagname=="hotart") $orderby = "click";
 				  else $orderby = $ctag->GetAtt('orderby');
 				  
 				  //兼容titlelength
 				  if($ctag->GetAtt('titlelength')!="") $titlelen = $ctag->GetAtt('titlelength');
 				  else $titlelen = $ctag->GetAtt('titlelen');
 				
 				  //兼容infolength
 				  if($ctag->GetAtt('infolength')!="") $infolen = $ctag->GetAtt('infolength');
 				  else $infolen = $ctag->GetAtt('infolen');
 				  
 				  if(trim($ctag->GetAtt('typeid'))==""){  $typeid = $this->TypeID;  }
 				  else{ $typeid = trim( $ctag->GetAtt('typeid') ); }
 				  
 				  $this->dtp->Assign($tagid,
 				      $this->PartView->GetArcList(
 				         $typeid,
 				         $ctag->GetAtt("row"),
 				         $ctag->GetAtt("col"),
 				         $titlelen,
 				         $infolen,
 				         $ctag->GetAtt("imgwidth"),
 				         $ctag->GetAtt("imgheight"),
 				         $listtype,
 				         $orderby,
 				         $ctag->GetAtt("keyword"),
 				         $innertext,
 				         $ctag->GetAtt("tablewidth"),
 				         0,
 				         "",
 				         $channelid,
 				         $ctag->GetAtt("limit")
 				      )
 				  );
 			  }
 			}//结束模板循环
 		}
 	}
 	//--------------------------------
 	//解析模板，对内容里的变动进行赋值
 	//--------------------------------
 	function ParseDMFields($PageNo,$ismake=1)
 	{
 		foreach($this->dtp->CTags as $tagid=>$ctag){
 			if($ctag->GetName()=="list"){
 				$limitstart = ($this->PageNo-1) * $this->PageSize;
 				$row = $this->PageSize;
 				if(trim($ctag->GetInnerText())==""){ $InnerText = GetSysTemplets("list_fulllist.htm"); }
 				else{ $InnerText = trim($ctag->GetInnerText()); }
 				$this->dtp->Assign($tagid,
 				      $this->GetArcList(
 				         $limitstart,
 				         $row,
 				         $ctag->GetAtt("col"),
 				         $ctag->GetAtt("titlelen"),
 				         $ctag->GetAtt("infolen"),
 				         $ctag->GetAtt("imgwidth"),
 				         $ctag->GetAtt("imgheight"),
 				         $ctag->GetAtt("listtype"),
 				         $ctag->GetAtt("orderby"),
 				         $InnerText,
 				         $ctag->GetAtt("tablewidth")
 				       )
 				);
 			}
 			else if($ctag->GetName()=="pagelist"){
 				$list_len = trim($ctag->GetAtt("listsize"));
 				if($list_len=="") $list_len = 3;
 				if($ismake==0) $this->dtp->Assign($tagid,$this->GetPageListDM($list_len));
 				else $this->dtp->Assign($tagid,$this->GetPageListST($list_len));
 			}
 	  }
  }
 	//----------------
 	//获得要创建的文件名称规则
 	//----------------
 	function GetMakeFileRule($wname,$typedir,$defaultname,$namerule2)
  {
	  if($wname=="index")
	    return $typedir."/".$defaultname;
	  else
	    return $typedir."/".$namerule2;
  }
 	//----------------------------------
  //获得一个单列的文档列表
  //---------------------------------
  function GetArcList($limitstart=0,$row=10,$col=1,$titlelen=30,$infolen=250,
  $imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$innertext="",$tablewidth="100")
  {
    $typeid=$this->TypeID;
		if($row=="") $row = 10;
		if($limitstart=="") $limitstart = 0;
		if($titlelen=="") $titlelen = 30;
		if($infolen=="") $infolen = 250;
    if($imgwidth=="") $imgwidth = 120;
    if($imgheight=="") $imgheight = 120;
    if($listtype=="") $listtype = "all";
		if($orderby=="") $orderby="default";
		else $orderby=strtolower($orderby);
		$tablewidth = str_replace("%","",$tablewidth);
		if($tablewidth=="") $tablewidth=100;
		if($col=="") $col=1;
		$colWidth = ceil(100/$col); 
		$tablewidth = $tablewidth."%";
		$colWidth = $colWidth."%";
		$innertext = trim($innertext);
		if($innertext=="") $innertext = GetSysTemplets("list_fulllist.htm");
		//按不同情况设定SQL条件
		$orwhere = " #@__archives.arcrank > -1 ";
		if($this->StartTime>0) $orwhere .= " And #@__archives.senddate>'".$this->StartTime."'";
		//类别ID的条件
		$orwhere .= " And (".$this->TypeLink->GetSunID($this->TypeID,"#@__archives",$this->TypeFields['channeltype'])." Or #@__archives.typeid2='".$this->TypeID."') ";
		//排序方式
		$ordersql = "";
		if($orderby=="senddate") $ordersql=" order by #@__archives.senddate desc";
		else if($orderby=="pubdate") $ordersql=" order by #@__archives.pubdate desc";
    else if($orderby=="id") $ordersql="  order by #@__archives.ID desc";
    else if($orderby=="click"||$orderby=="hot") $ordersql="  order by #@__archives.click desc";
		else $ordersql=" order by #@__archives.sortrank desc";
		
		//获得附加表的相关信息
		//-----------------------------
		$addtable  = $this->ChannelUnit->ChannelInfos['addtable'];
		if($addtable!=""){
			$addJoin = " left join $addtable on #@__archives.ID = ".$addtable.".aid ";
			$addField = "";
			$fields = explode(",",$this->ChannelUnit->ChannelInfos['listadd']);
			foreach($fields as $k=>$v){ $nfields[$v] = $k; }
			foreach($this->ChannelUnit->ChannelFields as $k=>$arr){
				if(isset($nfields[$k])){
				  if($arr['rename']!="")
				  	$addField .= ",".$addtable.".".$k." as ".$arr['rename'];
				  else
				  	$addField .= ",".$addtable.".".$k;
				}
			}
		}
		else{
			$addField = "";
			$addJoin = "";
		}
		//
		//----------------------------
		$query = "Select #@__archives.ID,#@__archives.writer,#@__archives.source,#@__archives.title,#@__archives.iscommend,#@__archives.color,
		#@__archives.typeid,#@__archives.ismake,#@__archives.money,#@__archives.description,
		#@__archives.pubdate,#@__archives.senddate,#@__archives.arcrank,#@__archives.click,#@__archives.litpic,
		#@__arctype.typedir,#@__arctype.typename,#@__arctype.isdefault,#@__arctype.defaultname,
		#@__arctype.namerule,#@__arctype.namerule2,#@__arctype.ispart 
		$addField
		from #@__archives 
		left join #@__arctype on #@__archives.typeid=#@__arctype.ID
		$addJoin
		where $orwhere $ordersql limit $limitstart,$row";
		$this->dsql->SetQuery($query);
		$this->dsql->Execute("al");
    $artlist = "";
    if($col>1) $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
    $this->dtp2->LoadSource($innertext);
    $GLOBALS['autoindex'] = 0;
    for($i=0;$i<$row;$i++)
		{
       if($col>1) $artlist .= "<tr>\r\n";
       for($j=0;$j<$col;$j++)
			 {
         if($col>1) $artlist .= "<td width='$colWidth'>\r\n";
         if($row = $this->dsql->GetArray("al"))
         {
           //处理一些特殊字段
           $row["description"] = cnw_left($row["description"],$infolen);
           $row["id"] =  $row["ID"];
           if($row["litpic"]=="") $row["litpic"] = $GLOBALS["cfg_plus_dir"]."/img/dfpic.gif";
           $row["picname"] = $row["litpic"];
           $row["arcurl"] = $this->GetArcUrl($row["id"],$row["typeid"],$row["senddate"],$row["title"],
                        $row["ismake"],$row["arcrank"],$row["namerule"],$row["typedir"],$row["money"]);
           $row["typeurl"] = $this->GetListUrl($row["typeid"],$row["typedir"],$row["isdefault"],$row["defaultname"],$row["ispart"],$row["namerule2"]);
           $row["info"] = $row["description"];
           $row["filename"] = $row["arcurl"];
           $row["stime"] = GetDateMK($row["pubdate"]);
           $row["textlink"] = "<a href='".$row["filename"]."'>".$row["title"]."</a>";
           if($row['typeid']!=$this->TypeFields['ID'])
           { $row["typelink"] = "[<a href='".$row["typeurl"]."'>".$row["typename"]."</a>]"; }
           else
           { $row["typelink"]=""; }
           $row["imglink"] = "<a href='".$row["filename"]."'><img src='".$row["picname"]."' border='0' width='$imgwidth' height='$imgheight'></a>";
           $row["image"] = "<img src='".$row["picname"]."' border='0' width='$imgwidth' height='$imgheight'>";
           $row["phpurl"] = $GLOBALS["cfg_plus_dir"];
 		       $row["templeturl"] = $GLOBALS["cfg_templets_dir"];
 		       $row["memberurl"] = $GLOBALS["cfg_member_dir"];
 		       $row["title"] = cn_substr($row["title"],$titlelen);
           if($row["color"]!="") $row["title"] = "<font color='".$row["color"]."'>".$row["title"]."</font>";
           if($row["iscommend"]==5||$row["iscommend"]==16) $row["title"] = "<b>".$row["title"]."</b>";
           //编译附加表里的数据
           foreach($row as $k=>$v){
 		  	      if(ereg("[A-Z]",$k)) $row[strtolower($k)] = $v;
 		       }
           foreach($this->ChannelUnit->ChannelFields as $k=>$arr){
 		  	      if(isset($row[$k])) $row[$k] = $this->ChannelUnit->MakeField($k,$row[$k]);
 		  	   }
           //---------------------------
           if(is_array($this->dtp2->CTags)){
       	     foreach($this->dtp2->CTags as $k=>$ctag){
       		 	   if(isset($row[$ctag->GetName()])) $this->dtp2->Assign($k,$row[$ctag->GetName()]);
       		 	   else $this->dtp2->Assign($k,"");
       	    }
           }
           $artlist .= $this->dtp2->GetResult();
           $GLOBALS['autoindex']++;
         }//if hasRow
         else{
         	 $artlist .= "";
         }
         if($col>1) $artlist .= "	</td>\r\n";
         $GLOBALS['autoindex']++;
       }//Loop Col
       if($col>1) $i += $col - 1;
       if($col>1) $artlist .= "	</tr>\r\n";
     }//Loop Line
     if($col>1) $artlist .= "</table>\r\n";
     $this->dsql->FreeResult("al");
     return $artlist;
  }
  //---------------------------------
  //获取静态的分页列表
  //---------------------------------
	function GetPageListST($list_len)
	{
		$prepage="";
		$nextpage="";
		$prepagenum = $this->PageNo-1;
		$nextpagenum = $this->PageNo+1;
		if($list_len==""||ereg("[^0-9]",$list_len)) $list_len=3;
		$totalpage = ceil($this->TotalResult/$this->PageSize);
		if($totalpage<=1 && $this->TotalResult>0) return "共1页/".$this->TotalResult."条记录"; 
		if($this->TotalResult == 0) return "共0页/".$this->TotalResult."条记录"; 
		$purl = $this->GetCurUrl();
		
		$tnamerule = $this->GetMakeFileRule("",$this->TypeFields['typedir'],$this->TypeFields['defaultname'],$this->TypeFields['namerule2']);
		
		//获得上一页和下一页的链接
		if($this->PageNo != 1){
			$prepage.="<a href='".str_replace("{page}",$prepagenum,$tnamerule)."'>上一页</a>\r\n";
			$indexpage="<a href='".str_replace("{page}",1,$tnamerule)."'>首页</a>\r\n";
		}
		else{
			$indexpage="首页\r\n";
		}	
		//
		if($this->PageNo!=$totalpage && $totalpage>1){
			$nextpage.="<a href='".str_replace("{page}",$nextpagenum,$tnamerule)."'>下一页</a>\r\n";
			$endpage="<a href='".str_replace("{page}",$totalpage,$tnamerule)."'>末页</a>\r\n";
		}
		else{
			$endpage="末页\r\n";
		}
		//获得数字链接
		$listdd="";
		$total_list = $list_len * 2 + 1;
		if($this->PageNo >= $total_list) {
      $j = $this->PageNo-$list_len;
      $total_list = $this->PageNo+$list_len;
      if($total_list>$totalpage) $total_list=$totalpage;
		}	
		else{ 
   		$j=1;
   		if($total_list>$totalpage) $total_list=$totalpage;
		}
		for($j;$j<=$total_list;$j++)
		{
   		if($j==$this->PageNo) $listdd.= "$j\r\n";
   		else $listdd.="<a href='".str_replace("{page}",$j,$tnamerule)."'>[".$j."]</a>\r\n";
		}
		$plist = $indexpage.$prepage.$listdd.$nextpage.$endpage;
		return $plist;
	}
  //---------------------------------
  //获取动态的分页列表
  //---------------------------------
	function GetPageListDM($list_len)
	{
		$prepage="";
		$nextpage="";
		$prepagenum = $this->PageNo-1;
		$nextpagenum = $this->PageNo+1;
		if($list_len==""||ereg("[^0-9]",$list_len)) $list_len=3;
		$totalpage = ceil($this->TotalResult/$this->PageSize);
		if($totalpage<=1 && $this->TotalResult>0) return "共1页/".$this->TotalResult."条记录"; 
		if($this->TotalResult == 0) return "共0页/".$this->TotalResult."条记录"; 
		
		$purl = $this->GetCurUrl();
		$geturl = "typeid=".$this->TypeID."&TotalResult=".$this->TotalResult."&";
		$hidenform = "<input type='hidden' name='typeid' value='".$this->TypeID."'>\r\n";
		$hidenform .= "<input type='hidden' name='TotalResult' value='".$this->TotalResult."'>\r\n";
		
		$purl .= "?".$geturl;
		
		//获得上一页和下一页的链接
		if($this->PageNo != 1){
			$prepage.="<td width='50'><a href='".$purl."PageNo=$prepagenum'>上一页</a></td>\r\n";
			$indexpage="<td width='30'><a href='".$purl."PageNo=1'>首页</a></td>\r\n";
		}
		else{
			$indexpage="<td width='30'>首页</td>\r\n";
		}	
		
		if($this->PageNo!=$totalpage && $totalpage>1){
			$nextpage.="<td width='50'><a href='".$purl."PageNo=$nextpagenum'>下一页</a></td>\r\n";
			$endpage="<td width='30'><a href='".$purl."PageNo=$totalpage'>末页</a></td>\r\n";
		}
		else{
			$endpage="<td width='30'>末页</td>\r\n";
		}
		//获得数字链接
		$listdd="";
		$total_list = $list_len * 2 + 1;
		if($this->PageNo >= $total_list) {
    		$j = $this->PageNo-$list_len;
    		$total_list = $this->PageNo+$list_len;
    		if($total_list>$totalpage) $total_list=$totalpage;
		}	
		else{ 
   			$j=1;
   			if($total_list>$totalpage) $total_list=$totalpage;
		}
		for($j;$j<=$total_list;$j++)
		{
   		if($j==$this->PageNo) $listdd.= "<td>$j&nbsp;</td>\r\n";
   		else $listdd.="<td><a href='".$purl."PageNo=$j'>[".$j."]</a>&nbsp;</td>\r\n";
		}
		$plist  =  "<table border='0' cellpadding='0' cellspacing='0'>\r\n";
		$plist .= "<tr align='center' style='font-size:10pt'>\r\n";
		$plist .= "<form name='pagelist' action='".$this->GetCurUrl()."'>$hidenform";
		$plist .= $indexpage.$prepage.$listdd.$nextpage.$endpage;
		if($totalpage>$total_list){
			$plist.="<td width='36'><input type='text' name='PageNo' style='width:30;height:18' value='".$this->PageNo."'></td>\r\n";
			$plist.="<td width='30'><input type='submit' name='plistgo' value='GO' style='width:24;height:18;font-size:9pt'></td>\r\n";
		}
		$plist .= "</form>\r\n</tr>\r\n</table>\r\n";
		return $plist;
	}
 	//--------------------------
 	//获得一个指定的频道的链接
 	//--------------------------
 	function GetListUrl($typeid,$typedir,$isdefault,$defaultname,$ispart,$namerule2)
  {
  	return GetTypeUrl($typeid,$typedir,$isdefault,$defaultname,$ispart,$namerule2);
  }
 	//--------------------------
 	//获得一个指定档案的链接
 	//--------------------------
 	function GetArcUrl($aid,$typeid,$timetag,$title,$ismake=0,$rank=0,$namerule="",$artdir="",$money=0)
  {
  	return GetFileUrl($aid,$typeid,$timetag,$title,$ismake,$rank,$namerule,$artdir,$money);
  }
  //---------------
  //获得当前的页面文件的url
  //----------------
  function GetCurUrl()
	{
		if(!empty($_SERVER["REQUEST_URI"])){
			$nowurl = $_SERVER["REQUEST_URI"];
			$nowurls = explode("?",$nowurl);
			$nowurl = $nowurls[0];
		}
		else
		{ $nowurl = $_SERVER["PHP_SELF"]; }
		return $nowurl;
	}
}//End Class
?>