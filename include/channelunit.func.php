<?php
if(!defined('DEDEINC'))
{
	exit("dedecms");
}

if(!isset($cfg_mainsite))
{
	extract($GLOBALS, EXTR_SKIP);
}
global $PubFields,$pTypeArrays,$idArrary,$envs,$v1,$v2;
$pTypeArrays = array();
$idArrary = array();
$PubFields = array();
$envs = array();
$PubFields['phpurl'] = $cfg_phpurl;
$PubFields['indexurl'] = $cfg_mainsite.$cfg_indexurl;
$PubFields['templeturl'] = $cfg_templeturl;
$PubFields['memberurl'] = $cfg_memberurl;
$PubFields['specurl'] = $cfg_specialurl;
$PubFields['indexname'] = $cfg_indexname;
$PubFields['templetdef'] = $cfg_templets_dir.'/'.$cfg_df_style;
$envs['typeid'] = 0;
$envs['reid'] = 0;
$envs['aid'] = 0;
$envs['keyword'] = '';
$envs['idlist'] = '';


//用星表示软件或Flash的等级
function GetRankStar($rank)
{
	$nstar = "";
	for($i=1;$i<=$rank;$i++)
	{
		$nstar .= "★";
	}
	for($i;$i<=5;$i++)
	{
		$nstar .= "☆";
	}
	return $nstar;
}

//获得文章网址
/*************************************************
如果要获得文件的路径，直接用
GetFileUrl($aid,$typeid,$timetag,$title,$ismake,$rank,$namerule,$typedir,$money)
即是不指定站点参数则返回相当对根目录的真实路径
**************************************************/
function GetFileUrl($aid,$typeid,$timetag,$title,$ismake=0,$rank=0,$namerule='',$typedir='',
$money=0, $filename='',$moresite=0,$siteurl='',$sitepath='')
{
	$articleUrl = GetFileName($aid,$typeid,$timetag,$title,$ismake,$rank,$namerule,$typedir,$money,$filename);
	$sitepath = MfTypedir($sitepath);

	//是否强制使用绝对网址
	if($GLOBALS['cfg_multi_site']=='Y')
	{
		if($siteurl=='')
		{
			$siteurl = $GLOBALS['cfg_basehost'];
		}
		if($moresite==1)
		{
			$articleUrl = ereg_replace("^".$sitepath,'',$articleUrl);
		}
		if(!ereg('http:',$articleUrl))
		{
			$articleUrl = $siteurl.$articleUrl;
		}
	}

	return $articleUrl;
}

//获得新文件名(本函数会自动创建目录)
function GetFileNewName($aid,$typeid,$timetag,$title,$ismake=0,$rank=0,$namerule='',$typedir='',$money=0,$filename='')
{
	$articlename = GetFileName($aid,$typeid,$timetag,$title,$ismake,$rank,$namerule,$typedir,$money,$filename);
	if(ereg("\?",$articlename))
	{
		return $articlename;
	}
	$slen = strlen($articlename)-1;
	for($i=$slen;$i>=0;$i--)
	{
		if($articlename[$i]=='/')
		{
			$subpos = $i;
			break;
		}
	}
	$okdir = substr($articlename,0,$subpos);
	CreateDir($okdir);
	return $articlename;
}

