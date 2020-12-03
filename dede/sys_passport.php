<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Passport');
if(!function_exists('file_get_contents')){
	ShowMsg("你的系统不支持函数：file_get_contents<br><br> 不能使用 Dede 通行证接口！","javascript:;");
	exit();
}
if(empty($action)) $action = '';
if($action=='save'){
	$dsql = new DedeSql(false);
	$dsql->ExecuteNoneQuery("Delete From #@__syspassport ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_isopen','cfg_pp_isopen'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_regurl','cfg_pp_regurl'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_loginurl','cfg_pp_loginurl'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_exiturl','cfg_pp_exiturl'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_editsafeurl','cfg_pp_editsafeurl'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_name','cfg_pp_name'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_indexurl','cfg_pp_indexurl'); ");
	$dsql->Close();
	$fp = fopen(dirname(__FILE__)."/../include/config_passport.php","w") or die("写入文件 ../include/config_passport.php 失败!");
	fwrite($fp,'<'.'?php ');
	fwrite($fp,"\r\n");
	foreach($GLOBALS as $k=>$v){
		if(ereg('^pp_',$k)){
			$v = str_replace("'","`",stripslashes($v));
			fwrite($fp,'$cfg_'.$k." = '".$v."';\r\n");
		}
	}
	fwrite($fp,'?'.'>');
	fclose($fp);
	ShowMsg("成功更改通行证设置！","sys_passport.php");
	exit();
}
$dsql = new DedeSql(false);
$dsql->SetQuery("Select * From #@__syspassport ");
$dsql->Execute();
while($row = $dsql->GetArray()){ $$row['varname'] = $row['value']; }

require_once(dirname(__FILE__)."/templets/sys_passport.htm");

ClearAllLink();
?>