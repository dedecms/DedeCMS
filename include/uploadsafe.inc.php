<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

if(isset($_FILES['GLOBALS']))
{
	exit('Request not allow!');
}

//为了防止用户通过注入的可能性改动了数据库
//这里强制限定的某些文件类型禁止上传
$cfg_not_allowall = "php|pl|cgi|asp|aspx|jsp|php3|shtm|shtml";
$keyarr = array('name','type','tmp_name','size');
foreach($_FILES as $_key=>$_value)
{
	foreach($keyarr as $k)
	{
		if(!isset($_FILES[$_key][$k]))
		{
			exit('Request Error!');
		}
	}
	if( eregi('^(cfg_|GLOBALS)',$_key) )
	{
		exit('Request var not allow for uploadsafe!');
	}
	$$_key = $_FILES[$_key]['tmp_name'] = str_replace("\\\\","\\",$_FILES[$_key]['tmp_name']);
	${$_key.'_name'} = $_FILES[$_key]['name'];
	${$_key.'_type'} = $_FILES[$_key]['type'] = eregi_replace('[^0-9a-z\./]','',$_FILES[$_key]['type']);
	${$_key.'_size'} = $_FILES[$_key]['size'] = ereg_replace('[^0-9]','',$_FILES[$_key]['size']);
	if(!empty(${$_key.'_name'}) && (eregi("\.(".$cfg_not_allowall.")$",${$_key.'_name'}) || !ereg("\.",${$_key.'_name'})) )
	{
		if(!defined('DEDEADMIN'))
		{
			exit('Upload filetype not allow !');
		}
	}
	if(empty(${$_key.'_size'}))
	{
		${$_key.'_size'} = @filesize($$_key);
	}
}

//前台会员通用上传函数
//$upname 是文件上传框的表单名，而不是表单的变量
//$handname 允许用户手工指定网址情况下的网址
function MemberUploads($upname,$handname,$userid=0,$utype='image',$exname='',$maxwidth=-1,$maxheight=-1,$water=false)
{
	global $cfg_imgtype,$cfg_mb_addontype,$cfg_mediatype,$cfg_user_dir,$cfg_basedir,$cfg_dir_purview;
	if(is_uploaded_file($GLOBALS[$upname]))
	{
		$nowtme = time();
		$GLOBALS[$upname.'_name'] = trim(ereg_replace("[ \r\n\t\*\%\\/\?><\|\":]{1,}",'',$GLOBALS[$upname.'_name']));
		if($utype=='image')
		{
			if(!eregi("\.(".$cfg_imgtype.")$",$GLOBALS[$upname.'_name']))
			{
				ShowMsg("你所上传的图片类型不在许可列表，请上传{$cfg_imgtype}类型！",'-1');
				exit();
			}
			$sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/xpng","image/wbmp");
			$imgfile_type = strtolower(trim($GLOBALS[$upname.'_type']));
			if(!in_array($imgfile_type,$sparr))
			{
				ShowMsg("上传的图片格式错误，请使用JPEG、GIF、PNG、WBMP格式的其中一种！",'-1');
				exit();
			}
		}
		else if($utype=='flash' && !eregi("\.swf$",$GLOBALS[$upname.'_name']))
		{
			ShowMsg("上传的文件必须为flash文件！",'-1');
			exit();
		}
		else if($utype=='media' && !eregi("\.(".$cfg_mediatype.")$",$GLOBALS[$upname.'_name']))
		{
			ShowMsg("你所上传的文件类型必须为：".$cfg_mediatype,'-1');
			exit();
		}
		else if(!eregi("\.(".$cfg_mb_addontype.")$",$GLOBALS[$upname.'_name']))
		{
			ShowMsg("你所上传的文件类型不被允许！",'-1');
			exit();
		}

		//当为游客投稿的情况下，这个id应该为 0
		if($userid == '')
		{
			ShowMsg("系统无法获得用户ID，禁止上传文件！",'-1');
			exit();
		}
		if(!is_dir($cfg_basedir.$cfg_user_dir."/$userid"))
		{
			MkdirAll($cfg_basedir.$cfg_user_dir."/$userid",$cfg_dir_purview);
			CloseFtp();
		}
		$fs = explode('.',$GLOBALS[$upname.'_name']);
		$sname = $fs[count($fs)-1];
		$alltype = $cfg_mb_addontype.'|'.$cfg_imgtype.'|'.$cfg_mediatype;
		$alltypes = explode('|',$alltype);

		//系统定义的许可类型
		if(!in_array(strtolower($sname),$alltypes))
		{
			ShowMsg("系统无法识别你上传的文件或为非指定类型！",'-1');
			exit();
		}

		//强制禁止的文件类型
		if(eregi("asp|php|pl|cgi|jsp|shtm",$sname))
		{
			ShowMsg("你上传的文件为系统禁止的类型！",'-1');
			exit();
		}
		if($exname=='')
		{
			$filename = $cfg_user_dir."/$userid/".dd2char($nowtme.'-'.mt_rand(1000,9999)).'.'.$sname;
		}
		else
		{
			$filename = $cfg_user_dir."/{$userid}/{$exname}.".$sname;
		}
		move_uploaded_file($GLOBALS[$upname],$cfg_basedir.$filename) or die("上传文件到 {$filename} 失败！");
		@unlink($GLOBALS[$upname]);

		//缩小图片或加水印
		if($utype=='image' && ( ($maxwidth > 0 && $maxheight > 0) || $water) )
		{
			include_once(DEDEINC.'/image.func.php');
			if($maxwidth > 0 && $maxheight > 0)
			{
				//ImageResize($cfg_basedir.$filename,$maxwidth,$maxheight);
			}
			if($water)
			{
				WaterImg($cfg_basedir.$filename);
			}
		}

		return $filename;
	}
	else
	{
		//if(ereg(':',$handname) && !eregi('http:')) return '';
		//else
		return $handname;
	}
}
?>