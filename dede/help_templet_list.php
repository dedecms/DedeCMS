<?
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>模板代码参考--列表模板标记</title>
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
    <td height="19" background="img/tbg.gif"><b>模板代码参考--列表模板标记</b></td>
</tr>
<tr>
<td bgcolor="#FFFFFF" valign="top">
<table width="98%" border="0" cellspacing="2">
        <tr> 
          <td colspan="3">　　列表模板是指显示所有文档分页列表的样式模板。</td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0"><strong>1、定义列表大小<a name="1"></a></strong></td>
        </tr>
        <tr> 
          <td height="29" colspan="3" bgcolor="#FFFFFF">{dede:page pagesize='每页结果条数'/}</td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0"><strong>2、分页文档列表<a name="2"></a></strong></td>
        </tr>
        <tr> 
          <td colspan="3"> <table width="96%" border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="action_tag_test.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记：list<a href="help_templet.php#311" target="_blank"><u>[参考]</u></a>，代码：</td>
                  <td width="156" align="center">&nbsp; </td>
                </tr>
                <tr> 
                  <td colspan="2">{dede:list col='' titlelen='' <br/>
                    infolen='' imgwidth='' imgheight='' orderby=''}{/dede:list}</td>
                </tr>
                <tr> 
                  <td colspan="2"><strong>list固定底层模板变量(即是[field:name/])：</strong><br>
                    id,title,iscommend,color,typeid,ismake,description(同 info),pubdate,senddate,<br>
                    arcrank,click,litpic(同 picname),typedir,typename,arcurl(同 
                    filename),typeurl,<br>
                    stime(pubdate 的&quot;0000-00-00&quot;格式),textlink,typelink,imglink,image<br> 
                    <strong>变动的底层变量：</strong> <br>
                    list标记允许使用附加表里的任何字段作为底层变量，不过要在频道模型中配置。</td>
                </tr>
              </form>
            </table></td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0"><strong>3、定义分页导航标记<a name="3"></a></strong></td>
        </tr>
        <tr> 
          <td height="110" colspan="3">表示[1][2][3]这样的分页导航链接。<br>
            {dede:pagelist listsize='3'/} <br>
            listsize 表示导航数字的长度/2，如listsize=3表示<font color="#660000"> '上一页 [1][2][3][4][5][6][7] 
            下一页' </font>这样的样式。</td>
        </tr>
      </table> </td>
</tr>
</table>
</body>
</html>