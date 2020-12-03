<?php
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
	if(empty($cid) && empty($channelid))
	{
		require_once(_ADMIN."/public_guide.php");
		exit();
	}
	$dsql = new DedeSql(false);
	if(!empty($channelid)) $row = $dsql->GetOne("Select ID,addcon from #@__channeltype where ID='$channelid'");
	else $row = $dsql->GetOne("Select #@__channeltype.addcon,#@__channeltype.ID from #@__arctype left join #@__channeltype on #@__channeltype.ID=#@__arctype.channeltype where #@__arctype.ID='$cid'");
	$gurl = $row["addcon"];
	$channelid = $row['ID'];
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
	  $dsql = new DedeSql(-100);
	  $row = $dsql->GetOne("Select t.typename,c.typename as channelname,c.ID,c.mancon from #@__arctype t left join #@__channeltype c on c.ID=t.channeltype where t.ID='$cid'");
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
  	$dsql = new DedeSql(-100);
	  $row = $dsql->GetOne("Select typename,ID,mancon from #@__channeltype where ID='$channelid'");
	  $gurl = $row["mancon"];
	  $channelid = $row["ID"];
	  $typename = "";
	  $channelname = $row["typename"];
	  $dsql->Close();
  }
  if(empty($gurl)) $gurl = 'content_list.php';
	header("location:{$gurl}?channelid={$channelid}&cid={$cid}");
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
	//检查权限许可
  CheckPurview('t_Edit,t_AccEdit');
  //检查栏目操作许可
  CheckCatalog($cid,"你无权更改本栏目！");
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
else if($dopost=="upRankAll")
{
	//检查权限许可
  CheckPurview('t_Edit');
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select ID From #@__arctype order by ID desc");
	if(is_array($row))
	{
		$maxID = $row['ID'];
		for($i=1;$i<=$maxID;$i++){
			if(isset(${'sortrank'.$i})){
				$dsql->ExecuteNoneQuery("Update #@__arctype set sortrank='".(${'sortrank'.$i})."' where ID='{$i}';");
			}
		}
	}
	$dsql->Close();
	ShowMsg("操作成功，正在返回...","catalog_main.php");
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
	//检查权限许可
  CheckPurview('plus_文件管理器');
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select defaultname,typedir From #@__arctype where ID='$cid'");
	$dsql->Close();
	require_once(dirname(__FILE__)."/../include/inc_arclist_view.php");
	$lv = new ListView($cid);
	$lv->MakeHtml();
	$lv->Close();
 	$row['typedir'] = eregi_replace("\{cmspath\}",$cfg_cmspath,$row['typedir']);
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
  //检查权限许可
  CheckPurview('plus_文件管理器');
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select tempone From #@__arctype where ID='$cid'");
	$dsql->Close();
	$tempone = $row['tempone'];
	$tempone = eregi_replace("\{style\}",$cfg_df_style,$tempone);
	if(!is_file($cfg_basedir.$cfg_templets_dir."/".$tempone)){
		ShowMsg("这个单独页面没有使用模板，现在转向直接编辑这个页面。","catalog_do.php?cid=$cid&dopost=editSgPage");
		exit();
	}
	$tempones = explode('/',$tempone);
	$filename = $tempones[count($tempones)-1];
	$tmpdir = $cfg_templets_dir;
	if(count($tempones)>1){
	  foreach($tempones as $v){
		  if($v!="") $tmpdir .= "/".$v;
	  }
  }
	$editurl = "file_manage_view.php?backurl=catalog_main.php&fmdo=editview&ishead=yes&filename=".$filename."&activepath=".urlencode($tmpdir)."&job=edit";
 	header("location:$editurl");
 	exit();
}
/*-----------
获得子类的内容
function GetSunLists();
-----------*/
else if($dopost=="GetSunLists")
{
	$userChannel = $cuserLogin->getUserChannel();
	require_once(dirname(__FILE__)."/../include/inc_typeunit_admin.php");
	AjaxHead();
	PutCookie('lastCid',$cid,3600*24,"/");
	$tu = new TypeUnit($userChannel);
	$tu->dsql = new DedeSql(false);
  $tu->LogicListAllSunType($cid,"　");
  $tu->Close();
}
/*-----------
获得子类的内容
function GetSunListsMenu();
-----------*/
else if($dopost=="GetSunListsMenu")
{
	$userChannel = $cuserLogin->getUserChannel();
	require_once(dirname(__FILE__)."/../include/inc_typeunit_menu.php");
	AjaxHead();
	PutCookie('lastCidMenu',$cid,3600*24,"/");
	$tu = new TypeUnit($userChannel);
	$tu->dsql = new DedeSql(false);
	$tu->LogicListAllSunType($cid,"　");
  $tu->Close();
}
/*-----------
获得子类的内容
function GetSunListsTree();
-----------*/
else if($dopost=="GetSunListsTree")
{
	$userChannel = $cuserLogin->getUserChannel();
	require_once(dirname(__FILE__)."/../include/inc_type_tree.php");
	if(empty($opall)) $opall = 0;
	if(empty($c)) $c = 0;
	if(empty($cid)) $cid = 0;
	AjaxHead();
	PutCookie('lastCidTree',$cid,3600*24,"/");
	$tu = new TypeTree($userChannel);
	$tu->dsql = new DedeSql(false);
	$tu->LogicListAllSunType($cid,"　",$opall,$c);
  $tu->Close();
}

ClearAllLink();
?>
