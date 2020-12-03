<?php
require_once(dirname(__FILE__)."/inc_arcpart_view.php");
require_once(dirname(__FILE__)."/inc_pubtag_make.php");
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
	var $pageno;
	var $TotalPage;
	var $totalresult;
	var $PageSize;
	var $ChannelUnit;
	var $ListType;
	var $Fields;
	var $PartView;
	var $StartTime;
	var $maintable;
	var $addtable;
	var $areas;
	var $areaid;
	var $areaid2;
	var $sectors;
	var $sectorid;
	var $sectorid2;
	var $smalltypes;
	var $smalltypeid;
	var $topareas;
	var $subareas;
	var $mysmalltypes;
	var $TempletsFile;
	var $addSql;
	var $hasDmCache;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($typeid,$starttime=0,$areaid=0,$areaid2=0,$sectorid=0,$sectorid2=0,$smalltypeid=0)
 	{
 		$this->areaid = $areaid;
 		$this->areaid2 = $areaid2;
 		$this->sectorid = $sectorid;
 		$this->sectorid2 = $sectorid2;
 		$this->smalltypeid = $smalltypeid;
 		$this->TypeID = $typeid;
 		$this->dsql = new DedeSql(false);
 		$this->dtp = new DedeTagParse();
 		$this->dtp->SetNameSpace("dede","{","}");
 		$this->dtp2 = new DedeTagParse();
 		$this->dtp2->SetNameSpace("field","[","]");
 		$this->TypeLink = new TypeLink($typeid);
 		$this->ChannelUnit = new ChannelUnit($this->TypeLink->TypeInfos['channeltype']);

 		$this->maintable = $this->ChannelUnit->ChannelInfos['maintable'];
 		$this->topareas = $this->subareas = $this->areas = $this->sectors = $this->smalltypes = array();
 		$this->areas[0] = $this->sectors[0] = $this->smalltypes[0] = '不限';
		$this->topareas[0] = array("id"=>0 , "name"=>'不限');
 		$this->addtable = $this->ChannelUnit->ChannelInfos['addtable'];
 		$this->Fields = $this->TypeLink->TypeInfos;
 		
 		$this->hasDmCache = false;
 		
 		$this->Fields['id'] = $typeid;
 		$this->Fields['position'] = $this->TypeLink->GetPositionLink(true);
 		$this->Fields['title'] = ereg_replace("[<>]"," / ",$this->TypeLink->GetPositionLink(false));
 		
 		//设置一些全局参数的值
 		foreach($GLOBALS['PubFields'] as $k=>$v) $this->Fields[$k] = $v;
 		$this->Fields['rsslink'] = $GLOBALS['cfg_mainsite'].$GLOBALS['cfg_plus_dir']."/rss/".$this->TypeID.".xml";
 		
 		if($starttime==0) $this->StartTime = 0;
 		else $this->StartTime = GetMkTime($starttime);

    if($this->TypeLink->TypeInfos['ispart']<=2)
    {
 		    $this->PartView = new PartView($typeid);
 		    $this->CountRecord();
 		}
  }
  //php4构造函数
 	//---------------------------
 	function ListView($typeid,$starttime=0,$areaid=0,$areaid2=0,$sectorid=0,$sectorid2=0,$smalltypeid=0){
 		$this->__construct($typeid,$starttime,$areaid,$areaid2,$sectorid,$sectorid2,$smalltypeid);
 	}
 	
 	//
 	function LoadDmCatCache()
 	{
 		//载入地区、行业、小分类数据表
 		$this->dsql->Execute('ar',"select * from #@__area ");
 		while($row=$this->dsql->GetArray('ar'))
 		{
 				if(!empty($this->areaid2) && $row['id'] == $this->areaid2){
 					 $this->areaid = $row['reid'];
 				}
 				if($row['reid'] == 0){
 					 $this->topareas[] = $row;
 				}else{
 					 $this->subareas[$row['reid']][] = $row;
 				}
 				$this->areas[$row['id']] = $row['name'];
 		}
 		
 	  $this->dsql->Execute('ar',"select * from #@__sectors");
 	  while($row=$this->dsql->GetArray('ar')){
 			$this->sectors[$row['id']] = $row['name'];
 		}

 		$this->dsql->Execute('ar',"select * from #@__smalltypes");
 		$this->mysmalltypes = array();
 		$mysmalltypesarray = explode(',',$this->Fields['smalltypes']);
 		$this->mysmalltypes[0] = array("id"=>0, "name"=>'不限');
 		while($row=$this->dsql->GetArray('ar'))
 		{
 				if(@in_array($row['id'], $mysmalltypesarray)){
 					$this->mysmalltypes[] = $row;
 				}
 				$this->smalltypes[$row['id']] = $row['name'];
 		}
 		$this->hasDmCache = true;
 	}
 
 	//---------------------------
 	//关闭相关资源
 	//---------------------------
 	function Close()
 	{
 		$this->dsql->Close();
 		$this->TypeLink->Close();
 		$this->ChannelUnit->Close();
 		if(is_object($this->PartView)) $this->PartView->Close();
 	}
 	//------------------
 	//统计列表里的记录
 	//------------------
 	function CountRecord()
 	{
 		global $cfg_list_son;
 		//统计数据库记录
 		$this->totalresult = -1;
 		if(isset($GLOBALS['totalresult'])) $this->totalresult = intval($GLOBALS['totalresult']);
 		if(isset($GLOBALS['pageno'])) $this->pageno = intval($GLOBALS['pageno']);
 		else $this->pageno = 1;
 		
 		//分析条件
	  $this->addSql  = " arc.arcrank > -1 ";
		
		if($cfg_list_son=='N'){
				$this->addSql .= " And (arc.typeid='".$this->TypeID."' or arc.typeid2='".$this->TypeID."') ";
		}else{
				$idlist = $this->TypeLink->GetSunID($this->TypeID,'',$this->Fields['channeltype'],true);
				if(ereg(',',$idlist)){	 
					$this->addSql .= " And (arc.typeid in ($idlist) Or arc.typeid2='{$this->TypeID}') ";
				}else{  
					$this->addSql .= " And (arc.typeid='{$this->TypeID}' Or arc.typeid2='{$this->TypeID}') ";
				}
		}
		
		if($this->areaid2 != 0){
				$this->addSql .= "and arc.areaid2=$this->areaid2 ";
		}else if($this->areaid != 0){
				$this->addSql .= "and arc.areaid=$this->areaid ";
    }
    
		if($this->sectorid2 != 0){
				$this->addSql .= "and arc.sectorid2=$this->sectorid2 ";
		}else if($this->sectorid != 0){
				$this->addSql .= "and arc.sectorid=$this->sectorid ";
		}
		
		if($this->smalltypeid != 0){
				$this->addSql .= "and arc.smalltypeid=$this->smalltypeid ";
		}
		
		if($this->StartTime>0){
				$this->addSql .= " And arc.senddate>'".$this->StartTime."'";
		}
		
		if($this->totalresult==-1)
 		{
		   $cquery = "Select count(*) as dd From `{$this->maintable}` arc where ".$this->addSql;
		   $row = $this->dsql->GetOne($cquery);
		   $this->totalresult = $row['dd'];
		}
 		//初始化列表模板，并统计页面总数
 		$tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$this->TypeLink->TypeInfos['templist'];
 		$tempfile = str_replace("{tid}",$this->TypeID,$tempfile);
 		$tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
		if(!file_exists($tempfile)){
			$tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$GLOBALS['cfg_df_style']."/list_article.htm";
		}
 		if(!file_exists($tempfile)||!is_file($tempfile)){
 			echo "模板文件：'".$tempfile."' 不存在，无法解析文档！";
 			exit();
 		}
 		$this->dtp->LoadTemplate($tempfile);
 		$this->TempletsFile = ereg_replace("^".$GLOBALS['cfg_basedir'],'',$tempfile);
 		$ctag = $this->dtp->GetTag('page');
 		if(!is_object($ctag)){ $ctag = $this->dtp->GetTag('list'); }
 		if(!is_object($ctag)) $this->PageSize = 20;
 		else{
 		  if($ctag->GetAtt("pagesize")!='') $this->PageSize = $ctag->GetAtt('pagesize');
      else $this->PageSize = 20;
    }
    $this->TotalPage = ceil($this->totalresult/$this->PageSize);
 	}
 	//------------------
 	//列表创建HTML
 	//------------------
 	function MakeHtml($startpage=1,$makepagesize=0)
 	{
 		if(empty($startpage)) $startpage = 1;
 		//创建封面模板文件
 		if($this->TypeLink->TypeInfos['isdefault']==-1){
 			echo '这个类目是动态类目！';
 			return '';
 	  }
 	  //单独页面
 		else if($this->TypeLink->TypeInfos['ispart']>0)
 		{
 			$reurl = $this->MakePartTemplets();
 			return $reurl;
 		}
 		//跳转网址
 		else if($this->TypeLink->TypeInfos['ispart']>2){
 			echo "这个类目是跳转网址！";
 			return $this->TypeLink->TypeInfos['typedir'];
 		}

 		//$this->CountRecord();
 		//初步给固定值的标记赋值
 		$this->ParseTempletsFirst();
 		$totalpage = ceil($this->totalresult/$this->PageSize);

 		if($totalpage==0) $totalpage = 1;

 		CreateDir($this->Fields['typedir'],$this->Fields['siterefer'],$this->Fields['sitepath']);

 		$murl = "";

 		if($makepagesize>0) $endpage = $startpage+$makepagesize;
 		else $endpage = ($totalpage+1);

 		if($endpage>($totalpage+1)) $endpage = $totalpage;

 		$ttmk = 0;
 		$rs = '';
 		for($this->pageno=$startpage;$this->pageno<$endpage;$this->pageno++)
 		{
 		  $ttmk++;
 		  $this->ParseDMFields($this->pageno,1);
 	    $makeFile = $this->GetMakeFileRule($this->Fields['ID'],"list",$this->Fields['typedir'],"",$this->Fields['namerule2']);
 	    $makeFile = str_replace("{page}",$this->pageno,$makeFile);
 	    $murl = $makeFile;
 	    if(!ereg("^/",$makeFile)) $makeFile = "/".$makeFile;
 	    $makeFile = $this->GetTruePath().$makeFile;
 	    $makeFile = ereg_replace("/{1,}","/",$makeFile);
 	    $murl = $this->GetTrueUrl($murl);
 	    $this->dtp->SaveTo($makeFile);
 	    $rs .= "<br/><a href='$murl' target='_blank'>$murl</a>";
 	  }
 	  echo "共创建：($ttmk) 文件 $rs";
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
 	  	  copy($list_1,$indexname);
 	  	  echo "<br>复制：$onlyrule 为 ".$this->Fields['defaultname'];
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
 		//ispart = 3 跳转网址
 		if($this->TypeLink->TypeInfos['ispart']>2)
 		{
 			$this->Close();
 			header("location:".$this->TypeLink->TypeInfos['typedir']);
 			exit();
 		}
 		//ispart = 1 板块或 2 单独页面
 		else if($this->TypeLink->TypeInfos['ispart']>0)
 		{
 			$this->DisplayPartTemplets();
 			return '';
 		}
 		
 		//ispart = 0 正常列表
 		if((empty($this->pageno) || $this->pageno==1)
 		 && $this->TypeLink->TypeInfos['ispart']==1)
 		{
 			$tmpdir = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir'];
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->Fields['tempindex']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			$tempfile = $tmpdir."/".$tempfile;
 			if(!file_exists($tempfile)){
 	  	  $tempfile = $tmpdir."/".$GLOBALS['cfg_df_style']."/index_article.htm";
 	    }
 			$this->dtp->LoadTemplate($tempfile);
 			$this->TempletsFile = ereg_replace("^".$GLOBALS['cfg_basedir'],'',$tempfile);
 		}
 		$this->LoadDmCatCache();
 		$this->ParseTempletsFirst();
 		$this->ParseDMFields($this->pageno,0);
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
 	  	  $tempfile = $tmpdir."/".$GLOBALS['cfg_df_style']."/index_article.htm";
 	    }
 			$this->PartView->SetTemplet($tempfile);
 		}else if($this->Fields['ispart']==2){
 			$tempfile = str_replace("{tid}",$this->TypeID,$this->Fields['tempone']);
 		  $tempfile = str_replace("{cid}",$this->ChannelUnit->ChannelInfos['nid'],$tempfile);
 			if(is_file($tmpdir."/".$tempfile)) $this->PartView->SetTemplet($tmpdir."/".$tempfile);
 			else{ $this->PartView->SetTemplet("这是没有使用模板的单独页！","string"); $nmfa = "1";}
 		}else if($this->Fields['ispart']==3){
 			return '';
 		}
 		CreateDir($this->Fields['typedir']);
 		$makeUrl = $this->GetMakeFileRule($this->Fields['ID'],"index",$this->Fields['typedir'],$this->Fields['defaultname'],$this->Fields['namerule2']);
 		$makeUrl = ereg_replace("/{1,}","/",$makeUrl);
 		$makeFile = $this->GetTruePath().$makeUrl;
 		if($nmfa==0) $this->PartView->SaveToHtml($makeFile);
 		else{
 			if(!file_exists($makeFile)) $this->PartView->SaveToHtml($makeFile);
 		}
 		//$this->Close();
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
 	  	  $tempfile = $tmpdir."/".$GLOBALS['cfg_df_style']."/index_article.htm";
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
 	   //对公用标记的解析，这里对对象的调用均是用引用调用的，因此运算后会自动改变传递的对象的值
 	   MakePublicTag($this,$this->dtp,$this->PartView,$this->TypeLink,$this->TypeID,0,0);
 	}
 	//--------------------------------
 	//解析模板，对内容里的变动进行赋值
 	//--------------------------------
 	function ParseDMFields($pageno,$ismake=1)
 	{
 		foreach($this->dtp->CTags as $tagid=>$ctag){
 			if($ctag->GetName()=="list"){
 				$limitstart = ($this->pageno-1) * $this->PageSize;
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
 			else if($ctag->GetName()=="area"){
 				$areaid = trim($ctag->GetAtt('areaid'));

 				if(empty($areaid)){
 					$areaid = $this->areaid;
 				}
 				$areaid2 = trim($ctag->GetAtt('areaid2'));
 				if(empty($areaid2)){
 					$areaid2 = $this->areaid2;
 				}
 				$sub = trim($ctag->GetAtt('sub'));
 				if(empty($sub)){
 					$sub = 0;
 				}else{
 					$sub = 1;
 				}
 				$this->dtp->Assign($tagid, $this->getarea($areaid, $areaid2,$sub));
 			}
 			else if($ctag->GetName()=="smalltype"){

 				$this->dtp->Assign($tagid, $this->getsmalltype());
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
//获取小分类数据
function getsmalltype()
{
	$str = '';
	if($this->mysmalltypes){
		//print_r($this->mysmalltypes);
		$str = '<div class="c1"><strong>分类：</strong>';
		foreach($this->mysmalltypes as $mysmalltype){
			if($mysmalltype['id'] == $this->smalltypeid){
				$str .= '<strong style="color:green">'.$mysmalltype['name'].'</strong> ';
				continue;
			}
			$str .= '<a href="list.php?tid='.$this->TypeID;

			if($this->areaid2){
				$str .= '&amp;areaid2='.$this->areaid2;
			}elseif($this->areaid){
				$str .= '&amp;areaid='.$this->areaid;
			}
			$str .= '&amp;smalltypeid='.$mysmalltype['id'].'">'.$mysmalltype['name']."</a>\n";

		}
		$str .= '</div>';
	}
	return $str;
}




//获得地区数据
function getarea($areaid, $areaid2,$sub=0)
{
	$str = '';
	$topareadata = '';
	foreach($this->topareas as $toparea){
		if($sub){
			$topareadata .= '<option value="'.$toparea['id'].'">'.$toparea['name']."</option>\n";
			continue;
		}
		if($toparea['id'] == $this->areaid){
			$str .= '<strong style="color:green">'.$toparea['name'].'</strong> ';
			continue;
		}
		$str .= '<a href="list.php?tid='.$this->TypeID;
		if($this->smalltypeid){
			$str .= '&amp;smalltypeid='.$this->smalltypeid;
		}
		$str .= '&amp;areaid='.$toparea['id'].'">'.$toparea['name'].'</a> ';

	}
	$str1 = '';
	if($areaid && is_array($this->subareas[$areaid])){
		
		foreach($this->subareas[$areaid] as $subarea){
		if($subarea['id'] == $this->areaid2){
			$str1 .= '<strong style="color:green">'.$subarea['name'].'</strong> ';
			continue;
		}
			$str1 .= '<a href="list.php?tid='.$this->TypeID;
			if($this->smalltypeid){
				$str1 .= '&amp;smalltypeid='.$this->smalltypeid;
			}
			$str1 .= '&amp;areaid2='.$subarea['id'].'">'.$subarea['name'].'</a> ';
		}
	}
	if($sub)
	{
		return $topareadata;
	}else{

		return '<div class="c1"><strong>地区：</strong>'.$str.'</div><div class="c2">'.$str1.'</div>';
	}
}


	//----------------------------------
	//获得一个单列的文档列表
	//---------------------------------
	function GetArcList($limitstart=0,$row=10,$col=1,$titlelen=30,$infolen=250,
	$imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$innertext="",$tablewidth="100",$ismake=1,$orderWay='desc')
	{
		global $cfg_list_son;
		$t1 = ExecTime();
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

			$orwhere = $this->addSql;

			//排序方式
			if($orderby=="senddate") $ordersql=" order by arc.senddate $orderWay";
			elseif($orderby=="pubdate") $ordersql=" order by arc.pubdate $orderWay";
			elseif($orderby=="id") $ordersql="  order by arc.ID $orderWay";
			elseif($orderby=="hot"||$orderby=="click") $ordersql = " order by arc.click $orderWay";
			elseif($orderby=="lastpost") $ordersql = "  order by arc.lastpost $orderWay";
			elseif($orderby=="postnum") $ordersql = "  order by arc.postnum $orderWay";
			elseif($orderby=="digg") $ordersql = "  order by arc.digg $orderWay";
		  elseif($orderby=="diggtime") $ordersql = "  order by arc.diggtime $orderWay";
			else $ordersql=" order by arc.sortrank $orderWay";

			//获得附加表的相关信息
			//-----------------------------
			$addtable  = $this->ChannelUnit->ChannelInfos['addtable'];
			$addfields = trim($this->ChannelUnit->ChannelInfos['listadd']);
			if($addtable!="" && $addfields!="")
			{
				$addJoin = " left join `$addtable` addt on addt.aid = arc.ID ";
				$addField = "";
				$fields = explode(",",$addfields);
				foreach($fields as $k=>$v){
					$addField .= ",addt.{$v}";
				}
			}else
			{
				$addField = "";
				$addJoin = "";
			}

			$query = "Select arc.*,
			tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl
			$addField
			from `{$this->maintable}` arc
			left join #@__arctype tp on arc.typeid=tp.ID
			$addJoin
			where $orwhere $ordersql limit $limitstart,$row";

			$this->dtp2->LoadSource($innertext);
			if(!is_array($this->dtp2->CTags)) return '';
			
			$this->dsql->Execute("al",$query);
			
			$t2 = ExecTime();
			
			$artlist = "";
			if($col>1) $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
			
			$GLOBALS['autoindex'] = 0;
			for($i=0;$i<$row;$i++)
			{
				if($col>1) $artlist .= "<tr>\r\n";
				for($j=0;$j<$col;$j++)
				{
					if($col>1) $artlist .= "<td width='$colWidth'>\r\n";
					if($row = $this->dsql->GetArray("al",MYSQL_ASSOC))
					{
						$GLOBALS['autoindex']++;
						//处理一些特殊字段
						//if()
						$row['id'] =  $row['ID'];
						$row['arcurl'] = $this->GetArcUrl($row['id'],$row['typeid'],$row['senddate'],$row['title'],$row['ismake'],$row['arcrank'],$row['namerule'],$row['typedir'],$row['money']);
						$row['typeurl'] = $this->GetListUrl($row['typeid'],$row['typedir'],$row['isdefault'],$row['defaultname'],$row['ispart'],$row['namerule2'],"abc");

						if($ismake==0 && $GLOBALS['cfg_multi_site']=='Y')
						{
							if($row["siteurl"]=="") $row["siteurl"] = $GLOBALS['cfg_mainsite'];
							if(!eregi("^http://",$row['picname'])){
								$row['litpic'] = $row['siteurl'].$row['litpic'];
								$row['picname'] = $row['litpic'];
							}
						}

						$row['description'] = cn_substr($row['description'],$infolen);
						if($row['litpic']=="") $row['litpic'] = $GLOBALS['cfg_plus_dir']."/img/dfpic.gif";
						$row['picname'] = $row['litpic'];
						$row['info'] = $row['description'];
						$row['filename'] = $row['arcurl'];
						$row['stime'] = GetDateMK($row['pubdate']);
						
						if($this->hasDmCache){
						  $row['areaidname'] = $row['areaid2name'] = $row['sectoridname'] = $row['sectorid2name'] =$row['smalltypeidname'] = '';
						  $row['areaidname'] = $this->areas[$row['areaid']];
						  $row['areaid2name'] = $this->areas[$row['areaid2']];
						  $row['sectoridname'] = $this->sectors[$row['sectorid']];
						  $row['sectorid2name'] = $this->sectors[$row['sectorid2']];
						  $row['smalltypeidname'] = $this->smalltypes[$row['smalltypeid']];
						}

						$row['textlink'] = "<a href='".$row['filename']."' title='".str_replace("'","",$row['title'])."'>".$row['title']."</a>";

						if($row['typeid'] != $this->Fields['ID']){
							$row['typelink'] = "<a href='".$row['typeurl']."'>[".$row['typename']."]</a>";
						}else{
							$row['typelink']= '';
						}
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
						foreach($row as $k=>$v){ $row[strtolower($k)] = $v; }
						foreach($this->ChannelUnit->ChannelFields as $k=>$arr){
							if(isset($row[$k])) $row[$k] = $this->ChannelUnit->MakeField($k,$row[$k]);
						}
						foreach($this->dtp2->CTags as $k=>$ctag){
								@$this->dtp2->Assign($k,$row[$ctag->GetName()]);
						}
						$artlist .= $this->dtp2->GetResult();
					}//if hasRow
					if($col>1) $artlist .= "	</td>\r\n";
				}//Loop Col
				if($col>1) $i += $col - 1;
				if($col>1) $artlist .= "	</tr>\r\n";
		}//Loop Line
		if($col>1) $artlist .= "</table>\r\n";
		$this->dsql->FreeResult("al");
		//$t3 = ExecTime();
		//echo ($t3-$t2)."<br>";
		return $artlist;
	}
  //---------------------------------
  //获取静态的分页列表
  //---------------------------------
	function GetPageListST($list_len,$listitem="info,index,end,pre,next,pageno")
	{
		$prepage="";
		$nextpage="";
		$prepagenum = $this->pageno-1;
		$nextpagenum = $this->pageno+1;
		if($list_len==""||ereg("[^0-9]",$list_len)) $list_len=3;
		$totalpage = ceil($this->totalresult/$this->PageSize);
		if($totalpage<=1 && $this->totalresult>0) return "共1页/".$this->totalresult."条";
		if($this->totalresult == 0) return "共0页/".$this->totalresult."条";
		$maininfo = " 共{$totalpage}页/".$this->totalresult."条 ";
		$purl = $this->GetCurUrl();

		$tnamerule = $this->GetMakeFileRule($this->Fields['ID'],"list",$this->Fields['typedir'],$this->Fields['defaultname'],$this->Fields['namerule2']);
		$tnamerule = ereg_replace('^(.*)/','',$tnamerule);
		//获得上一页和主页的链接
		if($this->pageno != 1){
			$prepage.="<a href='".str_replace("{page}",$prepagenum,$tnamerule)."'>上一页</a>\r\n";
			$indexpage="<a href='".str_replace("{page}",1,$tnamerule)."'>首页</a>\r\n";
		}else{ $indexpage="<a href='#'>首页</a>\r\n"; }
		//下一页,未页的链接
		if($this->pageno!=$totalpage && $totalpage>1){
			$nextpage.="<a href='".str_replace("{page}",$nextpagenum,$tnamerule)."'>下一页</a>\r\n";
			$endpage="<a href='".str_replace("{page}",$totalpage,$tnamerule)."'>末页</a>\r\n";
		}else{
			$endpage="<a href='#'>末页</a>\r\n";
		}

		//option链接
		$optionlen = strlen($totalpage);
		$optionlen = $optionlen*20+18;
		$optionlist = "<select name='sldd' style='width:{$optionlen}px' onchange='location.href=this.options[this.selectedIndex].value;'>\r\n";
		for($mjj=1;$mjj<=$totalpage;$mjj++){
			if($mjj==$this->pageno) $optionlist .= "<option value='".str_replace("{page}",$mjj,$tnamerule)."' selected>$mjj</option>\r\n";
		  else $optionlist .= "<option value='".str_replace("{page}",$mjj,$tnamerule)."'>$mjj</option>\r\n";
		}
		$optionlist .= "</select>";

		//获得数字链接
		$listdd="";
		$total_list = $list_len * 2 + 1;
		if($this->pageno >= $total_list) {
      $j = $this->pageno-$list_len;
      $total_list = $this->pageno+$list_len;
      if($total_list>$totalpage) $total_list=$totalpage;
		}
		else{
   		$j=1;
   		if($total_list>$totalpage) $total_list=$totalpage;
		}
		for($j;$j<=$total_list;$j++){
   		if($j==$this->pageno) $listdd.= "<strong>$j</strong>";
   		else $listdd.="<a href='".str_replace("{page}",$j,$tnamerule)."'>".$j."</a>\r\n";
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
		$prepagenum = $this->pageno-1;
		$nextpagenum = $this->pageno+1;
		if($list_len==""||ereg("[^0-9]",$list_len)) $list_len=3;
		$totalpage = ceil($this->totalresult/$this->PageSize);
		if($totalpage<=1 && $this->totalresult>0) return "<span>共1页/".$this->totalresult."条</span>";
		if($this->totalresult == 0) return "<span>共0页/".$this->totalresult."条</span>";
		$maininfo = "<span>共{$totalpage}页/".$this->totalresult."条</span>";

		$purl = $this->GetCurUrl();
		$geturl = "typeid=".$this->TypeID."&totalresult=".$this->totalresult."&";
		$hidenform = "<input type='hidden' name='typeid' value='".$this->TypeID."'>\r\n";
		$hidenform .= "<input type='hidden' name='totalresult' value='".$this->totalresult."'>\r\n";

		$purl .= "?".$geturl;

		//获得上一页和下一页的链接
		if($this->pageno != 1){
			$prepage.="<a href='".$purl."pageno=$prepagenum'>上一页</a>\r\n";
			$indexpage="<a href='".$purl."pageno=1'>首页</a>\r\n";
		}
		else{
			$indexpage="<a href='#'>首页</a>\r\n";
		}

		if($this->pageno!=$totalpage && $totalpage>1){
			$nextpage.="<a href='".$purl."pageno=$nextpagenum'>下一页</a>\r\n";
			$endpage="<a href='".$purl."pageno=$totalpage'>末页</a>\r\n";
		}
		else{
			$endpage="末页\r\n";
		}
		//获得数字链接
		$listdd="";
		$total_list = $list_len * 2 + 1;
		if($this->pageno >= $total_list) {
    		$j = $this->pageno-$list_len;
    		$total_list = $this->pageno+$list_len;
    		if($total_list>$totalpage) $total_list=$totalpage;
		}else{
   			$j=1;
   			if($total_list>$totalpage) $total_list=$totalpage;
		}
		for($j;$j<=$total_list;$j++){
   		if($j==$this->pageno) $listdd.= "<strong>$j</strong>\r\n";
   		else $listdd.="<a href='".$purl."pageno=$j'>".$j."</a>\n";
		}
		$plist  =  " ";
		$plist .= "<form name='pagelist' action='".$this->GetCurUrl()."'>$hidenform";
		$plist .= $maininfo.$indexpage.$prepage.$listdd.$nextpage.$endpage;
		if($totalpage>$total_list){
			$plist.="<input type='text' name='pageno' ' value='".$this->pageno."'>\r\n";
			$plist.="<input type='submit' id='button' name='plistgo' value='GO' >\r\n";
		}
		$plist .= "</form>\r\n";
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