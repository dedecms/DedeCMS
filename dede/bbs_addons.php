<?
require("config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>附加论坛插件</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
  <tr>
    <td width="100%" height="24" colspan="2" background="img/tbg.gif">
      <b>§论坛扩展代码§</b>    </td>
  </tr>
  <tr>
    <td height="250" colspan="2" bgcolor="#FFFFFF"><table width="90%"  border="0" align="center" cellpadding="2" cellspacing="2">
      <tr>
        <td colspan="2">　　由于PHP论坛种类繁多，织梦平台不可能花很大心思一一整合这些东西，本版本通过直接操作表的模板标记Loop，提供了直接调用这些论坛最新主题的代码，你只要把这些板块代码复制到主页模板或板块模板中即可使用(论坛与Dedecms必须在同一数据库)。<br>
          　　Loop标记是DedeCms未来版本新为结构预备的标签，在V2.X版本版本中，由于文章的基本信息和文章内容混合在一起，如果使用动态列表，性能会比较低，在未来版本中会将文章的基本信息和实际内容分离，但在文章模板中，你可以通过loop标记来连接两个表，这样大大的增强系统的灵活性和提高整个系统的性能，并且对于图片、Flash、电影、软件等信息也将被分离，请密切留意DedeCms的3.0版本。<br>
          　　在此不妨讨论一下这几个论坛：Discuz的结构和Phpwind的结构惊人的相近，不排除谁仿了谁的可能，但两者都是比较优秀的，至于VBB，则感觉有点混乱，PHPBB功能相对而言过于简单，我相信大家都知道应该选择那种论坛的了，在DedeCms的以后版本中，不排除自带论坛的可能性。</td>
      </tr>
	  <form name="mycode1" action="make_part_test.php" target="_blank" method="post">
      <tr>
        <td bgcolor="#F3F3F3">Discuz论坛：          </td>
        <td align="right" bgcolor="#F3F3F3"><input name="b1" type="submit" id="b1" value=" 预览 "></td>
      </tr>
      <tr>
        <td colspan="2"><textarea name="testcode" style="width:600" rows="8" id="testcode">论坛最新主题：<br>
{dede:loop table="cdb_threads" sort="tid" row="10"}
<a href="/dz/viewthread.php?tid=[loop:field name='tid'/]">
·[loop:field name="subject" function="substr" parameter="0,30"/]
([loop:field name="lastpost" function="date" parameter="m-d H:M"/])
</a>
<br>
{/dede}</textarea></td>
      </tr>
	  </form>
	  <form name="mycode2" action="make_part_test.php" target="_blank" method="post">
      <tr>
        <td bgcolor="#F3F3F3">PHPWIND论坛： </td>
        <td align="right" bgcolor="#F3F3F3"><input name="b2" type="submit" id="b2" value=" 预览 "></td>
      </tr>
      <tr>
        <td colspan="2"><textarea name="testcode" style="width:600" rows="8" id="testcode">论坛最新主题：<br>
{dede:loop table="pw_threads" sort="tid" row="10"}
<a href='/phpwind/read.php?tid=[loop:field name="tid"/]'>
·[loop:field name="subject" function="substr" parameter="0,30"/]
([loop:field name="lastpost" function="date" parameter="m-d H:M"/])
</a>
<br>
{/dede}</textarea></td>
      </tr>
	   </form>
	  <form name="mycode2" action="make_part_test.php" target="_blank" method="post">
      <tr>
        <td bgcolor="#F3F3F3">VBB论坛： </td>
        <td align="right" bgcolor="#F3F3F3"><input name="b3" type="submit" id="b3" value=" 预览 "></td>
      </tr>
      <tr>
        <td colspan="2"><textarea name="testcode" style="width:600" rows="8" id="testcode">论坛最新讨论：<br>
{dede:loop table="thread" sort="threadid" row="10"}
<a href='/vbb/showthread.php?threadid=[loop:field name="threadid"/]'>
·[loop:field name="title" function="substr" parameter="0,30"/]
([loop:field name="lastpost" function="date" parameter="m-d H:M"/])
</a>
<br>
{/dede}</textarea></td>
      </tr>
	   </form>
	  <form name="mycode2" action="make_part_test.php" target="_blank" method="post">
      <tr>
        <td bgcolor="#F3F3F3">PHPBB论坛： </td>
        <td align="right" bgcolor="#F3F3F3"><input name="b4" type="submit" id="b4" value=" 预览 "></td>
      </tr>
      <tr>
        <td colspan="2"><b>
          <textarea name="testcode" style="width:600" rows="7" id="testcode">{dede:loop table="phpbb_topics" sort="topic_id" row="10"}
<a href='/phpbb/viewtopic.php?t=[loop:field name="topic_id"/]'>
·[loop:field name="topic_title" function="substr" parameter="0,30"/]
</a>
([loop:field name="topic_time" function="date" parameter="m-d H:M"/])
<br>
{/dede}</textarea>
        </b></td>
      </tr>
	  </form>
      <tr>
        <td colspan="2">&nbsp;</td>
        </tr>
    </table>
    
    </td>
  </tr>
</table>
</body>
</html>