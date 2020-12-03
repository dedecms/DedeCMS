<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>生成HTML</title>
<link href="base.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" media="all" href="../include/calendar/calendar-win2k-1.css" title="win2k-1" />
<script type="text/javascript" src="../include/calendar/calendar.js"></script>
<script type="text/javascript" src="../include/calendar/calendar-cn.js"></script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" action="action_makehtml_list.php" method="get" target='stafrm'>
    <tr> 
      <td height="20" colspan="2" background='img/tbg.gif'> <table width="98%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="30%" height="18"><strong>更新栏目HTML：</strong></td>
            <td width="70%" align="right"><a href="catalog_main.php"><u>栏目管理</u></a> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td width="108" valign="top" bgcolor="#FFFFFF">选择栏目：</td>
      <td width="377" valign="top" bgcolor="#FFFFFF"> 
        <?
       if(empty($cid)) $cid="0";
       $tl = new TypeLink($cid);
       $typeOptions = $tl->GetOptionArray($cid,$cuserLogin->getUserChannel(),0,1);
       echo "<select name='typeid' style='width:300'>\r\n";
       if($cid=="0") echo "<option value='0' selected>更新所有栏目...</option>\r\n";
       echo $typeOptions;
       echo "</select>";
			 $tl->Close();
		?>
      </td>
    </tr>
    <tr> 
      <td height="20" valign="top" bgcolor="#FFFFFF">更新选项：</td>
      <td height="20" valign="top" bgcolor="#FFFFFF"> <input name="uptype" type="radio" class="np" value="all" checked>
        归档所有文档 
        <input name="uptype" type="radio" class="np" value="1">
        仅归档指定日期之后的文档</td>
    </tr>
    <tr> 
      <td height="20" valign="top" bgcolor="#FFFFFF">指定日期：</td>
      <td height="20" valign="top" bgcolor="#FFFFFF"> 
        <?
		$dayst = GetMkTime("2006-1-2 0:0:0") - GetMkTime("2006-1-1 0:0:0");
		$nowtime = GetDateTimeMk(time() - ($dayst * 365));
		echo "<input name=\"starttime\" value=\"$nowtime\" type=\"text\" id=\"pubdate\" style=\"width:200\">";
		echo "<input name=\"selPubtime\" type=\"button\" id=\"selkeyword\" value=\"选择\" onClick=\"showCalendar('pubdate', '%Y-%m-%d %H:%M:00', '24');\">";
	 ?>
      </td>
    </tr>
    <tr>
      <td height="20" valign="top" bgcolor="#FFFFFF">每次最大创建页数：</td>
      <td height="20" valign="top" bgcolor="#FFFFFF"><input name="maxpagesize" type="text" id="maxpagesize" value="100" size="10">
        个文件 </td>
    </tr>
    <tr> 
      <td height="20" valign="top" bgcolor="#FFFFFF">是否更新子栏目：</td>
      <td height="20" valign="top" bgcolor="#FFFFFF">
	  <input name="upnext" type="radio" class="np" value="1" checked>
        更新子级栏目 
        <input type="radio" name="upnext" class="np" value="0">
        仅更新所选栏目 </td>
    </tr>
    <tr> 
      <td height="20" colspan="2" bgcolor="#FAFAF1" align="center"> <input name="b112" type="button" class="np2" value="开始生成HTML" onClick="document.form1.submit();" style="width:100"> 
      </td>
    </tr>
  </form>
  <tr bgcolor="#E6F3CD"> 
    <td height="20" colspan="2"> <table width="100%">
        <tr> 
          <td width="74%">进行状态： </td>
          <td width="26%" align="right"> <script language='javascript'>
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
    <td colspan="2" id="mtd"> <div id='mdv' style='width:100%;height:100;'> 
        <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
      </div>
      <script language="JavaScript">
	  document.all.mdv.style.pixelHeight = screen.height - 360;
	  </script> </td>
  </tr>
</table>
</body>
</html>
