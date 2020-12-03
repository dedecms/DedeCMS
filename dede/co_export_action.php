<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_Export');
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
if(!isset($channelid)) $channelid = 0;
if(!isset($typeid)) $typeid = 0;
if(!isset($pageno)) $pageno = 1;
if(!isset($startid)) $startid = 0;
if(!isset($endid)) $endid = 0;
if(!isset($makehtml)) $makehtml = 0;
if(!isset($onlytitle)) $onlytitle = 0;
if($channelid>0 && $typeid==0){
	ShowMsg('请指定栏目ID！','javascript:;');
	exit();
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__co_exrule where aid='$ruleid'");
if(!is_array($row)){
	echo "找不到导入规则，无法完成操作！";
	$dsql->Close();
	exit();
}
//分析规则，并生成临时的SQL语句
//-------------------------------------
$dtp = new DedeTagParse();
$dtp->LoadString($row['ruleset']);
$noteinfo = $dtp->GetTagByName('note');
$tablenames = explode(",",$noteinfo->GetAtt('tablename'));
$autofield = $noteinfo->GetAtt('autofield');
$synfield = $noteinfo->GetAtt('synfield');
$tablename1 = $tablenames[0];
$tb1SqlKey = "Insert Into $tablename1(";
$tb1SqlValue = " Values(";
if(count($tablenames)>=2){
	$tablename2 = $tablenames[1];
	$tb2SqlKey = "Insert Into $tablename2(";
  $tb2SqlValue = " Values(";
  if($synfield!=''){
		$tb2SqlKey .= $synfield;
		$tb2SqlValue .= "'@$synfield@'";
  }
}
else{
	$tablename2 = "";
	$tb2SqlKey = "";
	$tb2SqlValue = "";
}

$exKeys = Array();

foreach($dtp->CTags as $tagid => $ctag)
{
	if($ctag->GetName()=='field')
	{
	  $fieldname = $ctag->GetAtt('name');
	  $tbname = $ctag->GetAtt('intable');
	  if($tbname==$tablename1){
	  	$tb1SqlKey .= ",$fieldname";
	  	if($ctag->GetAtt('source')!='value'){
	  		$tb1SqlValue .= ",'@#{$tbname}.{$fieldname}#@'";
	  	}else{
	  		$nvalue = str_replace('{tid}',$typeid,$ctag->GetInnerText());
	  		$nvalue = str_replace('{cid}',$channelid,$nvalue);
	  		$nvalue = str_replace('{rank}',$arcrank,$nvalue);
	  		$nvalue = str_replace('{admin}',$cuserLogin->getUserID(),$nvalue);
	  		$tb1SqlValue .= ",'$nvalue'";
	  	}
	  }
	  else if($tbname==$tablename2){
	  	$tb2SqlKey .= ",$fieldname";
	  	if($ctag->GetAtt('source')!='value'){
	  		$tb2SqlValue .= ",'@#{$tbname}.{$fieldname}#@'";
	  	}else{
	  		$nvalue = str_replace('{tid}',$typeid,$ctag->GetInnerText());
	  		$nvalue = str_replace('{cid}',$channelid,$nvalue);
	  		$nvalue = str_replace('{rank}',$arcrank,$nvalue);
	  		$tb2SqlValue .= ",'$nvalue'";
	  	} 
	  }
  }
}
$tb1SqlKey = str_replace('(,','(',$tb1SqlKey).")";
$tb1SqlValue = str_replace('(,','(',$tb1SqlValue).");";
$tb1Sql = $tb1SqlKey.$tb1SqlValue;
if($tablename2!="")
{
	$tb2SqlKey = str_replace("(,","(",$tb2SqlKey).")";
  $tb2SqlValue = str_replace("(,","(",$tb2SqlValue).");";
  $tb2Sql = $tb2SqlKey.$tb2SqlValue;
}
//导出数据的SQL操作
//---------------------------------
$totalpage = $totalcc/$pagesize;
$startdd = ($pageno-1) * $pagesize;
$dsql->SetQuery("Select * From #@__courl where nid='$nid' order by aid asc limit $startdd,$pagesize");
$dsql->Execute();
while($row = $dsql->GetObject())
{
	$tmpSql1 = $tb1Sql;
	$tmpSql2 = $tb2Sql;
	$dtp->LoadString($row->result);
	$aid = $row->aid;
	if(!is_array($dtp->CTags)){ continue; }
	if($onlytitle){
		$titletag = '';
		foreach ($dtp->CTags as $ctag){
			 $tvalue = $ctag->GetAtt("name");
			 if($tvalue == '#@__archives.title' || $tvalue == $cfg_dbprefix.'archives.title'){
			   	$titletag = $ctag;
			   	break;
			 }
	  }
		if(is_object($titletag)){
			$title = trim(addslashes($titletag->GetInnerText()));
			$testrow = $dsql->GetOne("Select count(ID) as dd From #@__archives where title like '$title'");
			if($testrow['dd']>0){
				echo "数据库已存在标题为: {$title} 的文档，程序阻止了此内容导入<br/>";
				continue;
			}
		}
	}
	foreach($dtp->CTags as $ctag)
	{
		if($ctag->GetName()!="field") continue;
		$tvalue = $ctag->GetAtt("name");
		$tmpSql1 = str_replace('@#'.$tvalue.'#@',addslashes($ctag->GetInnerText()),$tmpSql1);
		if($tablename2!=""){
    	$tmpSql2 = str_replace('@#'.$tvalue.'#@',addslashes($ctag->GetInnerText()),$tmpSql2);
    }
	}
	$tmpSql1 = ereg_replace('@#(.*)#@','',$tmpSql1);
	$rs = $dsql->ExecuteNoneQuery($tmpSql1);
	//echo "$tmpSql1";
	//echo $dsql->GetError();
	//exit();
	if($rs && $tablename2!=""){
		if($synfield!=""){
			$lid = $dsql->GetLastID();
			$tmpSql2 = str_replace("@$synfield@",$lid,$tmpSql2);
			$rs = $dsql->ExecuteNoneQuery($tmpSql2);
			if(!$rs) $dsql->ExecuteNoneQuery("Delete From $tablename1 where $autofield='$lid'");
		}
		else $dsql->ExecuteNoneQuery($tmpSql2);
	}
	$dsql->ExecuteNoneQuery("update #@__courl set isex=1 where aid='$aid'");
}
$dsql->Close();
//检测是否完成或后续操作
//---------------------------------
if($totalpage <= $pageno){
	if($channelid>0 && $makehtml==1){
		 $mhtml = "makehtml_archives_action.php?typeid=$typeid&startid=$startid&endid=$endid&pagesize=20";
		 ShowMsg("完成数据导入，准备生成文档HTML...",$mhtml);
		 exit();
	}else{
	   echo "完成所有数据导入！";
  }
}
else{
	if($totalpage>0) $rs = substr(($pageno/$totalpage * 100),0,2);
	else $rs = "100";
	$pageno++;
	$gourl = "co_export_action.php?nid=$nid&totalcc=$totalcc&channelid=$channelid&pageno=$pageno";
	$gourl .= "&ruleid=$ruleid&typeid=$typeid&arcrank=$arcrank&pagesize=$pagesize";
	$gourl .= "&startid=$startid&endid=$endid&onlytitle=$onlytitle&makehtml=$makehtml";
	ShowMsg("完成 {$rs}% 导入，继续执行操作...",$gourl,"",500);
	exit();
}
?>