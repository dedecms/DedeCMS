<?php
//########################################################################
//DedeCms V5 分表存储版安装程序
//程序开发：织梦团队
//使用 UltraEdit 查看本系统的软件，可以达到最佳理解效果
//#########################################################################
/*------------------------
初始化系统环境
function _0_InitEnv()
------------------------*/
error_reporting(E_ALL & ~E_NOTICE);
define('DEDEROOT',substr(dirname(__FILE__),0,-8));
$cfg_needFilter = false;
$cfg_isMagic = ini_get("magic_quotes_gpc");
if(!$cfg_isMagic){
  require_once(DEDEROOT."/include/config_rglobals_magic.php");
}else{
	$cfg_registerGlobals = ini_get("register_globals");
	if(!$cfg_registerGlobals) require_once(DEDEROOT."/include/config_rglobals.php");
}
if(empty($step)) $step = 1;
require_once("./inc_install.php");
require_once("./inc_moduls.php");
$gototime = 2000;
/*------------------------
显示协议文件
function _1_ShowReadMe()
------------------------*/
if($step==1){
	if(file_exists('install.txt')) {
		echo '你已经安装过该系统，如果想重新安装，请先删除install目录的 install.txt 文件，然后再次运行该程序';
		exit;
	}
	include_once("./templets/s1.html");
	exit();
}
/*------------------------
测试环境要求
function _2_TestEnv()
------------------------*/
else if($step==2)
{
	$phpv = @phpversion();
	$sp_os = $_ENV["OS"];
	$sp_gd = @gdversion();
	$sp_server = $_SERVER["SERVER_SOFTWARE"];
	$sp_host = (empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_HOST"] : $_SERVER["REMOTE_ADDR"]);
	$sp_name = $_SERVER["SERVER_NAME"];
	$sp_max_execution_time = ini_get('max_execution_time');
	$sp_allow_reference = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
  $sp_allow_url_fopen = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
  $sp_safe_mode = (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');
  $sp_gd = ($sp_gd>0 ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
  $sp_mysql = (function_exists('mysql_connect') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');

  if($sp_mysql=='<font color=red>[×]Off</font>') $sp_mysql_err = true;
  else $sp_mysql_err = false;

  $sp_testdirs = array(
        '/',
        '/include',
        '/data',
        '/data/sessions',
        '/data/textdata',
        '/data/cache',
        '/data/cache/user',
        '/dede/inc',
        '/dede/module',
        '/dede/module/ziptmp',
        '/member/templets'
  );
	include_once("./templets/s2.html");
	exit();
}
/*------------------------
填写设置
function _3_WriteConfig()
------------------------*/
else if($step==3)
{
	if(!empty($_SERVER["REQUEST_URI"])){$scriptName = $_SERVER["REQUEST_URI"]; }
  else{ $scriptName = $_SERVER["PHP_SELF"]; }
  $cmspath = ereg_replace("/install/index\.php(.*)$","",$scriptName);
  if(empty($_SERVER['HTTP_HOST'])) $baseurl = "http://".$_SERVER['HTTP_HOST'];
  else $baseurl = "http://".$_SERVER['SERVER_NAME'];
  $rnd_cookieEncode = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(1000,9999).chr(mt_rand(ord('A'),ord('Z')));
	include_once("./templets/s3.html");
	exit();
}
/*------------------------
开始安装进程
function _5_StartInstall()
------------------------*/
else if($step==5)
{
  if(empty($setupsta)) $setupsta = 0;
  //初始化基本安装数据、创建表
  //----------------------------------
  if($setupsta==0)
  {
  	$setupinfo = '';
  	$gotourl = '';
  	$gototime = 2000;
  	if(eregi("[^\.0-9a-z@!_-]",$adminuser) || eregi("[^\.0-9a-z@!_-]",$adminpwd)){
  		echo GetBackAlert("管理员用户名或密码含有非法字符！");
  		exit();
  	}

  	//检测数据库权限
  	$conn = @mysql_connect($dbhost,$dbuser,$dbpwd);
  	if(!$conn){
  		echo GetBackAlert("数据库服务器或登录密码无效，\\n\\n无法连接数据库，请重新设定！");
  		exit();
  	}
  	$rs = mysql_select_db($dbname,$conn);
  	if(!$rs){
  		$rs = mysql_query(" CREATE DATABASE `$dbname`; ",$conn);
  		if(!$rs){
  		  $errstr = GetBackAlert("数据库 {$dbname} 不存在，也没权限创建新的数据库！");
  		  echo $errstr;
  		  exit();
  		}else{
  			$rs = mysql_select_db($dbname,$conn);
  			if(!$rs){
  		    $errstr = GetBackAlert("你对数据库 {$dbname} 没权限！");
  		    echo $errstr;
  		    exit();
  		  }
  		}
  	}

  	//读取配置模板，并替换真实配置
  	$errstr = GetBackAlert("读取配置 config_base.php 失败，请检查install/config_base.php是否可读取！");
  	$fp = fopen(DEDEROOT."/install/config_base.php","r") or die($errstr);
    $configstr1 = fread($fp,filesize(DEDEROOT."/install/config_base.php"));
    fclose($fp);
    $errstr = GetBackAlert("读取配置 config_hand.php 失败，请检查install/config_hand.php是否可读取！");
    $fp = fopen(DEDEROOT."/install/config_hand.php","r") or die($errstr);
    $configstr2 = fread($fp,filesize(DEDEROOT."/install/config_hand.php"));
    fclose($fp);

    $configstr1 = str_replace('~dbhost~',$dbhost,$configstr1);
    $configstr1 = str_replace('~dbname~',$dbname,$configstr1);
    $configstr1 = str_replace('~dbuser~',$dbuser,$configstr1);
    $configstr1 = str_replace('~dbpwd~',$dbpwd,$configstr1);
    $configstr1 = str_replace('~dbprefix~',$dbprefix,$configstr1);
    $configstr1 = str_replace('~db_language~',$db_language,$configstr1);

    $errstr = GetBackAlert("写入配置 include/config_base.php 失败，请检查 include文件夹 是否可读写！");
  	$fp = fopen(DEDEROOT."/include/config_base.php","w") or die($errstr);
  	fwrite($fp,$configstr1);
  	fclose($fp);

  	$indexurl = (empty($cmspath) ? '/' : $cmspath);
  	$configstr2 = str_replace('~cmspath~',$cmspath,$configstr2);
  	$configstr2 = str_replace('~cookiepwd~',$cookiepwd,$configstr2);
  	$configstr2 = str_replace('~webname~',$webname,$configstr2);
  	$configstr2 = str_replace('~weburl~',$weburl,$configstr2);
  	$configstr2 = str_replace('~indexurl~',$indexurl,$configstr2);
  	$configstr2 = str_replace('~adminmail~',$adminmail,$configstr2);

  	$fp = fopen(DEDEROOT."/include/config_hand.php","w") or die($errstr);
  	fwrite($fp,$configstr2);
  	fclose($fp);
  	$fp = fopen(DEDEROOT."/include/config_hand_bak.php","w");
  	fwrite($fp,$configstr2);
  	fclose($fp);

  	 //检测数据库信息并创建基本数据表
  	 mysql_query("SET NAMES '{$db_language}';",$conn);
     $rs = mysql_query("SELECT VERSION();",$conn);
     $row = mysql_fetch_array($rs);
     $mysql_version = $row[0];
     $mysql_versions = explode(".",trim($mysql_version));
     $mysql_version = $mysql_versions[0].".".$mysql_versions[1];

     $adminpwd = substr(md5($adminpwd),0,24);

     $admindatas = "
          INSERT INTO `#@__admin` VALUES (1, 10, '{$adminuser}', '{$adminpwd}', 'admin', '', '', '', '2008-01-18 20:35:28', '127.0.0.1');
          INSERT INTO `#@__sysconfig` VALUES (2, 'cfg_basehost', '站点根网址', '{$weburl}', 'string', 8);
          INSERT INTO `#@__sysconfig` VALUES (3, 'cfg_cmspath', 'DedeCms安装目录', '{$cmspath}', 'string', 8);
          INSERT INTO `#@__sysconfig` VALUES (5, 'cfg_cookie_encode', 'cookie加密码', '{$cookiepwd}', 'string', 8);
          INSERT INTO `#@__sysconfig` VALUES (4, 'cfg_indexurl', '网页主页链接', '{$indexurl}', 'string',8);
          INSERT INTO `#@__sysconfig` VALUES (1, 'cfg_webname', '网站名称', '{$webname}', 'string', 8);
          INSERT INTO `#@__sysconfig` VALUES (8, 'cfg_adminemail', '站长EMAIL', '{$adminmail}', 'string', 8);
     ";
     if($mysql_version < 4.1) $fp = fopen(DEDEROOT."/install/setup40.sql","r");
     else $fp = fopen(DEDEROOT."/install/setup41.sql","r");
    //创建数据表和写入数据
    $query = "";
    while(!feof($fp))
	  {
		   $line = trim(fgets($fp,1024));
		   if(ereg(";$",$line)){
			   $query .= $line;
			   $query = str_replace('#@__',$dbprefix,$query);
			   if($mysql_version < 4.1) mysql_query($query,$conn);
			   else{
			   	 mysql_query(str_replace('#~lang~#',$db_language,$query),$conn);
			   }

			   $query='';
		   }else if(!ereg("^(//|--)",$line)){
			   $query .= $line;
		   }
	  }
	  fclose($fp);

	  $sysquerys = explode(';',$admindatas);
	  foreach($sysquerys as $query){
	  	if(trim($query)!='') mysql_query(str_replace('#@__',$dbprefix,$query),$conn);
	  }

	  mysql_close($conn);
	  $setupmodules = '';
	  if(is_array($moduls)){
	  	foreach($moduls as $m){
	  		if(trim($m)!='') $setupmodules .= ($setupmodules=='' ? $m : ",$m" );
	  	}
	  }

	  $gotourl = 'index.php?step=5&setupsta=1&moduls='.$setupmodules;
	  $setupinfo = "
	    成功安装系统基本数据，现在开始安装必要的基本数据<br />
	    请稍等...<br />
	    如果系统太长时间没反应，请点击此<a href='{$gotourl}'>链接&gt;&gt;</a>
	  ";
  	include_once("./templets/s4.html");
	  exit();
  }
  //安装基本数据
  else if($setupsta==1)
  {
  	 if($moduls!=''){
  	 	 $gotourl = 'index.php?step=5&setupsta=2&moduls='.$moduls;
  	 	 $setupinfo = "
	        成功安装系统必要的数据，现在开始安装你所选择的模块<br />
	        请稍等...<br />
	        如果系统太长时间没反应，请点击此<a href='{$gotourl}'>链接&gt;&gt;</a>
	     ";
  	 }
  	 else{
  	 	  $gototime = 2000;
  	 	  $gotourl = '../dede';
  	 	  $setupinfo = "
	        已完成所有项目的安装<br />
	        现在转入管理员登录页面，请稍等...<br />
	        如果系统太长时间没反应，请点击此<a href='{$gotourl}'>链接&gt;&gt;</a>
	      ";
  	 }
  	 include_once(DEDEROOT."/include/config_base.php");
  	 $dsql = new DedeSql(false);
  	 $fp = fopen(DEDEROOT."/install/setup_data1.sql","r");
     //创建数据表和写入数据
     $query = "";
     while(!feof($fp))
	   {
		   $line = trim(fgets($fp,1024));
		   if(ereg(";$",$line)){
			   $query .= $line;
			   $dsql->ExecuteNoneQuery($query);
			   $query='';
		   }else if(!ereg("^(//|--)",$line)){
			   $query .= $line;
		   }
	   }
	   fclose($fp);
  	 $dsql->Close();
  	 include_once("./templets/s4.html");
	   exit();
  }
  //安装附加数据
  else if($setupsta==2)
  {
  	 	require_once(DEDEROOT."/include/config_base.php");
  	 	require_once(DEDEROOT."/include/inc_modules.php");
  	 	$modulss = explode(',',$moduls);
  	 	$modul = $modulss[0];
  	 	$moduls = '';
  	 	for($i=1;isset($modulss[$i]);$i++){
  	 	  $moduls .= ($moduls=='' ? $modulss[$i] : ','.$modulss[$i]);
  	 	}
  	 	$moduledir = DEDEROOT."/dede/module";
  	 	$modulefile = $moduledir.$_moduls[$modul][1];
  	 	$AdminBaseDir = DEDEROOT."/dede/";

  	 	$dm = new DedeModule($moduledir);
  	 	$hash = $_moduls[$modul][3];
	    $dm->WriteFiles($hash,2);
	    $filename = $dm->WriteSystemFile($hash,'setup');
	    $dm->WriteSystemFile($hash,'uninstall');
	    $dm->WriteSystemFile($hash,'readme');
	    $dm->Clear();

  	 	include_once("./moduls/".$_moduls[$modul][2]);

  	 	if($moduls=='')
  	  {
  	    fopen('install.txt', 'w');
  	  	fwrite($fp,'ok');
  	  	fclose($fp);
  	    $gototime = 2000;
  	    $gotourl = '../dede';
  	    $setupinfo = "
	        已完成所有项目的安装<br />
	        现在转入管理员登录页面，请稍等...<br />
	        如果系统太长时间没反应，请点击此<a href='{$gotourl}'>链接&gt;&gt;</a>
	      ";
  	    include_once("./templets/s4.html");
  	    exit();
  	  }else
  	  {
  	 	  $gototime = 2000;
  	    $gotourl = 'index.php?step=5&setupsta=2&moduls='.$moduls;
  	    $setupinfo = "
	        完成模块 `{$_moduls[$modul][0]}` 的安装<br />
	        现在准备安装下一模块，请稍等...<br />
	        如果系统太长时间没反应，请点击此<a href='{$gotourl}'>链接&gt;&gt;</a>
	      ";
  	 	  include_once("./templets/s4.html");
  	 	  exit();
  	 }
  }
	exit();
}
/*------------------------
检测数据库是否有效
function _10_TestDbPwd()
------------------------*/
else if($step==10)
{
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	header("Content-Type: text/html; charset=utf-8");
	$conn = @mysql_connect($dbhost,$dbuser,$dbpwd);
	if($conn)
	{
	  $rs = mysql_select_db($dbname,$conn);
	  if(!$rs)
	  {
		   $rs = mysql_query(" CREATE DATABASE `$dbname`; ",$conn);
		   if($rs){
		  	  mysql_query(" DROP DATABASE `$dbname`; ",$conn);
		  	  echo "<font color='green'>信息正确</font>";
		   }else{
		      echo "<font color='red'>数据库不存在，也没权限创建新的数据库！</font>";
		   }
	  }else{
		    echo "<font color='green'>信息正确</font>";
	  }
	}else{
		echo "<font color='red'>数据库连接失败！</font>";
	}
	@mysql_close($conn);
	exit();
}
?>