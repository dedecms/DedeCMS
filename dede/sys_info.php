<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Edit');
if(empty($dopost))
{
	$dopost = "";
}
$configfile = DEDEDATA.'/config.cache.inc.php';

//更新配置函数
function ReWriteConfig()
{
	global $dsql,$configfile;
	if(!is_writeable($configfile))
	{
		echo "配置文件'{$configfile}'不支持写入，无法修改系统配置参数！";
		exit();
	}
	$fp = fopen($configfile,'w');
	flock($fp,3);
	fwrite($fp,"<"."?php\r\n");
	$dsql->SetQuery("Select `varname`,`type`,`value`,`groupid` From `#@__sysconfig` order by aid asc ");
	$dsql->Execute();
	while($row = $dsql->GetArray())
	{
		if($row['type']=='number')
		{
			if($row['value']=='') $row['value'] = 0;
			fwrite($fp,"\${$row['varname']} = ".$row['value'].";\r\n");
		}
		else
		{
			fwrite($fp,"\${$row['varname']} = '".str_replace("'",'',$row['value'])."';\r\n");
		}
	}
	fwrite($fp,"?".">");
	fclose($fp);
}

//保存配置的改动
if($dopost=="save")
{
	foreach($_POST as $k=>$v)
	{
		if(ereg("^edit___",$k))
		{
			$v = cn_substrR(${$k}, 1024);
		}
		else
		{
			continue;
		}
		$k = ereg_replace("^edit___","",$k);
		$dsql->ExecuteNoneQuery("Update `#@__sysconfig` set `value`='$v' where varname='$k' ");
	}
	ReWriteConfig();
	ShowMsg("成功更改站点配置！","sys_info.php");
	exit();
}

//增加新变量
else if($dopost=='add')
{
	if($vartype=='bool' && ($nvarvalue!='Y' && $nvarvalue!='N'))
	{
		ShowMsg("布尔变量值必须为'Y'或'N'!","-1");
		exit();
	}
	if(trim($nvarname)=='' || eregi('[^a-z_]', $nvarname) )
	{
		ShowMsg("变量名不能为空并且必须为[a-z_]组成!","-1");
		exit();
	}
	$row = $dsql->GetOne("Select varname From `#@__sysconfig` where varname like '$nvarname' ");
	if(is_array($row))
	{
		ShowMsg("该变量名称已经存在!","-1");
		exit();
	}
	$row = $dsql->GetOne("Select aid From `#@__sysconfig` order by aid desc ");
	$aid = $row['aid']+1;
	$inquery = "INSERT INTO `#@__sysconfig`(`aid`,`varname`,`info`,`value`,`type`,`groupid`)
    VALUES ('$aid','$nvarname','$varmsg','$nvarvalue','$vartype','$vargroup')";
	$rs = $dsql->ExecuteNoneQuery($inquery);
	if(!$rs)
	{
		ShowMsg("新增变量失败，可能有非法字符！","sys_info.php?gp=$vargroup");
		exit();
	}
	if(!is_writeable($configfile))
	{
		ShowMsg("成功保存变量，但由于 $configfile 无法写入，因此不能更新配置文件！","sys_info.php?gp=$vargroup");
		exit();
	}else
	{
		ReWriteConfig();
		ShowMsg("成功保存变量并更新配置文件！","sys_info.php?gp=$vargroup");
		exit();
	}
}
include DedeInclude('templets/sys_info.htm');

?>