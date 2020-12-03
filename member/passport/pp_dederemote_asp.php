<?php 
/*-------------------------------------------
通行站反向整合接口远程调用文件
（应用于其它系统整合Dedecms--即是使用其它系统作为登录、注册主入口）
作者： IT柏拉图  最后修改日期 2007-12-07
本文件用于整合ASP类的系统（字串没有使用base64加密）
//**********************************************************************
本文件仅作为网关供远程调用
请使用或参考 pp_dederemote_interface.asp 提供的接口函数进行整合编程
-----------------------------------------*/
require_once(dirname(__FILE__)."/../../include/config_base.php");
header("Content-Type: text/html; charset=utf-8");

if($cfg_pp_isopen = 0){
	echo "系统没开启通行证功能，禁止远程调用！";
	exit();
}

$cfg_ndsql = 0;

if(empty($rmdata)){
	echo "没接收到任何远程数据！";
	exit();
}

$keys = Array('userid','userpwd','signstr','newuserpwd','action','email','sex','uname','exptime');

foreach($keys as $v) $$v = '';

//解码GET字符串
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
	if($userpwd==''||!TestStringSafe($userpwd)){
	  echo "用户密码为空或存在非法字符串！";
	  exit();
  }
  if(strlen($userpwd)>24){
	  echo "用户密码长度不能超过24位！";
	  exit();
  }
	Z_OpenSql();
	$row = $cfg_ndsql->GetOne("Select ID,pwd From #@__member where userid like '$userid' ");
	//如果已经存在用户名，检测密码是否正确，如果密码正确，则返回登录信息，否则不允许注册
	if(is_array($row)){
		 $userpwd = GetEncodePwd($userpwd);
	   $ID = $row['ID'];
	   $pwd = $row['pwd'];
	   Z_CloseSql();
	   if($userpwd != $pwd){
		    echo "用户ID：{$userid} 已存在，请使用其它用户名！";
		    exit();
	   }else{
	      $backString = $ID;
	      echo 'OK!'.$backString;
	      exit();
	   }
	}
	$userpwd = GetEncodePwd($userpwd);
	$loginip = Z_GetIP();
	
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
   
   
   $row = $cfg_ndsql->GetOne("Select ID From #@__member where userid like '$userid' ");
	 if(!is_array($row)){
		 Z_CloseSql();
		 echo "系统出错，无法完成注册，请联系管理员！";
		 exit();
	 }
	 $ID = $row['ID'];
	 Z_CloseSql();
	 $backString = $ID;
	 echo 'OK!'.$backString;
	 exit();
}
/*--------------------------------
检查是否存在用户名
function __UserTest()
---------------------------------*/
else if($action=='test'){
	Z_OpenSql();
	$row = $cfg_ndsql->GetOne("Select count(ID) as dd From #@__member where userid like '$userid' ");
	if(!is_array($row)){
		 Z_CloseSql();
		 echo "系统出错，无法完成注册，请联系管理员！";
		 exit();
	}
	if($row['dd']>0){
		 Z_CloseSql();
		 echo "用户ID已存在！";
		 exit();
	}
	 Z_CloseSql();
	 echo 'OK!';
	 exit();
}
/*--------------------------------
会员登录
function __UserLogin()
---------------------------------*/
else if($action=='login'){
	Z_OpenSql();
	$row = $cfg_ndsql->GetOne("Select ID,pwd From #@__member where userid like '$userid' ");
	if(!is_array($row)){
		 Z_CloseSql();
		 echo "用户名不存在，无法登录本系统！";
		 exit();
	}
	if(strlen($userpwd)>24){
	  echo "用户密码长度不能超过24位！";
	  exit();
  }
	$userpwd = GetEncodePwd($userpwd);
	$ID = $row['ID'];
	$pwd = $row['pwd'];
	if($userpwd != $pwd){
		 Z_CloseSql();
		 echo "密码错误！";
		 exit();
	}
	$loginip = Z_GetIP();
	$cfg_ndsql->ExecuteNoneQuery("update #@__member set logintime='$ntime',loginip='$loginip' where ID='$ID' ");
	Z_CloseSql();
	$backString = $ID;
	echo 'OK!'.$backString;
	exit();
}
/*--------------------------------
更改密码
function __UserEdit()
---------------------------------*/
else if($action=='edit'){
	if($newuserpwd==''||!TestStringSafe($newuserpwd)){
	   echo "用户密码为空或存在非法字符串！";
	   exit();
  }
  if(strlen($newuserpwd)>24){
	  echo "用户密码长度不能超过24位！";
	  exit();
  }
  $newuserpwd = GetEncodePwd($newuserpwd);
	Z_OpenSql();
	$cfg_ndsql->ExecuteNoneQuery("Update #@__member set pwd='$newuserpwd' where userid like '$userid' ");
	Z_CloseSql();
	echo 'OK!';
}
/*--------------------------------
退出系统
function __UserExit()
---------------------------------*/
else if($action=='exit'){
	$backString = "0";
	echo 'OK!'.$backString;
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