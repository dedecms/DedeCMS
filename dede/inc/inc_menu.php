<?php
require_once(dirname(__FILE__)."/../../include/config_base.php");
require_once(dirname(__FILE__)."/../../include/pub_dedetag.php");
if(!isset($cfg_ct_mode)) $cfg_ct_mode = 0;
$dsql = new DedeSql(false);
//载入可发布/管理频道
$listset = "";
$addset = "";
$plusset = "";
if($c==2||$c==9||$c==0)
{

  $listsetn = '';
	$listsetn .= "<m:item name='所有档案列表' link='content_list.php' rank='a_List,a_AccList' target='main' />\r\n";
  $listsetn .= "<m:item name='我发布的文档' link='content_list.php?adminid=".$cuserLogin->getUserID()."' rank='a_List,a_AccList,a_MyList' target='main' />\r\n";
  $listsetn .= "<m:item name='等审核的文档' link='content_list.php?arcrank=-1' rank='a_Check,a_AccCheck' target='main' />\r\n";
  $listsetn .= "<m:item name='会员投稿文档' link='content_list.php?ismember=1' rank='a_Check,a_AccCheck' target='main' />\r\n";
  $listsetn .= "<m:item name='分类信息列表' link='info_list.php' rank='a_List,a_AccList' target='main' />\r\n";

  $dsql->SetQuery("Select ID,typename,addcon,mancon From #@__channeltype where ID<>-1 And isshow=1 order by ID asc");
  $dsql->Execute();
  while($row = $dsql->GetArray())
  {
    $dds = $dsql->GetOne("Select count(ID) as dd From `#@__arctype` where channeltype={$row['ID']} ");
    if($dds['dd']<1) continue;
    if($row['mancon']=='') $row['mancon'] = "content_list.php";
    if($row['addcon']=='') $row['addcon'] = "archives_add.php";
    $addset .= "  <m:item name='发布".$row['typename']."' link='".$row['addcon']."?channelid=".$row['ID']."' rank='' target='main' />\r\n";
    $listset .= "  <m:item name='{$row['typename']}管理' link='{$row['mancon']}?channelid={$row['ID']}' rank='t_List,t_AccList' target='main' />\r\n";
  }
  //传统非分表管理模式
  if($cfg_ct_mode!=1)
  {
    $listset = $listsetn;
  }
  $userChannel = $cuserLogin->getUserChannel();
  if(ereg(',',$userChannel) && !TestPurview('admin_AllowAll')){
	  $addset = '';
	  $listset = $listsetn;
  }
}
//载入插件
if($c==5||$c==0)
{
  $dsql->SetQuery("Select * From #@__plus where isshow=1 order by aid asc");
  $dsql->Execute();
  while($row = $dsql->GetObject()){
    $plusset .= $row->menustring."\r\n";
  }
}
//////////////////////////
$menus = "
-----------------------------------------------
<m:top name='快捷菜单' display='block' c='9,' rank=''>
  <m:item name='网站栏目管理' link='catalog_main.php' rank='t_List,t_AccList' target='main' />
  <m:item name='所有档案列表' link='full_list.php' rank='a_List,a_AccList' target='main' />
  <m:item name='我发布的文档' link='full_list.php?adminid=".$cuserLogin->getUserID()."' rank='a_List,a_AccList,a_MyList' target='main' />
  <m:item name='等审核的文档' link='full_list.php?arcrank=-1' rank='a_Check,a_AccCheck' target='main' />
  <m:item name='会员投稿文档' link='full_list.php?ismember=1' rank='a_Check,a_AccCheck' target='main' />
  <m:item name='文档评论管理' link='feedback_main.php' rank='sys_Feedback' target='main' />
  <m:item name='附件数据管理' link='media_main.php' rank='sys_Upload,sys_MyUpload' target='main' />
</m:top>

<m:top name='频道管理' display='block' c='1,' rank=''>
  <m:item name='内容模型管理' link='mychannel_main.php' rank='c_List' target='main' />
  <m:item name='网站栏目管理' link='catalog_main.php' rank='t_List,t_AccList' target='main' />
  <m:item name='自由列表管理' link='freelist_main.php' rank='c_FreeList' target='main' />
  <m:item name='单页文档管理' link='templets_one.php' rank='temp_One' target='main'/>
