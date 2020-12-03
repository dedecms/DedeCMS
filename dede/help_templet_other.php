<?
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>模板代码参考--其它模板</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<style type="text/css">
<!--
.style2 {color: #CC0000}
.style4 {color: #0000FF}
.style5 {color: #3300FF}
.style6 {
	color: #FF0000;
	font-weight: bold;
}
.style7 {color: #993300}
-->
</style>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
<tr>
    <td height="19" background="img/tbg.gif"><b>模板代码参考--其它模板</b></td>
</tr>
<tr>
    <td bgcolor="#FFFFFF" valign="top"> <table width="98%" border="0" cellspacing="2" cellpadding="2">
        <tr> 
          <td>除了基本的模板外，DedeCms系统还包含管理模板、搜索模板、专题列表模板等特殊模板的标记，这些标记如果含胡和其它标记同名的标记，则一般是功能类似的。</td>
        </tr>
        <tr> 
          <td bgcolor="#F9FCF3"><a name="1"></a><strong>专题列表模板</strong></td>
        </tr>
        <tr> 
          <td height="35"><a href="help_templet_list.php">请参考列表模板标记&gt;&gt;</a></td>
        </tr>
        <tr> 
          <td bgcolor="#F9FCF3"><a name="2"></a><strong>搜索列表模板</strong></td>
        </tr>
        <tr> 
          <td height="35"><a href="help_templet_list.php">请参考列表模板标记&gt;&gt;</a></td>
        </tr>
        <tr>
          <td bgcolor="#F9FCF3"><a name="3"></a><strong>系统模板标记</strong></td>
        </tr>
        <tr> 
          <td height="45">系统模板主要用到的是global和datalist标记。<br>
            <font color="#660000">1、global标记 </font><br>
            {dede:global name='变量名称'/}<br>
            <font color="#660000">2、datalist 标记一般用法是</font><br>
            {dede:detalist}<br>
            [field:字段/] ....<br>
            {/dede:detalist} </td>
        </tr>
      </table></td>
</tr>
</table>
</body>
</html>