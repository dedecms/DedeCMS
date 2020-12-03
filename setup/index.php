<?
$registerGlobals = @ini_get("register_globals");
$isUrlOpen = @ini_get("allow_url_fopen");
$isMagic = @ini_get("magic_quotes_gpc");
if(!$registerGlobals){
	if(!$isMagic) require_once(dirname(__FILE__)."/config_rglobals_magic.php");
	else require_once(dirname(__FILE__)."/config_rglobals.php");
}
else{
	if(!$isMagic) require_once(dirname(__FILE__)."/config_rglobals_magic.php");
}
function GetDateTimeMk($mktime)
{
	if($mktime==""||ereg("[^0-9]",$mktime)) return "";
	return strftime("%Y-%m-%d %H:%M:%S",$mktime);
}
if(empty($step)) $step = 1;

//安装程序
//---------------------------------------------------
if($step==2)
{
  if(!isset($isnew)) $isnew = 0;
  $conn = mysql_connect($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd) or die("<script>alert('数据库服务器或登录密码无效，\\n\\n无法连接数据库，请重新设定！');history.go(-1);</script>");
  if($isnew==1){
  	mysql_query("CREATE DATABASE ".$cfg_dbname,$conn) or die("<script>alert('创建数据库失败，可能权限不足！');history.go(-1);</script>");;
  }
  mysql_select_db($cfg_dbname) or die("<script>alert('数据库不存在，请重新设定！');history.go(-1);</script>");
  mysql_query("SET NAMES 'gbk';",$conn);
  $rs = mysql_query("SELECT VERSION();",$conn);
  $row = mysql_fetch_array($rs);
  $mysql_version = $row[0];
  $mysql_versions = explode(".",trim($mysql_version));
  $mysql_version = $mysql_versions[0].".".$mysql_versions[1];
  
  $fp = fopen(dirname(__FILE__)."/config_base.php","r") or die("<script>alert('读取配置，请检查setup/config_base.php 是否可读取！');history.go(-1);</script>");;
  $configstr = fread($fp,filesize(dirname(__FILE__)."/config_base.php"));
  fclose($fp);
  
  $configstr = str_replace("~dbhost~",$cfg_dbhost,$configstr);
	$configstr = str_replace("~dbname~",$cfg_dbname,$configstr);
	$configstr = str_replace("~dbuser~",$cfg_dbuser,$configstr);
	$configstr = str_replace("~dbpwd~",$cfg_dbpwd,$configstr);
	$configstr = str_replace("~dbprefix~",$cfg_dbprefix,$configstr);
	$configstr = str_replace("~cfg_webname~",$cfg_webname,$configstr);
	$configstr = str_replace("~email~",$email,$configstr);
	$bkdir = "backup_".substr(md5(mt_rand(1000,5000).time().mt_rand(1000,5000)),0,10);
	$configstr = str_replace("~bakdir~",$bkdir,$configstr);
	$configstr = str_replace("~baseurl~",$base_url,$configstr);
	
	$cfg_cmspath = trim(ereg_replace("/{1,}","/",$cfg_cmspath));
	if($cfg_cmspath!="" && !ereg("^/",$cfg_cmspath)) $cfg_cmspath = "/".$cfg_cmspath;
	
	if($cfg_cmspath=="") $indexUrl = "/";
	else $indexUrl = $cfg_cmspath;
	
	$configstr = str_replace("~basepath~",$cfg_cmspath,$configstr);
	$configstr = str_replace("~indexurl~",$indexUrl,$configstr);
	
	$fp = fopen(dirname(__FILE__)."/../include/config_base.php","w") or die("<script>alert('写入配置失败，请检查../include目录是否可写入！');history.go(-1);</script>");;
  $configstr = fwrite($fp,$configstr);
  fclose($fp);
  
  if($setuptype=="update")
  {
  	if($mysql_version < 4.1) $fp = fopen(dirname(__FILE__)."/upsql.txt","r");
  	else $fp = fopen(dirname(__FILE__)."/upsql-4.1.txt","r");
  }
  else
  {
  	if($mysql_version < 4.1) $fp = fopen(dirname(__FILE__)."/sql.txt","r");
  	else $fp = fopen(dirname(__FILE__)."/sql-4.1.txt","r");
  }
  $query = "";
  while(!feof($fp))
	{
		$line = trim(fgets($fp,1024));
		if(ereg(";$",$line)){
			$query .= $line;
			mysql_query(str_replace("#@__",$cfg_dbprefix,$query),$conn);
			$query="";
		}
		else if(!ereg("^//",$line)){
			$query .= $line."\n";
		}
	}
	fclose($fp);
	
	if($setuptype == "new")
	{
	  $adminquery = "INSERT INTO ".$cfg_dbprefix."admin VALUES (1, 10, '$adminuser', '".md5($adminpwd)."', 'admin', 0, '".GetDateTimeMk(time())."', '127.0.0.1');";
	  mysql_query($adminquery,$conn);
  }
	
  @mysql_close($conn);
  
  ShowMsg("完成设置，现转向登录面页...","../dede/");
  exit();
}
//-----------------
//提示信息
//-----------------
function ShowMsg($msg,$gourl,$onlymsg=0,$limittime=0)
{
		$htmlhead  = "<html>\r\n<head>\r\n<title>提示信息</title>\r\n";
		$htmlhead .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\" />\r\n";
		$htmlhead .= "</head>\r\n<body leftmargin='0' topmargin='0'><center>\r\n<script>\r\n";
		$htmlfoot  = "</script>\r\n</center></body>\r\n</html>\r\n";
		
		if($limittime==0) $litime = 1000;
		else $litime = $limittime;
		
		if($gourl=="-1"){
			if($limittime==0) $litime = 5000;
			$gourl = "javascript:history.go(-1);";
		}
		
		if($gourl==""||$onlymsg==1){
			$msg = "<script>alert(\"".str_replace("\"","“",$msg)."\");</script>";
		}
		else
		{
			$func = "      var pgo=0;
      function JumpUrl(){
        if(pgo==0){
          location='$gourl';
          pgo=1;
        }
      }\r\n";
			$rmsg = $func;
			$rmsg .= "document.write(\"<br/><div style='width:400px;padding-top:4px;height:24;font-size:10pt;border-left:1px solid #cccccc;border-top:1px solid #cccccc;border-right:1px solid #cccccc;background-color:#DBEEBD;'>DEDECMS 提示信息！</div>\");\r\n";
			$rmsg .= "document.write(\"<div style='width:400px;height:100;font-size:10pt;border:1px solid #cccccc;background-color:#F4FAEB'><br/><br/>\");\r\n";
			$rmsg .= "document.write(\"".str_replace("\"","“",$msg)."\");\r\n";
			$rmsg .= "document.write(\"";
			if($onlymsg==0){
				$rmsg .= "<br/><br/><a href='".$gourl."'>如果你的浏览器没反应，请点击这里...</a><br/><br/></div>\");\r\n";
				$rmsg .= "setTimeout('JumpUrl()',$litime);";
			}
			else{
				$rmsg .= "<br/><br/></div>\");\r\n";
			}
			$msg  = $htmlhead.$rmsg.$htmlfoot;
		}		
		echo $msg;
}
//--------------------------------------------------