</m:top>

<m:top name='信息维护' c='2,' display='block'>
  <m:item name='文档评论管理' link='feedback_main.php' rank='sys_Feedback' target='main' />
  <m:item name='附件数据管理' link='media_main.php' rank='sys_Upload,sys_MyUpload' target='main' />
  <m:item name='搜索关键词管理' link='search_keywords_main.php' rank='sys_Keyword' target='main' />
  <m:item name='文档信息统计' link='content_tj.php' rank='sys_ArcTj' target='main' />
</m:top>

<m:top name='文档管理' c='2,' display='block'>
  $listset
</m:top>

<m:top name='内容发布' c='9,' display='block' rank=''>
  <m:item name='树形栏目结构' link='catalog_menu.php' rank='' target='_self' />
</m:top>

<m:top name='批量管理' c='2,' display='block'>
  <m:item name='文档批量维护' link='content_batch_up.php' rank='sys_ArcBatch' target='main' />
  <m:item name='重复标题检测' link='article_test_same.php' rank='sys_ArcBatch' target='main' />
  <m:item name='文档错误修正' link='content_batch_up2.php' rank='sys_ArcBatch' target='main' />
  <m:item name='文档关键词维护' link='article_keywords_main.php' rank='sys_Keyword' target='main' />
  <m:item name='批量获取摘要' link='description_fetch.php' rank='sys_description' target='main' />
  <m:item name='批量获取关键词' link='article_keywords_fetch.php' rank='sys_description' target='main' />
  <m:item name='自动分页' link='pagination_main.php' rank='sys_description' target='main' />

  <m:item name='提取文章缩略图' link='makeminiature/makeminiature.php' rank='sys_ArcBatch' target='main' />
  <m:item name='数据库内容替换' link='sys_data_replace.php' rank='sys_ArcBatch' target='main' />
</m:top>

<m:top name='模块管理' c='6,' display='block'>
  <m:item name='模块管理' link='module_main.php' rank='sys_module' target='main' />
  <m:item name='上传新模块' link='module_upload.php' rank='sys_module' target='main' />
  <m:item name='模块生成向导' link='module_make.php' rank='sys_module' target='main' />
  <!--m:item name='官方模块列表' link='http://www.dedecms.com/modules.php' rank='' target='main' /-->
</m:top>

<m:top name='专题管理' display='block' c='4,' rank=''>
  <m:item name='创建新专题' link='spec_add.php' rank='spec_New' target='main' />
  <m:item name='专题列表' link='content_s_list.php' rank='spec_List' target='main' />
  <m:item name='更新专题HTML' link='makehtml_spec.php' rank='sys_MakeHtml' target='main' />
</m:top>



<m:top name='自动任务' display='block' rank='' c='3,'>
  <m:item name='一键更新网站' link='makehtml_all.php' rank='sys_MakeHtml' target='main' />
  <m:item name='系统计划任务' link='makehtml_task.php' rank='sys_MakeHtml' target='main' />
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
  <m:item name='更新小说HTML' link='makehtml_story.php' rank='sys_MakeHtml' target='main' />
</m:top>

<m:top name='采集管理' display='block' rank='' c='4,'>
  <m:item name='数据规则模型' link='co_export_rule.php' rank='co_NewRule' target='main' />
  <m:item name='采集节点管理' link='co_main.php' rank='co_ListNote' target='main' />
  <m:item name='已下载内容管理' link='co_url.php' rank='co_ViewNote' target='main' />
</m:top>

<m:top name='辅助插件' c='5,' display='block'>
  <m:item name='插件管理器' link='plus_main.php' rank='sys_plus' target='main' />
  $plusset
</m:top>

<m:top name='模板管理' display='block' c='9,10,' rank=''>
  <m:item name='智能标记向导' link='mytag_tag_guide.php' rank='temp_Other' target='main'/>
  <m:item name='自定义宏标记' link='mytag_main.php' rank='temp_MyTag' target='main'/>
  <m:item name='全局标记测试' link='tag_test.php' rank='temp_Test' target='main'/>
  <m:item name='浏览模板目录' link='catalog_do.php?dopost=viewTemplet' rank='temp_All' target='main'/>
</m:top>

~~addmenu~~

