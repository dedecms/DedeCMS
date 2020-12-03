<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/inc/inc_batchup.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
empty($_COOKIE['ENV_GOBACK_URL']) ? $ENV_GOBACK_URL = "content_list.php" : $ENV_GOBACK_URL=$_COOKIE['ENV_GOBACK_URL'];
if(empty($dopost)||empty($aid)){
	ShowMsg("对不起，你没指定运行参数！","-1");
	exit();
}
$aid = ereg_replace("[^0-9]","",$aid);
/*--------------------------
//编辑文档 
function editArchives();
---------------------------*/
if($dopost=="editArchives")
{
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select #@__channeltype.editcon from #@__archives left join #@__channeltype on #@__channeltype.ID=#@__archives.channel where #@__archives.ID='$aid'");
	$gurl = $row["editcon"];
	$dsql->Close();
	if($gurl==""){ $gurl=="article_edit.php"; }
	require_once(dirname(__FILE__)."/$gurl");
}
/*--------------------------
//浏览文档
function viewArchives();
---------------------------*/
else if($dopost=="viewArchives")
{
	$aid = ereg_replace("[^0-9]","",$aid);
	$dsql = new DedeSql(false);
	$arcQuery = "
    Select #@__archives.ID,#@__archives.title,#@__archives.typeid,
    #@__archives.ismake,#@__archives.senddate,#@__archives.arcrank,#@__channeltype.addtable,
 		#@__archives.money,#@__arctype.typedir,#@__arctype.typename,
 		#@__arctype.namerule,#@__arctype.namerule2,#@__arctype.ispart,
 		#@__arctype.moresite,#@__arctype.siteurl,#@__arctype.siterefer,#@__arctype.sitepath 
		from #@__archives
		left join #@__arctype on #@__archives.typeid=#@__arctype.ID
		left join #@__channeltype on #@__channeltype.ID=#@__archives.channel
		where #@__archives.ID='$aid'
    ";
  $arcRow = $dsql->GetOne($arcQuery);
  $dsql->Close();
	if($arcRow['ismake']==-1||$arcRow['arcrank']!=0
    ||$arcRow['typeid']==0||$arcRow['money']>0){
    	echo "<script language='javascript'>location.href='{$cfg_plus_dir}/view.php?aid={$aid}';</script>";
    	exit();
  }
  $arcurl = GetFileUrl($arcRow['ID'],$arcRow['typeid'],$arcRow['senddate'],$arcRow['title'],$arcRow['ismake'],
           $arcRow['arcrank'],$arcRow['namerule'],$arcRow['typedir'],$arcRow['money'],true,$arcRow['siteurl']);
  $arcfile = GetFileUrl($arcRow['ID'],$arcRow['typeid'],$arcRow['senddate'],$arcRow['title'],$arcRow['ismake'],
           $arcRow['arcrank'],$arcRow['namerule'],$arcRow['typedir'],$arcRow['money'],false,'');
	$truefile = GetTruePath($arcRow['siterefer'],$arcRow['sitepath']).$arcfile;
  if(!file_exists($truefile)) {
  	MakeArt($aid,true);
  }
  echo "<script language='javascript'>location.href='$arcurl"."?".mytime()."';</script>";
	exit();
}
/*--------------------------
//推荐文档
function commendArchives();
---------------------------*/
else if($dopost=="commendArchives")
{
	CheckPurview('a_Commend,sys_ArcBatch');
	if( $aid!="" && !ereg("(".$aid."`|`".$aid.")",$qstr) ) $qstr .= "`".$aid;
	if($qstr==""){
	  ShowMsg("参数无效！",$ENV_GOBACK_URL);
	  exit();
	}
	$qstrs = explode("`",$qstr);
	$dsql = new DedeSql(false);
	foreach($qstrs as $aid){
	  $aid = ereg_replace("[^0-9]","",$aid);
	  if($aid=="") continue;
	  $dsql->SetQuery("Update #@__archives set iscommend='11' where ID='$aid'");
	  $dsql->ExecuteNoneQuery();
	}
	$dsql->Close();
	ShowMsg("成功把所选的文档设为推荐！",$ENV_GOBACK_URL);
	exit();
}
/*--------------------------
//生成HTML
function makeArchives();
---------------------------*/
else if($dopost=="makeArchives")
{
	CheckPurview('sys_MakeHtml,sys_ArcBatch');
	$aid = ereg_replace("[^0-9]","",$aid);
	require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
	if(empty($qstr)){
	  $pageurl = MakeArt($aid,true);
	  ShowMsg("成功更新{$pageurl}...",$ENV_GOBACK_URL);
	  exit();
  }
  else{
  	$qstrs = explode("`",$qstr);
  	$i = 0;
  	foreach($qstrs as $aid){
  		$i++;
  		$pageurl = MakeArt($aid,true);
  	}
  	ShowMsg("成功更新指定 $i 个文件...",$ENV_GOBACK_URL);
  	exit();
  }
}
/*--------------------------
//审核文档
function checkArchives();
---------------------------*/
else if($dopost=="checkArchives")
{
	CheckPurview('a_Check,a_AccCheck,sys_ArcBatch');
	require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
	if( $aid!="" && !ereg("(".$aid."`|`".$aid.")",$qstr) ) $qstr .= "`".$aid;
	if($qstr==""){
	  ShowMsg("参数无效！",$ENV_GOBACK_URL);
	  exit();
	}
	$qstrs = explode("`",$qstr);
	foreach($qstrs as $aid)
	{
	  $aid = ereg_replace("[^0-9]","",$aid);
	  if($aid=="") continue;
	  $dsql = new DedeSql(false);
	  $dsql->SetQuery("Update #@__archives set arcrank='0',adminID='".$cuserLogin->getUserID()."' where ID='$aid' And arcrank<'0'");
	  $dsql->ExecuteNoneQuery();
	  $pageurl = MakeArt($aid,true);
	  $dsql->Close();
	}
	ShowMsg("成功审核指定的文档！",$ENV_GOBACK_URL);
	exit();
}
/*--------------------------
//删除文档
function delArchives();
---------------------------*/
else if($dopost=="delArchives")
{
	CheckPurview('a_Del,a_AccDel,a_MyDel,sys_ArcBatch');
	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
	if(empty($fmdo)) $fmdo = "";
	if($fmdo=="yes")
	{
	  if( $aid!="" && !ereg("(".$aid."`|`".$aid.")",$qstr) ) $qstr .= "`".$aid;
	  if($qstr==""){
	  	ShowMsg("参数无效！",$ENV_GOBACK_URL);
	  	exit();
	  }
	  $qstrs = explode("`",$qstr);
	  $okaids = Array();
	  $dsql = new DedeSql(false);
	  foreach($qstrs as $aid){
	    if(!isset($okaids[$aid])) DelArc($aid);
	    else $okaids[$aid] = 1;
    }
    $dsql->Close();
    ShowMsg("成功删除指定的文档！",$ENV_GOBACK_URL);
	  exit();
  }//确定刪除操作完成
  
  //删除确认消息
  //-----------------------
	$wintitle = "文档管理-删除文档";
	$wecome_info = "<a href='".$ENV_GOBACK_URL."'>文档管理</a>::删除文档";
	$win = new OxWindow();
	$win->Init("archives_do.php","js/blank.js","POST");
	$win->AddHidden("fmdo","yes");
	$win->AddHidden("dopost",$dopost);
	$win->AddHidden("qstr",$qstr);
	$win->AddHidden("aid",$aid);
	$win->AddTitle("你确实要删除“ $qstr 和 $aid ”这些文档？");
	$winform = $win->GetWindow("ok");
	$win->Display();
}
/*-----------------------------
function moveArchives()
------------------------------*/
else if($dopost=='moveArchives'){
	CheckPurview('sys_ArcBatch');
	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
	require_once(dirname(__FILE__)."/../include/inc_typelink.php");
	if(empty($targetTypeid)){
		$tl = new TypeLink($cid);
		$typeOptions = $tl->GetOptionArray(0,$cuserLogin->getUserChannel(),0);
		$tl->Close();
		$typeOptions = "
		<select name='targetTypeid' style='width:350'>
		<option value='0'>请选择移动到的位置...</option>\r\n
     $typeOptions
    </select>
    ";
		$wintitle = "文档管理-移动文档";
	  $wecome_info = "<a href='".$ENV_GOBACK_URL."'>文档管理</a>::移动文档";
	  $win = new OxWindow();
	  $win->Init("archives_do.php","js/blank.js","POST");
	  $win->AddHidden("fmdo","yes");
	  $win->AddHidden("dopost",$dopost);
	  $win->AddHidden("qstr",$qstr);
	  $win->AddHidden("aid",$aid);
	  $win->AddTitle("你目前的操作是移动文档，请选择目标栏目：");
	  $win->AddMsgItem($typeOptions,"30","1");
	  $win->AddMsgItem("你选中的文档ID是： $qstr <br>移动的栏目必须和选定的文档频道类型一致，否则程序会自动勿略不符合的文档。","30","1");
	  $winform = $win->GetWindow("ok");
	  $win->Display();
	}else{
		$targetTypeid = ereg_replace('[^0-9]','',$targetTypeid);
		$dsql = new DedeSql(false);
		$typeInfos = $dsql->GetOne("Select * From #@__arctype where ID='$targetTypeid' ");
		if(!is_array($typeInfos)){
			ShowMsg("参数错误！","-1");
			$dsql->Close();
			exit();
		}
		if($typeInfos['ispart']!=0){
			ShowMsg("文档保存的栏目必须为最终列表栏目！","-1");
			$dsql->Close();
			exit();
		}
		$arcids = explode('`',$qstr);
		$arc = "";
		$j = 0;
		$okids = Array();
		foreach($arcids as $arcid){
			$arcid = ereg_replace('[^0-9]','',$arcid);
			$arcrow = $dsql->GetOne("Select channel,typeid From #@__archives where ID='$arcid' ");
			if($arcrow['channel']==$typeInfos['channeltype'] && $arcrow['typeid']!=$targetTypeid){
				$dsql->ExecuteNoneQuery("Update #@__archives Set typeid='$targetTypeid' where ID='$arcid' ");
        $okids[] = $arcid;
        $j++;
		  }
		}
		//更新HTML
		foreach($okids as $aid){
			$arc = new Archives($aid);
      $arc->MakeHtml();
		}
		$dsql->Close();
		if(is_object($arc)) $arc->Close();
		ShowMsg("成功移动 $j 个文档！",$ENV_GOBACK_URL);
		//"content_list.php?cid=$targetTypeid"
		exit();
	}
}
?>