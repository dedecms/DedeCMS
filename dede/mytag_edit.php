<?
require(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($dopost)) $dopost = "";
$aid = ereg_replace("[^0-9]","",$aid);
if( empty($_COOKIE['ENV_GOBACK_URL']) ) $ENV_GOBACK_URL = "mytag_main.php";
else $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
//////////////////////////////////////////
if($dopost=="delete")
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Delete From #@__mytag where aid='$aid'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功删除一个自定义标记！",$ENV_GOBACK_URL);
	exit();
}
else if($dopost=="saveedit")
{
	$dsql = new DedeSql(false);
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	$query = "
	 Update #@__mytag
	 set
	 typeid='$typeid',
	 timeset='$timeset',
	 starttime='$starttime',
	 endtime='$endtime',
	 normbody='$normbody',
	 expbody='$expbody'
	 where aid='$aid'
	";
	$dsql->SetQuery($query);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改一个自定义标记！",$ENV_GOBACK_URL);
	exit();
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__mytag where aid='$aid'");
$dsql->Close();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>更改标记</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
<tr>
  <td height="19" background="img/tbg.gif"><b><a href="mytag_main.php"><u>自定义标记管理</u></a></b>&gt;&gt;更改标记</td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
	<table width="100%" border="0" cellspacing="4" cellpadding="4">
        <form action="mytag_edit.php" method="post" enctype="multipart/form-data" name="form1">
          <input type='hidden' name='aid' value='<?=$aid?>'>
          <input type='hidden' name='dopost' value='saveedit'>
          <tr> 
            <td width="15%" height="25" align="center">所属栏目：</td>
            <td colspan="2">
			<?
           	$tl = new TypeLink(0);
           	$typeOptions = $tl->GetOptionArray($row['typeid'],0,0);
            echo "<select name='typeid' style='width:300'>\r\n";
            echo "<option value='0' selected>显示在没有继承本标记的所有栏目</option>\r\n";
            echo $typeOptions;
            echo "</select>";
			$tl->Close();
			?>
			</td>
          </tr>
          <tr> 
            <td height="25" align="center">标记名称：</td>
            <td colspan="2"><?=$row['tagname']?></td>
          </tr>
          <tr> 
            <td height="25" align="center">时间限制：</td>
            <td colspan="2"><input name="timeset" type="radio" value="0"<?if($row['timeset']==0) echo " checked"; ?>>
              永不过期 
              <input type="radio" name="timeset" value="1" <?if($row['timeset']==1) echo " checked"; ?>>
              在设内时间内有效</td>
          </tr>
          <tr> 
            <td height="25" align="center">开始时间：</td>
            <td colspan="2"><input name="starttime" type="text" id="starttime" value="<?=GetDateTimeMk($row['starttime'])?>"></td>
          </tr>
          <tr> 
            <td height="25" align="center">结束时间：</td>
            <td colspan="2"><input name="endtime" type="text" id="endtime" value="<?=GetDateTimeMk($row['endtime'])?>"></td>
          </tr>
          <tr> 
            <td height="80" align="center">正常显示内容：</td>
            <td width="76%">
              <?
	GetEditor("normbody",$row['normbody'],120,"Small");
	?>
            </td>
            <td width="9%">&nbsp;</td>
          </tr>
          <tr> 
            <td height="80" align="center">过期显示内容：</td>
            <td> 
              <?
	GetEditor("expbody",$row['expbody'],120,"Small");
	?>
            </td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td height="53" align="center">&nbsp;</td>
            <td colspan="2"><input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
          </tr>
        </form>
      </table>
	 </td>
</tr>
</table>
</body>
</html>