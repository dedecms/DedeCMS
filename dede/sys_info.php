<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Edit');
if(empty($dopost)) $dopost = "";
$configfile = dirname(__FILE__)."/../include/config_hand.php";
$configfile_bak = dirname(__FILE__)."/../include/config_hand_bak.php";

header("Content-Type: text/html; charset={$cfg_ver_lang}");

if(!is_writeable($configfile)){
	echo "配置文件'{$configfile}'不支持写入，严禁修改系统配置参数！";
	exit();
}

if(empty($gp)) $gp = 1;

//保存更改
if($dopost=="save")
{
	$dsql = new DedeSql(false);
	foreach($_POST as $k=>$v){
	if(ereg("^edit___",$k)){
		$v = ${$k};

	}else continue;
		$k = ereg_replace("^edit___","",$k);
		if(strlen($v) > 250){
			showmsg("$k 太长，不能超过250字节",'-1');
			exit;
		}
		$dsql->ExecuteNoneQuery("Update #@__sysconfig set `value`='$v' where `varname`='$k' And `group`<>-1 ");
	}
	$dsql->SetQuery("Select `varname`,`value` From `#@__sysconfig` order by `aid` asc");
  $dsql->Execute();
  if($dsql->GetTotalRow()<=0){
		$dsql->Close();
		ShowMsg("成功保存变量但从数据库读取所有数据时失败，无法更新配置文件！","javascript:;");
	  exit();
	}
  @copy($configfile,$configfile_bak);
	$fp = @fopen($configfile,'w');
	@flock($fp,3);
	@fwrite($fp,"<"."?php\r\n") or die("配置文件'{$configfile}'不支持写入，本次操作无效！<a href='sys_info.php'>返回</a>");
  while($row = $dsql->GetArray()){
  	$row['value'] = str_replace("'","\\'",$row['value']);
  	fwrite($fp,"\${$row['varname']} = '".$row['value']."';\r\n");
  }
  fwrite($fp,"?>");
  fclose($fp);
	$dsql->Close();
	ShowMsg("成功更改站点配置！","sys_info.php");
	exit();
}


require_once(dirname(__FILE__)."/templets/sys_info.htm");

ClearAllLink();
?>