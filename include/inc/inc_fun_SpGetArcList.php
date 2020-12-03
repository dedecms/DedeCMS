<?php
$GLOBALS['__SpGetArcList'] = 1;

//获取一个文档列表
//--------------------------------
function SpGetArcList(&$dsql,$templets,$typeid=0,$row=10,$col=1,$titlelen=30,$infolen=160,
  $imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$keyword="",$innertext="",
  $tablewidth="100",$arcid=0,$idlist="",$channelid=0,$limitv="",$att=0,$order="desc",
  $subday=0,$ismember=0,$maintable='#@__archives',$notUpcache=true)
  {
		global $PubFields,$cfg_keyword_like,$cfg_al_cachetime,$cfg_arc_all;

		$row = AttDef($row,10);
		$titlelen = AttDef($titlelen,30);
		$infolen = AttDef($infolen,160);
		$imgwidth = AttDef($imgwidth,120);
		$imgheight = AttDef($imgheight,120);
		$listtype = AttDef($listtype,"all");
    $arcid = AttDef($arcid,0);
    $att = AttDef($att,0);
    $channelid = AttDef($channelid,0);
    $ismember = AttDef($ismember,0);
    $orderby = AttDef($orderby,"default");
    $orderWay = AttDef($order,"desc");
    $maintable = AttDef($maintable,"#@__archives");
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
		if(!empty($idlist) && ereg("[^0-9,]",$idlist)) $idlist = '';

		//检查取缓存
		if($idlist=="" && !eregi("spec",$listtype) && $notUpcache && $cfg_al_cachetime>0) return SpGetArclistCache($dsql,$templets,$typeid,$row,$col,$titlelen,$infolen,
  $imgwidth,$imgheight,$listtype,$orderby,$keyword,$innertext,
  $tablewidth,$arcid,$idlist,$channelid,$limitv,$att,$order,$subday,$ismember,$maintable);

	//按不同情况设定SQL条件 排序方式
	$orwhere = " arc.arcrank > -1 ";

  $addField = "";
  $addJoin = "";
  $channelinfos = '';

  //获取主表
  if(eregi('spec',$listtype)) $channelid = -1;
  if(!empty($typeid)) $reids = explode(',',$typeid);
  if(!empty($channelid))
  {
  	$channelinfos = $dsql->GetOne("Select ID,maintable,addtable,listadd From `#@__channeltype` where ID='$channelid' ");
  	$maintable = $channelinfos['maintable'];
  }else if(!empty($typeid))
  {
		$channelinfos = $dsql->GetOne("select c.ID,c.maintable,c.addtable,c.listadd from `#@__arctype` a left join #@__channeltype c on c.ID=a.channeltype where a.ID='".$reids[0]."' ");
		if(is_array($channelinfos)) {			
			$maintable = $channelinfos['maintable'];
			$channelid = $channelinfos['ID'];
		}
  }

  if(trim($maintable)=='') $maintable = "#@__archives";

//指定的文档ID列表，通常是专题和相关文章，将不使用其它附加条件
//----------------------------------
if($idlist!="")
{
	$idlist = ereg_replace("[^,0-9]","",$idlist);
	if($idlist!="") $orwhere .= "And arc.ID in ($idlist) ";
}
//普通标记（可缓存）
else
{
		//时间限制(用于调用最近热门文章、热门评论之类)
		if($subday>0){
			 $limitvday = time() - ($subday * 24 * 3600);
			 $orwhere .= " And arc.senddate > $limitvday ";
		}
		//文档的自定义属性
		if($att!="") $orwhere .= "And arc.arcatt='$att' ";
		//文档的频道模型
		if(!empty($channelid) && !eregi("spec",$listtype)) $orwhere .= " And arc.channel = '$channelid' ";
		//echo $orwhere.$channelid ;
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

		if($cfg_keyword_like=='N'){ $keyword=""; }

		//类别ID的条件，如果用 "," 分开,可以指定特定类目
		//------------------------------
		if(!empty($typeid))
		{
		  $ridnum = count($reids);
		  if($ridnum>1)
		  {
			  $sonids = '';
		    for($i=0;$i<$ridnum;$i++){
				  $sonids .= ($sonids=='' ? TypeGetSunID($reids[$i],$dsql,'arc',0,true) : ','.TypeGetSunID($reids[$i],$dsql,'arc',0,true));
		    }
		    $orwhere .= " And ( arc.typeid in ($sonids) Or arc.typeid2 in ($sonids) ) ";
		  }else{
			  $sonids = TypeGetSunID($typeid,$dsql,'arc',0,true);
			  $orwhere .= " And ( arc.typeid in ($sonids) Or arc.typeid2 in ($sonids) ) ";
		  }
		  unset($reids);
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

	  //获得附加表的相关信息
		//-----------------------------
		if(is_array($channelinfos))
		{
			$channelinfos['listadd'] = trim($channelinfos['listadd']);
			if($cfg_arc_all=='Y' && is_array($channelinfos) && $channelinfos['listadd']!='')
			{
				  $addField = '';
				  $fields = explode(',',$channelinfos['listadd']);
				  foreach($fields as $v) $addField .= ",addt.{$v}";
				  if($addField!='') $addJoin = " left join `{$channelinfos['addtable']}` addt on addt.aid = arc.ID ";
			}
		}
}//普通标记（可缓存）

	//文档排序的方式
		$ordersql = "";
		if($orderby=='hot'||$orderby=='click') $ordersql = " order by arc.click $orderWay";
		else if($orderby=='pubdate') $ordersql = " order by arc.pubdate $orderWay";
		else if($orderby=='sortrank') $ordersql = " order by arc.sortrank $orderWay";
    else if($orderby=='id') $ordersql = "  order by arc.ID $orderWay";
    else if($orderby=='near') $ordersql = " order by ABS(arc.ID - ".$arcid.")";
    else if($orderby=='lastpost') $ordersql = "  order by arc.lastpost $orderWay";
    else if($orderby=='postnum') $ordersql = "  order by arc.postnum $orderWay";
    else if($orderby=='digg') $ordersql = "  order by arc.digg $orderWay";
    else if($orderby=='diggtime') $ordersql = "  order by arc.diggtime $orderWay";
    else if($orderby=='rand') $ordersql = "  order by rand()";
		else $ordersql=" order by arc.senddate $orderWay";

	  if(!empty($limitv)) $limitvsql = " limit $limitv ";
	  else $limitvsql = " limit 0,$line ";
		//////////////
		$query = "Select arc.ID,arc.title,arc.iscommend,arc.color,arc.typeid,arc.channel,
		    arc.ismake,arc.description,arc.pubdate,arc.senddate,arc.arcrank,arc.click,arc.digg,arc.diggtime,
		    arc.money,arc.litpic,arc.writer,arc.shorttitle,arc.memberid,arc.postnum,arc.lastpost,
		    tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,
		    tp.namerule2,tp.ispart,tp.moresite,tp.siteurl{$addField}
		    from `$maintable` arc left join `#@__arctype` tp on arc.typeid=tp.ID $addJoin
		    where $orwhere $ordersql $limitvsql
		";
		
    $dtp2 = new DedeTagParse();
    $dtp2->SetNameSpace("field","[","]");
    $dtp2->LoadString($innertext);
    if(!is_array($dtp2->CTags)) return '';

    $t1 = ExecTime();
		//if($listtype == "spec"){
		  //echo $idlist."--".$query;
		  //exit();
	  //}

		$dsql->SetQuery($query);
		$dsql->Execute("al");
    $artlist = "";
    if($col>1) $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
    $GLOBALS['autoindex'] = 0;
    for($i=0;$i<$line;$i++)
		{
       if($col>1) $artlist .= "<tr>\r\n";
       for($j=0;$j<$col;$j++)
			 {
         if($col>1) $artlist .= "	<td width='$colWidth'>\r\n";
         if($row = $dsql->GetArray("al",MYSQL_ASSOC))
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
           if($GLOBALS['cfg_multi_site']=='Y'){
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

       	   foreach($dtp2->CTags as $k=>$ctag){ @$dtp2->Assign($k,$row[$ctag->GetName()]); }
       	   $GLOBALS['autoindex']++;

           $artlist .= $dtp2->GetResult();
         }//if hasRow
         else{
         	 $artlist .= '';
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
     
     return trim($artlist);
}

//缓存检查
//-----------------------
function SpGetArclistCache($dsql,$templets,$typeid=0,$row=10,$col=1,$titlelen=30,$infolen=160,
  $imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$keyword="",$innertext="",
  $tablewidth="100",$arcid=0,$idlist="",$channelid=0,$limitv="",$att=0,$order="desc",$subday=0,$ismember=0,$maintable='#@__archives')
{
	 global $cfg_al_cachetime;
	 $ntime = time();
	 
	 $hash = md5($typeid.$row.$channelid.$titlelen.$att.$templets.$listtype.$orderby.$order.$limitv.$subday.$ismember.$col.$keyword.$infolen.$imgwidth.$imgheight);
	 
	 //检查缓存是否存在指定内容
	 $getCacheQuery = " Select id,uptime From `#@__cache_tagindex` where hash = '$hash' ";

	 $drow = $dsql->GetOne($getCacheQuery);

	 //没有缓存或缓存已过期
	 if(!is_array($drow) || ($ntime - $drow['uptime']) > ($cfg_al_cachetime * 3600) )
	 {
	 	    $listValue = SpGetArcList($dsql,$templets,$typeid,$row,$col,$titlelen,$infolen,
                   $imgwidth,$imgheight,$listtype,$orderby,$keyword,$innertext,
                   $tablewidth,$arcid,$idlist,$channelid,$limitv,
                   $att,$order,$subday,$ismember,$maintable,false);

       //没缓存
       if(!is_array($drow))
       {
     	    $inQuery = "INSERT INTO `#@__cache_tagindex`(`typeid` , `channelid` , `uptime` ,`hash`) VALUES ( '$typeid', '$channelid', '$ntime', '$hash'); ";
          $rs = $dsql->ExecuteNoneQuery($inQuery);
	 	      $cacheid = $dsql->GetLastID();
	 	      if($cacheid>0){
	 	    	   $rs = $dsql->ExecuteNoneQuery("Insert Into `#@__cache_value`(`cid`,`value`) Values('$cacheid','".addslashes($listValue)."'); ");
	 	    	   if(!$rs) $dsql->ExecuteNoneQuery("Delete From `#@__cache_tagindex` where `id`='$cacheid'; ");
	 	      }
       }
       //更新缓存
       else
       {
     	    $rs = $dsql->ExecuteNoneQuery("Update `#@__cache_value` set `value`='".addslashes($listValue)."' where cid='{$drow['id']}' ");
     	    if($rs) $dsql->ExecuteNoneQuery("Update `#@__cache_tagindex` set uptime='$ntime' where id='{$drow['id']}' ");
       }
       return $listValue;
	 }
	 //从缓存获取数据
	 else
	 {
	 	 $drow = $dsql->GetOne("Select * From `#@__cache_value` where cid='{$drow['id']}' ");
	 	 return $drow['value'];
	 }
}

?>