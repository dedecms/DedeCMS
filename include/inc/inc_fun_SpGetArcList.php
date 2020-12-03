<?php 
$GLOBALS['__SpGetArcList'] = 1;
//获取一个文档列表
//--------------------------------
function SpGetArcList($dsql,$typeid=0,$row=10,$col=1,$titlelen=30,$infolen=160,
  $imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$keyword="",$innertext="",
  $tablewidth="100",$arcid=0,$idlist="",$channelid=0,$limit="",$att=0,$order="desc",$subday=0,$ismember=0)
  {
		global $PubFields,$cfg_keyword_like;
		$row = AttDef($row,10);
		$titlelen = AttDef($titlelen,30);
		$infolen = AttDef($infolen,160);
		$imgwidth = AttDef($imgwidth,120);
		$imgheight = AttDef($imgheight,120);
		$listtype = AttDef($listtype,"all");
    $arcid = AttDef($arcid,0);
    $channelid = AttDef($channelid,0);
    $ismember = AttDef($ismember,0);
    $orderby = AttDef($orderby,"default");
    $orderWay = AttDef($order,"desc");
    $subday = AttDef($subday,0);
    $line = $row;
		$orderby=strtolower($orderby);
		$tablewidth = str_replace("%","",$tablewidth);
		if($tablewidth=="") $tablewidth=100;
		if($col=="") $col = 1;
		$colWidth = ceil(100/$col); 
		$tablewidth = $tablewidth."%";
		$colWidth = $colWidth."%";
		$keyword = trim($keyword);
		$innertext = trim($innertext);
		if($innertext=="") $innertext = GetSysTemplets("part_arclist.htm");
		//按不同情况设定SQL条件 排序方式
		$orwhere = " arc.arcrank > -1 ";
		//时间限制(用于调用最近热门文章、热门评论之类)
		if($subday>0){
			 $limitday = mytime() - ($subday * 24 * 3600);
			 $orwhere .= " And arc.senddate > $limitday ";
		}
		//文档的自定义属性
		if($att!="") $orwhere .= "And arcatt='$att' ";
		//文档的频道模型
		if($channelid>0 && !eregi("spec",$listtype)) $orwhere .= " And arc.channel = '$channelid' ";
		//是否为推荐文档
		if(eregi("commend",$listtype)) $orwhere .= " And arc.iscommend > 10  ";
		//是否为带缩略图图片文档
		if(eregi("image",$listtype)) $orwhere .= " And arc.litpic <> ''  ";
		//是否为专题文档
		if(eregi("spec",$listtype) || $channelid==-1) $orwhere .= " And arc.channel = -1  ";
    //是否指定相近ID
		if($arcid!=0) $orwhere .= " And arc.ID<>'$arcid' ";
		
		//是否为会员文档
		if($ismember==1) $orwhere .= " And arc.memberid>0  ";
		
		//文档排序的方式
		$ordersql = "";
		if($orderby=='hot'||$orderby=='click') $ordersql = " order by arc.click $orderWay";
		else if($orderby=='pubdate') $ordersql = " order by arc.pubdate $orderWay";
		else if($orderby=='sortrank') $ordersql = " order by arc.sortrank $orderWay";
    else if($orderby=='id') $ordersql = "  order by arc.ID $orderWay";
    else if($orderby=='near') $ordersql = " order by ABS(arc.ID - ".$arcid.")";
    else if($orderby=='lastpost') $ordersql = "  order by arc.lastpost $orderWay";
    else if($orderby=='postnum') $ordersql = "  order by arc.postnum $orderWay";
    else if($orderby=='rand') $ordersql = "  order by rand()";
		else $ordersql=" order by arc.senddate $orderWay";
		
		if($orderby=='near' && $cfg_keyword_like=='否'){ $keyword=""; }
		
		//类别ID的条件，如果用 "," 分开,可以指定特定类目
		//------------------------------
		if(!empty($typeid))
		{
		  $reids = explode(",",$typeid);
		  $ridnum = count($reids);
		  if($ridnum>1){
			  $tpsql = "";
		    for($i=0;$i<$ridnum;$i++){
				  if($tpsql=="") $tpsql .= " And ( (".TypeGetSunID($reids[$i],$dsql,'arc')." Or arc.typeid2='".$reids[$i]."') ";
				  else $tpsql .= " Or (".TypeGetSunID($reids[$i],$dsql,'arc')." Or arc.typeid2='".$reids[$i]."') ";
		    }
		    $tpsql .= ") ";
		    $orwhere .= $tpsql;
		    unset($tpsql);
		  }else{
			  $orwhere .= " And (".TypeGetSunID($typeid,$dsql,'arc')." Or arc.typeid2='$typeid' ) ";
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
				if($idlistSql=="") $idlistSql .= " And ( arc.ID='".$reids[$i]."' ";
				else $idlistSql .= " Or arc.ID='".$reids[$i]."' ";
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
		  $rstr = trim($keywords[0]);
		  if($ridnum>4) $ridnum = 4;
		  for($i=0;$i<$ridnum;$i++){
			  $keywords[$i] = trim($keywords[$i]);
			  if($keywords[$i]!="") $rstr .= "|".$keywords[$i];
			}
		  if($rstr!="") $orwhere .= " And CONCAT(arc.title,arc.keywords) REGEXP '$rstr' ";
		  unset($keywords);
	  }
	  $addField = "";
		$addJoin = "";
	  //获得附加表的相关信息
		//-----------------------------
		if($channelid>0){
		  $mChannelUnit = new ChannelUnit($channelid);
		  $addtable  = $mChannelUnit->ChannelInfos['addtable'];
		  if($addtable!=""){
			  $addJoin = " left join $addtable on arc.ID = ".$addtable.".aid ";
			  $addField = "";
			  $fields = explode(",",$mChannelUnit->ChannelInfos['listadd']);
			  foreach($fields as $k=>$v){ $nfields[$v] = $k; }
			  foreach($mChannelUnit->ChannelFields as $k=>$arr){
				  if(isset($nfields[$k])){
				    if($arr['rename']!="")
				  	  $addField .= ",".$addtable.".".$k." as ".$arr['rename'];
				    else
				  	  $addField .= ",".$addtable.".".$k;
				  }
			  }
		  }
		}
	  
	  $limit = trim(eregi_replace("limit","",$limit));
	  if($limit!="") $limitsql = " limit $limit ";
	  else $limitsql = " limit 0,$line ";
		//////////////
		$query = "Select arc.ID,arc.title,arc.iscommend,arc.color,arc.typeid,
		arc.ismake,arc.description,arc.pubdate,arc.senddate,arc.arcrank,arc.click,
		arc.money,arc.litpic,arc.writer,arc.shorttitle,arc.memberid,arc.postnum,arc.lastpost,
		tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,
		tp.namerule2,tp.ispart,tp.moresite,tp.siteurl
		$addField
		from #@__archives arc 
		left join #@__arctype tp on arc.typeid=tp.ID
		$addJoin
		where $orwhere $ordersql $limitsql";
		
		$t1 = ExecTime();
		//echo $query;
		
		
		$dsql->SetQuery($query);
		$dsql->Execute("al");
    $artlist = "";
    if($col>1) $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
    $dtp2 = new DedeTagParse();
    $dtp2->SetNameSpace("field","[","]");
    $dtp2->LoadString($innertext);
    $GLOBALS['autoindex'] = 0;
    for($i=0;$i<$line;$i++)
		{
       if($col>1) $artlist .= "<tr>\r\n";
       for($j=0;$j<$col;$j++)
			 {
         if($col>1) $artlist .= "	<td width='$colWidth'>\r\n";
         if($row = $dsql->GetArray("al"))
         {
           //处理一些特殊字段
           $row['description'] = cn_substr($row['description'],$infolen);
           $row['id'] =  $row['ID'];
           
           $row['arcurl'] = GetFileUrl($row['id'],$row['typeid'],$row['senddate'],
                            $row['title'],$row['ismake'],$row['arcrank'],$row['namerule'],
                            $row['typedir'],$row['money'],true,$row['siteurl']);
           $row['typeurl'] = GetTypeUrl($row['typeid'],MfTypedir($row['typedir']),$row['isdefault'],$row['defaultname'],$row['ispart'],$row['namerule2'],$row['siteurl']);
           
           if($row['litpic']=="") $row['litpic'] = $PubFields['templeturl']."/img/default.gif";
           $row['picname'] = $row['litpic'];
           if($GLOBALS['cfg_multi_site']=='是'){
           	 if($row['siteurl']=="") $row['siteurl'] = $GLOBALS['cfg_mainsite'];
           	 if(!eregi("^http://",$row['picname'])){
           	 	  $row['litpic'] = $row['siteurl'].$row['litpic'];
           	 	  $row['picname'] = $row['litpic'];
           	 }
           }
           
           $row['info'] = $row['description'];
           $row['filename'] = $row['arcurl'];
           $row['stime'] = GetDateMK($row['pubdate']);
           $row['typelink'] = "<a href='".$row['typeurl']."'>".$row['typename']."</a>";
           $row['image'] = "<img src='".$row['picname']."' border='0' width='$imgwidth' height='$imgheight' alt='".ereg_replace("['><]","",$row['title'])."'>";
           $row['imglink'] = "<a href='".$row['filename']."'>".$row['image']."</a>";
           $row['title'] = cn_substr($row['title'],$titlelen);
           $row['textlink'] = "<a href='".$row['filename']."'>".$row['title']."</a>";
           
           if($row['color']!="") $row['title'] = "<font color='".$row['color']."'>".$row['title']."</font>";
           if($row['iscommend']==5||$row['iscommend']==16) $row['title'] = "<b>".$row['title']."</b>";
           
           $row['phpurl'] = $PubFields['phpurl'];
 		       $row['templeturl'] = $PubFields['templeturl'];
 		       
 		       //编译附加表里的数据
 		       if($channelid>0){
             foreach($row as $k=>$v){
 		  	        if(ereg("[A-Z]",$k)) $row[strtolower($k)] = $v;
 		         }
             foreach($mChannelUnit->ChannelFields as $k=>$arr){
 		  	        if(isset($row[$k])) $row[$k] = $mChannelUnit->MakeField($k,$row[$k]);
 		  	     }
 		  	   }
 		       
           if(is_array($dtp2->CTags)){
       	      foreach($dtp2->CTags as $k=>$ctag){
       		 	    if(isset($row[$ctag->GetName()])) $dtp2->Assign($k,$row[$ctag->GetName()]);
       		 	    else $dtp2->Assign($k,"");
       	      }
       	      $GLOBALS['autoindex']++;
           }
           $artlist .= $dtp2->GetResult()."\r\n";
         }//if hasRow
         else{
         	 $artlist .= "";
         }
         if($col>1) $artlist .= "	</td>\r\n";
       }//Loop Col
       if($col>1) $i += $col - 1;
       if($col>1) $artlist .= "	</tr>\r\n";
     }//Loop Line
     if($col>1) $artlist .= "	</table>\r\n";
     $dsql->FreeResult("al");
     
     $t2 = ExecTime();
     //echo "<hr>".($t2-$t1)." $query<hr>";
     
     if($artlist=="") $artlist="&nbsp;";
     return $artlist;
}
?>