<?php 
require_once(dirname(__FILE__)."/config.php");
@set_time_limit(3600);
if(empty($dopost)) $dopost = '';

header("Content-Type: text/html; charset={$cfg_ver_lang}");

//逻辑函数
//-------------------------------------
function GoSearchVir($fdir){
	global $tcc,$scc,$ddfiles,$shortname,$minsize,$maxsize,$crday,$cfg_basedir;
	$dh = dir($fdir);
	while($filename = $dh->read()){
		if($filename=='.'||$filename=='..') continue;
		$truefile = $fdir."/".$filename;
		if(is_dir($truefile)) GoSearchVir($truefile);
		if(!is_file($truefile)) continue;
		$scc++;
		$ftime = filemtime($truefile);
		$fsize = filesize($truefile);
		$ntime = time() - ($crday * 24 * 3600);
		if(eregi("\.".$shortname,$filename) && $ftime > $ntime
		&& ($fsize<$minsize || $fsize>$maxsize))
		{
			$nfsize = number_format($fsize/1024,2).'K';
			if(in_array($filename,$ddfiles)) continue;
			if($fsize<$minsize){
				$fp = fopen($truefile,'r');
				$tstr = fread($fp,$fsize);
				fclose($fp);
				if(!eregi("eval|fopen|unlink|rename",$tstr)) continue;
			}
			$furl = str_replace($cfg_basedir,"",$truefile);
			echo "<li><input type='checkbox' name='vfiles[]' value='$furl' class='np'> <a href='$furl' target='_blank'><u>$furl</u></a> 创建日期：".GetDateTimeMk($ftime)." 大小：{$nfsize} </li>\r\n";
			$tcc++;
		}
	}
	$dh->close();
}
function GoReplaceFile($fdir){
	global $tcc,$scc,$shortname,$cfg_basedir,$sstr,$rpstr;
	$dh = dir($fdir);
	while($filename = $dh->read()){
		if($filename=='.'||$filename=='..') continue;
		$truefile = $fdir."/".$filename;
		if(is_dir($truefile)) GoReplaceFile($truefile);
		if(!is_file($truefile)) continue;
		$scc++;
		$fsize = filesize($truefile);
		if($fsize>0 && eregi("\.(".$shortname.")",$filename))
		{
			$fp = fopen($truefile,'r');
			$tstr = fread($fp,$fsize);
			$tstr = eregi_replace($sstr,$rpstr,$tstr);
			fclose($fp);
			if(is_writeable($truefile)){
			  $fp = fopen($truefile,'w');
			  fwrite($fp,$tstr);
			  fclose($fp);
			  $tcc++;
			}else{
				$furl = str_replace($cfg_basedir,"",$truefile);
				echo "<li>文件： {$rurl} 不能写入！</li>";
			}
		}
	}
	$dh->close();
}
//----------------------------------

if($dopost=='search'){
   $tcc = 0;
   $scc = 0;
   $ddfile = "album_edit.php,catalog_add.php,file_manage_main.php,soft_edit.php,spec_edit.php,inc_archives_view.php,inc_arclist_view.php,inc_arcmember_view.php,inc_freelist_view.php,pub_collection.php,config_passport.php,downmix.php,inc_photowatermark_config.php,inc_arcpart_view.php,inc_typeunit_admin.php";
   $ddfiles = explode(',',$ddfile);
   if(empty($crday)) $crday = 365;
	 $minsize = $minsize * 1024;
	 $maxsize = $maxsize * 1024;
	 $phead = "<html>
  <head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
  <title>木马扫描检测结果</title>
  <link href='css_body.css' rel='stylesheet' type='text/css'>
  <style>
  li{width:100%;height:26px;border:1px solid #C9E3FA; margin:3px; list-style-type:none }
  .lii{ padding:3px; }
  </style>
  <body>
  <form action='virus_search.php' method='post' name='form1'>
  <input type='hidden' name='dopost' value='delete'>
";
	 echo $phead;
	 GoSearchVir($searchpath);
   echo "<li class='lii'> ";
   if($tcc>0) echo "<input type='submit' name='sb1' value='删除选中的文件！' class='inputbut'><br><br>\r\n";
   echo "&nbsp;&nbsp;共搜索 {$scc} 个文件，找到 {$tcc} 个可疑文件，删除文件后会在后台管理目录生成一个virlog.txt文件，如误删织梦系统文件，从此文件中找回这些文件路径，用dede相同版本没修改过的文件替换即可！ </li>\r\n";
   echo "</form><body></html>";
   exit();
}else if($dopost=='replace'){
	 $tcc = 0;
   $scc = 0;
	 $phead = "<html>
  <head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
  <title>内容替换结果</title>
  <link href='base.css' rel='stylesheet' type='text/css'>
  <style>
  li{width:100%;height:26px;border:1px solid #C9E3FA; margin:3px; list-style-type:none }
  .lii{ padding:3px; }
  </style>
  <body>
";
	 echo $phead;
	 $sstr = stripslashes($sstr);
	 $rpstr = stripslashes($rpstr);
	 if(strlen($sstr)>8){
	    GoReplaceFile($searchpath);
	 }else{
	 	  echo "替换内容不能小于8个字节！";
	 	  exit();
	 }
	 echo "<li class='lii'> ";
   echo "&nbsp;&nbsp;共搜索 {$scc} 个文件，成功替换 {$tcc} 个文件！ </li>\r\n";
   echo "<body></html>";
	 exit();
}else if($dopost=='delete')
{
	 if(is_array($vfiles)){
      $fp = fopen(dirname(__FILE__)."/virlog.txt","w");
      foreach($vfiles as $f){
      	unlink($cfg_basedir.$f);
      	fwrite($fp,$f."\r\n");
      	echo "删除文件： ".$cfg_basedir.$f." <br>\r\n";
      }
      fclose($fp);
	 }
	 echo "成功删除所有指定文件！";
	 exit();
}

require_once(dirname(__FILE__)."/templets/virus_search.htm");

ClearAllLink();
?>
