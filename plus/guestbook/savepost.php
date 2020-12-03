<?
require_once(dirname(__FILE__)."/config.php");
if(!empty($_COOKIE['GUEST_BOOK_MOVE'])) $GUEST_BOOK_MOVE = $_COOKIE['GUEST_BOOK_MOVE'];
else $GUEST_BOOK_MOVE = "index.php";

if(empty($validate)) $validate=="";
else $validate = strtolower($validate);
$svali = GetCkVdValue();
if($validate=="" || $validate!=$svali){
	 ShowMsg("验证码不正确!","");
	 exit();
}

$dsql = new DedeSql(false);
$ip = GetIP();
$dtime = strftime("%Y-%m-%d %H:%M:%S",mytime());
$uname = trimMsg($uname);
$email = trimMsg($email);
$homepage = trimMsg($homepage);
$homepage = eregi_replace("http://","",$homepage);
$qq = trimMsg($qq);
$msg = trimMsg($msg,1);
$msg = cn_substr($msg,2000);

if($msg==""||$uname==""){
	showMsg("你的姓名和留言内容不能为空!",-1);
	exit();
}

$query = "INSERT INTO 
#@__guestbook(uname,email,homepage,qq,face,msg,ip,dtime,ischeck) 
VALUES ('$uname','$email','$homepage','$qq','$img','$msg','$ip','$dtime','$needCheck')";
$dsql->SetQuery($query);
$dsql->ExecuteNoneQuery();
$dsql->Close();

if($needCheck==1) ShowMsg("成功发送一则留言!",$GUEST_BOOK_MOVE);
else ShowMsg("成功发送一则留言，但需审核后才能显示！",$GUEST_BOOK_MOVE,0,3000);

exit();

?>
