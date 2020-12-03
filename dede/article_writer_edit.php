<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Writer');
if(empty($dopost)) $dopost = "";
if(empty($allwriter)) $allwriter = "";
else $allwriter = stripslashes($allwriter);
$m_file = dirname(__FILE__)."/inc/writer.txt";
//保存
if($dopost=="save")
{
   $fp = fopen($m_file,'w');
   flock($fp,3);
   fwrite($fp,$allwriter);
   fclose($fp);
   header("Content-Type: text/html; charset={$cfg_ver_lang}");
   echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$cfg_ver_lang}\">\r\n";
   echo "<script>alert('Save OK!');</script>";
}
//读出
if(empty($allwriter)&&filesize($m_file)>0){
   $fp = fopen($m_file,'r');
   $allwriter = fread($fp,filesize($m_file));
   fclose($fp);
}
require_once(dirname(__FILE__)."/templets/article_writer_edit.htm");

ClearAllLink();
?>