//获得文件相对于主站点根目录的物理文件名(动态网址返回url)
function GetFileName($aid,$typeid,$timetag,$title,$ismake=0,$rank=0,$namerule='',$typedir='',$money=0,$filename='')
{
	global $cfg_rewrite;
	if($rank!=0 || $ismake==-1 || $typeid==0 || $money>0)
	{
		//动态文章
		if($cfg_rewrite == 'Y')
		{
			return $GLOBALS["cfg_plus_dir"]."/view-".$aid.'-1.html';
		}
		else
		{
			return $GLOBALS['cfg_phpurl']."/view.php?aid=$aid";
		}
	}
	else
	{
		$articleDir = MfTypedir($typedir);
		$articleRule = strtolower($namerule);
		if($articleRule=='')
		{
			$articleRule = strtolower($GLOBALS['cfg_df_namerule']);
		}
		if($typedir=='')
		{
			$articleDir  = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_arcdir'];
		}
		$dtime = GetDateMk($timetag);
		list($y,$m,$d) = explode('-',$dtime);
		$arr_rpsource = array('{typedir}','{y}','{m}','{d}','{timestamp}','{aid}','{cc}');
		$arr_rpvalues = array($articleDir,$y, $m, $d, $timetag, $aid, dd2char($m.$d.$aid.$y));
		if($filename != '')
		{
			$articleRule = dirname($articleRule).'/'.$filename.$GLOBALS['cfg_df_ext'];
		}
		$articleRule = str_replace($arr_rpsource,$arr_rpvalues,$articleRule);
		if(ereg('\{p',$articleRule))
		{
			$articleRule = str_replace('{pinyin}',GetPinyin($title).'_'.$aid,$articleRule);
			$articleRule = str_replace('{py}',GetPinyin($title,1).'_'.$aid,$articleRule);
		}
		$articleUrl = '/'.ereg_replace('^/','',$articleRule);
		return $articleUrl;
	}
}

//获得指定类目的URL链接
//对于使用封面文件和单独页面的情况，强制使用默认页名称
function GetTypeUrl($typeid,$typedir,$isdefault,$defaultname,$ispart,$namerule2,$moresite=0,$siteurl='',$sitepath='')
{
	$typedir = MfTypedir($typedir);
	$sitepath = MfTypedir($sitepath);
	if($isdefault==-1)
	{
		//动态
		$reurl = $GLOBALS['cfg_phpurl']."/list.php?tid=".$typeid;
	}
	else if($ispart==2)
	{
		//跳转网址
		$reurl = $typedir;
		return $reurl;
	}
	else
	{
		if($isdefault==0 && $ispart==0)
		{
			$reurl = str_replace("{page}","1",$namerule2);
			$reurl = str_replace("{tid}",$typeid,$reurl);
			$reurl = str_replace("{typedir}",$typedir,$reurl);
		} else {
			$reurl = $typedir.'/'.$defaultname;
		}
	}

	if( !eregi("^http://",$reurl) ) {
		$reurl = ereg_replace("/{1,}",'/',$reurl);
	}
	
	if($GLOBALS['cfg_multi_site']=='Y')
	{
		if($siteurl=='') {
			$siteurl = $GLOBALS['cfg_basehost'];
		}
		if($moresite==1 ) {
			$reurl = ereg_replace("^".$sitepath,'',$reurl);
		}
		if( !eregi('^http://', $reurl) ) {
			$reurl = $siteurl.$reurl;
		}
	}
	return $reurl;
}

//魔法变量，用于获取两个可变的值
function MagicVar($v1,$v2)
{
	return $GLOBALS['autoindex']%2==0 ? $v1 : $v2;
}

//获取某个类目的所有上级栏目id
function GetTopids($tid)
{
	$arr = GetParentIds($tid);
	return join(',',$arr);
}

//获取上级ID列表
function GetParentIds($tid)
{
	global $_Cs;
	$GLOBALS['pTypeArrays'][] = $tid;
	if(!is_array($_Cs))
	{
		require_once(DEDEROOT."/data/cache/inc_catalog_base.inc");
	}
	if(!isset($_Cs[$tid]) || $_Cs[$tid][0]==0)
	{
		return $GLOBALS['pTypeArrays'];
	}
	else
	{
		return GetParentIds($_Cs[$tid][0]);
	}
}

//获取一个类目的顶级类目id
function GetTopid($tid)
{
	global $_Cs;
	if(!is_array($_Cs))
	{
		require_once(DEDEROOT."/data/cache/inc_catalog_base.inc");
	}
	if(!isset($_Cs[$tid][0]) || $_Cs[$tid][0]==0)
	{
		return $tid;
	}
	else
	{
		return GetTopid($_Cs[$tid][0]);
	}
}

