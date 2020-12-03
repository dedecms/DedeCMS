<?
require_once(dirname(__FILE__)."/config.php");
$dsql = new DedeSql(false);
$dsql->SetQuery("Select nid,typename From #@__channeltype");
$dsql->Execute();
$nids = "";
while($row = $dsql->GetObject())
{
  $nids .= "({$row->typename}=&gt;{$row->nid}) ";
}
$dsql->Close();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>网站模板标记说明</title>
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
<td height="19" background="img/tbg.gif">&nbsp;<b>织梦内容管理系统模板代码参考</b></td>
</tr>
<tr>
<td height="94" bgcolor="#FFFFFF" valign="top">
<table width="98%" border="0" cellspacing="2">
<tr>
<td height="30" colspan="3" align="right"><input type="button" name="Submit" value=" 查看模板目录 " onClick="location='file_manage_main.php?activepath=<?=$cfg_templets_dir?>';"></td>
</tr>
<tr> 
<td height="30" colspan="3">
  <a href="#1"><u>解析引擎概述</u></a>
　<a href="#2"><u>模板设计规范</u></a>
　<a href="#3"><u>代码参考</u></a><br/>
            标记参考： <a href="#31"><u>arclist(artlist,likeart,hotart,imglist,imginfolist,coolart,specart)</u></a> 
            &nbsp;<a href="#32"><u>field</u></a> &nbsp;<a href="#33"><u>channel</u></a> 
            &nbsp;<a href="#34"><u>mytag</u></a> &nbsp;<a href="#35"><u>vote</u></a> 
            &nbsp;<a href="#36"><u>friendlink</u></a> &nbsp;<a href="#37"><u>mynews</u></a> 
            &nbsp;<a href="#38"><u>loop</u></a> &nbsp;<a href="#39"><u>channelartlist</u></a> 
            &nbsp;<a href="#310"><u>page</u></a> &nbsp;<a href="#311"><u>list</u></a> 
            &nbsp;<a href="#312"><u>pagelist</u></a> <a href="#313"><u>pagebreak</u></a> 
            <a href="#314"><u>fieldlist</u></a> </td>
</tr>
<tr> 
<td colspan="3" valign="top"> <hr size="1" style="color:#888888">
<strong><font color="#990000"> 一、织梦模板解析引擎概述</font></strong> <a name="1"></a> 
<p> 在了解DedeCms的模板代码之前，了解一下织梦模板引擎的知识是非常有意义的。织梦模板引擎是一种使用XML名字空间形式的模板解析器，使用织梦解析器解析模板的最大好处是可以轻松的制定标记的属性，感觉上就像在用HTML一样，使模板代码十分直观灵活，新版的织梦模板引擎不单能实现模板的解析还能分析模板里错误的标记。</p>
<p>1、织梦模板引擎的代码样式有如下几种形式：<br/>
{dede:标记名称 属性='值'/}<br/>
{dede:标记名称 属性='值'}{/dede:标记名称}<br/>
{dede:标记名称 属性='值'}自定义样式模板(InnerText){/dede:标记名称}</p>
<p>提示：<br/>
对于{dede:标记名称 属性='值'}{/dede:标记名称}这种形式的标记，在2.1版中，表示结束只需要用“{/dede}”，但<br/>
V3中需要严格用“{/dede:标记名称}”，否则会报错。</p>
<p>2、织梦模板引擎内置有多个系统标记，这些系统标记在任何场合都是能直接使用的。</p>
<p>(1) global 标记，表示获取一个外部变量，除了数据库密码之外，能调用系统的任何配置参数，形式为：<br/>
{dede:global name='变量名称'}{/dede:global}<br/>
或<br/>
{dede:global name='变量名称' /}</p>
<p>其中变量名称不能加 $ 符号，如变量 $cfg_cmspath ，应该写成 {dede:global name='cfg_cmspath' 
/} 。</p>
<p>(2) foreach 用来输出一个数组，形式为：<br/>
{dede:foreach array='数组名称'}[field:key/] [field:value/]{/dede:foreach}</p>
<p>(3) include 引入一个文件，形式为：<br/>
{dede:include file='文件名称' /}<br/>
对文件的搜索路径为顺序为：绝对路径、include文件夹，CMS安装目录，CMS主模板目录</p>
            <p>3、织梦标记允许在任何标记中使用函数对得到的值进行处理，形式为：<br/>
              {dede:标记名称 属性='值' function='youfunction(&quot;参数一&quot;,&quot;参数二&quot;,&quot;@me&quot;)'/}<br/>
              其中 @me 用于表示当前标记的值，其它参数由你的函数决定是否存在，例如：<br/>
              {dede:field name='pubdate' function='strftime(&quot;%Y-%m-%d %H:%M:%S&quot;,&quot;@me&quot;)' 
              /}<br>
              <br>
              4、织梦标记允许有限的编程扩展。<br>
              格式为：<br>
              {dede:tagname runphp='yes'}<br>
              $aaa = @me;<br>
              @me = &quot;123456&quot;;<br>
              {/dede:tagname} <br>
              @me 表示这个标记本身的值，因此标记内编程是不能使用<strong><font color="#990000">echo</font></strong>之类的语句的，只能把所有返回值传递给@me。<br>
              此外由于程序代码占用了底层模板InnerText的内容，因此需编程的标记只能使用默认的InnerText。 </p>
