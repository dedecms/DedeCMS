<?php
require_once(dirname(__FILE__)."/config.php");
$action = isset($action) ? trim($action) : '';
if(empty($action))
{

	include DEDEADMIN.'/templets/imagecut.htm';
}
elseif($action == 'cut')
{
	require_once(DEDEINC.'/image.func.php');

	if(!is_file($cfg_basedir.$file))
	{
		ShowMsg("对不起，请重新选择裁剪图片！","-1");
		exit();
	}
	if(empty($width))
	{
		ShowMsg("对不起，请选择裁剪图片的尺寸！","-1");
		exit();
	}
	if(empty($height))
	{
		ShowMsg("对不起，请选择裁剪图片的尺寸！","-1");
		exit();
	}
	$imginfo = getimagesize($cfg_basedir.$file);
	$imgw=$imginfo[0];
	$imgh=$imginfo[1];
	$temp=300/$imgw;
	$newwidth=300;
	$newheight=$imgh*$temp;
	$srcFile = $cfg_basedir.$file;
	$thumb = imagecreatetruecolor($newwidth, $newheight);
	$thumba = imagecreatetruecolor($width, $height);

	switch($imginfo['mime'])
	{
		case 'image/jpeg':
			$source = imagecreatefromjpeg($srcFile);
			break;
		case 'image/gif':
			$source = imagecreatefromgif($srcFile);
			break;
		case 'image/png':
			$source = imagecreatefrompng($srcFile);
			break;
		default:
			ShowMsg("对不起，裁剪图片类型不支持请选择其他类型图片！","-1");
			break;
	}

	imagecopyresized($thumb, $source, 0,0, 0,0 , $newwidth, $newheight, $imgw, $imgh);
	imagecopy($thumba,$thumb,0,0,$left,$top,$newwidth,$newheight);
	$ddn = substr($srcFile,-3);
	$ddpicok = ereg_replace("\.".$ddn."$","-lp.".$ddn,$file);
	$ddpicokurl = $cfg_basedir.$ddpicok;

	switch($imginfo['mime'])
	{
		case 'image/jpeg':
			imagejpeg($thumba,$ddpicokurl,"85");
			break;
		case 'image/gif':
			imagegif($thumba,$ddpicokurl);
			break;
		case 'image/png':
			imagepng($thumba,$ddpicokurl);
			break;
		default:
			ShowMsg("对不起，裁剪图片类型不支持请选择其他类型图片！","-1");
			break;
	}

	echo "<SCRIPT language=JavaScript>function TNav(){if(window.navigator.userAgent.indexOf('MSIE')>=1) return 'IE'; else if(window.navigator.userAgent.indexOf('Firefox')>=1) return 'FF'; else return 'OT';}function ReturnImg(reimg){window.opener.document.form1.picname.value=reimg;if(window.opener.document.getElementById('divpicview')){ if(TNav()=='IE'){window.opener.document.getElementById('divpicview').filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = reimg; window.opener.document.getElementById('divpicview').style.width = '150px'; window.opener.document.getElementById('divpicview').style.height = '100px';} else window.opener.document.getElementById('divpicview').style.backgroundImage = 'url('+reimg+')'; }else if(window.opener.document.getElementById('picview')){window.opener.document.getElementById('picview').src = reimg;}if(document.all) window.opener=true; window.close();}ReturnImg('$ddpicok');</SCRIPT>";
	exit();
}
?>