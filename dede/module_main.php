<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_module');
require_once(dirname(__FILE__)."/../include/dedemodule.class.php");
require_once(dirname(__FILE__)."/../include/oxwindow.class.php");
if(empty($action)) $action = '';
$mdir = DEDEROOT.'/data/module';

function TestWriteAble($d)
{
	$tfile = '_dedet.txt';
	$d = ereg_replace('/$','',$d);
	$fp = @fopen($d.'/'.$tfile,'w');
	if(!$fp) return false;
	else
	{
		fclose($fp);
		$rs = @unlink($d.'/'.$tfile);
		if($rs) return true;
		else return false;
	}
}

function ReWriteConfigAuto()
{
	global $dsql;
	$configfile = DEDEROOT.'/data/config.cache.inc.php';
	if(!is_writeable($configfile))
	{
		echo "配置文件'{$configfile}'不支持写入，无法修改系统配置参数！";
		//ClearAllLink();
		exit();
	}
	$fp = fopen($configfile,'w');
	flock($fp,3);
	fwrite($fp,"<"."?php\r\n");
	$dsql->SetQuery("Select `varname`,`type`,`value`,`groupid` From `#@__sysconfig` order by aid asc ");
	$dsql->Execute();
	while($row = $dsql->GetArray())
	{
		if($row['type']=='number') fwrite($fp,"\${$row['varname']} = ".$row['value'].";\r\n");
		else fwrite($fp,"\${$row['varname']} = '".str_replace("'",'',$row['value'])."';\r\n");
	}
	fwrite($fp,"?".">");
	fclose($fp);
}

