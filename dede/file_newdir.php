<?
require("config.php");
if(empty($newpath)) $newpath="";
if(empty($activepath)) $activepath="";
if(empty($filename)) $filename="";
if($newpath!="")
{
      mkdir("$base_dir$activepath/$newpath",0777);
      header("Location:file_view.php?activepath=$activepath/$newpath");
      exit();
}
if($activepath=="") $activepathname="根目录";
else $activepathname=$activepath;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>移动文件</title>
<link href="base.css" rel="stylesheet" type="text/css">
<style>
.bt{border-left: 1px solid #FFFFFF; border-right: 1px solid #666666; border-top: 1px solid #FFFFFF; border-bottom: 1px solid #666666; background-color: #C0C0C0}
</style>
</head>
<body background="img/allbg.gif" leftmargin="0" topmargin="0">
<p>&nbsp;</p>
<table width="400" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
  <form name="form1" action="file_newdir.php" method="post">
    <input type="hidden" name="activepath" value="<? echo $activepath ?>">
    <tr align="center" bgcolor="#CCCCCC"> 
      <td height="26" colspan="2"><strong>当前目录[<? echo $activepathname ?>]</strong></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td width="97" height="24"> &nbsp;新建目录：</td>
      <td width="196" height="24"> &nbsp; <input name="newpath" type="input" id="newpath"></td>
    </tr>
    <tr align="center" bgcolor="#CCCCCC"> 
      <td height="28" colspan="2"> <input type="button" name="Submit" value=" 确 认 " onclick="document.form1.submit();" class="bt"> 
        &nbsp;&nbsp; <input type="button" name="Submit2" value=" 取 消 " class="bt" onclick="location.href='file_view.php?activepath=<? echo $activepath ?>';"> 
      </td>
    </tr>
  </form>
</table>
</body>

</html>
