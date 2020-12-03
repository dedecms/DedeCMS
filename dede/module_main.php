<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_module');
require_once(dirname(__FILE__)."/../include/inc_modules.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
if(empty($action)) $action = '';
$mdir = dirname(__FILE__).'/module';
if($action=='')
{
	$dm = new DedeModule($mdir);
	$modules = $dm->GetModuleList();
  require_once(dirname(__FILE__)."/templets/module_main.htm");
  $dm->Clear();
  exit();
}
/*--------------
function Setup();
--------------*/
else if($action=='setup')
{
	$dm = new DedeModule($mdir);
	$infos = $dm->GetModuleInfo($hash);
	$filelists = $dm->GetFileLists($hash);
	$filelist = '';
	foreach($filelists as $v)
	{
		if(empty($v['name'])) continue;
		if($v['type']=='dir') $v['type'] = '目录';
		else $v['type'] = '文件';
		$filelist .= "{$v['type']}|{$v['name']}\r\n";
	}
	$win = new OxWindow();
	$win->Init("module_main.php","js/blank.js","post");
	$win->mainTitle = "模块管理";
	$win->AddTitle("<a href='module_main.php'>模块管理</a> &gt;&gt; 安装模块： {$infos['name']}");
	$win->AddHidden("hash",$hash);
	$win->AddHidden("action",'setupstart');
	$msg = "
	<style>.dtb{border-bottom:1px dotted #cccccc}</style>
	<table width='750' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='200' height='26' class='dtb'>模块名称：</td>
    <td width='550' class='dtb'>{$infos['name']}</td>
  </tr>
  <tr>
    <td width='200' height='26' class='dtb'>文件大小：</td>
    <td width='550' class='dtb'>{$infos['filesize']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>团队名称：</td>
    <td class='dtb'>{$infos['team']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>发布时间：</td>
    <td class='dtb'>{$infos['time']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>电子邮箱：</td>
    <td class='dtb'>{$infos['email']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>官方网址：</td>
    <td class='dtb'>{$infos['url']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>使用协议：</td>
    <td class='dtb'><a href='module_main.php?action=showreadme&hash={$hash}' target='_blank'>点击浏览...</a></td>
  </tr>
  <tr>
    <td height='26'>模块包含的文件：<br />(文件路径相对于当前目录)</td><td>&nbsp;</td>
  </tr>
  <tr>
    <td height='164' colspan='2'>
     <textarea name='filelists' id='filelists' style='width:90%;height:200px'>{$filelist}</textarea>
    </td>
  </tr>
  <tr>
    <td height='26'>对于已存在文件处理方法：</td>
    <td>
   <input name='isreplace' type='radio' value='3' checked='checked' />
    覆盖，保留旧文件副本
   <input type='radio' name='isreplace' value='0' />
     保留
   <input name='isreplace' type='radio' value='1' />
     覆盖
   </td>
  </tr>
</table>
	";
	$win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow("okonly","");
	$win->Display();
	$dm->Clear();
	exit();
}
/*---------------
function SetupRun()
--------------*/
else if($action=='setupstart')
{
	if(!is_writeable($mdir))
	{
		ShowMsg("目录 {$mdir} 不支持写入，这将导致安装程序没法正常创建！","-1");
		exit();
	}
	$dm = new DedeModule($mdir);
	$dm->WriteFiles($hash,$isreplace);
	$filename = $dm->WriteSystemFile($hash,'setup');
	$dm->WriteSystemFile($hash,'uninstall');
	$dm->WriteSystemFile($hash,'readme');
	$dm->Clear();
	ShowMsg("成功解压相关文件，现转模块详细安装程序&gt;&gt;","module/".$filename);
	exit();
}
/*--------------
function DelModule();
--------------*/
else if($action=='del')
{
	$dm = new DedeModule($mdir);
	$infos = $dm->GetModuleInfo($hash);
	$win = new OxWindow();
	$win->Init("module_main.php","js/blank.js","post");
	$win->mainTitle = "模块管理";
	$win->AddTitle("<a href='module_main.php'>模块管理</a> &gt;&gt; 删除模块： {$infos['name']}");
	$win->AddHidden("hash",$hash);
	$win->AddHidden("action",'delok');
	$msg = "
	<style>.dtb{border-bottom:1px dotted #cccccc}</style>
	<table width='750' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='200' height='26' class='dtb'>模块名称：</td>
    <td width='550' class='dtb'>{$infos['name']}</td>
  </tr>
  <tr>
    <td width='200' height='26' class='dtb'>文件大小：</td>
    <td width='550' class='dtb'>{$infos['filesize']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>团队名称：</td>
    <td class='dtb'>{$infos['team']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>发布时间：</td>
    <td class='dtb'>{$infos['time']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>电子邮箱：</td>
    <td class='dtb'>{$infos['email']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>官方网址：</td>
    <td class='dtb'>{$infos['url']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>使用协议：</td>
    <td class='dtb'><a href='module_main.php?action=showreadme&hash={$hash}' target='_blank'>点击浏览...</a></td>
  </tr>
  <tr>
    <td height='26' colspan='2'>
    删除模块仅删除这个模块的安装包文件，如果你已经安装，请执行<a href='module_main.php?hash={$hash}&action=uninstall'><u>卸载程序</u></a>来删除！
   </td>
  </tr>
</table>
	";
	$win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow("okonly","");
	$win->Display();
	$dm->Clear();
	exit();
}
else if($action=='delok')
{
	$dm = new DedeModule($mdir);
	$modfile = $mdir."/".$dm->GetHashFile($hash); 
	unlink($modfile) or die("删除文件 {$modfile} 失败！");
	ShowMsg("成功删除一个模块文件！","module_main.php");
	exit();
}
/*--------------
function UnInstall();
--------------*/
else if($action=='uninstall')
{
	$dm = new DedeModule($mdir);
	$infos = $dm->GetModuleInfo($hash);
	$filelists = $dm->GetFileLists($hash);
	$filelist = '';
	foreach($filelists as $v)
	{
		if(empty($v['name'])) continue;
		if($v['type']=='dir') $v['type'] = '目录';
		else $v['type'] = '文件';
		$filelist .= "{$v['type']}|{$v['name']}\r\n";
	}
	$win = new OxWindow();
	$win->Init("module_main.php","js/blank.js","post");
	$win->mainTitle = "模块管理";
	$win->AddTitle("<a href='module_main.php'>模块管理</a> &gt;&gt; 卸载模块： {$infos['name']}");
	$win->AddHidden("hash",$hash);
	$win->AddHidden("action",'uninstallok');
	$msg = "
	<style>.dtb{border-bottom:1px dotted #cccccc}</style>
	<table width='750' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='200' height='26' class='dtb'>模块名称：</td>
    <td width='550' class='dtb'>{$infos['name']}</td>
  </tr>
  <tr>
    <td width='200' height='26' class='dtb'>文件大小：</td>
    <td width='550' class='dtb'>{$infos['filesize']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>团队名称：</td>
    <td class='dtb'>{$infos['team']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>发布时间：</td>
    <td class='dtb'>{$infos['time']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>电子邮箱：</td>
    <td class='dtb'>{$infos['email']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>官方网址：</td>
    <td class='dtb'>{$infos['url']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>使用协议：</td>
    <td class='dtb'><a href='module_main.php?action=showreadme&hash={$hash}' target='_blank'>点击浏览...</a></td>
  </tr>
  <tr>
    <td height='26'>模块包含的文件：<br />(文件路径相对于当前目录)</td><td>&nbsp;</td>
  </tr>
  <tr>
    <td height='164' colspan='2'>
     <textarea name='filelists' id='filelists' style='width:90%;height:200px'>{$filelist}</textarea>
    </td>
  </tr>
  <tr>
    <td height='26'>对于模块的文件处理方法：</td>
    <td>
    <input type='radio' name='isreplace' value='0' checked='checked' />
    手工删除文件，仅运行卸载程序
   <input name='isreplace' type='radio' value='2' />
    删除模块的所有文件
   </td>
  </tr>
</table>
	";
	$win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow("okonly","");
	$win->Display();
	$dm->Clear();
	exit();
}
/*--------------
function UnInstallRun();
--------------*/
else if($action=='uninstallok')
{
	$dm = new DedeModule($mdir);
	$dm->DeleteFiles($hash,$isreplace);
	$dm->DelSystemFile($hash,'readme');
	$dm->DelSystemFile($hash,'setup');
	$dm->Clear();
	ShowMsg("成功完成文件移除，现在转向设置清理程序&gt;&gt;","module/".$hash."-uninstall.php");
	exit();
}
/*--------------
function ShowReadme();
--------------*/
else if($action=='showreadme')
{
	$dm = new DedeModule($mdir);
	$msg = $dm->GetSystemFile($hash,'readme');
	$msg = preg_replace("/(.*)<body/isU","",$msg);
	$msg = preg_replace("/<\/body>(.*)/isU","",$msg);
	$dm->Clear();
	$win = new OxWindow();
	$win->Init("module_main.php","js/blank.js","post");
	$win->mainTitle = "模块管理";
	$win->AddTitle("<a href='module_main.php'>模块管理</a> &gt;&gt; 使用协议：");
	$win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow("hand");
	$win->Display();
	exit();
}
/*--------------
function ViewOne();
--------------*/
else if($action=='view')
{
	$dm = new DedeModule($mdir);
	$infos = $dm->GetModuleInfo($hash);
	$filelists = $dm->GetFileLists($hash);
	$filelist = '';
	$setupinfo = '';
	foreach($filelists as $v)
	{
		if(empty($v['name'])) continue;
		if($v['type']=='dir') $v['type'] = '目录';
		else $v['type'] = '文件';
		$filelist .= "{$v['type']}|{$v['name']}\r\n";
	}
	if(file_exists(dirname(__FILE__)."/module/{$hash}-readme.php")){
      $setupinfo = "已安装 <a href='module_main.php?action=uninstall&hash={$hash}'>卸载</a>";
  }else{
      $setupinfo = "未安装 <a href='module_main.php?action=setup&hash={$hash}'>安装</a>";
  }
	$win = new OxWindow();
	$win->Init("","js/blank.js","");
	$win->mainTitle = "模块管理";
	$win->AddTitle("<a href='module_main.php'>模块管理</a> &gt;&gt; 模块详情： {$infos['name']}");
	$msg = "
	<style>.dtb{border-bottom:1px dotted #cccccc}</style>
	<table width='750' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='200' height='26' class='dtb'>模块名称：</td>
    <td width='550' class='dtb'>{$infos['name']}</td>
  </tr>
  <tr>
    <td width='200' height='26' class='dtb'>文件大小：</td>
    <td width='550' class='dtb'>{$infos['filesize']}</td>
  </tr>
  <tr>
    <td width='200' height='26' class='dtb'>是否已安装：</td>
    <td width='550' class='dtb'>{$setupinfo}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>团队名称：</td>
    <td class='dtb'>{$infos['team']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>发布时间：</td>
    <td class='dtb'>{$infos['time']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>电子邮箱：</td>
    <td class='dtb'>{$infos['email']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>官方网址：</td>
    <td class='dtb'>{$infos['url']}</td>
  </tr>
  <tr>
    <td height='26' class='dtb'>使用协议：</td>
    <td class='dtb'><a href='module_main.php?action=showreadme&hash={$hash}' target='_blank'>点击浏览...</a></td>
  </tr>
  <tr>
    <td height='26'>模块包含的文件：<br />(文件路径相对于当前目录)</td><td>&nbsp;</td>
  </tr>
  <tr>
    <td height='164' colspan='2'>
     <textarea name='filelists' id='filelists' style='width:90%;height:200px'>{$filelist}</textarea>
    </td>
  </tr>
</table>
	";
	$win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow("hand","");
	$win->Display();
	$dm->Clear();
	exit();
}
/*--------------
function EditOne();
--------------*/
else if($action=='edit')
{
	$dm = new DedeModule($mdir);
	$minfos = $dm->GetModuleInfo($hash);
	$modulname = $minfos['name'];
  $team = $minfos['team'];
  $mtime  = $minfos['time'];
  $email = $minfos['email'];
  $url = $minfos['url'];
  /*
	$filelists = $dm->GetFileLists($hash);
	$filelist = '';
	$setupinfo = '';
	$ds = array();
	foreach($filelists as $v)
	{
		if(empty($v['name'])) continue;
		$sonfile = false;
		foreach($ds as $vv){
			if(eregi("^".$vv,$v['name'])){ $sonfile=true; break; }
		}
		if(!$sonfile) $filelist .= "{$v['name']}\r\n";
		if($v['type']=='dir') $ds[] = $v['name'];
	}
	*/
	$filelist = $dm->GetSystemFile($hash,'oldfilelist',false);
  $dm->Clear();
	require_once(dirname(__FILE__)."/templets/module_edit.htm");
	exit();
}

ClearAllLink();
?>