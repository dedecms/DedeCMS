<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/inc/inc_admin_channel.php");
if(empty($action)) $action = '';

//获取模型信息
$dsql = new DedeSql(false);
$mysql_version = $dsql->GetVersion();
$mysql_versions = explode(".",trim($mysql_version));
$mysql_version = $mysql_versions[0].".".$mysql_versions[1];
$row = $dsql->GetOne("Select fieldset,maintable,addtable,issystem From #@__channeltype where ID='$ID'");
$fieldset = $row['fieldset'];
$trueTable = $row['addtable'];
$dtp = new DedeTagParse();
$dtp->SetNameSpace("field","<",">");
$dtp->LoadSource($fieldset);
foreach($dtp->CTags as $ctag){
	if(strtolower($ctag->GetName())==strtolower($fname)) break;
}

//字段类型信息
$ds = file(dirname(__FILE__)."/inc/fieldtype.txt");
foreach($ds as $d){
   $dds = explode(',',trim($d));
   $fieldtypes[$dds[0]] = $dds[1];
}

//保存更改
/*--------------------
function _SAVE()
----------------------*/
if($action=='save')
{
  
  if(!isset($fieldtypes[$dtype])){
  	ClearAllLink();
  	ShowMsg("你修改的是系统专用类型的数据，禁止操作！","-1");
  	exit();
  }
  
  //检测数据库是否存在附加表，不存在则新建一个
  $tabsql = "CREATE TABLE IF NOT EXISTS  `{$row['addtable']}`( `aid` int(11) NOT NULL default '0',\r\n `typeid` int(11) NOT NULL default '0',\r\n ";
  if($mysql_version < 4.1)
    $tabsql .= " PRIMARY KEY  (`aid`), KEY `".$trueTable."_index` (`typeid`)\r\n) TYPE=MyISAM; ";
  else
    $tabsql .= " PRIMARY KEY  (`aid`), KEY `".$trueTable."_index` (`typeid`)\r\n) ENGINE=MyISAM DEFAULT CHARSET=".$cfg_db_language."; ";
  $dsql->ExecuteNoneQuery($tabsql);
  
  //检测附加表里含有的字段
	$fields = array();
  $rs = $dsql->SetQuery("show fields from `{$row['addtable']}`");
  $dsql->Execute('a');
  
  while($nrow = $dsql->GetArray('a',MYSQL_ASSOC)){
	  $fields[ strtolower($nrow['Field']) ] = $nrow['Type'];
  }
	
	//修改字段配置信息
	$dfvalue = $vdefault;
	$isnull = ($isnull==1 ? "true" : "false");
	$mxlen = $maxlength;
	$fieldname = strtolower($fname);
	
	
	//检测被修改的字段类型，并更新数据表
	$fieldinfos = GetFieldMake($dtype,$fieldname,$dfvalue,$mxlen);
	$ntabsql = $fieldinfos[0];
  $buideType = $fieldinfos[1];
  
	$tabsql  = '';
	//检测旧数据类型，并替换为新配置
	foreach($dtp->CTags as $tagid=>$ctag)
	{
		if($fieldname==strtolower($ctag->GetName()))
		{
       if(isset($fields[$fieldname]) && $fields[$fieldname]!=$buideType){
     	   $tabsql = "ALTER TABLE `$trueTable` CHANGE `$fieldname` ".$ntabsql;
     	   $dsql->ExecuteNoneQuery($tabsql);
       }else if(!isset($fields[$fieldname])){
     	   $tabsql = "ALTER TABLE `$trueTable` ADD ".$ntabsql;
     	   $dsql->ExecuteNoneQuery($tabsql);
       }else{
     	   $tabsql = '';
       }
       $dtp->Assign($tagid,stripslashes($fieldstring),false);
       break;
		}
	}
	$oksetting = $dtp->GetResultNP();
	$addlist = GetAddFieldList($dtp,$oksetting);
  $oksetting = addslashes($oksetting);
  $dsql->ExecuteNoneQuery("Update #@__channeltype set fieldset='$oksetting',listadd='$addlist' where ID='$ID' ");
	ClearAllLink();
	ShowMsg("成功更改一个字段的配置！","mychannel_edit.php?ID={$ID}&dopost=edit");
	exit();
}
/*------------------
删除字段
function _DELETE()
-------------------*/
else if($action=="delete")
{
	if($row['issystem']==1){
		 ClearAllLink();
		 ShowMsg("对不起，系统模型的字段不允许删除！","-1");
		 exit();
	}
	//检测旧数据类型，并替换为新配置
	foreach($dtp->CTags as $tagid=>$ctag){
		if(strtolower($ctag->GetName())==strtolower($fname)){ $dtp->Assign($tagid,"@Has del {$fname}@"); }
	}
	$oksetting = addslashes($dtp->GetResultNP());
	$dsql->ExecuteNoneQuery("Update #@__channeltype set fieldset='$oksetting' where ID='$ID' ");
	$dsql->ExecuteNoneQuery("ALTER TABLE `$trueTable` DROP `$fname` ");
	ClearAllLink();
	ShowMsg("成功删除一个字段！","mychannel_edit.php?ID={$ID}&dopost=edit");
	exit();
}


require_once(dirname(__FILE__)."/templets/mychannel_field_edit.htm");

ClearAllLink();
?>