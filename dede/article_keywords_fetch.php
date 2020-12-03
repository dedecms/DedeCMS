<?
@ob_start();
@set_time_limit(3600);
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_splitword_www.php");
SetPageRank(10);
if(empty($startdd)) $startdd = 0;//结果集起始记录值
if(empty($pagesize)) $pagesize = 20;
if(empty($totalnum)) $totalnum = 0;

$dsql = new DedeSql(false);
//统计记录总数
//------------------------
if($totalnum==0)
{
	$row = $dsql->GetOne("Select count(*) as dd From #@__archives where keywords='' And channel='1';");
	$totalnum = $row['dd'];
}
//获取记录，并分析关键字
if($totalnum > $startdd+$pagesize) $limitSql = " limit $startdd,$pagesize";
else $limitSql = " limit $startdd,".($totalnum - $startdd);
$tjnum = $startdd;
$fquery = "
Select #@__archives.ID,#@__archives.title,#@__addonarticle.body 
From #@__archives left join #@__addonarticle on #@__addonarticle.aid=#@__archives.ID
where #@__archives.keywords='' And #@__archives.channel='1' $limitSql 
";
$dsql->SetQuery($fquery);
$dsql->Execute();
$sp = new SplitWord();
while($row=$dsql->GetObject())
{
	$tjnum++;
	$ID = $row->ID;
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
	$dsql->SetQuery("update #@__archives set keywords='$keywords' where ID='$ID'");
	$dsql->ExecuteNoneQuery();
}
$sp->Clear();
unset($sp);
//返回提示信息
if($totalnum>0) $tjlen = ceil( ($tjnum/$totalnum) * 100 );
else $tjlen=100;
$dvlen = $tjlen * 2;
$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
$tjsta .= "<br/>完成处理文档总数的：$tjlen %，继续执行任务...";

if($tjnum < $totalnum)
{
	$nurl = "article_keywords_fetch.php?totalnum=$totalnum&startdd=".($startdd+$pagesize)."&pagesize=$pagesize";
	$dsql->Close();
	ShowMsg($tjsta,$nurl,0,500);
	exit();
}
else
{
	$dsql->Close();
	echo "完成所有任务！";
	exit();
}

?>