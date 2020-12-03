<?
require("config.php");
$datafile = $base_dir.$art_php_dir."/webnews/news.xml";
if(!empty($mynews))
{
	$mynews=stripslashes($mynews);
	$fp = fopen($datafile,"w");
	fwrite($fp,$mynews);
	fclose($fp);
	ShowMsg("成功更改数据文件！","list_mynews.php");
	exit();
}
if(file_exists($datafile))
{
	$fp = fopen($datafile,"r");
	$mynews = fread($fp,5000*20);
	fclose($fp);
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>站内新闻编辑</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"><strong>&nbsp;编辑站内新闻&nbsp;</strong>[<a href="add_my_news.php"><u>新增站内新闻</u>]</a> [<a href="list_mynews.php"><u>查看站内新闻</u>]</a></td>
</tr>
<tr>
    <td height="127" align="center" bgcolor="#FFFFFF"><table width="98%"  border="0" cellspacing="4" cellpadding="2">
      <tr>
	  <form action="edit_mynews.php" method="post">
        <td height="163" valign="top">
		<p>
            <textarea name="mynews" cols="80" rows="20" id="mynews"><?=$mynews?></textarea>
        </p>
          <p>              <input type="submit" name="Submit" value="保存更改">
&nbsp;&nbsp;           
<input type="reset" name="Submit" value="恢复原来数据">
</p>
          </td>
		  </form>
      </tr>
    </table></td>
</tr>
</table>
</body>
</html>