<m:top name='会员资料管理' c='4,' display='block'>
  <m:item name='个人会员列表' link='member_main.php' rank='member_List' target='main' />
  <m:item name='企业会员列表' link='company_main.php' rank='member_List' target='main' />
  <m:item name='会员短信管理' link='member_pm.php' rank='member_Pm' target='main' />
  <m:item name='积分兑换记录' link='money2scores.php' rank='member_List' target='main' />
</m:top>

<m:top name='会员业务管理' c='4,' display='block'>
  <m:item name='点卡业务记录' link='member_card.php' rank='member_Operations' target='main' />
  <m:item name='会员业务记录' link='member_operations.php' rank='member_Operations' target='main' />
</m:top>

<m:top name='会员相关设置' c='4,' display='block'>
  <m:item name='会员级别设置' link='member_rank.php' rank='member_Type' target='main' />
  <m:item name='积分头衔设置' link='member_scores.php' rank='member_Scores' target='main' />
  <m:item name='会员产品分类' link='member_type.php' rank='member_Type' target='main' />
  <m:item name='点卡产品分类' link='member_card_type.php' rank='member_Card' target='main' />
  <m:item name='密码类型变换' link='member_password.php' rank='member_Data' target='main' />
</m:top>

<m:top name='互动模块设置' c='1,4,' display='block' rank=''>
  <m:item name='行业管理' link='sectors.php' rank='sectors_All' target='main' />
  <m:item name='地区管理' link='area.php' rank='area_All' target='main' />
  <m:item name='小分类管理' link='smalltype.php' rank='smalltype_All' target='main' />
</m:top>



<m:top name='系统帐号管理' c='7,' display='block' rank=''>
  <m:item name='更改个人资料' link='my_acc_edit.php' rank='sys_MdPwd' target='main' />
  <m:item name='系统帐号管理' link='sys_admin_user.php' rank='sys_User' target='main' />
  <m:item name='用户组设定' link='sys_group.php' rank='sys_Group' target='main' />
</m:top>

<m:top name='系统设置' c='7,' display='block' rank=''>
  <m:item name='系统变量配置' link='sys_info.php' rank='sys_Edit' target='main' />
  <m:item name='图片水印设置' link='sys_info_mark.php' rank='sys_Edit' target='main' />
  <m:item name='通行证设置' link='sys_passport.php' rank='sys_Passport' target='main' />
  <m:item name='系统日志管理' link='log_list.php' rank='sys_Log' target='main' />
</m:top>

<m:top name='PW营销模块' display='block' c='5,' rank=''>
  <m:item name='营销模块设置' link='code_main.php' rank='sys_Edit' target='main' />
</m:top>

<m:top name='快速设置' c='9,' display='block' rank=''>
  <m:item name='系统帐号管理' link='sys_admin_user.php' rank='sys_User' target='main' />
  <m:item name='系统变量配置' link='sys_info.php' rank='sys_Edit' target='main' />
  <m:item name='图片水印设置' link='sys_info_mark.php' rank='sys_Edit' target='main' />
  <m:item name='通行证设置' link='sys_passport.php' rank='sys_Passport' target='main' />
</m:top>

<m:top name='频道设置' c='1,' display='block' rank=''>
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

<m:top name='系统帮助' c='7,9,10,' display='block'>
  <m:item name='模板代码参考' link='http://www.dedecms.com/archives/templethelp/help/index.htm' rank='' target='_blank' />
  <m:item name='官方论坛' link='http://bbs.dedecms.com/' rank='' target='_blank' />
</m:top>
-----------------------------------------------
";
function GetMenus($userrank)
{
global $c,$menus;
$catalog =(isset($c) ? $c : 2);
$headTemplet = "
<dl>
    <dt><a href=\"###\" onclick=\"showHide('items~cc~');\" target=\"_self\">~channelname~</a></dt>
    <dd id=\"items~cc~\" style=\"display:block;\">
			<ul>
";
$footTemplet = "  			</ul>
		</dd>
	</dl>";
$itemTemplet = "<li><a href='~link~' target='~target~'>~itemname~</a></li>
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
		if(!empty($dtp2->CTags) && is_array($dtp2->CTags)){
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
		}
		echo $footTemplet;
		echo "<!-- Item ".($i+1)." End -->\r\n";
	}
}
}//End Function
?>