<?
if(empty($step)) $step=0;
$s_isreg = @get_cfg_var("register_globals");
if($s_isreg!=1)
{
	if (!empty($_GET)) foreach($_GET AS $key => $value){$$key = $value;}
	if (!empty($_POST)) foreach($_POST AS $key => $value){$$key = $value;}
}
if(empty($setuptype)) $setuptype=0;
function GetConfigFile()
{
	$fp = fopen("config_base.php","r") or die("<script>alert('配置文件无法读取或无法访问,请把当前目录权限设为可读写！');history.go(-1);</script>");
	$configfile = fread($fp,filesize("config_base.php"));
	fclose($fp);
	return $configfile;
}
function SaveConfigFile($str)
{
	$fp = fopen("config_base.php","w") or die("<script>alert('配置文件无法写入，请把当前目录权限设为可读写！');history.go(-1);</script>");;
	fwrite($fp,$str);
	fclose($fp);
}
//设置数据库等选项
if($step==2)
{
	$conn = @mysql_connect($dbhost,$dbuser,$dbpwd) or die("<script>alert('数据库服务器或登录密码无效，\\n\\n无法连接数据库，请重新设定！');history.go(-1);</script>");
	mysql_select_db($dbname) or die("<script>alert('数据库不存在，请重新设定！');history.go(-1);</script>");
	$basedir = ereg_replace("/$","",$basedir);
	$basedir = str_replace("\\","",$basedir);
	$basepath = ereg_replace("/$","",trim($basepath));
	if($basepath=="/") $basepath="";
	if($basepath!=""&&!ereg("^/",$basepath)) $basepath="/".$basepath;
	$rbasepath = "";
	if($basepath=="") $rbasepath="/";
	else $rbasepath = $basepath;
	if(!file_exists($basedir.$basepath."/setup/setup.php"))
	{
		echo "<script>alert('网站根目录或相对路径没设置正确！');history.go(-1);</script>";
		exit();
	}
	$phpdir = ereg_replace("/$","",$basepath)."/php";
	$bakdir = "/".ereg_replace("^/|/$","",$bakdir);
	$baktruedir = $phpdir.$bakdir;
	$baseurl = ereg_replace("/$","",$baseurl);
	$artdir = $basepath.ereg_replace("/$","",$artdir);
	$imgviewdir = $basepath.ereg_replace("/$","",$imgviewdir);
	//检测目录是否存在
	if(!is_dir($basedir.$artdir))
	{ mkdir($basedir.$artdir,0755) or die("<script>alert('创建目录 $basedir$artdir 失败！');history.go(-1);</script>"); }
	if(!is_dir($basedir.$imgviewdir))
	{ mkdir($basedir.$imgviewdir,0755) or die("<script>alert('创建目录 $basedir$imgviewdir 失败！');history.go(-1);</script>"); }
	if(!is_dir($basedir.$baktruedir))
	{ mkdir($basedir.$baktruedir,0755) or die("<script>alert('创建目录 $basedir$baktruedir 失败！');history.go(-1);</script>"); }
	//更改配置文件
	$configstr = GetConfigFile();
	$configstr = str_replace("~dbhost~",$dbhost,$configstr);
	$configstr = str_replace("~dbname~",$dbname,$configstr);
	$configstr = str_replace("~dbuser~",$dbuser,$configstr);
	$configstr = str_replace("~dbpwd~",$dbpwd,$configstr);
	$configstr = str_replace("~webname~",$webname,$configstr);
	$configstr = str_replace("~adminemail~",$adminemail,$configstr);
	//初始化系统表
	$sqlfiles[0]="newinstallsql.txt";
	$sqlfiles[1]="v2old-upsql.txt";
	$adminquery = "INSERT INTO dede_admin VALUES (1, 10, '$adminname', '".md5($adminpwd)."', '$adminwriter', 0, '0000-00-00 00:00:00', '127.0.0.1');";
	$sqlfile = $sqlfiles[$setuptype];
	$fp = fopen($sqlfile,"r");
	$query = "";
	while($line = fgets($fp,1024))
	{
		$line = trim($line);
		if(ereg(";$",$line))
		{
			$query.=$line;
			mysql_query($query,$conn);
			$query="";
		}
		else if(!ereg("^//",$line))
		{
			$query.=$line;
		}
	}
	fclose($fp);
	if($setuptype==0) mysql_query($adminquery,$conn);
	//
	mysql_close($conn);
	//设置应用目录
	$img_dir = $imgviewdir."/uploadimg";
	$ddimg_dir = $imgviewdir."/artlit";
	$userimg_dir = $imgviewdir."/user";
	$soft_dir = $imgviewdir."/uploadsoft";
	$flink_dir = $imgviewdir."/flink";
	if(!is_dir($basedir.$img_dir)) mkdir($basedir.$img_dir,0755);
	if(!is_dir($basedir.$userimg_dir)) mkdir($basedir.$userimg_dir,0755);
	if(!is_dir($basedir.$soft_dir)) mkdir($basedir.$soft_dir,0755);
	if(!is_dir($basedir.$ddimg_dir)) mkdir($basedir.$ddimg_dir,0755);
	if(!is_dir($basedir.$flink_dir)) mkdir($basedir.$flink_dir,0755);
	//////////////////////////////////////////////////////
	if($artdir=="/") $artdir="";
	$configstr = str_replace("~basedir~",$basedir,$configstr);
	$configstr = str_replace("~baseurl~",$baseurl,$configstr);
	$configstr = str_replace("~artdir~",$artdir,$configstr);
	$configstr = str_replace("~phpdir~",$phpdir,$configstr);
	$configstr = str_replace("~bakdir~",$bakdir,$configstr);
	$configstr = str_replace("~imgviewdir~",$imgviewdir,$configstr);
	$configstr = str_replace("~artnametag~",$artnametag,$configstr);
	$configstr = str_replace("~artshortname~",$artshortname,$configstr);
	$configstr = str_replace("~indexurl~",$rbasepath,$configstr);
	SaveConfigFile($configstr);
	copy("config_base.php","../dede/config_base.php");
	if($admindir!="dede")
	{
		rename("../dede","../".$admindir);
		$uadminfiles= Array(
		"../php/config.php",
		"../php/vote.php",
		"../php/viewart.php",
		"../php/list.php",
		"../php/guestbook/config.php",
		"../member/config.php",
		"../index.php"
		);
		foreach($uadminfiles as $key=>$uadminfile)
		{
			$f2 = fopen($uadminfile,"r");
			$fbody = fread($f2,filesize($uadminfile));
			fclose($f2);
			$fbody = str_replace("dede/",$admindir."/",$fbody);
			$f2 = fopen($uadminfile,"w");
			fwrite($f2,$fbody);
			fclose($f2);
		}
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>织梦内容管理系统V2.1完美版</title>
<link href="../base.css" rel="stylesheet" type="text/css">
</head>

<body>
<?
if($step==0)
{
?>
<table width="720"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="24" align="center" bgcolor="#6699FF"><b>织梦内容管理系统V2.1完美版</b></td>
  </tr>
  <tr>
    <td height="89" bgcolor="#FFFFFF"><p>本程序为免费无偿发布的开源软件项目，但使用时必须注意如下几点：<br>
        一、如果你更新或修改了本软件的部份功能，并在网上重新发布，必须声明本软件的最终拥有权属于织梦之旅(www.dedecms.com)；<br>
        二、本软件在正常使用的情况下，允许用于商业或非商业性质的用途，但不得把本软件修改后作为商业软件出售；<br>
        三、我们仅提供技术上的支持，不对用户对本软件的使用用途负任何责任。</p>
      <p>// 织梦之旅官方网站： <a href="http://www.dedecms.com" target="_blank">http://www.dedecms.com</a>　<br>
        // 程式制作：IT柏拉图 QQ:2500875<br>
        // Email：dbzllx@21cn.com 由于垃圾邮件太多，有问题请尽量用QQ联系或在官方网站留言。<br>
        <br>
        　　这个版本将是DedeCms旧结构的最后一个版本，如果在非根目录安装，可能会有一些初级用户难以理解的问题，但作为一个开源的平台，我相信仍然会有大量有支持者，DedeCms的未来版本DedeCmsV3.0（也可能是别的名称），将会是一个吸收大量其它的CMS平台的优点，并对结构重新设计过的一个超级内容管理平台，它对个人仍然是免费的，在将来的日子中我们一起创造永远的PHP，永远的开源平台。</p>
      <p>//////////////////////////////////////////////////////////////<br>
        DedeCms 织梦内容管理系统V2.1完美版 For PHP4<br>
        //////////////////////////////////////////////////////////////<br>
        本程序要求的安装平台:</p>
      <p>[1]PHP 大于4.1.0 版本,小于5.0版本(要求包含GD库),Apache或IIS的各种模式均可安装<br>
        [2]后台管理必须使用 IE 5.5 以上版本的浏览器<br>
        [3]必须开放：ini_get()、mysql_pconnect()、dir()、fopen()函数，建议用1.6以上的GD库</p>
      <p>V2.1完美版本功能改进说明</p>
      <p>[1] 修正已知的主要Bug<br>
	[2] 增加使用动态列表的功能,以适应不同用户的需求<br>
	[3] 细化了列表创建的功能<br>
	[4] 增加了频道首页默认的板块模板,以方便更高层次的应用<br>
	[5] 增加了可以按发布时间对文章进行归档的功能<br>
	[6] 增加了调用Discuz、PHPWIND、VBB、PHPBB 论坛最新贴子的板块代码<br>
	[7] 增加了可选的动态首页<br>
	[8] 增加了可以获直接取系统配置的{dede:extern name='var'/}标记<br>
	[9] 增加了栏目的移动功能<br>
	[10] 增加了loop标记,可以自由获取任意表的内容<br>
	[11] 加强了数据备份的功能<br>
	[12] 更改了系统操作菜单的界面<br>
        <br>
        <font color="#FF0000">目录权限要求：</font><font color="#FF0000"><br>
        ../ </font>dedecms的根目录可读写<font color="#FF0000"></font><font color="#FF0000"><br>
        ../php </font>PHP程序目录可读写<font color="#FF0000"><br>
        ../php/guestbook </font>留言簿目录可读写<font color="#FF0000"><br>
        ../dede </font>管理目录可读写<font color="#FF0000"></font> (安装完后可更改为只读权限)<font color="#FF0000"><br>
        ../member </font>会员目录可读写 (安装完后可更改为只读权限)<font color="#FF0000"><br>
        </font>以下文件均要求可读写<font color="#FF0000"> <br>
        ./config_base.php</font><font color="#FF0000"><br>
        ../php/config.php<br>
        ../php/vote.php<br>
        ../php/viewart.php<br>
        ../php/list.php<br>
        ../php/guestbook/config.php<br>
        ../member/config.php<br>
        ../index.php</font></p>
      <p><br>
        <br>
      </p>
      </td>
  </tr>
  <tr>
    <td height="26" align="center" bgcolor="#6699FF"><p>
      <input type="button" name="bt0" value="我已阅读完上面说明，进入正式安装" onClick="location='setup.php?step=1';">
    </p>      </td>
  </tr>
</table>
<?
}
else if($step==1)
{
?>
<form action="setup.php" name="form1" method="post">
 <input type="hidden" name="step" value="2">
  <table width="720" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
    <tr> 
      <td height="24" colspan="2" align="center" bgcolor="#6699FF"><b>MySql数据库安装和基本设定</b></td>
    </tr>
    <tr> 
      <td height="24" colspan="2" bgcolor="#FFFFFF">[1]请注意：程序不会自动创建数据库，请先自行建立数据库再安装。<br>
        [2]本程序建议安装在根目录，如果你不是在根目录安装，虽然系统也能正常使用，但模板里用/php和/member表示的链接将全部要手动更改，<font color='red'>如果你第一次使用本系统，强烈建议你把本程序安装到根目录，否则可能会有不可预知的错误</font>。</td>
    </tr>
    <tr> 
      <td width="152" height="24" align="right" bgcolor="#FFFFFF">数据服务器：</td>
      <td width="561" bgcolor="#FFFFFF"><input name="dbhost" type="text" id="dbhost" value="localhost" size="30"></td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">数据库名称：</td>
      <td bgcolor="#FFFFFF"><input name="dbname" type="text" id="dbname" size="30"></td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">数据库登录用户：</td>
      <td bgcolor="#FFFFFF"><input name="dbuser" type="text" id="dbuser" size="30"></td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">数据库登录密码：</td>
      <td bgcolor="#FFFFFF"><input name="dbpwd" type="text" id="dbpwd" size="30"></td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">数据表前缀：</td>
      <td bgcolor="#FFFFFF">dede_</td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">安装选项：</td>
      <td bgcolor="#FFFFFF"><input name="setuptype" type="radio" value="0" checked>
        全新安装 
        <input type="radio" name="setuptype" value="1">
        由2.0版升级</td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">超级管理员名称：</td>
      <td bgcolor="#FFFFFF"><input name="adminname" type="text" id="adminname" value="admin"></td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">超级管理员密码：</td>
      <td bgcolor="#FFFFFF"><input name="adminpwd" type="text" id="adminpwd" value="admin"></td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">管理员笔名：</td>
      <td bgcolor="#FFFFFF"><input name="adminwriter" type="text" id="adminwriter" value="admin"></td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">网站名称：</td>
      <td bgcolor="#FFFFFF"><input name="webname" type="text" id="webname" value="织梦之旅"></td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">管理员Email：</td>
      <td bgcolor="#FFFFFF"><input name="adminemail" type="text" id="adminemail" value="dbzllx@21cn.com"></td>
    </tr>
  </table>
<br>
  <table width="720" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
    <tr> 
      <td height="24" colspan="2" align="center" bgcolor="#6699FF"><b>文件目录和其它参数设定</b></td>
    </tr>
    <tr> 
      <td width="145" height="24" align="right" bgcolor="#FFFFFF">网站根目录：</td>
	  <?
	  $acpath="";
	  $bdir="";
	  
	  if(isset($_SERVER["SCRIPT_NAME"])) 
	  	$script_name = $_SERVER["SCRIPT_NAME"];
	  if(!eregi("setup",$script_name) && isset($_SERVER["PHP_SELF"]))
	  	$script_name = $_SERVER["PHP_SELF"];
	  if(!eregi("setup",$script_name) && isset($_SERVER["REQUEST_URI"]))
	  	$script_name = $_SERVER["REQUEST_URI"];
	  $acpath =	str_replace("/setup/setup.php","",$script_name); 
	  
	  $now_dir = @dirname(__FILE__);
	  if($now_dir=="") $now_dir = @getcwd();
	  $bdir = $now_dir;
	  $bdir = str_replace("\\","/",$bdir);
	  $bdir = str_replace("$acpath/setup","",$bdir);
	  ?>
      <td width="568" bgcolor="#FFFFFF"> <input name="basedir" type="text" id="basedir" value="<?=$bdir?>" size="30">
        （这项是必填的，如果你不能确定你的网站根目录，请与服务商联系）
      </td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">网站根网址：</td>
      <td bgcolor="#FFFFFF"><input name="baseurl" type="text" id="baseurl" value="http://<?if(isset($_SERVER["HTTP_HOST"])) echo $_SERVER["HTTP_HOST"];?>" size="30">
      </td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">DedeCms的目录：</td>
      <td bgcolor="#FFFFFF">
	  <input name="basepath" type="text" id="basepath" value="<?=$acpath?>" size="30">
        （空表示根目录）<br>
        假如安装文件网址为：http://test.com/dede2005/setup/setup.php 那么相对路径为：/dede2005 </td>
    </tr>
    <tr> 
      <td width="145" height="24" align="right" bgcolor="#FFFFFF">文章存放目录：</td>
      <td width="568" bgcolor="#FFFFFF">DedeCms目录：<input name="artdir" type="text" id="artdir" value="/html" size="30">
        &nbsp;&nbsp;（空表示根目录）</td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">文章保存形式：</td>
      <td bgcolor="#FFFFFF"> <input name="artnametag" type="text" id="artnametag" size="30" value="listdir"> 
        <br>
        //[1] listdir 表示在类目的目录下以 ID.htm 的形式生成文件<br>
        //[2] maketime 表示以 文章存放目录/year/monthday/ID 来生成文件</td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">文章扩展名：</td>
      <td bgcolor="#FFFFFF"><input name="artshortname" type="text" id="artshortname" value=".html" size="20"></td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">上传的文件存放根目录：<br>
        （图片浏览器的根目录）<br></td>
      <td bgcolor="#FFFFFF">DedeCms目录：<input name="imgviewdir" type="text" id="imgviewdir" value="/ddimg" size="20"> 
        &nbsp;(&quot;/&quot;表示根目录，结束不要加&quot;/&quot;)</td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">数据备份目录：</td>
      <td bgcolor="#FFFFFF">DedeCms目录：/php/ 
        <input name="bakdir" type="text" id="bakdir" value="bak" size="15"> &nbsp;<font color="#FF0000">(建议更改名称，结束不要加&quot;/&quot;)</font></td>
    </tr>
    <tr> 
      <td height="24" align="right" bgcolor="#FFFFFF">管理目录：</td>
      <td bgcolor="#FFFFFF">DedeCms目录：/ 
        <input name="admindir" type="text" id="admindir" value="dede" size="15"> 
        <font color="#FF0000">(建议更改名称，结束不要加&quot;/&quot;)</font></td>
    </tr>
    <tr> 
      <td height="26" colspan="2" align="center" bgcolor="#6699FF"> <input type="submit" name="bt0" value="保存，并进入下一步安装"> 
      </td>
    </tr>
  </table>
</form>
<?
}
else if($step==2)
{
?>
<table width="356" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
  <form name="form1" id="form1" action="../<?=$admindir?>/loginok.php" method="post">
  <tr>
      <td width="354" height="25" align="center" bgcolor="#F6F6F6" style="font-size:10pt"><strong>登录系统(请删除setup文件夹，否则下次无法登录)</strong></td>
  </tr>
  <tr>
    <td height="31" bgcolor="#FFFFFF">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td colspan="3" height="15"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td width="23%" height="24" style="font-size:10pt">用户名:</td>
          <td width="72%" height="24"><input name="userid" type="text" id="userid" style="width:160"></td>
        </tr>
        <tr>
          <td colspan="3" height="10"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td height="24" style="font-size:10pt">密　码:</td>
          <td height="24"><input name="pwd" type="password" id="pwd" style="width:160"></td>
        </tr>
        <tr>
          <td colspan="3" height="10"></td>
        </tr>
        <tr align="center">
          <td height="42" colspan="3">&nbsp;
              <input type="submit" name="Submit" value=" 登 录 ">
&nbsp; </td>
        </tr>
        <tr>
          <td colspan="3" height="10"></td>
        </tr>
    </table></td>
  </tr>
  </form>
</table>
<?
}
?>
</body>
</html>
