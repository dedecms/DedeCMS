<?php
$cfg_IsCanView=true;
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_userlogin.php");
if(empty($dopost)) $dopost="";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $cfg_softname." ".$cfg_version?></title>
<style type="text/css">
<!--
*{
	padding:0px;
	margin:0px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
}
body {
	margin: 0px;
	background:#F7F7F7;
	font-size:12px;
}
input{
	vertical-align:middle;
}
img{
	border:none;
	vertical-align:middle;
}
a{
	color:#333333;
}
a:hover{
	color:#FF3300;
	text-decoration:none;
}
.main{
	width:640px;
	margin:40px auto 0px;
	border:4px solid #EEE;
	background:#FFF;
	padding-bottom:10px;
}

.main .title{
	width:600px;
	height:50px;
	margin:0px auto;
	background:url(images/login_toptitle.jpg) -10px 0px no-repeat;
	text-indent:326px;
	line-height:46px;
	font-size:14px;
	letter-spacing:2px;
	color:#F60;
	font-weight:bold;
}

.main .login{
	width:560px;
	margin:20px auto 0px;
	overflow:hidden;
}
.main .login .inputbox{
	width:260px;
	float:left;
	background:url(images/login_input_hr.gif) right center no-repeat;
}
.main .login .inputbox dl{
	width:230px;
	height:41px;
	clear:both;
}
.main .login .inputbox dl dt{
	float:left;
	width:60px;
	height:31px;
	line-height:31px;
	text-align:right;
	font-weight:bold;
}
.main .login .inputbox dl dd{
	width:160px;
	float:right;
	padding-top:1px;
}
.main .login .inputbox dl dd input{
	font-size:12px;
	font-weight:bold;
	border:1px solid #888;
	padding:4px;
	background:url(images/login_input_bg.gif) left top no-repeat;
}


.main .login .butbox{
	float:left;
	width:200px;
	margin-left:26px;
}
.main .login .butbox dl{
	width:200px;
}
.main .login .butbox dl dt{
	width:160px;
	height:41px;
	padding-top:5px;
}
.main .login .butbox dl dt input{
	width:98px;
	height:33px;
	background:url(images/login_submit.gif) no-repeat;
	border:none;
	cursor:pointer;
}
.main .login .butbox dl dd{
	height:21px;
	line-height:21px;
}
.main .login .butbox dl dd a{
	margin:5px;
}



.main .msg{
	width:560px;
	margin:10px auto;
	clear:both;
	line-height:17px;
	padding:6px;
	border:1px solid #FC9;
	background:#FFFFCC;
	color:#666;
}

.copyright{
	width:640px;
	text-align:right;
	margin:10px auto;
	font-size:10px;
	color:#999999;
}
.copyright a{
	font-weight:bold;
	color:#F63;
	text-decoration:none;
}
.copyright a:hover{
	color:#000;
}
-->
</style>
<?php 
if($dopost!="login"){
?>
<script type="text/javascript" language="javascript">
<!--
	window.onload = function (){
		userid = document.getElementById("userid");
		userid.focus();
	}
-->
</script>
<?php
}
?>
</head>
<body>

<?php
//--------------------------------
//登录检测
//--------------------------------
if($dopost=="login")
{
  if(empty($validate)) $validate="";
  else $validate = strtolower($validate);
  $svali = GetCkVdValue();
  if(($validate=="" || $validate!=$svali) && $cfg_use_vdcode=='Y'){
	  ShowMsg("验证码不正确!","");
  }else{
     $cuserLogin = new userLogin();
     if(!empty($userid)&&!empty($pwd))
     {
	      $res = $cuserLogin->checkUser($userid,$pwd);
	      //成功登录
	      if($res==1){
		       $cuserLogin->keepUser();
		       if(!empty($gotopage)){
		       	ShowMsg("成功登录，正在转向管理管理主页！",$gotopage);
		       	exit();
		       }
		       else{
		       	ShowMsg("成功登录，正在转向管理管理主页！","index.php");
		       	exit();
		       }
	      }
	      else if($res==-1){
		      ShowMsg("你的用户名不存在!","");
	      }
	      else{
		      ShowMsg("你的密码错误!","");
	      }
     }//<-密码不为空
     else{
	    ShowMsg("用户和密码没填写完整!","");
     }
     
  }//<-验证用户
}
?>
	<div class="main">
		<div class="title">
			管理登陆
		</div>

		<div class="login">
		<form action="login.php" method="post">
            <input type="hidden" name="gotopage" value="<?php if(!empty($gotopage)) echo $gotopage;?>">
            <input type="hidden" name="dopost" value="login">
            <div class="inputbox">
				<dl>
					<dt>用户名：</dt>
					<dd><input type="text" name="userid" id="userid" size="20" onfocus="this.style.borderColor='#F93'" onblur="this.style.borderColor='#888'" />
					</dd>
				</dl>
				<dl>
					<dt>密码：</dt>
					<dd><input type="password" name="pwd" size="20" onfocus="this.style.borderColor='#F93'" onblur="this.style.borderColor='#888'" />
					</dd>
				</dl>
				<?php if($cfg_use_vdcode=='Y'){	?>
				<dl>
					<dt>验证码：</dt>
					<dd>
						<input type="text" name="validate" size="4" onfocus="this.style.borderColor='#F90'" onblur="this.style.borderColor='#888'" />
						<img src="../include/vdimgck.php" width="50" height="20" />
					</dd>
				</dl>
				<?php } ?>
            </div>
            <div class="butbox">
            <dl>
					<dt><input name="submit" type="submit" value="" /></dt>
					<dd><a href="http://www.dedecms.com">官方网站</a> | <a href="http://bbs.dedecms.com">技术论坛</a></dd>
				</dl>
			</div>
		</form>
		</div>
		
		<?php if($cfg_use_vdcode!='Y'){	?>
		<div class="msg">
			为了使程序有更大程度的兼容，后台默认关闭了验证码，为了你的登录更安全，请确认你的系统支持GD后，在后台参数中开启。
		</div>
		<?php } ?>
	
	</div>
	
	<div class="copyright">
		Powered by <a href="http://www.dedecms.com">DEDECMS <?php echo $cfg_version?></a> Copyright &copy;2004-2008 
	</div>

</body>
</html>
<?php
if(is_object($dsql)) $dsql->Close();
?>