/*--------------
function ShowAll();
--------------*/
if($action=='')
{
	$types = array('soft'=>'模块','templets'=>'模板','plus'=>'小插件','patch'=>'补丁');
	$dm = new DedeModule($mdir);
	if(empty($moduletype)) $moduletype = '';
	$modules = $dm->GetModuleList($moduletype);
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

	if($infos['url']=='') $infos['url'] = '&nbsp;';
	$alertMsg = ($infos['lang']==$cfg_soft_lang ? '' : '<br /><font color="red">(这个模块的语言编码与你系统的编码不一致，请向开发者确认它的兼容性)</font>');

	$filelists = $dm->GetFileLists($hash);
	$filelist = '';
	$prvdirs = array();
	$incdir = array();
	foreach($filelists as $v)
	{
		if(empty($v['name'])) continue;
		if($v['type']=='dir')
		{
			$v['type'] = '目录';
			$incdir[] = $v['name'];
		}
		else
		{
			$v['type'] = '文件';
		}
		$filelist .= "{$v['type']}|{$v['name']}\r\n";
	}
	//检测需要的目录权限
	foreach($filelists as $v)
	{
		$prvdir = ereg_replace('/([^/]*)$','/',$v['name']);
		if(!ereg('^\.',$prvdir)) $prvdir = './';
		$n = true;
		foreach($incdir as $k=>$v)
		{
			if(eregi("^".$v,$prvdir))
			{
				$n = false;
				break;
			}
		}
		if(!isset($prvdirs[$prvdir]) && $n && is_dir($prvdir))
		{
			$prvdirs[$prvdir][0] = 1;
			$prvdirs[$prvdir][1] = TestWriteAble($prvdir);
		}
	}
	$prvdir = "<table width='350'>\r\n";
	$prvdir .= "<tr style='background:#D1F5B6'><th width='270'>目录</td><th align='center'>可写</td></tr>\r\n";
	foreach($prvdirs as $k=>$v)
	{
		if($v) $cw = '√';
		else $cw = '<font color="red">×</font>';
		$prvdir .= "<tr><td style='border-bottom:1px solid #D1F5B6'>$k</td>";
		$prvdir .= "<td align='center' style='border-bottom:1px solid #D1F5B6'>$cw</td></tr>\r\n";
	}
	$prvdir .= "</table>";

	$win = new OxWindow();
	$win->Init("module_main.php","js/blank.js","post");
	$win->mainTitle = "模块管理";
	$win->AddTitle("<a href='module_main.php'>模块管理</a> &gt;&gt; 安装模块： {$infos['name']}");
	$win->AddHidden("hash",$hash);
	$win->AddHidden("action",'setupstart');
	if(trim($infos['url'])=='') $infos['url'] = '无';
	$msg = "<style>.dtb{border-bottom:1px dotted #cccccc}</style>
	<table width='98%' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='20%' height='28' class='dtb'>模块名称：</td>
    <td width='80%' class='dtb'>{$infos['name']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>语言：</td>
    <td class='dtb'>{$infos['lang']} {$alertMsg}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>文件大小：</td>
    <td class='dtb'>{$infos['filesize']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>团队名称：</td>
    <td class='dtb'>{$infos['team']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>发布时间：</td>
    <td class='dtb'>{$infos['time']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>电子邮箱：</td>
    <td class='dtb'>{$infos['email']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>官方网址：</td>
    <td class='dtb'>{$infos['url']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>使用协议：</td>
    <td class='dtb'><a href='module_main.php?action=showreadme&hash={$hash}' target='_blank'>点击浏览...</a></td>
  </tr>
  <tr>
    <td height='30' class='dtb' bgcolor='#FCFE9C' colspan='2'>
    <b>注意事项：</b>
    安装时请确保文件列表中涉及的目录前可写入权限，此外“后台管理目录”、“后台管理目录/templets”目录也必须暂时设置可写入权限。
    </td>
  </tr>
  <tr>
    <td height='30'><b>目录权限检测：</b><br /> ../ 为根目录 <br /> ./ 表示当前目录</td>
    <td>
    $prvdir
    </td>
  </tr>
  <tr>
    <td height='30'>模块包含的所有文件列表：</td>
    <td></td>
  </tr>
  <tr>
    <td height='164' colspan='2'>
     <textarea name='filelists' id='filelists' style='width:90%;height:200px'>{$filelist}</textarea>
    </td>
  </tr>
  <tr>
    <td height='28'>对于已存在文件处理方法：</td>
    <td>
   <input name='isreplace' type='radio' value='1' checked='checked' />
    覆盖
   <input name='isreplace' type='radio' value='3' />
   覆盖，保留副本
   <input type='radio' name='isreplace' value='0' />
   保留旧文件
   </td>
  </tr>
</table>
	";
	$win->AddMsgItem("<div style='padding-left:10px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow("ok","");
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

	$minfos = $dm->GetModuleInfo($hash);
	extract($minfos, EXTR_SKIP);

	$menustring = addslashes($dm->GetSystemFile($hash,'menustring'));

	$query = "INSERT INTO `#@__sys_module`(`hashcode` , `modname` , `indexname` , `indexurl` , `ismember` , `menustring` )
                                    VALUES ('$hash' , '$name' , '$indexname' , '$indexurl' , '$ismember' , '$menustring' ) ";

	$rs = $dsql->ExecuteNoneQuery("Delete From `#@__sys_module` where hashcode like '$hash' ");
	$rs = $dsql->ExecuteNoneQuery($query);
	if(!$rs)
	{
		ShowMsg('保存数据库信息失败，无法完成安装！'.$dsql->GetError(),'javascript:;');
		exit();
	}

	$dm->WriteFiles($hash,$isreplace);
	$filename = '';
	if(!isset($autosetup) || $autosetup==0) $filename = $dm->WriteSystemFile($hash,'setup');
	if(!isset($autodel) || $autodel==0) $dm->WriteSystemFile($hash,'uninstall');
	$dm->WriteSystemFile($hash,'readme');
	$dm->Clear();

	//用模块的安装程序安装
	if(!isset($autosetup) || $autosetup==0)
	{
		include(DEDEROOT.'/data/module/'.$filename);
		exit();
	}
	//系统自动安装
	else
	{
		$mysql_version = $dsql->GetVersion(true);
		//默认使用MySQL 4.1 以下版本的SQL语句，对大于4.1版本采用替换处理 TYPE=MyISAM ==> ENGINE=MyISAM DEFAULT CHARSET=#~lang~#
		$setupsql = $dm->GetSystemFile($hash,'setupsql40');
		
		$setupsql = eregi_replace('ENGINE=MyISAM','TYPE=MyISAM',$setupsql);
		
		$sql41tmp = 'ENGINE=MyISAM DEFAULT CHARSET='.$cfg_db_language;
		
		if($mysql_version >= 4.1)
		{
			$setupsql = eregi_replace('TYPE=MyISAM',$sql41tmp,$setupsql);
		}
		
		
		//_ROOTURL_
		if($cfg_cmspath=='/') {
			$cfg_cmspath = '';
		}
		$rooturl = $cfg_basehost.$cfg_cmspath;
		
		$setupsql = eregi_replace('_ROOTURL_',$rooturl,$setupsql);
		$setupsql = ereg_replace("[\r\n]{1,}","\n",$setupsql);
		
		$sqls = split(";[ \t]{0,}\n", $setupsql);
		foreach($sqls as $sql)
		{
			if(trim($sql)!='') $dsql->executenonequery($sql);
		}
		
		ReWriteConfigAuto();
		
		$rflwft = "<script language='javascript' type='text/javascript'>\r\n";
		$rflwft .= "if(window.navigator.userAgent.indexOf('MSIE')>=1) top.document.frames.menu.location = 'index_menu_module.php';\r\n";
		$rflwft .= "else top.document.getElementById('menu').src = 'index_menu_module.php';\r\n";
		$rflwft .= "</script>";
		echo $rflwft;
		
		UpDateCatCache();
		ShowMsg('模块安装完成...','module_main.php');
		exit();
	}
}
/*--------------
function DelModule();
--------------*/
else if($action=='del')
{
	$dm = new DedeModule($mdir);
	$infos = $dm->GetModuleInfo($hash);

	if($infos['url']=='') $infos['url'] = '&nbsp;';
	$alertMsg = ($infos['lang']==$cfg_soft_lang ? '' : '<br /><font color="red">(这个模块的语言编码与你系统的编码不一致，请向开发者确认它的兼容性)</font>');

	$win = new OxWindow();
	$win->Init("module_main.php","js/blank.js","post");
	$win->mainTitle = "模块管理";
	$win->AddTitle("<a href='module_main.php'>模块管理</a> &gt;&gt; 删除模块： {$infos['name']}");
	$win->AddHidden('hash',$hash);
	$win->AddHidden('action','delok');
	$msg = "<style>.dtb{border-bottom:1px dotted #cccccc}</style>
	<table width='750' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='200' height='28' class='dtb'>模块名称：</td>
    <td width='550' class='dtb'>{$infos['name']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>语言：</td>
    <td class='dtb'>{$infos['lang']} {$alertMsg}</td>
  </tr>
  <tr>
    <td width='200' height='28' class='dtb'>文件大小：</td>
    <td width='550' class='dtb'>{$infos['filesize']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>团队名称：</td>
    <td class='dtb'>{$infos['team']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>发布时间：</td>
    <td class='dtb'>{$infos['time']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>电子邮箱：</td>
    <td class='dtb'>{$infos['email']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>官方网址：</td>
    <td class='dtb'>{$infos['url']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>使用协议：</td>
    <td class='dtb'><a href='module_main.php?action=showreadme&hash={$hash}' target='_blank'>点击浏览...</a></td>
  </tr>
  <tr>
    <td height='28' colspan='2'>
    删除模块仅删除这个模块的安装包文件，如果你已经安装，请执行<a href='module_main.php?hash={$hash}&action=uninstall'><u>卸载程序</u></a>来删除！
   </td>
  </tr>
</table>
	";
	$win->AddMsgItem("<div style='padding-left:10px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow("ok","");
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

	if($infos['url']=='') $infos['url'] = '&nbsp;';
	$alertMsg = ($infos['lang']==$cfg_soft_lang ? '' : '<br /><font color="red">(这个模块的语言编码与你系统的编码不一致，请向开发者确认它的兼容性)</font>');

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
	$msg = "<style>.dtb{border-bottom:1px dotted #cccccc}</style>
	<table width='750' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='200' height='28' class='dtb'>模块名称：</td>
    <td width='550' class='dtb'>{$infos['name']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>语言：</td>
    <td class='dtb'>{$infos['lang']} {$alertMsg}</td>
  </tr>
  <tr>
    <td width='200' height='28' class='dtb'>文件大小：</td>
    <td width='550' class='dtb'>{$infos['filesize']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>团队名称：</td>
    <td class='dtb'>{$infos['team']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>发布时间：</td>
    <td class='dtb'>{$infos['time']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>电子邮箱：</td>
    <td class='dtb'>{$infos['email']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>官方网址：</td>
    <td class='dtb'>{$infos['url']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>使用协议：</td>
    <td class='dtb'><a href='module_main.php?action=showreadme&hash={$hash}' target='_blank'>点击浏览...</a></td>
  </tr>
  <tr>
    <td height='28'>模块包含的文件：<br />(文件路径相对于当前目录)</td><td>&nbsp;</td>
  </tr>
  <tr>
    <td height='164' colspan='2'>
     <textarea name='filelists' id='filelists' style='width:90%;height:200px'>{$filelist}</textarea>
    </td>
  </tr>
  <tr>
    <td height='28'>对于模块的文件处理方法：</td>
    <td>
    <input type='radio' name='isreplace' value='0' checked='checked' />
    手工删除文件，仅运行卸载程序
   <input name='isreplace' type='radio' value='2' />
    删除模块的所有文件
   </td>
  </tr>
</table>
	";
	$win->AddMsgItem("<div style='padding-left:10px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow("ok","");
	$win->Display();
	$dm->Clear();
	exit();
}
/*--------------
function UnInstallRun();
--------------*/
else if($action=='uninstallok')
{
	$dsql->ExecuteNoneQuery("Delete From `#@__sys_module` where hashcode like '$hash' ");
	$dm = new DedeModule($mdir);

	$minfos = $dm->GetModuleInfo($hash);
	extract($minfos, EXTR_SKIP);

	if(!isset($moduletype) || $moduletype != 'patch' )
	{
		$dm->DeleteFiles($hash,$isreplace);
	}
	@$dm->DelSystemFile($hash,'readme');
	@$dm->DelSystemFile($hash,'setup');
	$dm->Clear();
	if(!isset($autodel) || $autodel==0)
	{
		include(DEDEROOT."/data/module/{$hash}-uninstall.php");
		@unlink(DEDEROOT."/data/module/{$hash}-uninstall.php");
		exit();
	}
	else
	{
		@$dm->DelSystemFile($hash,'uninstall');
		$delsql = $dm->GetSystemFile($hash,'delsql');
		if(trim($delsql)!='')
		{
			$sqls = explode(';', $delsql);
			foreach($sqls as $sql)
			{
				if(trim($sql)!='') $dsql->executenonequery($sql);
			}
		}
		
		ReWriteConfigAuto();
		
		$rflwft = "<script language='javascript' type='text/javascript'>\r\n";
		$rflwft .= "if(window.navigator.userAgent.indexOf('MSIE')>=1) top.document.frames.menu.location = 'index_menu_module.php';\r\n";
		$rflwft .= "else top.document.getElementById('menu').src = 'index_menu_module.php';\r\n";
		$rflwft .= "</script>";
		echo $rflwft;
		
		ShowMsg('模块卸载完成...','module_main.php');
		exit();
	}
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
	$win->AddTitle("<a href='module_main.php'>模块管理</a> &gt;&gt; 使用说明：");
	$win->AddMsgItem("<div style='padding-left:10px;line-height:150%'>$msg</div>");
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

	if($infos['url']=='') $infos['url'] = '&nbsp;';
	$alertMsg = ($infos['lang']==$cfg_soft_lang ? '' : '<br /><font color="red">(这个模块的语言编码与你系统的编码不一致，请向开发者确认它的兼容性)</font>');

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
	if(file_exists(DEDEROOT."/data/module/{$hash}-readme.php")) {
		$setupinfo = "已安装 <a href='module_main.php?action=uninstall&hash={$hash}'>卸载</a>";
	} else {
		$setupinfo = "未安装 <a href='module_main.php?action=setup&hash={$hash}'>安装</a>";
	}
	$win = new OxWindow();
	$win->Init("","js/blank.js","");
	$win->mainTitle = "模块管理";
	$win->AddTitle("<a href='module_main.php'>模块管理</a> &gt;&gt; 模块详情： {$infos['name']}");
	$msg = "<style>.dtb{border-bottom:1px dotted #cccccc}</style>
	<table width='98%' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='20%' height='28' class='dtb'>模块名称：</td>
    <td width='80%' class='dtb'>{$infos['name']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>语言：</td>
    <td class='dtb'>{$infos['lang']} {$alertMsg}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>文件大小：</td>
    <td class='dtb'>{$infos['filesize']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>是否已安装：</td>
    <td class='dtb'>{$setupinfo}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>团队名称：</td>
    <td class='dtb'>{$infos['team']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>发布时间：</td>
    <td class='dtb'>{$infos['time']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>电子邮箱：</td>
    <td class='dtb'>{$infos['email']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>官方网址：</td>
    <td class='dtb'>{$infos['url']}</td>
  </tr>
  <tr>
    <td height='28' class='dtb'>使用协议：</td>
    <td class='dtb'><a href='module_main.php?action=showreadme&hash={$hash}' target='_blank'>点击浏览...</a></td>
  </tr>
  <tr>
    <td height='28'>模块包含的文件：<br />(文件路径相对于当前目录)</td><td>&nbsp;</td>
  </tr>
  <tr>
    <td height='164' colspan='2'>
     <textarea name='filelists' id='filelists' style='width:90%;height:200px'>{$filelist}</textarea>
    </td>
  </tr>
</table>
	";
	$win->AddMsgItem("<div style='padding-left:10px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow('hand','');
	$win->Display();
	$dm->Clear();
	exit();
}
/*--------------
function Edit();
--------------*/
else if($action=='edit')
{
	$dm = new DedeModule($mdir);

	$minfos = $dm->GetModuleInfo($hash);
	extract($minfos, EXTR_SKIP);

	if(!isset($lang)) $lang = 'gb2312';
	if(!isset($moduletype)) $moduletype = 'soft';

	$menustring = $dm->GetSystemFile($hash,'menustring');

	$setupsql40 = $dm->GetSystemFile($hash,'setupsql40');
	
	$readmetxt = $dm->GetSystemFile($hash,'readme');
	
	$delsql = $dm->GetSystemFile($hash,'delsql');

	$filelist = $dm->GetSystemFile($hash,'oldfilelist',false);
	$dm->Clear();
	require_once(dirname(__FILE__).'/templets/module_edit.htm');
	exit();
}

?>