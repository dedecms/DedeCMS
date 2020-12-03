<?
//不一定要求你用手工更改配置,但看一下这个文件对你更好的使用本系统是有帮助的

$registerGlobals = @ini_get("register_globals");
$isUrlOpen = @ini_get("allow_url_fopen");

if(!$registerGlobals) require_once("register_extern.php");

//网站根目录的绝对路径和站点网址

$base_dir="~basedir~";
$base_url = "~baseurl~";

//数据库连接信息
$dbname = "~dbname~";
$dbhost = "~dbhost~";
$dbusername = "~dbuser~";
$dbuserpwd = "~dbpwd~";

//网页首页链接和名称
$index_url="~indexurl~";
$index_name="首页";

//网站名称，RSS列表中,将以这个名称作为网站的描述
$webname = "~webname~"; 

//统管理员的Email,网站与发信有关的程序会用这个Email
$admin_email = "~adminemail~";

//文章的路径，建议按默认，如果要更改，必须为与管理目录同级深度的目录
$art_dir = "~artdir~";

//图片浏览器的默认路径
$imgview_dir = "~imgviewdir~";

//动态文件的目录，这个目录是用于存放计数器、投票、评论等程序的必要动态程序
$art_php_dir = "~phpdir~";

//数据备份目录
$bak_dir = $art_php_dir."~bakdir~";

//生成文件的扩展名，建议用 .htm 或 .html ,如有需要,你也可以用 .php 或 .shtml
$art_shortname = "~artshortname~";

//HTML的保存路径，选项为:
//[1] listdir 表示在类目的目录下以 ID.htm 的形式生成文件
//[2] maketime 表示以 $artdir/year/monthday/ID 来生成文件
//如果你是第一次使用，推荐用这种文件形式，如果是V0.8升级版本，请用listdir形式
$art_nametag = "~artnametag~";

//新建目录的权限
//如果你使用别的属性，本程不保证程序能顺利在Linux或Unix系统运行
$dir_purview = 0755;

//是否允许用户投稿, -1 表示所有会员允许投稿
//如果大于-1，表示允许投稿的会员级别代码，如果你不想用户可以投稿，请改为 10000 之类的数字
$userSendArt = -1;

//--------------------------------
//以下选项如无必要，不建议更改//

//标记风格
$tag_start_char = "{";
$tag_end_char = "}";

//默认的名字空间，不建议更改
$tag_namespace = "dede";

//上传的图片的路径,建议按默认
$img_dir = $imgview_dir."/uploadimg";

//缩略图
$ddimg_dir = $imgview_dir."/artlit";

//用户投稿图片存放目录
$userimg_dir = $imgview_dir."/user";

//上传的软件目录
$soft_dir = $imgview_dir."/uploadsoft";

//友情链接图标的目录
$flink_dir = $imgview_dir."/flink";

//模板的存放目录
$mod_dir = $art_php_dir."/modpage";

if(!is_dir($art_dir)) require("start_newdir.php");

/////////////////////////////////////////////////////////////
//相关的配置选项结束
//以下为常用函数
/////////////////////////////////////////////////////////////
//-----连接MySql数据库----------------
function connectMySql()
{
	global $dbname,$dbhost,$dbusername,$dbuserpwd;
	$openconn = mysql_pconnect($dbhost,$dbusername,$dbuserpwd) or die("无法连接MySQL数据库!");
	mysql_select_db($dbname,$openconn);
	return $openconn;
}
//-----中文字符截取--------
function cn_substr($str,$len)
{
  return cn_midstr($str,0,$len);
}
function cn_midstr($str,$start,$len){
  $i=0;
  $dd=0;
  while($i<$start)
  {
  		$ch=substr($str,$i,1);
  		if(ord($ch)>127) $dd++;
  		else $dd=$dd+2;
  		$i++;
  }
  if($dd%2!=0) $start++;
  $i=$start;
  $endnum = $start+$len;
  while($i<$endnum)
  {
    $ch=substr($str,$i,1);
    if(ord($ch)>127) $i++;
      $i++;
  }
  $restr=substr($str,$start,$i-$start);
  return $restr;
}
//-------返提示信息----------
function ShowMsg($msg,$gotoPage)
{
	$msg = str_replace("'","`",trim($msg));
	$gotoPage = str_replace("'","`",trim($gotoPage));
	echo "<script language='javascript'>\n";
	echo "alert('$msg');";
	if($gotoPage=="back")
	{
		echo "history.go(-1);\r\n";
	}
	else if(ereg("^-",$gotoPage))
	{
		echo "history.go($gotoPage);\r\n";
	}
	else if($gotoPage!="")
	{
		echo "location.href='$gotoPage';\r\n";
	}
	echo "</script>";
}
?>