//读取初始参数
//----------------------
if(!empty($_SERVER["REQUEST_URI"])){
  $scriptName = $_SERVER["REQUEST_URI"];
}
else{
  $scriptName = $_SERVER["PHP_SELF"];
}
$basepath = eregi_replace("/setup(.*)$","",$scriptName);

if(empty($_SERVER['HTTP_HOST'])) $baseurl = "http://".$_SERVER['HTTP_HOST'];
else $baseurl = "http://".$_SERVER['SERVER_NAME'];

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>织梦内容管理系统 DedeCms V3.0 安装程序</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body bgColor='#ffffff' leftMargin='0' topMargin='0'>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#111111" style="BORDER-COLLAPSE: collapse">
  <tr> 
    <td width="100%" height="64" background="img/indextitlebg.gif"><img src="img/indextitle.gif" width="250" height="64"> 
    </td>
  </tr>
  <tr> 
    <td width="100%" height="20" valign="middle" bgcolor="#F9FDF2"> <table width="540" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td><IMG height=14 src="img/book1.gif" width=20>&nbsp; 安装DedeCms</td>
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
            <td width="19%" height="44">&nbsp;目录权限：</td>
            <td width="81%">如果在linux或Unix平台，以下目录需设为组用户可读写，或用全权限 0777 <br>
              ../html<br>
              ../upimg/*<br>
              ../templets/*<br>
              ../special/*<br>
              ../plus/*<br>
              ../include/sessions<br>
              ../include (仅安装时需要设置为0777)</td>
          </tr>
          <tr bgcolor="#F9FDF2"> 
            <td colspan="2">&nbsp;数据库设定：</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;数据库主机：</td>
            <td><input name="cfg_dbhost" type="text" id="cfg_dbhost" value="localhost"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;数据库名称：</td>
            <td><input name="cfg_dbname" type="text" id="cfg_dbname" value="dedev3"> 
              <input name="isnew" type="checkbox" id="isnew" value="1">
              创建新数据库</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;数据库用户：</td>
            <td><input name="cfg_dbuser" type="text" id="cfg_dbuser" value="root"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;数据库密码：</td>
            <td><input name="cfg_dbpwd" type="text" id="cfg_dbpwd"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;数据库前缀：</td>
            <td><input name="cfg_dbprefix" type="text" id="cfg_dbprefix" value="dede_"></td>
          </tr>
          <tr bgcolor="#F9FDF2"> 
            <td colspan="2">&nbsp;管理员初始密码：（用户名和密码只允许使用 [a-z][A-Z][0-9][-][_][@][.]以内的字符！）</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;用户名：</td>
            <td><input name="adminuser" type="text" id="adminuser" value="admin"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;密　码：</td>
            <td><input name="adminpwd" type="text" id="adminpwd" value="admin"></td>
          </tr>
          <tr bgcolor="#F9FDF2"> 
            <td colspan="2">&nbsp;其它设定：</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>安装选项：</td>
            <td><input name="setuptype" type="radio" value="new" checked>
              全新安装 
              <input type="radio" name="setuptype" value="update">
              从V3正式版升级</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td colspan="2">如果你是从V3正式版升级的，可以不理会管理员用户名和密码，升级方法如下：<br>
              1、先备份 templets/default 文件夹里的文件；<br>
              2、把所有新版的文件夹覆盖旧文件；<br>
              3、运行升级程序；<br>
              4、把备份的模板文件覆盖新文件。</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;网站名称：</td>
            <td><input name="cfg_webname" type="text" id="cfg_webname"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;站长Email：</td>
            <td><input name="email" type="text" id="email"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;网站网址：</td>
            <td><input name="base_url" type="text" id="base_url" value="<?=$baseurl?>" size="35"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td>&nbsp;CMS安装目录：</td>
            <td><input name="cfg_cmspath" type="text" id="cfg_cmspath" value="<?=$basepath?>"></td>
          </tr>
          <tr bgcolor="#F9FDF2"> 
            <td height="30" colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="14%">&nbsp;</td>
                  <td width="86%"><input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
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
