<?php
require_once(dirname(__FILE__)."/inc_arcpart_view.php");
require_once(dirname(__FILE__)."/inc_pubtag_make.php");
/******************************************************
//Copyright 2004-2006 by DedeCms.com itprato
//本类的用途是用于浏览频道列表或对内容列表生成HTML
******************************************************/
@set_time_limit(0);
class DiggList
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
	var $ListType;
	var $Fields;
	var $PartView;
	var $SortType;
	var $NameRule;
	var $TempletsFile;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($typeid=0,$sorttype='time')
 	{
 		global $dsql;
 		$this->SortType = $sorttype;
 		$this->TypeID = $typeid;
 		$this->TempletsFile = '';
 		$this->NameRule = $GLOBALS['cfg_cmspath']."/digg/digg-{page}.html";
 		
 		$this->dsql = new DedeSql(false);
 		
 		$this->dtp = new DedeTagParse();
 		$this->dtp->SetNameSpace("dede","{","}");
 		$this->dtp2 = new DedeTagParse();
 		$this->dtp2->SetNameSpace("field","[","]");
 		$this->TypeLink = new TypeLink($typeid);
 		$this->Fields = $this->TypeLink->TypeInfos;
 		$this->Fields['id'] = $typeid;
 		$this->Fields['position'] = ' DIGG顶客 &gt; ';
 		$this->Fields['title'] = " DIGG顶客 ";
 		//设置一些全局参数的值
 		foreach($GLOBALS['PubFields'] as $k=>$v) $this->Fields[$k] = $v;
 		$this->Fields['rsslink'] = "";
 		$this->PartView = new PartView($typeid);
  }
  //php4构造函数
 	//---------------------------
 	function DiggList($typeid=0,$sorttype='time'){
 		$this->__construct($typeid,$sorttype);
 	}
 	//---------------------------
 	//关闭相关资源
 	//---------------------------
 	function Close()
 	{
 		@$this->dsql->Close();
 		@$this->dsql = '';
 		@$this->TypeLink->Close();
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
			  $cquery = "Select count(*) as dd From `#@__full_search` where $addSql";
			  $row = $this->dsql->GetOne($cquery);
			  if(is_array($row)) $this->TotalResult = $row['dd'];
			  else $this->TotalResult = 0;
 		 }
 		 //初始化列表模板，并统计页面总数
 		 $tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$GLOBALS['cfg_df_style'].'/digg.htm';
 		 if(!file_exists($tempfile)||!is_file($tempfile)){
 			  echo "模板文件：'".$tempfile."' 不存在，无法解析文档！";
 			  exit();
 		 }
 		 $this->dtp->LoadTemplate($tempfile);
 		 $this->TempletsFile = ereg_replace("^".$GLOBALS['cfg_basedir'],'',$tempfile);
 		 $ctag = $this->dtp->GetTag("page");
 		 if(!is_object($ctag)){ $ctag = $this->dtp->GetTag("list"); }
 		 if(!is_object($ctag)) $this->PageSize = 25;
 		 else{
 		   if($ctag->GetAtt("pagesize")!="") $this->PageSize = $ctag->GetAtt("pagesize");
       else $this->PageSize = 25;
     }
     $this->TotalPage = ceil($this->TotalResult/$this->PageSize);
 	}
 	//------------------
 	//列表创建HTML
 	//------------------
 	function MakeHtml($startpage=1,$makepagesize=0)
 	{
 		if(empty($startpage)) $startpage = 1;

 		$this->CountRecord();
 		
 		//初步给固定值的标记赋值
 		$this->ParseTempletsFirst();

 		CreateDir('/digg','','');

 		$murl = "";

 		if($makepagesize>0) $endpage = $startpage+$makepagesize;
 		else $endpage = ($this->TotalPage+1);

 		if($endpage>($this->TotalPage+1)) $endpage = $this->TotalPage;

 		//循环更新HTML
 		for($this->PageNo=$startpage;$this->PageNo<$endpage;$this->PageNo++)
 		{
 		  $this->ParseDMFields($this->PageNo,1);
 	    $makeFile = $this->NameRule;
 	    $makeFile = str_replace("{page}",$this->PageNo,$makeFile);
 	    $murl = $makeFile;
 	    $makeFile = $this->GetTruePath().$makeFile;
 	    $makeFile = ereg_replace("/{1,}","/",$makeFile);
 	    $murl = $this->GetTrueUrl($murl);
 	    $this->dtp->SaveTo($makeFile);
 	    echo "成功创建：<a href='$murl' target='_blank'>$murl</a><br/>";
 	  }
 	  
 		$this->Close();
 		
 		return $murl;
 	}
 	//------------------
 	//显示列表
 	//------------------
 	function Display()
 	{
 		$this->CountRecord();
 		$this->ParseTempletsFirst();
 		$this->ParseDMFields($this->PageNo,0);
 	  $this->Close();
 		$this->dtp->Display();
 	}

 	//----------------------------
 	//获得站点的真实根路径
 	//----------------------------
 	function GetTruePath(){
 		$truepath = $GLOBALS["cfg_basedir"];
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
 	function ParseDMFields($PageNo,$ismake=1)
 	{
 		foreach($this->dtp->CTags as $tagid=>$ctag){
 			if($ctag->GetName()=="list"){
 				$limitstart = ($this->PageNo-1) * $this->PageSize;
 				$row = $this->PageSize;
 				if(trim($ctag->GetInnerText())==""){ $InnerText = GetSysTemplets("list_digglist.htm"); }
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
 				         $this->SortType,
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
		if($innertext=="") $innertext = GetSysTemplets("list_digglist.htm");
		//按不同情况设定SQL条件
		$orwhere = " arc.arcrank > -1 ";
    
		//排序方式
		$ordersql = "";
		if($orderby=="digg") $ordersql=" order by arc.digg $orderWay";
		else $ordersql=" order by arc.diggtime $orderWay";

    $this->dtp2->LoadSource($innertext);
		if(!is_array($this->dtp2->CTags)) return '';

		$query = "Select arc.*,
			tp.typedir,tp.typename,tp.isdefault,tp.defaultname,
			tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl
			from `#@__full_search` arc
			left join #@__arctype tp on arc.typeid=tp.ID
			where $orwhere $ordersql limit $limitstart,$row
		";
		$this->dsql->SetQuery($query);
		$this->dsql->Execute("alf");
		//$t2 = ExecTime();
		//echo $query."|";
		//echo ($t2-$t1)."<br>";
		$artlist = "";
		$GLOBALS['autoindex'] = 0;
		while($row = $this->dsql->GetArray("alf"))
    {
       //处理一些特殊字段
       $row['description'] = cn_substr($row['addinfos'],$infolen);
       $row['id'] =  $row['aid'];
       $row['filename'] = $row['arcurl'] = $row['url'];
       $row['typeurl'] = GetTypeUrl($row['typeid'],MfTypedir($row['typedir']),$row['isdefault'],$row['defaultname'],$row['ispart'],$row['namerule2'],$row['siteurl']);
       if($row['litpic']=="") $row['litpic'] = $GLOBALS['PubFields']['templeturl']."/img/default.gif";
       $row['picname'] = $row['litpic'];
       if($GLOBALS['cfg_multi_site']=='Y')
       {
           if($row['siteurl']=="") $row['siteurl'] = $GLOBALS['cfg_mainsite'];
           if(!eregi("^http://",$row['picname'])){
           	 	$row['litpic'] = $row['siteurl'].$row['litpic'];
           	 	$row['picname'] = $row['litpic'];
           }
       }
       $row['stime'] = GetDateMK($row['uptime']);
       $row['typelink'] = "<a href='".$row['typeurl']."'>".$row['typename']."</a>";
       $row['image'] = "<img src='".$row['picname']."' border='0' width='$imgwidth' height='$imgheight' alt='".ereg_replace("['><]","",$row['title'])."' />";
       $row['imglink'] = "<a href='".$row['arcurl']."'>".$row['image']."</a>";
       $row['fulltitle'] = $row['title'];
       $row['title'] = cn_substr($row['title'],$titlelen);
       $row['textlink'] = "<a href='".$row['arcurl']."'>".$row['title']."</a>";
       foreach($this->dtp2->CTags as $k=>$ctag)
       {
       		if(isset($row[$ctag->GetName()])) $this->dtp2->Assign($k,$row[$ctag->GetName()]);
       		else $this->dtp2->Assign($k,'');
       }
       $GLOBALS['autoindex']++;
       $artlist .= $this->dtp2->GetResult();
     }//Loop Line
		 $this->dsql->FreeResult("alf");
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
		$prepagenum = $this->PageNo-1;
		$nextpagenum = $this->PageNo+1;
		if($list_len==""||ereg("[^0-9]",$list_len)) $list_len=3;
		$totalpage = ceil($this->TotalResult/$this->PageSize);
		if($totalpage<=1 && $this->TotalResult>0) return "共1页/".$this->TotalResult."条记录";
		if($this->TotalResult == 0) return "共0页/".$this->TotalResult."条记录";
		$maininfo = " 共{$totalpage}页/".$this->TotalResult."条记录 ";
		$purl = $this->GetCurUrl();

		$tnamerule = $this->NameRule;
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
		$hidenform = "<input type='hidden' name='sorttype' value='".$this->SortType."'>\r\n";
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
			$plist.="<td width='36'><input type='text' name='PageNo' style='width:30px;height:18px' value='".$this->PageNo."'></td>\r\n";
			$plist.="<td width='30'><input type='submit' name='plistgo' value='GO' style='width:24px;height:18px;font-size:9pt'></td>\r\n";
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