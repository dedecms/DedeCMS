<?php
require_once(dirname(__FILE__)."/config.php");
$cfg_txttype = "htm|html|tpl|txt";
if(empty($uploadfile))
{
	$uploadfile = "";
}
if(!is_uploaded_file($uploadfile))
{
	ShowMsg("你没有选择上传的文件!","-1");
	exit();
}
if(!ereg("^text",$uploadfile_type))
{
	ShowMsg("你上传的不是文本类型附件!","-1");
	exit();
}
if(!eregi("\.(".$cfg_txttype.")",$uploadfile_name))
{
	ShowMsg("你所上传的模板文件类型不能被识别，只允许htm、html、tpl、txt扩展名！","-1");
	exit();
}
if($filename!='')
{
	$filename = trim(ereg_replace("[ \r\n\t\*\%\\/\?><\|\":]{1,}",'',$filename));
}
else
{
	$uploadfile_name = trim(ereg_replace("[ \r\n\t\*\%\\/\?><\|\":]{1,}",'',$uploadfile_name));
	$filename = $uploadfile_name;
	if($filename=='' || !eregi("\.(".$cfg_txttype.")",$filename))
	{
		ShowMsg("你所上传的文件存在问题，请检查文件类型是否适合！","-1");
		exit();
	}
}
$fullfilename = $cfg_basedir.$activepath."/".$filename;
move_uploaded_file($uploadfile,$fullfilename) or die("上传文件到 $fullfilename 失败！");
@unlink($uploadfile);
ShowMsg("成功上传文件！","select_templets.php?comeback=".urlencode($filename)."&f=$f&activepath=".urlencode($activepath)."&d=".time());
exit();
?>