<p><font color="#990000"><strong>二、DedeCms 模板制作规范<a name="2"></a></strong></font></p>
            <p>　　DedeCms系统的模板是非固定的，用户可以在新建栏目时可以自行选择栏目模板，官方仅提供最基本的默认模板，即是内置系统模型的各个模板，DedeCms支持自定义频道模型，用户自定义新频道模型后，需要按该模型设计一套新的模板。<br>
              <strong>一、概念，设计和使用模板，必须要理解下面几个概念：</strong><br>
              <font color="#330000">1、板块（封面）模板：</font><br>
              　　指网站主页或比较重要的栏目封面使用的模板，一般用“index_识别ID.htm”命名，此外，用户单独定义的单个页面或自定义标记，也可选是否支持板块模板标记，如果支持，系统会用板块模板标记引擎去解析后才输出内容或生成特定的文件。<br>
              <font color="#330000">2、列表模板：</font><br>
              　　指网站某个栏目的所有文章列表的模板，一般用 “list_识别ID.htm” 命名。<br>
              <font color="#330000">3、档案模板：</font><br>
              　　表示文档查看页的模板，一般用 “article_识别ID.htm” 命名。<br>
              <font color="#330000">4、其它模板：</font><br>
              　　一般系统常规包含的模板有：主页模板、搜索模板、ＲＳＳ、ＪＳ编译功能模板等，此外用户也可以自定义一个模板创建为任意文件。<br>
              <strong>二、 命名，为了规范起见，织梦官方建议使用统一的方式来命名模板，具体如下：</strong><br>
              <font color="#330000">1、模板保存位置：</font><br>
              　　模板目录：｛cmspath/templets/样式名称（英文，默认为default，其中system为系统底层模板，plus为插件使用的模板）/具体功能模板文件｝<br>
              　　<font color="#CC0000">你的模板位置：“ 
              <?=$cfg_templets_dir."/{风格名称}/功能模板文件"?>
              ”，</font><a href="catalog_do.php?dopost=viewTemplet"><u><font color="#6600FF">点击此浏览模板目录</font></u></a><br>
              <font color="#330000">2、 模板文件命名规范：</font><br>
              （１）index_<font color="#990000">识别ID</font>.htm：　表示板块（栏目封面）模板；<br>
              （２）list_<font color="#990000">识别ID</font>.htm：　表示栏目列表模板；<br>
              （３）article_<font color="#990000">识别ID</font>.htm：　表示内容查看页（文档模板，包括专题查看页）；<br>
              （４）search.htm： 搜索结果列表模板； <br>
              （５）index.htm： 主页模板； <br>
              <font color="#990000"><strong>注解：</strong></font><font color="#990000"><br>
              你的系统各个内容频道的[识别ID]分别为： 
              <?=$nids?>
              </font><br>
              例：list_image.htm 表示是就是内容类型为图片集的栏目默认列表模板。</p>
