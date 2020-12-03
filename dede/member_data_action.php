<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Data');
if(empty($action)) $action = '';

/*-------------------------------
//列出数据库里的表
function __gettables()
--------------------------------*/
if($action=='gettables'){
	AjaxHead();
	$qbutton = "<input type='button' name='seldbtable' value='选择数据表' class='inputbut' onclick='SelectedTable()'>\r\n";
	if($dbptype==2 && $dbname==""){
		echo "<font color='red'>你没指定数据库名称！</font><br>";
		echo $qbutton;
		exit();
	}
	if($dbptype==3 
	&& (empty($dbhost) || empty($dbname) || empty($dbuser)))
	{
		echo "<font color='red'>你选择了“指定新的登录信息”，必须填写完所有数据库登录选项！</font><br>";
		echo $qbutton;
		exit();
	}
	if($dbptype==1){
		$dsql = new DedeSql(false);
	}
	else if($dbptype==2){
		 $dsql = new DedeSql(false,false);
		 $dsql->SetSource($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd,$dbname,'');
		 $dsql->Open(false);
	}
	else if($dbptype==3){
		$dsql = new DedeSql(false,false);
		$dsql->SetSource($dbhost,$dbuser,$dbpwd,$dbname,'');
		$dsql->Open(false);
	}
	if(!$dsql->linkID){
		echo "<font color='red'>连接数据库失败！</font><br>";
		echo $qbutton;
		exit();
	}
	$dsql->SetQuery("Show Tables");
  $dsql->Execute('t');
  if($dsql->GetError()!=""){
  	echo "<font color='red'>找不到你所指定的数据库！ $dbname</font><br>";
		echo $qbutton;
  }
  echo "<select name='exptable' id='exptable' size='10' style='width:60%' onchange='ShowFields()'>\r\n";
  while($row = $dsql->GetArray('t')){
	  echo "<option value='{$row[0]}'>{$row[0]}</option>\r\n";
  }
  echo "</select>\r\n";
	$dsql->Close();
	exit();
}
/*-------------------------------
//列出数据库表里的字段
function __getfields()
--------------------------------*/
if($action=='getfields'){
	AjaxHead();
	if($dbptype==1){
		$dsql = new DedeSql(false);
	}
	else if($dbptype==2){
		 $dsql = new DedeSql(false,false);
		 $dsql->SetSource($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd,$dbname,'');
		 $dsql->Open(false);
	}
	else if($dbptype==3){
		$dsql = new DedeSql(false,false);
		$dsql->SetSource($dbhost,$dbuser,$dbpwd,$dbname,'');
		$dsql->Open(false);
	}
	if(!$dsql->linkID){
		echo "<font color='red'>连接数据源的数据库失败！</font><br>";
		echo $qbutton;
		exit();
	}
	$dsql->GetTableFields($exptable);
	echo "<div style='border:1px solid #ababab;background-color:#FEFFF0;margin-top:6px;padding:3px;line-height:160%'>";
	echo "表(".$exptable.")含有的字段：<br>";
	while($row = $dsql->GetFieldObject()){
		echo $row->name." ";
	}
	echo "</div>";
	$dsql->Close();
	exit();
}
/*-------------------------------
//保存用户设置，清空会员数据
function __saveSetting()
--------------------------------*/
else if($action=='savesetting'){
	if(empty($validate)) $validate=="";
  else $validate = strtolower($validate);
  $svali = GetCkVdValue();
  if($validate=="" || $validate!=$svali){
	  ShowMsg("安全确认码不正确!","javascript:;");
	  exit();
  }
  if(empty($userfield) || empty($pwdfield)){
  	ShowMsg("用户名和密码字段必须指定！","javascript:;");
  	exit();
  }
  $configfile = dirname(__FILE__)."/../include/config_hand.php";
  $configfile_bak = dirname(__FILE__)."/../include/config_hand_bak.php";
  $dsql = new DedeSql(false);
  $dsql->ExecuteNoneQuery("Update #@__sysconfig set value='$oldtype' where varname='cfg_pwdtype' ");
  $dsql->ExecuteNoneQuery("Update #@__sysconfig set value='$oldmd5len' where varname='cfg_md5len' ");
  $dsql->ExecuteNoneQuery("Update #@__sysconfig set value='$oldsign' where varname='cfg_ddsign' ");
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
  $dsql->ExecuteNoneQuery("Delete From #@__member ");
  $dsql->ExecuteNoneQuery("Delete From #@__member_arctype ");
  $dsql->ExecuteNoneQuery("Delete From #@__member_flink ");
  $dsql->ExecuteNoneQuery("Delete From #@__member_guestbook ");
  $dsql->ExecuteNoneQuery("Delete From #@__memberstow ");
  $dsql->Close();
  $nurl = GetCurUrl();
  $nurl = str_replace("savesetting","converdata",$nurl);
  ShowMsg("完成数据保存，并清空本系统的会员数据，现在开始导入数据！",$nurl);
  exit();
}
/*-------------------------------
//保存用户设置，转换会员数据
function __ConverData()
--------------------------------*/
else if($action=='converdata'){
	set_time_limit(0);
	if(empty($tgmd5len)) $tgmd5len = 32;
	if($tgmd5len < $cfg_md5len && $tgtype=='md5'){
		ShowMsg("无法从短的MD5密码转换为更长的密码！","javascript:;");
		exit();
	}
	$oldchar = $cfg_db_language;
	$cfg_db_language = $dbchar;
	if($dbptype==1){
		$dsql = new DedeSql(false);
	}
	else if($dbptype==2){
		 $dsql = new DedeSql(false,false);
		 $dsql->SetSource($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd,$dbname,'');
		 $dsql->Open(false);
	}
	else if($dbptype==3){
		$dsql = new DedeSql(false,false);
		$dsql->SetSource($dbhost,$dbuser,$dbpwd,$dbname,'');
		$dsql->Open(false);
	}
	if(!$dsql->linkID){
		ShowMsg("连接数据源的数据库失败！","javascript:;");
		exit();
	}
	$fieldsql = '';
	$fieldsql = "$userfield,$pwdfield";
	if($emailfield!='') $fieldsql .= ",$emailfield";
	if($unamefield!='') $fieldsql .= ",$unamefield";
	if($sexfield!='') $fieldsql .= ",$sexfield";
	$dsql->SetQuery("Select $fieldsql From $exptable ");
	$dsql->Execute();
	
	$cfg_db_language = $oldchar;
	$dsql2 = new DedeSql(false);
	
	$c = 0;
	
	while($row = $dsql->GetArray()){
		$userid = addslashes($row[$userfield]);
		if($tgtype=='none') $pwd = GetEncodePwd($row[$pwdfield]);
		else if($tgtype=='md5'){
			if($cfg_md5len < $tgmd5len) $pwd = substr($row[$pwdfield],0,$cfg_md5len);
			else $pwd = $row[$pwdfield];
		}else if($tgtype=='md5m16'){
			$pwd = $row[$pwdfield];
		}
		$pwd = addslashes($pwd);
		
		if(empty($unamefield)) $uname = $userid;
		else $uname = addslashes($row[$unamefield]);
		
		if(empty($emailfield)) $email = '';
		else $email = addslashes($row[$emailfield]);
		
		if(empty($sexfield)) $sex = '';
		else{
			$sex = $row[$sexfield];
			if($sex==$sexman) $sex = '男';
			else if($sex==$sexwoman) $sex = '女';
			else $sex = '';
		}
		
		$ntime = time();
		$inQuery = "
 	 INSERT INTO #@__member(userid,pwd,uname,sex,birthday,membertype,money,
 	 weight,height,job,province,city,myinfo,tel,oicq,email,homepage,
 	 jointime,joinip,logintime,loginip,showaddr,address) 
   VALUES ('$userid','$pwd','$uname','$sex','0000-00-00','10','0',
   '0','0','','0','0','','','','$email','','$ntime','$loginip','$ntime','','0','');";
   
   $rs = $dsql2->ExecuteNoneQuery($inQuery);
   if($rs) $c++;
   
	}
	$dsql->Close();
	$dsql2->Close();
	ShowMsg("成功导入 ".$c." 条数据！","javascript:;");
	exit();
}

ClearAllLink();
?>