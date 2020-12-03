<?php
//------------------------------------------------
//该文件是所有涉及文档或列表读取必须引入的文件
//------------------------------------------------
global $PubFields,$cfg_mainsite,$cfg_plus_dir,$cfg_powerby,$cfg_indexname,$cfg_indexurl;
global $cfg_webname,$cfg_templets_dir,$cfg_df_style,$cfg_special,$cfg_member_dir;
//设置一些公用变量
$PubFields['phpurl'] = $cfg_mainsite.$cfg_plus_dir;
$PubFields['indexurl'] = $cfg_mainsite.$cfg_indexurl;
$PubFields['templeturl'] = $cfg_mainsite.$cfg_templets_dir;
$PubFields['memberurl'] = $cfg_mainsite.$cfg_member_dir;
$PubFields['specurl'] = $cfg_mainsite.$cfg_special;
$PubFields['indexname'] = $cfg_indexname;
$PubFields['powerby'] = $cfg_powerby;
$PubFields['webname'] = $cfg_webname;
$PubFields['templetdef'] = $cfg_templets_dir.'/'.$cfg_df_style;
$GLOBALS['pTypeArrays'] = Array();
$GLOBALS['idArrary'] = '';
//----------------------------------
//用星表示软件或Flash的等级
//----------------------------------
function GetRankStar($rank)
{
	$nstar = "";
	for($i=1;$i<=$rank;$i++) $nstar .= "★";
	for($i;$i<=5;$i++) $nstar .= "☆";
	return $nstar;
}
//-------------------------
//产品模块中供应方式的处理
//-----------------------
function SelSpType($stype){
	$tps = array('厂家直销','厂家批发','商家批发','商家零售','其它渠道');
	$rstr = "<select name='ssstype' style='width:80px'>\r\n";
	foreach($tps as $tp){
		$rstr .= ($stype==$tp ? "<option selected>$tp</option>\r\n" : "<option>$tp</option>\r\n");
	}
	$rstr .= "</select>\r\n";
	return $rstr;
}
//-----------------------------
//获得文章网址
//----------------------------
function GetFileUrl(
          $aid,$typeid,$timetag,$title,$ismake=0,$rank=0,
          $namerule="",$artdir="",$money=0,$aburl=false,$siteurl="")
{
	if($rank!=0||$ismake==-1||$typeid==0||$money>0) //动态文章
	{ return $GLOBALS['cfg_plus_dir']."/view.php?aid=$aid";}
	else
	{
		$articleRule = $namerule;
		$articleDir = MfTypedir($artdir);
		if($namerule=="") $articleRule = $GLOBALS['cfg_df_namerule'];
		if($artdir=="") $articleDir  = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_arcdir'];
		$dtime = GetDateMk($timetag);
		$articleRule = strtolower($articleRule);
		list($y,$m,$d) = explode("-",$dtime);

		$articleRule = str_replace("{typedir}",$articleDir,$articleRule);
		$articleRule = str_replace("{y}",$y,$articleRule);
		$articleRule = str_replace("{m}",$m,$articleRule);
		$articleRule = str_replace("{d}",$d,$articleRule);
		$articleRule = str_replace("{timestamp}",$timetag,$articleRule);
		$articleRule = str_replace("{aid}",$aid,$articleRule);
		$articleRule = str_replace("{cc}",dd2char($m.$d.$aid.$y),$articleRule);
		if(ereg('{p',$articleRule)){
		  $articleRule = str_replace("{pinyin}",GetPinyin($title)."_".$aid,$articleRule);
		  $articleRule = str_replace("{py}",GetPinyin($title,1)."_".$aid,$articleRule);
		}

		$articleUrl = "/".ereg_replace("^/","",$articleRule);

		//是否强制使用绝对网址
		if($aburl && $GLOBALS['cfg_multi_site']=='Y'){
			if($siteurl=="") $siteurl = $GLOBALS["cfg_basehost"];
			$articleUrl = $siteurl.$articleUrl;
		}

		return $articleUrl;
	}
}
//获得新文件网址
//本函数会自动创建目录
function GetFileNewName(
         $aid,$typeid,$timetag,$title,$ismake=0,$rank=0,
         $namerule="",$artdir="",$money=0,$siterefer="",
         $sitepath="",$moresite="",$siteurl="")
{
	if($rank!=0||$ismake==-1||$typeid==0||$money>0){ //动态文章
		return $GLOBALS['cfg_plus_dir']."/view.php?aid=$aid";
	}
	else
	{
		 $articleUrl = GetFileUrl(
		               $aid,$typeid,$timetag,$title,$ismake,$rank,
		               $namerule,$artdir,$money);
		 $slen = strlen($articleUrl)-1;
		 for($i=$slen;$i>=0;$i--){
		    if($articleUrl[$i]=="/"){ $subpos = $i; break; }
		 }
		 $okdir = substr($articleUrl,0,$subpos);
		 CreateDir($okdir,$siterefer,$sitepath);
	}
	return $articleUrl;
}
//--------------------------
//获得指定类目的URL链接
//对于使用封面文件和单独页面的情况，强制使用默认页名称
//-------------------------
function GetTypeUrl($typeid,$typedir,$isdefault,$defaultname,$ispart,$namerule2,$siteurl="")
{
	if($isdefault==-1)
	{ $reurl = $GLOBALS["cfg_plus_dir"]."/list.php?tid=".$typeid; }
	else if($ispart>0)
	{ $reurl = "$typedir/".$defaultname; }
	else
	{
		if($isdefault==0)
		{
			$reurl = str_replace("{page}","1",$namerule2);
			$reurl = str_replace("{tid}",$typeid,$reurl);
			$reurl = str_replace("{typedir}",$typedir,$reurl);
		}
		else $reurl = "$typedir/".$defaultname;
	}
	$reurl = ereg_replace("/{1,}","/",$reurl);
	if($GLOBALS['cfg_multi_site']=='Y'){
		if($siteurl=="") $siteurl = $GLOBALS["cfg_basehost"];
		if($siteurl!="abc") $reurl = $siteurl.$reurl;
	}
  $reurl = eregi_replace("{cmspath}",$GLOBALS['cfg_cmspath'],$reurl);
	return $reurl;
}