<p><font color="#990000"><strong>三、主要标记参考<a name="3"></a></strong></font></p>
<p><strong>1、arclist 标记</strong><a name="31"></a></p>
<p>这个标记是DedeCms最常用的一个标记，其中 hotart、coolart、likeart、artlist、imglist、imginfolist、specart 
这些标记都是由这个标记所定义的不同属性延伸出来的。</p>
<p>作用：获取一个指定的文档列表</p>
<p>适用范围：封面模板、列表模板、文档模板</p>
<p>(1)基本语法：</p>
<p>{dede:arclist<br/>
typeid='' row='' col='' titlelen='' <br/>
infolen='' imgwidth='' imgheight='' listtype='' orderby='' keyword=''}</p>
<p>自定义样式模板(InnerText)</p>
<p>{/dede:arclist}</p>
<p>本标记等同于artlist、imglist、imginfolist标记，其中与artlist是完全等同的，与imglist、imginfolist仅是默认的底层模板不同。</p>
<p><br/>
(2)属性参考：</p>
            <p>[1] typeid='' 表示栏目ID，在列表模板和档案模板中一般不需要指定，在封面模板中允许用&quot;,&quot;分开表示多个栏目；<br/>
              [2] row='' 表示返回文档行数，如果和col联合使用，刚结果数等于row * col；<br/>
              [3] col='' 表示分多少列显示（默认为单列）；<br/>
              [4] titlelen='' 表示标题长度；<br/>
              [5] infolen='' 表示内容简介长度；<br/>
              [6] imgwidth='' 表示缩略图宽度；<br/>
              [7] imgheight='' 表示缩略图高度；<br/>
              [8] type='' 表示档案类型，其中默认值或type='all'时为普通文档<br>
              § type='commend'时，表示推荐文档，等同于<br>
              § type='image'时，表示必须含有缩略图片的文档<br>
              [9] orderby='' 表示排序方式，默认值是 senddate 按发布时间排列。 <br>
              § orderby='hot' 或 orderby='click' 表示按点击数排列<br>
              § orderby='pubdate' 按出版时间排列（即是前台允许更改的时间值）<br>
              § orderby='sortrank' 按文章的新排序级别排序（如果你想使用置顶文章则使用这个属性）<br>
              § orderby='id' 按文章ID排序<br>
              [10] keyword='' 表示含有指定关键字的文档列表，多个关键字用&quot;,&quot;分开<br>
              [11] channelid='数字' 表示特定的频道类型，内置的频道：专题(-1)、文章(1)、图集(2)、Flash(4)、软件(3)<br>
              [12] limit='起始,结束' 表示限定的记录范围，row属性必须等于&quot;结束 - 起始&quot;，mysql的limit语句是由0起始的，如 
              “limit 0,5”表示的是取前五笔记录，“limit 5,5”表示由第五笔记录起，取下五笔记录。</p>
<p>(3)底层模板变量</p>
<p>ID(同 id),title,iscommend,color,typeid,ismake,description(同 info),<br/>
pubdate,senddate,arcrank,click,litpic(同 picname),typedir,typename,<br/>
arcurl(同 filename),typeurl,stime(pubdate 的&quot;0000-00-00&quot;格式),<br/>
textlink,typelink,imglink,image</p>
<p>其中：<br/>
textlink = &lt;a href='arcurl'&gt;title&lt;/a&gt;<br/>
typelink = &lt;a href='typeurl'&gt;typename&lt;/a&gt;<br/>
imglink = &lt;a href='arcurl'&gt;&lt;img src='picname' border='0' 
width='imgwidth' height='imgheight'&gt;&lt;/a&gt;<br/>
image = &lt;img src='picname' border='0' width='imgwidth' height='imgheight'&gt;</p>
<p>变量调用方法：[field:varname /]</p>
<p>如：<br/>
{dede:arclist infolen='100'}<br/>
[field:textlink /]<br/>
&lt;br&gt;<br/>
[field:info /]<br/>
&lt;br&gt;<br/>
{/dede:arclist}</p>
<p><strong>2、field 标记</strong><a name="32"></a></p>
<p>这个标记用于获取特定栏目或档桉的字段值及常用的环境变量值</p>
<p>适用范围：封面模板、列表模板、文档模板</p>
<p>(1)基本语法</p>
<p>{dede:field name=''/}</p>
<p>(2) name 属性的值：</p>
<p>板块模板：phpurl,indexurl,indexname,templeturl,memberurl,powerby,webname,specurl</p>
<p>列表模板：position,title,phpurl,templeturl,memberurl,powerby,indexurl,indexname,specurl,栏目表dede_arctype的所有字段<br/>
其中 position 为 “栏目一 &gt; 栏目二” 这样形式的链接，title则为这种形式的标题</p>
<p>文档模板：position,phpurl,templeturl,memberurl,powerby,indexurl,indexname,specurl,id(同 
ID,aid),档案dede_archives表和附加表的所有字段。</p>
<p><br/>
<strong>3、channel 标记</strong><a name="33"></a></p>
<p>用于获取栏目列表</p>
<p>适用范围：封面模板、列表模板、文档模板</p>
<p>(1)基本语法<br/>
{dede:channel row='' type=''}<br/>
自定义样式模板(InnerText)<br/>
{/dede:channel}</p>
<p>(2)属性</p>
            <p>[1] row='数字' 表示获取记录的条数（通用在某级栏目太多的时候使用，默认是 8）</p>
