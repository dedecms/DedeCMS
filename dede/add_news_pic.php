<?
require("config.php");
require("inc_typelink.php");
$conn = connectMySql();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>图集发布向导</title>
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
   alert("标题必须设定！");
   return false;
}
if(document.form1.bigpic.value=="")
{
   document.form1.bigpic.focus();
   alert("大图片必须选择！");
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
 <form action="add_news_picok.php" method="post" enctype="multipart/form-data" name="form1" onSubmit="return checkSubmit();">
  <tr>
    <td height="19" background="img/tbg.gif"><strong>&nbsp;图集发布向导&nbsp; </strong><? if($cuserLogin->getUserRank()>4) echo("[<a href=\"list_news.php\"><u>管理文章</u></a>]");?>&nbsp;[<a href="list_news_member.php"><u>管理稿件</u>]</a></td>
</tr>
<tr>
    <td height="127" align="center" bgcolor="#FFFFFF"> 
      <table width="98%" border="0" cellspacing="2" cellpadding="0">
          <tr> 
            <td height="30" colspan="2">　　图集向导是为了简化发布图集内容的文章的上传图片过程，图集的文章模板放在 
              “模块目录/模板名称/2” 的文件夹中，只有顶级频道内容为“图片集”才能在此向导中发布图片。 </td>
          </tr>
          <tr> 
            <td height="22">类别：</td>
            <td><select name="typeid">
                <?
						if(!empty($typeid)) echo "<option value='$typeid' selected>$typename</option>\r\n";
						else echo "<option value='0' selected>--请选择--</option>\r\n";
                    	$ut = new TypeLink();
						if($cuserLogin->getUserChannel()<=0)
							$ut->GetOptionArray(0,0,2);
						else
							$ut->GetOptionArray(0,$cuserLogin->getUserChannel(),2);
					?>
              </select></td>
          </tr>
          <tr> 
            <td height="20">标题：</td>
            <td> <input name="title" type="text" id="title" size="40"> </td>
          </tr>
          <tr> 
            <td height="20">来源：</td>
            <td><input name="source" type="text" id="source" value="-" size="30"></td>
          </tr>
          <tr> 
            <td height="50">图片描述：</td>
            <td><textarea name="msg" cols="52" rows="3" id="msg"></textarea></td>
          </tr>
          <tr> 
            <td height="24">小图片：</td>
            <td><input name="litpic" type="file" id="litpic" size="30" onChange="SeePic(document.picview,document.form1.litpic);">
            </td>
          </tr>
          <tr> 
            <td height="22">大图片： </td>
            <td><input name="bigpic" type="file" id="bigpic" size="30" onChange="SeePic(document.picview,document.form1.bigpic);"></td>
          </tr>
          <tr> 
            <td height="22" colspan="2">自动生成的小图片规格：宽： 
              <input name="imgw" type="text" id="imgw3" value="200" size="6">
              像素以内 高： 
              <input name="imgh" type="text" id="imgh" value="200" size="6">
            像素以内(如不上传小图片则系统根据大图片自动生成缩略图，但自动生成的小图片清析度是不能保证的)</td>
          </tr>
          <tr> 
            <td height="38">&nbsp;</td>
            <td><input type="submit" name="Submit" value="提交图片"> &nbsp;</td>
          </tr>
          <tr bgcolor="#F1FAF2"> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td width="19%">&nbsp;</td>
            <td width="81%" height="120"><img src="img/pview.gif" width="150" name="picview" id="picview"></td>
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