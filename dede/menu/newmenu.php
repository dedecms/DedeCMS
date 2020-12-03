<?
require("../config.php");
?>
<html>
<head>
<title>DedeCms menu</title>
<link rel="stylesheet" href="../base.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=gb2312"></head>
<style>
.tdborder{
border-left: 1px solid #43938B;
border-right: 1px solid #43938B;
border-bottom: 1px solid #43938B;
}
.tdline{
border-bottom: 1px solid #656363;
}
.topitem{cursor: hand;}
body {
scrollbar-base-color:#517CEA;scrollbar-arrow-color:#FFFFFF;
	}
</style>
<script language="javascript">
  function showHide(obj){
    var oStyle = obj.parentElement.parentElement.parentElement.rows[1].style;
    oStyle.display == "none" ? oStyle.display = "block" : oStyle.display = "none";
  }
</script>
<base target="main">
<body bgcolor="#999999" background="../img/bulebg.gif" leftmargin="0" topmargin="0" target="main">
<table width="120" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="19" height="24" align="center" background="newlinebg2.gif"><a href="../exit.php"><img src="topitem2.gif" width="11" height="13" border="0" onClick="showHide(this)" class="topitem"/></td>
    <td width="101" background="newlinebg2.gif" class="topitem"><a href="../exit.php" target="_parent">退出DEDECMS</a></td>
  </tr>
</table>
<?if($cuserLogin->getUserType()>=5){?>
<table width="120" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="19" height="24" align="center" background="newlinebg2.gif"><img src="topitem2.gif" width="11" height="13" border="0" onClick="showHide(this)" class="topitem"/></td>
    <td width="101" background="newlinebg2.gif" class="topitem" onClick="showHide(this)">频道管理</td>
  </tr>
  <tr> 
    <td colspan="2" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4FBF4">
        <tr> 
          <td width="17%" align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td width="83%" class="tdline"><a href='../list_type.php' target='main'>网站栏目管理</a></td>
        </tr>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../web_type_web.php' target='main'>板块模板管理</a></td>
        </tr>
        <?if($cuserLogin->getUserType()==10){?>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../file_view.php?activepath=<?=$mod_dir?>' target='main'>通用模板管理</a></td>
        </tr>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../add_home_page.php' target='main'>主页创建向导</a></td>
        </tr>
        <?}?>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr><td height="2"></td></tr>
      </table>
	  </td>
  </tr>
</table>
<?}?>
<table width="120" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="19" height="24" align="center" background="newlinebg2.gif"><img src="topitem2.gif" width="11" height="13" border="0" onClick="showHide(this)" class="topitem"/></td>
    <td width="101" background="newlinebg2.gif" class="topitem" onClick="showHide(this)">内容维护</td>
  </tr>
  <tr> 
    <td colspan="2" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4FBF4">
        <?if($cuserLogin->getUserType()>=5){?>
        <tr> 
          <td width="17%" align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td width="83%" class="tdline"><a href='../list_news.php' target='main'>已发布文章</a></td>
        </tr>
        <?}?>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../list_news_member.php' target='main'>待审核文章</a></td>
        </tr>
        <?if($cuserLogin->getUserType()>=5){?>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../list_news_spec.php' target='main'>专题管理</a></td>
        </tr>
        <?}?>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../list_feedback.php' target='main'>评论管理</a></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="2"></td>
        </tr>
      </table></td>
  </tr>
</table>
<table width="120" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="19" height="24" align="center" background="newlinebg2.gif"><img src="topitem2.gif" width="11" height="13" border="0" onClick="showHide(this)" class="topitem"/></td>
    <td width="101" background="newlinebg2.gif" class="topitem" onClick="showHide(this)">内容发布</td>
  </tr>
  <tr> 
    <td colspan="2" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4FBF4">
        <tr> 
          <td width="17%" align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td width="83%" class="tdline"><a href='../add_news_view.php' target='main'>普通文章发布</a></td>
        </tr>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../add_news_spec.php' target='main'>专题创建向导</a></td>
        </tr>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../add_news_pic.php' target='main'>图集发布向导</a></td>
        </tr>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../add_news_soft.php' target='main'>软件发布向导</a></td>
        </tr>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../add_news_flash.php' target='main'>Flash向导</a></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="2"></td>
        </tr>
      </table></td>
  </tr>
