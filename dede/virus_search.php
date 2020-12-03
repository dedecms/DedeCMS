<?php 
require_once(dirname(__FILE__)."/config.php");
@set_time_limit(3600);
if(empty($dopost)) $dopost = '';

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
		if(eregi("\.(".$shortname.")",$filename))
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
   $ddfile = "album_edit.php,catalog_add.php,file_manage_main.php,soft_edit.php,spec_edit.php,inc_archives_view.php,inc_arclist_view.php,inc_arcmember_view.php,inc_freelist_view.php,pub_collection.php,config_passport.php,downmix.php,inc_photowatermark_config.php";
   $ddfiles = explode(',',$ddfile);
   if(empty($crday)) $crday = 365;
	 $minsize = $minsize * 1024;
	 $maxsize = $maxsize * 1024;
	 $phead = "<html>
  <head>
  <meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
  <title>木马扫描检测结果</title>
  <link href='base.css' rel='stylesheet' type='text/css'>
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
   if($tcc>0) echo "<input type='submit' name='sb1' value='删除选中的文件！' class='nbt'><br><br>\r\n";
   echo "&nbsp;&nbsp;共搜索 {$scc} 个文件，找到 {$tcc} 个可疑文件，删除文件后会在后台管理目录生成一个virlog.txt文件，如误删织梦系统文件，从此文件中找回这些文件路径，用dede相同版本没修改过的文件替换即可！ </li>\r\n";
   echo "</form><body></html>";
   exit();
}else if($dopost=='replace'){
	 $tcc = 0;
   $scc = 0;
	 $phead = "<html>
  <head>
  <meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
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
	 GoReplaceFile($searchpath);
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

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>木马扫描检测插件</title>
<link href="base.css" rel="stylesheet" type="text/css">
<script language="javascript">
function CheckRp(){
  var dp1 = document.getElementById("dp1");
  var dp2 = document.getElementById("dp2");
  var rpct = document.getElementById("rpct");
  if(dp1.checked){
  	document.form1.shortname.value = "php|asp|aspx";
  	rpct.style.display = "none";
  }else{
    document.form1.shortname.value = "php|asp|aspx|htm|html|shtml";
    rpct.style.display = "block";
  }
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center">
  <form action="virus_search.php" name="form1" target="stafrm" method="post">
  <tr> 
    <td height="20" background='img/tbg.gif'> <table width="98%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
            <td width="30%" height="18"><strong>木马扫描检测插件：</strong></td>
          <td width="70%" align="right">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr>
      <td height="33" bgcolor="#FFFFFF">　　本文件的原理是扫描有可疑操作的PHP文件，或特定规则的文件，对于已经感染病毒的文件，请指定替换内容规则，替换被感染的文件内容，在文件数量非常多的情况下，本操作比较占用服务器资源，请确脚本超时限制时间允许更改，否则可能无法完成操作。</td>
  </tr>
  <tr> 
    <td height="48" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="2">
          <tr bgcolor="#EBFBE6"> 
            <td><strong>&nbsp;操作类型：</strong></td>
            <td> <input name="dopost" type="radio" id="dp1" class="np" onclick="CheckRp()" value="search" checked>
              扫描文件 
              <input name="dopost" type="radio" id="dp2" value="replace" onclick="CheckRp()" class="np">
              替换内容 </td>
          </tr>
          <tr> 
            <td width="14%"><strong>&nbsp;起始根路径：</strong></td>
            <td width="86%"> <input name="searchpath" type="text" id="searchpath" style="width:60%" value="<?=$cfg_basedir?>">	
            </td>
          </tr>
          <tr> 
            <td bgcolor="#EBFBE6"><strong>&nbsp;文件规则定义：</strong></td>
            <td bgcolor="#EBFBE6">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                <tr> 
                  <td height="30">&nbsp;扩 展 名： 
                    <input name="shortname" type="text" id="shortname" size="20" value="php|asp|aspx">
                    文件大小：（小于： 
                    <input name="minsize" type="text" id="minsize" value="1" size="6">
                    K 或 大于： 
                    <input name="maxsize" type="text" id="maxsize" value="20" size="6">
                    K） 文件创建日期： 
                    <input name="crday" type="text" id="crday" value="7" size="6">
                    天以内。
                    <hr size="1">
                    （说明：通常情况下PHP木马可能只是几十个字节，要么是20K以上，程序会自动忽略织梦系统大于20K的文件） </td>
                </tr>
              </table></td>
          </tr>
          <tr id="rpct" style="display:none"> 
            <td height="64" colspan="2">
			<table width="100%" border="0" cellspacing="1" cellpadding="1">
                <tr bgcolor="#EDFCE2"> 
                  <td colspan="4"><strong>内容替换选项：</strong>（替换内容请使用正则表达式，替换内容时仅判断扩展名，文件大小、文件创建日期选项会忽略）</td>
                </tr>
                <tr> 
                  <td width="15%">&nbsp;替换内容：</td>
                  <td width="35%"><textarea name="sstr" id="sstr" style="width:90%;height:45px"></textarea></td>
                  <td width="15%">替 换 为：</td>
                  <td><textarea name="rpstr" id="rpstr" style="width:90%;height:45px"></textarea></td>
                </tr>
              </table>
			  </td>
          </tr>
        </table></td>
  </tr>
  <tr> 
    <td height="31" bgcolor="#F8FBFB" align="center">
	<input type="submit" name="Submit" value="开始执行操作" class="nbt">
	</td>
  </tr>
  </form>
  <tr bgcolor="#E5F9FF"> 
    <td height="20"> <table width="100%">
        <tr> 
          <td width="74%"><strong>检测结果：</strong></td>
          <td width="26%" align="right"> <script language='javascript'>
            	function ResizeDiv(obj,ty)
            	{
            		if(ty=="+") document.all[obj].style.pixelHeight += 50;
            		else if(document.all[obj].style.pixelHeight>80) document.all[obj].style.pixelHeight = document.all[obj].style.pixelHeight - 50;
            	}
            	</script>
            [<a href='#' onClick="ResizeDiv('mdv','+');">增大</a>] [<a href='#' onClick="ResizeDiv('mdv','-');">缩小</a>] 
          </td>
        </tr>
      </table></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td id="mtd">
    	<div id='mdv' style='width:100%;height:100;'> 
        <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
      </div>
      <script language="JavaScript">
	    document.all.mdv.style.pixelHeight = screen.height - 420;
	    </script>
	   </td>
  </tr>
</table>
</body>
</html>
