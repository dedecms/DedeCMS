<?
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>模板代码参考--封面模板标记</title>
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
    <td height="19" background="img/tbg.gif"><b>模板代码参考--封面模板标记</b></td>
</tr>
<tr>
<td bgcolor="#FFFFFF" valign="top">
<table width="98%" border="0" cellspacing="2">
        <tr> 
          <td>　　封面模板标记是仅适合在栏目封面和网站主页，或者用户自定义的单独解析页面使用的标签，一般是整站性质的插件调用。</td>
        </tr>
        <tr> 
          <td bgcolor="#F9FBF0"><strong>按具体功能获取通用标记的代码：</strong></td>
        </tr>
        <tr> 
          <td>　　<a href="#1">投票代码</a> <a href="#2">友情链接</a> <a href="#3">站点新闻</a> 
            <a href="#4">论坛扩展标记</a></td>
        </tr>
        <tr> 
          <td bgcolor="#F9FBF0">１、<strong>投票代码</strong><a name="1"></a></td>
        </tr>
        <tr> 
          <td>　　投票代码使用的标记为vote，可以直接在<a href="vote_main.php"><u><font color="#990000"><strong>投票管理</strong></font></u></a>的页面获取代码或系统生成的表单HTML（主要是方便用户按自己的样式更改）。 
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FBF0">２、<strong>友情链接</strong><a name="2"></a></td>
        </tr>
        <tr> 
          <td bgcolor="#FFFFFF">　　使用标记：friendlink 或 flink [<a href="help_templet.php#36"><u>参考</u></a>]<br />
          	　　正常的情况下显示所有审核过或属性为“首页”的链接，如果你想只显示属性为“首页”的链接，请加上 linktype=2 属性
          	</td>
        </tr>
        <tr> 
          <td> <table width="96%" border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">用文字形式显示所有审核后的链接：</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:flink type='textall' row='4' col='6' titlelen='16'
 tablestyle='width=100% border=0 cellspacing=1 cellpadding=1'/}</textarea> 
                  </td>
                </tr>
              </form>
            </table> <table width="96%" border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">用图文混排形式显示所有审核后的链接：</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:flink type='textimage' row='4' col='6' titlelen='16'
 tablestyle='width=100% border=0 cellspacing=1 cellpadding=1'/}</textarea> 
                  </td>
                </tr>
              </form>
            </table>
            <table width="96%" border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">仅显示不带Logo的审核后的链接：</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:flink type='text' row='4' col='6' titlelen='16'
 tablestyle='width=100% border=0 cellspacing=1 cellpadding=1'/}</textarea> 
                  </td>
                </tr>
              </form>
            </table> <table width="96%" border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">仅显示带Logo的审核后的链接：</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:flink type='image' row='4' col='6' titlelen='16'
 tablestyle='width=100% border=0 cellspacing=1 cellpadding=1'/}</textarea> 
                  </td>
                </tr>
              </form>
            </table></td>
        </tr>
        <tr> 
          <td bgcolor="#F9FBF0">３、<strong>站点新闻</strong><a name="3"></a></td>
        </tr>
        <tr> 
          <td> <table width='96%' border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记： {dede:mynews row='条数' titlelen='标题长度'}Innertext{/dede:mynews}，Innertext支持的字段为：[field:title 
                    /],[field:writer /],[field:senddate /](时间),[field:body /]。 
                  </td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:mynews row='1' titlelen='24'}
[field:title/]([field:writer/]|[field:senddate function='GetDate("@me")'/])
<hr size=1>
[field:body /]
{/dede:mynews}</textarea></td>
                </tr>
              </form>
            </table></td>
        </tr>
        <tr> 
          <td bgcolor="#F9FBF0">４、<strong>论坛扩展标记</strong><a name="4"></a></td>
        </tr>
        <tr> 
          <td>　　请参考相关插件&gt;&gt;</td>
        </tr>
      </table> </td>
</tr>
</table>
</body>
</html>