<p>[2] type = top,sun,self</p>
<p>type='top' 表示顶级栏目<br/>
type='sun' 表示下级栏目<br/>
type='self' 表示同级栏目</p>
<p>其中后两个属性必须在列表模板中使用。</p>
<p>(3)底层模板变量</p>
<p>ID,typename,typedir,typelink(这里仅表示栏目的网址)</p>
<p>例：<br/>
{dede:channel type='top'}<br/>
&lt;a href='[field:typelink /]'&gt;[field:typename/]&lt;/a&gt; <br/>
{/dede:channel}</p>
<p><strong>4、mytag 标记</strong><a name="34"></a></p>
<p>用于获取自定义标记的内容</p>
<p>适用范围：封面模板、列表模板、文档模板</p>
<p>(1)基本语法</p>
<p>{dede:mytag typeid='' name='' ismake='' /}</p>
<p>(2)属性</p>
<p>[1] typeid = '数字' 表示栏目ID，默认为 0，在没有设定的栏目没有定义这个名称的标记，会按如下搜索方式来搜索“先向上查找父栏目 
-&gt; 通用标记（typeid=0）的同名标记”。</p>
<p>[2] name = '' 标记名称。</p>
<p>[3] ismake = yes|no 默认为 no 表示mytag里的内容不包含其它封面模板的标记，yes则表示标记内容含有其它封面模板标记。</p>
<p><strong>5、vote 标记<a name="35"></a></strong></p>
<p>用于获取一组投票表单</p>
<p>适用范围：封面模板</p>
<p>(1) 基本语法<br/>
{dede:vote id='投票ID' lineheight='22'<br/>
tablewidth='100%' titlebgcolor='#EDEDE2'<br/>
titlebackground='' tablebgcolor='#FFFFFF'}<br/>
{/dede:vote}</p>
<p><br/>
<strong>6、friendlink 标记，等同 flink<a name="36"></a></strong></p>
<p>用于获取友情链接</p>
<p>适用范围：封面模板</p>
<p>(1)基本语法</p>
            <p>{dede:flink type='' row='' col='' titlelen='' tablestyle=''}{/dede:flink}<br>
              属性注解：<br>
              [1]type：链接类型，值：<br>
              a. textall 全部用文字显示<br>
              b. textimage 文字和图得混合排列<br>
              c. text 仅显示不带Logo的链接<br>
              d. image 仅显示带Logo的链接<br>
              -------------------------------------<br>
              [2]row：显示多少行，默认为4行<br>
              [3]col：显示多少列，默认为6列<br>
              [4]titlelen：站点文字的长度<br>
              [5]tablestyle： 表示 &lt;table <font color="#990000">这里的内容</font>&gt;</p>
<p><strong>7、mynews 标记<a name="37"></a></strong></p>
<p>用于获取站内新闻</p>
<p>适用范围：封面模板</p>
<p>(1) 基本语法</p>
<p>{dede:mynews row='条数' titlelen='标题长度'}Innertext{/dede:mynews}</p>
<p>Innertext支持的字段为：[field:title /],[field:writer /],[field:senddate 
/](时间),[field:body /]</p>
<p><strong>8、loop 标记<a name="38"></a></strong></p>
            <p>用于调用任意表的数据，一般用于调用论坛贴子之类的操作，请参阅<a href="bbs_addons.php"><font color="#990000"><u>论坛扩展插件</u></font></a>。</p>
