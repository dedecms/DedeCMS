<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/com_config.php");
$step = (empty($step) ? '' : $step);
if($step != 2)
{
	$aid = (empty($aid) ? 0 : intval($aid));
	if($aid==0)
	{
	  ShowMsg("你没指定文档ID，不允许访问本页面！","-1");
	  exit();
	}

  require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
  require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");

  $dsql = new DedeSql(false);

  $cInfos = $dsql->GetOne("Select c.* From `#@__full_search` a left join #@__channeltype c on c.ID=a.channelid where a.aid='$aid' And a.mid='{$cfg_ml->M_ID}' ",MYSQL_ASSOC);

  if(!is_array($cInfos)){
	  $dsql->Close();
	  ShowMsg("读取频道信息出错，可能指定的ID有问题！","-1");
	  exit();
  }

  if($cInfos['issend']!=1){
	  $dsql->Close();
	  ShowMsg("你指定的频道不允许投稿！","-1");
	  exit();
  }

  $maintable = ($cInfos['maintable']=='' ? '#@__infos' : $cInfos['maintable']);
  $addtable = ($cInfos['addtable']=='' ? '#@__addoninfos' : $cInfos['addtable']);

  //读取归档信息
  //------------------------------
  $arcQuery = "Select c.typename as channelname,t.typename,t.smalltypes,ar.membername as rankname,a.*
  From `$maintable` a
  left join `#@__channeltype` c on c.ID=a.channel
  left join `#@__arcrank` ar on ar.rank=a.arcrank
  left join `#@__arctype` t on t.ID=a.typeid
  where a.ID='$aid' and a.memberID='{$cfg_ml->M_ID}'";

  $info = $dsql->GetOne($arcQuery,MYSQL_ASSOC);
  if(!is_array($info)){
	  $dsql->Close();
	  ShowMsg("读取档案基本信息出错!","-1");
	  exit();
  }

  $channelid = $info['channel'];

  if($addtable!=''){
    $addQuery = "Select * From `{$addtable}` where aid='$aid'";
    $addRow = $dsql->GetOne($addQuery,MYSQL_ASSOC);
  }

  $arow['typename'] = $info['typename'];

	//文章信息处理
	$info['endtime'] = ($info['endtime']-$info['senddate'])/(3600 * 24);

	//小分类处理
	if($info['smalltypes'] != '')
	{
		$sql = "select * from #@__smalltypes where id in($info[smalltypes]);";
		$dsql->SetQuery($sql);
		$dsql->Execute();
		$smalltypes = '';
		while($smalltype = $dsql->GetArray())
		{
			$ifcheck ='';
			if($smalltype['id'] == $info['smalltypeid']){
				$ifcheck = 'selected';
			}
			$smalltypes .= '<option value="'.$smalltype['id'].'"'.$ifcheck.'>'.$smalltype['name']."</option>\n";
		}
	}

	//////////////////////地区数据处理s/////////////////////////////
	$dsql->SetQuery("select * from #@__area");
	$dsql->Execute();
	$toparea = $subarea = array();
	$topselarea['id'] = $info['areaid'];
	$topselarea['name'] = '--不限--';
	$subselarea['id'] = $info['areaid2'];
	$subselarea['name'] = '--不限--';
	while($sector = $dsql->GetArray())
	{
			if($sector['reid'] == 0){
				$toparea[] = $sector;
			}else{
				$subarea[] = $sector;
			}
			//------------------------------------
			if($sector['id']==$info['areaid']){
				$topselarea['name'] = $sector['name'];
			}
			if($sector['id']==$info['areaid2']){
				$subselarea['name'] = $sector['name'];
			}
	}
	$areacache = "toparea=new Array();\n\n";
	$areaidname = $areaid2name = '--不限--';
	foreach($toparea as $topkey => $topsector)
	{
			if($topsector['id'] == $info['areaid'])
			{
				$areaidname = $topsector['name'];
			}
			$areacache .= "toparea[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
			$areacache .= "\t".'subareas'.$topsector['id'].'=new Array();'."\n";
			$arrCount = 0;
			foreach($subarea as $subkey => $subsector)
			{
				if($subsector['id'] == $info['areaid2']){
					$areaid2name = $subsector['name'];
				}
				if($subsector['reid'] == $topsector['id']){
					$areacache .= "\t".'subareas'.$topsector['id'].'['.$arrCount.']="'.$subsector['id'].'~'.$subsector['name'].'";'."\n";
					$arrCount++;
				}

			}
	}
	//////////////////////地区数据处理e/////////////////////////////

	//////////////////////行业数据处理s/////////////////////////////
  $dsql->SetQuery("select * from #@__sectors");
  $dsql->Execute();
  $topselsector['id'] = $info['sectorid'];
	$topselsector['name'] = '--不限--';
	$subselsector['id'] = $info['sectorid2'];
	$subselsector['name'] = '--不限--';
  $topsectors = $subsectors = array();
  while($sector = $dsql->GetArray())
  {
	  if($sector['reid'] == 0){
		  $topsectors[] = $sector;
	  }else{
		  $subsectors[] = $sector;
	  }
	  //------------------------------------
		if($sector['id']==$info['sectorid']){
			$topselsector['name'] = $sector['name'];
		}
		if($sector['id']==$info['sectorid2']){
			$subselsector['name'] = $sector['name'];
		}
  }
  $sectorcache = "topsectors=new Array();\n\n";
  $sectoridname = $sectorid2name = '--不限--';
  foreach($topsectors as $topkey => $topsector)
  {
	  if($topsector['id'] == $info['sectorid'])
	  {
		  $sectoridname = $topsector['name'];
	  }
	  $sectorcache .= "topsectors[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
	  $sectorcache .= "\t".'subsectors'.$topsector['id'].'=new Array();'."\n";
	  foreach($subsectors as $subkey => $subsector)
	  {
		   if($subsector['id'] == $info['sectorid2']){
			    $sectorid2name = $subsector['name'];
		   }
		   if($subsector['reid'] == $topsector['id'])
		   {//B1[0]="101~东城区";
			    $sectorcache .= "\t".'subsectors'.$topsector['id'].'['.$subkey.']="'.$subsector['id'].'~'.$subsector['name'].'";'."\n";
		   }
	  }
  }
  //////////////////////行业数据处理e/////////////////////////////
	require_once(dirname(__FILE__)."/templets/infoedit.htm");
	$dsql->Close();
}
/*----------------
function __Save();
----------------*/
else
{
	$cfg_id_hudong = 1;
	$cfg_add_dftable = '#@__addoninfos';
  require_once(dirname(__FILE__)."/archives_editcheck.php");

	if(!isset($smalltypeid)) $smalltypeid = 0;
	if(!isset($areaid)) $areaid = 0;
	if(!isset($areaid2)) $areaid2 = 0;
	if(!isset($sectorid)) $sectorid = 0;
	if(!isset($sectorid2)) $sectorid2 = 0;

	//对保存的内容进行处理
	$pubdate = $senddate = mytime();
	$endtime = $senddate + 3600 * 24 * $endtime;
	$title = ClearHtml($title);
	$description = cn_substr(trim(ClearHtml($body)),250);
	$title = cn_substr($title,80);
	if($keywords!='') $keywords = trim(cn_substr($keywords,60))." ";
	//处理上传的缩略图
  if(!empty($litpic)){
	  $litpic = GetUpImage('litpic',true,true);
	  $litpicsql = " litpic='$litpic', ";
  }else{
	  $litpic = '';
	  $litpicsql = '';
  }
	$userip = GetIP();
	$body = eregi_replace("<(iframe|script)","",$body);
	$body = addslashes($body);

//----------------------------------
//分析处理附加表数据
//----------------------------------
$inadd_f = '';
if(!empty($dede_addonfields))
{
  $addonfields = explode(";",$dede_addonfields);
  $inadd_f = "";
  if(is_array($addonfields))
  {
    foreach($addonfields as $v)
    {
	     if($v=="") continue;
	     $vs = explode(",",$v);
	     //HTML文本特殊处理
	     if($vs[1]=="htmltext"||$vs[1]=="textdata")
	     {
		     ${$vs[0]} = filterscript(stripslashes(${$vs[0]}));
         //自动摘要
         if($description==''){
    	      $description = cn_substr(html2text(${$vs[0]}),$cfg_auot_description);
	          $description = trim(preg_replace("/#p#|#e#/","",$description));
	          $description = addslashes($description);
         }
         ${$vs[0]} = addslashes(${$vs[0]});
         ${$vs[0]} = GetFieldValue(${$vs[0]},$vs[1],$ID,'add','','member');
	     }else{
		     ${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$ID);
	     }
	     $inadd_f .= ",`{$vs[0]}` = '".${$vs[0]}."'";
    }
  }
}

	//更新数据库的SQL语句
	//----------------------------------
	$inQuery = "update `$maintable` set {$litpicsql}typeid='$typeid', smalltypeid='$smalltypeid',
	       areaid='$areaid', areaid2='$areaid2', sectorid='$sectorid', sectorid2='$sectorid2',
	       pubdate='$pubdate',senddate='$senddate', endtime='$endtime', title='$title', keywords='$keywords',
	      description='$description' where ID='{$ID}' and memberID='{$cfg_ml->M_ID}'
	";

	if(!$dsql->ExecuteNoneQuery($inQuery)){
		$gerr = $dsql->GetError();
	  $dsql->Close();
	  ShowMsg("把数据保存到数据库主表时出错，错误原因为：".$gerr,"javascript:;");
	  exit();
	}

	$addQuery = "update `{$addtable}` set typeid='$typeid', message='$body', contact='$contact', phone='$phone',
	fax='$fax', email='$email', qq='$qq', msn='$msn', address='$address'{$inadd_f} where aid='$ID'";

	if(!$dsql->ExecuteNoneQuery($addQuery)){
		$gerr = $dsql->GetError();
	    $dsql->Close();
	    ShowMsg("把数据保存到数据库附加时出错，错误原因为：".$gerr,"javascript:;");
	    exit();
	}

	//更新全站搜索索引
  $datas = array('aid'=>$ID,'typeid'=>$typeid,'channelid'=>$channelid,'att'=>0,
               'title'=>$title,'keywords'=>$keywords,'addinfos'=>$description);
  if($litpic != '') $datas['litpic'] = $litpic;
  UpSearchIndex($dsql,$datas);
  //更新Tag索引
  UpTags($dsql,$keywords,$ID,$cfg_ml->M_ID,$typeid,0);
  unset($datas);

	$dsql->Close();
	//返回成功信息
	$msg = "请选择你的后续操作：
	<a href='../plus/view.php?aid={$ID}&tid={$typeid}' target='_blank'><u>预览信息</u></a>
	&nbsp;&nbsp;
	<a href='do.php?aid={$ID}&action=edit&typeid={$typeid}&channelid={$channelid}'><u>修改信息</u></a>
	&nbsp;&nbsp;
	<a href='do.php?typeid={$typeid}&action=list&channelid={$channelid}'><u>管理旧信息</u></a>
	";
	$wintitle = "成功更改文章！";
	$wecome_info = "文章管理::更改文章";
	$win = new OxWindow();
	$win->AddTitle("成功更改文章：");
	$win->AddMsgItem($msg);
	$winform = $win->GetWindow("hand","&nbsp;",false);
	$win->Display();
}
?>


