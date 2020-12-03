<?
require("config.php");
require("inc_typelink.php");
if(!isset($ID))
{
	ShowMsg("你没选中任何选项！","-1");
	exit;
}
$IDS = split("`",$ID);
$ID = $IDS[0];
$conn = connectMySql();
$rs = mysql_query("select * from dede_spec where ID=$ID",$conn);
$row = mysql_fetch_object($rs);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>专题更新</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script>
var popUpWin=0;
function popUpWindow(URLStr, left, top, width, height)
{
	window.open(URLStr, 'popUpWin', 'scrollbars=yes,resizable=no,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}
function checkSubmit()
{
	if(document.form1.typeid.value=="0")
{
   document.form1.typeid.focus();
   alert("类别必须选择！");
   return false;
}
if(document.form1.spectitle.value=="")
{
   document.form1.spectitle.focus();
   alert("专题标题必须填写！");
   return false;
}
if(document.form1.specimg.value=="")
{
   document.form1.specimg.focus();
   alert("专题图片必须设定！");
   return false;
}
if(document.form1.imgtitle.value=="")
{
   document.form1.imgtitle.focus();
   alert("专题图片标题必须设定！");
   return false;
}
if(document.form1.imglink.value=="")
{
   document.form1.imglink.focus();
   alert("专题图片链接必须设定！");
   return false;
}
if(document.form1.specmsg.value=="")
{
   document.form1.specmsg.focus();
   alert("专题简介必须设定！");
   return false;
}
if(document.form1.specartid.value=="")
{
   document.form1.specartid.focus();
   alert("专题文章列表必须选择！");
   return false;
}
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
 <form name="form1" action="news_spec_editok.php" method="post" onSubmit="return checkSubmit();">
  <input type="hidden" name="ID" value="<?=$ID?>">
  <tr>
    <td height="19" background="img/tbg.gif"><strong>&nbsp;更新专题&nbsp; </strong>[<a href="list_news_spec.php"><u>管理已创建的专题</u></a>]</td>
</tr>
<tr>
    <td height="127" align="center" bgcolor="#FFFFFF"> 
      <table width="98%" border="0" cellspacing="2" cellpadding="0">
        <tr>
          <td height="30"><strong>类别：</strong></td>
          <td><select name="typeid">
                     <?
                    	$ut = new TypeLink();
						if($cuserLogin->getUserChannel()<=0)
							$ut->GetOptionArray($row->typeid,0,1);
						else
							$ut->GetOptionArray($row->typeid,$cuserLogin->getUserChannel(),1);
					?>
					</select> (建议选择一级或二级分类)</td>
        </tr>
        <tr> 
          <td height="30"><strong>标题：</strong></td>
          <td> <input name="spectitle" type="text" id="spectitle" size="40" value="<?=$row->spectitle?>"> </td>
        </tr>
        <tr> 
          <td height="30" colspan="2" bgcolor="#F7F9F8"><strong>主题图片： 
            <input name="b1" type="button" id="b1" onClick="popUpWindow('list_news_picforspec.php', 50, 0,600,300)" value="从已有的图片新闻中选取" style="height:20;width:160">
            </strong></td>
        </tr>
        <tr> 
          <td>专题图片：</td>
          <td><input name="specimg" type="text" id="specimg" size="40" value="<?=$row->specimg?>"> &nbsp;(最佳大小为W150 
            * H120象素)</td>
        </tr>
        <tr> 
          <td>图片标题：</td>
          <td><input name="imgtitle" type="text" id="imgtitle" size="40" value="<?=$row->imgtitle?>"></td>
        </tr>
        <tr> 
          <td>图片连接：</td>
          <td><input name="imglink" type="text" size="40" value="<?=$row->imglink?>"></td>
        </tr>
        <tr> 
          <td colspan="2" bgcolor="#F7F9F8"><strong>简介：</strong>(新闻专题介绍，250中文字以内)</td>
        </tr>
        <tr align="center"> 
          <td height="80" colspan="2"> <textarea name="specmsg" cols="60" rows="3" id="specmsg"><?=$row->specmsg?></textarea> 
          </td>
        </tr>
        <tr> 
          <td height="30" colspan="2" bgcolor="#F7F9F8"><strong>专题文章列表：</strong>文章用“ID1，ID2..”形式，点击此从列表中 
            <input name="b2" type="button" id="b2" value="选取或增加" style="height:20;width:80" onClick="popUpWindow('list_news_forspec.php?qtype=specartid', 50, 0,600,300)"> 
            &nbsp;(这项是必须的)</td>
        </tr>
        <tr align="center"> 
          <td colspan="2"> <textarea name="specartid" cols="60" rows="3" id="specartid"><?=$row->specartid?></textarea> 
          </td>
        </tr>
        <tr> 
          <td height="30" colspan="2" bgcolor="#F7F9F8"><strong>相关文章：</strong>文章用“ID1，ID2..”形式，点击此从列表中 
            <input name="b3" type="button" id="b3" value="选取或增加" style="height:20;width:80" onClick="popUpWindow('list_news_forspec.php?qtype=speclikeid', 50, 0,600,300)"></td>
        </tr>
        <tr align="center"> 
          <td colspan="2"> <textarea name="speclikeid" cols="60" rows="3" id="speclikeid"><?=$row->speclikeid?></textarea> 
          </td>
        </tr>
        <tr> 
          <td height="38">&nbsp;</td>
          <td><input type="submit" name="Submit" value="提交专题"> &nbsp;</td>
        </tr>
        <tr> 
          <td width="19%">&nbsp;</td>
          <td width="81%">&nbsp;</td>
        </tr>
      </table> </td>
</tr>
</form>
</table>
</body>
</html>