<?
require("config.php");
require("inc_makepartcode.php");
$testcode = stripslashes(trim($testcode));
if($job=="save")
{
	$testcode = str_replace("\r","",$testcode);
	$testcode = ereg_replace("\n{1,}","\n",$testcode);
	$modefilename = $base_dir.$mod_dir."/主页向导/".$selmode;
	$fp = fopen($modefilename,"w") or die("<script>alert('文件路径 $modefilename 无效或权限不足！');history.go(-1);</script>");
	fwrite($fp,$testcode);
	fclose($fp);
	ShowMsg("成功更改模板！","add_home_page.php?modname=$selmode");
}
$maprt= new MakePartCode();
if($job=="make")
{
	$mfilename = $base_dir."/".$filename;
	$fp = fopen($mfilename,"w") or die("<script>alert('文件路径 $mfilename 无效或权限不足！');history.go(-1);</script>");
	fwrite($fp,$maprt->ParTemp($testcode));
	fclose($fp);
	ShowMsg("成功创建文件！","/$filename");
}
else
{
	echo $maprt->ParTemp($testcode);
}
?>