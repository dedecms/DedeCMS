<?
require("config.php");
if(empty($okurl)) $okurl="";
if(empty($artID)) $artID="";
if(empty($url)) $url="http://";
if($url!="http://"&&$artID!="")
{
	$okurl="<a href=\"$art_php_dir/download.php?artID=$artID&goto=".ereg_replace("=$","",base64_encode("/dd?goto=$url"))."\" target=_blank>[下载地址]</a> \r\n";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>软件链接生成器</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="2" topmargin="4">
<table width="400" align="center" border="0" cellpadding="1" cellspacing="1" bgcolor="#464646">
  <tr>
    <td width="100%" height="24" colspan="2" background="img/tbg.gif"><strong>&nbsp;软件链接生成器：</strong></td>
  </tr>
  <tr>
    <td height="190" colspan="2" valign="middle" bgcolor="#FFFFFF"> 
      <form name="form1" method="post" action="make_softlink.php">
	  <input name="artID" value="<?=$artID?>" type="hidden">
        <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr> 
            <td>软件下载网址：</td>
          </tr>
          <tr> 
            <td height="30">
            <input name="url" type="text" id="url" value="<?=$url?>" size="50"> 
            </td>
          </tr>
          <tr> 
            <td height="35" align="right">
<input type="submit" name="Submit" value="生成下载链接">
              　 
              <input type="button" name="Submit2" value="关闭窗口" onClick="window.opener=true;window.close();"></td>
          </tr>
          <tr> 
            <td>生成的下载链接：</td>
          </tr>
          <tr> 
            <td height="68"><textarea name="okurl" cols="50" rows="5" id="okurl"><?=$okurl?></textarea></td>
          </tr>
        </table>
	  </form>
    </td>
  </tr>
</table>
</body>
</html>