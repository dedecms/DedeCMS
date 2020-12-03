<?php 
require_once(dirname(__FILE__)."/inc_arcpart_view.php");
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
	var $Fields;
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
 		$this->Fields = $this->TypeLink->TypeInfos;
 		$this->Fields['id'] = $typeid;
 		$this->Fields['position'] = $this->TypeLink->GetPositionLink(true);
 		$this->Fields['title'] = ereg_replace("[<>]"," / ",$this->TypeLink->GetPositionLink(false));
 		//设置一些全局参数的值
 		foreach($GLOBALS['PubFields'] as $k=>$v) $this->Fields[$k] = $v;
 		$this->Fields['rsslink'] = $GLOBALS['cfg_mainsite'].$GLOBALS['cfg_plus_dir']."/rss/".$this->TypeID.".xml";
 		
 		if($starttime==0) $this->StartTime = 0;
 		else $this->StartTime = GetMkTime($starttime);
 		
 		$this->PartView = new PartView($typeid);

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
 		@$this->PartView->Close();
 	}
 	//------------------
 	//统计列表里的记录
 	//------------------
 	function CountRecord()
 	{
 		global $cfg_list_son;
 		//统计数据库记录
 		$this->TotalResult = -1;
 		if(isset($GLOBALS['TotalResult'])) $this->TotalResult = $GLOBALS['TotalResult'];
 		if(isset($GLOBALS['PageNo'])) $this->PageNo = $GLOBALS['PageNo'];
 		else $this->PageNo = 1;
 		
 		if($this->TotalResult==-1)
 		{
 		  $addSql  = " arcrank > -1 ";
 		  
 		  if($cfg_list_son=='否') $addSql .= " And (typeid='".$this->TypeID."' or typeid2='".$this->TypeID."') ";
 		  else $addSql .= " And (".$this->TypeLink->GetSunID($this->TypeID,"#@__archives",$this->Fields['channeltype'])." Or #@__archives.typeid2='".$this->TypeID."') ";
 		  
 		  if($this->StartTime>0) $addSql .= " And senddate>'".$this->StartTime."'";
 		  $cquery = "Select count(*) as dd From #@__archives where $addSql";
 			$row = $this->dsql->GetOne($cquery);
 			if(is_array($row)) $this->TotalResult = $row['dd'];
 			else $this->TotalResult = 0;
 		}
 		
 		//初始化列表模板，并统计页面总数
 		$tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$this->TypeLink->TypeInfos['templist'];
 		$tempfile = str_replace("{tid}",$this->TypeID,$tempfile);
 		$tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 		if(!file_exists($tempfile)){
 	  	  $tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$GLOBALS['cfg_df_style']."/list_default.htm";
 	  }
 		if(!file_exists($tempfile)||!is_file($tempfile)){
 			echo "模板文件：'".$tempfile."' 不存在，无法解析文档！";
 			exit();
 		}
 		$this->dtp->LoadTemplate($tempfile);
 		$ctag = $this->dtp->GetTag("page");
 		if(!is_object($ctag)){ $ctag = $this->dtp->GetTag("list"); }
 		if(!is_object($ctag)) $this->PageSize = 20;
 		else{
 		  if($ctag->GetAtt("pagesize")!="") $this->PageSize = $ctag->GetAtt("pagesize");
      else $this->PageSize = 20;
    }
    $this->TotalPage = ceil($this->TotalResult/$this->PageSize);
 	}
 	//------------------
 	//列表创建HTML
 	//------------------
 	function MakeHtml($startpage=1,$makepagesize=0)
 	{
 		if(empty($startpage)) $startpage = 1;
 		//创建封面模板文件
 		if($this->TypeLink->TypeInfos['isdefault']==-1){
 			echo "这个类目是动态类目！";
 			return "";
 	  }
 	  //单独页面
 		else if($this->TypeLink->TypeInfos['ispart']>0){
 			$reurl = $this->MakePartTemplets();
 			$this->Close();
 			return $reurl;
 		}
    
 		$this->CountRecord();
 		//初步给固定值的标记赋值
 		$this->ParseTempletsFirst();
 		$totalpage = ceil($this->TotalResult/$this->PageSize);

 		if($totalpage==0) $totalpage = 1;
 		
 		CreateDir($this->Fields['typedir'],$this->Fields['siterefer'],$this->Fields['sitepath']);
 		
 		$murl = "";
 		
 		if($makepagesize>0) $endpage = $startpage+$makepagesize;
 		else $endpage = ($totalpage+1);
 		
 		if($endpage>($totalpage+1)) $endpage = $totalpage;
 		
 		for($this->PageNo=$startpage;$this->PageNo<$endpage;$this->PageNo++)
 		{
 		  $this->ParseDMFields($this->PageNo,1);
 	    $makeFile = $this->GetMakeFileRule($this->Fields['ID'],"list",$this->Fields['typedir'],"",$this->Fields['namerule2']);
 	    $makeFile = str_replace("{page}",$this->PageNo,$makeFile);
 	    $murl = $makeFile;
 	    if(!ereg("^/",$makeFile)) $makeFile = "/".$makeFile;
 	    $makeFile = $this->GetTruePath().$makeFile;
 	    $makeFile = ereg_replace("/{1,}","/",$makeFile);
 	    $murl = $this->GetTrueUrl($murl);
 	    $this->dtp->SaveTo($makeFile);
 	    echo "成功创建：<a href='$murl' target='_blank'>$murl</a><br/>";
 	  }
 	  if($startpage==1)
 	  {
 	    //如果列表启用封面文件，复制这个文件第一页
 	    if($this->TypeLink->TypeInfos['isdefault']==1 
 	      && $this->TypeLink->TypeInfos['ispart']==0)
 	    {
 	  	  $onlyrule = $this->GetMakeFileRule($this->Fields['ID'],"list",$this->Fields['typedir'],"",$this->Fields['namerule2']);
 	  	  $onlyrule = str_replace("{page}","1",$onlyrule);
 	  	  $list_1 = $this->GetTruePath().$onlyrule;
 	  	  $murl = $this->Fields['typedir']."/".$this->Fields['defaultname'];
 	  	  $indexname = $this->GetTruePath().$murl;
 	  	  echo "复制：$onlyrule 为 ".$this->Fields['defaultname']." <br />";
 	  	  copy($list_1,$indexname);
 	    }
 	  }
 		$this->Close();
 		return $murl;
 	}
 	//------------------
 	//显示列表
 	//------------------
 	function Display()
 	{
 		if($this->TypeLink->TypeInfos['ispart']>0){
 			$this->DisplayPartTemplets();
 			return ;
 		}
 		$this->CountRecord();
 		if((empty($this->PageNo) || $this->PageNo==1)
 		 && $this->TypeLink->TypeInfos['ispart']==1)
 		{
 			$tmpdir = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir'];
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->Fields['tempindex']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			$tempfile = $tmpdir."/".$tempfile;
 			if(!file_exists($tempfile)){
 	  	  $tempfile = $tmpdir."/".$GLOBALS['cfg_df_style']."/index_default.htm";
 	    }
 			$this->dtp->LoadTemplate($tempfile);
 		}
 		$this->ParseTempletsFirst();
 		$this->ParseDMFields($this->PageNo,0);
 	  $this->Close();
 		$this->dtp->Display();
 	}
 	//------------------
 	//创建单独模板页面
 	//------------------
 	function MakePartTemplets()
 	{
 		$nmfa = 0;
 		$tmpdir = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir'];
 		if($this->Fields['ispart']==1){
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->Fields['tempindex']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			$tempfile = $tmpdir."/".$tempfile;
 			if(!file_exists($tempfile)){
 	  	  $tempfile = $tmpdir."/".$GLOBALS['cfg_df_style']."/index_default.htm";
 	    }
 			$this->PartView->SetTemplet($tempfile);
 		}else if($this->Fields['ispart']==2){
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->Fields['tempone']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			if(is_file($tmpdir."/".$tempfile)) $this->PartView->SetTemplet($tmpdir."/".$tempfile);
 			else{ $this->PartView->SetTemplet("这是没有使用模板的单独页！","string"); $nmfa = "1";}
 		}
 		CreateDir($this->Fields['typedir']);
 		$makeUrl = $this->GetMakeFileRule($this->Fields['ID'],"index",$this->Fields['typedir'],$this->Fields['defaultname'],$this->Fields['namerule2']);
 		$makeUrl = ereg_replace("/{1,}","/",$makeUrl);
 		$makeFile = $this->GetTruePath().$makeUrl;
 		if($nmfa==0) $this->PartView->SaveToHtml($makeFile);
 		else{
 			if(!file_exists($makeFile)) $this->PartView->SaveToHtml($makeFile);
 		}
 		$this->Close();
 		return $this->GetTrueUrl($makeUrl);
 	}
 	//------------------
 	//显示单独模板页面
 	//------------------
 	function DisplayPartTemplets()
 	{
 		$nmfa = 0;
 		$tmpdir = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir'];
 		if($this->Fields['ispart']==1){
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->Fields['tempindex']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			$tempfile = $tmpdir."/".$tempfile;
 			if(!file_exists($tempfile)){
 	  	  $tempfile = $tmpdir."/".$GLOBALS['cfg_df_style']."/index_default.htm";
 	    }
 			$this->PartView->SetTemplet($tempfile);
 		}else if($this->Fields['ispart']==2){
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->Fields['tempone']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			if(is_file($tmpdir."/".$tempfile)) $this->PartView->SetTemplet($tmpdir."/".$tempfile);
 			else{ $this->PartView->SetTemplet("这是没有使用模板的单独页！","string"); $nmfa = 1; }
 		}
	  CreateDir($this->Fields['typedir']);
 		$makeUrl = $this->GetMakeFileRule($this->Fields['ID'],"index",$this->Fields['typedir'],$this->Fields['defaultname'],$this->Fields['namerule2']);
 		$makeFile = $this->GetTruePath().$makeUrl;
	  if($nmfa==0) $this->PartView->Display();
 		else{
 			if(!file_exists($makeFile)) $this->PartView->Display();
 			else include($makeFile);
 		}
	  $this->Close();
 	}
 	//----------------------------
 	//获得站点的真实根路径
 	//----------------------------
 	function GetTruePath(){
 		if($this->Fields['siterefer']==1) $truepath = ereg_replace("/{1,}","/",$GLOBALS["cfg_basedir"]."/".$this->Fields['sitepath']);
	  else if($this->Fields['siterefer']==2) $truepath = $this->Fields['sitepath'];
	  else $truepath = $GLOBALS["cfg_basedir"];
	  return $truepath;
 	}
 	//----------------------------
 	//获得真实连接路径
 	//----------------------------
 	function GetTrueUrl($nurl){
 		if($this->Fields['moresite']==1){ $nurl = ereg_replace("/$","",$this->Fields['siteurl']).$nurl; }
 		return $nurl;
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
 			 if($tagname=="field"){ //类别的指定字段
 					if(isset($this->Fields[$ctag->GetAtt('name')]))
 					  $this->dtp->Assign($tagid,$this->Fields[$ctag->GetAtt('name')]);
 					else
 					  $this->dtp->Assign($tagid,"");
 			 }
 			 else if($tagname=="onetype"||$tagname=="type"){
 				 $typeid = $ctag->GetAtt('typeid');
 				 if($typeid=="") $typeid = 0;
 				 if($typeid=="") $typeid = $this->TypeID;
 				 $this->dtp->Assign($tagid,
 				   $this->PartView->GetOneType($typeid,$ctag->GetInnerText())
 				 );
 			 }else if($tagname=="channel"){ //下级频道列表
 				  $typeid = -1;
 				  if($ctag->GetAtt("typeid")=="") $typeid=0;
 				  if($this->TypeID > 0 && $typeid!=0){
 				  	$typeid = $this->TypeID; $reid = $this->TypeLink->TypeInfos['reID'];
 				  }
 				  else{ $typeid = $ctag->GetAtt("typeid"); $reid=0; }
 				  
 				  $this->dtp->Assign($tagid,
 				      $this->TypeLink->GetChannelList(
 				          $typeid,$reid,$ctag->GetAtt("row"),
 				          $ctag->GetAtt("type"),$ctag->GetInnerText(),
 				          $ctag->GetAtt("col"),$ctag->GetAtt("tablewidth"),
 				          $ctag->GetAtt("currentstyle")
 				      )
 				  );
 			 }
 			 //热门关键字
 			 else if($tagname=="hotwords"){
 				 $this->dtp->Assign($tagid,
 				 GetHotKeywords($this->dsql,$ctag->GetAtt('num'),$ctag->GetAtt('subday'),$ctag->GetAtt('maxlength')));
 			 }
 			 //自定义标记
 			 else if($tagname=="mytag"){
 				 $this->dtp->Assign($tagid,
 				   $this->PartView->GetMyTag($this->TypeID,$ctag->GetAtt("name"),$ctag->GetAtt("ismake"))
 				 );
 			 }
 			 //广告代码
 			 else if($tagname=="myad"){
 				 $this->dtp->Assign($tagid,
 				   $this->PartView->GetMyAd($this->TypeID,$ctag->GetAtt("name"))
 				 );
 			 }
 			 //频道下级栏目文档列表
 			 else if($tagname=="channelartlist"){
 				 //类别ID
 				 if(trim($ctag->GetAtt('typeid'))=="" && $this->TypeID!=0){  $typeid = $this->TypeID;  }
 				 else{ $typeid = trim( $ctag->GetAtt('typeid') ); }
 				 $this->dtp->Assign($tagid,
 				     $this->PartView->GetChannelList($typeid,$ctag->GetAtt('col'),$ctag->GetAtt('tablewidth'),$ctag->GetInnerText())
 				 );
 			 }
 			 //投票
 			 else if($tagname=="vote"){
 				 $this->dtp->Assign($tagid,
				    $this->PartView->GetVote(
				      $ctag->GetAtt("id"),$ctag->GetAtt("lineheight"),
              $ctag->GetAtt("tablewidth"),$ctag->GetAtt("titlebgcolor"),
              $ctag->GetAtt("titlebackground"),$ctag->GetAtt("tablebgcolor")
            )
			   );
 			 }
 			 //友情链接
 			 //------------------
 			 else if($tagname=="friendlink"||$tagname=="flink")
 			 {
 				 $this->dtp->Assign($tagid,
 				    $this->PartView->GetFriendLink(
 				     $ctag->GetAtt("type"),$ctag->GetAtt("row"),$ctag->GetAtt("col"),
 				     $ctag->GetAtt("titlelen"),$ctag->GetAtt("tablestyle")
 				   )
 				 );
 			 }
 			 //站点新闻
 			 //---------------------
 			 else if($tagname=="mynews")
 			 {
 				 $this->dtp->Assign($tagid,
 				   $this->PartView->GetMyNews(
 				    $ctag->GetAtt("row"),$ctag->GetAtt("titlelen"),$ctag->GetInnerText()
 				   )
 				 );
 			 }
 			 //调用论坛主题
 			 //----------------
 			 else if($tagname=="loop")
 			 {
 				 $this->dtp->Assign($tagid,
				   $this->PartView->GetTable(
					   $ctag->GetAtt("table"),$ctag->GetAtt("row"),
					   $ctag->GetAtt("sort"),$ctag->GetAtt("if"),$ctag->GetInnerText()
					 )
			   );
 			 }
 			 else if($tagname=="arclist"||$tagname=="artlist"||$tagname=="likeart"||$tagname=="hotart"
 			 ||$tagname=="imglist"||$tagname=="imginfolist"||$tagname=="coolart"||$tagname=="specart")
 			 { 
 			 	  //特定的文章列表
 				  $channelid = $ctag->GetAtt("channelid");
 				  if($tagname=="imglist"||$tagname=="imginfolist"){ $listtype = "image"; }
 				  else if($tagname=="specart"){ $channelid = -1; $listtype=""; }
 				  else if($tagname=="coolart"){ $listtype = "commend"; }
 				  else{ $listtype = $ctag->GetAtt('type'); }
 				  
 				  //对相应的标记使用不同的默认innertext
 				  if(trim($ctag->GetInnerText())!="") $innertext = $ctag->GetInnerText();
 				  else if($tagname=="imglist") $innertext = GetSysTemplets("part_imglist.htm");
 				  else if($tagname=="imginfolist") $innertext = GetSysTemplets("part_imginfolist.htm");
 				  else $innertext = GetSysTemplets("part_arclist.htm");
 				  
 				  if($tagname=="hotart") $orderby = "click";
 				  else $orderby = $ctag->GetAtt('orderby');
 				  
 				  //兼容titlelength
 				  if($ctag->GetAtt('titlelength')!="") $titlelen = $ctag->GetAtt('titlelength');
 				  else $titlelen = $ctag->GetAtt('titlelen');
 				
 				  //兼容infolength
 				  if($ctag->GetAtt('infolength')!="") $infolen = $ctag->GetAtt('infolength');
 				  else $infolen = $ctag->GetAtt('infolen');
 				  
 				  $typeid = trim($ctag->GetAtt("typeid"));
 				  if(empty($typeid)) $typeid = $this->TypeID;
 				  
 				  $this->dtp->Assign($tagid,
 				      $this->PartView->GetArcList(
 				         $typeid,$ctag->GetAtt("row"),
 				         $ctag->GetAtt("col"),$titlelen,$infolen,
 				         $ctag->GetAtt("imgwidth"),$ctag->GetAtt("imgheight"),
 				         $listtype,$orderby,$ctag->GetAtt("keyword"),
 				         $innertext,$ctag->GetAtt("tablewidth"),
 				         0,"",$channelid,$ctag->GetAtt("limit"),
 				         $ctag->GetAtt("att"),$ctag->GetAtt("orderway"),$ctag->GetAtt("subday"),-1,$ctag->GetAtt("ismember")
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
 				         $ctag->GetAtt("tablewidth"),
 				         $ismake,
 				         $ctag->GetAtt("orderway")
 				       )
 				);
 			}
 			else if($ctag->GetName()=="pagelist"){
 				$list_len = trim($ctag->GetAtt("listsize"));
 				$ctag->GetAtt("listitem")=="" ? $listitem="info,index,pre,pageno,next,end,option" : $listitem=$ctag->GetAtt("listitem");
 				if($list_len=="") $list_len = 3;
 				if($ismake==0) $this->dtp->Assign($tagid,$this->GetPageListDM($list_len,$listitem));
 				else $this->dtp->Assign($tagid,$this->GetPageListST($list_len,$listitem));
 			}
 	  }
  }
 	//----------------
 	//获得要创建的文件名称规则
 	//----------------
 	function GetMakeFileRule($typeid,$wname,$typedir,$defaultname,$namerule2)
  {
	  $typedir = eregi_replace("{cmspath}",$GLOBALS['cfg_cmspath'],$typedir);
	  $typedir = ereg_replace("/{1,}","/",$typedir);
	  if($wname=="index")
	  {  return $typedir."/".$defaultname; }
	  else
	  {
	    $namerule2 = str_replace("{tid}",$typeid,$namerule2); 
			$namerule2 = str_replace("{typedir}",$typedir,$namerule2); 
	    return $namerule2;
	  }
  }
 	//----------------------------------
  //获得一个单列的文档列表
  //---------------------------------
  function GetArcList($limitstart=0,$row=10,$col=1,$titlelen=30,$infolen=250,
  $imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$innertext="",$tablewidth="100",$ismake=1,$orderWay='desc')
  {
    global $cfg_list_son;
    $typeid=$this->TypeID;
		if($row=="") $row = 10;
		if($limitstart=="") $limitstart = 0;
		if($titlelen=="") $titlelen = 100;
		if($infolen=="") $infolen = 250;
    if($imgwidth=="") $imgwidth = 120;
    if($imgheight=="") $imgheight = 120;
    if($listtype=="") $listtype = "all";
		if($orderby=="") $orderby="default";
		else $orderby=strtolower($orderby);
		if($orderWay=='') $orderWay = 'desc';
		$tablewidth = str_replace("%","",$tablewidth);
		if($tablewidth=="") $tablewidth=100;
		if($col=="") $col=1;
		$colWidth = ceil(100/$col); 
		$tablewidth = $tablewidth."%";
		$colWidth = $colWidth."%";
		$innertext = trim($innertext);
		if($innertext=="") $innertext = GetSysTemplets("list_fulllist.htm");
		//按不同情况设定SQL条件
		$orwhere = " arc.arcrank > -1 ";
		if($this->StartTime>0) $orwhere .= " And arc.senddate>'".$this->StartTime."'";
		//类别ID的条件
		
		if($cfg_list_son=='否') $orwhere .= " And (arc.typeid='".$this->TypeID."' or arc.typeid2='".$this->TypeID."') ";
		else $orwhere .= " And (".$this->TypeLink->GetSunID($this->TypeID,"arc",$this->Fields['channeltype'])." Or arc.typeid2='".$this->TypeID."') ";
		
		//排序方式
		$ordersql = "";
		if($orderby=="senddate") $ordersql=" order by arc.senddate $orderWay";
		else if($orderby=="pubdate") $ordersql=" order by arc.pubdate $orderWay";
    else if($orderby=="id") $ordersql="  order by arc.ID $orderWay";
    else if($orderby=="hot"||$orderby=="click") $ordersql = " order by arc.click $orderWay";
		else if($orderby=="lastpost") $ordersql = "  order by arc.lastpost $orderWay";
    else if($orderby=="postnum") $ordersql = "  order by arc.postnum $orderWay";
    else if($orderby=="rand") $ordersql = "  order by rand()";
		else $ordersql=" order by arc.sortrank $orderWay";
		
		//获得附加表的相关信息
		//-----------------------------
		$addtable  = $this->ChannelUnit->ChannelInfos['addtable'];
		if($addtable!=""){
			$addJoin = " left join $addtable on arc.ID = ".$addtable.".aid ";
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
		$query = "Select arc.ID,arc.title,arc.iscommend,arc.color,
		arc.typeid,arc.ismake,arc.money,arc.description,arc.shorttitle,
		arc.memberid,arc.writer,arc.postnum,arc.lastpost,
		arc.pubdate,arc.senddate,arc.arcrank,arc.click,arc.litpic,
		tp.typedir,tp.typename,tp.isdefault,tp.defaultname,
		tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl 
		$addField
		from #@__archives arc 
		left join #@__arctype tp on arc.typeid=tp.ID
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
           $GLOBALS['autoindex']++;
           //处理一些特殊字段                        
           $row['id'] =  $row['ID'];
           $row['arcurl'] = $this->GetArcUrl($row['id'],$row['typeid'],$row['senddate'],$row['title'],$row['ismake'],$row['arcrank'],$row['namerule'],$row['typedir'],$row['money']);                             
           $row['typeurl'] = $this->GetListUrl($row['typeid'],$row['typedir'],$row['isdefault'],$row['defaultname'],$row['ispart'],$row['namerule2'],"abc");
          
           if($ismake==0 && $GLOBALS['cfg_multi_site']=='是'){
           	 if($row["siteurl"]=="") $row["siteurl"] = $GLOBALS['cfg_mainsite'];
           	 if(!eregi("^http://",$row['picname'])){
           	 	  $row['litpic'] = $row['siteurl'].$row['litpic'];
           	 	  $row['picname'] = $row['litpic'];
           	 }
           }
           
           $row['description'] = cnw_left($row['description'],$infolen);
           if($row['litpic']=="") $row['litpic'] = $GLOBALS['cfg_plus_dir']."/img/dfpic.gif";
           $row['picname'] = $row['litpic']; 
           $row['info'] = $row['description'];
           $row['filename'] = $row['arcurl'];
           $row['stime'] = GetDateMK($row['pubdate']);
           $row['textlink'] = "<a href='".$row['filename']."' title='".str_replace("'","",$row['title'])."'>".$row['title']."</a>";
           
           if($row['typeid']!=$this->Fields['ID']){ $row['typelink'] = "<a href='".$row['typeurl']."'>[".$row['typename']."]</a>"; }
           else{ $row['typelink']=""; }
           
           $row['imglink'] = "<a href='".$row['filename']."'><img src='".$row['picname']."' border='0' width='$imgwidth' height='$imgheight' alt='".str_replace("'","",$row['title'])."'></a>";
           $row['image'] = "<img src='".$row['picname']."' border='0' width='$imgwidth' height='$imgheight' alt='".str_replace("'","",$row['title'])."'>";
           $row['phpurl'] = $GLOBALS['cfg_plus_dir'];
           $row['plusurl'] = $GLOBALS['cfg_plus_dir'];
 		       $row['templeturl'] = $GLOBALS['cfg_templets_dir'];
 		       $row['memberurl'] = $GLOBALS['cfg_member_dir'];
 		       $row['title'] = cn_substr($row['title'],$titlelen);
           if($row['color']!="") $row['title'] = "<font color='".$row['color']."'>".$row['title']."</font>";
           if($row['iscommend']==5||$row['iscommend']==16) $row['title'] = "<b>".$row['title']."</b>";
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
         }//if hasRow
         else{
         	 $artlist .= "";
         }
         if($col>1) $artlist .= "	</td>\r\n";
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
	function GetPageListST($list_len,$listitem="info,index,end,pre,next,pageno")
	{
		$prepage="";
		$nextpage="";
		$prepagenum = $this->PageNo-1;
		$nextpagenum = $this->PageNo+1;
		if($list_len==""||ereg("[^0-9]",$list_len)) $list_len=3;
		$totalpage = ceil($this->TotalResult/$this->PageSize);
		if($totalpage<=1 && $this->TotalResult>0) return "共1页/".$this->TotalResult."条记录"; 
		if($this->TotalResult == 0) return "共0页/".$this->TotalResult."条记录"; 
		$maininfo = " 共{$totalpage}页/".$this->TotalResult."条记录 ";
		$purl = $this->GetCurUrl();
		
		$tnamerule = $this->GetMakeFileRule($this->Fields['ID'],"list",$this->Fields['typedir'],$this->Fields['defaultname'],$this->Fields['namerule2']);
		$tnamerule = ereg_replace('^(.*)/','',$tnamerule);
		//获得上一页和主页的链接
		if($this->PageNo != 1){
			$prepage.="<a href='".str_replace("{page}",$prepagenum,$tnamerule)."'>上一页</a>\r\n";
			$indexpage="<a href='".str_replace("{page}",1,$tnamerule)."'>首页</a>\r\n";
		}else{ $indexpage="首页\r\n"; }	
		//下一页,未页的链接
		if($this->PageNo!=$totalpage && $totalpage>1){
			$nextpage.="<a href='".str_replace("{page}",$nextpagenum,$tnamerule)."'>下一页</a>\r\n";
			$endpage="<a href='".str_replace("{page}",$totalpage,$tnamerule)."'>末页</a>\r\n";
		}else{
			$endpage="末页\r\n";
		}
		//option链接
		$optionlen = strlen($totalpage);
		$optionlen = $optionlen*20+18;
		$optionlist = "<select name='sldd' style='width:$optionlen' onchange='location.href=this.options[this.selectedIndex].value;'>\r\n";
		for($mjj=1;$mjj<=$totalpage;$mjj++){
			if($mjj==$this->PageNo) $optionlist .= "<option value='".str_replace("{page}",$mjj,$tnamerule)."' selected>$mjj</option>\r\n";
		  else $optionlist .= "<option value='".str_replace("{page}",$mjj,$tnamerule)."'>$mjj</option>\r\n";
		}
		$optionlist .= "</select>";
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
		for($j;$j<=$total_list;$j++){
   		if($j==$this->PageNo) $listdd.= "$j\r\n";
   		else $listdd.="<a href='".str_replace("{page}",$j,$tnamerule)."'>[".$j."]</a>\r\n";
		}
		$plist = "";
		if(eregi('info',$listitem)) $plist .= $maininfo.' ';
		if(eregi('index',$listitem)) $plist .= $indexpage.' ';
		if(eregi('pre',$listitem)) $plist .= $prepage.' ';
		if(eregi('pageno',$listitem)) $plist .= $listdd.' ';
		if(eregi('next',$listitem)) $plist .= $nextpage.' ';
		if(eregi('end',$listitem)) $plist .= $endpage.' ';
		if(eregi('option',$listitem)) $plist .= $optionlist;
		return $plist;
	}
  //---------------------------------
  //获取动态的分页列表
  //---------------------------------
	function GetPageListDM($list_len,$listitem="index,end,pre,next,pageno")
	{
		$prepage="";
		$nextpage="";
		$prepagenum = $this->PageNo-1;
		$nextpagenum = $this->PageNo+1;
		if($list_len==""||ereg("[^0-9]",$list_len)) $list_len=3;
		$totalpage = ceil($this->TotalResult/$this->PageSize);
		if($totalpage<=1 && $this->TotalResult>0) return "共1页/".$this->TotalResult."条记录"; 
		if($this->TotalResult == 0) return "共0页/".$this->TotalResult."条记录"; 
		$maininfo = "<td style='padding-right:6px'>共{$totalpage}页/".$this->TotalResult."条记录</td>";
		
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
		}else{ 
   			$j=1;
   			if($total_list>$totalpage) $total_list=$totalpage;
		}
		for($j;$j<=$total_list;$j++){
   		if($j==$this->PageNo) $listdd.= "<td>$j&nbsp;</td>\r\n";
   		else $listdd.="<td><a href='".$purl."PageNo=$j'>[".$j."]</a>&nbsp;</td>\r\n";
		}
		$plist  =  "<table border='0' cellpadding='0' cellspacing='0'>\r\n";
		$plist .= "<tr align='center' style='font-size:10pt'>\r\n";
		$plist .= "<form name='pagelist' action='".$this->GetCurUrl()."'>$hidenform";
		$plist .= $maininfo.$indexpage.$prepage.$listdd.$nextpage.$endpage;
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
 	function GetListUrl($typeid,$typedir,$isdefault,$defaultname,$ispart,$namerule2,$siteurl=""){
  	return GetTypeUrl($typeid,MfTypedir($typedir),$isdefault,$defaultname,$ispart,$namerule2,$siteurl);
  }
 	//--------------------------
 	//获得一个指定档案的链接
 	//--------------------------
 	function GetArcUrl($aid,$typeid,$timetag,$title,$ismake=0,$rank=0,$namerule="",$artdir="",$money=0){
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
		}else{ $nowurl = $_SERVER["PHP_SELF"]; }
		return $nowurl;
	}
}//End Class
?>