<?php
@ob_start();
@set_time_limit(3600);
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Keyword');
require_once(dirname(__FILE__)."/../include/pub_splitword_www.php");
if(empty($action)) $action = '';
/*-------------------------------
//列出数据库表里的字段
function __getfields()
--------------------------------*/
if($action=='getfields')
{
	AjaxHead();
	$dsql = new DedeSql(false);
	if(!$dsql->linkID){
		echo "<font color='red'>连接数据源的数据库失败！</font><br>";
		echo $qbutton;
		exit();
	}
	$exptable = $dsql->getone("select addtable from #@__channeltype where ID=$exptable");
	$exptable = str_replace('#@__',$cfg_dbprefix,$exptable['addtable']);
	$dsql->GetTableFields($exptable);
	echo "<div style='border:1px solid #ababab;background-color:#FEFFF0;margin-top:6px;padding:3px;line-height:160%'>";
	echo "表(".$exptable.")含有的字段：<br>";
	while($row = $dsql->GetFieldObject()){
		echo "<a href=\"javascript:pf('{$row->name}')\"><u>".$row->name."</u></a>\r\n";
	}
	echo "<input type='hidden' name='addtable' value='$exptable' />";
	echo "</div>";
	$dsql->Close();
	exit();
}elseif($action == 'fetch')
{
  header("Content-Type: text/html; charset={$cfg_ver_lang}");
	$dsql = new DedeSql(false);
	if(empty($startdd)) $startdd = 0;//结果集起始记录值
	if(empty($pagesize)) $pagesize = 50;
	if(empty($totalnum)) $totalnum = 0;
	$exptable = intval($exptable);
	$addtable = urldecode($addtable);
	$addtable = ereg_replace("[^a-zA-Z_#@]","",$addtable);
	$rpfield = ereg_replace("[^a-zA-Z_\[\]]","",$rpfield);
	//统计记录总数
	//------------------------
	if($totalnum==0)
	{
		$row = $dsql->GetOne("Select count(*) as dd From #@__full_search where trim(keywords)='' And channelid='$exptable';");
		$totalnum = $row['dd'];
	}
	//获取记录，并分析关键字
	if($totalnum > $startdd+$pagesize) $limitSql = " limit $startdd,$pagesize";
	else if(($totalnum-$startdd)>0) $limitSql = " limit $startdd,".($totalnum - $startdd);
	else $limitSql = "";
	$tjnum = $startdd;
	if($limitSql!=""){
		$fquery = "
		Select maintable.aid,maintable.title,addtable.$rpfield as body
		From #@__full_search maintable left join $addtable addtable on addtable.aid=maintable.aid
		where trim(maintable.keywords)='' And maintable.channelid='$exptable' $limitSql
		";
		$dsql->SetQuery($fquery);
		$dsql->Execute();
		$sp = new SplitWord();
		while($row=$dsql->GetObject())
		{
			$tjnum++;
			$ID = $row->aid;
			$keywords = "";
			$titleindexs = explode(" ",trim($sp->GetIndexText($sp->SplitRMM($row->title))));
			$allindexs = explode(" ",trim($sp->GetIndexText($sp->SplitRMM(Html2Text($row->body)),200)));
			if(is_array($allindexs) && is_array($titleindexs)){
				foreach($titleindexs as $k){
					if(strlen($keywords)>=50) break;
					else $keywords .= $k." ";
				}
				foreach($allindexs as $k){
					if(strlen($keywords)>=50) break;
					else if(!in_array($k,$titleindexs)) $keywords .= $k." ";
			  }
			}
			$keywords = addslashes($keywords);
			$dsql->SetQuery("update #@__full_search set keywords=' $keywords ' where aid='$ID'");
			$dsql->ExecuteNoneQuery();
		}
		$sp->Clear();
		unset($sp);
	}//end if limit
	//返回提示信息
	if($totalnum>0) $tjlen = ceil( ($tjnum/$totalnum) * 100 );
	else $tjlen=100;
	$dvlen = $tjlen * 2;
	$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
	$tjsta .= "<br/>完成处理文档总数的：$tjlen %，继续执行任务...";
	if($tjnum < $totalnum)
	{
		$nurl = "keywords_fetch_action.php?action=fetch&totalnum=$totalnum&startdd=".($startdd+$pagesize)
		."&pagesize=$pagesize&rpfield=$rpfield&addtable=".urlencode($addtable)."&exptable=".$exptable;
		$dsql->Close();
		ShowMsg($tjsta,$nurl,0,500);
		exit();
	}
	else
	{
		$dsql->executenonequery("OPTIMIZE TABLE `#@__full_search`");
		$dsql->Close();
		showmsg("完成所有任务！",'javascript:;');
	}
	ClearAllLink();
}
?>