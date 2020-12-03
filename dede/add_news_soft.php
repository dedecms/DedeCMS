<?
require("config.php");
require("inc_typelink.php");
$conn = connectMySql();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>软件发布向导</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script>
function checkSubmit()
{
	if(document.form1.typeid.value=="0")
{
   document.form1.typeid.focus();
   alert("类别必须选择！");
   return false;
}
if(document.form1.title.value=="")
{
   document.form1.title.focus();
   alert("软件名称必须设定！");
   return false;
}
if(document.form1.msg.value=="")
{
   document.form1.msg.focus();
   alert("软件简述必须设定！");
   return false;
}
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
 <form action="add_news_softok.php" method="post" enctype="multipart/form-data" name="form1" onSubmit="return checkSubmit();">
  <tr>
    <td height="19" background="img/tbg.gif"><strong>&nbsp;软件发布向导&nbsp; </strong><? if($cuserLogin->getUserRank()>4) echo("[<a href=\"list_news.php\"><u>管理文章</u></a>]");?>&nbsp;[<a href="list_news_member.php"><u>管理稿件</u>]</a></td>
</tr>
<tr>
    <td height="127" align="center" bgcolor="#FFFFFF"> 
      <table width="98%" border="0" cellspacing="2" cellpadding="0">
          <tr> 
            <td height="30" colspan="2">　　软件发布向导是为了简化软件内容的发布过程，模板放在 “模块目录/模板名称/3” 
              的文件夹中，只有顶级频道内容为“软件下载”才能在此向导中发布文章，软件的body模板在“模板目录/模板风格名称/3/向导.htm”中。 
            </td>
          </tr>
          <tr> 
            <td width="19%" height="30">类别：*</td>
            <td width="81%"><select name="typeid">
                <?
						if(!empty($typeid)) echo "<option value='$typeid' selected>$typename</option>\r\n";
						else echo "<option value='0' selected>--请选择--</option>\r\n";
                    	$ut = new TypeLink();
						if($cuserLogin->getUserChannel()<=0)
							$ut->GetOptionArray(0,0,3);
						else
							$ut->GetOptionArray(0,$cuserLogin->getUserChannel(),3);
					?>
              </select> &nbsp;</td>
          </tr>
          <tr> 
            <td height="20">软件名称：*</td>
            <td> <input name="title" type="text" id="title" size="20">
              软件出处： 
              <input name="source" type="text" id="source2" value="-" size="25"></td>
          </tr>
          <tr> 
            <td height="20">添加时间：*</td>
            <td><input name="stime" type="text" id="softsize5" value="<?=strftime("%Y-%m-%d")?>" size="15"> &nbsp;软件等级： 
              <select name="softrank" id="softrank">
                <option value="★">一星级</option>
                <option value="★★">二星级</option>
                <option value="★★★" selected>三星级</option>
                <option value="★★★★">四星级</option>
                <option value="★★★★★">五星级</option>
              </select> </td>
          </tr>
          <tr> 
            <td height="20">软件语言：</td>
            <td><input name="language" type="text" id="language3" value="简体中文" size="15"> 
              &nbsp;软件大小： 
              <input name="softsize" type="text" id="softsize4" value="1000 K" size="15"></td>
          </tr>
          <tr> 
            <td height="20">软件平台：</td>
            <td><select name="opensystem" id="opensystem">
                <option value="windows98/NT/2000/XP/2003" selected>windows98/NT/2000/XP/2003</option>
                <option value="Linux">Linux</option>
                <option value="FreeBSD/Unix">FreeBSD/Unix</option>
                <option value="其它平台">其它平台</option>
              </select> &nbsp;授权方式： 
              <select name="softtype" id="softtype">
                <option value="试用/共享软件">试用/共享软件</option>
                <option value="免费/开源软件" selected>免费/开源软件</option>
                <option value="破解/解密软件">破解/解密软件</option>
              </select></td>
          </tr>
          <tr> 
            <td height="50">软件简述：*<br>
              (200字以内) </td>
            <td><textarea name="msg" cols="52" rows="3" id="msg"></textarea></td>
          </tr>
          <tr> 
            <td height="50">软件介绍：*<br>
              (20K以内，不支持HTML)</td>
            <td><textarea name="body" cols="52" rows="5" id="body"></textarea></td>
          </tr>
          <tr> 
            <td height="22">界面图片(200*200)：</td>
            <td><input name="litpic" type="file" id="litpic" size="40"></td>
          </tr>
          <tr> 
            <td height="22">上传软件：</td>
            <td><input name="uploadsoft" type="file" id="uploadsoft" size="40"></td>
          </tr>
          <tr> 
            <td height="22">下载地址一：</td>
            <td><input name="addr1" type="text" id="addr1" value="http://" size="40"></td>
          </tr>
          <tr> 
            <td height="22">下载地址二：</td>
            <td><input name="addr2" type="text" id="addr2" value="http://" size="40"></td>
          </tr>
          <tr> 
            <td height="22">下载地址三：</td>
            <td><input name="addr3" type="text" id="addr3" value="http://" size="40"></td>
          </tr>
          <tr> 
            <td height="22">下载地址四：</td>
            <td><input name="addr4" type="text" id="addr4" value="http://" size="40"></td>
          </tr>
          <tr> 
            <td height="22">下载地址五：</td>
            <td><input name="addr5" type="text" id="addr5" value="http://" size="40"></td>
          </tr>
          <tr> 
            <td height="38">&nbsp;</td>
            <td><input type="submit" name="Submit" value="提交软件"> </td>
          </tr>
          <tr> 
            <td colspan="2" bgcolor="#F1FAF2">&nbsp;</td>
          </tr>
        </table> </td>
</tr>
</form>
</table>
</body>
</html>