<?php
$GLOBALS['__SpGetFullList'] = 1;
//------------------------------
//获取整站的文档列表
//本函数对应 arcfulllist 和 likeart 标记
//与 arclist 不同的地方是，本标记更自由（相当于旧版的arclist），
//但不生成缓存，不能按各类不同形式排序，不能调用浏览数，性能稍低，灵活性也不如arclist
//--------------------------------
function SpGetFullList(&$dsql,$typeid=0,$channelid=0,$row=10,$titlelen=30,$infolen=160,
   $keyword='',$innertext='',$idlist='',$limitv='',$ismember=0,$orderby='',$imgwidth=120,$imgheight=120)
  {
		$row = AttDef($row,10);
		$line = $row;
		$titlelen = AttDef($titlelen,30);
		$infolen = AttDef($infolen,160);
    $channelid = AttDef($channelid,0);
    $ismember = AttDef($ismember,0);
    $limitv = AttDef($limitv,'');
		$keyword = trim($keyword);
		$typeid = AttDef($typeid,'');
		$innertext = trim($innertext);
		$imgwidth = AttDef($imgwidth,120);
		$imgheight = AttDef($imgheight,120);
		$orderby = trim($orderby);
		if($innertext=="") $innertext = GetSysTemplets("part_arclist.htm");
		if(empty($idlist)) $idlist = '';
		else $idlist = ereg_replace("[^,0-9]","",$idlist);

	  //按不同情况设定SQL条件 排序方式
	  $orwhere = " arcf.arcrank > -1 ";

    //指定的文档ID列表，通常是专题和相关文章，使用了idlist将不启用后面任何条件
	  $idlist = trim($idlist);
	  if($idlist!=''){
	  	$orwhere .= "And arcf.aid in ($idlist) ";
	  }
    //没使用idlist才启用这些条件
    else
    {
		  //文档的频道模型
		  if(!empty($channelid)) $orwhere .= " And arcf.channelid = '$channelid' ";

		  //是否为会员文档
		  if($ismember==1) $orwhere .= " And arcf.memberid>0  ";

		  //指定栏目条件，如果用 "," 分开,可以指定特定类目
		  if(!empty($typeid) && empty($idlist))
		  {
		    $reids = explode(",",$typeid);
		    $ridnum = count($reids);
		    if($ridnum>1){
			    $tpsql = "";
		      for($i=0;$i<$ridnum;$i++)
		      {
				    $sonids = TypeGetSunID($reids[$i],$dsql,'arc',0,true);
				    $tpsql .= ($tpsql=='' ? $sonids : ','.$sonids);
		      }
		      $tpsql = " And (arcf.typeid in ($tpsql)) ";
		      $orwhere .= $tpsql;
		      unset($tpsql);
		    }else{
			    $sonids = TypeGetSunID($typeid,$dsql,'arc',0,true);
			    if(ereg(',',$sonids)) $orwhere .= " And ( arcf.typeid in ($sonids) ) ";
			    else $orwhere .= " And ( arcf.typeid=$sonids ) ";
		    }
		    unset($reids);
	    }

		  //指定了关键字条件
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
		    if($rstr!="") $orwhere .= " And CONCAT(arcf.title,arcf.keywords) REGEXP '$rstr' ";
		    unset($keywords);
	    }
    }//没使用idlist才启用这些条件
    
	 //文档排序的方式
		$ordersql = "";
    if($orderby=='rand') $ordersql = "  order by rand()";
    else if($orderby=='click'||$orderby=='hot') $ordersql = "  order by arcf.click desc";
    else if($orderby=='digg') $ordersql = "  order by arcf.digg desc";
    else if($orderby=='diggtime') $ordersql = "  order by arcf.diggtime desc";
		else $ordersql=" order by arcf.aid desc";

    //返回结果条数
	  if(!empty($limit)) $limitsql = " limit $limitv ";
	  else $limitsql = " limit 0,$line ";

	  //载入底层模板
	  $dtp2 = new DedeTagParse();
    $dtp2->SetNameSpace("field","[","]");
    $dtp2->LoadString($innertext);
    if(!is_array($dtp2->CTags)) return '';

		//执行SQL查询
		$query = "Select arcf.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl
		from `#@__full_search` arcf left join `#@__arctype` tp on arcf.typeid=tp.ID
		where $orwhere $ordersql $limitsql ";
		
		$t1 = ExecTime();
		$dsql->SetQuery($query);
		$dsql->Execute("alf");
    $artlist = '';
    $GLOBALS['autoindex'] = 0;
    while($row = $dsql->GetArray("alf"))
    {
       //处理一些特殊字段
       $row['description'] = cn_substr($row['addinfos'],$infolen);
       $row['id'] =  $row['aid'];
       if(!isset($row['picname'])) $row['picname'] = '';
       $row['filename'] = $row['arcurl'] = $row['url'];
       $row['typeurl'] = GetTypeUrl($row['typeid'],MfTypedir($row['typedir']),$row['isdefault'],$row['defaultname'],$row['ispart'],$row['namerule2'],$row['siteurl']);
       if($row['litpic']=="") $row['litpic'] = $GLOBALS['PubFields']['templeturl']."/img/default.gif";
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
       foreach($dtp2->CTags as $k=>$ctag)
       {
       		if(isset($row[$ctag->GetName()])){
       			$dtp2->Assign($k,$row[$ctag->GetName()]);
       		}
       		else $dtp2->Assign($k,'');
       }
       $GLOBALS['autoindex']++;
       $artlist .= $dtp2->GetResult();
     }//Loop Line
     $dsql->FreeResult("alf");
     $t2 = ExecTime();
     //echo "<hr>".($t2-$t1)." $query<hr>";
     return $artlist;
}
?>