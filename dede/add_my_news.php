<?
require("config.php");
require("inc_typelink.php");
$conn = connectMySql();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>站内新闻发布</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script>
function checkSubmit()
{
if(document.form1.title.value=="")
{
   document.form1.title.focus();
   alert("标题必须设定！");
   return false;
}
if(document.form1.msg.value=="")
{
   document.form1.msg.focus();
   alert("内容不能为空！");
   return false;
}
}

function SeePic(img,f)
{
   if ( f.value != "" ) { img.src = f.value; }
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
 <form action="add_my_newsok.php" method="post" name="form1" onSubmit="return checkSubmit();">
  <tr>
    <td height="19" background="img/tbg.gif"><strong>&nbsp;站内新闻发布&nbsp;</strong>[<a href="list_mynews.php"><u>查看站内新闻</u>]</a> [<a href="edit_mynews.php"><u>编辑站内新闻</u></a>] </td>
</tr>
<tr>
    <td height="127" align="center" bgcolor="#FFFFFF"> 
      <table width="98%" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td height="20" colspan="2">站内新闻保存在              <?=$art_php_dir."/webnews/news.xml"?>中，最多保留20条记录，如果已超过记录数，将删除最旧一则记录。</td>
          </tr>
          <tr>
            <td height="20" colspan="2">（站内新闻板块代码： {dede:mynews row=&quot;条数&quot;}{/dede}，Innertext支持的字段为：title,writer,senddate(时间),msg ） </td>
          </tr>
          <tr> 
            <td width="13%" height="30">标题：</td>
            <td width="87%"> <input name="title" type="text" id="title" size="30"> </td>
          </tr>
          <tr> 
            <td height="30">发言人：</td>
            <td><input name="writer" type="text" id="writer" size="16">　
              日期：
              <input name="sdate" type="text" id="sdate" size="25" value="<?=strftime("%Y-%m-%d %I:%M %p",time())?>"></td>
          </tr>
          <tr> 
            <td height="50">信息：</td>
            <td height="120"><textarea name="msg" cols="52" rows="5" id="msg"></textarea></td>
          </tr>
          <tr> 
            <td height="38">&nbsp;</td>
            <td><input type="submit" name="Submit" value="提交新闻"> &nbsp;</td>
          </tr>
          <tr bgcolor="#F1FAF2"> 
            <td colspan="2">&nbsp;</td>
          </tr>
        </table> </td>
</tr>
</form>
</table>
</body>
</html>