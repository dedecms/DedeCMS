<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_admin_channel.php");
if(empty($action)) $action = '';
$dsql = new DedeSql(false);
$mysql_version = $dsql->GetVersion();
$mysql_versions = explode(".",trim($mysql_version));
$mysql_version = $mysql_versions[0].".".$mysql_versions[1];
/*----------------------
function Save()
---------------------*/
if($action=='save')
{
	//模型信息
  $row = $dsql->GetOne("Select fieldset,addtable,issystem From #@__channeltype where ID='$ID'");
  $fieldset = $row['fieldset'];
  require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
  $dtp = new DedeTagParse();
  $dtp->SetNameSpace("field","<",">");
  $dtp->LoadSource($fieldset);
	$trueTable = $row['addtable'];
	
	//修改字段配置信息
	$dfvalue = trim($vdefault);
	$isnull = ($isnull==1 ? "true" : "false");
	$mxlen = $maxlength;
	
	//检测被修改的字段类型
	$fieldinfos = GetFieldMake($dtype,$fieldname,$dfvalue,$mxlen);
	$ntabsql = $fieldinfos[0];
  $buideType = $fieldinfos[1];
  $rs = $dsql->ExecuteNoneQuery(" ALTER TABLE `$trueTable` ADD  $ntabsql ");
  
  if(!$rs){
  	$gerr = $dsql->GetError();
  	ClearAllLink();
    ShowMsg("增加字段失败，错误提示为：".$gerr,"javascript:;");
    exit();
  }
  
  $ok = false;
  $fieldname = strtolower($fieldname);
  //检测旧配置信息，并替换为新配置
  if(is_array($dtp->CTags))
  {
	  //遍历旧配置
	  foreach($dtp->CTags as $tagid=>$ctag)
	  {
		   if($fieldname == strtolower($ctag->GetName()))
		   {
         $dtp->Assign($tagid,stripslashes($fieldstring),false);
         $ok = true;
         break;
		   }
	  }
	  if($ok) $oksetting = $dtp->GetResultNP();
	  else $oksetting = $fieldset."\n".stripslashes($fieldstring);
  }
  //原来的配置为空
  else{
		$oksetting = $fieldset."\n".stripslashes($fieldstring);
  }
  $addlist = GetAddFieldList($dtp,$oksetting);
  $oksetting = addslashes($oksetting);
  $rs = $dsql->ExecuteNoneQuery("Update #@__channeltype set fieldset='$oksetting',listadd='$addlist' where ID='$ID' ");
  if(!$rs){
  	$grr = $dsql->GetError();
  	ClearAllLink();
    ShowMsg("保存节点配置出错！".$grr,"javascript:;");
    exit();
  }
  ClearAllLink();
  ShowMsg("成功增加一个字段！","mychannel_edit.php?ID={$ID}&dopost=edit");
  exit();
}
/*----------------------
function ShowPage()
---------------------*/
//检测模型相关信息，并初始化相关数据

$row = $dsql->GetOne("Select maintable,addtable From #@__channeltype where ID='$ID'");

$trueTable = $row['addtable'];
$tabsql = "CREATE TABLE IF NOT EXISTS  `$trueTable`( `aid` int(11) NOT NULL default '0',\r\n `typeid` int(11) NOT NULL default '0',\r\n ";
if($mysql_version < 4.1)
  $tabsql .= " PRIMARY KEY  (`aid`), KEY `".$trueTable."_index` (`typeid`)\r\n) TYPE=MyISAM; ";
else
  $tabsql .= " PRIMARY KEY  (`aid`), KEY `".$trueTable."_index` (`typeid`)\r\n) ENGINE=MyISAM DEFAULT CHARSET=".$cfg_db_language."; ";
$dsql->ExecuteNoneQuery($tabsql);
  
//检测附加表里含有的字段
$fields = array();
if(empty($row['maintable'])) $row['maintable'] = '#@__archives';
$rs = $dsql->SetQuery("show fields from `{$row['maintable']}`");
$dsql->Execute('a');
while($nrow = $dsql->GetArray('a',MYSQL_ASSOC)){
	$fields[strtolower($nrow['Field'])] = 1;
}
$rs = $dsql->SetQuery("show fields from `{$row['addtable']}`");
$dsql->Execute('a');
while($nrow = $dsql->GetArray('a',MYSQL_ASSOC)){
	if(!isset($fields[strtolower($nrow['Field'])])) $fields[strtolower($nrow['Field'])] = 1;
}

$f = '';
foreach($fields as $k=>$v){
	$f .= ($f=='' ? $k : ' '.$k);
}

require_once(dirname(__FILE__)."/templets/mychannel_field_add.htm");

ClearAllLink();
?>