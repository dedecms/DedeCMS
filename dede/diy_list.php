<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('c_New');
$diyid = isset($diyid) && is_numeric($diyid) ? $diyid : 0;
$action = isset($action) && in_array($action, array('post', 'list', 'edit', 'check', 'delete')) ? $action : '';
if(empty($diyid))
{
	showMsg("非法操作!", 'javascript:;');
	exit();
}
require_once DEDEINC.'/diyform.cls.php';
$diy = new diyform($diyid);
if($action == 'post')
{
	if(empty($do))
	{
		$postform = $diy->getForm('post','','admin');
		include DEDEADMIN.'/templets/diy_post.htm';
	}
	elseif($do == 2)
	{
		$dede_fields = empty($dede_fields) ? '' : trim($dede_fields);
		$dede_fieldshash = empty($dede_fieldshash) ? '' : trim($dede_fieldshash);
		if(!empty($dede_fields))
		{
			if($dede_fieldshash != md5($dede_fields.$cfg_cookie_encode))
			{
				showMsg("数据校验不对，程序返回", '-1');
				exit();
			}
		}
		$diyform = $dsql->getOne("select * from #@__diyforms where diyid=$diyid");
		if(!is_array($diyform))
		{
			showmsg("自定义表单不存在", '-1');
			exit();
		}
		$addvar = $addvalue = '';
		if(!empty($dede_fields))
		{
			$fieldarr = explode(';', $dede_fields);
			if(is_array($fieldarr))
			{
				foreach($fieldarr as $field)
				{
					if($field == '')
					{
						continue;
					}
					$fieldinfo = explode(',', $field);
					if($fieldinfo[1] == 'htmltext' || $fieldinfo[1] == 'textdata')
					{
						${$fieldinfo[0]} = filterscript(stripslashes(${$fieldinfo[0]}));
						${$fieldinfo[0]} = addslashes(${$fieldinfo[0]});
						${$fieldinfo[0]} = getFieldValue(${$fieldinfo[0]}, $fieldinfo[1],0,'add','','member');
					}
					else
					{
						${$fieldinfo[0]} = getFieldValue(${$fieldinfo[0]}, $fieldinfo[1],0,'add','','member');
					}
					$addvar .= ', `'.$fieldinfo[0].'`';
					$addvalue .= ", '".${$fieldinfo[0]}."'";
				}
			}
		}
		$query = "insert into `{$diy->table}` (`id`, `ifcheck` $addvar)  values (NULL, 0 $addvalue)";
		if($dsql->executenonequery($query))
		{
			$goto = "diy_list.php?action=list&diyid={$diy->diyid}";
			showmsg('发布成功', $goto);
		}
		else
		{
			showmsg('对不起，发布不成功', '-1');
		}
	}
}elseif($action == 'list')
{
	include_once DEDEINC.'/datalistcp.class.php';
	$query = "select * from {$diy->table} order by id desc";
	$datalist = new DataListCP();
	$datalist->pageSize = 10;
	$datalist->SetParameter('action', 'list');
	$datalist->SetParameter('diyid', $diyid);
	$datalist->SetTemplate(DEDEADMIN.'/templets/diy_list.htm');
	$datalist->SetSource($query);
	$fieldlist = $diy->getFieldList();
	$datalist->Display();
}elseif($action == 'edit')
{
	if(empty($do))
	{
		$id = isset($id) && is_numeric($id) ? $id : 0;
		if(empty($id))
		{
			showMsg('非法操作！未指定id', 'javascript:;');
			exit();
		}
		$query = "select * from {$diy->table} where id=$id";
		$row = $dsql->getone($query);
		if(!is_array($row))
		{
			showmsg("你访问的记录不存在或未经审核", '-1');
			exit();
		}
		$postform = $diy->getForm('edit', $row, 'admin');
		$fieldlist = $diy->getFieldList();
		$c1 = $row['ifcheck'] == 1 ? 'checked' : '';
		$c2 = $row['ifcheck'] == 0 ? 'checked' : '';
		include DEDEADMIN.'/templets/diy_edit_content.htm';
	}
	elseif($do == 2)
	{
		$dede_fields = empty($dede_fields) ? '' : trim($dede_fields);
		$dede_fieldshash = empty($dede_fieldshash) ? '' : trim($dede_fieldshash);
		if(!empty($dede_fields))
		{
			if($dede_fieldshash != md5($dede_fields.$cfg_cookie_encode))
			{
				showMsg("数据校验不对，程序返回", '-1');
				exit();
			}
		}
		$diyform = $dsql->getOne("select * from #@__diyforms where diyid=$diyid");
		if(!is_array($diyform))
		{
			showmsg("自定义表单不存在", '-1');
			exit();
		}
		$addsql = '';
		if(!empty($dede_fields))
		{
			$fieldarr = explode(';', $dede_fields);
			if(is_array($fieldarr))
			{
				foreach($fieldarr as $field)
				{
					if($field == '')
					{
						continue;
					}
					$fieldinfo = explode(',', $field);
					if($fieldinfo[1] == 'htmltext' || $fieldinfo[1] == 'textdata')
					{
						${$fieldinfo[0]} = filterscript(stripslashes(${$fieldinfo[0]}));
						${$fieldinfo[0]} = addslashes(${$fieldinfo[0]});
						${$fieldinfo[0]} = getFieldValue(${$fieldinfo[0]}, $fieldinfo[1],0,'add','','member');
					}
					else
					{
						${$fieldinfo[0]} = getFieldValue(${$fieldinfo[0]}, $fieldinfo[1],0,'add','','member');
					}
					$addsql .= '`'.$fieldinfo[0]."`='".${$fieldinfo[0]}."',";
				}
			}
		}
		$query = "update `$diy->table` set $addsql ifcheck='$ifcheck' where id=$id";
		if($dsql->executenonequery($query))
		{
			$goto = "diy_list.php?action=list&diyid={$diy->diyid}";
			showmsg('编辑成功', $goto);
		}
		else
		{
			showmsg('编辑成功', '-1');
		}
	}
}elseif($action == 'check')
{
	if(is_array($id))
	{
		$ids = implode(',', $id);
	}
	else
	{
		showmsg('未选中要操作的内容', '-1');
		exit();
	}
	$query = "update `$diy->table` set ifcheck=1 where id in ($ids)";
	if($dsql->executenonequery($query))
	{
		showmsg('审核成功', "diy_list.php?action=list&diyid={$diy->diyid}");
	}
	else
	{
		showmsg('审核失败', "diy_list.php?action=list&diyid={$diy->diyid}");
	}
}elseif($action == 'delete')
{
	if(is_array($id))
	{
		$ids = implode(',', $id);
	}else
	{
		showmsg('未选中要操作的内容', '-1');
		exit();
	}
	$query = "delete from `$diy->table` where id in ($ids)";
	if($dsql->executenonequery($query))
	{
		showmsg('删除成功', "diy_list.php?action=list&diyid={$diy->diyid}");
	}
	else
	{
		showmsg('删除失败', "diy_list.php?action=list&diyid={$diy->diyid}");
	}
}else
{
	showmsg('未定义操作', "-1");
}

?>