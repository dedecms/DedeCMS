<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Type');
if(empty($dopost))
{
	$dopost = "";
}

if($dopost == 'save')
{
	$name = isset($name) ? trim($name) : '';

	//替换特殊字符
	$name = preg_replace("/['\"\.\/\*\\\?]/", '', $name);

	$str = 'ENUM(\'个人\',\'企业\'';
	if(isset($types) && is_array($types))
	{
		foreach ($types as $type)
		{
			$type = preg_replace("/['\"\.\/\*\\\?]/", '', $type);
			$str .= ',\''.$type.'\'';
		}
	}
	$str .= ')';
	if($name != ''){
		$str = str_replace(')', ',\''.$name.'\')', $str);
	}
	$sql = " ALTER TABLE `#@__member` CHANGE `mtype` `mtype` $str  NOT NULL DEFAULT '个人' ";

	if($dsql->ExecNoneQuery($sql))
	{
		ShowMsg('会员种类成功', 'member_mtype.php');
		exit();
	}else
	{
		ShowMsg('修改会员类别失败', '-1');
		exit;
	}
}
else
{
	$sql = "desc #@__member";
	$dsql->SetQuery($sql);
	$dsql->Execute();
	while ($row = $dsql->GetArray()) {
		if($row['Field'] == 'mtype')
		{
			$types = $row['Type'];
			break;
		}
	}
	$types = str_replace(array('enum', '(', ')', '\''), '', $types);
	$types = explode(',', $types);
	include(DEDEADMIN.'/templets/member_mtype.htm');
}

?>