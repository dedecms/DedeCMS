<?
require_once(dirname(__FILE__)."/../../include/config_base.php");
require_once(dirname(__FILE__)."/../../include/pub_dedetag.php");
$dsql = new DedeSql(false);
//载入可发布频道
$dsql->SetQuery("Select ID,typename,addcon From #@__channeltype where ID>0 And isshow=1 order by ID asc");
$dsql->Execute();
$addset = "";
while($row = $dsql->GetObject())
{
  $addset .= "  <m:item name='发布".$row->typename."' link='".$row->addcon."?channelid=".$row->ID."' rank='1' target='main' />\r\n";
}
//载入插件
$dsql->SetQuery("Select * From #@__plus where isshow=1 order by aid asc");
$dsql->Execute();
$plusset = "";
while($row = $dsql->GetObject())
{
  $plusset .= $row->menustring."\r\n";
}
$dsql->Close();
//////////////////////////
$menus = "
-----------------------------------------------
<m:top name='频道管理' display='block' rank='1'>
  <m:item name='网站栏目管理' link='catalog_main.php' rank='1' target='main' />
  <m:item name='频道模型管理' link='mychannel_main.php' rank='5' target='main' />
  <m:item name='自定义标记' link='mytag_main.php' rank='5' target='main' />
  <m:item name='全局标记测试' link='tag_test.php' rank='5' target='main' />
  <m:item name='单独页面管理' link='templets_one.php' rank='5' target='main' />
  <m:item name='浏览模板目录' link='catalog_do.php?dopost=viewTemplet' rank='5' target='main' />
</m:top>

<m:top name='文档维护' display='block' rank='1'>
  <m:item name='普通内容列表' link='content_list.php' rank='1' target='main' />
  <m:item name='图文混排列表' link='content_i_list.php' rank='1' target='main' />
  <m:item name='关键词维护' link='article_keywords_main.php' rank='5' target='main' />
  <m:item name='评论管理' link='feedback_main.php' rank='1' target='main' />
</m:top>

<m:top name='内容发布' display='block' rank='1'>
  $addset
</m:top>

<m:top name='HTML更新' display='block' rank='5'>
  <m:item name='更新主页HTML' link='makehtml_homepage.php' rank='5' target='main' />
  <m:item name='更新栏目HTML' link='makehtml_list.php' rank='5' target='main' />
  <m:item name='更新文档HTML' link='makehtml_archives.php' rank='5' target='main' />
  <m:item name='更新网站地图' link='makehtml_map_guide.php' rank='5' target='main' />
  <m:item name='更新RSS文件' link='makehtml_rss.php' rank='5' target='main' />
  <m:item name='获取JS文件' link='makehtml_js.php' rank='5' target='main' />
</m:top>

<m:top name='专题管理' display='none' rank='5'>
  <m:item name='创建新专题' link='spec_add.php' rank='5' target='main' />
  <m:item name='专题列表' link='content_s_list.php' rank='5' target='main' />
  <m:item name='更新专题HTML' link='makehtml_spec.php' rank='5' target='main' />
</m:top>

<m:top name='采集管理' display='none' rank='5'>
  <m:item name='采集节点管理' link='co_main.php' rank='5' target='main' />
  <!--m:item name='导出规则管理' link='co_export_rule.php' rank='5' target='main' /-->
  <m:item name='已下载内容管理' link='co_url.php' rank='5' target='main' />
</m:top>

<m:top name='附助插件' display='none' rank='1'>
  <m:item name='插件管理器' link='plus_main.php' rank='10' target='main' />
  $plusset
</m:top>

<m:top name='会员管理' display='none' rank='5'>
  <!--m:item name='外部系统整合' link='javascript:;' rank='5' target='main' /-->
  <m:item name='注册会员列表' link='member_main.php' rank='5' target='main' />
  <m:item name='会员权限管理' link='member_rank.php' rank='5' target='main' />
</m:top>

<m:top name='系统管理' display='none' rank='10'>
  <m:item name='系统帐号管理' link='sys_admin_user.php' rank='10' target='main' />
  <m:item name='数据备份还原' link='sys_back_data.php' rank='10' target='main' />
</m:top>

<m:top name='系统帮助' display='none' rank='1'>
  <m:item name='系统主页' link='index_body.php' rank='1' target='main' />
  <m:item name='模板代码参考' link='help_templet.php' rank='1' target='main' />
  <m:item name='织梦官方论坛' link='http://bbs.dedecms.com/' rank='1' target='_blank' />
</m:top>

-----------------------------------------------
";
function GetMenus($userrank)
{
$headTemplet = "<table width='130' border='0' align='center' cellpadding='0' cellspacing='0'>
  <tr> 
    <td colspan='2' height='24' align='center' background='img/menu_top.gif' onClick='showHide(\"items~cc~\")' class='top'>~channelname~</td>
  </tr>
  <tr style='display:~display~' id='items~cc~'> 
    <td colspan='2' align='center'>
	<table width='130' border='0' cellspacing='0' cellpadding='0' bgcolor='#F4FBF4'>
";
$footTemplet = "	   </table>
    </td>
  </tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
<tr><td height='3'></td></tr>
</table>
";
$itemTemplet = "	  <tr> 
          <td align='center' class='tdline-left' width='20%'><img src='img/newitem.gif' width='7' height='10' alt=''/></td>
          <td class='tdline-right' width='80%'><a href='~link~' target='~target~'>~itemname~</a></td>
	  </tr>
";
/////////////////////////////////////////
global $menus;
$dtp = new DedeTagParse();
$dtp->SetNameSpace("m","<",">");
$dtp->LoadSource($menus);
$dtp2 = new DedeTagParse();
$dtp2->SetNameSpace("m","<",">");
foreach($dtp->CTags as $i=>$ctag)
{
	if($ctag->GetName()=="top" && $ctag->GetAtt("rank")<=$userrank)
	{
		echo "<!-- Item ".($i+1)." Strat -->\r\n";
		$htmp = str_replace("~channelname~",$ctag->GetAtt("name"),$headTemplet);
		$htmp = str_replace("~display~",$ctag->GetAtt("display"),$htmp);
		$htmp = str_replace("~cc~",$i,$htmp);
		echo $htmp;
		$dtp2->LoadSource($ctag->InnerText);
		foreach($dtp2->CTags as $j=>$ctag2)
		{
			if($ctag2->GetName()=="item" && $ctag2->GetAtt("rank")<=$userrank)
			{
				 $itemtmp = str_replace("~link~",$ctag2->GetAtt("link"),$itemTemplet);
				 $itemtmp = str_replace("~target~",$ctag2->GetAtt("target"),$itemtmp);
				 $itemtmp = str_replace("~n~",$i,$itemtmp);
				 $itemtmp = str_replace("~itemname~",$ctag2->GetAtt("name"),$itemtmp);
				 echo $itemtmp;
			}
		}
		echo $footTemplet;
		echo "<!-- Item ".($i+1)." End -->\r\n";
	}
}
}//End Function
?>