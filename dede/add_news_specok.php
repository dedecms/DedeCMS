<?
require("config.php");
require("inc_makespec.php");
$spectitle = trim($spectitle);
$specartid = ereg_replace("(^,)|(,$)|[^0-9,]","",ereg_replace(",{2,}",",",$specartid));
$speclikeid = ereg_replace("(^,)|(,$)|[^0-9,]","",ereg_replace(",{2,}",",",$speclikeid));
$dtime = strftime("%Y-%m-%d %H:%M:%S",time());
$stime = strftime("%Y-%m-%d",time());
$adminid=$cuserLogin->getUserID();
$inquery="Insert Into dede_spec(typeid,spectitle,specimg,imgtitle,imglink,specmsg,specartid,speclikeid,stime,dtime,userid) Values('$typeid','$spectitle','$specimg','$imgtitle','$imglink','$specmsg','$specartid','$speclikeid','$stime','$dtime',$adminid)";
$conn = connectMySql();
mysql_query($inquery,$conn);
$ID = mysql_insert_id($conn);
if($ID!="")
{
	$mk = new MakeSpec($ID);
	$makeok = $mk->MakeMode();
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>专题创建向导</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
 <tr>
    <td height="19" background="img/tbg.gif"><strong>&nbsp;专题创建向导&nbsp; </strong></td>
</tr>
<tr>
      <td height="120" align="center" bgcolor="#FFFFFF"><table width="90%" border="0" cellspacing="2" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="37">
		  <?
		  if($ID!="")
		  {
		  		echo "成功把专题信息保存到数据库中，但还未把专题信息发布并生成HTML，请在下面选取进一步操作：";
		  		echo "<br><br>";
				echo "<a href='add_news_specview.php?ID=$ID' target='_blank'><u>[预览效果]</u></a>&nbsp;&nbsp;<a href='news_spec_edit.php?ID=$ID'><u>[修改]</u></a>&nbsp;&nbsp;<a href='list_news_spec.php'><u>[专题管理]</u></a>";
		  }
		  else
		  {
		      echo "把专题信息保存到数据库时失败，请检查原因！<br>";
		      echo mysql_error();		
		  }
		  ?>
		  </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table></td>
</tr>
</table>
</body>
</html>