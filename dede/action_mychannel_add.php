<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
if(empty($ismake)) $ismake = 0;
foreach($_POST as $k=>$v) ${$k} = trim(${$k});
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
if($addtable=="")
{
	ShowMsg("附加表不能为空！","-1");
	exit();
}
if($ismake==0 && $fieldset=="")
{
	ShowMsg("由于你没有手工创建表，心必须指定附加字段配置！","-1");
	exit();
}
$dsql = new DedeSql(false);
$trueTable = str_replace("#@__",$cfg_dbprefix,$addtable);
//检查ID是否重复
//--------------------------
$row = $dsql->GetOne("Select * from #@__channeltype where ID='$ID' Or nid like '$nid' Or addtable like '$addtable'");
if(is_array($row))
{
	$dsql->Close();
	ShowMsg("可能‘频道ID’、‘频道名称标识’、‘附加表名称’在数据库已存在，不能重复使用！","-1");
	exit();
}
//检查附加表和字段
//--------------------
if($ismake==1) //手工创建表的情况
{
		if(!$dsql->IsTable($trueTable))
		{
			$dsql->Close();
			ShowMsg("你所指定的选项为手工创建表，但经检测，没发现这个表！","-1");
			exit();
		}
		if($listadd=="")
		{
			$dsql->GetTableFields($trueTable);
			while($row=$dsql->GetFieldObject())
			{
				if($row->name=="aid" || $row->name=="typeid") continue;
				if($row->type != "blob")
				{
					if($listadd=="") $listadd = $row->name;
					else $listadd .= ",".$row->name;
				}
			}
			$listadd = addslashes($listadd);
		}
		if($fieldset=="")
		{
			$dsql->GetTableFields($trueTable);
			while($row=$dsql->GetFieldObject())
			{
				if($row->name=="aid" || $row->name=="typeid") continue;
				$fieldname = $row->name;
				if($row->numeric==1) $ftype = "int";
				if($row->blob==1) $ftype = "multitext";
				if($row->not_null==1) $isnull = "false";
				else $isnull = "true";
				$note = "<field:$fieldname itemname=\"$fieldname\" type=\"$ftype\" isnull=\"$isnull\" default=\"".$row->def."\" rename=\"\" function=\"\" maxlength=\"\" ></field:$fieldname>\r\n";
				$note = addslashes($note);
				$fieldset .= $note;
			}
		}
}
else //按配置生成表的情况
{
	$dtp = new DedeTagParse();
	$dtp->SetNameSpace("field","<",">");
  $dtp->LoadSource(stripslashes($fieldset));
  if(!is_array($dtp->CTags))
  {
  	$dsql->Close();
  	ShowMsg("配置参数无效！","-1");
  	exit();
  }
	if(!$dsql->IsTable($trueTable))
	{
		$tabsql = "CREATE TABLE `$trueTable` (
    `aid` int(11) NOT NULL default '0',
    `typeid` int(11) NOT NULL default '0',\n";
    $keysql = "";
    foreach($dtp->CTags as $tid=>$ctag)
    {
    	$fieldname = $ctag->GetName();
    	$dfvalue = $ctag->GetAtt('default');
    	$isnull = $ctag->GetAtt('isnull');
    	$dtype = $ctag->GetAtt('type');
    	$mxlen = $ctag->GetAtt('maxlength');
    	if($dtype=="int"||$dtype=="datetime")
    	{
    		if($dfvalue=="" || ereg("[^0-9]",$dfvalue)){ $dfvalue = 0; }
    		if($isnull=="true") $tabsql .= "    `$fieldname` int(11) default NULL,\n";
    		else $tabsql .= "    `$fieldname` int(11) NOT NULL default '$dfvalue',\n";
    		$keysql .= ",`$fieldname`";
    	}
    	else if($dtype=="float")
    	{
    		if($isnull=="true") $tabsql .= "    `$fieldname` float default NULL,\n";
    		else $tabsql .= "    `$fieldname` float NOT NULL default '$dfvalue',\n";
    		$keysql .= ",`$fieldname`";
    	}
    	else if($dtype=="img"||$dtype=="media"||$dtype=="addon")
    	{
    		if($mxlen=="") $mxlen = 200;
    		if($mxlen > 255) $mxlen = 50;
    		$tabsql .= "    `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue',\n";
    	}
    	else if($dtype=="multitext"||$dtype=="htmltext")
    	{
    		if($isnull=="true") $tabsql .= "    `$fieldname` text NOT NULL,\n";
    		else $tabsql .= "    `$fieldname` text,\n";
    	}
    	else
    	{
    		if($mxlen=="") $mxlen = 50;
    		if($mxlen > 255) $mxlen = 50;
    		$tabsql .= "    `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue',\n";
    		$keysql .= ",`$fieldname`";
    	}
    }
    $tabsql .= "    PRIMARY KEY  (`aid`),
    KEY `".$trueTable."_index` (`typeid`$keysql)\n); ";
    $dsql->SetQuery($tabsql);
    if(!$dsql->ExecuteNoneQuery())
    {
    	echo "运行： <font color='red'>".$tabsql."</font> 出错！";
    	$dsql->Close();
  	  ShowMsg("创建表失败，请检查你的配置参数！","-1",0,50000);
  	  exit();
    }
	}
	if($listadd=="")
	{
		foreach($dtp->CTags as $ctag)
    {
    	$fieldname = $ctag->GetName();
    	$dtype = $ctag->GetAtt('type');
    	if($dtype=="int"||$dtype=="datetime"||$dtype=="text")
    	{
    		if($listadd=="") $listadd = $fieldname;
    		else $listadd .= ",".$fieldname;
      }
    }
    $listadd = addslashes($listadd);
	}
}
$inQuery = "
INSERT INTO #@__channeltype(ID,nid,typename,addtable,addcon,mancon,editcon,fieldset,listadd,issystem) 
VALUES ('$ID','$nid','$typename','$addtable','$addcon','$mancon','$editcon','$fieldset','$listadd','$issystem');
";
$dsql->SetQuery($inQuery);
$dsql->ExecuteNoneQuery();
$dsql->Close();
ShowMsg("成功增加一个频道模型！","mychannel_main.php");
exit();
?>