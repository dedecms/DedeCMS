<?
//检测 Global 和 Magic 开关
$needFilter = false;
$registerGlobals = @ini_get("register_globals");
$isMagic = @ini_get("magic_quotes_gpc");
if(!$isMagic) require_once(dirname(__FILE__)."/../include/config_rglobals_magic.php");
else if(!$registerGlobals) require_once(dirname(__FILE__)."/../include/config_rglobals.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
//-----------------------------------------
if(empty($step)) $step = 1;
if($step==1){ //读取初始参数
  if(!empty($_SERVER["REQUEST_URI"])){$scriptName = $_SERVER["REQUEST_URI"]; }
  else{ $scriptName = $_SERVER["PHP_SELF"]; }
  $basepath = eregi_replace("/setup(.*)$","",$scriptName);
  if(empty($_SERVER['HTTP_HOST'])) $baseurl = "http://".$_SERVER['HTTP_HOST'];
  else $baseurl = "http://".$_SERVER['SERVER_NAME'];
  
  $rnd_cookieEncode = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(1000,9999).chr(mt_rand(ord('A'),ord('Z')));
  
}
else if($step==2){ //安装程序
  if(!isset($isnew)) $isnew = 0;
  $conn = mysql_connect($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd) or die("<script>alert('数据库服务器或登录密码无效，\\n\\n无法连接数据库，请重新设定！');history.go(-1);</script>");
  if($isnew==1){
  	mysql_query("CREATE DATABASE ".$cfg_dbname,$conn) or die("<script>alert('创建数据库失败，可能权限不足，请指定一个已创建好的数据库！');history.go(-1);</script>");
  }
  mysql_select_db($cfg_dbname) or die("<script>alert('数据库不存在，请重新设定！');history.go(-1);</script>");
  mysql_query("SET NAMES '$dblang';",$conn);
  $rs = mysql_query("SELECT VERSION();",$conn);
  $row = mysql_fetch_array($rs);
  $mysql_version = $row[0];
  $mysql_versions = explode(".",trim($mysql_version));
  $mysql_version = $mysql_versions[0].".".$mysql_versions[1];
  
  $fp = fopen(dirname(__FILE__)."/config_base.php","r") or die("<script>alert('读取配置，请检查setup/config_base.php 是否可读取！');history.go(-1);</script>");;
  $configstr1 = fread($fp,filesize(dirname(__FILE__)."/config_base.php"));
  fclose($fp);
  
  $fp = fopen(dirname(__FILE__)."/config_hand.php","r") or die("<script>alert('读取配置，请检查setup/config_base.php 是否可读取！');history.go(-1);</script>");;
  $configstr2 = fread($fp,filesize(dirname(__FILE__)."/config_hand.php"));
  fclose($fp);
  
  //config_base.php
  $configstr1 = str_replace("~dbhost~",$cfg_dbhost,$configstr1);
	$configstr1 = str_replace("~dbname~",$cfg_dbname,$configstr1);
	$configstr1 = str_replace("~dbuser~",$cfg_dbuser,$configstr1);
	$configstr1 = str_replace("~dbpwd~",$cfg_dbpwd,$configstr1);
	$configstr1 = str_replace("~dbprefix~",$cfg_dbprefix,$configstr1);
  $configstr1 = str_replace("~dblang~",$dblang,$configstr1);
  
  $fp = fopen(dirname(__FILE__)."/../include/config_base.php","w") or die("<script>alert('写入配置失败，请检查../include目录是否可写入！');history.go(-1);</script>");
  fwrite($fp,$configstr1);
  fclose($fp);

	//config_hand.php
	$cfg_cmspath = trim(ereg_replace("/{1,}","/",$cfg_cmspath));
	if($cfg_cmspath!="" && !ereg("^/",$cfg_cmspath)) $cfg_cmspath = "/".$cfg_cmspath;
	
	if($cfg_cmspath=="") $indexUrl = "/";
	else $indexUrl = $cfg_cmspath;
	
	$configstr2 = str_replace("~baseurl~",$base_url,$configstr2);
	$configstr2 = str_replace("~basepath~",$cfg_cmspath,$configstr2);
	$configstr2 = str_replace("~indexurl~",$indexUrl,$configstr2);
	$configstr2 = str_replace("~cookieEncode~",$cookieEncode,$configstr2);
	
	$fp = fopen(dirname(__FILE__)."/../include/config_hand.php","w") or die("<script>alert('写入配置失败，请检查../include目录是否可写入！');history.go(-1);</script>");
  fwrite($fp,$configstr2);
  fclose($fp);
  
  $fp = fopen(dirname(__FILE__)."/../include/config_hand_bak.php","w");
  fwrite($fp,$configstr2);
  fclose($fp);
  
  if($mysql_version < 4.1) $fp = fopen(dirname(__FILE__)."/sql_4_0.txt","r");
  else $fp = fopen(dirname(__FILE__)."/sql_4_1.txt","r");
  
  //创建数据表和写入数据
  $query = "";
  while(!feof($fp))
	{
		$line = trim(fgets($fp,1024));
		if(ereg(";$",$line)){
			$query .= $line;
			if($mysql_version < 4.1) mysql_query(str_replace("#@__",$cfg_dbprefix,$query),$conn);
			else mysql_query(str_replace("#~lang~#",$dblang,str_replace("#@__",$cfg_dbprefix,$query)),$conn);
			$query="";
		}else if(!ereg("^//",$line)){
			$query .= $line."\n";
		}
	}
	fclose($fp);
	
	$adminquery = "INSERT INTO `{$cfg_dbprefix}admin` VALUES (1, 10, '$adminuser', '".substr(md5($adminpwd),0,24)."', 'admin', '', '', 0, '".GetDateTimeMk(time())."', '127.0.0.1');";
	mysql_query($adminquery,$conn);
	$adminquery = "Update `{$cfg_dbprefix}sysconfig` set value='{$base_url}' where varname='cfg_basehost';";
	mysql_query($adminquery,$conn);
	$adminquery = "Update `{$cfg_dbprefix}sysconfig` set value='{$cfg_cmspath}' where varname='cfg_cmspath';";
	mysql_query($adminquery,$conn);
	$adminquery = "Update `{$cfg_dbprefix}sysconfig` set value='{$indexUrl}' where varname='cfg_indexurl';";
	mysql_query($adminquery,$conn);
	$adminquery = "Update `{$cfg_dbprefix}sysconfig` set value='{$cookieEncode}' where varname='cfg_cookie_encode';";
	mysql_query($adminquery,$conn);
	
  @mysql_close($conn);
  
  ShowMsg("完成设置，现转向登录面页...","../dede/login.php");
  exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>织梦内容管理系统 DedeCms V3.1 安装程序</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body bgColor='#ffffff' leftMargin='0' topMargin='0'>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#111111" style="BORDER-COLLAPSE: collapse">
  <tr> 
    <td width="100%" height="64" background="img/indextitlebg.gif">
    <a href="http://www.dedecms.com"><img src="img/df_dedetitle.gif" width="178" height="53" border="0"></a>
    </td>
  </tr>
  <tr> 
    <td width="100%" height="20" valign="middle" bgcolor="#F9FDF2"> <table width="540" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td>
          	<IMG height=14 src="img/book1.gif" width=20>&nbsp; 安装 DedeCms V3.1 版
          </td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td width="100%" height="1" background="img/sp_bg.gif"></td>
  </tr>
  <tr> 
    <td width="100%" height="2"></td>
  </tr>
  <form name="form1" method="post" action="index.php">
    <input type="hidden" name="step" value="2">
    <tr> 
      <td width="100%" valign="top"> <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#EEF9D9">
          <tr bgcolor="#FFFFFF"> 
            <td width="19%" height="87">&nbsp;目录权限：</td>
            <td width="81%">
            	如果在linux或Unix平台，以下目录需手工在FTP中设为组用户可读写，或用全权限 0777 <br>
              ../include<br>
              ../dede/templets<br>
              在安装完本程序后，请在后台进行一次DedeCms目录权限检测
              </td>
          </tr>
          <tr bgcolor="#F9FDF2"> 
            <td colspan="2">&nbsp;数据库设定：</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;数据库主机：</td>
            <td><input name="cfg_dbhost" type="text" id="cfg_dbhost" value="localhost"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;数据库名称：</td>
            <td><input name="cfg_dbname" type="text" id="cfg_dbname" value="dedev3_1"> 
              <input name="isnew" type="checkbox" id="isnew" value="1">
              创建新数据库</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;数据库用户：</td>
            <td><input name="cfg_dbuser" type="text" id="cfg_dbuser" value="root"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;数据库密码：</td>
            <td><input name="cfg_dbpwd" type="text" id="cfg_dbpwd"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;数据库前缀：</td>
            <td><input name="cfg_dbprefix" type="text" id="cfg_dbprefix" value="dede_"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;数据库编码：</td>
            <td><input name="dblang" type="radio" value="gbk" checked>
              GBK 
              <input type="radio" name="dblang" value="latin1">
              LATIN1 （仅对4.1+以上版本的MySql选择）</td>
          </tr>
          <tr bgcolor="#F9FDF2"> 
            <td colspan="2">&nbsp;管理员初始密码：</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;用户名：</td>
            <td><input name="adminuser" type="text" id="adminuser" value="admin"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;密　码：</td>
            <td><input name="adminpwd" type="text" id="adminpwd" value="admin"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;Cookie加密码：</td>
            <td><input name="cookieEncode" type="text" id="cookieEncode" value="<?=$rnd_cookieEncode?>"></td>
          </tr>
          <tr bgcolor="#F9FDF2"> 
            <td colspan="2">&nbsp;其它设定：</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;网站网址：</td>
            <td><input name="base_url" type="text" id="base_url" value="<?=$baseurl?>" size="35"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">&nbsp;CMS安装目录：</td>
            <td><input name="cfg_cmspath" type="text" id="cfg_cmspath" value="<?=$basepath?>">
              （在根目录安装时不必理会） </td>
          </tr>
          <tr bgcolor="#F9FDF2"> 
            <td height="30" colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="14%" height="35">&nbsp;</td>
                  <td width="86%"><input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
                </tr>
                <tr align="right" bgcolor="#FFFFFF"> 
                  <td height="80" colspan="2"><img src="py/p5.gif" width="43" height="41"><img src="py/p4.gif" width="43" height="41"><img src="py/p3.gif" width="43" height="41"><img src="py/p2.gif" width="43" height="41"><img src="py/p1.gif" width="43" height="41"></td>
                </tr>
              </table></td>
          </tr>
        </table> </td>
    </tr>
  </form>
  <tr> 
    <td width="100%" height="2" valign="top"></td>
  </tr>
</table>
<p align="center">
<a href='http://www.dedecms.com' target='_blank'>Power by DedeCms 织梦内容管理系统</a><br><br>
</p>
</body>
</html>
