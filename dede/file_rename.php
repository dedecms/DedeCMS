<?
require("config.php");
if(!empty($oldfilename)&&!empty($newfilename))
{
     $oldfilename = $base_dir.$activepath."/".$oldfilename;
	 $newfilename = $base_dir.$activepath."/".$newfilename;
	 if($newfilename!=$oldfilename){ rename($oldfilename,$newfilename); }
     header("Location:file_view.php?activepath=$activepath");
	 exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>更改文件名</title>
<link href="base.css" rel="stylesheet" type="text/css">
<script src="menu.js" language="JavaScript"></script>
<style>
.bt{border-left: 1px solid #FFFFFF; border-right: 1px solid #666666; border-top: 1px solid #FFFFFF; border-bottom: 1px solid #666666; background-color: #C0C0C0}
</style>
</head>
<body background="img/allbg.gif" leftmargin="0" topmargin="0">
<p>&nbsp;</p><table width="400" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
  <form name="form1" action="file_rename.php" method="post">
    <input type="hidden" name="activepath" value="<? echo $activepath ?>">
    <input type="hidden" name="filename" value="<? echo $filename ?>">
    <tr align="center" bgcolor="#CCCCCC"> 
      <td height="26" colspan="2"><strong>更改文件名</strong></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="24">&nbsp;路&nbsp;&nbsp;径：</td>
      <td height="24">&nbsp;&nbsp;[<a href='file_view.php?activepath=<? echo $activepath ?>'>
        <? if($activepath=="") echo "根目录";else echo $activepath?>
        </a>]</td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td width="97" height="24"> &nbsp;旧文件名：</td>
      <td width="196" height="24"> &nbsp; <input name="oldfilename" type="input" value="<? echo $filename ?>"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="24">&nbsp;新文件名：</td>
      <td height="24">&nbsp; <input name="newfilename" type="input" id="newfilename"></td>
    </tr>
    <tr align="center" bgcolor="#CCCCCC"> 
      <td height="28" colspan="2"> <input type="button" name="Submit" value=" 确 认 " onclick="document.form1.submit();" class="bt"> 
        &nbsp;&nbsp; <input type="reset" name="Submit2" value=" 重 设 " class="bt"> 
        &nbsp; <input type="button" name="Submit22" value=" 取 消 " onclick="location.href='file_view.php?activepath=<? echo $activepath ?>';" class="bt"> 
      </td>
    </tr>
  </form>
</table>
</body>

</html>
