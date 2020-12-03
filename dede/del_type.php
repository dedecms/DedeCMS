<?
require("config.php");
require("inc_typeunit.php");
if(!isset($job)) $job="";
if($job=="ok")
{
     $conn = connectMySql();
	 $ut = new TypeUnit();
	 $ut->DelType($ID,$delfile);
	 echo "<script>alert('成功删除一个类目！');location.href='list_type.php';</script>";
	 exit();
	 
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>删除类目</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script src='menu.js' language='JavaScript'></script>
</head>
<body background='img/allbg.gif' leftmargin='6' topmargin='6'>
<table width="80%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr> 
    <td height="19" background='img/tbg.gif'><a href="list_type.php"><u>类目管理</u></a>&gt;&gt;删除类目</td>
  </tr>
  <tr> 
    <td height="95" align="center" bgcolor="#FFFFFF"> <table width="80%" border="0" cellspacing="0" cellpadding="0">
        <form name="form1" action="del_type.php" method="post">
          <input type="hidden" name="ID" value="<?=$ID?>">
          <input type="hidden" name="job" value="ok">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2">你要删除目录：
              <?=$typeoldname?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" height="50">是否删除文件（因为动态[文章数据库内容]、静态[已生成HTML]文件可能已被搜索引擎收录,一般不建议删除，此外删除某类目后应该重新创建这个类目的文章的相关连等，所以如果非调试原因，都不建议删除文件。）</td>
          </tr>
          <tr> 
            <td width="51%" height="30"> <input type="radio" name="delfile" value="no" checked>
              否 &nbsp;&nbsp; <input type="radio" name="delfile" value="yes">
              是</td>
            <td width="49%"> <input type="button" name="Submit" value=" 确定 " onClick="javascript:document.form1.submit();"> 
              &nbsp; <input type="button" name="Submit2" value=" 返回 " onClick="javascript:location.href='list_type.php';"></td>
          </tr>
          <tr> 
            <td height="20" colspan="2">&nbsp;</td>
          </tr>
        </form>
      </table></td>
  </tr>
</table>
</body>

</html>
