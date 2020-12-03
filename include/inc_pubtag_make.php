<?php
/*-----------------------------
本函数用于作为通用的标记解析器
考虑性能原因，大部份使用引用调用，使用时必须注意
------------------------*/
function MakePublicTag(&$thisObj,&$tagObj,&$partObj,&$typeLink,$envTypeid=0,$envArcid=0,$envChannelid=0)
{
	//解析模板
 	//-------------------------
 	if( is_array($tagObj->CTags) )
 	{
 		 foreach($tagObj->CTags as $tagid=>$ctag)
 		 {
 			 $tagname = $ctag->GetName();
 			 //字段
 			 if($tagname=="field"){
 					if(isset($thisObj->Fields[$ctag->GetAtt('name')]))
 					  $tagObj->Assign($tagid,$thisObj->Fields[$ctag->GetAtt('name')]);
 					else
 					  $tagObj->Assign($tagid,"");
 			 }
 			 //单个栏目
 			 else if($tagname=="onetype"||$tagname=="type"){
 				   $typeid = $ctag->GetAtt('typeid');
 				   if($typeid=="") $typeid = 0;
 				   if($typeid=="") $typeid = $envTypeid;
 				   $tagObj->Assign($tagid,$partObj->GetOneType($typeid,$ctag->GetInnerText()));
 			 }
 			 //下级频道列表
 			 else if($tagname=="channel"){
 				  $typeid = trim($ctag->GetAtt('typeid'));
 				  if( empty($typeid) && $envTypeid > 0 ){
 					  $typeid = $envTypeid;
 				  	$reid = $typeLink->TypeInfos['reID'];
 				  }else{
 					  $reid=0;
 				  }
 				  $tagObj->Assign($tagid,
 				      $typeLink->GetChannelList(
 				          $typeid,$reid,$ctag->GetAtt("row"),
 				          $ctag->GetAtt("type"),$ctag->GetInnerText(),
 				          $ctag->GetAtt("col"),$ctag->GetAtt("tablewidth"),
 				          $ctag->GetAtt("currentstyle")
 				      )
 				  );
 			 }
 			 //热门关键字
 			 else if($tagname=="hotwords"){
 				 $tagObj->Assign($tagid,
 				 GetHotKeywords($thisObj->dsql,$ctag->GetAtt('num'),$ctag->GetAtt('subday'),$ctag->GetAtt('maxlength')));
 			 }
 			 //自定义标记
 			 else if($tagname=="mytag"){
 				 $tagObj->Assign($tagid,
 				   $partObj->GetMyTag($envTypeid,$ctag->GetAtt("name"),$ctag->GetAtt("ismake"))
 				 );
 			 }
 			 //广告代码
 			 else if($tagname=="myad"){
 				 $tagObj->Assign($tagid,
 				   $partObj->GetMyAd($envTypeid,$ctag->GetAtt("name"))
 				 );
 			 }
 			 //频道下级栏目文档列表
 			 else if($tagname=="channelartlist"){
 				  //类别ID
 				  if(trim($ctag->GetAtt('typeid'))=="" && $envTypeid!=0){  $typeid = $envTypeid;  }
 				  else{ $typeid = trim( $ctag->GetAtt('typeid') ); }
 				  $tagObj->Assign($tagid,
 				      $partObj->GetChannelList($typeid,$ctag->GetAtt('col'),$ctag->GetAtt('tablewidth'),$ctag->GetInnerText())
 				  );
 			 }
 			 //投票
 			 else if($tagname=="vote"){
 				  $tagObj->Assign($tagid,
				     $partObj->GetVote(
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
 				  $tagObj->Assign($tagid,
 				     $partObj->GetFriendLink(
 				        $ctag->GetAtt("type"),$ctag->GetAtt("row"),$ctag->GetAtt("col"),
 				        $ctag->GetAtt("titlelen"),$ctag->GetAtt("tablestyle"),$ctag->GetAtt("linktyle"),$ctag->GetInnerText()
 				     )
 				  );
 			 }
 			 //站点新闻
 			 //---------------------
 			 else if($tagname=="mynews")
 			 {
 				 $tagObj->Assign($tagid,
 				    $partObj->GetMyNews($ctag->GetAtt("row"),$ctag->GetAtt("titlelen"),$ctag->GetInnerText())
 				 );
 			 }
 			 //调用论坛主题
 			 //----------------
 			 else if($tagname=="loop")
 			 {
 				  $tagObj->Assign($tagid,
				    $partObj->GetTable(
					     $ctag->GetAtt("table"),
					     $ctag->GetAtt("row"),
					     $ctag->GetAtt("sort"),
					     $ctag->GetAtt("if"),
					     $ctag->GetInnerText()
					  )
			    );
 			 }
 			 //数据表操作
 			 else if($tagname=="sql"){
 				  $tagObj->Assign($tagid,
				     $partObj->GetSql($ctag->GetAtt("sql"),$ctag->GetInnerText())
			    );
 			 }
 			 else if($tagname=="tag"){
 			 	if($ctag->GetAtt('type') == 'current'){
					$arcid = $thisObj->ArcID;
	 				 $tagObj->Assign($tagid,
	 				 	GetCurrentTags($thisObj->dsql,$arcid, $ctag->GetInnerText())
				   );
 			 	}else{
	 				 //数据表操作
	 				 $tagObj->Assign($tagid,
					    $partObj->GetTags($ctag->GetAtt("row"),$ctag->GetAtt("sort"),$ctag->GetInnerText())
				   );
				}
 			 }
 			 else if($tagname=="toparea"){
 				 //数据表操作
 				 $tagObj->Assign($tagid,
				    $partObj->gettoparea($ctag->GetInnerText())
			   );
 			 }
 			 //特定条件的文档调用
 			 else if($tagname=="arclist"||$tagname=="artlist"||$tagname=="hotart"
 			 ||$tagname=="imglist"||$tagname=="imginfolist"||$tagname=="coolart")
 			 {

 				  $channelid = $ctag->GetAtt("channelid");
 				  if($tagname=="imglist"||$tagname=="imginfolist"){ $listtype = "image"; }
 				  else if($tagname=="coolart"){ $listtype = "commend"; }
 				  else{ $listtype = $ctag->GetAtt('type'); }

 				  //对相应的标记使用不同的默认innertext
 				  if(trim($ctag->GetInnerText())!="") $innertext = $ctag->GetInnerText();
 				  else if($tagname=="imglist") $innertext = GetSysTemplets("part_imglist.htm");
 				  else if($tagname=="imginfolist") $innertext = GetSysTemplets("part_imginfolist.htm");
 				  else $innertext = GetSysTemplets("part_arclist.htm");

 				  if($tagname=="hotart") $orderby = "click";
 				  else $orderby = $ctag->GetAtt('orderby');

 				  $typeid = trim($ctag->GetAtt("typeid"));
 				  if(empty($typeid)) $typeid = $envTypeid;

 				  if(!empty($thisObj->TempletsFile)) $tmpfile = $thisObj->TempletsFile;
 				  else $tmpfile = '';

 				  if(!empty($thisObj->maintable)) $maintable = $thisObj->TempletsFile;
 				  else $maintable = '';

         if(!isset($titlelen)) $titlelen = 0;
         if(!isset($infolen)) $infolen = 0;

         $idlist = '';
 				  if($tagname=="likeart"){
 				  	if(!empty($thisObj->Fields['likeid'])) $idlist = $thisObj->Fields['likeid'];
 				  }else{
 				  	$idlist = $ctag->GetAtt("idlist");
 				  }
 				  if($idlist!=''){ $typeid = '0'; $channelid = '0';}

 				  $tagObj->Assign($tagid,
 				      $partObj->GetArcList(
 				         $tmpfile,
 				         $typeid,
 				         $ctag->GetAtt("row"),
 				         $ctag->GetAtt("col"),
					       $ctag->GetAtt("titlelen"),
					       $ctag->GetAtt("infolen"),
 				         $ctag->GetAtt("imgwidth"),
					       $ctag->GetAtt("imgheight"),
 				         $listtype,
 				         $orderby,
					       $ctag->GetAtt("keyword"),
 				         $innertext,
					       $ctag->GetAtt("tablewidth"),
 				         0,$idlist,$channelid,
 				         $ctag->GetAtt("limit"),
 				         $ctag->GetAtt("att"),
 				         $ctag->GetAtt("orderway"),
					       $ctag->GetAtt("subday"),
					       -1,
					       $ctag->GetAtt("ismember"),
					       $maintable
 				      )
 				  );
 			}
 			else if($tagname=="groupthread")
 			{
 				 //圈子主题
				  $tagObj->Assign($tagid,
				      $partObj->GetThreads($ctag->GetAtt("gid"),$ctag->GetAtt("row"),
				              $ctag->GetAtt("orderby"),$ctag->GetAtt("orderway"),$ctag->GetInnerText())
			    );
 		  }
 		  else if($tagname=="group")
 		  {
 				 //圈子
 				 $tagObj->Assign($tagid,
				    $partObj->GetGroups($ctag->GetAtt("row"),$ctag->GetAtt("orderby"),$ctag->GetInnerText())
			   );
 		 }
 		 else if($tagname=="ask")
 		 {
 				 //问答
 				 $tagObj->Assign($tagid,
				    $partObj->GetAsk($ctag->GetAtt("row"),$ctag->GetAtt("qtype"),$ctag->GetInnerText()),$ctag->GetAtt("typeid")
			   );
 		 }
 		 else if($tagname=="spnote")
 		 {
 		 	$noteid = $ctag->GetAtt('noteid');
 				 //专题节点
 				 $tagObj->Assign($tagid,
				    getNote($noteid, $thisObj)
			   );
 		 }
 		 //特定条件的文档调用
 		 else if($tagname=="arcfulllist"||$tagname=="fulllist"||$tagname=="likeart"||$tagname=="specart")
 		 {
 				  $channelid = $ctag->GetAtt("channelid");
 				  if($tagname=="specart"){ $channelid = -1; }

 				  $typeid = trim($ctag->GetAtt("typeid"));
 				  if(empty($typeid)) $typeid = $envTypeid;

 				  $idlist = '';
 				  if($tagname=="likeart"){
 				  	if(!empty($thisObj->Fields['likeid'])) $idlist = $thisObj->Fields['likeid'];
 				  }else{
 				  	$idlist = $ctag->GetAtt("idlist");
 				  }
 				  if($idlist!=''){ $typeid = '0'; $channelid = '0';}

 				  $tagObj->Assign($tagid,
 				      $partObj->GetFullList(
 				         $typeid,$channelid,$ctag->GetAtt("row"),$ctag->GetAtt("titlelen"),$ctag->GetAtt("infolen"),
                 $ctag->GetAtt("keyword"),$ctag->GetInnerText(),$idlist,$ctag->GetAtt("limitv"),$ctag->GetAtt("ismember"),
                 $ctag->GetAtt("orderby"),$ctag->GetAtt("imgwidth"),$ctag->GetAtt("imgheight")
 				      )
 				  );
 			}
 		}//结束模板循环
 	}
}

//
function getNote($noteid, &$thisObj)
{
	$addtable = $thisObj->ChannelUnit->ChannelInfos['addtable'];
	global $dsql;
	$row = $dsql->getone("select note from {$addtable} where aid={$thisObj->ArcID}");
	$rs = $thisObj->ChannelUnit->MakeField('note',$row['note'], $noteid);
	return $rs;
}

//获得当前页面的tag
function GetCurrentTags(&$dsql,$aid, $innerText='')
{
	global $cfg_cmspath;
	$tags = '';
	$innerText = trim($innerText);
	if($innerText == '') $innerText = GetSysTemplets('tag_current.htm');
	$ctp = new DedeTagParse();
	$ctp->SetNameSpace("field","[","]");
	$ctp->LoadSource($innerText);
	$dsql->Execute('t',"Select i.tagname From #@__tag_list t left join #@__tag_index i on i.id=t.tid where t.aid='$aid' ");
	while($row = $dsql->GetArray('t',MYSQL_ASSOC)){
		$row['link'] = $cfg_cmspath."/tag.php?/".urlencode($row['tagname'])."/";
		  foreach($ctp->CTags as $tagid=>$ctag){
		    if(isset($row[$ctag->GetName()])) $ctp->Assign($tagid,$row[$ctag->GetName()]);
		  }
		  $tags .= $ctp->GetResult();
	}
	return $tags;
}

?>