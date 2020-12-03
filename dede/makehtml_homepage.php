<?
require_once(dirname(__FILE__)."/config.php");
$dsql = new DedeSql(false);
$row  = $dsql->GetOne("Select * From #@__homepageset");
$dsql->Close();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>主页更新向导</title>
<link href="base.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" media="all" href="../include/calendar/calendar-win2k-1.css" title="win2k-1" />
<script type="text/javascript" src="../include/calendar/calendar.js"></script>
<script type="text/javascript" src="../include/calendar/calendar-cn.js"></script>
<script language="javascript">
function SelectTemplets(fname)
{
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-300;
   window.open("../include/dialog/select_templets.php?f="+fname, "poptempWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" action="action_makehtml_homepage.php" target="stafrm" method="post">
  <input type="hidden" name="dopost" value="make">
    <tr> 
      <td height="20" colspan="2" background='img/tbg.gif'>
	  <table width="98%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="30%" height="18"><strong>主页更新向导：</strong></td>
            <td width="70%" align="right">&nbsp;</td>
          </tr>
        </table>
		</td>
    </tr>
    <tr> 
      <td width="177" valign="top" bgcolor="#FFFFFF">选择主页模板：</td>
      <td width="791" valign="top" bgcolor="#FFFFFF">
	    <input name="templet" type="text" id="templet" style="width:300" value="<?=$row['templet']?>"> 
        <input type="button" name="set4" value="浏览..." style="width:60" onClick="SelectTemplets('form1.templet');"> 
      </td>
    </tr>
    <tr> 
      <td height="20" colspan="2" valign="top" bgcolor="#FFFFFF">默认的情况下，生成的主页文件放在CMS的安装目录，如果你的CMS不是安装在网站根目录的，又想把主页创建到网站根目录，那么请用相对路径来表示“主页位置”。例：你的CMS安装在 
        http://www.abc.com/dedecms/ 目录，你想生成的主页为 http://www.abc.com/index.html，那么主页位置就应该用： 
        “../index.html”。</td>
    </tr>
    <tr> 
      <td height="20" valign="top" bgcolor="#FFFFFF">主页位置：</td>
      <td height="20" valign="top" bgcolor="#FFFFFF"><input name="position" type="text" id="position" value="<?=$row['position']?>" size="30"> 
      </td>
    </tr>
    <tr> 
      <td height="20" valign="top" bgcolor="#FFFFFF">相关选项：</td>
      <td height="20" valign="top" bgcolor="#FFFFFF">
	  <input name="saveset" type="radio" value="0" class="np">
       不保存当前选项 
      <input name="saveset" type="radio" class="np" value="1" checked>
      保存当前选项
	</td>
    </tr>
    <tr> 
      <td height="31" colspan="2" bgcolor="#FAFAF1" align="center">
	    <input name="view" type="button" id="view" value="预览主页" onclick="window.open('action_makehtml_homepage.php?dopost=view&templet='+form1.templet.value);">
        　
<input type="submit" name="Submit" value="更新主页HTML"> 
      </td>
    </tr>
  </form>
  <tr bgcolor="#E6F3CD"> 
    <td height="20" colspan="2"><table width="100%">
        <tr> 
          <td width="74%">进行状态： </td>
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
      </table> </td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="2" id="mtd">
	<div id='mdv' style='width:100%;height:100;'> 
        <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%">
        <script language="JavaScript">
	  document.all.mdv.style.pixelHeight = screen.height - 360;
	  </script>
        </iframe>
      </div>
	  </td>
  </tr>
</table>
</body>
</html>
