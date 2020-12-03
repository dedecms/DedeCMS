<?php 
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>生成HTML</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#98CAEF" align="center">
  <form name="form1" action="makehtml_freelist_action.php" method="get" target='stafrm'>
    <tr> 
      <td colspan="2" background='img/tbg.gif'><table width="98%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="30%" height="18"><strong>更新自由列表HTML：</strong></td>
          <td width="70%" align="right"><input type="button" name="b113" value="管理自由列表" onClick="location='freelist_main.php';" style="width:100" class='nbt'>
          </td>
        </tr>
      </table></td>
    </tr>
    <tr> 
      <td width="108" height="20" valign="top" bgcolor="#FFFFFF">起始ID：</td>
      <td width="377" height="20" valign="top" bgcolor="#FFFFFF">
      	<input name="startid" type="text" id="startid" size="10"<?php  if(!empty($aid)) echo " value='$aid'"; ?>>
        （空或0表示从头开始）
       </td>
    </tr>
    <tr> 
      <td height="20" valign="top" bgcolor="#FFFFFF">结束ID：</td>
      <td height="20" valign="top" bgcolor="#FFFFFF">
      	<input name="endid" type="text" id="endid" size="10"<?php  if(!empty($aid)) echo " value='$aid'"; ?>>
        （空或0表示直到结束ID） 
       </td>
    </tr>
    <tr> 
      <td height="20" bgcolor="#FFFFFF">每批生成：</td>
      <td height="20" bgcolor="#FFFFFF"> <input name="pagesize" type="text" id="pagesize" value="100" size="8">
        个文件</td>
    </tr>
    <tr> 
      <td height="20" colspan="2" bgcolor="#F8FBFB" align="center"> <input name="b112" type="button" value="开始生成HTML" onClick="document.form1.submit();" style="width:100" class='nbt'>
        &nbsp; </td>
    </tr>
  </form>
  <tr bgcolor="#E5F9FF"> 
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
