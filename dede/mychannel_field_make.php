<?
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>字段添加向导</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
<script language="javascript">
var notAllow = " aid ID typeid typeid2 sortrank iscommend ismake channel arcrank click money title color writer source litpic pubdate senddate adminID memberID description keywords ";
function GetFields()
{
	var fieldname = document.form1.fieldname.value;
	var itemname = document.form1.itemname.value;
	var dtype = document.form1.dtype.value;
	if(document.form1.isnull[0].checked) var isnull = document.form1.isnull[0].value;
	else  var isnull = document.form1.isnull[1].value;
	var vdefault = document.form1.vdefault.value;
	var maxlength = document.form1.maxlength.value;
	var vfunction = document.form1.vfunction.value;
	var vinnertext = document.form1.vinnertext.value;
	if(vinnertext!="") vinnertext += "\r\n";
	if(document.form1.spage[0].checked) var spage = document.form1.spage[0].value;
	else var spage = document.form1.spage[1].value;
	if(isnull==0) var sisnull="false";
	else var sisnull="true";
	if(notAllow.indexOf(" "+fieldname+" ") >-1 ) 
	{
		alert("字段名称不合法，如下名称是不允许的：\n"+notAllow);
		return false;
	}
	if(dtype=="text" && maxlength=="")
	{
		alert("你选择的是文本类型，必须设置最大长度！");
		return false;
	}
	if(itemname=="")
	{
		alert("表单提示名称不能为空！");
		return false;
	}
	if(spage=="no") spage = "";
	revalue =  "<field:"+fieldname+" itemname=\""+itemname+"\" type=\""+dtype+"\"";
	revalue += " isnull=\""+sisnull+"\" default=\""+vdefault+"\" function=\""+vfunction+"\"";
	revalue += " maxlength=\""+maxlength+"\" page=\""+spage+"\">\r\n"+vinnertext+"</field:"+fieldname+">\r\n";
	window.opener.document.<?=$f?>.value += revalue;
	window.opener=true;
  window.close();
}
</script>
</head>
<body topmargin="1" leftmargin="1">
<table width="100%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <form name="form1">
  <tr> 
    <td height="20" colspan="2" background="img/tbg.gif"> <b>&nbsp;<a href="mychannel_main.php"></a>字段添加向导：</b> 
    </td>
  </tr>
  <tr> 
    <td width="26%" align="center" bgcolor="#FFFFFF">字段名称：</td>
    <td width="74%" bgcolor="#FFFFFF" style="table-layout:fixed;word-break:break-all">
    	<input name="fieldname" type="text" id="fieldname"> *（英文，不能和archives表字段重复）
    </td>
  </tr>
  <tr> 
    <td align="center" bgcolor="#FFFFFF">表单提示名称：</td>
    <td bgcolor="#FFFFFF">
    	<input name="itemname" type="text" id="itemname"> *（发布内容时显示的项名字）
    </td>
  </tr>
  <tr> 
    <td align="center" bgcolor="#FFFFFF">数据类型：</td>
    <td bgcolor="#FFFFFF">
	<select name="dtype" id="type" style="width:150">
	<option value="text">单行文本</option>
	<option value="multitext">多行文本</option>
	<option value="htmltext">HTML文本</option>
	<option value="int">整数类型</option>
	<option value="float">小数类型</option>
	<option value="datetime">时间类型</option>
	<option value="img">图片</option>
	<option value="media">多媒体文件</option>
	<option value="addon">附件类型</option>
    </select>
	</td>
  </tr>
  <tr> 
    <td align="center" bgcolor="#FFFFFF">是否允许空：</td>
      <td bgcolor="#FFFFFF">
      	<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="30%">
         <input name="isnull" type="radio" class="np" value="1" checked>是
         &nbsp;
         <input type="radio" name="isnull" class="np" value="0">否
         </td>
            <td width="18%">是否分页：</td>
            <td width="52%">
            <input name="spage" type="radio" class="np" value="split">是
            &nbsp;
            <input name="spage" type="radio" class="np" value="no" checked>否
          </td>
          </tr>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" bgcolor="#FFFFFF">默认值：</td>
    <td bgcolor="#FFFFFF">
    	<input name="vdefault" type="text" id="vdefault">
    </td>
  </tr>
  <tr> 
    <td align="center" bgcolor="#FFFFFF">最大长度：</td>
    <td bgcolor="#FFFFFF">
    	<input name="maxlength" type="text" id="maxlength"> (文本数据必须填写，大于255为text类型)
    </td>
  </tr>
  <tr> 
    <td align="center" bgcolor="#FFFFFF">处理函数：</td>
    <td bgcolor="#FFFFFF"><input name="vfunction" type="text" id="vfunction">
      (可选，用'@me'表示当前项目值参数)</td>
  </tr>
  <tr> 
    <td align="center" bgcolor="#FFFFFF">表单HTML：<br>
      (如果你不想用向导生成的表单，你可以在这里输入你想用的表单的HTML，但表单名必须为“字段名称”)</td>
    <td bgcolor="#FFFFFF"><textarea name="vinnertext" cols="30" rows="5" id="vinnertext"></textarea>
    </td>
  </tr>
  <tr bgcolor="#F9FDF0"> 
    <td height="28" colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="26%">&nbsp;</td>
          <td width="20%"><img src="img/button_ok.gif" width="60" height="22" border="0" style="cursor:hand" onClick="GetFields()"></td>
          <td width="54%"><img src="img/button_reset.gif" width="60" height="22" border="0" style="cursor:hand" onClick="form1.reset()"></td>
        </tr>
      </table>
    </td>
  </tr>
</form>
</table>
</body>
</html>