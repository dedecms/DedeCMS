<?php
require_once(dirname(__FILE__)."/config.php");
if(empty($_POST) && empty($_GET))
{
	ShowMsg("本页面禁止返回!","control.php");
	exit();
}
if(empty($fmdo)) $fmdo = "";
if(empty($dopost)) $dopost = "";
switch($fmdo){
 /*********************
 function A_User()
 *******************/
 case "user":
 /*
 检查用户名是否存在
 function ACheckUser();
 */
 if($dopost=="checkuser")
 {
 	 AjaxHead();
 	 $msg = "";
 	 $userid = trim($userid);
 	 if($userid==""||!TestStringSafe($userid)){
 	 	 $msg = "<font color='red'> 用户名含有非法字符！ </font>";
 	 }else{
 	   $dsql = new DedeSql(false);
 	   $dsql->SetQuery("Select ID From #@__member where userid='$userid'");
 	   $dsql->Execute();
 	   $rowcount = $dsql->GetTotalRow();
 	   $dsql->Close();
 	   if($rowcount>0){ $msg = "用户名 [<font color='red'>$userid</font>]，已经被人使用，请使用其它用户名。"; }
 	   else{ $msg = "用户名 [<font color='red'>$userid</font>]，可以正常使用。"; }
 	 }
 	 echo $msg;
	 exit();
 }
 /*
 新用户注册
 function AUserReg()
 */
 else if($dopost=="regnew")
 {
 	 if($cfg_pp_isopen==1 && $cfg_pp_regurl!=''){
	    header("Location:{$cfg_pp_regurl}");
	    exit();
   }
 	 require_once(dirname(__FILE__)."/reg_new.php");
 	 exit();
 }
 else if($dopost=="regok")
 {
 	 if($cfg_pp_isopen==1 && $cfg_pp_regurl!=''){
	    header("Location:{$cfg_pp_regurl}");
	    exit();
   }
 	 $svali = GetCkVdValue();
   if(strtolower($vdcode)!=$svali || $svali==""){
  	 ShowMsg("验证码错误！","-1");
  	 exit();
   }
 	 $userid = trim($userid);
 	 $pwd = trim($userpwd);
 	 $pwdc = trim($userpwdok);
 	 if(!TestStringSafe($userid)||!TestStringSafe($pwd))
 	 {
 	 	  ShowMsg("你的用户名或密码不合法！","-1");
 	 	  exit();
 	 }
 	 if(strlen($userid)<3||strlen($pwd)<3){
 	 	  ShowMsg("你的用户名或密码小于三位，不允许注册！","-1");
 	 	  exit();
 	 }
 	 if(strlen($userid)>24||strlen($pwd)>24){
 	 	  ShowMsg("你的用户名或密码长度不能超过24位！","-1");
 	 	  exit();
 	 }
 	 if($pwdc!=$pwd){
 	 	 ShowMsg("你两次输入的密码不一致！","-1");
 	 	 exit();
 	 }
 	 $dsql = new DedeSql(false);

 	 //会员的默认金币
 	 $dfrank = $dsql->GetOne("Select money From #@__arcrank where rank='10' ");
 	 if(is_array($dfrank)) $dfmoney = $dfrank['money'];
 	 else $dfmoney = 0;

 	 $dsql->SetQuery("Select ID From #@__member where userid='$userid'");
 	 $dsql->Execute();
 	 $rowcount = $dsql->GetTotalRow();
 	 if($rowcount>0){
 	 	 $dsql->Close();
 	 	 ShowMsg("你指定的用户名已存在，请使用别的用户名！","-1");
 	 	 exit();
   }
	$uname = eregi_replace("['\"\$ \r\n\t;<>\*%\?]", '', $uname);
   $pwd = GetEncodePwd($pwd);
 	 $jointime = time();
 	 $logintime = time();
 	 $joinip = GetIP();
 	 $loginip = GetIP();


 	 //设置可选注册项目的默认值
 	 $dfregs['birthday_y'] = '0000';
 	 $dfregs['birthday_m'] = '00';
 	 $dfregs['birthday_d'] = '00';
 	 $dfregs['birthday'] = '0000-00-00';
 	 $dfregs['weight'] = '0';
 	 $dfregs['height'] = '0';
 	 $dfregs['job'] = '';
 	 $dfregs['province'] = '0';
 	 $dfregs['city'] = '0';
 	 $dfregs['myinfo'] = '';
 	 $dfregs['tel'] = '';
 	 $dfregs['type'] = 0;
 	 $dfregs['oicq'] = '';
 	 $dfregs['homepage'] = '';
 	 $dfregs['address'] = '';
 	 $dfregs['spacename'] = '';
 	 $dfregs['showaddr'] = '0';
 	 foreach($dfregs as $k=>$v){
 	 	 if(!isset($$k)) $$k = $v;
 	 }
$membertype = 10;
if($cfg_member_regsta == 'Y'){
	$membertype = 0;
}
	if($type == 1){
		if(trim($comname) == ''){
			ShowMsg("公司用户必须填写公司名称","-1");
			exit();
		}
		$comname = htmlEncode(trim($comname));
	}else{
		$type = 0;
	}

   if(empty($spacename) && !empty($comname)) $spacename = $comname;
   if(empty($spacename)) $spacename = $uname.'的个人空间';

 	 $birthday = GetAlabNum($birthday_y)."-".GetAlabNum($birthday_m)."-".GetAlabNum($birthday_d);
 	 if($birthday=='0-0-0'){
 	 	 $birthday = '0000-00-00';
 	 }

 	 $height = GetAlabNum($height);

 	 $inQuery = "
 	  INSERT INTO `#@__member` (`userid` , `pwd` , `type` , `uname` ,`sex` , `membertype` , `uptime` , `exptime` ,
 	   `money` , `email` , `jointime` , `joinip` , `logintime` , `loginip` ,
 	    `c1` , `c2` , `c3` , `matt` , `guestbook` , `spaceshow` , `pageshow` , `spacestyle` ,
 	     `spacename` , `spaceimage` , `news` ,`mybb` , `listnum` , `scores` )
    VALUES ('$userid', '$pwd', '$type', '$uname','$sex', '$membertype', '0', '0',
     '0', '$email', '$jointime', '$joinip', '$jointime', '$joinip',
      '0', '0', '0', '0', '0', '0', '0', '',
       '$spacename', '', '', '', '20', '{$cfg_df_score}')
	 ";

	if($dsql->ExecuteNoneQuery($inQuery))
	{
		$id = $dsql->GetLastID();
		if($type == 1)
		{
			$inQuery = "INSERT INTO #@__member_cominfo(id, comname) VALUES ('$id', '$comname');";
			$dsql->ExecuteNoneQuery($inQuery);
		}
		else
		{
			$inQuery = "
	     INSERT INTO `#@__member_perinfo` (`id`, `uname` , `sex` , `birthday` , `weight` ,`height` , `job` , `province` , `city` , `myinfo` ,
	     `tel` , `oicq` , `homepage` , `showaddr` ,`address` , `fullinfo`)
      VALUES ('$id','$uname', '$sex', '$birthday', '$weight','$height', '$job', '$province', '$city', '$myinfo' ,
        '$tel' , '$oicq' , '$homepage' ,'$showaddr','$address','');";
      $dsql->ExecuteNoneQuery($inQuery);
		}
		$dsql->Close();
		$ml = new MemberLogin();
		$rs = $ml->CheckUser($userid,$pwd);
		if($rs==1)
		{
			if($type == 1){
				ShowMsg("注册成功，请您立即填写企业相关资料","mycominfo.php",0,2000);
				exit();
			}
			ShowMsg("注册成功，5秒钟后转向空间管理中心","control.php",0,2000);
			exit();
		}else
		{
			ShowMsg("注册成功，5秒钟后转向登录页面...","login.php",0,2000);
			exit();
		}
	}else
	{
		echo $dsql->GetError();
		$dsql->Close();
		ShowMsg("注册失败，请检查资料是否有误或与管理员联系！","javascript:;");
		exit();
	}
 }
  /*
 更改用户资料
 function AEditUser()
 */
 else if($dopost=="editUserSafe")
 {
 	  if($cfg_pp_isopen==1 && $cfg_pp_editsafeurl!=''){
	    header("Location:{$cfg_pp_editsafeurl}");
	    exit();
    }
 	  CheckRank(0,0);
 	  $svali = GetCkVdValue();
    if(strtolower($vdcode)!=$svali || $svali==""){
  	  ShowMsg("验证码错误！","-1");
  	  exit();
    }
 	  if($oldpwd==""){
 	  	ShowMsg("你没有填写你的旧密码！","-1");
 	  	exit();
 	  }
 	  $pwd = trim($userpwd);
 	  $pwdc = trim($userpwdok);
 	  if($pwd!=""){
 	      if(strlen($pwd)>24){
 	 	       ShowMsg("密码长度不能超过24位！","-1");
 	 	       exit();
 	      }
 	      if(!TestStringSafe($pwd)){
 	 	      ShowMsg("你的新密码含有非法字符！","-1");
 	 	      exit();
 	      }
 	      if($pwdc!=$pwd){
 	 	      ShowMsg("你两次输入的密码不一致！","-1");
 	 	      exit();
 	      }
 	  }else{
 	  	ShowMsg("你没有设置要更改的密码！","-1");
 	 	  exit();
 	  }
 	  $dsql = new DedeSql(false);
 	  $row = $dsql->GetOne("Select pwd From #@__member where ID='".$cfg_ml->M_ID."'");
 	  $oldpwd = GetEncodePwd($oldpwd);
 	  if(!is_array($row)||$row['pwd']!=$oldpwd){
 	     $dsql->Close();
 	     ShowMsg("你输入的旧密码错误！","-1");
 	 	   exit();
 	  }
 	  $pwd = GetEncodePwd($pwd);
 	  $query = "update #@__member set pwd = '$pwd' where ID='".$cfg_ml->M_ID."'";
 	  $dsql->ExecuteNoneQuery($query);
 	  ShowMsg("成功更改你的密码！","-1");
 	 	exit();
 }
 else if($dopost=="editUser")
 {
 	  CheckRank(0,0);
 	  $svali = GetCkVdValue();
    if(strtolower($vdcode)!=$svali || $svali==""){
  	  ShowMsg("验证码错误！","-1");
  	  exit();
    }
 	  $query1 = "
 	  update #@__member set
 	  email = '$email',uname = '$uname'
 	  where ID='".$cfg_ml->M_ID."'
 	  ";
 	  $query2 = "
 	  update #@__member_perinfo set
    uname = '$uname',
    sex = '$sex',
    birthday = '$birthday',
    weight = '$weight',
    height = '$height',
    job = '$job',
    province = '$province',
    city = '$city',
    myinfo = '$myinfo',
    fullinfo = '$fullinfo',
    showaddr = '$showaddr',
    address = '$address',
    oicq = '$oicq',
    tel = '$tel',
    homepage = '$homepage'
 	  where ID='".$cfg_ml->M_ID."'
 	  ";
 	  $dsql = new DedeSql(false);
 	  $dsql->ExecuteNoneQuery($query1);
 	  $dsql->ExecuteNoneQuery($query2);
 	  $dsql->Close();
 	  ShowMsg("成功更新你的个人资料！","edit_info.php");
 	 	exit();
 }
  /*
 更改个人空间资料
 function EditSpace()
 */
 else if($dopost=="editSpace")
 {
 	  CheckRank(0,0);
 	  $svali = GetCkVdValue();
    if(strtolower($vdcode)!=$svali || $svali==""){
  	  ShowMsg("验证码错误！","-1");
  	  exit();
    }
    require_once("./inc/inc_archives_functions.php");
    $title = "空间形象";
    $spaceimage = GetUpImage('spaceimage',true,true,150,112,'myface');
    if($spaceimage=="" && $oldimg!="" && $oldimg!="img/pview.gif"){
    	 if(file_exists($cfg_basedir.$oldimg)){
    	 	  $spaceimage = $oldimg;
    	 }
    }
 	  $dsql = new DedeSql(false);
 	  $news = addslashes(cn_substr(stripslashes($news),1024));
 	  $news = eregi_replace("<(iframe|script|javascript)","",$news);
 	  $spacename = ereg_replace("[><]","",$spacename);
 	  $mybb = addslashes(html2text(stripslashes($mybb)));
 	  $upquery = "Update #@__member set
 	      spacename='$spacename',spaceimage='$spaceimage',news='$news',mybb='$mybb'
 	      where ID='".$cfg_ml->M_ID."';
 	  ";
 	  $ok = $dsql->ExecuteNoneQuery($upquery);
 	  if($ok){
 	  	$dsql->Close();
 	  	ShowMsg("成功更新你的个人空间介绍！","space_info.php?".time().mt_rand(100,900));
 	  	exit();
 	  }else{
 	  	$dsql->Close();
 	    ShowMsg("更新资料失败！","space_info.php?".time().mt_rand(100,900));
 	 	  exit();
 	  }
 }
 //
 break;
 /*********************
 function B_Login()
 *******************/
 case "login":
 //
 /*
 用户登录
 function BUserLogin()
 */
 if($dopost=="login")
 {
 	 if($cfg_pp_isopen==1 && $cfg_pp_loginurl!=''){
	    header("Location:{$cfg_pp_loginurl}");
	    exit();
   }
 	 $svali = GetCkVdValue();
   if(strtolower($vdcode)!=$svali || $svali==""){
  	 ShowMsg("验证码错误！","-1");
  	 exit();
   }
   if(!TestStringSafe($userid)||!TestStringSafe($pwd))
   {
   	 ShowMsg("用户名或密码不合法！","-1",0,2000);
  	 exit();
   }
   if($userid==""||$pwd==""){
   	 ShowMsg("用户名或密码不能为空！","-1",0,2000);
  	 exit();
   }
   //检查帐号
   $rs = $cfg_ml->CheckUser($userid,GetEncodePwd($pwd));
   if($rs==0) {
   	 ShowMsg("用户名不存在！","-1",0,2000);
  	 exit();
   }
   else if($rs==-1){
   	 ShowMsg("密码错误！","-1",0,2000);
  	 exit();
   }
   else{
   	 $dsql->SetQuery("update #@__member set logintime='".time()."',loginip='".GetIP()."' where ID='".$cfg_ml->M_ID."'");
   	 $dsql->ExecuteNoneQuery();
   	 $dsql->Close();
   	 if(empty($gourl)||eregi("action|_do",$gourl)){
   	 	  ShowMsg("成功登录，5秒钟后转向系统管理中心...","control.php",0,2000);
   	 }else{
   	 	  ShowMsg("成功登录，转到进入页面...",$gourl,0,2000);
   	 }
  	 exit();
   }
 }
 /*
 退出登录
 function BUserExit()
 */
 else if($dopost=="exit")
 {
 	 $cfg_ml->ExitCookie();
 	 if($cfg_pp_isopen==1 && $cfg_pp_exiturl!=''){
	    echo "<script> location='{$cfg_pp_exiturl}'; </script>";
	    exit();
   }
 	 $cfg_ml->ExitCookie();
 	 if(empty($cfg_mb_exiturl)) $cfg_mb_exiturl = $cfg_indexurl;
 	 if(empty($cfg_mb_exiturl)) $cfg_mb_exiturl = '/';
 	 ShowMsg("成功退出登录！",$cfg_mb_exiturl,0,2000);
   exit();
 }
/*
 获取密码
 function BUserGetPwd()
*/
 else if($dopost=="getpwd")
 {
 	 if($cfg_pwdtype=='md5'){
 	 	 ShowMsg("系统的密码被设置为单向加密，无法取回，请与管理员联系。","javascript:;");
 	 	 exit();
 	 }
 	 $svali = GetCkVdValue();
   if(strtolower($vdcode)!=$svali || $svali==""){
  	 ShowMsg("验证码错误！","-1");
  	 exit();
   }
   if(!ereg("(.*)@(.*)\.(.*)",$email)||!TestStringSafe($email)){
   	 ShowMsg("邮箱地址格式不正确！","-1");
  	 exit();
   }
   $dsql = new DedeSql(false);
   $row = $dsql->GetOne("Select userid,pwd,uname,email From #@__member where email='$email'");
   if(!is_array($row)){
     $dsql->Close();
     ShowMsg("系统找不到此邮箱地址！","-1");
  	 exit();
   }
   $dsql->Close();
	 $mailtitle = "你在".$cfg_webname."的用户名和密码";
	 $mailbody = "\r\n用户名：'".$row['userid']."'  密码：'".$row['pwd']."'\r\n\r\n，
	 $cfg_powerby";
	 $headers = "From: ".$cfg_adminemail."\r\nReply-To: $cfg_adminemail";
   sendmail($email, $mailtitle, $mailbody, $headers);
   $gurl = explode("@",$email);
   ShowMsg("成功发出你的用户名和密码，请注意查收！","login.php");
   exit();
 }
 //
 break;
}

?>