<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_sitemap.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
if(empty($dopost))
{
	ShowMsg("参数错误!","-1");
	exit();
}
$sm = new SiteMap();
$maplist = $sm->GetSiteMap($dopost);
$sm->Close();
if($dopost=="site")
{
	$murl = $cfg_plus_dir."/sitemap.html";
	$tmpfile = $cfg_basedir.$cfg_templets_dir."/plus/sitemap.htm";
}
else
{
	$murl = $cfg_plus_dir."/rssmap.html";
	$tmpfile = $cfg_basedir.$cfg_templets_dir."/plus/rssmap.htm";
}
$dtp = new DedeTagParse();
$dtp->LoadTemplet($tmpfile);
$dtp->SaveTo($cfg_basedir.$murl);
$dtp->Clear();
header("Content-Type: text/html; charset={$cfg_ver_lang}");
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$cfg_ver_lang}\">\r\n";
echo "<a href='$murl' target='_blank'>成功更新文件: $murl 浏览...</a>";
ClearAllLink();
?>