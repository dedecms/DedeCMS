<?
require(dirname(__FILE__)."/config.php");
require(dirname(__FILE__)."/../include/pub_oxwindow.php");
CheckPurview('plus_文件管理器');
$activepath = str_replace("..","",$activepath);
$activepath = ereg_replace("^/{1,}","/",$activepath);
if($activepath == "/") $activepath = "";
if($activepath == "") $inpath = $cfg_basedir;
else $inpath = $cfg_basedir.$activepath;

//文件管理器交互与逻辑控制文件
$fmm = new FileManagement();
$fmm->Init();
/*---------------
function __rename();
----------------*/
if($fmdo=="rename")
{
	$fmm->RenameFile($oldfilename,$newfilename);
}
//新建目录
/*---------------
function __newdir();
----------------*/
else if($fmdo=="newdir")
{
	$fmm->NewDir($newpath);
}
//移动文件
/*---------------
function __move();
----------------*/
else if($fmdo=="move")
{
	$fmm->MoveFile($filename,$newpath);
}
//删除文件
/*---------------
function __delfile();
----------------*/
else if($fmdo=="del")
{
	$fmm->DeleteFile($filename);
}
//文件编辑
/*---------------
function __saveEdit();
----------------*/
else if($fmdo=="edit")
{
		$filename = str_replace("..","",$filename);
		$file = "$cfg_basedir$activepath/$filename";
    $str = eregi_replace("< textarea","<textarea",$str);
	  $str = eregi_replace("< /textarea","</textarea",$str);
	  $str = eregi_replace("< form","<form",$str);
	  $str = eregi_replace("< /form","</form",$str);
    $str = stripslashes($str);
    $fp = fopen($file,"w");
    fputs($fp,$str);
    fclose($fp);
    if(empty($backurl)) ShowMsg("成功保存一个文件！","file_manage_main.php?activepath=$activepath");
    else ShowMsg("成功保存文件！",$backurl);
    exit();
}
//文件编辑，可视化模式
/*---------------
function __saveEditView();
----------------*/
else if($fmdo=="editview")
{
		$filename = str_replace("..","",$filename);
		$file = "$cfg_basedir$activepath/$filename";
	  $str = eregi_replace('&quot;','\\"',$str);
    $str = stripslashes($str);
    $fp = fopen($file,"w");
    fputs($fp,$str);
    fclose($fp);
    if(empty($backurl)) $backurl = "file_manage_main.php?activepath=$activepath";
    ShowMsg("成功保存文件！",$backurl);
    exit();
}
//文件上传
/*---------------
function __upload();
----------------*/
else if($fmdo=="upload")
{
	$j=0;
	for($i=1;$i<=50;$i++)
	{
		$upfile = "upfile".$i;
		$upfile_name = "upfile".$i."_name";
		if(!isset(${$upfile}) || !isset(${$upfile_name})) continue;
		$upfile = ${$upfile};
		$upfile_name = ${$upfile_name};
		if(is_uploaded_file($upfile))
		{
			if(!file_exists($cfg_basedir.$activepath."/".$upfile_name)){
					move_uploaded_file($upfile,$cfg_basedir.$activepath."/".$upfile_name);
			}
			@unlink($upfile);
			$j++;
		}
	}
	ShowMsg("成功上传 $j 个文件到: $activepath","file_manage_main.php?activepath=$activepath");
	exit();
}
//空间检查
else if($fmdo=="space")
{
	if($activepath=="") $ecpath = "所有目录";
	else $ecpath = $activepath;	
	$titleinfo = "目录 <a href='file_manage_main.php?activepath=$activepath'><b><u>$ecpath</u></b></a> 空间使用状况：<br/>";
	$wintitle = "文件管理";
	$wecome_info = "文件管理::空间大小检查 [<a href='file_manage_main.php?activepath=$activepath'>文件浏览器</a>]</a>";
	$activepath=$cfg_basedir.$activepath;
	$space=new SpaceUse;
	$space->checksize($activepath);
	$total=$space->totalsize;
	$totalkb=$space->setkb($total);
	$totalmb=$space->setmb($total);
	$win = new OxWindow();
	$win->Init("","js/blank.js","POST");
	$win->AddTitle($titleinfo);
	$win->AddMsgItem("　　$totalmb M<br/>　　$totalkb KB<br/>　　$total 字节");
	$winform = $win->GetWindow("");
	$win->Display();
}
//---------------------
//文件管理逻辑类
//---------------------
class FileManagement
{	
	var $baseDir="";
	var $activeDir="";
	//是否允许文件管理器删除目录；
	//默认为不允许 0 ,如果希望可能管理整个目录,请把值设为 1 ；
	var $allowDeleteDir=0;
	//初始化系统
	function Init()
	{
		global $cfg_basedir;
		global $activepath;
		$this->baseDir = $cfg_basedir;
		$this->activeDir = $activepath;
	}
	//更改文件名
	function RenameFile($oldname,$newname)
	{
		$oldname = $this->baseDir.$this->activeDir."/".$oldname;
		$newname = $this->baseDir.$this->activeDir."/".$newname;
		if(($newname!=$oldname) && is_writable($oldname)){
			rename($oldname,$newname);
		}
		ShowMsg("成功更改一个文件名！","file_manage_main.php?activepath=".$this->activeDir);
		return 0;
	}
	//创建新目录
	function NewDir($dirname)
	{
		$newdir = $dirname;
		$dirname = $this->baseDir.$this->activeDir."/".$dirname;
		if(is_writable($this->baseDir.$this->activeDir)){
			MkdirAll($dirname,777);
			CloseFtp();
			ShowMsg("成功创建一个新目录！","file_manage_main.php?activepath=".$this->activeDir."/".$newdir);
		  return 1;
		}
		else{
			ShowMsg("创建新目录失败，因为这个位置不允许写入！","file_manage_main.php?activepath=".$this->activeDir);
			return 0;
		}
	}
	//移动文件
	function MoveFile($mfile,$mpath)
	{
		if($mpath!="" && !ereg("\.\.",$mpath))
		{
			$oldfile = $this->baseDir.$this->activeDir."/$mfile";
			$mpath = str_replace("\\","/",$mpath);
			$mpath = ereg_replace("/{1,}","/",$mpath);
			if(!ereg("^/",$mpath)){ $mpath = $this->activeDir."/".$mpath;  }
			$truepath = $this->baseDir.$mpath;
		  if(is_readable($oldfile) 
		  && is_readable($truepath) && is_writable($truepath))
		  {
				if(is_dir($truepath)) copy($oldfile,$truepath."/$mfile");
			  else{
			  	MkdirAll($truepath,777);
			  	CloseFtp();
			  	copy($oldfile,$truepath."/$mfile");
			  }
				unlink($oldfile);
				ShowMsg("成功移动文件！","file_manage_main.php?activepath=$mpath",0,1000);
				return 1;
			}
			else
			{
				ShowMsg("移动文件 $oldfile -&gt; $truepath/$mfile 失败，可能是某个位置权限不足！","file_manage_main.php?activepath=$mpath",0,1000);
				return 0;
			}
		}
		else{
		  ShowMsg("对不起，你移动的路径不合法！","-1",0,5000);
		  return 0;
	  }
	}
	//删除目录
	function RmDirFiles($indir)
	{
    $dh = dir($indir);
    while($filename = $dh->read()) {
      if($filename == "." || $filename == "..")
      	continue;
      else if(is_file("$indir/$filename"))
      	@unlink("$indir/$filename");
      else
        $this->RmDirFiles("$indir/$filename");
    }
    $dh->close();
    @rmdir($indir);
	}
	//删除文件
	function DeleteFile($filename)
	{
		$filename = $this->baseDir.$this->activeDir."/$filename";
		if(is_file($filename)){ @unlink($filename); $t="文件"; }
		else{
			$t = "目录";
			if($this->allowDeleteDir==1) $this->RmDirFiles($filename);
		}
		ShowMsg("成功删除一个".$t."！","file_manage_main.php?activepath=".$this->activeDir);
		return 0;
	}
}
//
//目录文件大小检测类
//
class SpaceUse
{
	var $totalsize=0;	
	function checksize($indir)
	{
		$dh=dir($indir);
		while($filename=$dh->read())
		{
			if(!ereg("^\.",$filename))
			{
				if(is_dir("$indir/$filename")) $this->checksize("$indir/$filename");
				else $this->totalsize=$this->totalsize + filesize("$indir/$filename");
			}
		}
	}
	function setkb($size)
	{
		$size=$size/1024;
		//$size=ceil($size);
		if($size>0)
		{
			list($t1,$t2)=explode(".",$size);
			$size=$t1.".".substr($t2,0,1);
		}
		return $size;
	}
	function setmb($size)
	{
		$size=$size/1024/1024;
		if($size>0)
		{
			list($t1,$t2)=explode(".",$size);
			$size=$t1.".".substr($t2,0,2);
		}
		return $size;
	}	
}
?>