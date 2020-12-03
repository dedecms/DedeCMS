<?
require("config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>网站模板</title>
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
          <td height="30" colspan="3" align="right"> 
            <input type="button" name="Submit" value=" 查看模板目录 " onClick="window.open('file_view.php?activepath=<?=$mod_dir?>');">          </td>
        </tr>
        <tr> 
          <td colspan="3"><hr size="1" style="color:#888888">
            <b>织梦内容管理系统模板代码V2.0版风格说明：</b><br>            
            1、织梦内容管理系统V2.0以上版本的模板风格采用XML名字空间的风格，使用双重模板技术，这种先进的页面与代码分离思想，在同类软件中也是十分超前的。<br>
            2、由于考虑到模板的代码可见性，系统用 &quot;{&quot;、&quot;}&quot; 符号来括起模板标记，如果你不喜欢这种风格，可以在config_base.php中更改为其它符号，如：&quot;&lt;&quot;、&quot;&gt;&quot; 或 &quot;[&quot;、&quot;]&quot;(注意：如果你更改了符号为&quot;[&quot;、&quot;]&quot;，可能会导致表格的 loop 标记不可用)。<br>
            3、Dede模板的代码与XML的名字空间形式的语法是相同的，但不允许嵌套(仅在loop标记内允许嵌套，但使用的是不同的形式)。<br> 
            一般格式为： <br>
{dede:tagname attribute=&quot;value&quot;/}<br>
            {dede:tagname attribute=&quot;value&quot;}{/dede}<br>
{dede:tagname attribute=&quot;value&quot;}innertext{/dede}            <br>
dede就是表示本系统的名字空间，这样的代码可以方便的将HTML标记或CSS标记区分开来。<br>
<span class="style2"><b>tagname 和 属性 或 属性值 是不分大小写的。</b></span><br>
4、2.0以上的版大部份模板标记支持二重模块技术，它的默认模板放在“模板目录/低层模板”中，如果你要自定义这些低层模板，一般不要更改“模板目录/低层模板”里的文件，只要直接把模板字符串放在innertext的位置即可，低层的模板直接用 ~名称~ 来映射同名的变量。<br>
例：<br>
{dede:list type=&quot;small&quot;}<br>
&lt;table border='0' width='100%'&gt;<br>
&lt;tr height='24'&gt;<br>
&lt;td width='2%'&gt;&lt;img src='/dedeimages/file.gif' width='18' height='17'&gt;&lt;/td&gt;<br>
&lt;td width='83%'&gt;~fulltitle~&lt;font color='#8F8C89'&gt;(~stime~)&lt;/font&gt;&lt;/td&gt;<br>
&lt;td width='15%'&gt;点击:~click~&lt;/td&gt;&lt;/tr&gt;<br>
&lt;tr&gt;&lt;td height='2' colspan='3' background='/dedeimages/writerbg.gif'&gt;&lt;/td&gt;&lt;/tr&gt;<br>
&lt;/table&gt;<br>
{/dede}<br>
            5、本内容管理系统的解析器，分为<span class="style2">文章模板解析器</span>、<span class="style2">列表模板解析器</span>、<span class="style2">板块模板解析器</span>和<span class="style2">专题模板解析器</span>，考虑性能原因，这些解析器是分离的，因此你不能在文章模板中使用列表模板和板块模板的标记，反之也是相同的道理，虽然有些标记名称相同，但在不同的模板里可能得到的会是不同的内容。<br>
            <span style="font-size:10pt; font-weight: bold;">请参考指定的模板标记：[<a href="#art"><u>文章模板标记</u></a>] 
            [<a href="#list"><u>列表模板标记</u></a>] [<a href="#part"><u>板块模板标记</u></a>]</span> 
            <strong>[<a href="#spec"><u>专题模板解析器</u></a>]</strong> <br>
            <hr size="1">
            <img src="img/arttag.gif" width="184" height="38"><a name="art"></a><br>
              <span class="style6">※文章模板※</strong></u>可选的插入代码为：</span></font> <br>
              1、<span class="style4">{dede:field name=&quot;value&quot;/}</span> 获得一个指定的字段。<br>
              value可以为下值：title、stime、source、body、click、writer、id 、position(文章出处)<br>
              是否支持二重模板：不支持<br>
              2、<span class="style5">{dede:likeart titlelength=&quot;24&quot; line=&quot;10&quot;}{/dede}</span><br>
            用途：获得文章相关文章列表<br>
            属性：titlelength 标题长度  line 结果行数 支持innertext  <br>
            默认属性为：<br>
            {dede:likeart titlelength=&quot;24&quot; line=&quot;6&quot;}·&lt;a href='~filename~'&gt;~title~&lt;/a&gt;&lt;br&gt;{/dede}            <br>
            coolart、hotart同 如果你使用默认属性，标记可简化为：{dede:likeart/}<br>
            是否支持二重模板：支持<br>
            InnerText支持的字段：
            filename、title、stime、ID<br>
            3、<span class="style5">{dede:coolart titlelength=&quot;24&quot; line=&quot;6&quot;}{/dede}</span><br>
用途：获得与文章同类的文章的推荐的文章列表            <br>
属性：titlelength 标题长度 line 结果行数 支持innertext<br>
是否支持二重模板：支持<br>
InnerText支持的字段： filename、title、stime、ID<br>
4、<span class="style5">{dede:hotart titlelength=&quot;24&quot; line=&quot;6&quot;}{/dede}</span> <br>
用途：获得与文章同类的文章的热点的文章列表<br>
属性：titlelength 标题长度 line 结果行数 支持innertext<br>
是否支持二重模板：支持<br>
            InnerText支持的字段： filename、title、stime、ID            
            <br>
            5、<span class="style5">{dede:channel type=&quot;&quot;}{/dede}</span><br>
用途：获得相关类目。<br>
属性：type 类型，值枚举为： sun 下级分类，top 顶级频道列表，self 同级分类<br>
支持innertext<br>
InnerText支持的字段： typelink、typename            <hr size="1">
            <font color="#FF0000"><u><strong><img src="img/listtag.gif" width="184" height="38"><a name="list"></a><br>
            ※分类列表※</strong></u><strong>可选插入代码：<br>
            </strong></font>列表模板必须定义{dede:page pagesize=&quot;页面大小&quot;/}标记，如果没有定义，将按每页20条记录分页，如果你希望把某类目列表首页当一个板块处理，请在“频道管理-&gt;板块模板”选择类目为自定义板块，并把板块模板代码保存到数据库。<br> 
            1、<span class="style5">{dede:page pagesize=&quot;20&quot;/}</span><br>
            用途：定义页面的大小（如果列表为多列图片展览，这个标记将无效）<br>
            2、<span class="style5">{dede:field name=&quot;value&quot;/}</span><br>
            用途：获得一个单一意义的字段。<br>
            属性：name 字段名称，值为： title、position<br>
            3、<span class="style5">{dede:coolart titlelength=&quot;24&quot; line=&quot;10&quot;}{/dede}</span><br>
用途：获得这个类别的推荐的文章列表 <br>
属性：titlelength 标题长度 line 结果行数 支持innertext<br>
是否支持二重模板：支持<br>
InnerText支持的字段： filename、title、stime、ID<br>
4、<span class="style5">{dede:hotart titlelength=&quot;24&quot; line=&quot;10&quot;}{/dede}</span> <br>
用途：获得这个类别的热点的文章列表<br>
属性：titlelength 标题长度 line 结果行数 支持innertext<br>
是否支持二重模板：支持<br>
InnerText支持的字段： filename、title、stime、ID<br>
            5、<span class="style5">{dede:channel type=&quot;&quot;}{/dede}</span><br>
            用途：获得相关类目。<br>
            属性：type 类型，值枚举为： sun 下级分类，top 顶级频道列表，self 同级分类<br>            
            支持innertext<br>
            InnerText支持的字段： typelink、typename<br>
            6、<span class="style5">{dede:list type=&quot;&quot;}{/dede}</span>            <br>
            表示列表内容的类型<br>
            属性：type 列表类型，type的值枚举为：full、small、imglist、multiimglist、soft、pagelist<br>
            type属性是没有默认值的，必须指定列表类型<br>
            [1]当type为：full、small 时<br> 
            type=full 表示含标题、简介等信息的文章列表           <br>
            type=small 表示只含标题的文章列表<br>
            支持属性：<br>
            titleLength 标题长度，
            默认为50<br>
            infolength             内容简介长度，默认为：300<br>
            支持的Innertext二级模板值--fulltitle、title、filename、click、member、shortinfo、stime            <br> 
            [2]当type为：imglist、soft 时<br>
            带图片的普通文章列表            <br>
            支持属性：<br>
            titleLength 标题长度，默认为50<br>
            infolength 内容简介长度，默认为：300<br>
            imgwidth 宿略图宽度<br>
            imgheight 宿略图高度<br>            
            支持的Innertext二级模板值--fulltitle、title、filename、click、member、shortinfo、stime<br>
            [3]当type为：            multiimglist 时<br>
            多列图片形式的文章列表            <br>
            支持属性：<br>
titleLength 标题长度，默认为50，<br>
infolength 内容简介长度，默认为：300，<br>
imgwidth 宿略图宽度<br>
imgheight 宿略图高度<br>
row 图片行数<br>
col 图片列数<br>
hastitle 是否显示标题链接，yes 或 no<br>
{dede:page pagesize=&quot;页面大小&quot;/} 页面大小应该等于 row * col 的值，否则可能出错。 <br>            
支持的Innertext二级模板值--ID、title、filename、stime、click、img<br>
[5]当type为：pagelist
表示分页列表，可以用size属性定义列表长度，实际长度为size*2+1，如定义列表为<br>
{dede:page type=&quot;pagelist&quot; size=&quot;3&quot;/}列表的样式为：<br>
            <span class="style7">上一页 [1][2][3][4][5][6][7] 下一页</span><br>
            7、<span class="style5">{dede:rss/}</span><br>
            用途：获得类目的Rss链接，这里只返回一个链接网址，但并不返回超链接，你必须在模板文件中用&nbsp;<br>
            &lt;a href=&quot;<span class="style5">{dede:rss/}</span>&quot;&gt;RSS&lt;/a&gt;这样来实现超链接。<br>
            不支持InnerText<br>
            <hr size="1">            <p><img src="img/parttag.gif" width="184" height="38"><a name="part"></a><br>
              <font color="#FF0000"><u><strong>※板块模板※</strong></u><strong>可选插入代码：<br>
              </strong></font>模板代码是最灵活的可订制代码，一般用于组织网站主页或大栏目或频道的首页，由于功能相对复杂，一般有较多的属性值，并且所有标记允许不使用任何属性，系统会分配一个默认值，以下均列出它们的默认值，实际应用中，这些都是可选的，如果要使用这些板块模板代码，必须将在&quot;<a href="web_type_web.php"><u>板块模板管理</u></a>&quot;使用这些模板，这个版本，在获取文章链接等方面，比上一个版本减少了hotlist、commendlist功能标记，但实际上只要在imglist和artlist的sort属性中设参数为 
              hot 或 commend 即可轻松实现这样的功能，如果你参适当使用二重模板，远远比以往版本灵活。<br>
              1、<font color="#0000FF">{dede:imglist typeid=0 row=1 col=4 imgwidth=100 
              imgheight=100 tablewidth=&quot;100%&quot; sort=&quot;new&quot; titlelength=20}{/dede}</font><br>
              用途：显示一个图片列表<br>
              属性及意义：<br>
              [1]typeid 类别ID，为零或没有这个属性时表示不限类别<br>
              [2]row 图片行数 col 图片列数<br>
              [3]imgwidth 图片宽度 imgheight 图片高度<br>
              [4]sort 排序方式 默认为 new ，把最新发布的排在前面，可选项为： hot 把点击数高的排列在前面 commend 最新的推荐文章<br>
              [5]titlelength 标题文字的长度（中文*2）<br>
              [6]infolength 文章简介的长度 <br>
              [7]tablewidth 容器表格的大小，默认为 100%，可以选择用相对或绝对大小<br>
              是否支持二重模板：支持<br>
              默认的InnerText：<br>
              <font color="#CC00FF">&lt;table width='98%' border='0' cellspacing='2' 
              cellpadding='0'&gt;<br>
&lt;tr&gt;<br>
&lt;td align='center'&gt;~imglink~&lt;/td&gt;<br>
&lt;/tr&gt;<br>
&lt;tr&gt;<br>
&lt;td align='center'&gt;~textlink~&lt;/td&gt;<br>
&lt;/tr&gt;<br>
&lt;/table&gt;</font><br>
              支持的二重模板字段：ID、title、filename、img、imglink(带链接的图片)、textlink,info(文章简介)<br>
              2、<font color="#3300FF">{dede:artlist typeid=0 row=6 sort=&quot;new&quot; titlelength=10}{/dede}<br>
              </font>用途：显示一个文章列表<br>
              属性及意义：<br>
              [1]typeid 类别ID，为零或没有这个属性时表示不限类别<br>
              [2]row 文章的行数 与line属性等同<br>
              [3]line 文章的行数 与row属性等同<br>
              [4]sort 排序方式 默认为 new ，把最新发布的排在前面，可选项为： hot 把点击数高的排列在前面 commend 最新的推荐文章<br>
              [5]titlelength 标题文字的长度（中文*2）<br>
              是否支持二重模板：支持<br>
              默认的InnerText：<font color="#3300FF"> <br>
              <font color="#CC00FF">·&lt;a href=&quot;~filename~&quot;&gt;~title~&lt;/a&gt;&lt;br&gt;</font> 
              </font><br>
              支持的二重模板字段：ID、title、filename、stime、click、typelink(文章所属类目链接)、<br>
              textlink(文章标题链接，即&lt;a href='~filename~'&gt;~title~&lt;/a&gt;)<br>
              </font> 
              3、<font color="#3300FF">{dede:imginfolist typeid=0 row=3 col=1 infolength=30 
              imgwidth=60 imgheight=60 sort=hot titlelength=10 tablewidth='200'}{/dede}<br>
              </font> 用途：返回一列带简介的图文信息<br>
              这个标记与{dede:imglist/}是同一解析函数的，主要默认的低层模板不同，如果你调整适当的属性和低层模板，用{dede:imglist}innertext{/dede}也能达到同样的目的。<br>
              属性及意义：<br>
              [1]typeid 类别ID，为零或没有这个属性时表示不限类别<br>
              [2]row 图文信息的行数<br>
              [3]col 图文信息的列数<br>
              [4]imgwidth 图片宽度 imgheight 图片高度<br>
              [5]sort 排序方式 默认为 new ，把最新发布的排在前面，可选项为： hot 把点击数高的排列在前面 commend 最新的推荐文章<br>
              [6]titlelength 标题文字的长度（中文*2） <br>
              [7]infolength 文章简介的长度<br>
              [8]tablewidth 容器表格的大小，默认为 100%，可以选择用相对或绝对大小<br>
              是否支持二重模板：支持<br>
              默认的InnerText：<br>
              <font color="#CC00FF">&lt;table width=&quot;100%&quot; border=&quot;0&quot; 
              cellspacing=&quot;2&quot; cellpadding=&quot;2&quot;&gt;<br>
&lt;tr&gt; <br>
&lt;td width=&quot;30%&quot; rowspan=&quot;2&quot; align=&quot;center&quot;&gt;~imglink~&lt;/td&gt;<br>
&lt;td width=&quot;70%&quot;&gt;&lt;a href='~filename~'&gt;~title~&lt;/a&gt;&lt;/td&gt;<br>
&lt;/tr&gt;<br>
&lt;tr&gt; <br>
&lt;td&gt;~info~&lt;/td&gt;<br>
&lt;/tr&gt;<br>
&lt;/table&gt;</font> <br>
              支持的二重模板字段：ID、title、filename、img、imglink(带链接的图片)、textlink,info(文章简介)<br>
              4、<font color="#3300FF">{dede:vote name=&quot;&quot;}{/dede}</font><br>
            用途：返回一个投票用的表单，name是创建投票时所用的名字<br>
            属性：<br>
            name: 投票的名称（必须）<br>
            lineheight: 投票项目的行高<br>
            tablewidth: 投票容器表格的大小<br>
            titlebgcolor: 投票标题的背景颜色<br>
            titlebackground: 投票标题的背景图片<br>
            5、<font color="#3300FF">{dede:link row=3 col=6 type=&quot;text&quot; 
            titlelength=&quot;24&quot;}{/dede}</font> <br>
              用途：返回友情链接的表格<br>
            属性：<br>
            type: 可选为：text和img 其中img为标准的：88*31格式。 <br>
            row: 行数<br>
            col: 列表<br>
            titlelen: 标题长度，默认为 24 即 12中文字<br>
            tablestyle: 表格的HTML属性 <br>
            6、<font color="#3300FF">{dede:channel typeid=0}{/dede}</font> <br>
            用途：获得一个频道链接列表<br>
            属性： <br>
            typeid: 为0时表示顶级频道列表，否则为这个类目的下级分类列表。<br>
            支持：InnerText<br>
            二重模板字段：typelink typename <br>
            7、<font color="#3300FF">{dede:channelArtlist typeid=0 bgcolor='' background=''}{/dede}</font> <br>
            用途：用于获取某类目的下级类目的指定条数的文章列表，它具有 artlist 的所有属性，但是多了以下几个属性：<br>
            col 列数 <br>
            bgcolor 类目标题的背景颜色<br>
            background 类目标题的背景图片<br>
            titleheight 类目标题的行高<br>
            titleimg 类目标题的图片（默认的情况下，这里为§符号，因为程序内并不适合放图片）<br>
            tablewidth 类目列表格的宽度（一般多列时才需设置）<br>
            例：            <br>
            {dede:channelArtlist typeid=0 col=2 row=6 bgcolor=#A09D74 background='/php/modpage/img/2-mbg2.gif' titleheight='22' titleimg='/php/modpage/img/file.gif'


 tablewidth='98%'

/} <br>
            这些附加的属性是用于定义类目的链接样式的<br>
              至于文章列表的样式，其属性和artlist标记完全相同，请参考{dede:artlist/}标记。            <br>
            8、<font color="#3300FF">{dede:webinfo name=''/} <br>
            </font>用途：获得一个系统配置参数。<br>
            目前支持：webname、baseurl、adminemail、powerby<br>
            9、<font color="#3300FF">{dede:mynews row='条数'}{/dede}</font><br>
站内新闻获取，Innertext支持的字段为：title,writer,senddate(时间),msg 。<br>
10、<font color="#3300FF">{dede:field name=''/}</font><br>
获取某类目的信息，仅在类目块板中有效。<br>
11、<font color="#3300FF">{dede:extern name=''/}</font><br>
获得系统变量。 <br>
12、<font color="#3300FF">{dede:loop table=&quot;表名&quot; sort=&quot;排序&quot; row=&quot;条数&quot; if=&quot;条件&quot;}{/dede}<br>
</font>获取一个表格的字段，是一个十分灵活的标记，为将来版本的插件技术接口标记，本版仅作测试，在Innertext中使用的不是波浪线作为字段，而是一个XML标记，格式如下：<br>
&lt;loop:field name='字段名' function='用于处理的函数' parameter='函数参数(用&quot;,&quot;分开)'/&gt;<br> 
例：<br>
{dede:loop table=&quot;dede_art&quot; sort=&quot;click&quot;}<br>
&lt;loop:field name='title' function='substr' parameter='1,20'/&gt;&lt;br&gt;<br>
{/dede}</p>
            <hr size="1">
            <font color="#FF0000"><u><strong><img src="img/spectag.gif" width="184" height="38"><a name="spec" id="spec"></a></strong></u></font> 
            <br>
            <font color="#FF0000"><u><strong>※专题模板※</strong></u><strong>可选插入代码：</strong></font> 
            <br>
            专题的功能和上一版没作什么变动，唯独是代码风格的改变。<br>
            1、<span class="style4">{dede:field name=&quot;value&quot;/}</span> 
            获得一个指定的字段。<br>
            value可以为下值：title、specimg(专题图片及链接)、specmsg(专题简介)、click、id 、position(文章出处)<br>
            是否支持二重模板：不支持<br>
            2、<span class="style5">{dede:speclist titlelength=&quot;24&quot;}{/dede}</span><br>
            专题文章列表<br>
            是否支持二重模板：支持<br>
            InnerText支持的字段： filename、title、stime、ID <br>
            3、<span class="style5">{dede:speclike titlelength=&quot;24&quot;}{/dede}</span><br>
            专题相关文章列表<br>
            是否支持二重模板：支持<br>
            InnerText支持的字段： filename、title、stime、ID <br>
            4、<span class="style5">{dede:coolart titlelength=&quot;24&quot; line=&quot;6&quot;}{/dede}</span><br>
            用途：获得与文章同类的文章的推荐的文章列表 <br>
            属性：titlelength 标题长度 line 结果行数 支持innertext<br>
            是否支持二重模板：支持<br>
            InnerText支持的字段： filename、title、stime、ID<br>
            5、<span class="style5">{dede:hotart titlelength=&quot;24&quot; line=&quot;6&quot;}{/dede}</span> 
            <br>
            用途：获得与文章同类的文章的热点的文章列表<br>
            属性：titlelength 标题长度 line 结果行数 支持innertext<br>
            是否支持二重模板：支持<br>
            InnerText支持的字段： filename、title、stime、ID <br>
            6、<span class="style5">{dede:channel type=&quot;&quot;}{/dede}</span><br>
用途：获得相关类目。<br>
属性：type 类型，值枚举为： sun 下级分类，top 顶级频道列表，self 同级分类<br>
支持innertext<br>
InnerText支持的字段： typelink、typename</td>
        </tr>
        <tr>
          <td colspan="3">&nbsp;</td>
        </tr>
      </table> </td>
</tr>
</table>
</body>
</html>