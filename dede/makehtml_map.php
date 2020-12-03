<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/sitemap.class.php");
require_once(DEDEINC."/dedetag.class.php");
if(empty($dopost))
{
	ShowMsg("参数错误!","-1");
	exit();
}
$sm = new SiteMap();
$maplist = $sm->GetSiteMap($dopost);
if($dopost=="site")
{
	$murl = $cfg_cmspath."/data/sitemap.html";
	$tmpfile = $cfg_basedir.$cfg_templets_dir."/plus/sitemap.htm";
}
else
{
	$murl = $cfg_cmspath."/data/rssmap.html";
	$tmpfile = $cfg_basedir.$cfg_templets_dir."/plus/rssmap.htm";
}
$dtp = new DedeTagParse();
$dtp->LoadTemplet($tmpfile);
$dtp->SaveTo($cfg_basedir.$murl);
$dtp->Clear();
echo "<a href='$murl' target='_blank'>成功更新文件: $murl 浏览...</a>";
exit();
?>