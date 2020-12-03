<?php 
/*-------------------------------------------
通行站反向整合接口远程调用文件，会员专用版

本接口不需要导入第三方程序的用户和密码信息，系统会自动判断和生成

作者： IT柏拉图  最后修改日期 2007-12-07
//**********************************************************************
本文件仅作为网关供远程调用
请使用或参考 pp_dederemote_interface_new.php 提供的接口函数进行整合编程
-----------------------------------------*/
require_once(dirname(__FILE__)."/../../include/config_base.php");
header("Content-Type: text/html; charset=utf-8");

//使用通行证的用户ID的区别符号，如果原DEDE系统无用户数据的不需要理会，否则可以加 @pp 之类的识别
$ppName = "";

if($cfg_pp_isopen = 0){
	echo "系统没开启通行证功能，禁止远程调用！";
	exit();
}

$cfg_ndsql = 0;

if(empty($rmdata)){
	echo "没接收到任何远程数据！";
	exit();
}

$keys = Array('userid','signstr','action');

foreach($keys as $v) $$v = '';

//解码GET字符串
$rmdata = base64_decode($rmdata);
$datas = explode('&',$rmdata);
foreach($datas as $ky){
	$nkys = explode('=',$ky);
	if(in_array($nkys[0],$keys) && isset($nkys[1])) ${$nkys[0]} = urldecode($nkys[1]);
}

$ntime = time();

if($action!='exit'){
  //验证证书
  if($userid==''||!TestStringSafe($userid)){
	  echo "用户ID为空或存在非法字符串！".$oldrmdata;
	  exit();
  }
  if(strlen($userid)>24){
	  echo "用户ID长度不能超过24位！";
	  exit();
  }
  $testSign = substr(md5($userid.$cfg_cookie_encode),0,24);
  if($testSign!=$signstr){
	  echo "证书验证失败！";
	  exit();
  }
}

//注解里的function仅方便UltraEdit索引，并无其它意义
/*--------------------------------
会员注册
function __UserReg()
---------------------------------*/
if($action=='reg'){
	Z_OpenSql();
	$userpwd = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(1000,9999).chr(mt_rand(ord('A'),ord('Z')));
	$userpwd = GetEncodePwd($userpwd);
	$loginip = Z_GetIP();
	$ppuserid = $userid.$ppName;
   
   $uname = $ppuserid;
   $inQuery1 = "
 	 INSERT INTO `#@__member` (`userid` , `pwd` , `type` , `uname` , `membertype` , `uptime` , `exptime` ,
 	   `money` , `email` , `jointime` , `joinip` , `logintime` , `loginip` ,
 	    `c1` , `c2` , `c3` , `matt` , `guestbook` , `spaceshow` , `pageshow` , `spacestyle` ,
 	     `spacename` , `spaceimage` , `news` , `mybb` , `listnum` , `scores` ) 
    VALUES ('$ppuserid', '$userpwd', '0', '$uname', '10', '0', '0',
     '0', '', '$ntime', '$loginip', '$ntime', '$loginip',
      '0', '0', '0', '0', '0', '0', '0', '',
       '', '', '', '', '20', '1000');
	 ";
   
   $cfg_ndsql->ExecuteNoneQuery($inQuery);
   
   $id = $cfg_ndsql->GetLastID();
   if($id>0){
      $inQuery2 = "
	      INSERT INTO `#@__member_perinfo` (`id`, `uname` , `sex` , `birthday` , `weight` ,`height` , `job` , `province` , `city` , `myinfo` , 
	     `tel` , `oicq` , `homepage` , `showaddr` ,`address` , `fullinfo`) 
       VALUES ('$id','$uname', '', '0000-00-00', '0','0', '0', '0', '0', '0' ,
        '0' , '0' , '0' ,'0','0','');
     ";	
     $cfg_ndsql->ExecuteNoneQuery($inQuery);
   }
   
   $row = $cfg_ndsql->GetOne("Select ID From #@__member where userid like '{$userid}$ppName' ");
	 $ID = $row['ID'];
	 Z_CloseSql();
	 echo 'OK!'.$ID;
	 exit();
}
/*--------------------------------
会员登录
function __UserLogin()
---------------------------------*/
else if($action=='login'){
	Z_OpenSql();
	$row = $cfg_ndsql->GetOne("Select ID,pwd From #@__member where userid like '{$userid}$ppName' ");
	$loginip = Z_GetIP();
	if(!is_array($row)){
		 $userpwd = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(1000,9999).chr(mt_rand(ord('A'),ord('Z')));
	   $userpwd = GetEncodePwd($userpwd);
	   $ppuserid = $userid.$ppName;
		 
		 
		 $uname = $ppuserid;
   $inQuery1 = "
 	 INSERT INTO `#@__member` (`userid` , `pwd` , `type` , `uname` , `membertype` , `uptime` , `exptime` ,
 	   `money` , `email` , `jointime` , `joinip` , `logintime` , `loginip` ,
 	    `c1` , `c2` , `c3` , `matt` , `guestbook` , `spaceshow` , `pageshow` , `spacestyle` ,
 	     `spacename` , `spaceimage` , `news` , `mybb` , `listnum` , `scores` ) 
    VALUES ('$ppuserid', '$userpwd', '0', '$uname', '10', '0', '0',
     '0', '', '$ntime', '$loginip', '$ntime', '$loginip',
      '0', '0', '0', '0', '0', '0', '0', '',
       '', '', '', '', '20', '1000');
	 ";
   
   $cfg_ndsql->ExecuteNoneQuery($inQuery);
   
   $id = $cfg_ndsql->GetLastID();
   if($id>0){
      $inQuery2 = "
	      INSERT INTO `#@__member_perinfo` (`id`, `uname` , `sex` , `birthday` , `weight` ,`height` , `job` , `province` , `city` , `myinfo` , 
	     `tel` , `oicq` , `homepage` , `showaddr` ,`address` , `fullinfo`) 
       VALUES ('$id','$uname', '', '0000-00-00', '0','0', '0', '0', '0', '0' ,
        '0' , '0' , '0' ,'0','0','');
     ";	
     $cfg_ndsql->ExecuteNoneQuery($inQuery);
   }
     
     
     $row = $cfg_ndsql->GetOne("Select ID,pwd From #@__member where userid like '$userid' ");
	}
	$ID = $row['ID'];
	$cfg_ndsql->ExecuteNoneQuery("update #@__member set logintime='$ntime',loginip='$loginip' where ID='$ID' ");
	Z_CloseSql();
	echo 'OK!'.$ID;
	exit();
}
/*--------------------------------
退出系统
function __UserExit()
---------------------------------*/
else if($action=='exit'){
	echo 'OK!0';
	exit();
}
/*--------------------------------
无法识别远程动作
function __ActionError()
---------------------------------*/
else{
	echo "无法识别你的动作！";
	exit();
}

//其它功能如函数
function Z_OpenSql(){
	global $cfg_ndsql;
	if(!$cfg_ndsql) $cfg_ndsql = new DedeSql(false);
}
function Z_CloseSql(){
	global $cfg_ndsql;
	if($cfg_ndsql) $cfg_ndsql->Close();
}
function Z_GetIP(){
	if(!empty($_SERVER["HTTP_CLIENT_IP"])) $cip = $_SERVER["HTTP_CLIENT_IP"];
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else if(!empty($_SERVER["REMOTE_ADDR"])) $cip = $_SERVER["REMOTE_ADDR"];
	else $cip = "无法获取！";
	return $cip;
}
?>