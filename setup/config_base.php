<?
/*************************************************
本文件的信息不建议用户自行更改，否则发生意外自行负责
**************************************************/

//禁止用户提交某些特殊变量
$ckvs = Array('_GET','_POST','_COOKIE','_FILES');
foreach($ckvs as $ckv){
    if(is_array($$ckv)){ 
    	foreach($$ckv AS $key => $value) 
    	   if(eregi("^cfg_|globals",$key)) unset(${$ckv}[$key]);
    }
}


require_once(dirname(__FILE__)."/config_hand.php");
if(!isset($needFilter)) $needFilter = false;
$registerGlobals = @ini_get("register_globals");
$isUrlOpen = @ini_get("allow_url_fopen");
$isMagic = @ini_get("magic_quotes_gpc");
$isSafeMode = @ini_get("safe_mode");

//检测系统是否注册外部变量
if(!$isMagic) require_once(dirname(__FILE__)."/config_rglobals_magic.php");
else if(!$registerGlobals || $needFilter) require_once(dirname(__FILE__)."/config_rglobals.php");

unset($_ENV,$HTTP_ENV_VARS,$_REQUEST,$HTTP_POST_VARS,$HTTP_GET_VARS,$HTTP_POST_FILES,$HTTP_COOKIE_VARS);

//Session保存路径
$sessSavePath = dirname(__FILE__)."/sessions/";
if(is_writeable($sessSavePath) && is_readable($sessSavePath)){ session_save_path($sessSavePath); }

//对于仅需要简单ＳＱＬ操作的页面，引入本文件前把此$__ONLYDB设为true，可避免引入不必要的文件
if(!isset($__ONLYDB)) $__ONLYDB = false;

//站点根目录
$ndir = str_replace("\\","/",dirname(__FILE__));
$cfg_basedir = eregi_replace($cfg_cmspath."/include[/]{0,1}$","",$ndir);
if($cfg_multi_site == '是') $cfg_mainsite = $cfg_basehost;
else  $cfg_mainsite = "";

//数据库连接信息
$cfg_dbhost = '~dbhost~';
$cfg_dbname = '~dbname~';
$cfg_dbuser = '~dbuser~';
$cfg_dbpwd = '~dbpwd~';
$cfg_dbprefix = '~dbprefix~';
$cfg_db_language = '~dblang~';

//模板的存放目录
$cfg_templets_dir = $cfg_cmspath.'/templets';
$cfg_templeturl = $cfg_mainsite.$cfg_templets_dir;

//插件目录，这个目录是用于存放计数器、投票、评论等程序的必要动态程序
$cfg_plus_dir = $cfg_cmspath.'/plus';
$cfg_phpurl = $cfg_mainsite.$cfg_plus_dir;

//会员目录
$cfg_member_dir = $cfg_cmspath.'/member';
$cfg_memberurl = $cfg_mainsite.$cfg_member_dir;

//会员个人空间目录#new
$cfg_space_dir = $cfg_cmspath.'/space';
$cfg_spaceurl = $cfg_basehost.$cfg_space_dir;

$cfg_medias_dir = $cfg_cmspath.$cfg_medias_dir;
//上传的普通图片的路径,建议按默认
$cfg_image_dir = $cfg_medias_dir.'/allimg';
//上传的缩略图
$ddcfg_image_dir = $cfg_medias_dir.'/litimg';
//专题列表的存放路径
$cfg_special = $cfg_cmspath.'/special';
//用户投稿图片存放目录
$cfg_user_dir = $cfg_medias_dir.'/userup';
//上传的软件目录
$cfg_soft_dir = $cfg_medias_dir.'/soft';
//上传的多媒体文件目录
$cfg_other_medias = $cfg_medias_dir.'/media';

//软件摘要信息，****请不要删除本项**** 否则系统无法正确接收系统漏洞或升级信息
//-----------------------------
$cfg_softname = "织梦内容管理系统";
$cfg_soft_enname = "DedeCms OX";
$cfg_soft_devteam = "IT柏拉图";
$cfg_version = '3_1';

//默认扩展名，仅在命名规则不含扩展名的时候调用
$art_shortname = '.html';
//文档的默认命名规则
$cfg_df_namerule = '{typedir}/{Y}/{M}{D}/{aid}.html';
//新建目录的权限，如果你使用别的属性，本程不保证程序能顺利在Linux或Unix系统运行
$cfg_dir_purview = '0777';

//引入数据库类和常用函数
require_once(dirname(__FILE__).'/pub_db_mysql.php');
require_once(dirname(__FILE__).'/config_passport.php');

if($cfg_pp_need=='否'){
	$cfg_pp_login = $cfg_cmspath.'/member/login.php';
  $cfg_pp_exit = $cfg_cmspath.'/member/index_do.php?fmdo=login&dopost=exit';
  $cfg_pp_reg = $cfg_cmspath.'/member/index_do.php?fmdo=user&dopost=regnew';
}

if(!$__ONLYDB){
	require_once(dirname(__FILE__).'/inc_functions.php');
}

?>