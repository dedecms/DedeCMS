<?php

if(!defined('DEDEINC')) exit("Request Error!");

if(!file_exists(DEDEDATA.'/enums/system.php'))
{
	WriteEnumsCache();
}

//更新枚举缓存
function WriteEnumsCache($egroup='')
{
	global $dsql;
	$egroups = array();
	if($egroup=='') {
		$dsql->SetQuery("Select egroup From `#@__sys_enum` group by egroup ");
	}
	else {
		$dsql->SetQuery("Select egroup From `#@__sys_enum` where egroup='$egroup' group by egroup ");
	}
	$dsql->Execute('enum');
	while($nrow = $dsql->GetArray('enum')) {
		$egroups[] = $nrow['egroup'];
	}
	foreach($egroups as $egroup)
	{
		$cachefile = DEDEDATA.'/enums/'.$egroup.'.php';
		$fp = fopen($cachefile,'w');
		fwrite($fp,'<'."?php\r\nglobal \$em_{$egroup}s;\r\n\$em_{$egroup}s = array();\r\n");
		$dsql->SetQuery("Select ename,evalue,issign From `#@__sys_enum` where egroup='$egroup' order by disorder asc, evalue asc ");
		$dsql->Execute('enum');
		$issign = -1;
		while($nrow = $dsql->GetArray('enum'))
		{
			fwrite($fp,"\$em_{$egroup}s[{$nrow['evalue']}] = '{$nrow['ename']}';\r\n");
			if($issign==-1) $issign = $nrow['issign'];
		}
		fwrite($fp,'?'.'>');
		fclose($fp);
		if(empty($issign)) WriteEnumsJs($egroup);
	}
	return '成功更新所有枚举缓存！';
}

//获取联动表单两级数据的父类与子类
function GetEnumsTypes($v)
{
	$rearr['top'] = $rearr['son'] = 0;
	if($v==0) return $rearr;
	if($v%500==0) {
		$rearr['top'] = $v;
	}
	else {
		$rearr['son'] = $v;
		$rearr['top'] = $v - ($v%500);
	}
	return $rearr;
}

//获取枚举的select表单
function GetEnumsForm($egroup,$evalue=0,$formid='',$seltitle='')
{
	$cachefile = DEDEDATA.'/enums/'.$egroup.'.php';
	include($cachefile);
	if($formid=='')
	{
		$formid = $egroup;
	}
	$forms = "<select name='$formid' id='$formid' class='enumselect'>\r\n";
	$forms .= "\t<option value='0' selected='selected'>--请选择--{$seltitle}</option>\r\n";
	foreach(${'em_'.$egroup.'s'} as $v=>$n)
	{
		$prefix = ($v > 500 && $v%500 != 0) ? '└─ ' : '';
		if($v==$evalue)
		{
			$forms .= "\t<option value='$v' selected='selected'>$prefix$n</option>\r\n";
		}
		else
		{
			$forms .= "\t<option value='$v'>$prefix$n</option>\r\n";
		}
	}
	$forms .= "</select>";
	return $forms;
}

//获取一级数据
function getTopData($egroup)
{
	$data = array();
	$cachefile = DEDEDATA.'/enums/'.$egroup.'.php';
	include($cachefile);
	foreach(${'em_'.$egroup.'s'} as $k=>$v)
	{
		if($k >= 500 && $k%500 == 0) {
			$data[$k] = $v;
		}
	}
	return $data;
}


//获取数据的JS代码(二级联动)
function GetEnumsJs($egroup)
{
	global ${'em_'.$egroup.'s'};
	include_once(DEDEDATA.'/enums/'.$egroup.'.php');
	$jsCode = "<!--\r\n";
	$jsCode .= "em_{$egroup}s=new Array();\r\n";
	foreach(${'em_'.$egroup.'s'} as $k => $v)
	{
		$jsCode .= "em_{$egroup}s[$k]='$v';\r\n";
	}
	$jsCode .= "-->";
	return $jsCode;
}

function WriteEnumsJs($egroup)
{
	$jsfile = DEDEDATA.'/enums/'.$egroup.'.js';
	$fp = fopen($jsfile, 'w');
	fwrite($fp, GetEnumsJs($egroup));
	fclose($fp);
}


//获取枚举的值
function GetEnumsValue($egroup,$evalue=0)
{
	include_once(DEDEDATA.'/enums/'.$egroup.'.php');
	if(isset(${'em_'.$egroup.'s'}[$evalue])) {
		return ${'em_'.$egroup.'s'}[$evalue];
	}
	else {
		return "保密";
	}
}



?>