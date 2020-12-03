<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('temp_Other');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($dopost)) $dopost = "";
//////////////////////////////////////////
if($dopost=="save")
{
	$tagname = trim($tagname);
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select typeid From #@__mytag where typeid='$typeid' And tagname like '$tagname'");
	if(is_array($row)){
		$dsql->Close();
		ShowMsg("在相同栏目下已经存在同名的标记！","-1");
		exit();
	}
	$starttime = GetMkTime($starttime);
	$endtime = GetMkTime($endtime);
	$inQuery = "
	 Insert Into #@__mytag(typeid,tagname,timeset,starttime,endtime,normbody,expbody)
	 Values('$typeid','$tagname','$timeset','$starttime','$endtime','$normbody','$expbody');
	";
	$dsql->SetQuery($inQuery);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功增加一个自定义标记！","mytag_main.php");
	exit();
}
$startDay = mytime();
$endDay = AddDay($startDay,30);
$startDay = GetDateTimeMk($startDay);
$endDay = GetDateTimeMk($endDay);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>增加自定义标记</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript">
function checkSubmit()
{
	if(document.form1.tagname.value=="")
	{
		alert("标记名称不能为空！");
		document.form1.tagname.focus();
		return false;
	}
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#98CAEF">
<tr>
  <td height="19" background="img/tbg.gif"><b><a href="mytag_main.php"><u>自定义标记管理</u></a></b>&gt;&gt;增加新标记</td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
	<table width="100%" border="0" cellspacing="4" cellpadding="4">
        <form action="mytag_add.php" method="post" enctype="multipart/form-data" name="form1" onSubmit="return checkSubmit()">
          <input type='hidden' name='dopost' value='save'>
          <tr> 
            <td height="25" colspan="3">自定义标记的调用方法：<br/>
              {dede:mytag name='标记名称' ismake='是否含板块代码（yes 或 no）' typeid='栏目ID'/} 
              <br/>
              1、name 标记名称，该项是必须的属性，以下 2、3是可选属性；<br/>
              2、ismake 默认是 no 表示设定的纯HTML代码， yes 表示含板块标记的代码；<br/>
              3、typeid 表示所属栏目的ID，默认为 0 ，表示所有栏目通用的显示内容，在列表和文档模板中，typeid默认是这个列表或文档本身的栏目ＩＤ。</td>
          </tr>
          <tr> 
            <td width="15%" height="25" align="center">所属栏目：</td>
            <td colspan="2"> 
              <?php 
           	$tl = new TypeLink(0);
           	$typeOptions = $tl->GetOptionArray(0,0,0);
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
            <td colspan="2"><input name="tagname" type="text" id="tagname"></td>
          </tr>
          <tr> 
            <td height="25" align="center">时间限制：</td>
            <td colspan="2"><input name="timeset" type="radio" value="0" checked>
              永不过期 
              <input type="radio" name="timeset" value="1">
              在设内时间内有效</td>
          </tr>
          <tr> 
            <td height="25" align="center">开始时间：</td>
            <td colspan="2"><input name="starttime" type="text" id="starttime" value="<?php echo $startDay?>"></td>
          </tr>
          <tr> 
            <td height="25" align="center">结束时间：</td>
            <td colspan="2"><input name="endtime" type="text" id="endtime" value="<?php echo $endDay?>"></td>
          </tr>
          <tr> 
            <td height="80" align="center">正常显示内容：</td>
            <td width="76%"> <textarea name="normbody" id="normbody" style="width:80%;height:100"></textarea>
            </td>
            <td width="9%">&nbsp;</td>
          </tr>
          <tr> 
            <td height="80" align="center">过期显示内容：</td>
            <td> <textarea name="expbody" id="expbody" style="width:80%;height:100"></textarea>
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
"