//魔法变量，用于获取两个可变的值
//------------------------
function MagicVar($v1,$v2)
{
  if($GLOBALS['autoindex']%2==0) return $v1;
  else return $v2;
}

//获取上级ID列表
function GetParentIDS($tid,&$dsql)
{
	global $_Cs;
	$GLOBALS['pTypeArrays'][] = $tid;

	if(!is_array($_Cs)){ require_once(dirname(__FILE__)."/../data/cache/inc_catalog_base.php"); }

	if(!isset($_Cs[$tid]) || $_Cs[$tid][0]==0){
		return $GLOBALS['pTypeArrays'];
	}
	else{
		return GetParentIDS($_Cs[$tid][0],$dsql);
	}
}

//----------------------
//获取一个类目的顶级类目ID
//----------------------
function SpGetTopID($tid){
  global $_Cs;
  if(!is_array($_Cs)){ require_once(dirname(__FILE__)."/../data/cache/inc_catalog_base.php"); }
  if($v[$tid][0]==0) return $tid;
  else return SpGetTopID($tid);
}

//----------------------
//获取一个类目的所有顶级栏目ID
//----------------------
global $TopIDS;
function SpGetTopIDS($tid){
  global $_Cs,$TopIDS;
  if(!is_array($_Cs)){ require_once(dirname(__FILE__)."/../data/cache/inc_catalog_base.php"); }
  $TopIDS[] = $tid;
  if($_Cs[$tid][0]==0) return $TopIDS;
  else return SpGetTopIDS($tid);
}

//-----------------------------
// 返回与某个目相关的下级目录的类目ID列表(删除类目或文章时调用)
//由于PHP有些版本存在Bug,不能直接使用同一数组在递归逻辑,只能复制副本传递给函数
//-----------------------------
function TypeGetSunTypes($ID,&$dsql,$channel=0)
{
		global $_Cs;
		$GLOBALS['idArray'] = array();
		if( !is_array($_Cs) ){ require_once(dirname(__FILE__)."/../data/cache/inc_catalog_base.php"); }
		TypeGetSunTypesLogic($ID,$_Cs,$channel);
		return $GLOBALS['idArray'];
}

function TypeGetSunTypesLogic($ID,$sArr,$channel=0)
{
		if($ID!=0) $GLOBALS['idArray'][$ID] = $ID;
		foreach($sArr as $k=>$v)
		{
			if( $v[0]==$ID && ($channel==0 || $v[1]==$channel ))
			{
				 TypeGetSunTypesLogic($k,$sArr,$channel);
			}
		}
}

