<?
require("config.php");
require("inc_makepartcode.php");
if($ID!=0)
{
	if($job=="view")
	{
		$conn = connectMySql();
		$mp = new MakePartCode();
		$rs = mysql_query("Select * From dede_partmode where ID=$ID",$conn);
		$row=mysql_fetch_object($rs);
		echo "<base href='".$base_url."'>";
		$mp->typeID = $row->typeid;
		echo $mp->ParTemp($row->body);
		exit();
	}
	if($job=="make")
	{
		$conn = connectMySql();
		$mp = new MakePartCode();
		$rs = mysql_query("Select * From dede_partmode where ID=$ID",$conn);
		$row=mysql_fetch_object($rs);
		$uname = "/".ereg_replace("^/{1,}","",$row->fname);
		$fname = $base_dir.$uname;
		$body = $row->body;
		$mp->typeID = $row->typeid;
		$mp->MakeMode($body,$fname);
		echo "<br><br>　　创建板块：<a href='$uname' target='_blank'>".$row->pname."  $uname</a> OK <br>\r\n";
		echo "　　<a href='web_type_web.php'><u>[返回上一页]</u></a>";
		exit();
	}
	if($job=="save")
	{
		$conn = connectMySql();
		$mp = new MakePartCode();
		$fname = trim($fname);
		$rs = mysql_query("update dede_partmode set pname='$pname',fname='$fname',body='$body' where ID=$ID",$conn);
		$uname = "/".ereg_replace("^/{1,}","",$fname);
		$fname = $base_dir.$uname;
		$mp->typeID = $row->typeid;
		$mp->MakeMode(stripslashes($body),$fname);
		ShowMsg("成功保存！","");
	}
	if($job=="del")
	{
		echo "<br><br>　　你确实要删除这个板块吗？<br>";
		echo "　　<a href='web_del_type_web.php?ID=$ID'><u>[确定删除]</u></a>";
		exit();
	}
}
else
{
	echo "<br><br>　　你没有选中任何板块模板！<br>";
	echo "　　<a href='javascript:window.close();'><u>[关闭这个窗口]</u></a>　　<a href='web_type_web.php'><u>[返回上一页]</u></a>";
	exit();
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>网站板块管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background='img/tbg.gif'>&nbsp;<strong>编辑网站板块&nbsp;&nbsp;[<a href="web_type_web.php"><u>管理网站板块</u></a>]</strong></td>
</tr>
<tr>
    <td height="94" bgcolor="#FFFFFF"> 
      <table width="98%" border="0" cellspacing="2" cellpadding="0">
        <form method="POST" name="upfrom">
          <input type='hidden' name='ID' value='<?=$ID?>'>
          <input type='hidden' name='job' value='save'>
          <?
        $conn = connectMySql();
        $rs = mysql_query("Select * From dede_partmode where ID=$ID",$conn);
        $row = mysql_fetch_object($rs);
        ?>
          <tr> 
            <td colspan="3" bgcolor="#FAFCF3">网站板块建议全部以根目录为参照路径，设计完成后上传到数据库中。</td>
          </tr>
          <tr> 
            <td height="30" colspan="3">板块模板名称： 
              <input name="pname" type="text" id="pname" size="18" value="<?=$row->pname?>">
              要生成的文件： 
              <input name="fname" type="text" id="fname" size="15" value="<?=$row->fname?>">
              .html (不需加.html)</td>
          </tr>
          <tr> 
            <td width="12%" height="40">模板内容： &nbsp;&nbsp; </td>
            <td width="71%" height="40">&nbsp;</td>
            <td width="17%"><input type="submit" name="Submit" value=" 提交 "></td>
          </tr>
          <tr align="center"> 
            <td height="40" colspan="3"> <textarea name="body" cols="80" rows="15" id="body"><?=$row->body?></textarea> 
            </td>
          </tr>
        </form>
        <tr bgcolor="#FFFFFF"> 
          <td colspan="3">&nbsp;</td>
        </tr>
        <tr bgcolor="#CCCC99">
            <td colspan="3"><b>代码参考，<a href="web_mode.php#part"><u>更详细的说明&gt;&gt;&gt;</u></a></b></td>
          </tr>
          <tr> 
            <td height="34" colspan="3">测试代码： <textarea name="testcode" cols="56" rows="2" id="testcode"></textarea>
              　
              <input type="submit" name="Submit2" value="确定测试"> </td>
          </tr>
        </form>
        <tr> 
          <td height="40" colspan="3"><?include("parthelp.html");?></td>
        </tr>
      </table> </td>
</tr>
</table>
</body>
</html>