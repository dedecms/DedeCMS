<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_collection.php");
if($nid=="") 
{
	ShowMsg("参数无效!","-1");	
	exit();
}
$co = new DedeCollection();
$co->Init();
$co->LoadFromDB($nid);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>测试节点</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <tr> 
    <td height="20" background='img/tbg.gif'> <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="30%" height="18"><strong>测试节点：</strong></td>
          <td width="70%" align="right">&nbsp;<input type="button" name="b11" value="返回采集节点管理页" class="np2" style="width:160" onClick="location.href='co_main.php';"></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td height="94" bgcolor="#FFFFFF" align="center">
    	<table width="98%" border="0">
        <tr bgcolor="#F9FCF3"> 
          <td width="13%" height="24" align="center"><b>节点名称：</b></td>
          <td width="87%">&nbsp;<? echo($co->Item["name"]); ?></td>
        </tr>
        <tr> 
          <td height="24" align="center">列表测试信息：</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td height="24" colspan="2">
 <textarea name="r1" id="r1" style="width:100%;height:250"><? $turl = $co->TestList();?></textarea> 
          </td>
        </tr>
        <tr> 
          <td height="24" align="center">网页规则测试：</td>
          <td>(Dedecms里时间日期字段一般是整数类型，如果你看到sortrank、pubdate、senddate是整数，那情况是属正常的)</td>
        </tr>
        <tr> 
          <td height="24" colspan="2" align="center">
         <textarea name="r2" id="r2" style="width:100%;height:250">测试网址: <? echo "$turl \r\n"; $co->TestArt($turl); ?></textarea>
		  </td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td height="28" bgcolor="#FAFAF1">&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?
$co->Close();
?>