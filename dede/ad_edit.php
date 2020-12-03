<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_广告管理');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($dopost)) $dopost = "";
$aid = ereg_replace("[^0-9]","",$aid);
if( empty($_COOKIE['ENV_GOBACK_URL']) ) $ENV_GOBACK_URL = "ad_main.php";
else $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
//////////////////////////////////////////
if($dopost=="delete")
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Delete From #@__myad where aid='$aid'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功删除一则广告代码！",$ENV_GOBACK_URL);
	exit();
}
else if($dopost=="getjs")
{
	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
	$jscode = "<script src='{$cfg_plus_dir}/ad_js.php?aid=$aid' language='javascript'></script>";
	$showhtml = "<xmp style='color:#333333;background-color:#ffffff'>\r\n\r\n$jscode\r\n\r\n</xmp>";
  $showhtml .= "预览：<iframe name='testfrm' frameborder='0' src='ad_edit.php?aid={$aid}&dopost=testjs' id='testfrm' width='100%' height='200'></iframe>";
  $wintitle = "广告管理-获取JS";
	$wecome_info = "<a href='ad_main.php'><u>广告管理</u></a>::获取JS";
  $win = new OxWindow();
  $win->Init();
  $win->AddTitle("以下为选定广告的JS调用代码：");
  $winform = $win->GetWindow("hand",$showhtml);
  $win->Display();
	exit();
}
else if($dopost=="testjs")
{
	echo "<script src='{$cfg_plus_dir}/ad_js.php?aid=$aid' language='javascript'></script>";
	exit();
}
else if($dopost=="saveedit")
{
	$dsql = new DedeSql(false);
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	$query = "
	 Update #@__myad
	 set
	 typeid='$typeid',
	 adname='$adname',
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
	ShowMsg("成功更改一则广告代码！",$ENV_GOBACK_URL);
	exit();
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__myad where aid='$aid'");
$dsql->Close();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>更改广告</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#98CAEF">
<tr>
  <td height="19" background="img/tbg.gif"><b><a href="ad_main.php"><u>广告管理</u></a></b>&gt;&gt;更改广告</td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
	<table width="100%" border="0" cellspacing="4" cellpadding="4">
        <form action="ad_edit.php" method="post" enctype="multipart/form-data" name="form1">
          <input type='hidden' name='aid' value='<?php echo $aid?>'>
          <input type='hidden' name='dopost' value='saveedit'>
           <tr> 
            <td height="25" align="center">广告位标识：</td>
            <td colspan="2"><?php echo $row['tagname']?></td>
          </tr>
          <tr> 
            <td width="15%" height="25" align="center">广告投放范围：</td>
            <td colspan="2">
			<?php 
           	$tl = new TypeLink(0);
           	$typeOptions = $tl->GetOptionArray($row['typeid'],0,0);
            echo "<select name='typeid' style='width:300'>\r\n";
            echo "<option value='0' selected>投放在没有同名标识的所有栏目</option>\r\n";
            echo $typeOptions;
            echo "</select>";
			$tl->Close();
			?>
			<br>
      （如果在所选栏目找不到指定标识的广告内容，系统会自动搜索父栏目）
			</td>
          </tr>
         <tr> 
            <td height="25" align="center">广告位名称：</td>
            <td colspan="2"><input name="adname" type="text" id="adname" size="30" value="<?php echo $row['adname']?>"></td>
          </tr>
          <tr> 
            <td height="25" align="center">时间限制：</td>
            <td colspan="2"><input class="np" name="timeset" type="radio" value="0"<?php if($row['timeset']==0) echo " checked"; ?>>
              永不过期 
              <input class="np" type="radio" name="timeset" value="1" <?php if($row['timeset']==1) echo " checked"; ?>>
              在设内时间内有效</td>
          </tr>
          <tr> 
            <td height="25" align="center">开始时间：</td>
            <td colspan="2"><input name="starttime" type="text" id="starttime" value="<?php echo GetDateTimeMk($row['starttime'])?>"></td>
          </tr>
          <tr> 
            <td height="25" align="center">结束时间：</td>
            <td colspan="2"><input name="endtime" type="text" id="endtime" value="<?php echo GetDateTimeMk($row['endtime'])?>"></td>
          </tr>
          <tr> 
            <td height="80" align="center">正常显示内容：</td>
            <td width="76%">
              <textarea name="normbody" id="normbody" style="width:80%;height:100"><?php echo $row['normbody']?></textarea>
            </td>
            <td width="9%">&nbsp;</td>
          </tr>
          <tr> 
            <td height="80" align="center">过期显示内容：</td>
            <td> 
            	<textarea name="expbody" id="expbody" style="width:80%;height:100"><?php echo $row['expbody']?></textarea>
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