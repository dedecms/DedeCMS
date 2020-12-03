<?php
require_once(dirname(__FILE__)."/inc_arcpart_view.php");
require_once(dirname(__FILE__)."/inc_downclass.php");
require_once(dirname(__FILE__)."/inc_channel_unit.php");
require_once(dirname(__FILE__)."/inc_pubtag_make.php");
/******************************************************
//Copyright 2004-2008 by DedeCms.com itprato
//本类的用途是用于浏览文档或对文档生成HTML
******************************************************/
@set_time_limit(0);
class Archives
{
	var $TypeLink;
	var $ChannelUnit;
	var $dsql;
	var $Fields;
	var $dtp;
	var $ArcID;
	var $SplitPageField;
	var $SplitFields;
	var $NowPage;
	var $TotalPage;
	var $NameFirst;
	var $ShortName;
	var $FixedValues;
	var $PartView;
	var $TempSource;
	var $IsError;
	var $SplitTitles;
	var $MemberInfos;
	var $MainTable;
	var $AddTable;
	var $PreNext;
	var $TempletsFile;
	var $fullurl;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($aid)
 	{
 		global $PubFields;
 		$t1 = ExecTime();
 		$this->IsError = false;
 		$this->dsql = new DedeSql(false);
 		$this->ArcID = $aid;
 		$this->MemberInfos = array();
 		$this->PreNext = array();
 		$this->TempletsFile = '';
 		$this->MainTable = '';
 		$this->fullurl = '';
 		
    //获取文档表信息
    $fullsearchs = $this->dsql->GetOne("select c.ID as channelid,c.maintable,c.addtable,a.keywords,a.url from `#@__full_search` a left join  #@__channeltype c on  c.ID = a.channelid where a.aid='$aid'",MYSQL_ASSOC);
    if($fullsearchs['channelid']==-1)
    {
       $this->MainTable = '#@__archivesspec';
 		   $this->AddTable = '#@__addonspec';
 		}else
 		{
 		   if($fullsearchs['maintable']=='') $fullsearchs['maintable']='#@__archives';
 		   $this->MainTable = $fullsearchs['maintable'];
 		   $this->AddTable = $fullsearchs['addtable'];
 		}

 		$query = "Select arc.*,sm.name as smalltypename,tp.reID,tp.typedir,tp.typename,am.uname from `{$this->MainTable}` arc
 		          left join #@__arctype tp on tp.ID=arc.typeid
 		          left join #@__smalltypes sm on sm.id=arc.smalltypeid
 		          left join #@__admin am on arc.adminID = am.ID 
 		          where arc.ID='$aid'";
 		$row = $this->dsql->GetOne($query,MYSQL_ASSOC);

 		//无法获取记录
 		if(!is_array($row)){
 			$this->IsError = true;
 			return;
 	  }
 		//把主表记录转换为Field
 		foreach($row as $k=>$v) $this->Fields[$k] = $v;
 		$this->Fields['keywords'] = $fullsearchs['keywords'];
 		$this->fullurl = $fullsearchs['url'];
 		unset($row);
 		unset($fullsearchs);
 		if($this->Fields['redirecturl']!="") return;

 		//模板引擎与页面环境初始化
 		$this->ChannelUnit = new ChannelUnit($this->Fields['channel'],$aid);
 		$this->TypeLink = new TypeLink($this->Fields['typeid']);
 		$this->dtp = new DedeTagParse();
 		$this->SplitPageField = $this->ChannelUnit->SplitPageField;
 		$this->SplitFields = "";
 		$this->TotalPage = 1;
 		$this->NameFirst = "";
 		$this->ShortName = "html";
 		$this->FixedValues = "";
 		$this->TempSource = "";
 		$this->PartView = new PartView($this->Fields['typeid']);

 		if(empty($GLOBALS["pageno"])) $this->NowPage = 1;
 		else $this->NowPage = intval($GLOBALS["pageno"]);

 		//特殊的字段Field数据处理
 		$this->Fields['aid'] = $aid;
 		$this->Fields['id'] = $aid;
 		$this->Fields['position'] = $this->TypeLink->GetPositionLink(true);
 		//设置一些全局参数的值
 		foreach($PubFields as $k=>$v) $this->Fields[$k] = $v;
 		if($this->Fields['litpic']=="") $this->Fields['litpic'] = $this->Fields["phpurl"]."/img/dfpic.gif";

 		//读取附加表信息，并把附加表的资料经过编译处理后导入到$this->Fields中，以方便在
 		//模板中用 {dede:field name='fieldname' /} 标记统一调用
 		if($this->AddTable!="")
 		{
 		  $row = $this->dsql->GetOne("select * from ".trim($this->ChannelUnit->ChannelInfos["addtable"])." where aid=$aid ");
 		  if(is_array($row)) foreach($row as $k=>$v){ if(ereg("[A-Z]",$k)) $row[strtolower($k)] = $v; }
 		  foreach($this->ChannelUnit->ChannelFields as $k=>$arr)
 		  {
 		  	if(isset($row[$k]))
 		  	{
 		  		 if($arr["rename"]!="") $nk = $arr["rename"];
 		  		 else $nk = $k;
 		  		 $this->Fields[$nk] = $this->ChannelUnit->MakeField($k,$row[$k]);
 		  		 if($arr['type']=='htmltext' && $GLOBALS['cfg_keyword_replace']=='Y'){
 		  			 $this->Fields[$nk] = $this->ReplaceKeyword($this->Fields['keywords'],$this->Fields[$nk]);
 		  		 }
 		  	}
 		  }//End foreach
 		}
 		unset($row);

 		//处理要分页显示的字段
 		//---------------------------
 		$this->SplitTitles = Array();
 		if($this->SplitPageField!="" && $GLOBALS['cfg_arcsptitle']='Y' &&
 		 isset($this->Fields[$this->SplitPageField]))
 	  {
 			$this->SplitFields = explode("#p#",$this->Fields[$this->SplitPageField]);
 			$i = 1;
 			foreach($this->SplitFields as $k=>$v)
 			{
 				$tmpv = cn_substr($v,50);
 				$pos = strpos($tmpv,'#e#');
 				if($pos>0)
 				{
 					 $st = trim(cn_substr($tmpv,$pos));
 					 if($st==""||$st=="副标题"||$st=="分页标题"){
 						 $this->SplitFields[$k] = preg_replace("/^(.*)#e#/is","",$v);
 						 continue;
 					 }else{
 						 $this->SplitFields[$k] = preg_replace("/^(.*)#e#/is","",$v);
 						 $this->SplitTitles[$k] = $st;
 				   }
 				}else{
 					continue;
 				}
 				$i++;
 			}
 			$this->TotalPage = count($this->SplitFields);
 		}
 		$this->Fields['totalpage'] = $this->TotalPage;
 	}
 	//php4构造函数
 	//---------------------------
 	function Archives($aid){
 		$this->__construct($aid);
 	}
 	//----------------------------
  //生成静态HTML
  //----------------------------
  function MakeHtml()
  {
  	//读取模型信息错误
  	if($this->IsError) return '';
  	
  	$this->Fields["displaytype"] = "st";
  	
  	//分析要创建的文件名称
  	//------------------------------------------------------
  	if(!is_object($this->TypeLink)) $this->TypeLink = new TypeLink($this->Fields["typeid"]);
  	$filename = $this->TypeLink->GetFileNewName(
  	  $this->ArcID,$this->Fields["typeid"],$this->Fields["senddate"],
  	  $this->Fields["title"],$this->Fields["ismake"],
  	  $this->Fields["arcrank"],$this->TypeLink->TypeInfos['namerule'],$this->TypeLink->TypeInfos['typedir'],$this->Fields["money"],
  	  $this->TypeLink->TypeInfos['siterefer'],
  	  $this->TypeLink->TypeInfos['sitepath']
  	);
  	$filenames  = explode(".",$filename);
  	$this->ShortName = $filenames[count($filenames)-1];
  	if($this->ShortName=="") $this->ShortName = "html";
  	$fileFirst = eregi_replace("\.".$this->ShortName."$","",$filename);
  	
  	//对于已设置不生成HTML的文章直接返回网址
  	//------------------------------------------------
  	if($this->Fields['ismake']==-1||$this->Fields['arcrank']!=0||
  	   $this->Fields['typeid']==0||$this->Fields['money']>0)
  	{
  		return $this->GetTrueUrl($filename);
  	}
  	//跳转网址
  	else if($this->Fields['redirecturl']!='')
  	{
  		$truefilename = $this->GetTruePath().$fileFirst.".".$this->ShortName;
  		$tffp = fopen(dirname(__FILE__)."/../include/jump.html","r");
  		$tmpfile = fread($tffp,filesize(dirname(__FILE__)."/../include/jump.html"));
  		fclose($tffp);
  		$tmpfile = str_replace('[title]',$this->Fields['title'],$tmpfile);
  		$tmpfile = str_replace('[aid]',$this->Fields['ID'],$tmpfile);
  		$tmpfile = str_replace('[description]',$this->Fields['description'],$tmpfile);
  		$tmpfile = str_replace('[redirecturl]',$this->Fields['redirecturl'],$tmpfile);
  		$fp = @fopen($truefilename,"w") or die("Create File False：$filename");
		  fwrite($fp,$tmpfile);
		  fclose($fp);
		  return $this->GetTrueUrl($filename);
  	}

  	$filenames  = explode("/",$filename);
  	$this->NameFirst = eregi_replace("\.".$this->ShortName."$","",$filenames[count($filenames)-1]);
  	if($this->NameFirst=="") $this->NameFirst = $this->arcID;
  	//获得当前文档的全名
  	$filenameFull = $filename;
  	if(!eregi('http://',$filenameFull)) $filenameFull = $GLOBALS['cfg_basehost'].$filenameFull;
  	$this->Fields['arcurl'] = $filenameFull;
  	$this->Fields['fullname'] = $this->Fields['arcurl'];
  	//载入模板
  	$this->LoadTemplet();
  	
  	//循环生成HTML文件
    for($i=1;$i<=$this->TotalPage;$i++){
  	     if($i>1){ $truefilename = $this->GetTruePath().$fileFirst."_".$i.".".$this->ShortName; }
  	     else{ $truefilename = $this->GetTruePath().$filename; }
  	     $this->Fields['namehand'] = $fileFirst;
  	     $this->ParseDMFields($i,1);
  	     $this->dtp->SaveTo($truefilename);
    }
    $this->dsql->SetQuery("Update `{$this->MainTable}` set ismake=1 where ID='".$this->ArcID."'");
    $this->dsql->ExecuteNoneQuery();
  	return $this->GetTrueUrl($filename);
  }
  //----------------------------
 	//获得真实连接路径
 	//----------------------------
 	function GetTrueUrl($nurl)
 	{
 		
 		if($GLOBALS['cfg_multi_site']=='Y' && !eregi('php\?',$nurl)){
 			if($this->TypeLink->TypeInfos['siteurl']=="") $nsite = $GLOBALS["cfg_mainsite"];
 			else $nsite = $this->TypeLink->TypeInfos['siteurl'];
 			$nurl = ereg_replace("/$","",$nsite).$nurl;
 		}
 		if($nurl != $this->fullurl){
 			$this->dsql->SetQuery("update #@__full_search set url='$nurl' where aid='$this->ArcID'");
 			$this->dsql->ExecuteNoneQuery();
 		}
 		return $nurl;
 	}
 	//----------------------------
 	//获得站点的真实根路径
 	//----------------------------
 	function GetTruePath()
 	{
 		if($GLOBALS['cfg_multi_site']=='Y'){
 		   if($this->TypeLink->TypeInfos['siterefer']==1) $truepath = ereg_replace("/{1,}","/",$GLOBALS["cfg_basedir"]."/".$this->TypeLink->TypeInfos['sitepath']);
	     else if($this->TypeLink->TypeInfos['siterefer']==2) $truepath = $this->TypeLink->TypeInfos['sitepath'];
	     else $truepath = $GLOBALS["cfg_basedir"];
	  }else{
	  	$truepath = $GLOBALS["cfg_basedir"];
	  }
	  return $truepath;
 	}
  //----------------------------
  //获得指定键值的字段
  //----------------------------
  function GetField($fname)
  {
  	if(isset($this->Fields[$fname])) return $this->Fields[$fname];
  	else return "";
  }
  //-----------------------------
  //获得模板文件位置
  //-----------------------------
  function GetTempletFile()
  {
 	  global $cfg_basedir,$cfg_templets_dir,$cfg_df_style;
 	  $cid = $this->ChannelUnit->ChannelInfos["nid"];
 	  if($this->Fields['templet']!=''){ $filetag = MfTemplet($this->Fields['templet']); }
 	  else{ $filetag = MfTemplet($this->TypeLink->TypeInfos["temparticle"]); }
 	  $tid = $this->Fields["typeid"];
 	  $filetag = str_replace("{cid}",$cid,$filetag);
 	  $filetag = str_replace("{tid}",$tid,$filetag);
 	  $tmpfile = $cfg_basedir.$cfg_templets_dir."/".$filetag;
 	  if($cid=='spec'){
 	  	if($this->Fields['templet']!=''){ $tmpfile = $cfg_basedir.$cfg_templets_dir."/".MfTemplet($this->Fields['templet']); }
 	  	else $tmpfile = $cfg_basedir.$cfg_templets_dir."/{$cfg_df_style}/article_spec.htm";
 	  }
 	  if(!file_exists($tmpfile)) $tmpfile = $cfg_basedir.$cfg_templets_dir."/{$cfg_df_style}/article_default.htm";
 	  return $tmpfile;
  }
  //----------------------------
 	//动态输出结果
 	//----------------------------
 	function display()
 	{

 		//读取模型信息错误
 		if($this->IsError) return '';
 		$this->LoadTemplet();
 		//跳转网址
  	if($this->Fields['redirecturl']!='')
  	{
  		$tffp = fopen(dirname(__FILE__)."/../include/jump.html","r");
  		$tmpfile = fread($tffp,filesize(dirname(__FILE__)."/../include/jump.html"));
  		fclose($tffp);
  		$tmpfile = str_replace('[title]',$this->Fields['title'],$tmpfile);
  		$tmpfile = str_replace('[aid]',$this->Fields['ID'],$tmpfile);
  		$tmpfile = str_replace('[description]',$this->Fields['description'],$tmpfile);
  		$tmpfile = str_replace('[redirecturl]',$this->Fields['redirecturl'],$tmpfile);
  		echo $tmpfile;
		  return '';
  	}
 		$this->Fields["displaytype"] = "dm";
 	  $this->Fields['arcurl'] = $GLOBALS['cfg_phpurl']."/plus.php?aid=".$this->Fields['ID'];
 	  $pageCount = $this->NowPage;
 	  $this->ParseDMFields($pageCount,0);
  	$this->dtp->display();
 	}
 	//--------------
 	//载入模板
 	//--------------
 	function LoadTemplet()
 	{
 		global $cfg_basedir;
 		if($this->TempSource=='')
 		{
 			$tempfile = $this->GetTempletFile();
 		  if(!file_exists($tempfile)||!is_file($tempfile)){
 			  return false;
 		  }
 		  $this->dtp->LoadTemplate($tempfile);
 		  $this->TempSource = $this->dtp->SourceString;
 		  $this->ParseTempletsFirst();
 		}else{
 			$this->dtp->LoadSource($this->TempSource);
 			$this->ParseTempletsFirst();
 		}
 		$this->TempletsFile = ereg_replace("^".$cfg_basedir,'',$tempfile);
 		 return true;
 	}
  //--------------------------------
 	//解析模板，对固定的标记进行初始给值
 	//--------------------------------
 	function ParseTempletsFirst()
 	{
 		 //对公用标记的解析，这里对对象的调用均是用引用调用的，因此运算后会自动改变传递的对象的值
 	   MakePublicTag($this,$this->dtp,$this->PartView,$this->TypeLink,
 	        $this->TypeLink->TypeInfos['ID'],$this->Fields['id'],$this->Fields['channel']);
  }
 	//--------------------------------
 	//解析模板，对内容里的变动进行赋值
 	//--------------------------------
 	function ParseDMFields($pageNo,$ismake=1)
 	{
 		$this->NowPage = $pageNo;
 		$this->Fields['nowpage'] = $this->NowPage;
 		if($this->SplitPageField!="" &&
 		  isset($this->Fields[$this->SplitPageField]))
 		{
 			$this->Fields[$this->SplitPageField] = $this->SplitFields[$pageNo - 1];
 		}
 		//-------------------------
 	  //解析模板
 		//-------------------------
 		if(is_array($this->dtp->CTags))
 		{
 			foreach($this->dtp->CTags as $tagid=>$ctag){
 				 $tagname = $ctag->GetName();
 				 if($tagname=="field")
 				 {
 					  $this->dtp->Assign($tagid,$this->GetField($ctag->GetAtt("name")));
 				 }
 				 else if($tagname=="pagebreak")
 				 {
 			      if($ismake==0)
 			      { $this->dtp->Assign($tagid,$this->GetPagebreakDM($this->TotalPage,$this->NowPage,$this->ArcID)); }
 			      else
 			      { $this->dtp->Assign($tagid,$this->GetPagebreak($this->TotalPage,$this->NowPage,$this->ArcID)); }
 		     }
 		     else if($tagname=='prenext')
 		     {
 		     	  $this->dtp->Assign($tagid,$this->GetPreNext($ctag->GetAtt("get")));
 		     }
 		     else if($ctag->GetName()=="pagetitle")
 		     {
 			      if($ismake==0)
 			      { $this->dtp->Assign($tagid,$this->GetPageTitlesDM($ctag->GetAtt("style"),$pageNo)); }
 			      else
 			      { $this->dtp->Assign($tagid,$this->GetPageTitlesST($ctag->GetAtt("style"),$pageNo)); }
 		     }
 		     else if($ctag->GetName()=="memberinfo")
 		     {
 		     	 $this->dtp->Assign($tagid,$this->GetMemberInfo());
 		     }
 		     else if($ctag->GetName()=="fieldlist")
 		     {
 		     	 $tagidnnertext = trim($ctag->GetInnerText());
 		     	 if($tagidnnertext=="") $tagidnnertext = GetSysTemplets("tag_fieldlist.htm");
 		     	 $dtp2 = new DedeTagParse();
	         $dtp2->SetNameSpace("field","[","]");
 		     	 $dtp2->LoadSource($tagidnnertext);
           $oldSource = $dtp2->SourceString;
           $oldCtags = $dtp2->CTags;
           $res = "";
 		     	 if(is_array($this->ChannelUnit->ChannelFields) && is_array($dtp2->CTags))
 		     	 {
 		     	   foreach($this->ChannelUnit->ChannelFields as $k=>$v)
 		     	   {
 		     	 	   $dtp2->SourceString = $oldSource;
               $dtp2->CTags = $oldCtags;
               $fname = $v['itemname'];
               if($v['type']=="datetime"){
               	 @$this->Fields[$k] = GetDateTimeMk($this->Fields[$k]);
               }
               foreach($dtp2->CTags as $tid=>$ctag){
               	 if($ctag->GetName()=='name') $dtp2->Assign($tid,$fname);
               	 else if($ctag->GetName()=='value') @$dtp2->Assign($tid,$this->Fields[$k]);
               }
               $res .= $dtp2->GetResult();
 		     	   }
 		     	 }
 		     	 $this->dtp->Assign($tagid,$res);

 		     }//end if
 			}//结束模板循环
 		}
 	}
 	//---------------------------
 	//关闭所占用的资源
 	//---------------------------
 	function Close()
 	{
 		$this->FixedValues = "";
 		$this->Fields = "";
 		if(is_object($this->dsql)) $this->dsql->Close();
 		if(is_object($this->ChannelUnit)) $this->ChannelUnit->Close();
 		if(is_object($this->TypeLink)) $this->TypeLink->Close();
 		if(is_object($this->PartView)) $this->PartView->Close();
 	}
 	//----------------------
 	//获得本文的投稿作者信息
 	//----------------------
 	function GetMemberInfo()
 	{
 		if(!isset($this->MemberInfos['ID'])){
 			if($this->Fields['memberID']==0) return '';
 			else{
 			  $this->MemberInfos = $this->dsql->GetOne("Select ID,userid,uname,spacename,spaceimage From #@__member where ID='{$this->Fields['memberID']}' ");
 			}
 		}
 		if(!isset($this->MemberInfos['ID'])) return "";
 		else{
 			$minfo  = "<a href='".$cfg_memberurl."/index.php?uid=".$this->MemberInfos['userid']."'>浏览 <font color='red'><b>";
 			$minfo .= $this->MemberInfos['uname']."</font></b> 的个人空间</a>\r\n";
 			return $minfo;
 		}
 	}
 	//--------------------------
 	//获取上一篇，下一篇链接
 	//--------------------------
 function GetPreNext($gtype='')
 {
   $rs = "";
   if(count($this->PreNext)<2)
   {

 		  $aid = $this->ArcID;
 		  $next = " arc.ID>'$aid' And arc.arcrank>-1 And typeid='{$this->Fields['typeid']}' order by arc.ID asc ";
 		  $pre = " arc.ID<'$aid' And arc.arcrank>-1 And typeid='{$this->Fields['typeid']}' order by arc.ID desc ";
 		  $query = "Select arc.ID,arc.title,arc.shorttitle,
 		arc.typeid,arc.ismake,arc.senddate,arc.arcrank,arc.money,
		t.typedir,t.typename,t.namerule,t.namerule2,t.ispart,
		t.moresite,t.siteurl
		from `{$this->MainTable}` arc left join #@__arctype t on arc.typeid=t.ID
		where ";
		  $nextRow = $this->dsql->GetOne($query.$next);
		  $preRow = $this->dsql->GetOne($query.$pre);
		  if(is_array($preRow))
		  {
			   $mlink = GetFileUrl($preRow['ID'],$preRow['typeid'],$preRow['senddate'],$preRow['title'],$preRow['ismake'],$preRow['arcrank'],$preRow['namerule'],$preRow['typedir'],$preRow['money'],true,$preRow['siteurl']);
         $this->PreNext['pre'] = "上一篇：<a href='$mlink'>{$preRow['title']}</a> ";
		  }
		  else{
			  $this->PreNext['pre'] = "上一篇：没有了 ";
		  }
		  if(is_array($nextRow))
		  {
			   $mlink = GetFileUrl($nextRow['ID'],$nextRow['typeid'],$nextRow['senddate'],$nextRow['title'],$nextRow['ismake'],$nextRow['arcrank'],$nextRow['namerule'],$nextRow['typedir'],$nextRow['money'],true,$nextRow['siteurl']);
         $this->PreNext['next'] = "下一篇：<a href='$mlink'>{$nextRow['title']}</a> ";
		  }
		  else{
			  $this->PreNext['next'] = "下一篇：没有了 ";
	    }
    }

		if($gtype=='pre'){
			$rs =  $this->PreNext['pre'];
		}
		else if($gtype=='next'){
			$rs =  $this->PreNext['next'];
		}
		else{
			$rs =  $this->PreNext['pre']." &nbsp; ".$this->PreNext['next'];
		}

		return $rs;
 }
 	//------------------------
 	//获得动态页面分页列表
 	//------------------------
 	function GetPagebreakDM($totalPage,$nowPage,$aid)
	{
		if($totalPage==1){ return ""; }
		$PageList = '';   // "共".$totalPage."页: ";
		$nPage = $nowPage-1;
		$lPage = $nowPage+1;
		if($nowPage==1) $PageList.="<a href='#'>上一页</a> ";
		else{
		  if($nPage==1) $PageList.="<a href='view.php?aid=$aid'>上一页</a> ";
		  else $PageList.="<a href='view.php?aid=$aid&pageno=$nPage'>上一页</a> ";
		}
		for($i=1;$i<=$totalPage;$i++)
		{
			if($i==1){
			  if($nowPage!=1) $PageList.="<a href='view.php?aid=$aid'>1</a> ";
			  else $PageList.="<strong>1</strong> ";
			}else{
			  $n = $i;
			  if($nowPage!=$i) $PageList.="<a href='view.php?aid=$aid&pageno=$i'>".$n."</a> ";
			  else $PageList.="<strong>$n</strong> ";
			}
		}
		if($lPage <= $totalPage) $PageList.="<a href='view.php?aid=$aid&pageno=$lPage'>下一页</a> ";
		else $PageList.= "<a href='#'>下一页</a>";
		return $PageList;
	}
	//-------------------------
	//获得静态页面分页列表
	//-------------------------
	function GetPagebreak($totalPage,$nowPage,$aid)
	{
		if($totalPage==1){ return ""; }
		$PageList = '';   // "共".$totalPage."页: ";
		$nPage = $nowPage-1;
		$lPage = $nowPage+1;
		if($nowPage==1) $PageList.="<a href='#'>上一页</a>";
		else{
		  if($nPage==1) $PageList.="<a href='".$this->NameFirst.".".$this->ShortName."'>上一页</a> ";
		  else $PageList.="<a href='".$this->NameFirst."_".$nPage.".".$this->ShortName."'>上一页</a> ";
		}
		for($i=1;$i<=$totalPage;$i++)
		{
			if($i==1){
			  if($nowPage!=1) $PageList.="<a href='".$this->NameFirst.".".$this->ShortName."'>1</a> ";
			  else $PageList.="<strong>1</strong>";
			}else{
			  $n = $i;
			  if($nowPage!=$i) $PageList.="<a href='".$this->NameFirst."_".$i.".".$this->ShortName."'>".$n."</a> ";
			  else $PageList.="<strong>$n</strong>";
			}
		}
		if($lPage <= $totalPage) $PageList.="<a href='".$this->NameFirst."_".$lPage.".".$this->ShortName."'>下一页</a> ";
		else $PageList.= "<a href='#'>下一页</a>";
		return $PageList;
	}
	//-------------------------
	//获得动态页面小标题
	//-------------------------
	function GetPageTitlesDM($styleName,$pageNo)
	{
		if($this->TotalPage==1){ return ""; }
		if(count($this->SplitTitles)==0){ return ""; }
		$i=1;
		$aid = $this->ArcID;
		if($styleName=='link')
		{
			$revalue = "";
		  foreach($this->SplitTitles as $k=>$v){
			   if($i==1) $revalue .= "<a href='view.php?aid=$aid&pageno=$i'>$v</a> \r\n";
		     else{
		     	 if($pageNo==$i) $revalue .= " $v \r\n";
		     	 else $revalue .= "<a href='view.php?aid=$aid&pageno=$i'>$v</a> \r\n";
		     }
		     $i++;
		  }
	  }else
	  {
		  $revalue = "<select id='dedepagetitles' onchange='location.href=this.options[this.selectedIndex].value;'>\r\n";
			foreach($this->SplitTitles as $k=>$v){
			   if($i==1) $revalue .= "<option value='".$this->Fields['phpurl']."/view.php?aid=$aid&pageno=$i'>{$i}、{$v}</option>\r\n";
		     else{
		     	 if($pageNo==$i) $revalue .= "<option value='".$this->Fields['phpurl']."/view.php?aid=$aid&pageno=$i' selected>{$i}、{$v}</option>\r\n";
		     	 else $revalue .= "<option value='".$this->Fields['phpurl']."/view.php?aid=$aid&pageno=$i'>{$i}、{$v}</option>\r\n";
		     }
		     $i++;
		  }
		  $revalue .= "</select>\r\n";
	  }
		return $revalue;
	}
	//-------------------------
	//获得静态页面小标题
	//-------------------------
	function GetPageTitlesST($styleName,$pageNo)
	{
		if($this->TotalPage==1){ return ""; }
		if(count($this->SplitTitles)==0){ return ""; }
		$i=1;
		if($styleName=='link')
		{
			$revalue = "";
		  foreach($this->SplitTitles as $k=>$v){
			   if($i==1) $revalue .= "<a href='".$this->NameFirst.".".$this->ShortName."'>$v</a> \r\n";
		     else{
		     	  if($pageNo==$i) $revalue .= " $v \r\n";
		        else  $revalue .= "<a href='".$this->NameFirst."_".$i.".".$this->ShortName."'>$v</a> \r\n";
		     }
		     $i++;
		  }
	  }else
	  {
		  $revalue = "<select id='dedepagetitles' onchange='location.href=this.options[this.selectedIndex].value;'>\r\n";
			foreach($this->SplitTitles as $k=>$v){
			   if($i==1) $revalue .= "<option value='".$this->NameFirst.".".$this->ShortName."'>{$i}、{$v}</option>\r\n";
		     else{
		     	  if($pageNo==$i) $revalue .= "<option value='".$this->NameFirst."_".$i.".".$this->ShortName."' selected>{$i}、{$v}</option>\r\n";
		     	  else $revalue .= "<option value='".$this->NameFirst."_".$i.".".$this->ShortName."'>{$i}、{$v}</option>\r\n";
		     }
		     $i++;
		  }
		  $revalue .= "</select>\r\n";
	  }
		return $revalue;
	}
	//----------------------------
  //把指定关键字替换成链接
  //----------------------------
  function ReplaceKeyword($kw,&$body)
  {
  	global $cfg_cmspath;
  	$maxkey = 5;
  	$kws = explode(" ",trim($kw));
  	$i=0;
$words = array();
$hrefs = array();
  	foreach($kws as $k){
  		$k = trim($k);
  		if($k!=""){
  			if($i > $maxkey) break;
  			$myrow = $this->dsql->GetOne("select * from #@__keywords where keyword='".addslashes($k)."' And rpurl<>'' ");
  			if(is_array($myrow)){
  				 //$ka = "<a href='{$myrow['rpurl']}'>$k</a>";
  			   //$body = str_replace($k,$ka,$body);
  			   $words[] = $k;
  			   $hrefs[] = $myrow['rpurl'];
  		  }
  			$i++;
  		}
  	}
  	
  	$body = highlight($body, $words, $hrefs);

  	return $body;
  }
  
  
  
  
  
}//End Archives
?>