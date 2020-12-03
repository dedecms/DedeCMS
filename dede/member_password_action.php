<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Data');
if(empty($notcdata)) $notcdata = 0;
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select count(*) as dd From #@__member ");
$dd = $row['dd'];
//保存配置
$configfile = dirname(__FILE__)."/../include/config_hand.php";
$configfile_bak = dirname(__FILE__)."/../include/config_hand_bak.php";
$dsql->ExecuteNoneQuery("Update #@__sysconfig set value='$newtype' where varname='cfg_pwdtype' ");
$dsql->ExecuteNoneQuery("Update #@__sysconfig set value='$newmd5len' where varname='cfg_md5len' ");
$dsql->ExecuteNoneQuery("Update #@__sysconfig set value='$newsign' where varname='cfg_ddsign' ");
$dsql->SetQuery("Select varname,value From #@__sysconfig order by aid asc");
$dsql->Execute();
copy($configfile,$configfile_bak) or die("保存配置{$configfile}时失败！请检测权限");
$fp = fopen($configfile,'w') or die("保存配置{$configfile}时失败！请检测权限");
flock($fp,3);
fwrite($fp,"<"."?php\r\n");
while($row = $dsql->GetArray()){
  fwrite($fp,"\${$row['varname']} = '".str_replace("'","\\'",$row['value'])."';\r\n");
}
fwrite($fp,"?".">");
fclose($fp);
if($dd==0){
	$dsql->Close();
	ShowMsg("成功保存配置，由于会员系统无数据，因此不需转换！","javascript:;");
	exit();
}
if($notcdata==1){
	$dsql->Close();
	ShowMsg("成功保存配置！","javascript:;");
	exit();
}
//-------------------------------
//旧密码为文明密码
if($cfg_pwdtype=='none'){
	if($newtype=='none'){
		$dsql->Close();
	  ShowMsg("你指定的类型和系统目前的类型一致，不需要转换！","javascript:;");
	  exit();
	}
  $cfg_pwdtype = $newtype;
  $cfg_md5len = $newmd5len;
  $cfg_ddsign = $newsign;
	$dsql->SetQuery("Select ID,pwd From #@__member ");
	$dsql->Execute();
	while($row = $dsql->GetArray()){
		$pwd = addslashes(GetEncodePwd($row['pwd']));
		$ID = $row['ID'];
		$dsql->ExecuteNoneQuery("Update #@__member set pwd='$pwd' where ID='$ID' ");
	}
	$dsql->Close();
	ShowMsg("成功完成 {$dd} 条数据的转换！","javascript:;");
	exit();
}
//旧密码为dede加密算法密码
else if($cfg_pwdtype=='dd'){
	if($newtype=='dd' && $newsign==$cfg_ddsign){
		$dsql->Close();
	  ShowMsg("你指定的类型和系统目前的类型一致，不需要转换！","javascript:;");
	  exit();
	}
  $oosign = $cfg_ddsign;
  $cfg_pwdtype = $newtype;
  $cfg_md5len = $newmd5len;
  $cfg_ddsign = $newsign;
	$dsql->SetQuery("Select ID,pwd From #@__member ");
	$dsql->Execute();
	while($row = $dsql->GetArray()){
		$ID = $row['ID'];
		$pwd = DdPwdDecode($row['pwd'],$oosign);
		$pwd = addslashes(GetEncodePwd($pwd));
		$dsql->ExecuteNoneQuery("Update #@__member set pwd='$pwd' where ID='$ID' ");
	}
	$dsql->Close();
	ShowMsg("成功完成 {$dd} 条数据的转换！","javascript:;");
	exit();
}
//旧密码为md5密码
else if($cfg_pwdtype=='md5'){
	if($newtype!='md5'){
		$dsql->Close();
		ShowMsg("你原来的数据类型为MD5类型，系统无法转换你的数据为非MD5类型！","javascript:;");
		exit();
	}
	if($newmd5len > $cfg_md5len){
		$dsql->Close();
		ShowMsg("你原来的MD5密码比你目前指定的短，系统无法转换为更长的密码！","javascript:;");
		exit();
	}
	if($newmd5len == $cfg_md5len){
		$dsql->Close();
		ShowMsg("你原来的密码和你所选的一样，无需转换！","javascript:;");
		exit();
	}
	$dsql->SetQuery("Select ID,pwd From #@__member ");
	$dsql->Execute();
	while($row = $dsql->GetArray()){
		$ID = $row['ID'];
		$pwd = $row['pwd'];
		$pwd = substr($pwd,0,$newmd5len);
		$dsql->ExecuteNoneQuery("Update #@__member set pwd='$pwd' where ID='$ID' ");
	}
	$dsql->Close();
	ShowMsg("成功完成 {$dd} 条数据的转换！","javascript:;");
	exit();
}
//旧密码为md5中间16位
else if($cfg_pwdtype=='md5m16'){
	$dsql->Close();
	ShowMsg("你原来的数据类型为MD5中间取16位的类型，无法转换为其它数据！","javascript:;");
	exit();
}

ClearAllLink();
?>