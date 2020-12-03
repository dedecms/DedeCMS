<?
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>模板代码参考--通用模板标记</title>
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
    <td height="19" background="img/tbg.gif"><b>模板代码参考--通用模板标记</b></td>
</tr>
<tr>
<td bgcolor="#FFFFFF" valign="top">
<table width="98%" border="0" cellspacing="2">
        <tr> 
          <td colspan="3">　　通用模板标记适用于所有模板，但可能会因为不同环境（所在的模板页）而有一定的不同含义，如果你还不了解DedeCms模板的基本结构，请先阅读一下： 
            <a href="help_templet.php#2"><u>模板标记参考 -&gt; DedeCms模板制作规范</u></a> 
            这一章。</td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#F9FBF0"><strong>概念解析：</strong></td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#FFFFFF"> 　<strong>　</strong><font color="#990000">1、环境变量：</font>是指某些属性的默认值在不同的模板中会改变的变量，如typeid，在板块模板中，默认值为0，表示所有分类；但在列表或栏目封面模板中，默认值为这个栏目的ＩＤ；在文档中则默认值是这个文档所属栏目的栏目ＩＤ。</td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0"><strong>按具体功能获取通用标记的代码：</strong></td>
        </tr>
        <tr> 
          <td colspan="3">　　<a href="#1"><u>最新文档列表</u></a> <a href="#2"><u>最新图片列表</u></a> 
            <a href="#3"><u>推荐文档列表</u></a> <a href="#4"><u>热门文档列表</u></a> <a href="#5"><u>最新专题列表</u></a> 
            <a href="#6"><u>栏目列表</u></a> <a href="#7"><u>自定义标记</u></a> <a href="#8"><u>系统变量</u></a> 
            <a href="#9"><u>引入一个文件</u></a> </td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0">１、<strong>最新文档列表</strong><a name="1"></a></td>
        </tr>
        <tr> 
          <td colspan="3"> 
            <table width="96%" border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记：arclist<a href="help_templet.php#31" target="_blank"><u>[参考]</u></a>，代码：</td>
                  <td width="156" align="center"> <input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr>
                  <td colspan="2"><textarea name="partcode" style='width:100%' rows="6" id="partcode">{dede:arclist typeid='' titlelen='28' row='10' col='1'}
·<a href='[field:arcurl/]'>[field:title/]</a><br>
{/dede:arclist}</textarea></td>
                </tr>
                <tr> 
                  <td colspan="2"><strong>arclist底层模板变量(即是[field:name/])：</strong><br>
                    id,title,iscommend,color,typeid,ismake,description(同 info),pubdate,senddate,<br>
                    arcrank,click,litpic(同 picname),typedir,typename,arcurl(同 
                    filename),typeurl,<br>
                    stime(pubdate 的&quot;0000-00-00&quot;格式),textlink,typelink,imglink,image</td>
                </tr>
              </form>
            </table></td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0">２、<strong>最新图片列表</strong><a name="2"></a></td>
        </tr>
        <tr> 
          <td colspan="3">
		  <table width="96%" border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记：arclist<a href="help_templet.php#31" target="_blank"><u>[参考]</u></a>，代码：</td>
                  <td width="156" align="center">&nbsp;</td>
                </tr>
                <tr> 
                  <td colspan="2">{dede:arclist typeid='0' titlelen='24' row='2' 
                    col='4' imgwidth='120' imgheight='90'}<br>
                    &lt;table width='120' border='0' align=&quot;center&quot; 
                    cellpadding='2' cellspacing='1' bgcolor='#E6EAE3'&gt;<br>
                    &lt;tr align='center'&gt;<br>
                    &lt;td bgcolor='#FFFFFF'&gt;[field:imglink/]&lt;/td&gt;<br>
                    &lt;/tr&gt;<br>
                    &lt;tr align='center'&gt; <br>
                    &lt;td height='20' bgcolor=&quot;#F8FCEF&quot;&gt;[field:textlink/]&lt;/td&gt;<br>
                    &lt;/tr&gt;<br>
                    &lt;/table&gt;<br>
                    {/dede:arclist} </td>
                </tr>
              </form>
            </table>
			</td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0">３、<strong>推荐文档列表</strong><a name="3"></a></td>
        </tr>
        <tr> 
          <td colspan="3">
		  <table width='96%' border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记：arclist<a href="help_templet.php#31" target="_blank"><u>[参考]</u></a>，代码：</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:arclist typeid='' type='commend' titlelen='28' row='10' col='1'}
