<?php 
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
$dsql = new DedeSql(false);
$dsql->SetSql("Select count(aid) as dd From #@__courl where nid='$nid'");
$dsql->Execute();
$row = $dsql->GetObject();
$dd = $row->dd;
$dsql->Close();
if($dd==0)
{
	$unum = "没有记录或从来没有采集过这个节点！";
}
else
{
	$unum = "共有 $dd 个历史种子网址！<a href='javascript:SubmitNew();'>[<u>更新种子网址，并采集</u>]</a>";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>采集节点</title>
<script language='javascript'>
	function SubmitNew()
	{
		document.form1.totalnum.value = "0";
		document.form1.submit();
	}
</script>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center">
  <tr> 
      <td height="20" colspan="2" background='img/tbg.gif'>
      	<table width="98%" border="0" cellpadding="0" cellspacing="0">
         <form name='form2' action='co_url.php' target='stafrm'>
         	<input type='hidden' name='small' value='1'>
         	<input type='hidden' name='nid' value='<?php echo $nid?>'>
         	</form>
          <tr> 
            <td width="30%" height="18"><strong>采集指定节点：</strong></td>
            <td width="70%" align="right">
            	<input type="button" name="b11" value="查看已下载"  class='nbt' onClick="document.form2.submit();" style="width:90"> 
              <input type="button" name="b12" value="采集节点管理"  class='nbt' style="width:90" onClick="location.href='co_main.php';">
              <input type="button" name="b13" value="导出数据"  class='nbt' style="width:90" onClick="location.href='co_export.php?nid=<?php echo $nid?>';">
              </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td width="108" valign="top" bgcolor="#FFFFFF">节点名称：</td>
      <td width="377" valign="top" bgcolor="#FFFFFF"> 
        <?php echo $co->Item["name"]?>
      </td>
    </tr>
    <tr> 
      <td height="20" valign="top" bgcolor="#FFFFFF">种子网址数：</td>
      <td height="20" valign="top" bgcolor="#FFFFFF"> 
        <?php echo $unum?>
      </td>
    </tr>
    <form name="form1" action="co_getsource_url_action.php" method="get" target='stafrm'>
    <input type='hidden' name='nid' value='<?php echo $nid?>'>
    <input type='hidden' name='totalnum' value='<?php echo $dd?>'>
    <input type='hidden' name='startdd' value='0'>
    <tr> 
      <td height="20" bgcolor="#FFFFFF">每页采集：</td>
      <td height="20" bgcolor="#FFFFFF">
      	<input name="pagesize" type="text" id="pagesize" value="5" size="3">
        条，线程数： 
        <input name="threadnum" type="text" id="threadnum" value="1" size="3">
        间隔时间： 
        <input name="sptime" type="text" id="sptime" value="0" size="3">
        秒（防刷新的站点需设置）</td>
    </tr>
    <tr> 
      <td height="20" bgcolor="#FFFFFF">附加选项：</td>
      <td height="20" bgcolor="#FFFFFF">
      	<input name="islisten" type="radio" class="np" value="0" checked>
        不下载曾下载的网址
        <input name="islisten" type="radio" class="np" value="-1">
        仅下载未下载内容
      	<input name="islisten" type="radio" class="np" value="1">
      	重新下载所有内容
      	</td>
    </tr>
    <tr> 
      <td height="20" colspan="2" bgcolor="#F8FBFB" align="center">
      	<input name="b112" type="button"  class='nbt' value="开始采集网页" onClick="document.form1.submit();" style="width:100">　
      	<input type="button" name="b113" value="查看种子网址"  class='nbt' onClick="document.form2.submit();" style="width:100">
      </td>
    </tr>
  </form>
    <tr bgcolor="#E5F9FF"> 
      <td height="20" colspan="2">
<table width="100%">
          <tr> 
            <td width="74%">节点的种子网址： </td>
            <td width="26%" align="right">
            	<script language='javascript'>
            	function ResizeDiv(obj,ty)
            	{
            		if(ty=="+") document.all[obj].style.pixelHeight += 50;
            		else if(document.all[obj].style.pixelHeight>80) document.all[obj].style.pixelHeight = document.all[obj].style.pixelHeight - 50;
            	}
            	</script>
            	[<a href='#' onClick="ResizeDiv('mdv','+');">增大</a>] [<a href='#' onClick="ResizeDiv('mdv','-');">缩小</a>]
            	</td>
          </tr>
        </table></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="2" id="mtd">
	  <div id='mdv' style='width:100%;height:100;'>
	  <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"<?php if($dd>0) echo " src=co_url.php?nid=$nid&small=1";?>></iframe>
	  </div>
	  <script language="JavaScript">
	  document.all.mdv.style.pixelHeight = screen.height - 360;
	  </script>
	  </td>
    </tr>
</table>
</body>
</html>
<?php 
$co->Close();
?>