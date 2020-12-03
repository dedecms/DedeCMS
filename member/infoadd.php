<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/com_config.php");
$step = (empty($step) ? '' : $step);
if($step != 2)
{
	$dsql=new DedeSql(false);
	if($cfg_ml->M_ID && $cfg_ml->M_utype == 1){
		$cominfo = $dsql->GetOne("select m.*, mc.* from #@__member m left join #@__member_cominfo mc on mc.id=m.ID where m.ID='{$cfg_ml->M_ID}' ");
	}
	if(!isset($typeid)) {
		$typeid = $cid;
	}
	$typeid = intval($typeid);
	if($typeid <1) {
		ShowMsg("请先选择栏目!","-1");
		exit();
	}
	require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");

	$arcQuery = "Select t.typename,t.smalltypes,t.channeltype,c.fieldset From `#@__arctype` t left join #@__channeltype c on c.ID=t.channeltype where t.ID='$typeid' ";
	$typeinfo = $dsql->GetOne($arcQuery);
	$channelid = $typeinfo['channeltype'];
	$smalltypes = '';
	if($typeinfo['smalltypes'] != '')
	{
		$sql = "select * from #@__smalltypes where id in($typeinfo[smalltypes]);";
		$dsql->SetQuery($sql);
		$dsql->Execute();
		while($smalltype = $dsql->GetArray()){
			$smalltypes .= '<option value="'.$smalltype['id'].'">'.$smalltype['name']."</option>\n";
		}
	}
	//////////////////////地区数据处理s/////////////////////////////
		$sql = "select * from #@__area";
		$dsql->SetQuery($sql);
		$dsql->Execute();
		$toparea = $subarea = array();
		while($sector = $dsql->GetArray())
		{
			if($sector['reid'] == 0)
			{
				$toparea[] = $sector;
			}else
			{
				$subarea[] = $sector;
			}
		}
		$areacache = "toparea=new Array();\n\n";
		$areaidname = $areaid2name = '-不限-';
		foreach($toparea as $topkey => $topsector)
		{
			$areacache .= "toparea[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
			$areacache .= "\t".'subareas'.$topsector['id'].'=new Array();'."\n";
			$arrCount = 0;
			foreach($subarea as $subkey => $subsector)
			{
				if($subsector['reid'] == $topsector['id'])
				{
					$areacache .= "\t".'subareas'.$topsector['id'].'['.$arrCount.']="'.$subsector['id'].'~'.$subsector['name'].'";'."\n";
					$arrCount++;
				}

			}
		}
		//echo $areacache;exit;
	//////////////////////地区数据处理e/////////////////////////////

	//////////////////////行业数据处理s/////////////////////////////
	$sql = "select * from #@__sectors";
	$dsql->SetQuery($sql);
	$dsql->Execute();
	$topsectors = $subsectors = array();
	while($sector = $dsql->GetArray())
	{
		if($sector['reid'] == 0)
		{
			$topsectors[] = $sector;
		}else
		{
			$subsectors[] = $sector;
		}
	}
	$sectorcache = "topsectors=new Array();\n\n";
	$sectoridname = $sectorid2name = '--不限--';
	foreach($topsectors as $topkey => $topsector)
	{
		$sectorcache .= "topsectors[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
		$sectorcache .= "\t".'subsectors'.$topsector['id'].'=new Array();'."\n";
		$arrCount = 0;
		foreach($subsectors as $subkey => $subsector)
		{
			if($subsector['reid'] == $topsector['id'])
			{
				$sectorcache .= "\t".'subsectors'.$topsector['id'].'['.$arrCount.']="'.$subsector['id'].'~'.$subsector['name'].'";'."\n";
				$arrCount++;
			}

		}
	}
	//echo $sectorcache;exit;
//////////////////////行业数据处理e/////////////////////////////

	require_once(dirname(__FILE__)."/templets/infoadd.htm");
	$dsql->Close();
	exit();
}
/*----------------
function __Save();
----------------*/
else
{

	$cfg_main_dftable = '#@__infos';
	$cfg_add_dftable = '#@__addoninfos';
	require_once(dirname(__FILE__)."/archives_addcheck.php");

  if(!isset($smalltypeid)) $smalltypeid = 0;
	if(!isset($areaid)) $areaid = 0;
	if(!isset($areaid2)) $areaid2 = 0;
	if(!isset($sectorid)) $sectorid = 0;
	if(!isset($sectorid2)) $sectorid2 = 0;
  $upscore = $cfg_send_score;
	$ismake = -1;
	$arcrank = -1;

	//对保存的内容进行处理
	//--------------------------------

	$senddate = $pubdate = mytime();
	$endtime = (empty($endtime) ? 0 : intval($endtime));
	if($endtime==0) $endtime = 15;
	$endtime = $senddate + 3600 * 24 * $endtime;
	$pubdate = GetMkTime($pubdate);
	$sortrank = AddDay($senddate,0);
	$typeid2 = 0;
	$iscommend = 0;
	$title = ClearHtml($title);
	$title = cn_substr($title,80);
	$adminID = 0;
	if($keywords!=''){
		$keywords = ereg_replace("[,;]"," ",trim(ClearHtml($keywords)));
		$keywords = trim(cn_substr($keywords,60))." ";
	}
	$userip = GetIP();
  if($description=='') $description = cn_substr(trim(ClearHtml($body)),250);
	//处理上传的缩略图
	if(!empty($litpic)) $litpic = GetUpImage('litpic',true,true);
	else $litpic = "";
	$adminID = 0;
	$memberID = $cfg_ml->M_ID;
	$username = $cfg_ml->M_LoginID;

	$inQuery = "insert into `{$maintable}` (`ID`, `typeid`, `smalltypeid`, `areaid`,
		`areaid2`, `sectorid`,`sectorid2`, `endtime`, `typeid2`, sortrank,
		iscommend, ismake, channel, `title`, click, money, shorttitle,
		litpic, pubdate, senddate, arcatt, adminID, memberID,writer,
		description, keywords, arcrank, color,  source, templet, redirecturl, likeid,userip)
		values('$arcID','$typeid', '$smalltypeid', '$areaid',
		'$areaid2', '$sectorid', '$sectorid2', '$endtime', '0', '$sortrank',
		'0', '-1', '-2', '$title', '0', '0', '',
		'$litpic', '$pubdate', '$senddate', '0', '0', '$memberID','$username',
		'$description', '$keywords', '$arcrank', '', '', '', '', '', '$userip')";

	if(!$dsql->ExecuteNoneQuery($inQuery)){
		$gerr = $dsql->GetError();
	  $dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcID'");
	  $dsql->Close();
	  ShowMsg("把数据保存到数据库 `$maintable` 时出错，请联系管理员！".$gerr,"-1");
		exit();
	}

	$infoid = $arcID;

	$sql = "insert into `{$addtable}`(aid, typeid, message, contact, phone, fax, email, qq, msn, address{$inadd_f})
		values('$infoid', '$typeid', '$body', '$contact', '$phone', '$fax', '$email', '$qq', '$msn', '$address'{$inadd_v})";

	if(!$dsql->ExecuteNoneQuery($sql)){
		$gerr = $dsql->GetError();
	  $dsql->ExecuteNoneQuery("Delete From `$maintable` where ID='$arcID'");
	  $dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcID'");
	  $dsql->Close();
	  ShowMsg("把数据保存到附加表时出错，请联系管理员！".$gerr,"-1");
	  exit();
	}


	$dsql->ExecuteNoneQuery("Update `#@__member` set c3=c3+1,scores=scores+{$upscore} where ID='".$cfg_ml->M_ID."';");
  $cfg_ml->FushCache();

  $artUrl = $cfg_cmspath."/plus/view.php?aid=".$infoid;

  //更新全站搜索索引
  $datas = array('aid'=>$arcID,'typeid'=>$typeid,'channelid'=>$channelid,'adminid'=>0,'mid'=>$memberID,'att'=>0,
               'title'=>$title,'url'=>$artUrl,'litpic'=>$litpic,'keywords'=>$keywords,
               'addinfos'=>$description,'uptime'=>$senddate,'arcrank'=>$arcrank);
  WriteSearchIndex($dsql,$datas);
  //写入Tag索引
  InsertTags($dsql,$keywords,$arcID,$memberID,$typeid,$arcrank);
  unset($datas);
  $dsql->Close();

	$msg = "请选择你的后续操作：
	<a href='do.php?action=add&channelid=-2'><u>发布新信息</u></a>
	&nbsp;&nbsp;
	<a href='../plus/view.php?aid=".$infoid."&tid=$typeid'><u>预览信息</u></a>
	&nbsp;&nbsp;
	<a href='do.php?aid=".$infoid."&action=edit&typeid=$typeid&channelid=$channelid'><u>更改信息</u></a>
	&nbsp;&nbsp;
	<a href='do.php?typeid=$typeid&action=list&channelid=$channelid'><u>管理信息</u></a>
	";
	$wintitle = "成功更改信息！";
	$wecome_info = "信息管理::更改信息";
	$win = new OxWindow();
	$win->AddTitle("成功更改信息：");
	$win->AddMsgItem($msg);
	$winform = $win->GetWindow("hand","&nbsp;",false);
	$win->Display();
}
?>
