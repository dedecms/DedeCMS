<?
require_once(dirname(__FILE__)."/config.php");
SetPageRank(10);
if(empty($dopost)) $dopost = "";
if($dopost=="save")
{
	$plusname = str_replace("\\'","",$plusname);
	$link = str_replace("\\'","",$link);
	$target = str_replace("\\'","",$target);
	$menustring = "<m:item name=\\'$plusname\\' link=\\'$link\\' rank=\\'$rank\\' target=\\'$target\\' />";
  $dsql = new DedeSql(false);
  $dsql->SetQuery("Insert Into #@__plus(plusname,menustring,writer,isshow) Values('$plusname','$menustring','$writer','1');");
  $dsql->Execute();
  $dsql->Close();
  ShowMsg("成功安装一个插件,请刷新导航菜单!","plus_main.php");
	exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>安装新插件</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <form name="form1" action="plus_add.php" method="post">
   <input type='hidden' name='dopost' value='save'>
    <tr> 
      <td height="20" colspan="2" background="img/tbg.gif"> <b>&nbsp;<a href="plus_main.php"><u>插件管理</u></a> 
        &gt; 安装新插件：</b> </td>
    </tr>
    <tr> 
      <td width="19%" align="center" bgcolor="#FFFFFF">插件名称</td>
      <td width="81%" bgcolor="#FFFFFF"><input name="plusname" type="text" id="plusname"> 
      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">作者</td>
      <td bgcolor="#FFFFFF"> <input name="writer" type="text" id="writer"> </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">使用权限</td>
      <td bgcolor="#FFFFFF">
	  <select name='rank' style='width:150'>
		<?
			$dsql = new DedeSql(false);
			$dsql->SetQuery("Select * from #@__admintype order by rank desc");
			$dsql->Execute("ut");
			while($myrow = $dsql->GetObject("ut"))
			{
			  echo "<option value='".$myrow->rank."'>".$myrow->typename."</option>\r\n";
			}
			$dsql->Close();
		?>
      </select>
	</td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">主程序文件</td>
      <td bgcolor="#FFFFFF"><input name="link" type="text" id="link" size="30"> </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">目标框架</td>
      <td bgcolor="#FFFFFF"><input name="target" type="text" id="target" value="main"></td>
    </tr>
    <tr bgcolor="#F9FDF0"> 
      <td height="28" colspan="2"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="26%">&nbsp;</td>
            <td width="15%"><input name="imageField" class="np" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
            <td width="59%"><img src="img/button_back.gif" width="60" height="22" onClick="location='plus_main.php';" style="cursor:hand"></td>
          </tr>
        </table></td>
    </tr>
  </form>
</table>
</body>
</html>