<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_Export');
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/../include/inc_channel_unit_functions.php");

header("Content-Type: text/html; charset={$cfg_ver_lang}");

if(!isset($channelid)) $channelid = 0;
if(!isset($typeid)) $typeid = 0;
if(!isset($pageno)) $pageno = 1;
if(!isset($startid)) $startid = 0;
if(!isset($endid)) $endid = 0;
if(!isset($makehtml)) $makehtml = 0;
if(!isset($onlytitle)) $onlytitle = 0;
if(!isset($smakeid)) $smakeid = 0;
$nid = intval($nid);
if($channelid>0 && $typeid==0){
	ShowMsg('请指定栏目ID！','javascript:;');
	exit();
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select ex.*,n.arcsource From `#@__conote` n left join `#@__co_exrule` ex on ex.aid=n.typeid where nid='$nid'");
if(!is_array($row)){
	ShowMsg('找不到导入规则，无法完成操作！','javascript:;');
	$dsql->Close();
	exit();
}
$channelid = $row['channelid'];
$etype = $row['etype'];
$arcsource = $row['arcsource'];
$senddate = time();

$typeinfos = $dsql->GetOne("Select * From `#@__arctype` where ID='$typeid'",MYSQL_ASSOC);
//分析规则，并生成临时的SQL语句
//-------------------------------------
$dtp = new DedeTagParse();
$dtp->LoadString($row['ruleset']);
$noteinfo = $dtp->GetTagByName('note');
$tablenames = explode(",",$noteinfo->GetAtt('tablename'));
$autofield = $noteinfo->GetAtt('autofield');
$synfield = $noteinfo->GetAtt('synfield');
$tablename1 = str_replace('#@__',$cfg_dbprefix,$tablenames[0]);
$tb1SqlKey = "Insert Into `$tablename1`($autofield";
$tb1SqlValue = " Values('@$autofield@'";
if(count($tablenames)>=2){
	$tablename2 = str_replace('#@__',$cfg_dbprefix,$tablenames[1]);
	$tb2SqlKey = "Insert Into `$tablename2`(";
  $tb2SqlValue = " Values(";
  if($synfield!=''){
		$tb2SqlKey .= $synfield;
		$tb2SqlValue .= "'@$synfield@'";
  }
}
else{
	$tablename2 = '';
	$tb2SqlKey = '';
	$tb2SqlValue = '';
}

$exKeys = Array();

foreach($dtp->CTags as $tagid => $ctag)
{
	if($ctag->GetName()=='field')
	{
	  $fieldname = $ctag->GetAtt('name');
	  $tbname = str_replace('#@__',$cfg_dbprefix,$ctag->GetAtt('intable'));
	  if($tbname==$tablename1){
	  	$tb1SqlKey .= ",$fieldname";
	  	if($ctag->GetAtt('source')!='value'){
	  		$tb1SqlValue .= ",'@#{$tbname}.{$fieldname}#@'";
	  	}else{
	  		$nvalue = str_replace('{tid}',$typeid,$ctag->GetInnerText());
	  		$nvalue = str_replace('{cid}',$channelid,$nvalue);
	  		$nvalue = str_replace('{rank}',$arcrank,$nvalue);
	  		$nvalue = str_replace('{admin}',$cuserLogin->getUserID(),$nvalue);
	  		$nvalue = str_replace('{senddate}',$senddate,$nvalue);
	  		$nvalue = str_replace('{source}',$arcsource,$nvalue);
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
	$isbreak = false;
	$title = '';
	$pubdate = 0;
	foreach($dtp->CTags as $ctag)
	{
		if($ctag->GetName()!="field") continue;
		$tvalue = str_replace('#@__',$cfg_dbprefix,$ctag->GetAtt("name"));
		if($pubdate==0 && eregi("\.pubdate$",$tvalue)){
			$pubdate = $ctag->GetInnerText();
			$pubdate = @intval($pubdate);
	  }
		if($title=='' && eregi("\.title$",$tvalue))
		{
				$title = $ctag->GetInnerText();
				$title = trim($title);
				if($title==''){
				   echo "程序阻止标题为空的内容导入<br/>";
				   $isbreak = true;
				   break;
				}
				//排除重复标题
				if($onlytitle==1)
				{
				    $title = addslashes($title);
				    $testrow = $dsql->GetOne("Select count(aid) as dd From `#@__full_search` where channelid='$channelid' And title like '$title'");
			      if($testrow['dd']>0){
				       echo "数据库已存在标题为: {$title} 的文档，程序阻止了此内容导入<br/>";
				       $isbreak = true;
				       break;
			      }
				}////排除重复标题
		}
		$truevalue = $ctag->GetInnerText();
		$tmpSql1 = str_replace('@#'.$tvalue.'#@',addslashes(trim($truevalue)),$tmpSql1);
		if($tablename2!=''){
    	$tmpSql2 = str_replace('@#'.$tvalue.'#@',addslashes(trim($truevalue)),$tmpSql2);
    }
	}
	if($isbreak) continue;
	if($pubdate==0) $pubdate = $senddate;
	$tmpSql1 = ereg_replace('@#(.*)#@','',$tmpSql1);
	$tmpSql2 = ereg_replace('@#(.*)#@','',$tmpSql2);
	
	//这里的规则仅针对当前系统，如果其它系统，需修改这里的逻辑
	$arcid = GetIndexKey($dsql,$typeid,$channelid);
	if($smakeid==0) $smakeid = $arcid;
	$fileurl = GetFileUrl($arcid,$typeid,$senddate,$title,1,0,$typeinfos['namerule'],$typeinfos['typedir'],0,true,$typeinfos['siteurl']);
	$dsql->ExecuteNoneQuery("Update `#@__full_search` set url='".addslashes($fileurl)."',uptime='$senddate',pubdate='$pubdate' where aid='$arcid' ");
	
	$tmpSql1 = str_replace("@$autofield@",$arcid,$tmpSql1);
	$rs = $dsql->ExecuteNoneQuery($tmpSql1);
	
	if($rs){
		if($tablename2!=""){
			  $tmpSql2 = str_replace("@$synfield@",$arcid,$tmpSql2);
			  $rs = $dsql->ExecuteNoneQuery($tmpSql2);
			  if(!$rs){
				  $dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcid' ");
				  $dsql->ExecuteNoneQuery("Delete From `$tablename1` where $autofield='$arcid'");
			  }else{
			  	$dsql->ExecuteNoneQuery("update `#@__courl` set isex=1 where aid='$aid'");
			  }
		  }
	}else
	{
		$dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$arcid' ");
		echo "错误记录：";
		echo "$tmpSql1 ";
	  echo $dsql->GetError()."<hr />";
	}
}
$dsql->Close();
//检测是否完成或后续操作
//---------------------------------
if($totalpage <= $pageno)
{
	if($channelid>0 && $makehtml==1){
		if($arcrank==0){
		   $mhtml = "makehtml_archives_action.php?typeid={$typeid}&startid={$smakeid}&endid=0&pagesize=20";
		   ShowMsg("完成数据导入，准备生成文档HTML...",$mhtml);
		   ClearAllLink();
		   exit();
		}else
		{
			ShowMsg("完成数据导入，因为你选择了把文档保存为草稿，因此无法生成HTML！","javascript:;");
		  ClearAllLink();
		  exit();
		}
	}else{
	   ShowMsg("完成所有数据导入！","javascript:;");
	   ClearAllLink();
	   exit();
  }
}
else
{
	if($totalpage>0) $rs = substr(($pageno/$totalpage * 100),0,2);
	else $rs = "100";
	$pageno++;
	$gourl = "co_export_action.php?nid=$nid&smakeid={$smakeid}&totalcc=$totalcc&channelid=$channelid&pageno=$pageno";
	$gourl .= "&ruleid=$ruleid&typeid=$typeid&arcrank=$arcrank&pagesize=$pagesize";
	$gourl .= "&startid=$startid&endid=$endid&onlytitle=$onlytitle&makehtml=$makehtml";
	ShowMsg("完成 {$rs}% 导入，继续执行操作...",$gourl,"",100);
	ClearAllLink();
	exit();
}
?>