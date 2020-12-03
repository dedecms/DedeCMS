<?php
@set_time_limit(0);
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_modules.php");
CheckPurview('sys_module');
if(empty($action)) $action = '';

if($action=='')
{
	$modules = array();
	require_once(dirname(__FILE__)."/templets/module_make.htm");
	exit();
}
/*---------
//获得Hash码
function GetHash()
----------*/
else if($action=='gethash')
{
	echo md5($modulname.$email);
	exit();
}
/*-------------
//生成项目
function Makemodule()
--------------*/
else if($action=='make')
{
	$filelist = str_replace("\r","\n",trim($filelist));
	$filelist = trim(ereg_replace("[\n]{1,}","\n",$filelist));
	if($filelist==""){
		ShowMsg("对不起，你没有指定模块的文件列表，因此不能创建项目！","-1");
		exit();
	}
	foreach($_POST as $k=>$v) $$k = stripslashes($v);
	$mdir = dirname(__FILE__).'/module';
	$hashcode = md5($modulname.$email);
	$moduleFilename = $mdir.'/'.$hashcode.'.dev';
	$dm = new DedeModule($mdir);
	if($dm->HasModule($hashcode))
	{
		$dm->Clear();
		ShowMsg("对不起，你指定同名模块已经存在，因此不能创建项目！<br>如果你要更新这个模块，请先删除：module/{$hashcode}.dev","-1");
		exit();
	}
	
	move_uploaded_file($readme,$mdir."/{$hashcode}-r.html") or die("你没上传，或系统无法把readme文件移动到 module 目录！");
	move_uploaded_file($setup,$mdir."/{$hashcode}-s.php") or die("你没上传，或系统无法把setup文件移动到 module 目录！");
	move_uploaded_file($uninstall,$mdir."/{$hashcode}-u.php") or die("你没上传，或系统无法把uninstall文件移动到 module 目录！");
	
	$readmef = $dm->GetEncodeFile($mdir."/{$hashcode}-r.html",true);
	$setupf = $dm->GetEncodeFile($mdir."/{$hashcode}-s.php",true);
	$uninstallf = $dm->GetEncodeFile($mdir."/{$hashcode}-u.php",true);
	$modulinfo = "<?xml version='1.0' encoding='utf-8' ?>
<module>
<baseinfo>
name={$modulname}
team={$team}
time={$mtime}
email={$email}
url={$url}
hash={$hashcode}
</baseinfo>
<systemfile>  
<readme>
{$readmef}
</readme>  
<setup>
{$setupf}
</setup>  
<uninstall>
{$uninstallf}
</uninstall>
<oldfilelist>
$filelist
</oldfilelist>
</systemfile>
";
$fp = fopen($moduleFilename,'w');
fwrite($fp,$modulinfo);
fwrite($fp,"<modulefiles>\r\n");
$filelists = explode("\n",$filelist);
foreach($filelists as $v)
{
  $v = trim($v);
  if(!empty($v)) $dm->MakeEncodeFile(dirname(__FILE__),$v,$fp);
}
fwrite($fp,"</modulefiles>\r\n");
fwrite($fp,"</module>\r\n");
fclose($fp);
ShowMsg("成功对一个新模块进行编译！","module_main.php");
exit();
}/*-------------
//修改项目
function editModule()
--------------*/
else if($action=='edit')
{
	$filelist = str_replace("\r","\n",trim($filelist));
	$filelist = trim(ereg_replace("[\n]{1,}","\n",$filelist));
	if($filelist==""){
		ShowMsg("对不起，你没有指定模块的文件列表，因此不能创建项目！","-1");
		exit();
	}
	foreach($_POST as $k=>$v) $$k = stripslashes($v);
	$mdir = dirname(__FILE__).'/module';
	$hashcode = $hash;
	$moduleFilename = $mdir.'/'.$hashcode.'.dev';
	$modulname = str_replace('=','',$modulname);
	$email = str_replace('=','',$email);
	$team = str_replace('=','',$team);
	
	$dm = new DedeModule($mdir);

	if(is_uploaded_file($readme)){
	  move_uploaded_file($readme,$mdir."/{$hashcode}-r.html") or die("你没上传，或系统无法把readme文件移动到 module 目录！");
	  $readmef = $dm->GetEncodeFile($mdir."/{$hashcode}-r.html",true);
  }else{
  	$readmef = base64_encode($dm->GetSystemFile($hashcode,'readme'));
  }
  if(is_uploaded_file($setup)){
	  move_uploaded_file($setup,$mdir."/{$hashcode}-s.php") or die("你没上传，或系统无法把setup文件移动到 module 目录！");
	  $setupf = $dm->GetEncodeFile($mdir."/{$hashcode}-s.php",true);
	}else{
		$setupf = base64_encode($dm->GetSystemFile($hashcode,'setup'));
	}
	if(is_uploaded_file($uninstall)){
		move_uploaded_file($uninstall,$mdir."/{$hashcode}-u.php") or die("你没上传，或系统无法把uninstall文件移动到 module 目录！");
    $uninstallf = $dm->GetEncodeFile($mdir."/{$hashcode}-u.php",true);
  }else{
  	$uninstallf = base64_encode($dm->GetSystemFile($hashcode,'uninstall'));
  }
	
	$modulinfo = "<?xml version='1.0' encoding='utf-8' ?>
<module>
<baseinfo>
name={$modulname}
team={$team}
time={$mtime}
email={$email}
url={$url}
hash={$hashcode}
</baseinfo>
<systemfile>  
<readme>
{$readmef}
</readme>  
<setup>
{$setupf}
</setup>  
<uninstall>
{$uninstallf}
</uninstall>
<oldfilelist>
$filelist
</oldfilelist>
</systemfile>
";
$fp = fopen($moduleFilename,'w');
fwrite($fp,$modulinfo);
fwrite($fp,"<modulefiles>\r\n");
$filelists = explode("\n",$filelist);
foreach($filelists as $v)
{
  $v = trim($v);
  if(!empty($v)) $dm->MakeEncodeFile(dirname(__FILE__),$v,$fp);
}
fwrite($fp,"</modulefiles>\r\n");
fwrite($fp,"</module>\r\n");
fclose($fp);
ShowMsg("成功对模块重新编译！","module_main.php");
exit();
}

ClearAllLink();
?>