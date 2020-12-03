<?php
if(!isset($cfg_basedir))
{
	include_once(dirname(__FILE__)."/config.php");
}
if(empty($uploadfile))
{
	$uploadfile = '';
}
if(empty($uploadmbtype))
{
	$uploadmbtype = "软件类型";
}
if(empty($bkurl))
{
	$bkurl = 'select_soft.php';
}
if(!is_uploaded_file($uploadfile))
{
	ShowMsg("你没有选择上传的文件或选择的文件大小超出限制!","-1");
	exit();
}
$uploadfile_name = trim(ereg_replace("[ \r\n\t\*\%\\/\?><\|\":]{1,}",'',$uploadfile_name));
if(!eregi("\.(".$cfg_softtype.")",$uploadfile_name))
{
	ShowMsg("你所上传的{$uploadmbtype}不在许可列表，请更改系统对扩展名限定的配置！","-1");
	exit();
}
$nowtme = time();
if($activepath==$cfg_soft_dir)
{
	$newdir = MyDate('Ym',$nowtme);
	$activepath = $activepath.'/'.$newdir;
	if(!is_dir($cfg_basedir.$activepath))
	{
		MkdirAll($cfg_basedir.$activepath,$cfg_dir_purview);
		CloseFtp();
	}
}
$filename = $cuserLogin->getUserID().'_'.MyDate('dHis',$nowtme);
$fs = explode('.',$uploadfile_name);
if(eregi($cfg_not_allowall,$fs[count($fs)-1]))
{
	ShowMsg("你上传了某些可能存在不安全因素的文件，系统拒绝操作！",'javascript:;');
	exit();
}
$filename = $filename.'.'.$fs[count($fs)-1];
$fullfilename = $cfg_basedir.$activepath.'/'.$filename;
$fullfileurl = $activepath.'/'.$filename;
move_uploaded_file($uploadfile,$fullfilename) or die("上传文件到 $fullfilename 失败！");
@unlink($uploadfile);
if($uploadfile_type == 'application/x-shockwave-flash')
{
	$mediatype=2;
}
else if(eregi('audio|media|video',$uploadfile_type))
{
	$mediatype=3;
}
else
{
	$mediatype=4;
}
$inquery = "INSERT INTO #@__uploads(arcid,title,url,mediatype,width,height,playtime,filesize,uptime,mid)
   VALUES ('0','$filename','$fullfileurl','$mediatype','0','0','0','{$uploadfile_size}','{$nowtme}','".$cuserLogin->getUserID()."'); ";

$dsql->ExecuteNoneQuery($inquery);
ShowMsg("成功上传文件！",$bkurl."?comeback=".urlencode($filename)."&f=$f&activepath=".urlencode($activepath)."&d=".time());
exit();
?>