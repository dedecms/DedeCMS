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
  $addset .= "  <m:item name='发布".$row->typename."' link='".$row->addcon."?channelid=".$row->ID."' rank='' target='main' />\r\n";
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
<m:top name='频道管理' display='block' rank='t_List,t_AccList,c_List,temp_One'>
  <m:item name='网站栏目管理' link='catalog_main.php' rank='t_List,t_AccList' target='main' />
  <m:item name='频道模型管理' link='mychannel_main.php' rank='c_List' target='main' />
  <!--m:item name='自由列表管理' link='freelist_main.php' rank='temp_One' target='main' /-->
  <m:item name='单页文档管理' link='templets_one.php' rank='temp_One' target='main'/>
</m:top>

<m:top name='文档维护' display='block'>
  <m:item name='所有档案列表' link='content_list.php' rank='a_List,a_AccList' target='main' />
  <m:item name='我发布的档案' link='content_list.php?adminid=".$cuserLogin->getUserID()."' rank='a_List,a_AccList,a_MyList' target='main' />
  <m:item name='等审核的档案' link='content_list.php?arcrank=-1' rank='a_Check,a_AccCheck' target='main' />
  <m:item name='评论管理' link='feedback_main.php' rank='sys_Feedback' target='main' />
  <m:item name='文档批量维护' link='content_batch_up.php' rank='sys_ArcBatch' target='main' />
  <m:item name='文档关键词维护' link='article_keywords_main.php' rank='sys_Keyword' target='main' />
  <m:item name='搜索关键词处理' link='search_keywords_main.php' rank='sys_Keyword' target='main' />
  <m:item name='自动摘要|分页' link='article_description_main.php' rank='sys_Keyword' target='main' />
  <m:item name='文档信息统计' link='content_tj.php' rank='sys_ArcTj' target='main' />
</m:top>

<m:top name='内容发布' display='block' rank='a_New,a_AccNew'>
  $addset
</m:top>

<m:top name='频道参数' display='none' rank='sys_Att,sys_SoftConfig,sys_Source,sys_Writer,sys_StringMix'>
  <m:item name='自定义文档属性' link='content_att.php' rank='sys_Att' target='main' />
  <m:item name='软件频道设置' link='soft_config.php' rank='sys_SoftConfig' target='main' />
  <m:item name='防采集串混淆' link='article_string_mix.php' rank='sys_StringMix' target='main' />
  <m:item name='来源管理' link='article_source_edit.php' rank='sys_Source' target='main' />
  <m:item name='作者管理' link='article_writer_edit.php' rank='sys_Writer' target='main' />
</m:top>

<m:top name='专题管理' display='none' rank='spec_New,spec_List'>
  <m:item name='创建新专题' link='spec_add.php' rank='spec_New' target='main' />
  <m:item name='专题列表' link='content_s_list.php' rank='spec_List' target='main' />
  <m:item name='更新专题HTML' link='makehtml_spec.php' rank='sys_MakeHtml' target='main' />
</m:top>

<m:top name='HTML更新' display='none' rank='sys_MakeHtml'>
  <m:item name='更新主页HTML' link='makehtml_homepage.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新栏目HTML' link='makehtml_list.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新文档HTML' link='makehtml_archives.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新网站地图' link='makehtml_map_guide.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新RSS文件' link='makehtml_rss.php' rank='sys_MakeHtml' target='main' />
  <m:item name='获取JS文件' link='makehtml_js.php' rank='sys_MakeHtml' target='main' />
</m:top>

<m:top name='采集管理' display='none' rank='co_NewRule,co_ListNote,co_ViewNote,co_Switch,co_GetOut'>
  <m:item name='数据规则模型' link='co_export_rule.php' rank='co_NewRule' target='main' />
  <m:item name='采集节点管理' link='co_main.php' rank='co_ListNote' target='main' />
  <m:item name='已下载内容管理' link='co_url.php' rank='co_ViewNote' target='main' />
  <m:item name='导入离线数据' link='javascript:;' tmp='co_data_export_out.php' rank='co_GetOut' target='main'/>
</m:top>

<m:top name='辅助插件' display='none'>
  <m:item name='插件管理器' link='plus_main.php' rank='10' target='main' />
  $plusset
</m:top>

<m:top name='文件上传管理' display='none' rank='sys_Upload,sys_MyUpload,plus_文件管理器'>
  <m:item name='上传新文件' link='media_add.php' rank='' target='main' />
  <m:item name='附件数据管理' link='media_main.php' rank='sys_Upload,sys_MyUpload' target='main' />
  <m:item name='文件式管理器' link='file_manage_main.php?activepath=".urlencode($cfg_medias_dir)."' rank='plus_文件管理器' target='main' />
</m:top>

<m:top name='模板管理' display='none' rank='temp_One,temp_Other,temp_MyTag,temp_test,temp_All'>
  <m:item name='智能标记向导' link='mytag_tag_guide.php' rank='temp_Other' target='main'/>
  <m:item name='自定义宏标记' link='mytag_main.php' rank='temp_MyTag' target='main'/>
  <m:item name='全局标记测试' link='tag_test.php' rank='temp_Test' target='main'/>
  <m:item name='浏览模板目录' link='catalog_do.php?dopost=viewTemplet' rank='temp_All' target='main'/>
</m:top>

<m:top name='会员管理' display='none' rank='member_List,member_Type'>
  <m:item name='注册会员列表' link='member_main.php' rank='member_List' target='main' />
  <m:item name='会员权限管理' link='member_rank.php' rank='member_Type' target='main' />
  <m:item name='通行证设置' linka='sys_passport.php' link='javascript:;' rank='sys_Edit' target='main' />
</m:top>

<m:top name='系统设置' display='none' rank='sys_User,sys_Group,sys_Edit,sys_Log,sys_Data'>
  <m:item name='系统用户管理' link='sys_admin_user.php' rank='sys_User' target='main' />
  <m:item name='用户组设定' link='sys_group.php' rank='sys_Group' target='main' />
  <m:item name='修改系统参数' link='sys_info.php' rank='sys_Edit' target='main' />
  <m:item name='系统日志管理' link='log_list.php' rank='sys_Log' target='main' />
  <m:item name='图片水印设置' link='sys_info_mark.php' rank='sys_Edit' target='main' />
  <m:item name='数据库备份/还原' link='sys_data.php' rank='sys_Data' target='main' />
  <m:item name='SQL命令行工具' link='sys_sql_query.php' rank='sys_Data' target='main' />
</m:top>

<m:top name='系统帮助' display='none'>
  <m:item name='模板标记分类' link='templets_menu.php' rank='' target='_self' />
  <m:item name='模板代码参考' link='help_templet.php' rank='' target='main' />
  <m:item name='官方论坛' link='http://bbs.dedecms.com/' rank='' target='_blank' />
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
	if($ctag->GetName()=="top" 
	&& ($ctag->GetAtt('rank')=='' || TestPurview($ctag->GetAtt('rank')) )
	)
	{
		echo "<!-- Item ".($i+1)." Strat -->\r\n";
		$htmp = str_replace("~channelname~",$ctag->GetAtt("name"),$headTemplet);
		$htmp = str_replace("~display~",$ctag->GetAtt("display"),$htmp);
		$htmp = str_replace("~cc~",$i,$htmp);
		echo $htmp;
		$dtp2->LoadSource($ctag->InnerText);
		foreach($dtp2->CTags as $j=>$ctag2)
		{
			if($ctag2->GetName()=="item"
			&& ($ctag2->GetAtt('rank')=='' || TestPurview($ctag2->GetAtt('rank')) )
			)
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