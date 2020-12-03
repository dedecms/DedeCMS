<?php 
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
<m:top name='快捷菜单' display='block' c='9,' rank=''>
  <m:item name='网站栏目管理' link='catalog_main.php' rank='t_List,t_AccList' target='main' />
  <m:item name='所有档案列表' link='content_list.php' rank='a_List,a_AccList' target='main' />
  <m:item name='我发布的文档' link='content_list.php?adminid=".$cuserLogin->getUserID()."' rank='a_List,a_AccList,a_MyList' target='main' />
  <m:item name='等审核的文档' link='content_list.php?arcrank=-1' rank='a_Check,a_AccCheck' target='main' />
  <m:item name='会员投稿文档' link='content_list.php?ismember=1' rank='a_Check,a_AccCheck' target='main' />
  <m:item name='文档评论管理' link='feedback_main.php' rank='sys_Feedback' target='main' />
  <m:item name='附件数据管理' link='media_main.php' rank='sys_Upload,sys_MyUpload' target='main' />
</m:top>

<m:top name='频道管理' display='block' c='1,' rank=''>
  <m:item name='内容模型管理' link='mychannel_main.php' rank='c_List' target='main' />
  <m:item name='网站栏目管理' link='catalog_main.php' rank='t_List,t_AccList' target='main' />
  <m:item name='自由列表管理' link='freelist_main.php' rank='c_FreeList' target='main' />
  <m:item name='单页文档管理' link='templets_one.php' rank='temp_One' target='main'/>
</m:top>

<m:top name='文档维护' c='2,' display='block'>
  <m:item name='所有档案列表' link='content_list.php' rank='a_List,a_AccList' target='main' />
  <m:item name='我发布的文档' link='content_list.php?adminid=".$cuserLogin->getUserID()."' rank='a_List,a_AccList,a_MyList' target='main' />
  <m:item name='等审核的文档' link='content_list.php?arcrank=-1' rank='a_Check,a_AccCheck' target='main' />
  <m:item name='会员投稿文档' link='content_list.php?ismember=1' rank='a_Check,a_AccCheck' target='main' />
  <m:item name='文档评论管理' link='feedback_main.php' rank='sys_Feedback' target='main' />
  <m:item name='附件数据管理' link='media_main.php' rank='sys_Upload,sys_MyUpload' target='main' />
  <m:item name='文档信息统计' link='content_tj.php' rank='sys_ArcTj' target='main' />
</m:top>

<m:top name='内容发布' c='9,' display='block' rank=''>
  <m:item name='树形栏目结构' link='catalog_menu.php' rank='' target='_self' />
  $addset
</m:top>

<m:top name='批量管理' c='2,' display='block'>
  <m:item name='文档批量维护' link='content_batch_up.php' rank='sys_ArcBatch' target='main' />
  <m:item name='文档关键词维护' link='article_keywords_main.php' rank='sys_Keyword' target='main' />
  <m:item name='搜索关键词处理' link='search_keywords_main.php' rank='sys_Keyword' target='main' />
  <m:item name='自动摘要|分页' link='article_description_main.php' rank='sys_description' target='main' />
  <m:item name='提取文章缩略图' link='makeminiature/makeminiature.php' rank='sys_ArcBatch' target='main' />
  <m:item name='重复文档检测' link='article_test_same.php' rank='sys_ArcBatch' target='main' />
  <m:item name='数据库内容替换' link='sys_data_replace.php' rank='sys_ArcBatch' target='main' />
</m:top>

<m:top name='专题管理' display='block' c='6,' rank=''>
  <m:item name='创建新专题' link='spec_add.php' rank='spec_New' target='main' />
  <m:item name='专题列表' link='content_s_list.php' rank='spec_List' target='main' />
  <m:item name='更新专题HTML' link='makehtml_spec.php' rank='sys_MakeHtml' target='main' />
</m:top>

<m:top name='HTML更新' display='block' rank='' c='3,'>
  <m:item name='更新主页HTML' link='makehtml_homepage.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新栏目HTML' link='makehtml_list.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新文档HTML' link='makehtml_archives.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新网站地图' link='makehtml_map_guide.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新RSS文件' link='makehtml_rss.php' rank='sys_MakeHtml' target='main' />
  <m:item name='获取JS文件' link='makehtml_js.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新专题列表' link='makehtml_spec.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新自由列表' link='makehtml_freelist.php' rank='sys_MakeHtml' target='main' />
</m:top>

<m:top name='采集管理' display='block' rank='' c='6,'>
  <m:item name='数据规则模型' link='co_export_rule.php' rank='co_NewRule' target='main' />
  <m:item name='采集节点管理' link='co_main.php' rank='co_ListNote' target='main' />
  <m:item name='已下载内容管理' link='co_url.php' rank='co_ViewNote' target='main' />
  <m:item name='导入离线数据' link='javascript:;' tmp='co_data_export_out.php' rank='co_GetOut' target='main'/>
</m:top>

<m:top name='辅助插件' c='5,' display='block'>
  <m:item name='插件管理器' link='plus_main.php' rank='10' target='main' />
  $plusset
</m:top>

<m:top name='模板管理' display='block' c='4,' rank=''>
  <m:item name='智能标记向导' link='mytag_tag_guide.php' rank='temp_Other' target='main'/>
  <m:item name='自定义宏标记' link='mytag_main.php' rank='temp_MyTag' target='main'/>
  <m:item name='全局标记测试' link='tag_test.php' rank='temp_Test' target='main'/>
  <m:item name='浏览模板目录' link='catalog_do.php?dopost=viewTemplet' rank='temp_All' target='main'/>
