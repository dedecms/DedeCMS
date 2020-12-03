<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");

$diyid = isset($diyid) && is_numeric($diyid) ? $diyid : 0;
$action = isset($action) && in_array($action, array('post', 'list', 'view')) ? $action : 'post';
$id = isset($id) && is_numeric($id) ? $id : 0;

if(empty($diyid))
{
	showMsg("非法操作!", 'javascript:;');
	exit();
}

require_once DEDEINC.'/diyform.cls.php';

$diy = new diyform($diyid);

if($action == 'post'){
	if(empty($do)){
		$postform = $diy->getForm(true);
		include DEDEROOT."/templets/plus/{$diy->postTemplate}";
	}elseif($do == 2){
		$dede_fields = empty($dede_fields) ? '' : trim($dede_fields);
		$dede_fieldshash = empty($dede_fieldshash) ? '' : trim($dede_fieldshash);
		if(!empty($dede_fields)){
			if($dede_fieldshash != md5($dede_fields.$cfg_cookie_encode)){
				showMsg("数据校验不对，程序返回", '-1');
				exit();
			}
		}
		$diyform = $dsql->getOne("select * from #@__diyforms where diyid=$diyid");
		if(!is_array($diyform)){
			showmsg("自定义表单不存在", '-1');
			exit();
		}

		$addvar = $addvalue = '';

		if(!empty($dede_fields)){

			$fieldarr = explode(';', $dede_fields);
			if(is_array($fieldarr)){
				foreach($fieldarr as $field){
					if($field == '') continue;
					$fieldinfo = explode(',', $field);
					if($fieldinfo[1] == 'htmltext' || $fieldinfo[1] == 'textdata')
					{
						${$fieldinfo[0]} = FilterSearch(stripslashes(${$fieldinfo[0]}));
						${$fieldinfo[0]} = addslashes(${$fieldinfo[0]});
						${$fieldinfo[0]} = getFieldValue(${$fieldinfo[0]}, $fieldinfo[1],0,'add','','member');
					}else{
						${$fieldinfo[0]} = getFieldValue(${$fieldinfo[0]}, $fieldinfo[1],0,'add','','member');
					}
					$addvar .= ', `'.$fieldinfo[0].'`';
					$addvalue .= ", '".${$fieldinfo[0]}."'";
				}
			}

		}
		$query = "insert into `{$diy->table}` (`id`, `ifcheck` $addvar)  values (NULL, 0 $addvalue)";

		if($dsql->executenonequery($query)){
			$id = $dsql->GetLastID();
			if($diy->public == 2){
				$goto = "diy.php?action=view&diyid={$diy->diyid}&id=$id";
			}else{
				$goto = !empty($cfg_cmspath) ? $cfg_cmspath : '/';
			}
			showmsg('发布成功', $goto);
		}
	}
}elseif($action == 'list'){
	if(empty($diy->public)){
		showMsg("后台关闭前台浏览", 'javascript:;');
		exit();
	}
	include_once DEDEINC.'/datalistcp.class.php';
	if($diy->public == 2){
		$query = "select * from {$diy->table} order by id desc";
	}else{
		$query = "select * from {$diy->table} where ifcheck=1 order by id desc";
	}
	$datalist = new DataListCP();
	$datalist->pageSize = 10;
	$datalist->SetParameter('action', 'list');
	$datalist->SetParameter('diyid', $diyid);
	$datalist->SetTemplate(DEDEINC."/../templets/plus/{$diy->listTemplate}");
	$datalist->SetSource($query);
	$fieldlist = $diy->getFieldList();
	$datalist->Display();
	/*$mylist = $datalist->GetDataList();
	$data = array();
	while($row = $mylist->GetArray('dm')){
		$data[] =$row;
	}


	include DEDEROOT."/templets/plus/{$diy->listTemplate}";
*/
}elseif($action == 'view'){
	if(empty($diy->public)){
		showMsg("后台关闭前台浏览" , 'javascript:;');
		exit();
	}

	if(empty($id)){
		showMsg('非法操作！未指定id', 'javascript:;');
		exit();
	}
	if($diy->public == 2){
		$query = "select * from {$diy->table} where id=$id";
	}else{
		$query = "select * from {$diy->table} where id=$id and ifcheck=1";
	}
	$row = $dsql->getone($query);

	if(!is_array($row)){
		showmsg("你访问的记录不存在或未经审核", '-1');
		exit();
	}

	$fieldlist = $diy->getFieldList();
	include DEDEROOT."/templets/plus/{$diy->viewTemplate}";
}
?>