·<a href='[field:arcurl/]'>[field:title/]</a><br>
{/dede:arclist}</textarea></td>
                </tr>
              </form>
            </table></td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0">４、<strong>热门文档列表</strong><a name="4"></a></td>
        </tr>
        <tr> 
          <td colspan="3"><table width='96%' border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记：arclist<a href="help_templet.php#31" target="_blank"><u>[参考]</u></a>，代码：</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:arclist typeid='' orderby='click' titlelen='28' row='10' col='1'}
·<a href='[field:arcurl/]'>[field:title/]</a><br>
{/dede:arclist}</textarea></td>
                </tr>
              </form>
            </table></td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0">５、<strong>最新专题列表</strong><a name="5"></a></td>
        </tr>
        <tr> 
          <td colspan="3"><table width='96%' border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记：arclist<a href="help_templet.php#31" target="_blank"><u>[参考]</u></a>，代码：</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:specart typeid='' titlelen='28' row='10' col='1'}
·<a href='[field:arcurl/]'>[field:title/]</a><br>
{/dede:specart}</textarea></td>
                </tr>
              </form>
            </table></td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0">６、<strong>栏目列表</strong><a name="6"></a></td>
        </tr>
        <tr> 
          <td colspan="3"><table width='96%' border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记：channel[参考]，代码：</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:channel type='top'}
<a href="[field:typelink/]">[field:typename/]</a> 
{/dede:channel}</textarea></td>
                </tr>
              </form>
            </table></td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0">７、<strong>自定义标记</strong><a name="7"></a></td>
        </tr>
        <tr> 
          <td colspan="3"><table width='96%' border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记：mytag[参考]，代码：</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:mytag typeid='' name='标记名称' ismake='0'/}</textarea></td>
                </tr>
              </form>
            </table></td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0">８、<strong>系统变量</strong><a name="8"></a></td>
        </tr>
        <tr> 
          <td colspan="3"><table width='96%' border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记：global，代码：</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr>
                  <td colspan="2"><textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:global name='变量名'/}</textarea></td>
                </tr>
                <tr> 
                  <td colspan="2">常用变量：cfg_webname(网站名称)、cfg_cmspath(CMS安装目录)、cfg_templeturl(模板网址)、cfg_phpurl(插件网址)</td>
                </tr>
              </form>
            </table></td>
        </tr>
        <tr> 
          <td colspan="3" bgcolor="#F9FBF0">９、<strong>引入一个文件</strong><a name="9"></a></td>
        </tr>
        <tr> 
          <td colspan="3">
		  <table width='96%' border="0" cellspacing="2" cellpadding="2">
              <form name="form1" action="tag_test_action.php" target="_blank" method="post">
                <input type="hidden" name="dopost" value="make">
                <tr> 
                  <td width="430">使用标记：include，代码：(file 文件名 ismake 是否包含模板标记，如果包含用 
                    ismake='yes')</td>
                  <td width="156" align="center"><input type="submit" name="Submit" value="预览" class="np" style="width:60px"></td>
                </tr>
                <tr> 
                  <td colspan="2"> <textarea name="partcode" style='width:100%' rows="4" id="partcode">{dede:include file='文件名' ismake=''/}</textarea></td>
                </tr>
              </form>
            </table></td>
        </tr>
      </table> </td>
</tr>
</table>
</body>
</html>