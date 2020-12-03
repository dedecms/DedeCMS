<?php
require_once(dirname(__FILE__).'/config.php');
CheckPurview('sys_ArcBatch');
require_once(dirname(__FILE__).'/../include/oxwindow.class.php');

if(empty($dopost))
{
	$win = new OxWindow();
	$win->Init("sys_repair.php","js/blank.js","POST' enctype='multipart/form-data' ");
	$win->mainTitle = "系统修复工具";
	$wecome_info = "<a href='index_body.php'>系统主页</a> &gt;&gt; 系统错误修复工具";
	$win->AddTitle('本工具用于检测和修复你的系统可能存在的错误');
	$msg = "
	<table width='98%' border='0' cellspacing='0' cellpadding='0' align='center'>
  <tr>
    <td height='250' valign='top'>
    <br />
    由于手动升级时用户没运行指定的SQL语句，或自动升级的遗漏处理或处理出错，可能会导致一些错误，使用本工具会自动检测并处理。<br /><br />
    <b>本工具目前主要执行下面动作：</b><br />
    1、检测从DedeCms V5.3发布以来的数据结构的完整性；<br />
    2、检测微表dede_arctiny的健康性；<br />
    3、检测你的系统是否有因为非正常录入导致的文档id不正常问题。<br />
    <br />
    <br />
    <a href='sys_repair.php?dopost=1' style='font-size:14px;color:red'><b>点击此开始进行常规检测&gt;&gt;</b></a>
    <br /><br /><br />
    </td>
  </tr>
 </table>
	";
	$win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow('hand','');
	$win->Display();
	exit();
}
/*-------------------
检测数据结构
function 1_test_db() {  }
--------------------*/
else if($dopost==1)
{
	$msg = '';
	$dsql->Execute('n',"Show Create Table `#@__arctiny` ");
	$row = $dsql->GetArray('n', MYSQL_BOTH);
	if(!eregi('typeid2', $row[1]))
	{
		$rs = $dsql->ExecuteNoneQuery(" ALTER TABLE `#@__arctiny`  ADD `typeid2` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `typeid` ; ");
		if($rs) $msg .= "◎微表 #@__arctiny 没有副栏目字段typeid2，修复成功！<br />";
		else $msg .= "<font color='red'>◎微表 #@__arctiny 没有副栏目字段typeid2，修复无效！</font><br />";
	}
	else
	{
		$msg .= "◎表： #@__arctiny 结构完整，不需修复！<br />";
	}
	$dsql->Execute('n',"Show Create Table `#@__archives` ");
	$row = $dsql->GetArray('n', MYSQL_BOTH);
	if(!eregi('typeid2', $row[1]))
	{
		$rs = $dsql->ExecuteNoneQuery(" ALTER TABLE `#@__archives`  ADD `typeid2` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `typeid` ; ");
		if($rs) $msg .= "主表 #@__archives 没有副栏目字段typeid2，修复成功！<br />";
		else $msg .= "<font color='red'>主表 #@__archives 没有副栏目字段typeid2，修复无效！</font><br />";
	}
	else
	{
		$msg .= "◎表： #@__archives 结构完整，不需修复！<br />";
	}
	
	$upsqls[] = "ALTER TABLE `#@__tagindex` CHANGE `tag` `tag` VARCHAR( 20 ) NOT NULL default ''; ";
	$upsqls[] = "ALTER TABLE `#@__taglist` CHANGE `tag` `tag` VARCHAR( 20 ) NOT NULL default ''; ";
	$upsqls[] = "ALTER TABLE `#@__archives` CHANGE `litpic` `litpic` VARCHAR( 80 ) NOT NULL default ''; ";
	$upsqls[] = "INSERT INTO `#@__sysconfig` (`aid` ,`varname` ,`info` ,`value` ,`type` ,`groupid`) VALUES (713, 'cfg_need_typeid2', '是否启用副栏目', 'N', 'bool', 6); ";
  $upsqls[] = "INSERT INTO `#@__sysconfig` (`aid` ,`varname` ,`info` ,`value` ,`type` ,`groupid`) VALUES (715, 'cfg_mb_pwdtype', '前台密码验证类型：默认32 — 32位md5，可选：<br />l16 — 前16位， r16 — 后16位， m16 — 中间16位', '32', 'string', 4); ";
	$upsqls[] = "Update `#@__sysconfig` set `groupid` = '8' where `varname` like 'cfg_group_%'; ";
	
	$msg .= "◎检测系统修改过的字段或增加的变量，这些SQL操作运行多次并不会影响系统正常运行...<br />";
	foreach($upsqls as $upsql)
	{
		$dsql->ExecuteNoneQuery($upsql);
		$msg .= "·执行 <font color='green'>".$upsql."</font> ok!<br />";
	}
	
	$msg .= "◎检测 #@__advancedsearch 表是否存在...<br />";
	
	$createQuery = "CREATE TABLE IF NOT EXISTS `#@__advancedsearch` (
  	`mid` int(11) NOT NULL,
  	`maintable` varchar(255) NOT NULL default '',
  	`mainfields` text,
  	`addontable` varchar(255) default '',
  	`addonfields` text,
  	`forms` text,
  	`template` varchar(255) NOT NULL default '',
  	UNIQUE KEY `mid` (`mid`)
  ) TYPE=MyISAM; ";
	$dsql->ExecuteNoneQuery($createQuery);
	
	$win = new OxWindow();
	$win->Init("sys_repair.php","js/blank.js","POST' enctype='multipart/form-data' ");
	$win->mainTitle = "系统修复工具";
	$wecome_info = "<a href='sys_repair.php'>系统错误修复工具</a> &gt;&gt; 检测数据结构";
	$win->AddTitle('本工具用于检测和修复你的系统可能存在的错误');
	$msg = "
	<table width='98%' border='0' cellspacing='0' cellpadding='0' align='center'>
  <tr>
    <td height='250' valign='top'>
    {$msg}
    <b><font color='green'>已完成数据结构完整性检测！</font></b>
    <hr size='1'/>
    <br />
    <b>如果你系统有下面几种问题之一，请检测微表正确性：</b><br />
    1、无法获得主键，因此无法进行后续操作<br />
    2、更新数据库archives表时出错<br />
    3、列表显示数据目与实际文档数不一致<br />
    <br />
    <a href='sys_repair.php?dopost=2' style='font-size:14px;'><b>点击此检测微表正确性&gt;&gt;</b></a>
    <br /><br /><br />
    </td>
  </tr>
 </table>
	";
	$win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow('hand','');
	$win->Display();
	exit();
}
/*-------------------
检测微表正确性并尝试修复
function 2_test_arctiny() {  }
--------------------*/
else if($dopost==2)
{
  $msg = '';
  
  $allarcnum = 0;
  $row = $dsql->GetOne("Select count(*) as dd From `#@__archives` ");
  $allarcnum = $arcnum = $row['dd'];
  $msg .= "·#@__archives 表总记录数： {$arcnum} <br />";
  
  $shtables = array();
  $dsql->Execute('me', " Select addtable From `#@__channeltype` where id < -1 ");
  while($row = $dsql->GetArray('me') )
  {
  	$addtable = strtolower(trim(str_replace('#@__', $cfg_dbprefix, $row['addtable'])));
  	if(empty($addtable)) {
  		continue;
  	}
  	else
  	{
  		if( !isset($shtables[$addtable]) )
  		{
  			$shtables[$addtable] = 1;
  			$row = $dsql->GetOne("Select count(aid) as dd From `$addtable` ");
  			$msg .= "·{$addtable} 表总记录数： {$row['dd']} <br />";
  			$allarcnum += $row['dd'];
  		}
  	}
  }
  $msg .= "※总有效记录数： {$allarcnum} <br /> ";
  $errall = "<a href='index_body.php' style='font-size:14px;'><b>完成修正或无错误返回&gt;&gt;</b></a>";
  $row = $dsql->GetOne("Select count(*) as dd From `#@__arctiny` ");
  $msg .= "※微统计表记录数： {$row['dd']}<br />";
  if($row['dd']==$allarcnum)
  {
  	$msg .= "<p style='color:green;font-size:16px'><b>两者记录一致，无需修正！</b></p><br />";
  }
  else
  {
  	$sql = " TRUNCATE TABLE `#@__arctiny`";
		$dsql->executenonequery($sql);
	  $msg .= "<font color='red'>两者记录不一致，尝试进行简单修正...</font><br />";
		//导入普通模型微数据
		$sql = "insert into `#@__arctiny`(id, typeid, typeid2, arcrank, channel, senddate, sortrank, mid)  
	        Select id, typeid, typeid2, arcrank, channel, senddate, sortrank, mid from `#@__archives` ";
		$dsql->executenonequery($sql);
		//导入单表模型微数据
		foreach($shtables as $tb=>$v)
		{
			$sql = "insert into `#@__arctiny`(id, typeid, typeid2, arcrank, channel, senddate, sortrank, mid)  
			        Select aid, typeid, 0, arcrank, channel, senddate, 0, mid from `$tb` ";
			$rs = $dsql->executenonequery($sql); 
			$doarray[$tb]  = 1;
		}
		$row = $dsql->GetOne("Select count(*) as dd From `#@__arctiny` ");
		if($row['dd']==$allarcnum)
    {
    	$msg .= "<p style='color:green;font-size:16px'><b>修正记录成功！</b></p><br />";
    }
    else
    {
    	$msg .= "<p style='color:red;font-size:16px'><b>修正记录失败，建议进行高级综合检测！</b></p><br />";
    	$errall = " <a href='sys_repair.php?dopost=3' style='font-size:14px;'><b>进行高级结合性检测&gt;&gt;</b></a> ";
    }
  }
  UpDateCatCache();
  $win = new OxWindow();
	$win->Init("sys_repair.php","js/blank.js","POST' enctype='multipart/form-data' ");
	$win->mainTitle = "系统修复工具";
	$wecome_info = "<a href='sys_repair.php'>系统错误修复工具</a> &gt;&gt; 检测微表正确性";
	$win->AddTitle('本工具用于检测和修复你的系统可能存在的错误');
	$msg = "
	<table width='98%' border='0' cellspacing='0' cellpadding='0' align='center'>
  <tr>
    <td height='250' valign='top'>
    {$msg}
    <hr />
    <br />
    {$errall}
    </td>
  </tr>
 </table>
	";
	$win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow('hand','');
	$win->Display();
	exit();
}
/*-------------------
高级方式修复微表(会删除不合法主键的内容)
function 3_re_arctiny() {  }
--------------------*/
else if($dopost==3)
{
  $errnum = 0;
  $sql = " TRUNCATE TABLE `#@__arctiny`";
	$dsql->executenonequery($sql);
	
	$sql = "Select arc.id, arc.typeid, arc.typeid2, arc.arcrank, arc.channel, arc.senddate, arc.sortrank,
	        arc.mid, ch.addtable FROM `#@__archives` arc left join `#@__channeltype` ch on ch.id=arc.channel ";
  $dsql->Execute('me', $sql);
  while($row = $dsql->GetArray('me') )
  {
      $sql = "Insert Into `#@__arctiny`(id, typeid, typeid2, arcrank, channel, senddate, sortrank, mid)
              Values('{$row['id']}','{$row['typeid']}','{$row['typeid2']}','{$row['arcrank']}',
             '{$row['channel']}','{$row['senddate']}','{$row['sortrank']}','{$row['mid']}');  ";
      $rs = $dsql->executenonequery($sql);
      if(!$rs)
      {
      	$addtable = trim($addtable);
      	$errnum ++;
      	$dsql->executenonequery("Delete From `#@__archives` where id='{$row['id']}' ");
      	if(!empty($addtable)) $dsql->executenonequery("Delete From `$addtable` where id='{$row['id']}' ");
      }
  }
  //导入单表模型微数据
	$dsql->SetQuery("Select id,addtable From `#@__channeltype` where id < -1 ");
	$dsql->Execute();
	$doarray = array();
	while($row = $dsql->GetArray())
	{
		$tb = str_replace('#@__', $cfg_dbprefix, $row['addtable']);
		if(empty($tb) || isset($doarray[$tb]) )
		{
			continue;
		}
		else
		{
			$sql = "insert into `#@__arctiny`(id, typeid, typeid2, arcrank, channel, senddate, sortrank, mid)  
			        Select aid, typeid, 0, arcrank, channel, senddate, 0, mid from `$tb` ";
			$rs = $dsql->executenonequery($sql); 
			$doarray[$tb]  = 1;
		}
	}
	$win = new OxWindow();
	$win->Init("sys_repair.php","js/blank.js","POST' enctype='multipart/form-data' ");
	$win->mainTitle = "系统修复工具";
	$wecome_info = "<a href='sys_repair.php'>系统错误修复工具</a> &gt;&gt; 高级综合检测修复";
	$win->AddTitle('本工具用于检测和修复你的系统可能存在的错误');
	$msg = "
	<table width='98%' border='0' cellspacing='0' cellpadding='0' align='center'>
  <tr>
    <td height='250' valign='top'>
    完成所有修复操作，移除错误记录 {$errnum} 条！
    <hr />
    <br />
    <a href='index_body.php' style='font-size:14px;'><b>完成修正或无错误返回&gt;&gt;</b></a>
    </td>
  </tr>
 </table>
	";
	$win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow('hand','');
	$win->Display();
	exit();
}
?>