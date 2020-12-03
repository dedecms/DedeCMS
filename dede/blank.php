<?
require("config.php");
$conn = connectMySql();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>织梦内容管理系统(DedeCms)V2.1完美版</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="90%" border="0" cellpadding="2" cellspacing="1" bgcolor="#666666" align="center">
  <tr>
    <td height="23" background="img/tbg.gif"> &nbsp;欢迎使用织梦内容管理系统(DedeCms)V2.1完美版</td>
</tr>
<tr>
    <td height="250" align="center" valign="middle" bgcolor="#FFFFFF">
	<br>
      <table width="96%" border="0" cellpadding="1" cellspacing="1" bgcolor="#666666">
        <tr bgcolor="#FFFFFF"> 
          <td width="40%" align="center">当前用户类别</td>
          <td width="60%">&nbsp; 
            <?
      if($cuserLogin->getUserType()==10) echo "超级管理员";
      else if($cuserLogin->getUserType()==5) echo "频道总编辑";
      else echo "信息采编员";
      ?>
          </td>
      </tr>
        <tr bgcolor="#FFFFFF"> 
          <td align="center">当前登录用户IP</td>
          <td> &nbsp; 
            <?
	  if(!empty($_SERVER["REMOTE_ADDR"])) echo $_SERVER["REMOTE_ADDR"];
	  ?>
          </td>
      </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="40%" align="center">PHP版本</td>
          <td width="60%">&nbsp; 
            <?=@phpversion();?>
          </td>
      </tr>
        <tr bgcolor="#FFFFFF"> 
          <td align="center">是否支持register_globals</td>
          <td>&nbsp; 
            <?=ini_get("register_globals") ? '支持' : '不支持'?>
          </td>
      </tr>
        <tr bgcolor="#FFFFFF"> 
          <td align="center">是否支持getenv()</td>
          <td> &nbsp; 
            <?=ereg("getenv",ini_get("disable_functions")) ? '不支持' : '支持'?>
          </td>
      </tr>
        <tr bgcolor="#FFFFFF"> 
          <td align="center">是否支持$_SERVER</td>
          <td>&nbsp; 
            <?
      if(isset($_SERVER)) echo "支持";
      else echo "不支持，使用本系统可能会有问题";
      ?>
          </td>
      </tr>
        <tr bgcolor="#FFFFFF"> 
          <td align="center">是否支持magic_quotes_gpc</td>
          <td>&nbsp; 
            <?=ini_get("magic_quotes_gpc") ? '支持' : '不支持，使用本系统可能会有问题'?>
          </td>
      </tr>
        <tr bgcolor="#FFFFFF"> 
          <td align="center">是否支持上传的最大文件</td>
          <td>&nbsp; 
            <?=ini_get("post_max_size")?>
          </td>
      </tr>
        <tr bgcolor="#FFFFFF"> 
          <td align="center">是否允许打开远程连接</td>
          <td>&nbsp; 
            <?=ini_get("allow_url_fopen") ? '支持' : '不支持'?>
          </td>
      </tr>
    </table>
      <br>
    </td>
</tr>
</table>
<center>
<a href="http://www.dedecms.com" target="_blank">Power by PHP+MySQL 织梦之旅 2004-2006 官方网站：www.DedeCMS.com</a>
</center>
</body>
</html>