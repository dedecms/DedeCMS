<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('c_New');
if(empty($isdel1)) $isdel1 = 0;
if(empty($isdel2)) $isdel2 = 0;
//检查输入
//----------------------------
if(ereg("[^0-9-]",$ID)||$ID==""){
	ShowMsg("<font color=red>'频道ID'</font>必须为数字！","-1");
	exit();
}
if(eregi("[^a-z0-9_-]",$nid)||$nid==""){
	$nid = GetPinyin($typename);
}
if($addtable==""){
	ShowMsg("附加表不能为空！","-1");
	exit();
}
$dsql = new DedeSql(false);
$trueTable1 = str_replace("#@__",$cfg_dbprefix,$maintable);
$trueTable2 = str_replace("#@__",$cfg_dbprefix,$addtable);
//检查ID是否重复
//--------------------------
$row = $dsql->GetOne("Select * from #@__channeltype where ID='$ID' Or nid like '$nid' Or typename like '$typename' ");
if(is_array($row)){
	$dsql->Close();
	ShowMsg("可能‘频道ID’、‘频道名称/标识’在数据库已存在，不能重复使用！","-1");
	exit();
}
$mysql_version = $dsql->GetVersion();
$mysql_versions = explode(".",trim($mysql_version));
$mysql_version = $mysql_versions[0].".".$mysql_versions[1];
//复制并创建索引表
//--------------------
$istb = $dsql->IsTable($trueTable1);
if(!$istb || ($isdel1==1 && strtolower($trueTable1)!="{$cfg_dbprefix}archives") )
{
	$dsql->SetQuery("SHOW CREATE TABLE {$dsql->dbName}.#@__archives");
  $dsql->Execute();
  $row2 = $dsql->GetArray();
  $dftable = $row2[1];
	$dsql->ExecuteNoneQuery("DROP TABLE IF EXISTS `{$trueTable1}`;");
	$dftable = str_replace("{$cfg_dbprefix}archives",$trueTable1,$dftable);
	$rs = $dsql->ExecuteNoneQuery($dftable);
	if(!$rs){
		$dsql->Close();
		ShowMsg("创建主索引表副本失败!","-1");
		exit();
	}
}
//创建附加表
//--------------------
if($trueTable2!='')
{
  $istb = $dsql->IsTable($trueTable2);
  if(!$istb || $isdel2==1)
  {
	  $dsql->ExecuteNoneQuery("DROP TABLE IF EXISTS `{$trueTable2}`;");
	  $tabsql = "CREATE TABLE `$trueTable2`(
	           `aid` int(11) NOT NULL default '0',
             `typeid` int(11) NOT NULL default '0',
    ";
	  if($mysql_version < 4.1)
       $tabsql .= "    PRIMARY KEY  (`aid`), KEY `".$trueTable2."_index` (`typeid`)\r\n) TYPE=MyISAM; ";
    else
       $tabsql .= "    PRIMARY KEY  (`aid`), KEY `".$trueTable2."_index` (`typeid`)\r\n) ENGINE=MyISAM DEFAULT CHARSET=".$cfg_db_language."; ";
    $rs = $dsql->ExecuteNoneQuery($tabsql);
    if(!$rs){
		  $dsql->Close();
		  ShowMsg("创建附加表失败!","-1");
		  exit();
	  }
  }
}

$inQuery = "
INSERT INTO #@__channeltype(ID,nid,typename,maintable,addtable,
           addcon,mancon,editcon,useraddcon,usermancon,usereditcon,
           fieldset,listadd,issystem,issend,arcsta,sendrank,sendmember) 
VALUES ('$ID','$nid','$typename','$maintable','$addtable',
          '$addcon','$mancon','$editcon','$useraddcon','$usermancon','$usereditcon',
            '',     '',   '$issystem','$issend','$arcsta','$sendrank','$sendmember');
";

$rs = $dsql->ExecuteNoneQuery($inQuery);

ClearAllLink();

ShowMsg("成功增加一个频道模型！","mychannel_edit.php?ID={$ID}&dopost=edit");

exit();

?>