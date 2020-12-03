<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('a_Edit,a_AccEdit,a_MyEdit');
require_once(DEDEADMIN."/inc/inc_catalog_options.php");
require_once(DEDEADMIN."/../include/pub_dedetag.php");
require_once(DEDEADMIN."/inc/inc_archives_functions.php");
$aid = intval($aid);

$dsql = new DedeSql(false);

$tables = GetChannelTable($dsql,$aid,'arc');

//读取归档信息
//------------------------------
$arcQuery = "Select c.typename as channelname,ar.membername as rankname,a.* ,full.keywords as words
From `{$tables['maintable']}` a 
left join #@__channeltype c on c.ID=a.channel 
left join #@__arcrank ar on ar.rank=a.arcrank 
left join #@__full_search full on full.aid=a.ID 
where a.ID='$aid'";

$addQuery = "Select * From `{$tables['addtable']}` where aid='$aid'";

$arcRow = $dsql->GetOne($arcQuery);
$arcRow['keywords'] = $arcRow['words'];
if(!is_array($arcRow)){
	$dsql->Close();
	ShowMsg("读取档案基本信息出错!","javascript:;");
	exit();
}

$query = "Select * From #@__channeltype where ID='".$arcRow['channel']."'";
$cInfos = $dsql->GetOne($query);
if(!is_array($cInfos)){
	$dsql->Close();
	ShowMsg("读取频道配置信息出错!","javascript:;");
	exit();
}

$channelid = $arcRow['channel'];
$addtable = $cInfos['addtable'];

$addQuery = "Select * From ".$cInfos['addtable']." where aid='$aid'";
$addRow = $dsql->GetOne($addQuery);

$tags = GetTagFormLists($dsql,$aid);

$newRowStart = 1;
$nForm = "";
if($addRow["softlinks"]!="")
{
	$dtp = new DedeTagParse();
  $dtp->LoadSource($addRow["softlinks"]);
  if(is_array($dtp->CTags))
  {
    foreach($dtp->CTags as $ctag){
      if($ctag->GetName()=="link"){
      	$nForm .= "
      	软件地址".$newRowStart."：<input type='text' name='softurl".$newRowStart."' style='width:280px' value='".trim($ctag->GetInnerText())."'>
        服务器名称：<input type='text' name='servermsg".$newRowStart."' value='".$ctag->GetAtt("text")."' style='width:150px'>
        <br/>";
        $newRowStart++;
      }
    }
  }
  $dtp->Clear();
}

require_once(dirname(__FILE__)."/templets/soft_edit.htm");


ClearAllLink();

?>