<p><strong>9、channelartlist 标记<a name="39"></a></strong></p>
<p>用于获取频道的下级栏目的内容列表</p>
<p>适用范围：封面模板</p>
<p>语法：</p>
<p>{dede:channelArtlist typeid=0 col=2 tablewidth='100%'}<br/>
&lt;table width=&quot;99%&quot; border=&quot;0&quot; cellpadding=&quot;3&quot; 
cellspacing=&quot;1&quot; bgcolor=&quot;#BFCFA9&quot;&gt;<br/>
&lt;tr&gt;<br/>
&lt;td bgcolor=&quot;#E6F2CC&quot;&gt;<br/>
{dede:type}<br/>
&lt;table border=&quot;0&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; 
width=&quot;98%&quot;&gt;<br/>
&lt;tr&gt;<br/>
&lt;td width='10%' align=&quot;center&quot;&gt;&lt;img src='[field:global 
name='cfg_plus_dir'/]/img/channellist.gif' width='14' height='16'&gt;&lt;/td&gt;<br/>
&lt;td width='60%'&gt;<br/>
&lt;a href=&quot;[field:typelink /]&quot;&gt;[field:typename /]&lt;/a&gt;<br/>
&lt;/td&gt;<br/>
&lt;td width='30%' align='right'&gt;<br/>
&lt;a href=&quot;[field:typelink /]&quot;&gt;更多...&lt;/a&gt;<br/>
&lt;/td&gt;<br/>
&lt;/tr&gt;<br/>
&lt;/table&gt;<br/>
{/dede:type}<br/>
&lt;/td&gt;<br/>
&lt;/tr&gt;<br/>
&lt;tr&gt;<br/>
&lt;td height=&quot;150&quot; valign=&quot;top&quot; bgcolor=&quot;#FFFFFF&quot;&gt;<br/>
{dede:arclist row=&quot;8&quot;}<br/>
·&lt;a href=&quot;[field:arcurl /]&quot;&gt;[field:title /]&lt;/a&gt;&lt;br&gt;<br/>
{/dede:arclist}<br/>
&lt;/td&gt;<br/>
&lt;/tr&gt;<br/>
&lt;/table&gt;<br/>
&lt;div style='font-size:2px'&gt;&amp;nbsp;&lt;/div&gt;<br/>
{/dede:channelArtlist}</p>
<p>channelArtlist 是唯一一个可以直接嵌套其它标记的标记，不过仅限于嵌套</p>
<p>{dede:type}{/dede:type} 和 {dede:arclist}{/dede:arclist}</p>
<p>标记。</p>
<p>(1) 属性<br/>
typeid=0 频道ID,默认的情况下，嵌套的标记使用的是这个栏目ID的下级栏目，如果你想用特定的栏目，可以用&quot;,&quot;分开多个ID。</p>
<p>col=2 分多列显示</p>
<p>tablewidth='100%' 外围表格的大小</p>
<p><br/>
<strong>10、page 标记<a name="310"></a></strong></p>
<p>表示分页页面的附加参数</p>
<p>适用范围：列表模板</p>
<p>语法：</p>
<p>{dede:page pagesize=&quot;每页结果条数&quot;/}</p>
<p><strong>11、list 标记<a name="311"></a></strong></p>
<p>表示列表模板里的内容列表</p>
<p>语法：</p>
            <p>{dede:list col='' titlelen='' <br/>
              infolen='' imgwidth='' imgheight='' orderby=''}{/dede:list}</p>
<p>底层模板变量</p>
<p>ID(同 id),title,iscommend,color,typeid,ismake,description(同 info),<br/>
pubdate,senddate,arcrank,click,litpic(同 picname),typedir,typename,<br/>
arcurl(同 filename),typeurl,stime(pubdate 的&quot;0000-00-00&quot;格式),<br/>
textlink,typelink,imglink,image</p>
<p><strong>12、pagelist 标记<a name="312"></a></strong></p>
<p>表示分页页码列表</p>
<p>适用范围：列表模板</p>
<p>语法：</p>
<p>{dede:pagelist listsize=&quot;3&quot;/}</p>
            <p>listsize 表示 [1][2][3] 这些项的长度 x 2 </p>
            <p><strong>13、pagebreak 标记</strong><strong><a name="313" id="313"></a></strong><br>
              <br>
              用途：表示文档的分页链接列表。<br>
              适用范围：仅文档模板。 <br>
              语法：{dede:pagebreak /} <br>
              <br>
              <strong>14、 fieldlist 标记<a name="314" id="314"></a><br>
              </strong>用途：获得附加表的所有字段信息。<br>
              适用范围：仅文档模板。 <br>
              语法：<br>
              {dede:fieldlist}<br>
              [field:name /] ： [field:value /] &lt;br&gt;<br>
              {/dede:fieldlist}</p>
            <p></p></td>
</tr>
<tr> 
<td colspan="3">&nbsp;</td>
</tr>
</table> </td>
</tr>
</table>
</body>
</html>