<?
/*-----------------------------------------
// DedeCms 主配置文件
// 如果你需要改动某些选项，请先备份本文件
-----------------------------------------*/

require_once(dirname(__FILE__)."/config_start.php");

//站点根网址
$cfg_basehost = "~baseurl~";

//DedeCms安装目录
$cfg_cmspath = "~basepath~";

//站点根目录
$ndir = str_replace("\\","/",dirname(__FILE__));
$cfg_basedir = eregi_replace($cfg_cmspath."/include[/]{0,1}$","",$ndir);

//数据库连接信息
$cfg_dbname = "~dbname~";
$cfg_dbhost = "~dbhost~";
$cfg_dbuser = "~dbuser~";
$cfg_dbpwd = "~dbpwd~";
$cfg_dbprefix = "~dbprefix~";

//cookie加密码
$cfg_cookie_encode = "EWT237827fdfsFSDA";

//网页首页链接和名称
$cfg_indexurl = "~indexurl~";
$cfg_indexname = "首页";

//网站名称，RSS列表中,将以这个名称作为网站的描述
$cfg_webname = "~cfg_webname~"; 

//统管理员的Email,网站与发信有关的程序会用这个Email
$cfg_adminemail = "~email~";

//DedeCms 版本信息

$cfg_powerby = "<a href='http://www.dedecms.com' target='_blank'>Power by DedeCms 织梦内容管理系统</a>";

$cfg_version = "3.0_final"; //请不要删除本项，否则系统无法正确接收最新漏洞或升级信息

//文档默认保存路径
//对于没有归类的文章也会保存在这个目录
//---------------------------------------------------
$cfg_arcdir = $cfg_cmspath."/html";

//模板的存放目录
$cfg_templets_dir = $cfg_cmspath."/templets";

//图片浏览器的默认路径
$cfg_medias_dir = $cfg_cmspath."/upimg";

//插件目录，这个目录是用于存放计数器、投票、评论等程序的必要动态程序
$cfg_plus_dir = $cfg_cmspath."/plus";

//扩展目录，保存RSS、网站地图、RSS地图、JS文件等扩展内容
//为了不弄太多系统目录,把这些东东都放到 $cfg_plus_dir 中
$cfg_extend_dir = $cfg_plus_dir;

//会员目录
$cfg_member_dir = $cfg_cmspath."/member";

//数据备份目录
$cfg_backup_dir = $cfg_plus_dir."/~bakdir~";

//上传的普通图片的路径,建议按默认
$cfg_image_dir = $cfg_medias_dir."/allimg";

//上传的缩略图
$ddcfg_image_dir = $cfg_medias_dir."/litimg";
//缩略图的大小限制
$cfg_ddimg_width = 200;
$cfg_ddimg_height = 150;
//图集默认显示图片的大小
$cfg_album_width = 600;

//专题列表的存放路径
$cfg_special = $cfg_cmspath."/special";

//用户投稿图片存放目录
$cfg_user_dir = $cfg_medias_dir."/userup";

//上传的软件目录
$cfg_soft_dir = $cfg_medias_dir."/soft";

//上传的多媒体文件目录
$cfg_other_medias = $cfg_medias_dir."/media";

//文件选择器可浏览的文件类型
$cfg_imgtype = "jpg|gif|png";

$cfg_softtype = "exe|zip|gz|rar|iso";

$cfg_mediatype = "swf|mpg|dat|avi|mp3|rm|rmvb|wmv|asf|vob|wma|wav|mid|mov";

//检测目录，如果你确保所有目录都已经创建，可以屏蔽这个语句
require_once(dirname(__FILE__)."/config_makenewdir.php");

//附加选项：
//-------------------------------

$cfg_specnote = 6; //专题的最大节点数

$art_shortname = ".html"; //默认扩展名，仅在命名规则不含扩展名的时候调用

//文档的默认命名规则
$cfg_df_namerule = "{Y}/{M}{D}/{aid}.html";

//类目位置的间隔符号,类目>>类目二>>类目三
$cfg_list_symbol = " &gt; ";

//新建目录的权限
//如果你使用别的属性，本程不保证程序能顺利在Linux或Unix系统运行
$cfg_dir_purview = 0777;

require_once(dirname(__FILE__)."/pub_db_mysql.php");
require_once(dirname(__FILE__)."/inc_functions.php");

?>