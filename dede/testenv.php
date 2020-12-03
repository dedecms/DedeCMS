<?php 
header("Content-Type: text/html; charset=utf-8");
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Edit');
if(empty($action)) $action = "";
$needDir = "$cfg_templets_dir|
$cfg_templets_dir/system|
$cfg_templets_dir/plus|
$cfg_templets_dir/default|
$cfg_plus_dir|
$cfg_plus_dir/js|
$cfg_plus_dir/rss|
$cfg_plus_dir/cache|
$cfg_medias_dir|
$cfg_image_dir|
$ddcfg_image_dir|
$cfg_user_dir|
$cfg_soft_dir|
$cfg_other_medias|
$cfg_cmspath/include|
$cfg_cmspath/freelist|
$cfg_cmspath/data/textdata|
$cfg_cmspath/data/sessions|
$cfg_cmspath/data/cache|
$cfg_cmspath/data/cache/user|
$cfg_special|
$cfg_member_dir/templets|
$cfg_cmspath$cfg_arcdir";

header("Content-Type: text/html; charset={$cfg_ver_lang}");

if(($cfg_isSafeMode || $cfg_ftp_mkdir=='Y') && $cfg_ftp_host==""){
	echo "由于你的站点的PHP配置存在限制，程序只能通过FTP形式进行目录操作，因此你必须在后台指定FTP相关的变量！<br>";
	echo "<a href='sys_info.php'>&lt;&lt;修改系统参数&gt;&gt;</a>";
	exit();
}
if($action==""){
	echo "本程序将检测下列目录是否存在，或者是否具有写入的权限，并尝试创建或更改：<br>";
	echo "（如果你的主机使用的是windows系统，你无需进行此操作）<br>";
	echo "'/include' 目录和 '当前目录/templets' 文件夹请你在FTP中手工更改权限为可写入(0755或0777)<br>";
	echo "<pre>".str_replace('|','',$needDir)."</pre>";
	echo "<a href='testenv.php?action=ok'>&lt;&lt;开始检测&gt;&gt;</a> &nbsp; <a href='index_body.php'>&lt;&lt;返回主页&gt;&gt;</a>";
}else{
	$needDirs = explode('|',$needDir);
	$needDir = "";
	foreach($needDirs as $needDir){
		$needDir = trim($needDir);
		$needDir = str_replace("\\","/",$needDir);
		$needDir = ereg_replace("/{1,}","/",$needDir);
		if(CreateDir($needDir)) echo "成功更改或创建：{$needDir} <br>";
		else echo "更改或创建目录：{$needDir} <font color='red'>失败！</font> <br>";
	}
	echo "<br>如果发现更改或创建错误的项目，请<a href='testenv.php?action=ok&play=".time()."'><u>重试</u></a>或手动登陆到FTP更改相关目录的权限为777或666<br>";
	echo "<br><a href='index_body.php'>&lt;&lt;返回主页&gt;&gt;</a>";
	CloseFtp();
}

ClearAllLink();
?>