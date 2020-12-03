<?
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>附加表设计说明</title>
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
    <td height="19" background="img/tbg.gif">&nbsp;<b>织梦内容管理系统自定义模型附加字段的定义</b></td>
</tr>
<tr>
    <td bgcolor="#FFFFFF" valign="top"> 
      <table width="98%" border="0" cellspacing="2">
        <tr> 
          <td colspan="3" valign="top" style="line-height:160%"> <strong><font color="#990000"> 
            概述：<br>
            </font></strong> DedeCms使用通用参数表＋附模型的方式来定义频道数据的差异，通用参数表保存在dede_archives表中，对于所有频道类型通用参数实际基本上是一致的，不同的频道之间通过附加表实现数据的差异化，因此要设计一个自定义模型，就必须先建立一个附加表，在附加表中，有两个字段是必须的，一个 
            aid （INT 非递增类型的主键）， 这是用来关连主表的字段， 另一个是 typeid （INT类型），表示所属的栏目（用于删除栏目时方便清理附加表的数据），其它的字段数据，必须在频道模型中定义，每个字段用一个配置语句表示，具体参数如下：<br> 
            <hr>
            <p><strong><font color="#990000"> </font></strong>配置参数格式：<br>
              <font color="#660000">&lt;field:字段名称 itemname=&quot;表单提示名称&quot; 
              type=&quot;类型&quot; isnull=&quot;&quot; default=&quot;&quot; rename=&quot;&quot; 
              function=&quot;函数名称('@me')&quot; maxlength=&quot;&quot; &gt;<br>
              <font color="#FF0000">表单样式（表单名必须等于字段名称）</font><br>
              &lt;/field:字段名称&gt;</font><br>
              属性如下：<br>
              <strong>一、type 是必须的属性，包含的数据类型有：</strong><br>
              <font color="#990000">1、type=&quot;int&quot;</font>：整数类型；<br>
              &nbsp;　<font color="#990000">type=&quot;float&quot;</font>：小数类型； 
              <br>
              <font color="#990000">2、type=&quot;datetime&quot;</font>：日期类型，保存在数据库中为linux时间截，读出时需要用function处理，即是：{dede:field 
              name='' function=&quot;date('format',@me)&quot;/}； <br>
              <font color="#990000">3、type=&quot;text&quot;</font>：单行文本类型数据； <br>
              &nbsp;　<font color="#990000">type=&quot;multitext&quot;</font>：多行文本； 
              <br>
              <font color="#990000">4、type=&quot;htmltext&quot;</font>：HTML文本数据（载入表单时将使用可视化编辑器），数据为text类型；<br>
              <font color="#990000">5、type=&quot;img&quot;</font>：图片集合，是一种放置一个或多个图片的链接。<br>
              <font color="#990000">6、type=&quot;addon&quot;</font>：附件集合，一种存放软件、或其它附件文档的链接。<font color="#333333"><br>
              </font><font color="#990000">7</font><font color="#990000">、type=&quot;media&quot;</font>：多媒体文件。<font color="#333333"> 
              <br>
              </font><strong>二、isnull 是必须的属性<br>
              </strong>表示字段是否允许为空，用 true(允许) 和 false（不允许） 表示<br>
              <strong>三、default 是可选的属性，表示字段的默认值。<br>
              </strong><strong>四、rename 是可选的属性，表示字段名和主档案表有冲突时，更改引入名称（仅系统模型有效）。<br>
              </strong><strong>五、function 是可选的属性<br>
              </strong>function定义的函数统一放置在：“CMS目录/include/inc_channel_unit_functions.php”文件内。<br>
              表示实际返回的值是执行这个函数后的返回值。<br>
              如：&lt;field:rank type='number' function=&quot;GetRank('@me')&quot;&gt;&lt;/field:rank&gt;<br>
              表示执行了 GetRank($rank) 后返回的值，而不是 rank 本身。<br>
              <strong>六、maxlength 是可选属性，表示字段的最大字节长度。<br>
              </strong><strong>七、itemname 表单的提示名称<br>
              八、page='split' 字段内容是否分页显示，仅且仅有一个text类型的字段使用，如果设置了该项，内容中有#p#标记，系统会自动对其分页显示。<br>
              </strong><strong>九、InnerText 部份<br>
              </strong>如果系统没有指定频道的内容发布和编辑页面，则使用自动生成的表单，对于一些特殊的情况，可以在InnerText定义这个表单。<br>
              例：如果你希望text不是用input来，而是用select让用户选择，那么就应该自己进行定义。</p>
            </td>
        </tr>
      </table> 
    </td>
</tr>
</table>
</body>
</html>