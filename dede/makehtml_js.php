<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>生成HTML</title>
<link href="base.css" rel="stylesheet" type="text/css">
<script language="javascript">
function SelectTemplets(fname)
{
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-300;
   window.open("../include/dialog/select_templets.php?&activepath=<?=urlencode($cfg_templets_dir.'/plus')?>&f="+fname, "poptempWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" action="makehtml_js_action.php" method="get" target='stafrm'>
    <tr> 
      <td height="20" colspan="2" background='img/tbg.gif'> <table width="98%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="30%" height="18"><strong>获取栏目JS文件：</strong></td>
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
       if($cid=="0") echo "<option value='0' selected>所有栏目...</option>\r\n";
       echo $typeOptions;
       echo "</select>";
			 $tl->Close();
		?>
      </td>
    </tr>
    <tr> 
      <td height="40" bgcolor="#FFFFFF">JS文件：</td>
      <td height="40" bgcolor="#FFFFFF">
	  <font color="#660000">
	  <? echo "&lt;script src='".$cfg_plus_dir."/js/".$cid.".js' language='javascript'&gt;&lt;/script&gt;"; ?>
	  </font>
	  </td>
    </tr>
    <tr>
      <td height="20" valign="top" bgcolor="#FFFFFF">模板文件：</td>
      <td height="20" valign="top" bgcolor="#FFFFFF"><input name="templet" type="text" id="templet" style="width:300" value="plus/js.htm"> 
        <input type="button" name="set4" value="浏览..." style="width:60" onClick="SelectTemplets('form1.templet');"> 
      </td>
    </tr>
    <tr> 
      <td height="20" valign="top" bgcolor="#FFFFFF">更新选项：</td>
      <td height="20" valign="top" bgcolor="#FFFFFF">
	    <input type="radio" name="uptype" value="all" class="np">
        更新所有栏目 
        <input name="uptype" type="radio" value="onlyme" class="np" checked>
        仅当前文件
	  </td>
    </tr>
    <tr> 
      <td height="20" colspan="2" bgcolor="#FAFAF1" align="center"> <input name="b112" type="button" class="np2" value="生成/更新JS文件" onClick="document.form1.submit();" style="width:120"> 
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
