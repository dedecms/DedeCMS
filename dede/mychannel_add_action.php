<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('c_New');
if(empty($ismake)) $ismake = 0;
//检查输入
//----------------------------
if(ereg("[^0-9]",$ID)||$ID==""){
	ShowMsg("<font color=red>'频道ID'</font>必须为数字！","-1");
	exit();
}
if(eregi("[^a-z]",$nid)||$nid==""){
	ShowMsg("<font color=red>'频道名字标识'</font>必须为英文字母！","-1");
	exit();
}
if($addtable==""){
	ShowMsg("附加表不能为空！","-1");
	exit();
}
$dsql = new DedeSql(false);
$trueTable = str_replace("#@__",$cfg_dbprefix,$addtable);
//检查ID是否重复
//--------------------------
$row = $dsql->GetOne("Select * from #@__channeltype where ID='$ID' Or nid like '$nid' Or addtable like '$addtable'");
if(is_array($row)){
	$dsql->Close();
	ShowMsg("可能‘频道ID’、‘频道名称标识’、‘附加表名称’在数据库已存在，不能重复使用！","-1");
	exit();
}
$mysql_version = $dsql->GetVersion();
$mysql_versions = explode(".",trim($mysql_version));
$mysql_version = $mysql_versions[0].".".$mysql_versions[1];
//检查附加表
//--------------------
if(!$dsql->IsTable($trueTable)){
   $tabsql = "CREATE TABLE IF NOT EXISTS  `$trueTable`(
	           `aid` int(11) NOT NULL default '0',
             `typeid` int(11) NOT NULL default '0',
  ";
	if($mysql_version < 4.1){
     $tabsql .= "    PRIMARY KEY  (`aid`), KEY `".$trueTable."_index` (`typeid`)\r\n) TYPE=MyISAM; ";
  }else{
     $tabsql .= "    PRIMARY KEY  (`aid`), KEY `".$trueTable."_index` (`typeid`)\r\n) ENGINE=MyISAM DEFAULT CHARSET=".$cfg_db_language."; ";
  }
  $dsql->ExecuteNoneQuery($tabsql);
}
$inQuery = "
INSERT INTO #@__channeltype(ID,nid,typename,addtable,addcon,mancon,editcon,fieldset,listadd,issystem,issend,arcsta,sendrank) 
VALUES ('$ID','$nid','$typename','$addtable','$addcon','$mancon','$editcon','','$listadd','$issystem','$issend','$arcsta','$sendrank');
";
$dsql->SetQuery($inQuery);
$dsql->ExecuteNoneQuery();
$dsql->Close();
ShowMsg("成功增加一个频道模型！","mychannel_main.php");
exit();
?>