//获得某id的所有下级id
function GetSonIds($id,$channel=0,$addthis=true)
{
	global $_Cs;
	$GLOBALS['idArray'] = array();
	if( !is_array($_Cs) )
	{
		require_once(DEDEROOT."/data/cache/inc_catalog_base.inc");
	}
	GetSonIdsLogic($id,$_Cs,$channel,$addthis);
	$rquery = join(',',$GLOBALS['idArray']);
	return $rquery;
}

//递归逻辑
function GetSonIdsLogic($id,$sArr,$channel=0,$addthis=false)
{
	if($id!=0 && $addthis)
	{
		$GLOBALS['idArray'][$id] = $id;
	}
	foreach($sArr as $k=>$v)
	{
		if( $v[0]==$id && ($channel==0 || $v[1]==$channel ))
		{
			GetSonIdsLogic($k,$sArr,$channel,true);
		}
	}
}

//栏目目录规则
function MfTypedir($typedir)
{
	if(eregi("^http:",$typedir)) return $typedir;
	$typedir = str_replace("{cmspath}",$GLOBALS['cfg_cmspath'],$typedir);
	$typedir = ereg_replace("/{1,}","/",$typedir);
	return $typedir;
}

//模板目录规则
function MfTemplet($tmpdir)
{
	$tmpdir = str_replace("{style}",$GLOBALS['cfg_df_style'],$tmpdir);
	$tmpdir = ereg_replace("/{1,}","/",$tmpdir);
	return $tmpdir;
}

//清除用于js的空白块
function FormatScript($atme)
{
	return $atme=='&nbsp;' ? '' : $atme;
}

//给属性默认值
function FillAttsDefault(&$atts,$attlist)
{
	$attlists = explode(',',$attlist);
	for($i=0;isset($attlists[$i]);$i++)
	{
		list($k,$v) = explode('|',$attlists[$i]);
		if(!isset($atts[$k]))
		{
			$atts[$k] = $v;
		}
	}
}

//给块标记赋值
function MakeOneTag(&$dtp,&$refObj)
{
	$alltags = array();

	//读取自由调用tag列表
	$dh = dir(DEDEINC.'/taglib');
	while($filename = $dh->read())
	{
		if(ereg("\.lib\.",$filename))
		{
			$alltags[] = str_replace('.lib.php','',$filename);
		}
	}
	$dh->Close();

	//遍历tag元素
	if(!is_array($dtp->CTags))
	{
		return '';
	}
	foreach($dtp->CTags as $tagid=>$ctag)
	{
		$tagname = $ctag->GetName();
		if($tagname=='field')
		{
			$vname = $ctag->GetAtt('name');
			if(isset($refObj->Fields[$vname]))
			{
				$dtp->Assign($tagid,$refObj->Fields[$vname]);
			}
			continue;
		}

		//由于考虑兼容性，原来文章调用使用的标记别名统一保留，这些标记实际调用的解析文件为inc_arclist.php
		if(ereg("^(artlist|likeart|hotart|imglist|imginfolist|coolart|specart|autolist)$",$tagname))
		{
			$tagname='arclist';
		}
		if($tagname=='friendlink')
		{
			$tagname='flink';
		}
		if(in_array($tagname,$alltags))
		{
			$filename = DEDEINC.'/taglib/'.$tagname.'.lib.php';
			include_once($filename);
			$funcname = 'lib_'.$tagname;
			$dtp->Assign($tagid,$funcname($ctag,$refObj));
		}
	}
}

//获取某栏目的url
function GetOneTypeUrlA($typeinfos)
{
	return GetTypeUrl($typeinfos['id'],MfTypedir($typeinfos['typedir']),$typeinfos['isdefault'],$typeinfos['defaultname'],
	$typeinfos['ispart'],$typeinfos['namerule2'],$typeinfos['moresite'],$typeinfos['siteurl'],$typeinfos['sitepath']);
}

