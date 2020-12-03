<?
require_once(dirname(__FILE__)."/inc_arcpart_view.php");
require_once(dirname(__FILE__)."/inc_downclass.php");
require_once(dirname(__FILE__)."/inc_channel_unit.php");
/******************************************************
//Copyright 2004-2006 by DedeCms.com itprato
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
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($aid)
 	{
 		$t1 = ExecTime();
 		$this->IsError = false;
 		$this->dsql = new DedeSql(false);
 		$this->ArcID = $aid;
 		$query = "Select arc.*,tp.reID,tp.typedir from #@__archives arc 
 		left join #@__arctype tp on tp.ID=arc.typeid where arc.ID='$aid'";
 		$row = $this->dsql->GetOne($query);
 		
 		if(!is_array($row)){
 			$this->dsql->Close();
 			$this->IsError = true;
 			return;
 	  }
 		
 		foreach($row as $k=>$v){
 			if(!ereg("[^0-9]",$k)) continue;
 			else $this->Fields[$k] = $v;
 		}
 		
 		if($this->Fields['channel']==0) $this->Fields['channel']=1;
 		$this->ChannelUnit = new ChannelUnit($this->Fields['channel'],$aid);
 		
 		$this->TypeLink = new TypeLink($this->Fields['typeid']);
 		
 		if($row['redirecturl']!="") return;
 		
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
 		else $this->NowPage = $GLOBALS["pageno"];
 		//特殊的字段数据处理
 		//-------------------------------------
 		$this->Fields['aid'] = $aid;
 		$this->Fields['id'] = $aid;
 		$this->Fields['position'] = $this->TypeLink->GetPositionLink(true);
 		//设置一些全局参数的值
 		foreach($GLOBALS['PubFields'] as $k=>$v) $this->Fields[$k] = $v;
 		if($this->Fields['litpic']=="") $this->Fields['litpic'] = $this->Fields["phpurl"]."/img/dfpic.gif";
 		
 		//读取附加表信息，并把附加表的资料经过编译处理后导入到$this->Fields中，以方便在
 		//模板中用 {dede:field name='fieldname' /} 标记统一调用
 		if($this->ChannelUnit->ChannelInfos["addtable"]!=""){
 		  $row = $this->dsql->GetOne("select * from ".trim($this->ChannelUnit->ChannelInfos["addtable"])." where aid=$aid ");
 		  if(is_array($row)) foreach($row as $k=>$v){ if(ereg("[A-Z]",$k)) $row[strtolower($k)] = $v; }
 		  foreach($this->ChannelUnit->ChannelFields as $k=>$arr)
 		  {
 		  	if(isset($row[$k])){
 		  		if($arr["rename"]!="") $nk = $arr["rename"];
 		  		else $nk = $k;
 		  		$this->Fields[$nk] = $this->ChannelUnit->MakeField($k,$row[$k]);
 		  		if($arr['type']=='htmltext' && $GLOBALS['cfg_keyword_replace']=='是'){
 		  			$this->Fields[$nk] = $this->ReplaceKeyword($this->Fields['keywords'],$this->Fields[$nk]);
 		  		}
 		  	} 
 		  }//End foreach
 		}
 		//完成附加表信息读取
 		unset($row);
 		//处理要分页显示的字段
 		//---------------------------
 		$this->SplitTitles = Array();
 		if($this->SplitPageField!="" && $GLOBALS['cfg_arcsptitle']='是' &&
 		 isset($this->Fields[$this->SplitPageField])){
 			$this->SplitFields = explode("#p#",$this->Fields[$this->SplitPageField]);
 			$i = 1;
 			foreach($this->SplitFields as $k=>$v){
 				$tmpv = cn_substr($v,50);
 				$pos = strpos($tmpv,'#e#');
 				if($pos>0){
 					$st = trim(cn_substr($tmpv,$pos));
 					if($st==""||$st=="副标题"||$st=="分页标题"){
 						$this->SplitFields[$k] = cn_substr($v,strlen($v),$pos+3);
 						continue;
 					}else{
 						$this->SplitFields[$k] = cn_substr($v,strlen($v),$pos+3);
 						$this->SplitTitles[$k] = $st;
 				  }
 				}else{ continue; }
 				$i++;
 			}
 			$this->TotalPage = count($this->SplitFields);
 		}
 		
 		$this->LoadTemplet();
 		$this->ParseTempletsFirst();
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
  	if($this->IsError) return "";
  	//分析要创建的文件名称
  	//------------------------------------------------------
  	$filename = $this->TypeLink->GetFileNewName(
  	  $this->ArcID,$this->Fields["typeid"],$this->Fields["senddate"],
  	  $this->Fields["title"],$this->Fields["ismake"],
  	  $this->Fields["arcrank"],"","",$this->Fields["money"],
  	  $this->TypeLink->TypeInfos['siterefer'],
  	  $this->TypeLink->TypeInfos['sitepath']
  	);
  	$filenames  = explode(".",$filename);
  	$this->ShortName = $filenames[count($filenames)-1];
  	if($this->ShortName=="") $this->ShortName = "html";
  	$fileFirst = eregi_replace("\.".$this->ShortName."$","",$filename);
  	$filenames  = explode("/",$filename);
  	$this->NameFirst = eregi_replace("\.".$this->ShortName."$","",$filenames[count($filenames)-1]);
  	if($this->NameFirst=="") $this->NameFirst = $this->arcID;
  	
  	//获得当前文档的全名
  	$filenameFull = GetFileUrl(
  	  $this->ArcID,$this->Fields["typeid"],$this->Fields["senddate"],
  	  $this->Fields["title"],$this->Fields["ismake"],
  	  $this->Fields["arcrank"],"","",$this->Fields["money"],
  	  true,
  	  $this->TypeLink->TypeInfos['siteurl']
  	);
  	if(!eregi('http://',$filenameFull)) $filenameFull = $GLOBALS['cfg_basehost'].$filenameFull;
  	$this->Fields['arcurl'] = $filenameFull;
  	$this->Fields['fullname'] = $this->Fields['arcurl'];
  	//对于已设置不生成HTML的文章直接返回网址
  	//------------------------------------------------
  	if($this->Fields['ismake']==-1||$this->Fields['arcrank']!=0||
  	   $this->Fields['typeid']==0||$this->Fields['money']>0)
  	{
  		$this->Close();
  		return $this->GetTrueUrl($filename);
  	}
  	//跳转网址
  	if($this->Fields['redirecturl']!="")
  	{
  		$truefilename = $this->GetTruePath().$fileFirst.".".$this->ShortName;
  		$pageHtml = "<html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">\n<title>转向：".$this->Fields['title']."</title>\n";
  		$pageHtml .= "<meta http-equiv=\"refresh\" content=\"1;URL=".$this->Fields['redirecturl']."\">\n</head>\n<body>\n";
      $pageHtml .= "现在正在转向：".$this->Fields['title']."，请稍候...<br/><br/>\n转向内容简介:".$this->Fields['description']."\n</body>\n</html>\n";
  		$fp = @fopen($truefilename,"w") or die("Create File False：$filename");
		  fwrite($fp,$pageHtml);
		  fclose($fp);
  	}else{ //循环生成HTML文件
  	  for($i=1;$i<=$this->TotalPage;$i++){
  	     if($i>1){ $truefilename = $this->GetTruePath().$fileFirst."_".$i.".".$this->ShortName; }
  	     else{ $truefilename = $this->GetTruePath().$filename; }
  	     $this->ParseDMFields($i,1);
  	     $this->dtp->SaveTo($truefilename);
      }
    }
    $this->dsql->SetQuery("Update #@__archives set ismake=1 where ID='".$this->ArcID."'");
    $this->dsql->ExecuteNoneQuery();
    $this->Close();
  	return $this->GetTrueUrl($filename);
  }
  //----------------------------
 	//获得真实连接路径
 	//----------------------------
 	function GetTrueUrl($nurl)
 	{
 		if($GLOBALS['cfg_multi_site']=='是' && !eregi('php\?',$nurl)){
 			if($this->TypeLink->TypeInfos['siteurl']=="") $nsite = $GLOBALS["cfg_mainsite"];
 			else $nsite = $this->TypeLink->TypeInfos['siteurl'];
 			$nurl = ereg_replace("/$","",$nsite).$nurl;
 		}
 		return $nurl;
 	}
 	//----------------------------
 	//获得站点的真实根路径
 	//----------------------------
 	function GetTruePath()
 	{
 		if($GLOBALS['cfg_multi_site']=='是'){
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
 		if($this->IsError){
 			$this->Close();
 			return "";
 		}
 		//跳转网址
  	if($this->Fields['redirecturl']!="")
  	{
  		$truefilename = $this->GetTruePath().$fileFirst.".".$this->ShortName;
  		$pageHtml = "<html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">\n<title>转向：".$this->Fields['title']."</title>\n";
  		$pageHtml .= "<meta http-equiv=\"refresh\" content=\"1;URL=".$this->Fields['redirecturl']."\">\n</head>\n<body>\n";
      $pageHtml .= "现在正在转向：".$this->Fields['title']."，请稍候...<br/><br/>\n转向内容简介:\n".$this->Fields['description']."\n</body>\n</html>\n";
  		echo $pageHtml;
		  $this->Close();
		  exit();
  	}
 		$pageCount = $this->NowPage;
 		$this->ParseDMFields($pageCount,0);
 		$this->Close();
  	$this->dtp->display();
  	
 	}
 	//--------------
 	//载入模板
 	//--------------
 	function LoadTemplet()
 	{
 		if($this->TempSource=="")
 		{
 			$tempfile = $this->GetTempletFile();
 		  if(!file_exists($tempfile)||!is_file($tempfile)){
 			  echo "模板文件：'".$tempfile."' 不存在，无法解析文档！";
 			  exit();
 		  }
 		  $this->dtp->LoadTemplate($tempfile);
 		  $this->TempSource = $this->dtp->SourceString;
 		}else{
 			$this->dtp->LoadSource($this->TempSource);
 		}
 	}
  //--------------------------------
 	//解析模板，对固定的标记进行初始给值
 	//--------------------------------
 	function ParseTempletsFirst()
 	{
 		if(is_array($this->dtp->CTags))
 		{
 			foreach($this->dtp->CTags as $tagid=>$ctag)
 			{
 				$tagname = $ctag->GetName();
 				
 				$typeid = $ctag->GetAtt('typeid');
 				if($typeid=="") $typeid = 0;
 				if($typeid==0) $typeid = $this->Fields['typeid'];
 				
 				if($tagname=="arclist"||$tagname=="artlist"||$tagname=="likeart"||$tagname=="hotart"
 			  ||$tagname=="imglist"||$tagname=="imginfolist"||$tagname=="coolart"||$tagname=="specart")
 			  { 
 			  	//特定的文章列表
 				  $channelid = $ctag->GetAtt("channelid");
 				  if($tagname=="imglist"||$tagname=="imginfolist"){ $listtype = "image"; }
 				  else if($tagname=="specart"){ $channelid = -1; $listtype=""; }
 				  else if($tagname=="coolart"){ $listtype = "commend"; }
 				  else{ $listtype = $ctag->GetAtt('type'); }
 				   
 				  if($tagname=="likeart") $keywords = ""; //str_replace(" ",",",trim($this->Fields['keywords']));
 				  else $keywords = $ctag->GetAtt('keyword');
 				  
 				  //排序
 				  if($tagname=="hotart") $orderby = "click";
 				  else if($tagname=="likeart") $orderby = "near";
 				  else $orderby = $ctag->GetAtt('orderby');
 				  
 				  //对相应的标记使用不同的默认innertext
 				  if(trim($ctag->GetInnerText())!="") $tagidnnertext = $ctag->GetInnerText();
 				  else if($tagname=="imglist") $tagidnnertext = GetSysTemplets("part_imglist.htm");
 				  else if($tagname=="imginfolist") $tagidnnertext = GetSysTemplets("part_imginfolist.htm");
 				  else $tagidnnertext = GetSysTemplets("part_arclist.htm");
 				  
 				  //兼容titlelength
 				  if($ctag->GetAtt('titlelength')!="") $titlelen = $ctag->GetAtt('titlelength');
 				  else $titlelen = $ctag->GetAtt('titlelen');
 				
 				  //兼容infolength
 				  if($ctag->GetAtt('infolength')!="") $tagidnfolen = $ctag->GetAtt('infolength');
 				  else $tagidnfolen = $ctag->GetAtt('infolen');
 				    
 				  //环境变量
 					if($tagname!="likeart"){
 						$gid = $this->Fields['typeid'];
 					  if($this->Fields['typeid2']!=0) $gid = $gid.",".$this->Fields['typeid2'];
 					}else{
 						$gid = 0;
 					}
 					/*if($this->Fields['reID']!=0 && $tagname=="likeart") $gid = $this->Fields['reID'];
 					else{
 					  $gid = $this->Fields['typeid'];
 					  if($this->Fields['typeid2']!=0) $gid = $gid.",".$this->Fields['typeid2'];
 					}*/

 					$typeid = trim($ctag->GetAtt("typeid"));
 				  if(empty($typeid)) $typeid = $gid;
 					
 				  $this->dtp->Assign($tagid,
 				       $this->PartView->GetArcList(
 				         $typeid,
 				         $ctag->GetAtt("row"),
 				         $ctag->GetAtt("col"),
 				         $titlelen,
 				         $tagidnfolen,
 				         $ctag->GetAtt("imgwidth"),
 				         $ctag->GetAtt("imgheight"),
 				         $listtype,
 				         $orderby,
 				         $keywords,
 				         $tagidnnertext,
 				         $ctag->GetAtt("tablewidth"),
 				         $this->ArcID,
 				         "",
 				         $channelid,
 				         $ctag->GetAtt("limit"),
 				         $ctag->GetAtt("att")
 				        )
 				  );
 			  }
 			  //自定义标记
 			  //-----------------------
 			  else if($ctag->GetName()=="mytag")
 			  {
 				  $this->dtp->Assign($tagid,$this->PartView->GetMyTag(
 				        $typeid,
 				        $ctag->GetAtt("name"),
 				        $ctag->GetAtt("ismake")
 				     )
 				  );
 			  }
 			  //热门关键字
 			  else if($tagname=="hotwords"){
 				  $this->dtp->Assign($tagid,
 				  GetHotKeywords($this->dsql,$ctag->GetAtt('num'),$ctag->GetAtt('subday'),$ctag->GetAtt('maxlength')));
 			  }
 			  //上下篇链接
 			  else if($tagname=="prenext"){
 			  	$this->dtp->Assign($tagid,$this->GetPreNext());
 			  }
 			  //获得单个栏目目的属性
 			  //-----------------------------
 			  else if($tagname=="onetype"||$tagname=="typeinfo"){
 				  $this->dtp->Assign($tagid,$this->PartView->GetOneType($typeid,$ctag->GetInnerText()));
 			  }
 			  //广告标记
 			  //-----------------------
 			  else if($ctag->GetName()=="myad"){
 				  $this->dtp->Assign($tagid,$this->PartView->GetMyAd($typeid,$ctag->GetAtt("name")));
 			  }
 			  else if($tagname=="loop")
 			  {
 				  $this->dtp->Assign($tagid,
				    $this->PartView->GetTable(
					    $ctag->GetAtt("table"),
					    $ctag->GetAtt("row"),
					    $ctag->GetAtt("sort"),
					    $ctag->GetAtt("if"),
					    $ctag->GetInnerText()
					  )
			    );
 			  }
 				else if($ctag->GetName()=="channel")
 				{
 					if($ctag->GetAtt("line")!="") $nrow = trim($ctag->GetAtt("line"));
 					else $nrow = trim($ctag->GetAtt("row"));
 				  
 				  if($nrow=="") $nrow = 8;
 			
 					$this->dtp->Assign($tagid,$this->TypeLink->GetChannelList(
 					  $this->Fields['typeid'],
 					  $this->Fields['reID'],
 					  $nrow,
 					  $ctag->GetAtt("type"),
 					  $ctag->GetInnerText(),
 				    $ctag->GetAtt("col"),
 				    $ctag->GetAtt("tablewidth"),
 				    $ctag->GetAtt("currentstyle")
 					));
 				}//End 判断标记
 		  }//End foreach
 		}//is_array
  }
 	//--------------------------------
 	//解析模板，对内容里的变动进行赋值
 	//--------------------------------
 	function ParseDMFields($pageNo,$ismake=1)
 	{
 		$this->NowPage = $pageNo;
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
 			foreach($this->dtp->CTags as $i=>$ctag){
 				 if($ctag->GetName()=="field"){
 					  $this->dtp->Assign($i,$this->GetField($ctag->GetAtt("name")));
 				 }
 				 else if($ctag->GetName()=="pagebreak"){
 			      if($ismake==0)
 			      { $this->dtp->Assign($i,$this->GetPagebreakDM($this->TotalPage,$this->NowPage,$this->ArcID)); }
 			      else
 			      { $this->dtp->Assign($i,$this->GetPagebreak($this->TotalPage,$this->NowPage,$this->ArcID)); }
 		     }
 		     else if($ctag->GetName()=="pagetitle"){
 			      if($ismake==0)
 			      { $this->dtp->Assign($i,$this->GetPageTitlesDM($ctag->GetAtt("style"),$pageNo)); }
 			      else
 			      { $this->dtp->Assign($i,$this->GetPageTitlesST($ctag->GetAtt("style"),$pageNo)); }
 		     }
 		     else if($ctag->GetName()=="fieldlist")
 		     {
 		     	 $innertext = trim($ctag->GetInnerText());
 		     	 if($innertext=="") $innertext = GetSysTemplets("tag_fieldlist.htm");
 		     	 $dtp2 = new DedeTagParse();
	         $dtp2->SetNameSpace("field","[","]");
 		     	 $dtp2->LoadSource($innertext);
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
               foreach($dtp2->CTags as $tid=>$ctag)
               {
               	 if($ctag->GetName()=='name') $dtp2->Assign($tid,$fname);
               	 else if($ctag->GetName()=='value') @$dtp2->Assign($tid,$this->Fields[$k]);
               }
               $res .= $dtp2->GetResult();
 		     	   }
 		     	 }
 		     	 $this->dtp->Assign($i,$res);
 		     
 		     }//end case
 		     
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
 	//--------------------------
 	//获取上一篇，下一篇链接
 	//--------------------------
 	function GetPreNext()
 	{
 		$rs = "";
 		$aid = $this->ArcID;
 		$next = " #@__archives.ID>'$aid' order by #@__archives.ID asc ";
 		$pre = " #@__archives.ID<'$aid' order by #@__archives.ID desc ";
 		$query = "Select #@__archives.ID,#@__archives.title,
 		#@__archives.typeid,#@__archives.ismake,#@__archives.senddate,#@__archives.arcrank,#@__archives.money,
		#@__arctype.typedir,#@__arctype.typename,#@__arctype.namerule,#@__arctype.namerule2,#@__arctype.ispart,
		#@__arctype.moresite,#@__arctype.siteurl 
		from #@__archives left join #@__arctype on #@__archives.typeid=#@__arctype.ID
		where ";
		$nextRow = $this->dsql->GetOne($query.$next);
		$preRow = $this->dsql->GetOne($query.$pre);
		if(is_array($preRow)){
			 $mlink = GetFileUrl($preRow['ID'],$preRow['typeid'],$preRow['senddate'],$preRow['title'],$preRow['ismake'],$preRow['arcrank'],$preRow['namerule'],$preRow['typedir'],$preRow['money'],true,$preRow['siteurl']);
       $rs .= "上一篇：<a href='$mlink'>{$preRow['title']}</a> ";
		}
		else{
			$rs .= "上一篇：没有了 ";
		}
		if(is_array($nextRow)){
			 $mlink = GetFileUrl($nextRow['ID'],$nextRow['typeid'],$nextRow['senddate'],$nextRow['title'],$nextRow['ismake'],$nextRow['arcrank'],$nextRow['namerule'],$nextRow['typedir'],$nextRow['money'],true,$nextRow['siteurl']);
       $rs .= " &nbsp; 下一篇：<a href='$mlink'>{$nextRow['title']}</a> ";
		}
		else{
			$rs .= " &nbsp; 下一篇：没有了 ";
	  }
		return $rs;
  }
 	//------------------------
 	//获得动态页面分页列表
 	//------------------------
 	function GetPagebreakDM($totalPage,$nowPage,$aid)
	{	
		if($totalPage==1){ return ""; }
		$PageList = "共".$totalPage."页: ";
		$nPage = $nowPage-1;
		$lPage = $nowPage+1;
		if($nowPage==1) $PageList.="上一页 ";
		else{ 
		  if($nPage==1) $PageList.="<a href='view.php?aid=$aid'>上一页</a> ";
		  else $PageList.="<a href='view.php?aid=$aid&pageno=$nPage'>上一页</a> ";
		}
		for($i=1;$i<=$totalPage;$i++)
		{
			if($i==1){
			  if($nowPage!=1) $PageList.="<a href='view.php?aid=$aid'>[1]</a> ";
			  else $PageList.="1 ";
			}else{
			  $n = $i;
			  if($nowPage!=$i) $PageList.="<a href='view.php?aid=$aid&pageno=$i'>[".$n."]</a> ";
			  else $PageList.="$n ";
			}
		}
		if($lPage <= $totalPage) $PageList.="<a href='view.php?aid=$aid&pageno=$lPage'>下一页</a> ";
		else $PageList.= "下一页 ";
		return $PageList;
	}
	//-------------------------
	//获得静态页面分页列表
	//-------------------------
	function GetPagebreak($totalPage,$nowPage,$aid)
	{
		if($totalPage==1){ return ""; }
		$PageList = "共".$totalPage."页: ";
		$nPage = $nowPage-1;
		$lPage = $nowPage+1;
		if($nowPage==1) $PageList.="上一页 ";
		else{ 
		  if($nPage==1) $PageList.="<a href='".$this->NameFirst.".".$this->ShortName."'>上一页</a> ";
		  else $PageList.="<a href='".$this->NameFirst."_".$nPage.".".$this->ShortName."'>上一页</a> ";
		}
		for($i=1;$i<=$totalPage;$i++)
		{
			if($i==1){
			  if($nowPage!=1) $PageList.="<a href='".$this->NameFirst.".".$this->ShortName."'>[1]</a> ";
			  else $PageList.="1 ";
			}else{
			  $n = $i;
			  if($nowPage!=$i) $PageList.="<a href='".$this->NameFirst."_".$i.".".$this->ShortName."'>[".$n."]</a> ";
			  else $PageList.="$n ";
			}
		}
		if($lPage <= $totalPage) $PageList.="<a href='".$this->NameFirst."_".$lPage.".".$this->ShortName."'>下一页</a> ";
		else $PageList.= "下一页 ";
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
  	foreach($kws as $k){
  		$k = trim($k);
  		if($k!=""){
  			if($i > $maxkey) break;
  			$myrow = $this->dsql->GetOne("select * from #@__keywords where keyword='$k' And rpurl<>'' ");
  			if(is_array($myrow)){
  				 $ka = "<a href='{$myrow['rpurl']}'><u>$k</u></a>";
  			   $body = str_replace($k,$ka,$body);
  		  }
  			$i++;
  		}
  	}
  	return $body;
  }
}//End Archives
?>