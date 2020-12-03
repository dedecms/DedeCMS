<?
require_once(dirname(__FILE__)."/config.php");
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select ID From #@__channeltype order by ID desc limit 0,1 ");
$dsql->Close();
$newid = $row['ID']+1;
if($newid<10) $newid = $newid+10;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>新增频道</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<script language="javascript">
<!--
function SelectGuide(fname)
{
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-200;
   window.open("mychannel_field_make.php?f="+fname, "popUpImagesWin", "scrollbars=yes,resizable=no,statebar=no,width=600,height=360,left="+posLeft+", top="+posTop);
}
-->
</script>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <form name="form1" action="action_mychannel_add.php" method="post">
    <tr> 
      <td height="20" colspan="2" background="img/tbg.gif"> <b>&nbsp;<a href="mychannel_main.php"><u>频道模型管理</u></a> 
        &gt; 新增频道模型：</b> </td>
    </tr>
    <tr> 
      <td width="19%" align="center" bgcolor="#FFFFFF">频道ID</td>
      <td width="81%" bgcolor="#FFFFFF"> <input name="ID" type="text" id="ID" size="10" value="<?=$newid?>">
        * （不可更改，并具有唯一性） </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">名字标识</td>
      <td bgcolor="#FFFFFF"> <input name="nid" type="text" id="nid">
        * （不可更改，并具有唯一性） </td>
    </tr>
    <tr>
      <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
      <td bgcolor="#FFFFFF"><font color="#CC0000">频道默认文档模板是 “default/article_名字标识.htm”，列表模板、封面模板类推</font></td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">频道名称</td>
      <td bgcolor="#FFFFFF"> <input name="typename" type="text" id="typename">
        * </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">附加表</td>
      <td bgcolor="#FFFFFF"> <input name="addtable" type="text" id="addtable" value="<?=$cfg_dbprefix?>addon">
        * 
        <input name="ismake" type="checkbox" class="np" id="ismake" value="1">
        我已经手动创建了表 </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">模型性质</td>
      <td bgcolor="#FFFFFF"> <input name="issystem" type="radio" value="0" checked>
        自动模型 
        <input type="radio" name="issystem" value="1">
        系统模型　（如果为<u>系统模型</u>将禁止删除，此选项不可更改） </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">档案发布程序</td>
      <td bgcolor="#FFFFFF"> <input name="addcon" type="text" id="addcon" value="archives_add.php">
        * </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">档案修改程序</td>
      <td bgcolor="#FFFFFF"> <input name="editcon" type="text" id="editcon" value="archives_edit.php">
        * </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">档案管理程序</td>
      <td bgcolor="#FFFFFF"> <input name="mancon" type="text" id="mancon" value="content_list.php">
        * </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">列表附加字段：</td>
      <td bgcolor="#FFFFFF"> <input name="listadd" type="text" id="listadd" size="50"> 
        <br>
        (用&quot;,&quot;分开，可以在列表模板{dede:list}{/dede:list}中用[field:name/]调用) </td>
    </tr>
    <tr> 
      <td height="24" align="center" bgcolor="#FFFFFF">附加字段配置：</td>
      <td rowspan="2" bgcolor="#FFFFFF"> <textarea name="fieldset" style="width:600" rows="12" id="fieldset"></textarea> 
      </td>
    </tr>
    <tr> 
      <td height="110" align="center" valign="top" bgcolor="#FFFFFF"> <br> <input name="fset" type="button" id="fset" value="字段添加向导" onClick="SelectGuide('form1.fieldset')"> 
        <br>
        <br>
        <a href="help_addtable.php" target="_blank"><u>模型附加字段定义参考</u></a> <br>
        (附加表的所有字段均可在<br>
        文档模板中用{dede:field name='fieldname' /}调用) </td>
    </tr>
    <tr bgcolor="#F9FDF0"> 
      <td height="28" colspan="2"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="26%">&nbsp;</td>
            <td width="15%"><input name="imageField" class="np" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
            <td width="59%"><img src="img/button_back.gif" width="60" height="22" onClick="location='mychannel_main.php';" style="cursor:hand"></td>
          </tr>
        </table></td>
    </tr>
  </form>
</table>
</body>
</html>