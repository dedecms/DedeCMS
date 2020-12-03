<?php
require_once(dirname(__FILE__)."/../config.php");

/*
模块菜单一般在不要直接改此文件，直接保存在#@__sys_module表即可，格式为
<m:top name='问答模块管理' c='6,' display='block' rank=''>
<m:item name='问答栏目管理' link='ask_type.php' rank='' target='main' />
<m:item name='问答问题管理' link='ask_admin.php' rank='' target='main' />
<m:item name='问答答案管理' link='ask_answer.php' rank='' target='main' />
</m:top>
这个菜单可在生成模块时指定
*/

//载入模块菜单
$moduleset = '';
$dsql->SetQuery("Select * From `#@__sys_module` order by id desc");
$dsql->Execute();
while($row = $dsql->GetObject()) {
	$moduleset .= $row->menustring."\r\n";
}
//载入插件菜单
$plusset = '';
$dsql->SetQuery("Select * From `#@__plus` where isshow=1 order by aid asc");
$dsql->Execute();
while($row = $dsql->GetObject()) {
	$plusset .= $row->menustring."\r\n";
}
//////////////////////////
$menusMoudle = "
-----------------------------------------------

<m:top name='模块管理' c='6,' display='block'>
  <m:item name='模块管理' link='module_main.php' rank='sys_module' target='main' />
  <m:item name='上传新模块' link='module_upload.php' rank='sys_module' target='main' />
  <m:item name='模块生成向导' link='module_make.php' rank='sys_module' target='main' />
</m:top>

<m:top item='7' name='辅助插件' display='block'>
  <m:item name='插件管理器' link='plus_main.php' rank='10' target='main' />
  $plusset
</m:top>

$moduleset
-----------------------------------------------
";
?>