</m:top>

<m:top name='模板标记参考' display='block' c='4,' rank=''>
  <m:item name='通用模板标记' link='help_templet_all.php' rank='' target='main'/>
  <m:item name='封面模板标记' link='help_templet_index.php' rank='' target='main'/>
  <m:item name='列表模板标记' link='help_templet_list.php' rank='' target='main'/>
  <m:item name='文档模板标记' link='help_templet_view.php' rank='' target='main'/>
  <m:item name='其它模板标记' link='help_templet_other.php' rank='' target='main'/>
  <m:item name='标记详细分类' link='templets_menu.php' rank='' target='_self' />
</m:top>

<m:top name='会员管理' c='6,' display='block'>
  <m:item name='注册会员列表' link='member_main.php' rank='member_List' target='main' />
  <m:item name='会员级别设置' link='member_rank.php' rank='member_Type' target='main' />
  <m:item name='会员产品分类' link='member_type.php' rank='member_Type' target='main' />
  <m:item name='点卡产品分类' link='member_card_type.php' rank='member_Card' target='main' />
  <m:item name='点卡业务记录' link='member_card.php' rank='member_Operations' target='main' />
  <m:item name='会员业务记录' link='member_operations.php' rank='member_Operations' target='main' />
  <m:item name='数据导入与转换' link='member_data.php' rank='member_Data' target='main' />
  <m:item name='密码类型变换' link='member_password.php' rank='member_Data' target='main' />
</m:top>

<m:top name='系统帐号管理' c='7,' display='block' rank=''>
  <m:item name='系统帐号管理' link='sys_admin_user.php' rank='sys_User' target='main' />
  <m:item name='用户组设定' link='sys_group.php' rank='sys_Group' target='main' />
</m:top>

<m:top name='系统设置' c='7,' display='block' rank=''>
  <m:item name='系统变量配置' link='sys_info.php' rank='sys_Edit' target='main' />
  <m:item name='图片水印设置' link='sys_info_mark.php' rank='sys_Edit' target='main' />
  <m:item name='通行证设置' link='sys_passport.php' rank='sys_Passport' target='main' />
  <m:item name='系统日志管理' link='log_list.php' rank='sys_Log' target='main' />
</m:top>

<m:top name='快速设置' c='9,' display='block' rank=''>
  <m:item name='系统帐号管理' link='sys_admin_user.php' rank='sys_User' target='main' />
  <m:item name='系统变量配置' link='sys_info.php' rank='sys_Edit' target='main' />
  <m:item name='图片水印设置' link='sys_info_mark.php' rank='sys_Edit' target='main' />
  <m:item name='通行证设置' link='sys_passport.php' rank='sys_Passport' target='main' />
</m:top>

<m:top name='频道设置' c='7,1,2,' display='block' rank=''>
  <m:item name='自定义文档属性' link='content_att.php' rank='sys_Att' target='main' />
  <m:item name='软件频道设置' link='soft_config.php' rank='sys_SoftConfig' target='main' />
  <m:item name='防采集串混淆' link='article_string_mix.php' rank='sys_StringMix' target='main' />
  <m:item name='来源管理' link='article_source_edit.php' rank='sys_Source' target='main' />
  <m:item name='作者管理' link='article_writer_edit.php' rank='sys_Writer' target='main' />
</m:top>

<m:top name='数据库管理' c='7,' display='block' rank=''>
  <m:item name='SQL命令运行器' link='sys_sql_query.php' rank='sys_Data' target='main' />
  <m:item name='数据库备份' link='sys_data.php' rank='sys_Data' target='main' />
  <m:item name='数据库还原' link='sys_data_revert.php' rank='sys_Data' target='main' />
</m:top>

<m:top name='系统帮助' c='7,4,9,' display='block'>
  <m:item name='模板代码参考' link='http://www.dedecms.com/archives/templethelp/help/index.htm' rank='' target='_blank' />
  <m:item name='官方论坛' link='http://bbs.dedecms.com/' rank='' target='_blank' />
</m:top>

-----------------------------------------------
";
function GetMenus($userrank)
{
if(isset($_GET['c'])) $catalog = $_GET['c'];
else $catalog = 2;
global $menus;
$headTemplet = "
  <div onClick='showHide(\"items~cc~\")' class='topitem' align='left'> 
    <div class='topl'><img src='img/mtimg1.gif' width='21' height='24' border='0'></div>
    <div class='topr'>~channelname~</div>
  </div>
  <div style='clear:both'></div>
  <div style='display:~display~' id='items~cc~' class='itemsct'> 
";
$footTemplet = "  </div>";
$itemTemplet = "  <dl class='itemem'> 
    <dd class='tdl'><img src='img/newitem.gif' width='7' height='10' alt=''/></dd>
    <dd class='tdr'><a href='~link~' target='~target~'>~itemname~</a></dd>
  </dl>
";
/////////////////////////////////////////
$dtp = new DedeTagParse();
$dtp->SetNameSpace("m","<",">");
$dtp->LoadSource($menus);
$dtp2 = new DedeTagParse();
$dtp2->SetNameSpace("m","<",">");
foreach($dtp->CTags as $i=>$ctag)
{
	$lc = $ctag->GetAtt('c');
	if($ctag->GetName()=="top" 
	&& (ereg($catalog.',',$lc) || $catalog=='0') )
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