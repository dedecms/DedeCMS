<?php 
require_once(dirname(__FILE__)."/../config.php");
require_once(dirname(__FILE__)."/../../include/inc_typelink.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>批量生成缩略图</title>
<link href="../css_body.css" rel="stylesheet" type="text/css" />
<script src="../main.js" language="javascript"></script>
</head>
<body>
<div class="bodytitle">
	<div class="bodytitleleft"></div>
	<div class="bodytitletxt">批量管理</div>
</div>
<table width="96%" border="0" cellpadding="1" cellspacing="1" align="center" class="tbtitle" style="background:#E2F5BC;">
	 <form name='form2' action='../content_list.php' method="get" target='stafrm'>
   <input type='hidden' name='nullfield' value='ok'>
  </form>
  <form name="form1" action="makeminiature_action.php" method="get" target='stafrm'>
  <tr> 
    <td height="20" colspan="2">
    	<table width="98%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td height="18"><strong>更新缩略图：</strong>（本插件由网友“<a href='mailto:smpluckly@gmail.com'><u>落梦天蝎[beluckly]</u></a>”编写）</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td width="108" valign="top" bgcolor="#FFFFFF">选择栏目：</td>
    <td width="377" valign="top" bgcolor="#FFFFFF">
   <?php
   $opall = 1;
   echo GetTypeidSel('form1','typeid','selbt1',0,0,'请选择...','../');
   ?>
   </td>
  </tr>
  <tr>
    <td height="20" valign="top" bgcolor="#FFFFFF">起始ID：</td>
    <td height="20" valign="top" bgcolor="#FFFFFF"><input name="startid" type="text" id="startid" size="10">
      （空或0表示从头开始）</td>
  </tr>
  <tr> 
    <td height="20" valign="top" bgcolor="#FFFFFF">结束ID：</td>
    <td height="20" valign="top" bgcolor="#FFFFFF"><input name="endid" type="text" id="endid" size="10">
      （空或0表示直到结束ID） </td>
  </tr>
  <tr> 
    <td height="20" valign="top" bgcolor="#FFFFFF">生成类型：</td>
    <td height="20" valign="top" bgcolor="#FFFFFF"><input type=radio class=np name=isall id=isall value=1>全部提取第一个图片生成
      <input type=radio class=np name=isall id=isall value=2 checked=1>已有上传缩图不生成</td>
  </tr>
  <tr>
    <td height="20" valign="top" bgcolor="#FFFFFF">生成缩图类型：</td>
    <td height="20" valign="top" bgcolor="#FFFFFF"><input type=radio class=np name=maketype id=maketype value=1>扭曲变形型&nbsp;&nbsp;<input type=radio class=np name=maketype id=maketype value=2>比例缩放型&nbsp;&nbsp;<input type=radio class=np name=maketype id=maketype value=3>部分裁剪型&nbsp;&nbsp;<input type=radio class=np name=maketype id=maketype value=4 checked="1">背景填充型&nbsp;&nbsp;背景色：<input type="text" name="backcolor1" id="backcolor1" size=5 value="255">&nbsp;&nbsp;<input type="text" name="backcolor2" id="backcolor2" size=5 value="255">&nbsp;&nbsp;<input type="text" name="backcolor3" id="backcolor3" size=5 value="255"></td>
  </tr>
  <tr> 
    <td height="20" valign="top" bgcolor="#FFFFFF">缩略图宽和高：</td>
    <td height="20" valign="top" bgcolor="#FFFFFF">宽：<input name="imgwidth" type="text" id="imgwidth" size="10" value="240">
      高：<input name="imgheight" type="text" id="imgheight" size="10" value="180"> </td>
  </tr>
    <tr> 
      <td height="20" bgcolor="#FFFFFF">每页生成：</td>
      <td height="20" bgcolor="#FFFFFF"> <input name="pagesize" type="text" id="pagesize" value="10" size="8">
        个文件</td>
    </tr>
    <tr> 
      <td height="35" colspan="2" bgcolor="#FAFAF1" align="center">
      	<input name="b112" type="button" value="开始生成缩略图" onClick="document.form1.submit();" class="inputbut" />
        &nbsp;
        <input type="button" name="b113" value="查看所有文档" onClick="document.form2.submit();" class="inputbut" />      </td>
    </tr>
  </form>
  <tr bgcolor="#E5F9FF"> 
    <td height="20" colspan="2"> <table width="100%">
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