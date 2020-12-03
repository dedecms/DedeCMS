<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Data');
if(empty($action)) $action = '';

/*-------------------------------
//列出数据库表里的字段
function __getfields()
--------------------------------*/
if($action=='getfields'){
	AjaxHead();
	$dsql = new DedeSql(false);
	if(!$dsql->linkID){
		echo "<font color='red'>连接数据源的数据库失败！</font><br>";
		echo $qbutton;
		exit();
	}
	$dsql->GetTableFields($exptable);
	echo "<div style='border:1px solid #ababab;background-color:#FEFFF0;margin-top:6px;padding:3px;line-height:160%'>";
	echo "表(".$exptable.")含有的字段：<br>";
	while($row = $dsql->GetFieldObject()){
		echo "<a href=\"javascript:pf('{$row->name}')\"><u>".$row->name."</u></a>\r\n";
	}
	echo "</div>";
	$dsql->Close();
	exit();
}
/*-------------------------------
//保存用户设置，清空会员数据
function __Apply()
--------------------------------*/
else if($action=='apply'){
	if(empty($validate)) $validate=="";
	else $validate = strtolower($validate);
	$svali = GetCkVdValue();
	if($validate=="" || $validate!=$svali){
	ShowMsg("安全确认码不正确!","javascript:;");
	exit();
	}
	if($exptable==''||$rpfield==''){
		ShowMsg("请指定数据表和字段！","javascript:;");
		exit();
	}
	if($rpstring==''){

		ShowMsg("请指定被替换内容！","javascript:;");
		exit();
	}
	$dsql = new DedeSql(false);
	if($rptype=='replace'){
		if(!empty($condition)) $condition = " where $condition ";
		else $condition = "";
		$rs = $dsql->ExecuteNoneQuery("Update $exptable set $rpfield=Replace($rpfield,'$rpstring','$tostring') $condition ");
		$dsql->executenonequery("OPTIMIZE TABLE `$exptable`");
		$dsql->Close();
		if($rs) ShowMsg("成功完成数据替换！","javascript:;");
		else ShowMsg("数据替换失败！","javascript:;");
	}else
	{
		if(!empty($condition)) $condition = " And $condition ";
		else $condition = "";
		$rpstring = stripslashes($rpstring);
		$rpstring2 = str_replace("\\","\\\\",$rpstring);
		$rpstring2 = str_replace("'","\\'",$rpstring2);
		$dsql->SetQuery("Select $keyfield,$rpfield From $exptable where $rpfield REGEXP '$rpstring2'  $condition ");
		$dsql->Execute();
		$tt = $dsql->GetTotalRow();
		if($tt==0){
			$dsql->Close();
			ShowMsg("根据你指定的正则，找不到任何东西！","javascript:;");
			exit();
		}
		$oo = 0;
		while($row = $dsql->GetArray()){
			$kid = $row[$keyfield];
			$rpf = eregi_replace($rpstring,$tostring,$row[$rpfield]);
			$rs = $dsql->ExecuteNoneQuery("Update $exptable set $rpfield='$rpf' where $keyfield='$kid' ");
			if($rs) $oo++;
		}
		$dsql->executenonequery("OPTIMIZE TABLE `$exptable`");
		$dsql->close;
		ShowMsg("共找到 $tt 条记录，成功替换了 $oo 条！","javascript:;");
		exit();
	}
}
ClearAllLink();
?>