<?
require("config.php");
$conn = connectMySql();
$rs = mysql_query("Select * From dede_arttype where ID=$ID",$conn);
$row = mysql_fetch_object($rs);
$modname = urlencode($row->modname);
$modnameurl = $mod_dir."/".$modname."/".$row->channeltype;
$modname = $mod_dir."/".$row->modname."/".$row->channeltype;
$modname2 = $mod_dir."/".$row->modname;
$modnameurl2 = $mod_dir."/".urlencode($row->modname);
$typename = $row->typename;
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>类目模板管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script src='menu.js' language='JavaScript'></script>
</head>
<body background='img/allbg.gif' leftmargin='6' topmargin='6'>
<table width="600" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr> 
    <td height="19" background="img/tbg.gif" bgcolor="#E7E7E7"><strong>板块模板管理</strong>&nbsp; 
      [<a href="list_type.php"><u>类目管理</u></a>]</td>
  </tr>
  <tr> 
    <td height="95" align="center" bgcolor="#FFFFFF">
    <table width="96%" border="0" cellspacing="0" cellpadding="0">
        <form name="form1" action="" method="post">
          <input type="hidden" name="ID" value="<?=$ID?>">
          <tr> 
            <td height="30" colspan="2">
            你所选择的目录为：
            <?
            echo $typename;
            echo " <a href='mod_type.php?ID=$ID&typeoldname=$typename'>[<u>更改频道风格</u>]</a>";
            ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" height="30">目录的列表模板：
           <?
            echo "<a href='$modnameurl/".urlencode("列表").".htm' target='_blank'><u>$modname/列表.htm</u></a>";
            echo "　　<a href='file_edit.php?activepath=".$modname."&filename=列表.htm&job=edit'>[<u>编辑模板</u>]</a>\r\n";
           ?>
           </td>
          </tr>
          <tr> 
            <td colspan="2" height="30">目录的文章模板：
           <?
            echo "<a href='$modnameurl/".urlencode("文章").".htm' target='_blank'><u>$modname/文章.htm</u></a>";
            echo "　　<a href='file_edit.php?activepath=".$modname."&filename=文章.htm&job=edit'>[<u>编辑模板</u>]</a>";
           ?>
           </td>
          </tr>
          <tr> 
            <td colspan="2" height="30">目录的专题模板：
           <?
            echo "<a href='$modnameurl/".urlencode("专题").".htm' target='_blank'><u>$modname/专题.htm</u></a>";
            echo "　　<a href='file_edit.php?activepath=".$modname."&filename=专题.htm&job=edit'>[<u>编辑模板</u>]</a>";
           ?>
           </td>
          </tr>
          <tr> 
            <td colspan="2" height="30">默认板块模板：
           <?
            echo "<a href='$modnameurl2/part.htm' target='_blank'><u>$modname2/part.htm</u></a>";
            echo "　　<a href='file_edit.php?activepath=".$modname2."&filename=part.htm&job=edit'>[<u>编辑模板</u>]</a>";
           ?>
           </td>
          </tr>
          <tr> 
            <td  colspan="2" height="40">
            是否使用自定义的板块模板作为频道首页：
            <?
            $rs = mysql_query("Select ID,typeid From dede_partmode where typeid=$ID",$conn);
			if(mysql_num_rows($rs)<=0)
			{
				echo "没有 [<a href='web_type_web.php?ID=$ID'><u>设置自定板块</u></a>]";
			}
			else
			{
				$row = mysql_fetch_object($rs);
				echo "有 [<a href='web_type_webtest.php?ID=".$row->ID."&job=edit'><u>编辑自定板块</u></a>]";
			}
            ?>
            <hr size="1">
            在没有设置频道首页为板块模板的情况下，频道首页为列表文件的第一页或按列表模板的固定格式，<br>如果设置了频道首页为板块模板，更新列表后，必须在<a href='web_type_web.php'><u>“板块管理”</u></a>中更新这个个频道的板块。
            </td>
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
