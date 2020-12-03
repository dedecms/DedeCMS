<?
require_once(dirname(__FILE__)."/config.php");
if(empty($fmdo)) $fmdo = "";
if(empty($dopost)) $dopost = "";
if(empty($_POST) && empty($_GET))
{
	ShowMsg("本页面禁止返回!","index.php");
	exit();
}
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
 	 $msg = "";
 	 $userid = trim($userid);
 	 if($userid==""||ereg("[ '\"\*\?\%]","",$userid)){
 	 	 $msg = "你的用户名为空，或者含有 ['],[\"],[*],[?],[%],[空格] 这类非法字符！";
 	 }
 	 else
 	 {
 	   $dsql = new DedeSql(false);
 	   $dsql->SetQuery("Select ID From #@__member where userid='$userid'");
 	   $dsql->Execute();
 	   $rowcount = $dsql->GetTotalRow();
 	   $dsql->Close();
 	   if($rowcount>0){ $msg = "　　你选择的用户名：[<font color='red'>$userid</font>] ，已经被人使用，请使用其它用户名。"; }
 	   else{ $msg = "　　你选择的用户名：[<font color='red'>$userid</font>] ，可以正常使用，欢迎注册。"; }
 	 }
 	 $htmlhead  = "<html>\r\n<head>\r\n<title>提示信息</title>\r\n";
	 $htmlhead .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\" />\r\n";
	 $htmlhead .= "</head>\r\n<body leftmargin='8' topmargin='8' background='img/dedebg.gif' bgcolor='#D0E8C8' style='font-size:10pt;line-height:150%'>";
	 $htmlfoot  = "</body>\r\n</html>\r\n";
	 echo $htmlhead.$msg.$htmlfoot;
	 exit();
 }
 /*
 新用户注册
 function AUserReg()
 */
 else if($dopost=="regnew")
 {
 	 require_once(dirname(__FILE__)."/reg_new.php");
 	 exit();
 }
 else if($dopost=="regok")
 {
 	 session_start();
 	 if( empty($_SESSION["s_validate"]) ) $svali = "";
   else $svali = $_SESSION["s_validate"];
   if(strtolower($vdcode)!=$svali && $svali!=""){
  	 ShowMsg("验证码错误！","-1");
  	 exit();
   }
 	 $userid = trim($userid);
 	 $pwd = trim($userpwd);
 	 $pwdc = trim($userpwdok);
 	 if(ereg("[ '\"\*\?\%]","",$userid)||ereg("[ '\"\*\?\%]","",$pwd)){
 	 	  ShowMsg("你的用户名为空，或者含有 ['],[\"],[*],[?],[%],[空格] 这类非法字符！","-1");
 	 	  exit();
 	 }
 	 if($pwdc!=$pwd){
 	 	 ShowMsg("你两次输入的密码不一致！","-1");
 	 	 exit();
 	 }
 	 $dsql = new DedeSql(false);
 	 $dsql->SetQuery("Select ID From #@__member where userid='$userid'");
 	 $dsql->Execute();
 	 $rowcount = $dsql->GetTotalRow();
 	 if($rowcount>0){
 	 	 $dsql->Close();
 	 	 ShowMsg("你指定的用户名已存在，请使用别的用户名！","-1");
 	 	 exit();
   }
   $uname = ereg_replace("[ '\"\*\?\%]","",$uname);
   if($uname==""){
   	 ShowMsg("用户昵称有非法字符！","-1");
 	 	 exit();
   }
 	 $jointime = time();
 	 $logintime = time();
 	 $joinip = GetIP();
 	 $loginip = GetIP();
 	 $birthday = GetAlabNum($birthday_y)."-".GetAlabNum($birthday_m)."-".GetAlabNum($birthday_d);
 	 $height = GetAlabNum($height);
 	 $inQuery = "
 	 INSERT INTO #@__member(userid,pwd,uname,sex,birthday,membertype,uptype,money,weight,height,job,province,city,myinfo,mybb,tel,oicq,email,homepage,jointime,joinip,logintime,loginip) 
   VALUES ('$userid','$pwd','$uname','$sex','$birthday','0','0','0','$weight','$height','$job','$province','$city','$myinfo','$mybb','$tel','$oicq','$email','$homepage','$jointime','$joinip','$logintime','$loginip');
 	 ";
 	 $dsql->SetQuery($inQuery);
 	 if($dsql->ExecuteNoneQuery())
 	 {
 	 	  $dsql->Close();
 	 	  $ml = new MemberLogin();
 	 	  $rs = $ml->CheckUser($userid,$pwd);
 	 	  if($rs==1){
 	 	  	ShowMsg("注册成功，5秒钟后转向系统主页...","index.php",0,2000);
 	 	    exit();
 	 	  }
 	 	  else{
 	 	  	ShowMsg("注册成功，5秒钟后转向登录页面...","login.php",0,2000);
 	 	    exit();
 	 	  }
 	 }
 	 else
 	 {
 	 	 $dsql->Close();
 	 	 ShowMsg("注册失败，请检查资料是否有误或与管理员联系！","-1");
 	 	 exit();
 	 }
 }
 /*
 申请升级
 function AUserUpRank();
 */
 else if($dopost=="uprank")
 {
 	 CheckRank(0,0);
 	 if(empty($uptype))
 	 {
 	 	 ShowMsg("数据无效！","-1");
 	 	 exit();
 	 }
 	 $uptype = GetAlabNum($uptype);
 	 if($uptype < $cfg_ml->M_Type)
 	 {
 	 	 ShowMsg("类型不对，你的级别比你目前申请的级别还要高！","-1");
 	 	 exit();
 	 }
 	 $dsql = new DedeSql();
 	 $dsql->SetQuery("update #@__member set uptype='$uptype' where ID='".$cfg_ml->M_ID."' ");
 	 $dsql->Execute();
 	 $dsql->Close();
 	 ShowMsg("成功申请升级，请等待管理员开通！","index.php?".time());
 	 exit();
 }
 /*
 增加金币
 function AddMoney();
 */
 else if($dopost=="addmoney")
 {
 	 CheckRank(0,0);
 	 session_start();
 	 if( empty($_SESSION["s_validate"]) ) $svali = "";
   else $svali = $_SESSION["s_validate"];
   if(strtolower($vdcode)!=$svali && $svali!=""){
  	 ShowMsg("验证码错误！","-1");
  	 exit();
   }
 	 if(empty($money))
 	 {
 	 	 ShowMsg("你没指定要申请多少金币！","-1");
 	 	 exit();
 	 }
 	 $dsql = new DedeSql();
 	 $dsql->SetQuery("update #@__member set upmoney='$money' where ID='".$cfg_ml->M_ID."'");
 	 $dsql->Execute();
 	 $dsql->Close();
 	 ShowMsg("成功提交你的申请！","index.php?".time());
 	 exit();
 }
  /*
 更改用户资料
 function AEditUser()
 */
 else if($dopost=="editUser")
 {
 	  session_start();
 	  CheckRank(0,0);
 	  if( empty($_SESSION["s_validate"]) ) $svali = "";
    else $svali = $_SESSION["s_validate"];
    if(strtolower($vdcode)!=$svali && $svali!=""){
  	  ShowMsg("验证码错误！","-1");
  	  exit();
    }
 	  if($province==0) $province = $oldprovince;
 	  if($city==0) $city = $oldcity;
 	  $oldpwd = ereg_replace("[ '\"\*\?\%]","",$oldpwd);
 	  if($oldpwd==""){
 	  	ShowMsg("你没有填写你的旧密码！","-1");
 	  	exit();
 	  }
 	  $pwd = trim($userpwd);
 	  $pwdc = trim($userpwdok);
 	  if($pwd!="")
 	  {
 	    if(ereg("[ '\"\*\?\%]","",$pwd)){
 	 	    ShowMsg("你的密码含有 ['],[\"],[*],[?],[%],[空格] 这类非法字符！","-1");
 	 	    exit();
 	    }
 	    if($pwdc!=$pwd){
 	 	    ShowMsg("你两次输入的密码不一致！","-1");
 	 	    exit();
 	    }
 	  }
 	  else{
 	  	$pwd = $oldpwd;
 	  }
 	  $dsql = new DedeSql(false);
 	  $row = $dsql->GetOne("Select pwd From #@__member where ID='".$cfg_ml->M_ID."'");
 	  if(!is_array($row)||$row['pwd']!=$oldpwd)
 	  {
 	     $dsql->Close();
 	     ShowMsg("你输入的旧密码错误！","-1");
 	 	   exit();
 	  }
 	  $query = "
 	  update #@__member set 
 	  pwd='$pwd',
 	  email = '$email',
    uname = '$uname',
    sex = '$sex',
    birthday = '$birthday',
    weight = '$weight',
    height = '$height',
    job = '$job',
    province = '$province',
    city = '$city',
    myinfo = '$myinfo',
    mybb = '$mybb',
    oicq = '$oicq',
    tel = '$tel',
    homepage = '$homepage'
 	  where ID='".$cfg_ml->M_ID."'
 	  ";
 	  $dsql->SetQuery($query);
 	  if(!$dsql->ExecuteNoneQuery())
 	  {
 	  	 $dsql->Close();
 	     ShowMsg("更改资料出错，请检查输入是否合法！","-1");
 	 	   exit();
 	  }
 	  else{
 	    $dsql->Close();
 	    ShowMsg("成功更新你的个人资料！","index.php");
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
 	 session_start();
 	 if( empty($_SESSION["s_validate"]) ) $svali = "";
   else $svali = $_SESSION["s_validate"];
   if(strtolower($vdcode)!=$svali && $svali!=""){
  	 ShowMsg("验证码错误！","-1");
  	 exit();
   }
   if(ereg("[ '\"\*\?\%]","",$userid)||ereg("[ '\"\*\?\%]","",$pwd))
   {
   	 ShowMsg("用户名或密码不合法！","-1",0,2000);
  	 exit();
   }
   if($userid==""||$pwd==""){
   	 ShowMsg("用户名或密码不能为空！","-1",0,2000);
  	 exit();
   }
   //检查帐号
   $rs = $cfg_ml->CheckUser($userid,$pwd);
   if($rs==0) {
   	 ShowMsg("用户名不存在！","-1",0,2000);
  	 exit();
   }
   else if($rs==-1){
   	 ShowMsg("密码错误！","-1",0,2000);
  	 exit();
   }
   else{
   	 $dsql = new DedeSql(false);
   	 $dsql->SetQuery("update #@__member set logintime='".time()."',loginip='".GetIP()."' where ID='".$cfg_ml->M_ID."'");
   	 $dsql->ExecuteNoneQuery();
   	 $dsql->Close();
   	 if(empty($gourl)) ShowMsg("成功登录，5秒钟后转向系统主页...","index.php",0,2000);
   	 else ShowMsg("成功登录，转到进入页面...",$gourl,0,2000);
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
 	 ShowMsg("成功退出登录！","login.php",0,2000);
   exit();
 }
 /*
 获取密码
 function BUserGetPwd()
 */
 else if($dopost=="getpwd")
 {
 	 session_start();
 	 if( empty($_SESSION["s_validate"]) ) $svali = "";
   else $svali = $_SESSION["s_validate"];
   if(strtolower($vdcode)!=$svali && $svali!=""){
  	 ShowMsg("验证码错误！","-1");
  	 exit();
   }
   if(!ereg("(.*)@(.*)\.(.*)",$email)){
   	 ShowMsg("邮箱地址格式不正确！","-1");
  	 exit();
   }
   $dsql = new DedeSql(false);
   $row = $dsql->GetOne("Select userid,pwd,uname,email From #@__member where email='$email'");
   if(!is_array($row))
   {
     $dsql->Close();
     ShowMsg("系统找不到此邮箱地址！","-1");
  	 exit();
   }
   $dsql->Close();
	 $mailtitle = "你在".$cfg_webname."的用户名和密码";
	 $mailbody = "\r\n用户名：'".$row['userid']."'  密码：'".$row['pwd']."'\r\n\r\n，
	 $cfg_powerby";
	 $headers = "From: ".$cfg_adminemail."\r\nReply-To: $cfg_adminemail";
   @mail($email, $mailtitle, $mailbody, $headers);
   $gurl = explode("@",$email);
   ShowMsg("成功发出你的用户名和密码，请注意查收！","login.php");
   exit();
 }
 //
 break;
}
?>