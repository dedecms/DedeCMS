<?php
require_once(dirname(__FILE__)."/inc_channel_unit.php");
require_once(dirname(__FILE__)."/inc_typelink.php");
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
	var $TempletsFile;
	var $OldCacheTime;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($typeid=0)
 	{
 		global $PubFields;
 		$this->TypeID = $typeid;
 		$this->dsql = new DedeSql(false);
 		$this->dtp = new DedeTagParse();
 		$this->dtp->SetNameSpace("dede","{","}");
 		$this->dtp2 = new DedeTagParse();
 		$this->dtp2->SetNameSpace("field","[","]");
 		$this->TypeLink = new TypeLink($typeid);
 		$this->TempletsFile = '';
 		$this->OldCacheTime = -100;
 		if(is_array($this->TypeLink->TypeInfos))
 		{
 			foreach($this->TypeLink->TypeInfos as $k=>$v)
 			{
 				if(ereg("[^0-9]",$k)) $this->Fields[$k] = $v;
 			}
 		}
 		//设置一些全局参数的值
 		foreach($PubFields as $k=>$v) $this->Fields[$k] = $v;

  }
  //php4构造函数
 	//---------------------------
 	function PartView($typeid=0){
 		$this->__construct($typeid);
 	}
 	//设置要解析的模板
 	//------------------------
 	function SetTemplet($temp,$stype="file")
 	{
 		$this->OldCacheTime = $GLOBALS['cfg_al_cachetime'];
 		$GLOBALS['cfg_al_cachetime'] = 0;
 		if($stype=="string") $this->dtp->LoadSource($temp);
 		else{
 			$this->dtp->LoadTemplet($temp);
 			$this->TempletsFile = ereg_replace("^".$GLOBALS['cfg_basedir'],'',$temp);
 		}
 		if($this->TypeID > 0){
 			$this->Fields['position'] = $this->TypeLink->GetPositionLink(true);
 			$this->Fields['title'] = $this->TypeLink->GetPositionLink(false);
 		}
 		$this->ParseTemplet();
 	}
 	//显示内容
 	//-----------------------
 	function Display(){
 		$this->dtp->Display();
 		if($this->OldCacheTime!=-100) $GLOBALS['cfg_al_cachetime'] = $this->OldCacheTime;
 	}
 	//获取内容
 	//-----------------------
 	function GetResult(){
 		$rs = $this->dtp->GetResult();
 		if($this->OldCacheTime!=-100) $GLOBALS['cfg_al_cachetime'] = $this->OldCacheTime;
 		return $rs;
 	}
 	//保存结果为文件
 	//------------------------
 	function SaveToHtml($filename){
 		$this->dtp->SaveTo($filename);
 		if($this->OldCacheTime!=-100) $GLOBALS['cfg_al_cachetime'] = $this->OldCacheTime;
 	}
 	//解析的模板
 	/*
 	function __ParseTemplet();
 	*/
 	//------------------------
 	function ParseTemplet()
 	{
 		//global $envTypeid;
    //if(!isset($envTypeid)) $envTypeid = 0;
 		if(!is_array($this->dtp->CTags)) return "";
 		foreach($this->dtp->CTags as $tagid=>$ctag)
 		{
 			$tagname = $ctag->GetName();
 			if($tagname=="field"){
 				//获得 field 标记值
 				@$this->dtp->Assign($tagid,$this->Fields[$ctag->GetAtt('name')]);
 			}else if($tagname=="onetype"||$tagname=="type"){
 				//获得单个栏目属性
 				$this->dtp->Assign($tagid,$this->GetOneType($ctag->GetAtt('typeid'),$ctag->GetInnerText()));
 			}else if($tagname=="autochannel"){
 				//获得自动栏目内容
 				$this->dtp->Assign($tagid,
 				     $this->GetAutoChannel($ctag->GetAtt('partsort'),$ctag->GetInnerText(),$ctag->GetAtt('typeid'))
 				);
 			}else if($tagname=="arclist"||$tagname=="artlist"||$tagname=="likeart"||$tagname=="hotart"||
 			$tagname=="imglist"||$tagname=="imginfolist"||$tagname=="coolart"||$tagname=="specart"||$tagname=="autolist")
 			{  //特定的文章列表
 				  $autopartid = 0;
 				  $channelid = $ctag->GetAtt("channelid");
 				  if($tagname=="imglist"||$tagname=="imginfolist"){ $listtype = "image"; }
 				  else if($tagname=="specart"){ $channelid = -1; $listtype=""; }
 				  else if($tagname=="coolart"){ $listtype = "commend"; }
 				  else if($tagname=="autolist"){ $autopartid = $ctag->GetAtt('partsort'); }
 				  else{ $listtype = $ctag->GetAtt('type'); }

 				  //排序
 				  if($ctag->GetAtt('sort')!="") $orderby = $ctag->GetAtt('sort');
 				  else if($tagname=="hotart") $orderby = "click";
 				  else $orderby = $ctag->GetAtt('orderby');

 				  //对相应的标记使用不同的默认innertext
 				  if(trim($ctag->GetInnerText())!="") $innertext = $ctag->GetInnerText();
 				  else if($tagname=="imglist") $innertext = GetSysTemplets("part_imglist.htm");
 				  else if($tagname=="imginfolist") $innertext = GetSysTemplets("part_imginfolist.htm");
 				  else $innertext = GetSysTemplets("part_arclist.htm");

 				  $typeid = trim($ctag->GetAtt("typeid"));
 				  if(empty($typeid)) $typeid = $this->TypeID;
					if(!isset($titlelen)) $titlelen = '';
					if(!isset($infolen)) $infolen = '';

 				  $this->dtp->Assign($tagid,
 				      $this->GetArcList(
 				        $this->TempletsFile,
 				        $typeid,$ctag->GetAtt("row"),$ctag->GetAtt("col"),
 				        $ctag->GetAtt("titlelen"),$ctag->GetAtt("infolen"),$ctag->GetAtt("imgwidth"),$ctag->GetAtt("imgheight"),
 				        $ctag->GetAtt("type"),$orderby,$ctag->GetAtt("keyword"),$innertext,
                $ctag->GetAtt("tablewidth"),0,"",$channelid,$ctag->GetAtt("limit"),$ctag->GetAtt("att"),
                $ctag->GetAtt("orderway"),$ctag->GetAtt("subday"),$autopartid,$ctag->GetAtt("ismember")
             )
 				  );
 			}else if($tagname=="channelartlist"){
 				 //获得频道的下级栏目列表及文档列表
 				 $this->dtp->Assign($tagid,
 				     $this->GetChannelList(trim($ctag->GetAtt('typeid')),$ctag->GetAtt('col'),$ctag->GetAtt('tablewidth'),$ctag->GetInnerText())
 				 );
 			}else if($tagname=="hotwords"){
 				 //热门关键字
 				 $this->dtp->Assign($tagid,
 				 GetHotKeywords($this->dsql,$ctag->GetAtt('num'),$ctag->GetAtt('subday'),$ctag->GetAtt('maxlength'),$ctag->GetAtt('orderby')));
 			}
 			else if($tagname=="channel"){
 				//获得栏目连接列表
 				$typeid = trim($ctag->GetAtt('typeid'));
 				if( empty($typeid) ){
 					$typeid = $this->TypeID;
 					$reid = $this->TypeLink->TypeInfos['reID'];
 				}else{
 					$reid=0;
 				}
 				$this->dtp->Assign($tagid,
 				   $this->TypeLink->GetChannelList($typeid,$reid,$ctag->GetAtt("row"),
 				   $ctag->GetAtt("type"),$ctag->GetInnerText(),
 				   $ctag->GetAtt("col"),$ctag->GetAtt("tablewidth"),
 				   $ctag->GetAtt("currentstyle"))
 				);
 			}else if($tagname=="mytag"){
 				//自定义标记
 				$this->dtp->Assign($tagid,
 				   $this->GetMyTag($ctag->GetAtt("typeid"),$ctag->GetAtt("name"),$ctag->GetAtt("ismake"))
 				);
 			}else if($tagname=="myad"){
 				//广告代码
 				$this->dtp->Assign($tagid,
 				  $this->GetMyAd($ctag->GetAtt("typeid"),$ctag->GetAtt("name"))
 				);
 			}else if($tagname=="vote"){
 				//投票
 				$this->dtp->Assign($tagid,
				   $this->GetVote(
				     $ctag->GetAtt("id"),$ctag->GetAtt("lineheight"),
             $ctag->GetAtt("tablewidth"),$ctag->GetAtt("titlebgcolor"),
             $ctag->GetAtt("titlebackground"),$ctag->GetAtt("tablebgcolor")
           )
			   );
 			}else if($tagname=="friendlink"||$tagname=="flink"){
 				//友情链接
 				$this->dtp->Assign($tagid,
 				  $this->GetFriendLink($ctag->GetAtt("type"),
 				    $ctag->GetAtt("row"),$ctag->GetAtt("col"),
 				    $ctag->GetAtt("titlelen"),$ctag->GetAtt("tablestyle"),
 				    $ctag->GetAtt("linktype"),
 				    $ctag->GetInnerText()
 				  )
 				);
 			}else if($tagname=="mynews"){
 				//站内新闻
 				$this->dtp->Assign($tagid,
 				  $this->GetMyNews($ctag->GetAtt("row"),$ctag->GetAtt("titlelen"),$ctag->GetInnerText())
 				);
			}else if($tagname=="toparea"){
 				$this->dtp->Assign($tagid,
 				  $this->GetTopArea($ctag->GetInnerText())
 				);
 			}else if($tagname=="loop"){
 				//数据表操作
 				$this->dtp->Assign($tagid,
				  $this->GetTable($ctag->GetAtt("table"),
					  $ctag->GetAtt("row"),$ctag->GetAtt("sort"),
					  $ctag->GetAtt("if"),$ctag->GetInnerText()
					)
			  );
 			}else if($tagname=="sql"){
 				//数据表操作
 				$this->dtp->Assign($tagid,
				    $this->GetSql($ctag->GetAtt("sql"),$ctag->GetInnerText())
			  );
 			}else if($tagname=="tag"){
 				//自定义宏标签
 				$this->dtp->Assign($tagid,
				    $this->GetTags($ctag->GetAtt("row"),$ctag->GetAtt("sort"),$ctag->GetInnerText())
			  );
 			}
 			else if($tagname=="groupthread")
 			{
 				 //圈子主题
 				 $this->dtp->Assign($tagid,
				    $this->GetThreads($ctag->GetAtt("gid"),$ctag->GetAtt("row"),
				            $ctag->GetAtt("orderby"),$ctag->GetAtt("orderway"),$ctag->GetInnerText())
			   );
 		  }
 		  else if($tagname=="group")
 		  {
 				 //圈子
 				 $this->dtp->Assign($tagid,
				    $this->GetGroups($ctag->GetAtt("row"),$ctag->GetAtt("orderby"),$ctag->GetInnerText())
			   );
 		 }
 		 else if($tagname=="ask")
 		 {
 				 //问答
 				 $this->dtp->Assign($tagid,
				    $this->GetAsk($ctag->GetAtt("row"),$ctag->GetAtt("qtype"),$ctag->GetInnerText()),$ctag->GetAtt("typeid")
			   );
 		 }
 		 //特定条件的文档调用
 		 else if($tagname=="arcfulllist"||$tagname=="fulllist"||$tagname=="likeart"||$tagname=="specart")
 		 {
 				  $channelid = $ctag->GetAtt("channelid");
 				  if($tagname=="specart"){ $channelid = -1; }

 				  $typeid = trim($ctag->GetAtt("typeid"));
 				  if(empty($typeid)) $typeid = $this->TypeID;

 				  $this->dtp->Assign($tagid,
 				      $this->GetFullList(
 				         $typeid,$channelid,$ctag->GetAtt("row"),$ctag->GetAtt("titlelen"),$ctag->GetAtt("infolen"),
                 $ctag->GetAtt("keyword"),$ctag->GetInnerText(),$ctag->GetAtt("idlist"),$ctag->GetAtt("limitv"),$ctag->GetAtt("ismember"),
                 $ctag->GetAtt("orderby"),$ctag->GetAtt("imgwidth"),$ctag->GetAtt("imgheight")
 				      )
 				  );
 			}
 		}//End Foreach
 	}
 	//------------------------------------
 	//获得限定模型或栏目的一个指定文档列表
 	//这个标记由于使用了缓存，并且处理数据是支持分表模式的，因此速度更快，但不能进行整站的数据调用
  //---------------------------------
  function GetArcList($templets='',$typeid=0,$row=10,$col=1,$titlelen=30,$infolen=160,
  $imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$keyword="",$innertext="",
  $tablewidth="100",$arcid=0,$idlist="",$channelid=0,$limit="",$att=0,$order='desc',$subday=0,
  $autopartid=-1,$ismember=0)
  {
     if(empty($autopartid)) $autopartid = -1;
     if(empty($typeid)) $typeid=$this->TypeID;
     if($autopartid!=-1){
       	$typeid = $this->GetAutoChannelID($autopartid,$typeid);
       	if($typeid==0) return "";
     }

     if(!isset($GLOBALS['__SpGetArcList'])) require_once(dirname(__FILE__)."/inc/inc_fun_SpGetArcList.php");
     return SpGetArcList($this->dsql,$templets,$typeid,$row,$col,$titlelen,$infolen,$imgwidth,$imgheight,$listtype,
            $orderby,$keyword,$innertext,$tablewidth,$arcid,$idlist,$channelid,$limit,$att,$order,$subday,$ismember);
  }
  //获得整站一个指定的文档列表
  //---------------------------------
  function GetFullList($typeid=0,$channelid=0,$row=10,$titlelen=30,$infolen=160,
  $keyword='',$innertext='',$idlist='',$limitv='',$ismember=0,$orderby='',$imgwidth=120,
  $imgheight=120,$autopartid=-1)
  {
     if(empty($autopartid)) $autopartid = -1;
     if(empty($typeid)) $typeid=$this->TypeID;
     if($autopartid!=-1){
       	$typeid = $this->GetAutoChannelID($autopartid,$typeid);
       	if($typeid==0) return "";
     }
     if(!isset($GLOBALS['__SpGetFullList'])) require_once(dirname(__FILE__)."/inc/inc_fun_SpFullList.php");
     return SpGetFullList($this->dsql,$typeid,$channelid,$row,$titlelen,$infolen,$keyword,$innertext,
                          $idlist,$limitv,$ismember,$orderby,$imgwidth,$imgheight);
  }
  //GetChannelList($typeid=0,$col=2,$tablewidth=100,$innertext="")
  //获得一个包含下级类目文档列表信息列表
  //---------------------------------
  function GetChannelList($typeid=0,$col=2,$tablewidth=100,$innertext="")
  {
  	if($typeid=="") $typeid=0;
  	if($typeid==0 && !empty($this->TypeID)) $typeid = $this->TypeID;
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
    	$this->dsql->SetQuery("Select ID from #@__arctype where reID='0' And ispart<2 And ishidden<>'1' order by sortrank asc");
    	$this->dsql->Execute();
    	while($row = $this->dsql->GetObject()){ $typeids[] = $row->ID; }
    }else{
    	if(!ereg(",",$typeid)){
    	    $this->dsql->SetQuery("Select ID from #@__arctype where reID='".$typeid."' And ispart<2 And ishidden<>'1' order by sortrank asc");
    	    $this->dsql->Execute();
    	    while($row = $this->dsql->GetObject()){ $typeids[] = $row->ID; }
       }else{
    	    $ids = explode(",",$typeid);
    	    foreach($ids as $id){
    		      $id = trim($id); if($id!=""){ $typeids[] = $id; }
    	    }
    	 }
    }
    if(!is_array($typeids)) return "";
    if(count($typeids)<1) return "";
    $nrow = count($typeids);
    $artlist = "";
    $dtp = new DedeTagParse();
 		$dtp->LoadSource($innertext);
    if($col>1){ $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n"; }

    for($i=0;$i < $nrow;)
		{
       if($col>1) $artlist .= "<tr>\r\n";
       for($j=0;$j<$col;$j++)
			 {
         if($col>1) $artlist .= "	<td width='$colWidth' valign='top'>\r\n";
         if(isset($typeids[$i]))
         {
           foreach($dtp->CTags as $tid=>$ctag)
           {

							$sql = "select reid from #@__arctype where ID={$typeids[$i]}";
							$trow = $this->dsql->GetOne($sql);

              $tagname = $ctag->GetName();

         	   if($tagname=="type"){
         	 	   $dtp->Assign($tid,$this->GetOneType($typeids[$i],$ctag->GetInnerText()));
         	   }
         	   else if($tagname=="arclist")
         	   {
         	   	 $dtp->Assign($tid,$this->GetArcList($this->TempletsFile,$typeids[$i],$ctag->GetAtt('row'),
         	   	     $ctag->GetAtt('col'),$ctag->GetAtt('titlelen'),$ctag->GetAtt('infolen'),
                   $ctag->GetAtt('imgwidth'),$ctag->GetAtt('imgheight'),$ctag->GetAtt('type'),
                   $ctag->GetAtt('orderby'),$ctag->GetAtt('keyword'),$ctag->GetInnerText(),
                   $ctag->GetAtt('tablewidth'),$ctag->GetAtt('arcid'),$ctag->GetAtt('idlist'),
                   $ctag->GetAtt('channel'),$ctag->GetAtt('limit'),$ctag->GetAtt('att'),
                   $ctag->GetAtt('order'),$ctag->GetAtt('subday')
                   -1,0,0
                   )
               );
         	}
         	else if($tagname=="channel"){
 				  $dtp->Assign($tid,
 				      $this->TypeLink->GetChannelList(
 				          $typeids[$i],$trow['reid'],$ctag->GetAtt("row"),
 				          $ctag->GetAtt("type"),$ctag->GetInnerText(),
 				          $ctag->GetAtt("col"),$ctag->GetAtt("tablewidth"),
 				          $ctag->GetAtt("currentstyle")
 				      )
 				  );
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
  //获取自定义标记的值
  //---------------------------
  function GetMyTag($typeid=0,$tagname="",$ismake="no")
  {
  	if(trim($ismake)=="") $ismake = "no";
    $body = $this->GetMyTagT($typeid,$tagname,"#@__mytag");
  	//编译
  	if($ismake=="yes"){
  		$this->pvCopy = new PartView($typeid);
  		$this->pvCopy->SetTemplet($body,"string");
  		$body = $this->pvCopy->GetResult();
  	}
  	return $body;
  }
  //获取广告值
  //---------------------------
  function GetMyAd($typeid=0,$tagname=""){
  	return $this->GetMyTagT($typeid,$tagname,"#@__myad");
  }
  function GetMyTagT($typeid,$tagname,$tablename){
  	if($tagname=="") return "";
  	if(trim($typeid)=="") $typeid=0;
  	if($this->TypeID > 0 && $typeid==0) $typeid = $this->TypeID;
  	$row = "";
    $pids = Array();
    if($typeid > 0) $pids = GetParentIDS($typeid,$this->dsql);
  	$idsql = " typeid='0' ";
  	foreach($pids as $v){ $idsql .= " Or typeid='$v' "; }
    $row = $this->dsql->GetOne(" Select * From $tablename where tagname like '$tagname' And ($idsql) order by typeid desc ");
  	if(!is_array($row)){ return ""; }
  	else{
  		$nowtime = time();
  		if($row['timeset']==1 && ($nowtime<$row['starttime'] || $nowtime>$row['endtime']) )
  		{ $body = $row['expbody']; }
  		else{ $body = $row['normbody']; }
  	}
  	return $body;
  }
  	function GetTopArea($innertext)
  	{
  		$str = '<option value="0">-不限-</option>';
  		$this->dsql->SetQuery("select * from #@__area where reid=0 order by disorder asc, id asc");
  		$this->dsql->Execute();
  		if(!$innertext){
  			$innertext = '<option value="[field:id/]">[field:name/]</option>';
  		}
  		 while($row = $this->dsql->getarray())
  		 {
  		 	$str .= str_replace(array('[field:id/]','[field:name/]'),array($row['id'],$row['name']),$innertext);
  		 	//'<option value="'.$row['id'].'">'.$row['name']."</option>\n";

  		}
  		return $str;

	}
  //获取站内新闻消息
  //--------------------------
  function GetMyNews($row=1,$titlelen=30,$innertext=""){
  	if($row=="") $row=1;
  	if($titlelen=="") $titlelen=30;
  	if($innertext=="") $innertext = GetSysTemplets("mynews.htm");
  	if($this->TypeID > 0){
  		$topid = SpGetTopID($this->TypeID);
  		$idsql = " where typeid='$topid' ";
  	}else{
  		$idsql = "";
  	}
  	$this->dsql->SetQuery("Select * from #@__mynews $idsql order by senddate desc limit 0,$row");
  	$this->dsql->Execute();
  	$ctp = new DedeTagParse();
		$ctp->SetNameSpace("field","[","]");
		$ctp->LoadSource($innertext);
		$revalue = "";
		while($row = $this->dsql->GetArray())
		{
		  foreach($ctp->CTags as $tagid=>$ctag){
		    @$ctp->Assign($tagid,$row[$ctag->GetName()]);
		  }
		  $revalue .= $ctp->GetResult();
		}
		return $revalue;
  }
  //获得一个类目的链接信息
  //------------------------------
  function GetOneType($typeid,$innertext=""){
  	$row = $this->dsql->GetOne("Select ID,typedir,isdefault,defaultname,ispart,namerule2,typename,moresite,siterefer,siteurl,sitepath From #@__arctype where ID='$typeid'");
  	if(!is_array($row)) return "";
  	if(trim($innertext)=="") $innertext = GetSysTemplets("part_type_list.htm");
  	$dtp = new DedeTagParse();
 		$dtp->SetNameSpace("field","[","]");
 		$dtp->LoadSource($innertext);
 		if(!is_array($dtp->CTags)){ unset($dtp); return ""; }
 		else{
 			$row['typelink'] = GetTypeUrl($row['ID'],MfTypedir($row['typedir']),$row['isdefault'],
 			                    $row['defaultname'],$row['ispart'],$row['namerule2'],$row['siteurl']);
 			foreach($dtp->CTags as $tagid=>$ctag){
 				if(isset($row[$ctag->GetName()])){ $dtp->Assign($tagid,$row[$ctag->GetName()]); }
 			}
 			$revalue = $dtp->GetResult();
 			unset($dtp);
 			return $revalue;
 		}
  }
  //----------------------------------------
	//获得任意表的内容
	//----------------------------------------
	function GetTable($tablename="",$row=6,$sort="",$ifcase="",$InnerText=""){
		$InnerText = trim($InnerText);
		if($tablename==""||$InnerText=="") return "";
		$row = AttDef($row,6);
		if($sort!="") $sort = " order by $sort desc ";
		if($ifcase!="") $ifcase=" where $ifcase ";
		$revalue="";
		$this->dsql->SetQuery("Select * From $tablename $ifcase $sort limit 0,$row");
		$this->dsql->Execute();
		$ctp = new DedeTagParse();
		$ctp->SetNameSpace("field","[","]");
		$ctp->LoadSource($InnerText);
		while($row = $this->dsql->GetArray())
    {
		  foreach($ctp->CTags as $tagid=>$ctag){
		    if(!empty($row[$ctag->GetName()]))
		    { $ctp->Assign($tagid,$row[$ctag->GetName()]); }
		  }
		  $revalue .= $ctp->GetResult();
		}
		return $revalue;
	}
	//----------------------------------------
	//通过任意SQL查询获得内容
	//----------------------------------------
	function GetSql($sql="",$InnerText=""){
		$InnerText = trim($InnerText);
		if($sql==""||$InnerText=="") return "";
		$revalue = "";
		$this->dsql->SetQuery($sql);
		$this->dsql->Execute();
		$ctp = new DedeTagParse();
		$ctp->SetNameSpace("field","[","]");
		$ctp->LoadSource($InnerText);
		while($row = $this->dsql->GetArray())
    {
		  foreach($ctp->CTags as $tagid=>$ctag){
		    if(isset($row[$ctag->GetName()]))
		    { $ctp->Assign($tagid,$row[$ctag->GetName()]); }
		  }
		  $revalue .= $ctp->GetResult();
		}
		return $revalue;
	}
	//----------------------------------------
	//获得标签
	//----------------------------------------
	function GetTags($num,$ltype='new',$InnerText=""){
		global $cfg_cmspath;
		$InnerText = trim($InnerText);
		if($InnerText=="") $InnerText = GetSysTemplets("tag_one.htm");
		$revalue = "";
		if($ltype=='rand') $orderby = ' rand() ';
		else if($ltype=='week') $orderby=' weekcc desc ';
		else if($ltype=='month') $orderby=' monthcc desc ';
		else if($ltype=='hot') $orderby=' count desc ';
		else $orderby = '  id desc  ';
		if(empty($num)) $num = 10;
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

	/*调用圈子模块相关方法*/
  //最新主题调用 $num:贴子数,$gid:圈子ID,$dsql数据连接,$h:依照什么排序,$orders:排序方法
  function GetThreads($gid=0,$num=0,$orderby="dateline",$orderway="DESC",$innertext='')
  {
	  global $cfg_group_url,$cfg_dbprefix;
    if( !$this->dsql->IsTable("{$cfg_dbprefix}group_threads") ) return '没安装问答模块';
	  $num = AttDef($num,12);
	  $gid = AttDef($gid,0);
	  $orderway = AttDef($orderway,'desc');
	  $orderby = AttDef($orderby,'dateline');
	  if(trim($innertext)=="") $innertext = GetSysTemplets("groupthreads.htm");
	  $WhereSql = " WHERE t.closed=0 ";
	  $orderby = 't.'.$orderby;
	  if($gid > 0) $WhereSql .= " AND t.gid='$gid' ";
	  $this->dsql->SetQuery("SELECT t.subject,t.gid,t.tid,t.lastpost,g.groupname FROM #@__group_threads t left join #@__groups g on g.groupid=t.gid  $WhereSql ORDER BY $orderby $orderway LIMIT 0,{$num}");
    $this->dsql->Execute();
    $ctp = new DedeTagParse();
		$ctp->SetNameSpace("field","[","]");
		if(!isset($list)) $list = '';
    while($rs = $this->dsql->GetArray())
    {
  	  $ctp->LoadSource($innertext);
  	  $rs['url'] = $cfg_group_url."/viewthread.php?id={$rs['gid']}&tid={$rs['tid']}";
  	  $rs['groupurl'] = $cfg_group_url."/group.php?id={$rs['gid']}";
  	  foreach($ctp->CTags as $tagid=>$ctag){
		    if(!empty($rs[strtolower($ctag->GetName())])){ $ctp->Assign($tagid,$rs[$ctag->GetName()]); }
		  }
		  $list .= $ctp->GetResult();
    }
    return $list;
  }

  function GetGroups($nums=0,$orderby='threads',$innertext='')
  {
	  global $cfg_group_url,$cfg_dbprefix;
    if( !$this->dsql->IsTable("{$cfg_dbprefix}groups") ) return '没安装圈子模块';
	  $list = '';
	  $nums = AttDef($nums,6);
	  $orderby = AttDef($orderby,'threads');
	  if(trim($innertext)=='') $innertext = GetSysTemplets("groups.htm");
	  $this->dsql->SetQuery("SELECT groupimg,groupid,groupname FROM #@__groups WHERE ishidden=0 ORDER BY $orderby DESC LIMIT 0,{$nums}");
    $this->dsql->Execute();
    $ctp = new DedeTagParse();
		$ctp->SetNameSpace("field","[","]");
    while($rs = $this->dsql->GetArray())
    {
  	  $ctp->LoadSource($innertext);
  	  $rs['url'] = $cfg_group_url."/group.php?id={$rs['groupid']}";
  	  $rs['icon']  = $rs['groupimg'];
  	  foreach($ctp->CTags as $tagid=>$ctag){
		    if(!empty($rs[strtolower($ctag->GetName())])){ $ctp->Assign($tagid,$rs[$ctag->GetName()]); }
		  }
		  $list .= $ctp->GetResult();
    }
	  return $list;
 }

 //调用问答最新主题
 //--------------------------------
 function GetAsk($nums=8,$qtype='new',$innertext='',$tid=0)
 {
    global $cfg_ask_url,$cfg_dbprefix;

    if( !$this->dsql->IsTable("{$cfg_dbprefix}ask") ) return '没安装问答模块';

    $nums = AttDef($nums,6);
    $qtype = AttDef($qtype,'new');
    $tid = AttDef($tid,0);
    if(trim($innertext)=='') $innertext = GetSysTemplets("asks.htm");
		$qtypeQuery = '';

		if($tid>0) $tid = " (tid=$tid Or $tid2='$tid') And ";
		else $tid = '';
		//推荐问题
		if($qtype=='commend') $qtypeQuery = " $tid digest=1 order by dateline desc ";
		//新解决问题
		else if($qtype=='ok') $qtypeQuery = " $tid status=1 order by solvetime desc ";
		//高分问题
		else if($qtype=='high') $qtypeQuery = " $tid status=0 order by reward desc ";
		//新问题
		else $qtypeQuery = " $tid status=0 order by disorder desc, dateline desc ";

		$ctp = new DedeTagParse();
		$ctp->SetNameSpace("field","[","]");

    $query = "select id, tid, tidname, tid2, tid2name, title from #@__ask where $qtypeQuery  limit $nums";
    $this->dsql->Execute('me',$query);
    $solvingask = '';
    while($rs = $this->dsql->GetArray('me'))
    {
	    $ctp->LoadSource($innertext);
	    if($rs['tid2name']!='')
	    {
	    	$rs['tid'] = $rs['tid2'];
	    	$rs['tidname'] = $rs['tid2name'];
	    }
  	  $rs['url'] = $cfg_ask_url."/question.php?id={$rs['id']}";
      $rs['typeurl'] = $cfg_ask_url."/browser.php?tid={$rs['tid']}";
  	  foreach($ctp->CTags as $tagid=>$ctag){
		    if(!empty($rs[strtolower($ctag->GetName())])){ $ctp->Assign($tagid,$rs[$ctag->GetName()]); }
		  }
		  $solvingask .= $ctp->GetResult();
    }
    return $solvingask;
 }

	//获得一组投票
	//-------------------------
	function GetVote($id=0,$lineheight=24,$tablewidth="100%",$titlebgcolor="#EDEDE2",$titlebackgroup="",$tablebg="#FFFFFF"){
		if($id=="") $id=0;
		if($id==0){
			$row = $this->dsql->GetOne("select aid From #@__vote order by aid desc limit 0,1");
			if(!isset($row['aid'])) return "";
			else $id=$row['aid'];
		}
		require_once(dirname(__FILE__)."/inc_vote.php");
		$vt = new DedeVote($id);
		//$vt->Close();
		return $vt->GetVoteForm($lineheight,$tablewidth,$titlebgcolor,$titlebackgroup,$tablebg);
	}
	//获取友情链接列表
	//------------------------
	function GetFriendLink($type="",$row="",$col="",$titlelen="",$tablestyle="",$linktype=1,$innertext=''){
		$type = AttDef($type,"textall");
		$row = AttDef($row,4);
		$col = AttDef($col,6);
		if($linktype=="") $linktype = 1;
		$titlelen = AttDef($titlelen,24);
		$tablestyle = AttDef($tablestyle," width='100%' border='0' cellspacing='1' cellpadding='1' ");
		$tdwidth = round(100/$col)."%";
		$totalrow = $row*$col;

		if($innertext=='') $innertext = " [field:link/] ";

		$wsql = " where ischeck >= '$linktype' ";
		if($type=="image") $wsql .= " And logo<>'' ";
		else if($type=="text") $wsql .= " And logo='' ";
		else $wsql .= "";

		$equery = "Select * from #@__flink $wsql order by sortrank asc limit 0,$totalrow";

		$this->dsql->SetQuery($equery);
		$this->dsql->Execute();

		$revalue = "";
		while($row = $this->dsql->GetArray())
		{
			if($type=="text"||$type=="textall")
					$row['link'] = "<a href='".$row['url']."' target='_blank'>".cn_substr($row['webname'],$titlelen)."</a>";
			else if($type=="image")
					$row['link'] = "<a href='".$row['url']."' target='_blank'><img alt='".str_replace("'","`",$row['webname'])."' src='".$row['logo']."' border='0'></a>";
			else{
				if($row['logo']=="")
					$row['link'] = "&nbsp;<a href='".$row['url']."' target='_blank'>".cn_substr($row['webname'],$titlelen)."</a>";
				else
					$row['link'] = "&nbsp;<a href='".$row['url']."' target='_blank'><img alt='".str_replace("'","`",$row['webname'])."' src='".$row['logo']."' border='0'></a>";
			}
			$rbtext = preg_replace("/\[field:url([\s]{0,})\/\]/isU",$row['url'],$innertext);
 			$rbtext = preg_replace("/\[field:webname([\s]{0,})\/\]/isU",$row['ID'],$rbtext);
 			$rbtext = preg_replace("/\[field:logo([\s]{0,})\/\]/isU",$row['logo'],$rbtext);
 			$rbtext = preg_replace("/\[field:link([\s]{0,})\/\]/isU",$row['link'],$rbtext);
 			$revalue .= $rbtext;
		}
		return $revalue;
	}
	//按排列顺序获得一个下级分类信息
	function GetAutoChannel($sortid,$innertext,$topid="-1"){
		if($topid=="-1" || $topid=="") $topid = $this->TypeID;
		$typeid = $this->GetAutoChannelID($sortid,$topid);
		if($typeid==0) return "";
		if(trim($innertext)=="") $innertext = GetSysTemplets("part_autochannel.htm");
		return $this->GetOneType($typeid,$innertext);
  }
  function GetAutoChannelID($sortid,$topid){
		if(empty($sortid)) $sortid = 1;
		$getstart = $sortid - 1;
		$row = $this->dsql->GetOne("Select ID,typename From #@__arctype where reid='{$topid}' And ispart<2 And ishidden<>'1' order by sortrank asc limit $getstart,1");
		if(!is_array($row)) return 0;
		else return $row['ID'];
  }
 	//---------------------------
 	//关闭所占用的资源
 	//---------------------------
 	function Close(){
 		$this->dsql->Close();
 		if(is_object($this->TypeLink)) $this->TypeLink->Close();
 	}
}//End Class

?>