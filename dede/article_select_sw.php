<?
header("Content-Type: text/html; charset=gb2312");
header("Pragma:no-cache"); 
header("Cache-Control:no-cache"); 
header("Expires:0"); 
$t = $_GET['t'];
if($t=='source') //来源列表
{
  $m_file = dirname(__FILE__)."/inc/source.txt";
  $allsources = file($m_file);
  echo "<span class='coolbg4'>[<a href=\"javascript:OpenMyWin('article_source_edit.php')\">设置</a>]&nbsp;[<a href='#' onclick='javascript:HideObj(\"_mysource\")'>关闭</a>]</span>\r\n";
  foreach($allsources as $v){
	  $v = trim($v);
	  if($v!="") echo "<a href='#' onclick='javascript:PutSource(\"$v\")'>$v</a> | \r\n";
  }
  echo "<span class='coolbg5'>&nbsp;</span>\r\n";
}else{ //作者列表
	$m_file = dirname(__FILE__)."/inc/writer.txt";
	echo "<span class='coolbg4'>[<a href=\"javascript:OpenMyWin('article_writer_edit.php')\">设置</a>]&nbsp;[<a href='#' onclick='javascript:HideObj(\"_mywriter\")'>关闭</a>]</span>\r\n";
	if(filesize($m_file)>0){
	   $fp = fopen($m_file,'r');
	   $str = fread($fp,filesize($m_file));
	   fclose($fp);
	   $strs = explode(',',$str);
	   foreach($strs as $str){
	   	 $str = trim($str);
	   	 if($str!="") echo "<a href='#' onclick='javascript:PutWriter(\"$str\")'>$str</a> | ";
	   }
  }
  echo "<br><span class='coolbg5'>&nbsp;</span>\r\n";
}
exit();
?>