</table>
<?if($cuserLogin->getUserType()>=5){?>
<table width="120" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="19" height="24" align="center" background="newlinebg2.gif"><img src="topitem2.gif" width="11" height="13" border="0" onClick="showHide(this)" class="topitem"/></td>
    <td width="101" background="newlinebg2.gif" class="topitem" onClick="showHide(this)">附助功能</td>
  </tr>
  <tr style="display: none"> 
    <td colspan="2" align="center"> 
	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4FBF4">
        <?if($cuserLogin->getUserType()==10){?>
        <tr> 
          <td width="17%" align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td width="83%" class="tdline"><a href='../add_my_news.php' target='main'>站内新闻发布</a></td>
        </tr>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../add_friendlink.php' target='main'>友情链接管理</a></td>
        </tr>
		<?}?>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../add_vote.php' target='main'>投票管理</a></td>
        </tr>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../bbs_addons.php' target='main'>论坛扩展</a></td>
        </tr>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='<?=$art_php_dir?>/guestbook/index.php' target='main'>留言簿管理</a></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="2"></td>
        </tr>
      </table></td>
  </tr>
</table>
<?}?>
<?if($cuserLogin->getUserType()==10){?>
<table width="120" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="19" height="24" align="center" background="newlinebg2.gif"><img src="topitem2.gif" width="11" height="13" border="0" onClick="showHide(this)" class="topitem"/></td>
    <td width="101" background="newlinebg2.gif" class="topitem" onClick="showHide(this)">会员管理</td>
  </tr>
  <tr style="display: none"> 
    <td colspan="2" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4FBF4">
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../list_user.php' target='main'>网上会员管理</a></td>
        </tr>
        <tr> 
          <td width="17%" align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td width="83%" class="tdline"><a href='../sys_manager.php' target='main'>系统帐号管理</a></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="2"></td>
        </tr>
      </table></td>
  </tr>
</table>
<?}?>
<table width="120" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="19" height="24" align="center" background="newlinebg2.gif"><img src="topitem2.gif" width="11" height="13" border="0" onClick="showHide(this)" class="topitem"/></td>
    <td width="101" background="newlinebg2.gif" class="topitem" onClick="showHide(this)">文件管理</td>
  </tr>
  <tr style="display: none"> 
    <td colspan="2" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4FBF4">
        <?if($cuserLogin->getUserType()==10){?>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../file_view.php' target='main'>文件浏览器</a></td>
        </tr>
        <?
    	}
    	if($cuserLogin->getUserType()==10) $picview="file_pic.php";
    	else $picview="pic_view.php";
        ?>
        <tr> 
          <td width="17%" align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td width="83%" class="tdline"><a href='../<?=$picview?>' target='main'>图片浏览器</a></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="2"></td>
        </tr>
      </table></td>
  </tr>
</table>
<?if($cuserLogin->getUserType()==10){?>
<table width="120" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="19" height="24" align="center" background="newlinebg2.gif"><img src="topitem2.gif" width="11" height="13" border="0" onClick="showHide(this)" class="topitem"/></td>
    <td width="101" background="newlinebg2.gif" class="topitem" onClick="showHide(this)">数据库管理</td>
  </tr>
  <tr style="display: none"> 
    <td colspan="2" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4FBF4">
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../sys_back_data.php' target='main'>数据备份/还原</a></td>
        </tr>
        <tr>
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../sys_domysql.php' target='main'>MySQL命令</a></td>
        </tr>
        <tr> 
          <td width="17%" align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td width="83%" class="tdline"><a href='../sys_back_datanew.php' target='main'>特定数据备份</a></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="2"></td>
        </tr>
      </table></td>
  </tr>
</table>
<?}?>
<table width="120" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="19" height="24" align="center" background="newlinebg2.gif"><img src="topitem2.gif" width="11" height="13" border="0" onClick="showHide(this)" class="topitem"/></td>
    <td width="101" background="newlinebg2.gif" class="topitem" onClick="showHide(this)">系统帮助</td>
  </tr>
  <tr style="display: none"> 
    <td colspan="2" align="center"> <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4FBF4">
        <tr> 
          <td width="17%" align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td width="83%" class="tdline"><a href='../web_mode.php' target='main'>模板代码参考</a></td>
        </tr>
        <?if($cuserLogin->getUserType()==10){?>
        <tr> 
          <td width="17%" align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td width="83%" class="tdline"><a href='../sys_info.php' target='main'>系统配置参数</a></td>
        </tr>
        <tr> 
          <td align="center" class="tdline"><img src="newitem.gif" width="7" height="10"/></td>
          <td class="tdline"><a href='../blank.php' target='main'>系统信息</a></td>
        </tr>
        <?}?>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="2"></td>
        </tr>
      </table></td>
  </tr>
</table>
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="6"></td>
        </tr>
      </table>
</body>
</html>