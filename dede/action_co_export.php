<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
SetPageRank(5);
//对保存的内容进行处理
//--------------------------------
$iscommend = 0;
$senddate  = time();
$sortrank  = $senddate;
$ismake    = 0;

if($typeid==""||$typeid==0)
{
	ShowMsg('档案主栏目必须选择！','javascript:;');
	exit();
}
$arcrank = $sendtype;
$color   =  "";
$description = "";
$keywords = "";
$adminID  = $cuserLogin->getUserID();

$dsql = new DedeSql(false);
$arcInfo = $dsql->GetOne("Select ID From #@__archives order by ID desc");
$startID = $arcInfo['ID']+1;

$dsql->SetQuery("Select * From #@__courl where nid='$nid' And result<>''");
$dsql->Execute();

if($dsql->GetTotalRow()==0)
{
	$dsql->close();
	ShowMsg("这个节点不存在任何数据或已经导入完毕！","javascript:;");
  exit();
}
$okdd = 0;
$dtp  = new DedeTagParse();
$dtp->SetNameSpace("dede","{","}");
//开始导入数据
//----------------------------------
$sdd = 1;
while($row = $dsql->GetObject())
{
	if($maxexport > 0){ if($sdd > $maxexport) break; }
	$sdd++; 
	$naid = $row->aid;
	$dtp->LoadString($row->result);
	$fields = "";
	foreach($dtp->CTags as $tid => $ctag){
	  if($ctag->GetName()=="field") $fields[$ctag->GetAtt("name")] = $ctag->GetInnerText();
  }
  
  if($title_sel=="0") $title = addslashes($row->title);
  else $title = cn_substr($fields[$title_sel],56);
  if($title=="") $title = addslashes($row->title);
  
  if($writer_sel=="0") $writer = $writer_null;
  else $writer = addslashes($fields[$writer_sel]);
  
  if($source_sel=="0") $source = $source_null;
  else $source = addslashes($fields[$source_sel]);
 
  if($pubdate_sel=="0") $pubdate = $pubdate_null;
  else $pubdate = addslashes($fields[$pubdate_sel]);
  
  if($body_sel=="0") $body = $body_null;
  else $body = addslashes($fields[$body_sel]);
  
  $pubdate = GetMkTime($pubdate);
  $cpdate = GetMkTime("1990-01-01 00:00:00");
  if($pubdate <= $cpdate) $pubdate = $senddate;
 
  $inQuery = "INSERT INTO #@__archives(
  typeid,typeid2,sortrank,iscommend,
  ismake,channel,arcrank,click,title,color,writer,source,litpic,
  pubdate,senddate,adminID,memberID,description,keywords) 
  VALUES ('$typeid','$typeid2','$sortrank','$iscommend',
  '$ismake','1','$arcrank','0','$title','$color','$writer','$source','',
  '$pubdate','$senddate','$adminID','0','$description','$keywords');";
  $dsql->SetQuery($inQuery);
  if($dsql->ExecuteNoneQuery($inQuery))
  {
  	$aid = $dsql->GetLastID();
  	$dsql->SetQuery("INSERT INTO #@__addonarticle(aid,typeid,body) Values('$aid','$typeid','$body')");
    if(!$dsql->ExecuteNoneQuery())
    {
	    $dsql->SetQuery("Delete From #@__archives where ID='$aid'");
	    $dsql->ExecuteNoneQuery();
    }
    else
    {
    	if($nextdo==-1){
	       $dsql->SetQuery("Delete From #@__courl where aid='$naid'");
	       $dsql->ExecuteNoneQuery();
      }
    	$okdd++;
    }
  }//Execute main ok
}
unset($dtp);
$dsql->Close();

if($maxexport>0 && $okdd>0){ ShowMsg("成功导出 $okdd 条数据到库中，<br>请点击上面的[确定]按钮继续导入...","javascript:;"); }
else{ ShowMsg("成功导出 $okdd 条数据到库中！","javascript:;"); } 
exit();
?>