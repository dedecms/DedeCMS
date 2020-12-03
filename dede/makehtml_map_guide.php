<?
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>更新网站地图</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" action="makehtml_map.php" method="get" target='stafrm'>
    <tr> 
      <td height="20" colspan="2" background='img/tbg.gif'> <table width="98%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="30%" height="18"><strong>更新网站地图：</strong></td>
            <td width="70%" align="right"></td>
          </tr>
        </table></td>
    </tr>
    <tr height="20" bgcolor="#FFFFFF"> 
      <td width="25%">地图类型：</td>
      <td>
      	<input name="dopost" type="radio" id="mdopost" class="np" value="site" checked>
      	普通地图
      	&nbsp;
      	<input name="dopost" type="radio" id="mdopost" class="np" value="rss">
      	RSS地图
      </td>
    </tr>
    <tr> 
      <td height="20" colspan="2" bgcolor="#FAFAF1" align="center">
      	<input name="b112" type="button" class="np2" value="开始更新" onClick="document.form1.submit();" style="width:100"> 
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
