<?
//----------------------------------
//用星表示软件或Flash的等级
//----------------------------------
function GetRankStar($rank)
{
	$nstar = "";
	for($i=1;$i<=$rank;$i++){
		$nstar .= "★";
	}
	for($i;$i<=5;$i++){
		$nstar .= "☆";
	}
	return $nstar;
}
//-----------------------------
//获得文章网址
//----------------------------
function GetFileUrl($aid,$typeid,$timetag,$title,$ismake=0,$rank=0,$namerule="",$artdir="",$money=0)
{
	if($rank!=0||$ismake==-1||$typeid==0||$money>0) //动态文章
	{ return $GLOBALS['cfg_plus_dir']."/view.php?aid=$aid";}
	else
	{
		$articleRule = $namerule;
		$articleDir = $artdir;
		if($namerule=="") $articleRule = $GLOBALS['cfg_df_namerule'];
		if($artdir=="") $articleDir  = $GLOBALS['cfg_arcdir'];
		$dtime = GetDateMk($timetag);
		$articleRule = strtolower($articleRule);
		list($y,$m,$d) = explode("-",$dtime);
		$articleRule = str_replace("{y}",$y,$articleRule);
		$articleRule = str_replace("{m}",$m,$articleRule);
		$articleRule = str_replace("{d}",$d,$articleRule);
		$articleRule = str_replace("{d}",$d,$articleRule);
		$articleRule = str_replace("{aid}",$aid,$articleRule);
		if(ereg("{pinyin}",$articleRule)){
				$articleRule = str_replace("{pinyin}",GetPinyin($title)."_".$aid,$articleRule);
		}
		$articleUrl = $articleDir."/".$articleRule;
		return $articleUrl;
	}
}
//获得新文件网址
//本函数会自动创建目录
function GetFileNewName($aid,$typeid,$timetag,$title,$ismake=0,$rank=0,$namerule="",$artdir="",$money=0)
{
	if($rank!=0||$ismake==-1||$typeid==0||$money>0){ //动态文章
		return $GLOBALS['cfg_plus_dir']."/view.php?aid=$aid";
	}
	else
	{
		 $articleUrl = GetFileUrl($aid,$typeid,$timetag,$title,$ismake,$rank,$namerule,$artdir,$money);
		 $slen = strlen($articleUrl)-1;
		 for($i=$slen;$i>=0;$i--){
		  if($articleUrl[$i]=="/"){ $subpos = $i; break; }
		 }
		 $okdir = substr($articleUrl,0,$subpos);
		 CreateDir($okdir);
	}
	return $articleUrl;
}
//--------------------------
//获得指定类目的URL链接
//对于使用封面文件和单独页面的情况，强制使用默认页名称
//-------------------------
function GetTypeUrl($typeid,$typedir,$isdefault,$defaultname,$ispart,$namerule2)
{
	if($isdefault==-1)
	{ return $GLOBALS["cfg_plus_dir"]."/list.php?tid=".$typeid; }
	else if($ispart>0)
	{ return "$typedir/".$defaultname; }
	else{
		if($isdefault==0) return "$typedir/".str_replace("{page}","1",$namerule2);
		else return "$typedir/".$defaultname;
	}
}
?>