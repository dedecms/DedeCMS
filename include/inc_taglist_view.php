<?php
require_once(DEDEINC."/pub_oxwindow.php");

//打开所有标记，开启此项允许你使用封面模板的所有标记，但会对性能造成一定的影响
$cfg_OpenAll = false;

include_once(DEDEINC."/inc_arcpart_view.php");
include_once(DEDEINC."/inc_pubtag_make.php");
/******************************************************
//Copyright 2004-2006 by DedeCms.com itprato
//本类的用途是用于浏览含有某Tag的文档
******************************************************/
@set_time_limit(0);
class TagList
{
	var $dsql;
	var $dtp;
	var $dtp2;
	var $TypeLink;
	var $PageNo;
	var $TotalPage;
	var $TotalResult;
	var $PageSize;
	var $ListType;
	var $Fields;
	var $PartView;
	var $Tag;
	var $Templet;
	var $TagInfos;
	var $TempletsFile;
	var $TagID;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($keyword,$templet)
 	{
 		$this->Templet = $templet;
 		$this->Tag = $keyword;
 		$this->dsql = new DedeSql(false);
 		$this->dtp = new DedeTagParse();
 		$this->dtp->SetNameSpace("dede","{","}");
 		$this->dtp2 = new DedeTagParse();
 		$this->dtp2->SetNameSpace("field","[","]");
 		$this->TypeLink = new TypeLink(0);
 		$this->Fields['tag'] = $keyword;
 		$this->Fields['position'] = " ".$keyword;
 		$this->Fields['title'] = " 标签：{$keyword} ";
 		$this->TempletsFile = '';
 		//设置一些全局参数的值
 		foreach($GLOBALS['PubFields'] as $k=>$v) $this->Fields[$k] = $v;
 		$this->PartView = new PartView(0);

 		//读取Tag信息
 		if($this->Tag!='')
 	  {
 		   $this->TagInfos = $this->dsql->GetOne("Select * From `#@__tag_index` where tagname like '{$this->Tag}' ");
 		   if(!is_array($this->TagInfos))
 		   {
 			    $fullsearch = $GLOBALS['cfg_phpurl']."/search.php?keyword=".$this->Tag."&searchtype=titlekeyword";
 			    $msg = "系统无此标签，可能已经移除！<br /><br />你还可以尝试通过搜索程序去搜索这个关键字：<a href='$fullsearch'>前往搜索&gt;&gt;</a>";
 			    ShowMsgWin($msg,"<a href='tag.php'>Tag标签</a> &gt;&gt; 错误提示");
 			    $this->dsql->Close();
 			    exit();
 		   }
 		   $this->TagID = $this->TagInfos['id'];
 		}

 		//初始化模板
 		$tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$GLOBALS['cfg_df_style'].'/'.$this->Templet;
 		if(!file_exists($tempfile)||!is_file($tempfile)){
 			  echo "模板文件：'".$tempfile."' 不存在，无法解析文档！";
 			  exit();
 	  }
 	  $this->dtp->LoadTemplate($tempfile);
 	  $this->TempletsFile = ereg_replace("^".$GLOBALS['cfg_basedir'],'',$tempfile);

  }
  //php4构造函数
 	//---------------------------
 	function TagList($keyword,$templet){
 		$this->__construct($keyword,$templet);
 	}
 	//---------------------------
 	//关闭相关资源
 	//---------------------------
 	function Close()
 	{
 		@$this->dsql->Close();
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
			  $cquery = "Select count(*) as dd From #@__tag_list where tid = '{$this->TagID}' And arcrank>-1 ";
			  $row = $this->dsql->GetOne($cquery);
			  $this->TotalResult = $row['dd'];
			  //更新Tag信息
			  $ntime = time();
			  //更新浏览量和记录数
			  $upquery = "Update #@__tag_index set result='{$row['dd']}',count=count+1,weekcc=weekcc+1,monthcc=monthcc+1 where id='{$this->TagID}' ";
			  $this->dsql->ExecuteNoneQuery($upquery);
			  $oneday = 24 * 3600;
			  //周统计
			  if(ceil( ($ntime - $this->TagInfos['weekup'])/$oneday )>7){
			  	 $this->dsql->ExecuteNoneQuery("Update #@__search_keywords set weekcc=0,weekup='{$ntime}' where id='{$this->TagID}' ");
			  }
			  //月统计
			  if(ceil( ($ntime - $this->TagInfos['monthup'])/$oneday )>30){
			  	 $this->dsql->ExecuteNoneQuery("Update #@__search_keywords set monthcc=0,monthup='{$ntime}' where id='{$this->TagID}' ");
			  }
 		 }
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
 	//显示列表
 	//------------------
 	function Display()
 	{
 		if($this->Tag!='') $this->CountRecord();
 		$this->ParseTempletsFirst();
 		if($this->Tag!='') $this->ParseDMFields($this->PageNo,0);
 	  $this->Close();
 		$this->dtp->Display();
 	}