//--------------------------------
//获得某ID的下级ID(包括本身)的SQL语句“($tb.typeid=id1 or $tb.typeid=id2...)”
//-----------------------------
function TypeGetSunID($ID,&$dsql,$tb="#@__archives",$channel=0,$onlydd=false)
{
		$GLOBALS['idArray'] = array();
		TypeGetSunTypes($ID,$dsql,$channel);
		$rquery = "";
		foreach($GLOBALS['idArray'] as $k=>$v)
		{
			if($onlydd){
				$rquery .= ($rquery=='' ? $k : ",{$k}");
			}else{
			  if($tb!="")
			     $rquery .= ($rquery!='' ? " Or {$tb}.typeid='$k' " : " {$tb}.typeid='$k' ");
		    else
		  	   $rquery .= ($rquery!='' ? " Or typeid='$k' " : " typeid='$k' ");
		  }
		}
		if($onlydd) return $rquery;
		else return " (".$rquery.") ";
}
//栏目目录规则
function MfTypedir($typedir)
{
  global $cfg_cmspath;
  $typedir = eregi_replace("{cmspath}",$cfg_cmspath,$typedir);
  $typedir = ereg_replace("/{1,}","/",$typedir);
  return $typedir;
}
//模板目录规则
function MfTemplet($tmpdir)
{
  global $cfg_df_style;
  $tmpdir = eregi_replace("{style}",$cfg_df_style,$tmpdir);
  $tmpdir = ereg_replace("/{1,}","/",$tmpdir);
  return $tmpdir;
}
//获取网站搜索的热门关键字
function GetHotKeywords(&$dsql,$num=8,$nday=365,$klen=16,$orderby='count'){
	global $cfg_phpurl,$cfg_cmspath;
	$nowtime = mytime();
	$num = @intval($num);
	$nday = @intval($nday);
	$klen = @intval($klen);
	if(empty($nday)) $nday = 365;
	if(empty($num)) $num = 6;
	if(empty($klen)) $klen = 16;
	$klen = $klen+1;
	$mintime = $nowtime - ($nday * 24 * 3600);
	if(empty($orderby)) $orderby = 'count';
	$dsql->SetQuery("Select keyword,istag From #@__search_keywords where lasttime>$mintime And length(keyword)<$klen order by $orderby desc limit 0,$num");
  $dsql->Execute('hw');
  $hotword = "";
  while($row=$dsql->GetArray('hw')){
 		 if($row['istag']==1) $hotword .= "　<a href='".$cfg_cmspath."/tag.php?/{$row['keyword']}/'>".$row['keyword']."</a> ";
 		 else $hotword .= "　<a href='".$cfg_phpurl."/search.php?keyword=".urlencode($row['keyword'])."&searchtype=titlekeyword'>".$row['keyword']."</a> ";
 	}
 	return $hotword;
}
//
function FormatScript($atme){
	if($atme=="&nbsp;") return "";
	else return trim(html2text($atme));
}
//------------------------------
//获得自由列表的网址
//------------------------------
function GetFreeListUrl($lid,$namerule,$listdir,$defaultpage,$nodefault){
	$listdir = str_replace('{cmspath}',$GLOBALS['cfg_cmspath'],$listdir);
	if($nodefault==1){
	  $okfile = str_replace('{page}','1',$namerule);
	  $okfile = str_replace('{listid}',$lid,$okfile);
	  $okfile = str_replace('{listdir}',$listdir,$okfile);
  }else{
  	$okfile = $listdir.'/'.$defaultpage;
  }
	$okfile = str_replace("\\","/",$okfile);
	$trueFile = $GLOBALS['cfg_basedir'].$okfile;
	if(!file_exists($trueFile)){
		 $okfile = $GLOBALS['cfg_phpurl']."/freelist.php?lid=$lid";
	}
	return $okfile;
}
//----------
//判断图片可用性
function CkLitImageView($imgsrc,$imgwidth){
	$imgsrc = trim($imgsrc);
	if(!empty($imgsrc) && eregi('^http',$imgsrc)){
		 $imgsrc = $cfg_mainsite.$imgsrc;
	}
	if(!empty($imgsrc) && !eregi("img/dfpic\.gif",$imgsrc)){
		return "<img src='".$imgsrc."' width=80 align=left>";
	}
	return "";
}
//----------
//使用绝对网址
function Gmapurl($gurl){
	if(!eregi("http://",$gurl)) return $GLOBALS['cfg_basehost'].$gurl;
	else return $gurl;
}

//----------------
//获得图书的URL
//----------------
function GetBookUrl($bid,$title,$gdir=0)
{
	global $cfg_cmspath;
	if($gdir==1) $bookurl = "{$cfg_cmspath}/book/".DedeID2Dir($bid);
	else $bookurl = "{$cfg_cmspath}/book/".DedeID2Dir($bid).'/'.GetPinyin($title).'-'.$bid.'.html';
	return $bookurl;
}

//-----------------
//根据ID生成目录
//-----------------
function DedeID2Dir($aid)
{
	$n = ceil($aid / 1000);
	return $n;
}

?>