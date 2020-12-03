<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typeunit.php");
require_once(dirname(__FILE__)."/../include/inc_archives_view.php");
SetPageRank(10);
if(empty($startid)) $startid = 1;//起始ID号
if(empty($endid)) $endid = 0;//结束ID号
if(empty($startdd)) $startdd = 0;//结果集起始记录值
if(empty($pagesize)) $pagesize = 20;
if(empty($totalnum)) $totalnum = 0;
if(empty($typeid)) $typeid = 0;

$dsql = new DedeSql(false);
//获取ID条件
//------------------------
$gwhere = " where ID>=$startid And arcrank=0 ";
if($endid > $startid) $gwhere .= " And ID<= $endid ";

$idsql = "";

if($typeid!=0){
	$idArrary = TypeGetSunTypes($typeid,$dsql,0);
	if(is_array($idArrary))
	{
	  foreach($idArrary as $tid){
		  if($idsql=="") $idsql .= " typeid=$tid ";
		  else $idsql .= " or typeid=$tid ";
	  }
	  $idsql = " And (".$idsql.")";
  }
  $idsql = $gwhere.$idsql;
}
if($idsql=="") $idsql = $gwhere;
//统计记录总数
//------------------------
if($totalnum==0)
{
	$row = $dsql->GetOne("Select count(*) as dd From #@__archives $idsql");
	$totalnum = $row['dd'];
}
//获取记录，并生成HTML
if($totalnum > $startdd+$pagesize) $limitSql = " limit $startdd,$pagesize";
else $limitSql = " limit $startdd,".($totalnum - $startdd);
$tjnum = $startdd;
$dsql->SetQuery("Select ID From #@__archives $idsql $limitSql");
$dsql->Execute();
while($row=$dsql->GetObject())
{
	$tjnum++;
	$ID = $row->ID;
	$ac = new Archives($ID);
	$rurl = $ac->MakeHtml();
	$ac->Close();
}
//返回提示信息
if($totalnum>0) $tjlen = ceil( ($tjnum/$totalnum) * 100 );
else $tjlen=100;
$dvlen = $tjlen * 2;
$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
$tjsta .= "<br/>完成创建文件总数的：$tjlen %，继续执行任务...";

if($tjnum < $totalnum)
{
	$nurl = "action_makehtml_archives.php?endid=$endid&startid=$startid&typeid=$typeid&totalnum=$totalnum&startdd=".($startdd+$pagesize)."&pagesize=$pagesize";
	$dsql->Close();
	ShowMsg($tjsta,$nurl,0,300);
	exit();
}
else
{
	$dsql->Close();
	echo "完成所有创建任务！";
	exit();
}

?>