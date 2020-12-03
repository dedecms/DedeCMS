<?
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)){
	ShowMsg("对不起，请指定栏目参数！","catalog_main.php");
	exit();
}
if(empty($cid)) $cid = 0;
$cid = ereg_replace("[^0-9]","",$cid);
/*--------------------------
//增加文档
function addArchives();
---------------------------*/
if($dopost=="addArchives")
{
	if($cid==0){
		require_once(dirname(__FILE__)."/article_add.php");
		exit();
	}
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select #@__channeltype.addcon from #@__arctype left join #@__channeltype on #@__channeltype.ID=#@__arctype.channeltype where #@__arctype.ID='$cid'");
	$gurl = $row["addcon"];
	$dsql->Close();
	if($gurl==""){
		ShowMsg("对不起，你指的栏目可能有误！","catalog_main.php");
	  exit();
	}
	require_once(dirname(__FILE__)."/$gurl");
	exit();
}
/*--------------------------
//管理文档
function listArchives();
---------------------------*/
else if($dopost=="listArchives")
{
	if(!isset($channelid)) $channelid = 0;
	if(!empty($gurl)){
		$gurl = str_replace("..","",$gurl);
		require_once(dirname(__FILE__)."/$gurl");
	  exit();
	}
	if($cid>0)
	{
	  $dsql = new DedeSql(false);
	  $row = $dsql->GetOne("Select #@__arctype.typename,#@__channeltype.typename as channelname,#@__channeltype.ID,#@__channeltype.mancon from #@__arctype left join #@__channeltype on #@__channeltype.ID=#@__arctype.channeltype where #@__arctype.ID='$cid'");
	  $gurl = $row["mancon"];
	  $channelid = $row["ID"];
	  $typename = $row["typename"];
	  $channelname = $row["channelname"];
	  $dsql->Close();
	  if($gurl==""){
		  ShowMsg("对不起，你指的栏目可能有误！","catalog_main.php");
	    exit();
	  }
  }
  else if($channelid>0)
  {
  	$dsql = new DedeSql(false);
	  $row = $dsql->GetOne("Select typename,ID,mancon from #@__channeltype where ID='$channelid'");
	  $gurl = $row["mancon"];
	  $channelid = $row["ID"];
	  $typename = "";
	  $channelname = $row["typename"];
	  $dsql->Close();
  }
	require_once(dirname(__FILE__)."/$gurl");
	exit();
}
/*--------------------------
//浏览通用模板目录
function viewTempletDir();
---------------------------*/
else if($dopost=="viewTemplet")
{
	header("location:file_manage_main.php?activepath=".$cfg_templets_dir);
	exit();
}
/*--------------------------
//留言簿管理
function GoGuestBook();
---------------------------*/
else if($dopost=="guestbook")
{
	echo "<script language='javascript'>location='".$cfg_plus_dir."/guestbook/index.php?gotopagerank=admin';</script>";
	exit();
}
/*------------------------
浏览单个页面的栏目
function ViewSgPage()
------------------------*/
else if($dopost=="viewSgPage")
{
	require_once(dirname(__FILE__)."/../include/inc_arclist_view.php");
	$lv = new ListView($cid);
  $pageurl = $lv->MakeHtml();
  $lv->Close();
  ShowMsg("更新缓冲，请稍后...",$pageurl);
	exit();
}
/*------------------------
更改栏目排列顺序
function upRank()
------------------------*/
else if($dopost=="upRank")
{
	SetPageRank(5);
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select reID,sortrank From #@__arctype where ID='$cid'");
	$reID = $row['reID'];
	$sortrank = $row['sortrank'];
	$row = $dsql->GetOne("Select sortrank From #@__arctype where sortrank<=$sortrank And reID=$reID order by sortrank desc ");
	if(is_array($row)){
		$sortrank = $row['sortrank']-1;
		$dsql->SetQuery("update #@__arctype set sortrank='$sortrank' where ID='$cid'");
		$dsql->ExecuteNoneQuery();
	}
	$dsql->Close();
	ShowMsg("操作成功，返回目录...","catalog_main.php");
	exit();
}
/*---------------------
获取JS文件
function GetJs
----------------------*/
else if($dopost=="GetJs")
{
	require_once(dirname(__FILE__)."/makehtml_js.php");
	exit();
}
/*-----------
编辑单独页面
function editSgPage();
-----------*/
else if($dopost=="editSgPage")
{
	SetPageRank(5);
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select defaultname,typedir From #@__arctype where ID='$cid'");
	$dsql->Close();
	require_once(dirname(__FILE__)."/../include/inc_arclist_view.php");
	$lv = new ListView($cid);
	$lv->MakeHtml();
	$lv->Close();
 	$editurl = "file_manage_view.php?backurl=catalog_main.php&fmdo=editview&ishead=yes&filename=".$row['defaultname']."&activepath=".urlencode($row['typedir'])."&job=edit";
 	header("location:$editurl");
 	exit();
}
/*-----------
编辑模板页面
function editSgTemplet();
-----------*/
else if($dopost=="editSgTemplet")
{
	SetPageRank(5);
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select tempone From #@__arctype where ID='$cid'");
	$dsql->Close();
	$tempone = $row['tempone'];
	if(!is_file($cfg_basedir.$cfg_templets_dir."/".$tempone))
	{
		ShowMsg("这个单独页面没有使用模板，现在转向直接编辑这个页面。","catalog_do.php?cid=$cid&dopost=editSgPage");
		exit();
	}
	$tempones = explode('/',$tempone);
	$filename = $tempones[count($tempones)-1];
	$tmpdir = $cfg_templets_dir;
	if(count($tempones)>1)
	{
	  foreach($tempones as $v){
		  if($v!="") $tmpdir .= "/".$v;
	  }
  }
	$editurl = "file_manage_view.php?backurl=catalog_main.php&fmdo=editview&ishead=yes&filename=".$filename."&activepath=".urlencode($tmpdir)."&job=edit";
 	header("location:$editurl");
 	exit();
}
?>
