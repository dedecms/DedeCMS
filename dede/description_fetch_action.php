<?php
@ob_start();
@set_time_limit(3600);
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_description');
$tjnum = 0;
if($action=='getfields')
{
	header("Pragma:no-cache\r\n");
	header("Cache-Control:no-cache\r\n");
	header("Expires:0\r\n");
	header("Content-Type: text/html; charset=utf-8");
	$dsql = new DedeSql(false);
	if(!$dsql->linkID){
		echo "<font color='red'>连接数据源的数据库失败！</font><br>";
		echo $qbutton;
		exit();
	}
	$channel = $dsql->getone("select addtable from #@__channeltype where ID=$channel");
	$channel = str_replace('#@__',$cfg_dbprefix,$channel['addtable']);
	$dsql->GetTableFields($channel);
	echo "<div style='border:1px solid #ababab;background-color:#FEFFF0;margin-top:6px;padding:3px;line-height:160%'>";
	echo "表(".$channel.")含有的字段：<br>";
	while($row = $dsql->GetFieldObject()){
		echo "<a href=\"javascript:pf('{$row->name}')\"><u>".$row->name."</u></a>\r\n";
	}
	echo "<input type='hidden' name='addtable' value='$channel' />";
	echo "</div>";
	$dsql->Close();
	exit();
}elseif($action == 'fetch')
{
	$dsql = new DedeSql(false);
	if(empty($startdd)) $startdd = 0;
	if(empty($pagesize)) $pagesize = 100;
	if(empty($totalnum)) $totalnum = 0;
	if(empty($sid)) $sid = 0;
	if(empty($eid)) $eid = 0;
	if(empty($dojob)) $dojob = 'desc';
	$addtable = urldecode($addtable);
	$addtable = ereg_replace("[^a-zA-Z_#@]","",$addtable);

	$rpfield = ereg_replace("[^a-zA-Z_\[\]]","",$rpfield);

	$channel = intval($channel);
	if($dsize>250) $dsize = 250;
	$channelinfo = $dsql->getone("select * from #@__channeltype where ID=$channel");
	$maintable = $channelinfo['maintable'];
	if(empty($totalnum)){
		$addquery  = "";
		if($sid!=0) $addquery  = " And ID>='$sid' ";
		if($eid!=0) $addquery  = " And ID<='$eid' ";
		$tjQuery = "Select count(*) as dd From #@__full_search where channelid='{$channel}' $addquery";
		$row = $dsql->GetOne($tjQuery);
		$totalnum = $row['dd'];
	}
	if($totalnum > 0){
	    $addquery  = "";
	    if($sid!=0) $addquery  = " And maintable.ID>='$sid' ";
	    if($eid!=0) $addquery  = " And maintable.ID<='$eid' ";
	    $fquery = "
	      Select maintable.ID,maintable.title,maintable.description,addtable.{$rpfield} as body
	      From $maintable maintable left join {$addtable} addtable on addtable.aid=maintable.ID
	      where maintable.channel='{$channel}' $addquery limit $startdd,$pagesize ;
	    ";

	    $dsql->SetQuery($fquery);
	    $dsql->Execute();
	    while($row=$dsql->GetArray())
	    {
		     $body = $row['body'];
		     $description = $row['description'];
		     if(strlen($description)>10 || $description=='-') continue;
		     $bodytext = preg_replace("/#p#|#e#|副标题|分页标题/isU","",Html2Text($body));
		     if(strlen($bodytext) < $msize) continue;
		     $des = trim(addslashes(cn_substr($bodytext,$dsize)));
		     if(strlen($des)<3) $des = "-";
		     $dsql->ExecuteNoneQuery("Update $maintable set description='{$des}' where ID='{$row['ID']}';");
		     $dsql->ExecuteNoneQuery("Update #@__full_search set addinfos='{$des}' where aid='{$row['ID']}';");
	    }
	    //返回进度信息
	    $startdd = $startdd + $pagesize;
	    if($totalnum > $startdd){
	      	$tjlen = ceil( ($startdd/$totalnum) * 100 );
			$dvlen = $tjlen * 2;
			$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
			$tjsta .= "<br/>完成处理文档总数的：$tjlen %，继续执行任务...";
			$nurl = "description_fetch_action.php?action=fetch&totalnum=$totalnum&startdd={$startdd}&pagesize=$pagesize&channel={$channel}&rpfield={$rpfield}&dsize={$dsize}&msize={$msize}&sid={$sid}&eid=$eid&addtable=".urlencode($addtable);
			$dsql->Close();
			ShowMsg($tjsta,$nurl,0,500);
			exit();
	    }else{
	    	$tjlen=100;
	    	$dsql->executenonequery("OPTIMIZE TABLE `#@__full_search`");
	    	$dsql->executenonequery("OPTIMIZE TABLE `$maintable`");
	    	$dsql->Close();
		    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\r\n";
		    echo "完成所有任务！";
		    exit();
	    }
  }else{
  	$dsql->Close();
	  echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\r\n";
	  echo "完成所有任务！";
	  exit();
  }
	ClearAllLink();
}
?>