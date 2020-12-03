<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>模板管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<style>
.coolbg2 {
border: 1px solid #000000;
background-color: #F2F5E9;
height:18px
}
.bline {border-bottom: 1px solid #BCBCBC;background-color:#F0F4F1;}
.tdborder{
border-left: 1px solid #43938B;
border-right: 1px solid #43938B;
border-bottom: 1px solid #43938B;
}
.tdline-left{
border-bottom: 1px solid #656363;
border-left: 1px solid #788C47;
}
.tdline-right{
border-bottom: 1px solid #656363;
border-right: 1px solid #788C47;
}
.tdrl{
border-left: 1px solid #788C47;
border-right: 1px solid #788C47;
}
.top{cursor: hand;}
body {
scrollbar-base-color:#C0D586;
scrollbar-arrow-color:#FFFFFF;
scrollbar-shadow-color:DEEFC6
}
</style>
<script language="javascript">
function showHide(objname)
{
   var obj = document.getElementById(objname);
   if(obj.style.display=="none") obj.style.display = "block";
	 else obj.style.display="none";
}
</script>
</head>
<base target="main">
<body bgcolor="#B5D185" leftmargin="0" topmargin="0" target="main">
<center>
<div style='font-size:2pt'>&nbsp;</div>
<table width='130' border='0' cellspacing='0' cellpadding='2'>
  <tr bgcolor='#FFFFFF'>
  <td width='2%'><img src='img/dedeexplode.gif' width='11' height='11'></td>
      <td background='img/itemcomenu2.gif'> <a href='index_menu.php' target='_self'><u>全部管理项目</u></a> 
      </td>
  </tr>
</table>
<div style='font-size:2pt'>&nbsp;</div>
<table width='130' border='0' cellspacing='0' cellpadding='2'>
  <tr bgcolor='#FFFFFF'>
  <td width='2%'><img src='img/dedeexplode.gif' width='11' height='11'></td>
      <td background='img/itemcomenu2.gif'> <a href='catalog_do.php?dopost=viewTemplet'><u>浏览模板目录</u></a> 
      </td>
  </tr>
</table>
<div style='font-size:2pt'>&nbsp;</div>
<table width='130' border='0' cellspacing='0' cellpadding='2'>
  <tr bgcolor='#FFFFFF'> 
    <td width='2%'><img src='img/dedeexplode.gif' width='11' height='11'></td>
      <td background='img/itemcomenu2.gif'> <a href='help_templet.php'><u>模板标记参考</u></a> 
      </td>
  </tr>
</table>
<div style='font-size:2pt'>&nbsp;</div>
<table width='130' border='0' cellspacing='0' cellpadding='2'>
  <tr bgcolor='#F5F5F5'>
  <td width='2%'><img style='cursor:hand' onClick="showHide('suns10');" src='img/dedeexplode.gif' width='11' height='11'></td>
      <td  background='img/itemcomenu.gif'><a href="help_templet_all.php">通用模板标记</a></td>
  </tr>
  <tr id='suns10'><td colspan='2'>
    <table width='96%' border='0' align="right" cellpadding='0' cellspacing='0'>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_all.php#1">最新文字列表</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_all.php#2">最新图片列表</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_all.php#3">推荐文档列表</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_all.php#4">热门文档列表</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_all.php#5">最新专题列表</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_all.php#6">栏目列表</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_all.php#7">自定义标记</a></td>
          </tr>
          <tr height='24'>
            <td height="20">·<a href="help_templet_all.php#8">系统变量</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_all.php#9">引入一个文件</a></td>
          </tr>
        </table>
	</td></tr>
</table>
<table width='130' border='0' cellspacing='0' cellpadding='2'>
  <tr bgcolor='#F5F5F5'>
  <td width='2%'><img style='cursor:hand' onClick="showHide('suns9');" src='img/dedeexplode.gif' width='11' height='11'></td>
      <td  background='img/itemcomenu.gif'><a href="help_templet_index.php">封面模板标记</a></td>
  </tr>
  <tr id='suns9'><td colspan='2'>
    <table width='96%' border='0' align="right" cellpadding='0' cellspacing='0'>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_index.php#1">获取一组投票</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_index.php#2">获取友情链接</a></td>
          </tr>
          <tr height='24'>
            <td height="20">·<a href="help_templet_index.php#3">获取站点新闻</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_index.php#4">论坛扩展标记</a></td>
          </tr>
        </table>
</td></tr>
</table>
<table width='130' border='0' cellspacing='0' cellpadding='2'>
  <tr bgcolor='#F5F5F5'>
  <td width='2%'><img style='cursor:hand' onClick="showHide('suns3');" src='img/dedeexplode.gif' width='11' height='11'></td>
      <td  background='img/itemcomenu.gif'><a href="help_templet_list.php">列表模板标记</a></td>
  </tr>
  <tr id='suns3'><td colspan='2'>
    <table width='96%' border='0' align="right" cellpadding='0' cellspacing='0'>
        <tr height='24'> 
            <td height="20">·<a href="help_templet_list.php#1">定义分页大小</a></td>
        </tr>
        <tr height='24'>
            <td height="20">·<a href="help_templet_list.php#2">分页内容列表</a></td>
        </tr>
        <tr height='24'> 
            <td height="20">·<a href="help_templet_list.php#3">分页导航标记</a></td>
        </tr>
      </table>
</td></tr>
</table>
<table width='130' border='0' cellspacing='0' cellpadding='2'>
  <tr bgcolor='#F5F5F5'>
  <td width='2%'><img style='cursor:hand' onClick="showHide('suns6');" src='img/dedeexplode.gif' width='11' height='11'></td>
      <td  background='img/itemcomenu.gif'><a href="help_templet_view.php">文档模板标记</a></td>
  </tr>
  <tr id='suns6'><td colspan='2'>
    <table width='96%' border='0' align="right" cellpadding='0' cellspacing='0'>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_view.php#1">文档当前位置</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_view.php#2">文档字段值</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_view.php#3">引入计数器</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_view.php#4">引入最新评论</a></td>
          </tr>
          <tr height='24'>
            <td height="20">·<a href="help_templet_view.php#5">引入推荐好友</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_view.php#6">引入加入收藏</a></td>
          </tr>
        </table>
</td></tr>
</table>
<table width='130' border='0' cellspacing='0' cellpadding='2'>
  <tr bgcolor='#F5F5F5'> 
    <td width='2%'><img style='cursor:hand' onClick="showHide('suns5');" src='img/dedeexplode.gif' width='11' height='11'></td>
      <td  background='img/itemcomenu.gif'><a href="help_templet_other.php">其它模板</a></td>
  </tr>
  <tr id='suns5'>
    <td colspan='2'> <table width='96%' border='0' align="right" cellpadding='0' cellspacing='0'>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_other.php#1">专题列表模板</a></td>
          </tr>
          <tr height='24'>
            <td height="20">·<a href="help_templet_other.php#2">搜索列表模板</a></td>
          </tr>
          <tr height='24'> 
            <td height="20">·<a href="help_templet_other.php#3">系统模板</a></td>
          </tr>
        </table></td>
  </tr>
</table>
</center>
</body>
</html>