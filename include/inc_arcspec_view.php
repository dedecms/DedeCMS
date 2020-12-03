<?php 
require_once(dirname(__FILE__)."/inc_arcpart_view.php");
require_once(dirname(__FILE__)."/inc_pubtag_make.php");
/******************************************************
//Copyright 2004-2006 by DedeCms.com itprato
//本类的用途是用于浏览所有专题列表或对专题列表生成HTML
******************************************************/
@set_time_limit(0);
class SpecView
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
	var $Fields;
	var $PartView;
	var $StartTime;
	var $TempletsFile;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($starttime=0)
 	{
 		$this->TypeID = 0;
 		$this->dsql = new DedeSql(false);
 		$this->dtp = new DedeTagParse();
 		$this->dtp->SetNameSpace("dede","{","}");
 		$this->dtp2 = new DedeTagParse();
 		$this->dtp2->SetNameSpace("field","[","]");
 		$this->TypeLink = new TypeLink(0);
 		$this->ChannelUnit = new ChannelUnit(-1);
 		//设置一些全局参数的值
 		foreach($GLOBALS['PubFields'] as $k=>$v) $this->Fields[$k] = $v;
 		
 		if($starttime==0) $this->StartTime = 0;
 		else{
 			$this->StartTime = GetMkTime($starttime);
 		}
 		
 		$this->PartView = new PartView();

 		$this->CountRecord();
 		$tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$GLOBALS['cfg_df_style']."/list_spec.htm";
 		if(!file_exists($tempfile)||!is_file($tempfile)){
 			 echo "模板文件：'".$tempfile."' 不存在，无法解析文档！";
 			 exit();
 		 }
 		 $this->dtp->LoadTemplate($tempfile);
 		 $this->TempletsFile = ereg_replace("^".$GLOBALS['cfg_basedir'],'',$tempfile);
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
  //php4构造函数
 	//---------------------------
 	function SpecView($starttime=0){
 		$this->__construct($starttime);
 	}
 	//---------------------------
 	//关闭相关资源
 	//---------------------------
 	function Close()
 	{
 		$this->dsql->Close();
 		$this->TypeLink->Close();
 		$this->ChannelUnit->Close();
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
 		
 		if($this->TotalResult==-1)
 		{
 		  if($this->StartTime>0) $timesql = " And senddate>'".$this->StartTime."'";
 		  else $timesql = "";
 			$row = $this->dsql->GetOne("Select count(*) as dd From `#@__archivesspec` where arcrank > -1 And channel=-1 $timesql");
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
 		}
 		$this->ParseTempletsFirst();
 		foreach($this->dtp->CTags as $tagid=>$ctag){
 			if($ctag->GetName()=="list"){
 				$limitstart = ($this->PageNo-1) * $this->PageSize;
 				$row = $this->PageSize;
 				if(trim($ctag->GetInnerText())==""){ $InnerText = GetSysTemplets("list_fulllist.htm"); }
 				else{ $InnerText = trim($ctag->GetInnerText()); }
 				$this->dtp->Assign($tagid,
 				      $this->GetArcList($limitstart,$row,
 				      $ctag->GetAtt("col"),
 				      $ctag->GetAtt("titlelen"),
 				      $ctag->GetAtt("infolen"),
 				      $ctag->GetAtt("imgwidth"),
 				      $ctag->GetAtt("imgheight"),
 				      $ctag->GetAtt("listtype"),
 				      $ctag->GetAtt("orderby"),
 				      $InnerText,
 				      $ctag->GetAtt("tablewidth"))
 				);
 			}
 			else if($ctag->GetName()=="pagelist"){
 				$list_len = trim($ctag->GetAtt("listsize"));
 				if($list_len=="") $list_len = 3;
 				$this->dtp->Assign($tagid,$this->GetPageListDM($list_len));
 			}
 	  }
 	  $this->Close();
 		$this->dtp->Display();
 	}
 	//------------------
 	//开始创建列表
 	//------------------
 	function MakeHtml()
 	{
 		//初步给固定值的标记赋值
 		$indexfile = '';
 		$this->ParseTempletsFirst();
 		$totalpage = ceil($this->TotalResult/$this->PageSize);
 		if($totalpage==0) $totalpage = 1;
 		CreateDir($GLOBALS['cfg_special']);
 		$murl = "";
 		for($this->PageNo=1;$this->PageNo<=$totalpage;$this->PageNo++)
 		{
 		  foreach($this->dtp->CTags as $tagid=>$ctag){
 			  if($ctag->GetName()=="list"){
 				  $limitstart = ($this->PageNo-1) * $this->PageSize;
 				  $row = $this->PageSize;
 				  if(trim($ctag->GetInnerText())==""){ $InnerText = GetSysTemplets("spec_list.htm"); }
 				  else{ $InnerText = trim($ctag->GetInnerText()); }
 				  $this->dtp->Assign($tagid,
 				      $this->GetArcList($limitstart,$row,
 				      $ctag->GetAtt("col"),
 				      $ctag->GetAtt("titlelen"),
 				      $ctag->GetAtt("infolen"),
 				      $ctag->GetAtt("imgwidth"),
 				      $ctag->GetAtt("imgheight"),
 				      "spec",
 				      $ctag->GetAtt("orderby"),
 				      $InnerText,
 				      $ctag->GetAtt("tablewidth"))
 				  );
 			  }
 			  else if($ctag->GetName()=="pagelist"){
 				  $list_len = trim($ctag->GetAtt("listsize"));
 				  if($list_len=="") $list_len = 3;
 				  $this->dtp->Assign($tagid,$this->GetPageListST($list_len));
 			  }
 	    }//End foreach
 	    $makeFile = $GLOBALS['cfg_special']."/spec_".$this->PageNo.$GLOBALS['art_shortname'];
 	    $murl = $makeFile;
 	    $makeFile = $GLOBALS['cfg_basedir'].$makeFile;
 	    $this->dtp->SaveTo($makeFile);
 	    if(empty($indexfile)){
 	    	$indexfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_special']."/index".$GLOBALS['art_shortname'];
 	    	copy($makeFile,$indexfile);
 	    }
 	    echo "成功创建：<a href='$murl' target='_blank'>$murl</a><br/>";
 	  }
 		$this->Close();
 		return $murl;
 	}
 	//--------------------------------
 	//解析模板，对固定的标记进行初始给值
 	//--------------------------------
 	function ParseTempletsFirst()
 	{
 	   //对公用标记的解析，这里对对象的调用均是用引用调用的，因此运算后会自动改变传递的对象的值
 	   MakePublicTag($this,$this->dtp,$this->PartView,$this->TypeLink,$this->TypeID,0,0);
 	}
 	//----------------------------------
  //获取内容列表
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
		if($innertext=="") $innertext = GetSysTemplets("spec_list.htm");
		//按不同情况设定SQL条件
		$orwhere = " arcs.arcrank > -1 And arcs.channel = -1 ";
		if($this->StartTime>0) $orwhere .= " And arcs.senddate>'".$this->StartTime."'";
		
		//排序方式
		$ordersql = "";
		if($orderby=="senddate") $ordersql=" order by arcs.senddate desc";
		else if($orderby=="pubdate") $ordersql=" order by arcs.pubdate desc";
    else if($orderby=="id") $ordersql="  order by arcs.ID desc";
		else $ordersql=" order by arcs.sortrank desc";
		//
		//----------------------------
		$query = "Select arcs.ID,arcs.title,arcs.typeid,arcs.ismake,
		arcs.description,arcs.pubdate,arcs.senddate,arcs.arcrank,
		arcs.click,arcs.postnum,arcs.lastpost,arcs.money,arcs.litpic,t.typedir,t.typename,t.isdefault,
		t.defaultname,t.namerule,t.namerule2,t.ispart,t.moresite,t.siteurl
		from `#@__archivesspec` arcs 
		left join #@__arctype t on arcs.typeid=t.ID
		where $orwhere $ordersql limit $limitstart,$row";
		$this->dsql->SetQuery($query);
		$this->dsql->Execute("al");
    $artlist = "";
    if($col>1) $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
    $this->dtp2->LoadSource($innertext);
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
           $row["title"] = cnw_left($row["title"],$titlelen);
           $row["id"] =  $row["ID"];
           if($row["litpic"]=="") $row["litpic"] = $GLOBALS["cfg_plus_dir"]."/img/dfpic.gif";
           $row["picname"] = $row["litpic"];
           $row["arcurl"] = GetFileUrl($row["ID"],$row["typeid"],$row["senddate"],$row["title"],
                        $row["ismake"],$row["arcrank"],$row["namerule"],$row["typedir"],$row["money"],true,$row["siteurl"]);
           $row["typeurl"] = $this->GetListUrl($row["typeid"],$row["typedir"],$row["isdefault"],$row["defaultname"],$row["ispart"],$row["namerule2"],$row["siteurl"]);
           $row["info"] = $row["description"];
           $row["filename"] = $row["arcurl"];
           $row["stime"] = GetDateMK($row["pubdate"]);
           $row["textlink"] = "<a href='".$row["filename"]."'>".$row["title"]."</a>";
           $row["typelink"] = "[<a href='".$row["typeurl"]."'>".$row["typename"]."</a>]"; 
           $row["imglink"] = "<a href='".$row["filename"]."'><img src='".$row["picname"]."' border='0' width='$imgwidth' height='$imgheight'></a>";
           $row["image"] = "<img src='".$row["picname"]."' border='0' width='$imgwidth' height='$imgheight'>";
           $row["phpurl"] = $GLOBALS["cfg_plus_dir"];
 		       $row["templeturl"] = $GLOBALS["cfg_templets_dir"];
 		       $row["memberurl"] = $GLOBALS["cfg_member_dir"];
           //编译附加表里的数据
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
         if($col>1) $artlist .= "</td>\r\n";
       }//Loop Col
       if($col>1) $artlist .= "</tr>\r\n";
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
		
		$tnamerule = "spec_";
		
		//获得上一页和下一页的链接
		if($this->PageNo != 1){
			$prepage.="<a href='".$tnamerule."_$prepagenum".$GLOBALS['art_shortname']."'>上一页</a>\r\n";
			$indexpage="<a href='".$tnamerule."_1".$GLOBALS['art_shortname']."'>首页</a>\r\n";
		}
		else{
			$indexpage="首页\r\n";
		}	
		//
		if($this->PageNo!=$totalpage && $totalpage>1){
			$nextpage.="<a href='".$tnamerule."_$nextpagenum".$GLOBALS['art_shortname']."'>下一页</a>\r\n";
			$endpage="<a href='".$tnamerule."_$totalpage".$GLOBALS['art_shortname']."'>末页</a>\r\n";
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
   		else $listdd.="".$tnamerule."_$j".$GLOBALS['art_shortname']."'>[".$j."]</a>\r\n";
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
		$plist .= $indexpage;
		$plist .= $prepage;
		$plist .= $listdd;
		$plist .= $nextpage;
		$plist .= $endpage;
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
  	return GetTypeUrl($typeid,MfTypedir($typedir),$isdefault,$defaultname,$ispart,$namerule2);
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