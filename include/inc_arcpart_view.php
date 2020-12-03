<?
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/pub_db_mysql.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/../include/inc_typeunit.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/inc_channel_unit.php");
//---------------------------
/******************************************************
//Copyright 2004-2006 by DedeCms.com itprato
//本类的用途是用于解析和创建全局性质的模板，如频道封面，主页，单个页面等
******************************************************/
class PartView
{
	var $dsql;
	var $dtp;
	var $dtp2;
	var $TypeID;
	var $Fields;
	var $TypeLink;
	var $pvCopy;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($typeid=0)
 	{
 		$this->TypeID = $typeid;
 		$this->pvCopy = "";
 		$this->dsql = new DedeSql(false);
 		$this->dtp = new DedeTagParse();
 		$this->dtp->SetNameSpace("dede","{","}");
 		$this->dtp2 = new DedeTagParse();
 		$this->dtp2->SetNameSpace("field","[","]");
 		$this->TypeLink = new TypeLink($typeid);
 		if(is_array($this->TypeLink->TypeInfos)){
 			foreach($this->TypeLink->TypeInfos as $k=>$v){
 				if(ereg("[^0-9]",$k)) $this->Fields[$k] = $v;
 			}
 		}
  }
  //php4构造函数
 	//---------------------------
 	function PartView($typeid=0){
 		$this->__construct($typeid);
 	}
 	//------------------------
 	//设置要解析的模板
 	//------------------------
 	function SetTemplet($temp,$stype="file")
 	{
 		if($stype=="string") $this->dtp->LoadSource($temp);
 		else $this->dtp->LoadTemplet($temp);
 		//设置一些全局参数的值
 		//---------------------------
 		$this->Fields['phpurl'] = $GLOBALS["cfg_plus_dir"];
 		$this->Fields['indexurl'] = $GLOBALS['cfg_indexurl']."/";
 		$this->Fields['indexurl'] = ereg_replace("/{1,}","/",$this->Fields['indexurl']);
 		$this->Fields['indexname'] = $GLOBALS["cfg_indexname"];
 		$this->Fields['templeturl'] = $GLOBALS["cfg_templets_dir"];
 		$this->Fields['memberurl'] = $GLOBALS["cfg_member_dir"];
 		$this->Fields['powerby'] = $GLOBALS["cfg_powerby"];
 		$this->Fields['webname'] = $GLOBALS["cfg_webname"];
 		$this->Fields['specurl'] = $GLOBALS["cfg_special"];
 		if($this->TypeID > 0){
 			$this->Fields['position'] = $this->TypeLink->GetPositionLink(true);
 		}
 		//---------------------------
 		$this->ParseTemplet();
 	}
 	//-----------------------
 	//显示内容
 	//-----------------------
 	function Display()
 	{
 		$this->dtp->Display();
 	}
 	//-----------------------
 	//获取内容
 	//-----------------------
 	function GetResult()
 	{
 		return $this->dtp->GetResult();
 	}
 	//------------------------
 	//保存结果为文件
 	//------------------------
 	function SaveToHtml($filename)
 	{
 		$this->dtp->SaveTo($filename);
 	}
 	//------------------------
 	//解析的模板
 	//------------------------
 	function ParseTemplet()
 	{
 		if(!is_array($this->dtp->CTags)) return "";
 		foreach($this->dtp->CTags as $tagid=>$ctag)
 		{
 			$tagname = $ctag->GetName();
 			/*---
 			function Tag_A_Fields
 			//field 标记的实现
 			---*/
 			if($tagname=="field")
 			{
 				if(isset($this->Fields[$ctag->GetAtt('name')])){
 					$this->dtp->Assign($tagid,$this->Fields[$ctag->GetAtt('name')]);
 				}
 			}
 			/*---
 			function Tag_B_ArcList
 			//arclist artlist imglist coolart hotart imginfolist specart 标记的实现
 			---*/
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
 				  
 				  //排序
 				  if($ctag->GetAtt('sort')!="") $orderby = $ctag->GetAtt('sort');
 				  else if($tagname=="hotart") $orderby = "click";
 				  else $orderby = $ctag->GetAtt('orderby');
 				  
 				  //对相应的标记使用不同的默认innertext
 				  if(trim($ctag->GetInnerText())!="") $innertext = $ctag->GetInnerText();
 				  else if($tagname=="imglist"){
 				  	$innertext = GetSysTemplets("part_imglist.htm");
 				  	if($ctag->GetAtt('type')=='') $listtype = 'image';
 				  }
 				  else if($tagname=="imginfolist"){
 				  	$innertext = GetSysTemplets("part_imginfolist.htm");
 				  	if($ctag->GetAtt('type')=='') $listtype = 'image';
 				  }
 				  else $innertext = GetSysTemplets("part_arclist.htm");
 				  
 				  //兼容titlelength
 				  if($ctag->GetAtt('titlelength')!="") $titlelen = $ctag->GetAtt('titlelength');
 				  else $titlelen = $ctag->GetAtt('titlelen');
 				
 				  //兼容infolength
 				  if($ctag->GetAtt('infolength')!="") $infolen = $ctag->GetAtt('infolength');
 				  else $infolen = $ctag->GetAtt('infolen');
 				  
 				  //类别ID
 				 if(trim($ctag->GetAtt('typeid'))=="" && $this->TypeID!=0){  $typeid = $this->TypeID;  }
 				 else{ $typeid = trim( $ctag->GetAtt('typeid') ); }
 				  
 				  $this->dtp->Assign($tagid,
 				      $this->GetArcList($typeid,
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
 			/*---
 			function Tag_C_channelArtlist
 			---*/
 			else if($tagname=="channelartlist")
 			{
 				//类别ID
 				if(trim($ctag->GetAtt('typeid'))=="" && $this->TypeID!=0){  $typeid = $this->TypeID;  }
 				else{ $typeid = trim( $ctag->GetAtt('typeid') ); }
 				//-------------------------------
 				$this->dtp->Assign($tagid,
 				     $this->GetChannelList($typeid,
 				        $ctag->GetAtt('col'),
 				        $ctag->GetAtt('tablewidth'),
 				        $ctag->GetInnerText()
 				     )
 				);
 			}
 			/*---
 			function Tag_D_channel
 			//channel 标记的实现
 			---*/
 			else if($tagname=="channel")
 			{
 				//设置环境参数
 				if(trim($ctag->GetAtt('typeid'))==""
 				 && $this->TypeID!=0){ $typeid = $this->TypeID; $reid = $this->TypeLink->TypeInfos['reID']; }
 				else{ $typeid = $ctag->GetAtt("typeid"); $reid=0; }
 				//-------------------------------
 				$this->dtp->Assign($tagid,
 				      $this->TypeLink->GetChannelList($typeid,
 				          $reid,
 				          $ctag->GetAtt("row"),
 				          $ctag->GetAtt("type"),
 				          $ctag->GetInnerText()
 				      )
 				 );
 			}
 			/*---
 			function Tag_E_mytag
 			---*/
 			else if($tagname=="mytag")
 			{
 				$this->dtp->Assign($tagid,
 				   $this->GetMyTag(
 				     $ctag->GetAtt("typeid"),
 				     $ctag->GetAtt("name"),
 				     $ctag->GetAtt("ismake")
 				   )
 				);
 			}
 			/*---
 			function Tag_F_vote
 			//投票标记的实现
 			---*/
 			else if($tagname=="vote")
 			{
 				$this->dtp->Assign($tagid,
				   $this->GetVote(
				     $ctag->GetAtt("id"),
				     $ctag->GetAtt("lineheight"),
             $ctag->GetAtt("tablewidth"),
				     $ctag->GetAtt("titlebgcolor"),
             $ctag->GetAtt("titlebackground"),
             $ctag->GetAtt("tablebgcolor")
           )
			   );
 			}
 			/*---
 			function Tag_G_flink
 			//friendlink 友情链接标记的实现
 			---*/
 			else if($tagname=="friendlink"||$tagname=="flink")
 			{
 				$this->dtp->Assign($tagid,
 				  $this->GetFriendLink(
 				    $ctag->GetAtt("type"),
 				    $ctag->GetAtt("row"),
 				    $ctag->GetAtt("col"),
 				    $ctag->GetAtt("titlelen"),
 				    $ctag->GetAtt("tablestyle")
 				  )
 				);
 			}
 			/*---
 			function Tag_H_mynews
 			---*/
 			else if($tagname=="mynews")
 			{
 				$this->dtp->Assign($tagid,
 				  $this->GetMyNews(
 				  $ctag->GetAtt("row"),
 				  $ctag->GetAtt("titlelen"),
 				  $ctag->GetInnerText()
 				  )
 				);
 			}
 			/*---
 			function Tag_I_loop
 			---*/
 			else if($tagname=="loop")
 			{
 				$this->dtp->Assign($tagid,
				  $this->GetTable(
					  $ctag->GetAtt("table"),
					  $ctag->GetAtt("row"),
					  $ctag->GetAtt("sort"),
					  $ctag->GetAtt("if"),
					  $ctag->GetInnerText()
					)
			  );
 			}
 		}//End Foreach
 	}
 	//----------------------------------
 	//GetArcList($typeid=0,$row=10,$titlelen=30,$infolen=160,
 	//$imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$keyword="",$innertext="")
  //获得一个单列的文档列表
  //---------------------------------
  function GetArcList($typeid=0,$row=10,$col=1,$titlelen=30,$infolen=160,
  $imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$keyword="",
  $innertext="",$tablewidth="100",$arcid=0,$idlist="",$channelid=0,$limit="")
  {
    if($typeid==0||$typeid=="") $typeid=$this->TypeID;
		
		if($row=="") $line = 10;
		else $line = $row;
		
		if($titlelen=="") $titlelen = 30;
		if($infolen=="") $infolen = 160;
    if($imgwidth=="") $imgwidth = 120;
    if($imgheight=="") $imgheight = 120;
    if($listtype=="") $listtype = "all";
    if($arcid=="") $arcid = 0;
    
    if($channelid=="") $channelid = 0;
    
		if($orderby=="") $orderby="default";
		else $orderby=strtolower($orderby);
		
		$tablewidth = str_replace("%","",$tablewidth);
		if($tablewidth=="") $tablewidth=100;
		if($col=="") $col = 1;
		$colWidth = ceil(100/$col); 
		$tablewidth = $tablewidth."%";
		$colWidth = $colWidth."%";
		
		$keyword = trim($keyword);
		$innertext = trim($innertext);
		if($innertext=="") $innertext = GetSysTemplets("part_arclist.htm");
		//按不同情况设定SQL条件
		//排序方式
		$orwhere = " #@__archives.arcrank > -1 ";
		
		if($channelid!=0){
			$orwhere .= " And #@__archives.channel = $channelid ";
		}
		else{
			$orwhere .= " And #@__archives.channel > 0 ";
		}
		
		//是否为推荐文档
		if(eregi("commend",$listtype)){
			$orwhere .= " And #@__archives.iscommend > 10  ";
		}
		//是否为带缩略图图片文档
		if(eregi("image",$listtype)){
			 $orwhere .= " And #@__archives.litpic <> ''  ";
		}
		//是否为推荐文档
		if(eregi("spec",$listtype) && $channelid!=-1){
			 $orwhere .= " And #@__archives.channel = -1  ";
		}
		
		
		if($arcid!=0) $orwhere .= " And #@__archives.ID<>'$arcid' ";
		$ordersql = "";
		
		//文档排序的方式
		//------------------------------
		if($orderby=="hot"||$orderby=="click") $ordersql = " order by #@__archives.click desc";
		else if($orderby=="pubdate") $ordersql = " order by #@__archives.pubdate desc";
		else if($orderby=="sortrank") $ordersql = " order by #@__archives.sortrank desc";
    else if($orderby=="id") $ordersql = "  order by #@__archives.ID desc";
    else if($orderby=="near" || ($arcid!=0 && $keyword!=""))
    {
    	//如果指定了文档ID,并使用关键字，则取最靠近文档ID的相似文章
    	$ordersql = " order by ABS(#@__archives.ID - ".$arcid.")";
    }
		else $ordersql=" order by #@__archives.senddate desc";
		
		//类别ID的条件，如果用 "," 分开,可以指定特定类目
		//------------------------------
		if($typeid!=0)
		{
		  $reids = explode(",",$typeid);
		  $ridnum = count($reids);
		  if($ridnum>1){
			  $tpsql = "";
		    for($i=0;$i<$ridnum;$i++){
				  if($tpsql=="") $tpsql .= " And (".TypeGetSunID($reids[$i],$this->dsql);
				  else $tpsql .= " Or ".TypeGetSunID($reids[$i],$this->dsql);
		    }
		    $tpsql .= ") ";
		    $orwhere .= $tpsql;
		    unset($tpsql);
		  }
		  else{
			  $orwhere .= " And ".TypeGetSunID($typeid,$this->dsql);
		  }
		  unset($reids);
	  }
		//指定的文档ID列表
		//----------------------------------
		if($idlist!="")
		{
			$reids = explode(",",$idlist);
		  $ridnum = count($reids);
		  $idlistSql = "";
		  for($i=0;$i<$ridnum;$i++){
				if($idlistSql=="") $idlistSql .= " And ( #@__archives.ID='".$reids[$i]."' ";
				else $idlistSql .= " Or #@__archives.ID='".$reids[$i]."' ";
		  }
		  $idlistSql .= ") ";
		  $orwhere .= $idlistSql;
		  unset($idlistSql);
		  unset($reids);
		  $row = $ridnum;
		}
		
		//关键字条件
		if($keyword!="")
		{
		  $keywords = explode(",",$keyword);
		  $ridnum = count($keywords);
		  $orwhere .= " And (#@__archives.keywords like '%".trim($keywords[0])." %' ";
		  for($i=1;$i<$ridnum;$i++){
			  if($keywords[$i]!="") $orwhere .= " Or #@__archives.keywords like '%".trim($keywords[$i])." %' ";
		  }
		  $orwhere .= ")";
		  unset($keywords);
	  }
	  $limit = trim(eregi_replace("limit","",$limit));
	  if($limit!="") $limitsql = " limit $limit ";
	  else $limitsql = " limit 0,$line ";
		//////////////
		$query = "Select #@__archives.ID,#@__archives.title,#@__archives.writer,#@__archives.source,#@__archives.iscommend,#@__archives.color,#@__archives.typeid,#@__archives.ismake,
		#@__archives.description,#@__archives.pubdate,#@__archives.senddate,#@__archives.arcrank,#@__archives.click,#@__archives.money,
		#@__archives.litpic,#@__arctype.typedir,#@__arctype.typename,#@__arctype.isdefault,
		#@__arctype.defaultname,#@__arctype.namerule,#@__arctype.namerule2,#@__arctype.ispart
		from #@__archives left join #@__arctype on #@__archives.typeid=#@__arctype.ID
		where $orwhere $ordersql $limitsql";
		 
		$this->dsql->SetQuery($query);
		$this->dsql->Execute("al");
    $artlist = "";
    if($col>1) $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
    $this->dtp2->LoadSource($innertext);
    $GLOBALS['autoindex'] = 0;
    for($i=0;$i<$line;$i++)
		{
       if($col>1) $artlist .= "<tr>\r\n";
       for($j=0;$j<$col;$j++)
			 {
         if($col>1) $artlist .= "	<td width='$colWidth'>\r\n";
         if($row = $this->dsql->GetArray("al"))
         {
           //处理一些特殊字段
           $row["description"] = cn_substr($row["description"],$infolen);
           $row["id"] =  $row["ID"];
           if($row["litpic"]=="") $row["litpic"] = $GLOBALS["cfg_plus_dir"]."/img/default.gif";
           $row["picname"] = $row["litpic"];
           $row["arcurl"] = $this->GetArcUrl($row["id"],$row["typeid"],$row["senddate"],$row["title"],
                        $row["ismake"],$row["arcrank"],$row["namerule"],$row["typedir"],$row["money"]);
           $row["typeurl"] = $this->GetListUrl($row["typeid"],$row["typedir"],$row["isdefault"],$row["defaultname"],$row["ispart"],$row["namerule2"]);
           $row["info"] = $row["description"];
           $row["filename"] = $row["arcurl"];
           $row["stime"] = GetDateMK($row["pubdate"]);
           $row["typelink"] = "<a href='".$row["typeurl"]."'>".$row["typename"]."</a>";
           $row["imglink"] = "<a href='".$row["filename"]."'><img src='".$row["picname"]."' border='0' width='$imgwidth' height='$imgheight'></a>";
           $row["image"] = "<img src='".$row["picname"]."' border='0' width='$imgwidth' height='$imgheight'>";
           $row["title"] = cn_substr($row["title"],$titlelen);
           $row["textlink"] = "<a href='".$row["filename"]."'>".$row["title"]."</a>";
           if($row["color"]!="") $row["title"] = "<font color='".$row["color"]."'>".$row["title"]."</font>";
           if($row["iscommend"]==5||$row["iscommend"]==16) $row["title"] = "<b>".$row["title"]."</b>";
           $row["phpurl"] = $GLOBALS["cfg_plus_dir"];
 		       $row["templeturl"] = $GLOBALS["cfg_templets_dir"];
           //---------------------------
           if(is_array($this->dtp2->CTags)){
       	     foreach($this->dtp2->CTags as $k=>$ctag){
       		 	   if(isset($row[$ctag->GetName()])) $this->dtp2->Assign($k,$row[$ctag->GetName()]);
       		 	   else $this->dtp2->Assign($k,"");
       	    }
           }
           $artlist .= $this->dtp2->GetResult()."\r\n";
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
     if($col>1) $artlist .= "	</table>\r\n";
     $this->dsql->FreeResult("al");
     return $artlist;
  }
  //----------------------------------
 	//GetChannelList($typeid=0,$innertext="") 获得一个包含下级类目文档列表信息列表
  //---------------------------------
  function GetChannelList($typeid=0,$col=2,$tablewidth=100,$innertext="")
  {
    if($typeid=="") $typeid=0;
    if($col=="") $col=2;
    
    $tablewidth = str_replace("%","",$tablewidth);
		if($tablewidth=="") $tablewidth=100;
		if($col=="") $col = 1;
		$colWidth = ceil(100/$col); 
		$tablewidth = $tablewidth."%";
		$colWidth = $colWidth."%";
		if($innertext=="") $innertext = GetSysTemplets("part_channelartlist.htm");
    //获得类别ID总数的信息
    //----------------------------
    $typeids = "";
    if($typeid==0){
    	$this->dsql->SetQuery("Select ID from #@__arctype where reID='0' And ispart<>2 order by sortrank asc");
    	$this->dsql->Execute();
    	while($row = $this->dsql->GetObject()){ $typeids[] = $row->ID; }
    }
    else if(!ereg(",",$typeid)){
    	$this->dsql->SetQuery("Select ID from #@__arctype where reID='".$typeid."' And ispart<>2 order by sortrank asc");
    	$this->dsql->Execute();
    	while($row = $this->dsql->GetObject()){ $typeids[] = $row->ID; }
    }
    else if(ereg(",",$typeid)){
    	$ids = explode(",",$typeid); 
    	foreach($ids as $id){
    		$id = trim($id);
    		if($id!="") $typeids[] = $id;
    	}
    }

    if(!is_array($typeids)) return "";
    if(count($typeids)<1) return "";
    
    $row = count($typeids);
    
    $artlist = "";
    $dtp = new DedeTagParse();
 		$dtp->LoadSource($innertext);
    if($col>1){ $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n"; }

    for($i=0;$i<$row;)
		{
       if($col>1) $artlist .= "<tr>\r\n";
       for($j=0;$j<$col;$j++)
			 {
         if($col>1) $artlist .= "	<td width='$colWidth' valign='top'>\r\n";
         if(isset($typeids[$i]))
         {
           foreach($dtp->CTags as $tid=>$ctag)
           {
         	   if($ctag->GetName()=="type"){
         	 	   $dtp->Assign($tid,$this->GetOneType($typeids[$i],$ctag->GetInnerText()));
         	   }
         	   else if($ctag->GetName()=="arclist"){
         	   	 $dtp->Assign($tid,$this->GetArcList($typeids[$i],
         	   	 $ctag->GetAtt('row'),
         	   	 $ctag->GetAtt('col'),
         	   	 $ctag->GetAtt('titlelen'),
         	   	 $ctag->GetAtt('infolen'),
               $ctag->GetAtt('imgwidth'),
               $ctag->GetAtt('imgheight'),
               $ctag->GetAtt('type'),
               $ctag->GetAtt('orderby'),
               "",$ctag->GetInnerText()));
         	   }
           }
           $artlist .= $dtp->GetResult();
         }
         if($col>1) $artlist .= "	</td>\r\n";
         $i++;
       }//Loop Col
       if($col>1){ $artlist .= "	</tr>\r\n";}
     }//Loop Line
     if($col>1) $artlist .= "	</table>\r\n";
     return $artlist;
  }
  //---------------------------
  //获取自定义标记的值
  //---------------------------
  function GetMyTag($typeid=0,$tagname="",$ismake="no")
  {
  	if($tagname=="") return "";
  	if($typeid=="") $typeid=0;
  	if($ismake=="") $ismake = "no";
  	if($this->TypeID > 0 && $typeid==0) $typeid = $this->TypeID;
  	$oldtypeid = $typeid;
  	$row = "";
  	if($typeid > 0)
  	{
  		$row = $this->dsql->GetOne("Select * From #@__mytag where typeid='$typeid' And tagname like '$tagname' order by starttime Desc ");
  		if(!is_array($row))
  		{
  		  //获取与本类目最相近的父类目
  		  $pids = GetParentIDS($typeid,$this->dsql);
  		  $pids[count($pids)] = 0;
  		  $pcount = count($pids);
  		  $idsql = " typeid='0' ";
  		  foreach($pids as $v){ $idsql .= " Or typeid='$v' "; }
  		  if($idsql!="") $idsql = " And ( $idsql ) ";
  		
  		  $this->dsql->SetQuery(" Select typeid From #@__mytag where tagname like '$tagname' $idsql ");
  		  $this->dsql->ExecuteNoneQuery();
  		  $latest = -1;
  		  while($row = $this->dsql->GetObject()){
  			  $typeid = $row->typeid;
  			  for($i=0;$i<$pcount;$i++){
  				  if($pids[$i]==$typeid){
  					  if($latest==-1){ $latest = $i; }
  					  else{ 
  					  	if($latest>$i) $latest=$i;
  					  	else continue;
  					  }
  				  }
  			  }
  		  }
  		  if($latest!=-1) $typeid = $pids[$latest];
  		  else $typeid = 0;
  	  }
  	}
  	else{
  		$typeid=0;
  	}
  	//获取内容
  	if(!is_array($row)){
  	  $row = $this->dsql->GetOne("Select * From #@__mytag where typeid='$typeid' And tagname like '$tagname' order by starttime Desc ");
    }
  	//---------------------------------
  	if(!is_array($row)){
  		return "";
  	}else{
  		$nowtime = time();
  		if($row['timeset']==1 
  		&& ($nowtime<$row['starttime'] || $nowtime>$row['endtime']) )
  		{ $body = $row['expbody']; }
  		else
  		{ $body = $row['normbody']; }
  	}
  	//编译
  	if($ismake=="yes")
  	{
  		$this->pvCopy = new PartView($oldtypeid);
  		$this->pvCopy->SetTemplet($body,"string");
  		$body = $this->pvCopy->GetResult();
  	}
  	return $body;
  }
  //--------------------------
  //获取站内新闻消息
  //--------------------------
  function GetMyNews($row=1,$titlelen=30,$innertext="")
  {
  	if($row=="") $row=1;
  	if($titlelen=="") $titlelen=30;
  	if($innertext=="") $innertext = GetSysTemplets("mynews.htm");
  	if($this->TypeID > 0){
  		$topid = $this->GetTopID($this->TypeID);
  		$idsql = " where typeid='$topid' ";
  	}
  	else $idsql = "";
  	$this->dsql->SetQuery("Select * from #@__mynews $idsql order by senddate desc limit 0,$row");
  	$this->dsql->Execute();
  	$ctp = new DedeTagParse();
		$ctp->SetNameSpace("field","[","]");
		$ctp->LoadSource($innertext);
		$revalue = "";
		while($row = $this->dsql->GetArray())
		{
		  foreach($ctp->CTags as $tagid=>$ctag)
		  {
		    if(!empty($row[$ctag->GetName()]))
		    { $ctp->Assign($tagid,$row[$ctag->GetName()]); }
		  }
		  $revalue .= $ctp->GetResult();
		}
		return $revalue;
  }
  //----------------------
  //获取一个类目的顶级类目ID
  //----------------------
  function GetTopID($tid)
  {
  	$row = $this->dsql->GetOne("Select ID,reID From #@__arctype where ID='$tid'");
  	if($row['reID']==0) return $ID;
  	else $this->GetTopID($row['reID']);
  }
  //------------------------------
  //获得一个类目的链接信息
  //------------------------------
  function GetOneType($typeid,$innertext="")
  {
  	$this->dsql->SetQuery("Select ID,typedir,isdefault,defaultname,ispart,namerule2,typename From #@__arctype where ID='$typeid'");
  	$row = $this->dsql->GetOne();
  	if(!is_array($row)) return "";
  	if($innertext=="") $innertext = GetSysTemplets("part_type_list.htm");
  	$dtp = new DedeTagParse();
 		$dtp->SetNameSpace("field","[","]");
 		$dtp->LoadSource($innertext);
 		if(!is_array($dtp->CTags)){ $dtp->Clear(); return ""; }
 		else{
 			$row['typelink'] = GetTypeUrl($row['ID'],$row['typedir'],$row['isdefault'],$row['defaultname'],$row['ispart'],$row['namerule2']);
 			foreach($dtp->CTags as $tagid=>$ctag){
 				if(isset($row[$ctag->GetName()])){ $dtp->Assign($tagid,$row[$ctag->GetName()]); }
 			}
 			$revalue = $dtp->GetResult();
 			$dtp->Clear();
 			return $revalue;
 		}
  }
  //----------------------------------------
	//获得任意表的内容
	//----------------------------------------
	function GetTable($tablename="",$row=6,$sort="",$ifcase="",$InnerText="")
	{
		$InnerText = trim($InnerText);
		if($tablename=="") return "";
		if($InnerText=="") return "";
		if($row=="") $row=6;
		if($sort!="") $sort = " order by $sort desc ";
		if($ifcase!="") $ifcase=" where $ifcase ";
		$revalue="";
		$this->dsql->SetQuery("Select * From $tablename $ifcase $sort limit 0,$row");
		$this->dsql->Execute();
		$ctp = new DedeTagParse();
		$ctp->SetNameSpace("field","[","]");
		$ctp->LoadSource($InnerText);
		$oldSource = $ctp->SourceString;
    $oldCtags = $ctp->CTags;
		while($row = $this->dsql->GetArray())
		{
		  $ctp->SourceString = $oldSource;
      $ctp->CTags = $oldCtags;
		  foreach($ctp->CTags as $tagid=>$ctag)
		  {
		    if(!empty($row[$ctag->GetName()]))
		    { $ctp->Assign($tagid,$row[$ctag->GetName()]); }
		  }
		  $revalue .= $ctp->GetResult();
		}
		return $revalue;
	}
	//--------------------------
	//获得一组投票
	//-------------------------
	function GetVote($id=0,$lineheight=24,$tablewidth="100%",$titlebgcolor="#EDEDE2",$titlebackgroup="",$tablebg="#FFFFFF")
	{
		if($id=="") $id=0;
		if($id==0){
			$row = $this->dsql->GetOne("select aid From #@__vote order by aid desc limit 0,1");
			if(!isset($row['aid'])) return "";
			else $id=$row['aid'];
		}
		require_once(dirname(__FILE__)."/inc_vote.php");
		$vt = new DedeVote($id);
		$vt->Close();
		return $vt->GetVoteForm($lineheight,$tablewidth,$titlebgcolor,$titlebackgroup,$tablebg);
	}
	//------------------------
	//获取友情链接列表
	//------------------------
	function GetFriendLink($type="",$row="",$col="",$titlelen="",$tablestyle="")
	{
		if($type=="") $type="textall";
		if($row=="") $row=4;
		if($col==""||$col==0) $col=6;
		if($titlelen=="") $titlelen=24;
		if($tablestyle=="") $tablestyle=" width='100%' border='0' cellspacing='1' cellpadding='1' ";
		$tdwidth = ceil(100/$col)."%";
		$totalrow = $row*$col;
		
		$wsql = " where ischeck=1 ";
		if($type=="image") $wsql .= " And logo<>'' ";
		else if($type=="text") $wsql .= " And logo='' ";
		else $wsql .= "";
		
		$this->dsql->SetQuery("Select * from #@__flink $wsql order by sortrank asc limit 0,$totalrow");
		$this->dsql->Execute();
		$revalue = "<table $tablestyle>";
		for($i=1;$i<=$row;$i++)
		{
			$revalue.="<tr bgcolor='#FFFFFF' height='20'>\r\n";
			for($j=1;$j<=$col;$j++)
			{
				if($dbrow=$this->dsql->GetObject())
				{
					if($type=="text"||$type=="textall")
					{
					   $link = "&nbsp;<a href='".$dbrow->url."' target='_blank'>".cn_substr($dbrow->webname,$titlelen)."</a>";
					}
					else if($type=="image")
					{
					   $link = "&nbsp;<a href='".$dbrow->url."' target='_blank'><img src='".$dbrow->logo."' width='88' height='31' border='0'></a>";
					}
					else
					{
						if($dbrow->logo=="")
						   $link = "&nbsp;<a href='".$dbrow->url."' target='_blank'>".cn_substr($dbrow->webname,$titlelen)."</a>";
						else
						   $link = "&nbsp;<a href='".$dbrow->url."' target='_blank'><img src='".$dbrow->logo."' width='88' height='31' border='0'></a>";
					}
					$revalue.="<td width='$tdwidth'>$link</td>\r\n";
				}
				else{
					$revalue.="<td></td>\r\n";
				}
			}
			$revalue.="</tr>\r\n";
		}
		$revalue .= "</table>";
		return $revalue;
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
 	//---------------------------
 	//关闭所占用的资源
 	//---------------------------
 	function Close()
 	{
 		if(is_object($this->dsql)) $this->dsql->Close();
 		if(is_object($this->TypeLink)) $this->TypeLink->Close();
 		if(is_object($this->pvCopy)) $this->pvCopy->Close();
 	}
}//End Class
$pTypeArrays = "";
function GetParentIDS($tid,$dsql)
{
	$GLOBALS['pTypeArrays'][] = $tid;
	$row = $dsql->GetOne("Select ID,reID From #@__arctype where ID='$tid'");
	if($row['reID']==0) return $GLOBALS['pTypeArrays'];
	else GetParentIDS($row['reID'],$dsql);
}
?>