 	//--------------------------------
 	//解析模板，对固定的标记进行初始给值
 	//--------------------------------
 	function ParseTempletsFirst()
 	{
 	   //对公用标记的解析，这里对对象的调用均是用引用调用的，因此运算后会自动改变传递的对象的值
 	   MakePublicTag($this,$this->dtp,$this->PartView,$this->TypeLink,0,0,0);
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
 				if(trim($ctag->GetInnerText())==""){
 					 $InnerText = GetSysTemplets("list_fulllist.htm");
 				}else{
 					 $InnerText = trim($ctag->GetInnerText());
 				}
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
 			else if($ctag->GetName()=="pagelist")
 			{
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
		$getrow = ($row=="" ? 10 : $row);
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

		$idlists = '';
		$this->dsql->SetQuery("Select aid From #@__tag_list where tid='{$this->TagID}' And arcrank>-1 limit $limitstart,$getrow");
		//echo "Select aid From #@__tag_list where tid='{$this->TagID}' And arcrank>-1 limit $limitstart,$getrow";
		$this->dsql->Execute();
		while($row=$this->dsql->GetArray()){
			$idlists .= ($idlists=='' ? $row['aid'] : ','.$row['aid']);
		}
		if($idlists=='') return '';

		//按不同情况设定SQL条件
		$orwhere = " se.aid in($idlists) ";

		//排序方式
		$ordersql = "";
		if($orderby=="uptime") $ordersql = "  order by se.uptime $orderWay";
		else $ordersql=" order by se.aid $orderWay";

		//----------------------------
		$query = "Select se.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl
			from `#@__full_search` se left join `#@__arctype` tp on se.typeid=tp.ID
			where $orwhere $ordersql
		";

		$this->dsql->SetQuery($query);
		$this->dsql->Execute('al');
		echo $this->dsql->GetError();
		$artlist = "";
		if($col>1) $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
		$this->dtp2->LoadSource($innertext);
		if(!is_array($this->dtp2->CTags)) return '';
		$GLOBALS['autoindex'] = 0;
		for($i=0;$i<$getrow;$i++)
		{
				if($col>1) $artlist .= "<tr>\r\n";
				for($j=0;$j<$col;$j++)
				{
					if($col>1) $artlist .= "<td width='$colWidth'>\r\n";
					if($row = $this->dsql->GetArray('al',MYSQL_ASSOC))
					{
						$GLOBALS['autoindex']++;
						//处理一些特殊字段
						$row['id'] =  $row['aid'];
						$row['arcurl'] = $row['url'];
						$row['typeurl'] = $this->GetListUrl($row['typeid'],$row['typedir'],$row['isdefault'],$row['defaultname'],$row['ispart'],$row['namerule2'],"abc");

						if($ismake==0 && $GLOBALS['cfg_multi_site']=='Y')
						{
							if($row['litpic']==''){
								$row['litpic'] = $GLOBALS['cfg_mainsite'].$GLOBALS['cfg_plus_dir']."/img/dfpic.gif";
              }
							else if(!eregi("^http://",$row['picname'])){
								$row['litpic'] = $row['siteurl'].$row['litpic'];
							}
							$row['picname'] = $row['litpic'];
						}else
						{
							if($row['litpic']=='') $row['litpic'] = $GLOBALS['cfg_plus_dir']."/img/dfpic.gif";
						}

						$row['description'] = cnw_left($row['addinfos'],$infolen);
						$row['picname'] = $row['litpic'];
						$row['info'] = $row['description'];
						$row['filename'] = $row['arcurl'];
						$row['uptime'] = GetDateMK($row['uptime']);
						$row['typelink'] = "<a href='".$row['typeurl']."'>[".$row['typename']."]</a>";
						$row['imglink'] = "<a href='".$row['filename']."'><img src='".$row['picname']."' border='0' width='$imgwidth' height='$imgheight' alt='".str_replace("'","",$row['title'])."'></a>";
						$row['image'] = "<img src='".$row['picname']."' border='0' width='$imgwidth' height='$imgheight' alt='".str_replace("'","",$row['title'])."'>";
						$row['title'] = cn_substr($row['title'],$titlelen);
						$row['textlink'] = "<a href='".$row['filename']."' title='".str_replace("'","",$row['title'])."'>".$row['title']."</a>";

						foreach($this->dtp2->CTags as $k=>$ctag)
						{
							if(isset($row[$ctag->GetName()])) $this->dtp2->Assign($k,$row[$ctag->GetName()]);
							else $this->dtp2->Assign($k,"");
						}

						$artlist .= $this->dtp2->GetResult();
					//if hasRow
					}else
					{
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

	//----------------------------------------
	//获得标签
	//----------------------------------------
	function GetTags($num,$ltype='new',$InnerText=""){

		$InnerText = trim($InnerText);
		if($InnerText=="") $InnerText = GetSysTemplets("tag_one.htm");
		$revalue = "";
		if($ltype=='rand') $orderby = ' rand() ';
		else if($ltype=='week') $orderby=' weekcc desc ';
		else if($ltype=='month') $orderby=' monthcc desc ';
		else if($ltype=='hot') $orderby=' count desc ';
		else $orderby = '  id desc  ';
		$this->dsql->SetQuery("Select tagname,count,monthcc,result From #@__tag_index order by $orderby limit 0,$num");
    $this->dsql->Execute();
		$ctp = new DedeTagParse();
		$ctp->SetNameSpace("field","[","]");
		$ctp->LoadSource($InnerText);
		while($row = $this->dsql->GetArray())
    {
		  $row['keyword'] = $row['tagname'];
		  $row['link'] = $cfg_cmspath."/tag.php?/".urlencode($row['keyword'])."/";

		  $row['highlight'] = $row['keyword'];
		  $row['result'] = trim($row['result']);
		  if(empty($row['result'])) $row['result'] = 0;

		  if($ltype=='view'||$ltype=='rand'||$ltype=='new'){
		  	 if($row['monthcc']>1000 || $row['weekcc']>300 ){
		  	 	  $row['highlight'] = "<span style='font-size:".mt_rand(12,16)."px;color:red'><b>{$row['highlight']}</b></span>";
		  	 }
		  	 else if($row['result']>150){
		  	 	  $row['highlight'] = "<span style='font-size:".mt_rand(12,16)."px;color:blue'>{$row['highlight']}</span>";
		  	 }
		  	 else if($row['count']>1000){
		  	 	  $row['highlight'] = "<span style='font-size:".mt_rand(12,16)."px;color:red'>{$row['highlight']}</span>";
		  	 }
		  }else{
		  	$row['highlight'] = "<span style='font-size:".mt_rand(12,16)."px;'>{$row['highlight']}</span>";
		  }

		  foreach($ctp->CTags as $tagid=>$ctag){
		    if(isset($row[$ctag->GetName()])) $ctp->Assign($tagid,$row[$ctag->GetName()]);
		  }
		  $revalue .= $ctp->GetResult();
		}

		return $revalue;
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

		$totalpage = $this->TotalPage;

		if($totalpage<=1 && $this->TotalResult>0) return "<a>共1页/".$this->TotalResult."条</a>";

		if($this->TotalResult == 0) return "<a>共0页/".$this->TotalResult."条</a>";

		$maininfo = "<a>共{$totalpage}页/".$this->TotalResult."条</a>\r\n";

		$purl = $this->GetCurUrl();

		$purl .= "?/".$this->Tag;

		//获得上一页和下一页的链接
		if($this->PageNo != 1){
			$prepage.="<a href='".$purl."/$prepagenum/'>上一页</a>\r\n";
			$indexpage="<a href='".$purl."/1/'>首页</a>\r\n";
		}
		else{
			$indexpage="<a>首页</a>\r\n";
		}

		if($this->PageNo!=$totalpage && $totalpage>1){
			$nextpage.="<a href='".$purl."/$nextpagenum/'>下一页</a>\r\n";
			$endpage="<a href='".$purl."/$totalpage/'>末页</a>\r\n";
		}
		else{
			$endpage="<a>末页</a>\r\n";
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
   		if($j==$this->PageNo) $listdd.= "<strong>$j</strong>\r\n";
   		else $listdd.="<a href='".$purl."/$j/'>".$j."</a>\r\n";
		}
		$plist  =  "";
		$plist .= $maininfo.$indexpage.$prepage.$listdd.$nextpage.$endpage;
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