//设置全局环境变量
function SetSysEnv($typeid=0,$typename='',$aid=0,$title='',$curfile='')
{
	global $_sys_globals;
	if(empty($_sys_globals['curfile']))
	{
		$_sys_globals['curfile'] = $curfile;
	}
	if(empty($_sys_globals['typeid']))
	{
		$_sys_globals['typeid'] = $typeid;
	}
	if(empty($_sys_globals['typename']))
	{
		$_sys_globals['typename'] = $typename;
	}
	if(empty($_sys_globals['aid']))
	{
		$_sys_globals['aid'] = $aid;
	}
}

//获得图书的URL
function GetBookUrl($bid,$title,$gdir=0)
{
	global $cfg_cmspath;
	$bookurl = $gdir==1 ?
	"{$cfg_cmspath}/book/".DedeID2Dir($bid) : "{$cfg_cmspath}/book/".DedeID2Dir($bid).'/'.GetPinyin($title).'-'.$bid.'.html';
	return $bookurl;
}

//根据ID生成目录
function DedeID2Dir($aid)
{
	$n = ceil($aid / 1000);
	return $n;
}

//获得自由列表的网址
function GetFreeListUrl($lid,$namerule,$listdir,$defaultpage,$nodefault){
	$listdir = str_replace('{cmspath}',$GLOBALS['cfg_cmspath'],$listdir);
	if($nodefault==1)
	{
		$okfile = str_replace('{page}','1',$namerule);
		$okfile = str_replace('{listid}',$lid,$okfile);
		$okfile = str_replace('{listdir}',$listdir,$okfile);
	}
	else
	{
		$okfile = $GLOBALS['cfg_phpurl']."/freelist.php?lid=$lid";
		return $okfile;
	}
	$okfile = str_replace("\\","/",$okfile);
	$okfile = str_replace("//","/",$okfile);
	$trueFile = $GLOBALS['cfg_basedir'].$okfile;
	if(!@file_exists($trueFile))
	{
		$okfile = $GLOBALS['cfg_phpurl']."/freelist.php?lid=$lid";
	}
	return $okfile;
}

//获取网站搜索的热门关键字
function GetHotKeywords(&$dsql,$num=8,$nday=365,$klen=16,$orderby='count')
{
	global $cfg_phpurl,$cfg_cmspath;
	$nowtime = time();
	$num = @intval($num);
	$nday = @intval($nday);
	$klen = @intval($klen);
	if(empty($nday))
	{
		$nday = 365;
	}
	if(empty($num))
	{
		$num = 6;
	}
	if(empty($klen))
	{
		$klen = 16;
	}
	$klen = $klen+1;
	$mintime = $nowtime - ($nday * 24 * 3600);
	if(empty($orderby))
	{
		$orderby = 'count';
	}
	$dsql->SetQuery("Select keyword From #@__search_keywords where lasttime>$mintime And length(keyword)<$klen order by $orderby desc limit 0,$num");
	$dsql->Execute('hw');
	$hotword = "";
	while($row=$dsql->GetArray('hw'))
	{
		$hotword .= "　<a href='".$cfg_phpurl."/search.php?keyword=".urlencode($row['keyword'])."&searchtype=titlekeyword'>".$row['keyword']."</a> ";
	}
	return $hotword;
}

//使用绝对网址
function Gmapurl($gurl)
{
	return eregi("http://",$gurl) ? $gurl : $GLOBALS['cfg_basehost'].$gurl;
}

//引用回复标记处理
function Quote_replace($quote)
{
	$quote = str_replace('{quote}','<div class="decmt-box">',$quote);
	$quote = str_replace('{title}','<div class="decmt-title"><span class="username">',$quote);
	$quote = str_replace('{/title}','</span></div>',$quote);
	$quote = str_replace('&lt;br/&gt;','<br>',$quote);
	$quote = str_replace('{content}','<div class="decmt-content">',$quote);
	$quote = str_replace('{/content}','</div>',$quote);
	$quote = str_replace('{/quote}','</div>',$quote);
	return $quote;
}

?>