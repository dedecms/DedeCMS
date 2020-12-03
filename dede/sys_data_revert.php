<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Data');
$bkdir = dirname(__FILE__)."/".$cfg_backup_dir;
$filelists = Array();
$structfile = "没找到数据结构文件";
if(is_dir($bkdir)){
  $dh = dir($bkdir);
  while($filename=$dh->read()){
	  if(!ereg('sql|txt$',$filename)) continue;
	  if(ereg('tables_struct',$filename)) $structfile = $filename;
	  else if( filesize("$bkdir/$filename") >0 ) $filelists[] = $filename;
  }
  $dh->Close();
}

require_once(dirname(__FILE__)."/templets/sys_data_revert.htm");

ClearAllLink();
?>