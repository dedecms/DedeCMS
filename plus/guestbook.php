<?php
require_once(dirname(__FILE__).'/guestbook/guestbook.inc.php');
require_once(DEDEINC.'/datalistcp.class.php');
if(empty($action)) $action = '';
//修改留言
if($action=='admin')
{

	include_once(dirname(__FILE__).'/guestbook/edit.inc.php');

	exit();
}
//保存留言
else if($action=='save')
{
	if(!empty($_COOKIE['GUEST_BOOK_POS'])) $GUEST_BOOK_POS = $_COOKIE['GUEST_BOOK_POS'];
	else $GUEST_BOOK_POS = 'guestbook.php';
	if(empty($validate)) $validate=='';
	else $validate = strtolower($validate);
	$svali = GetCkVdValue();
	if($validate=='' || $validate!=$svali)
	{
	 	ShowMsg("验证码不正确!","");
	 	exit();
	}
	$ip = GetIP();
	$dtime = time();
	$uname = trimMsg($uname);
	$email = trimMsg($email);
	$homepage = trimMsg($homepage);
	$homepage = eregi_replace('http://','',$homepage);
	$qq = trimMsg($qq);
	$msg = trimMsg(cn_substrR($msg, 1024), 1);
	$tid = empty($tid) ? 0 : intval($tid);
	$reid = empty($reid) ? 0 : intval($reid);

	if($msg=='' || $uname=='') {
		showMsg('你的姓名和留言内容不能为空!','-1');
		exit();
	}
	$title = HtmlReplace( cn_substrR($title,60), 1 );
	if($title=='') $title = '无标题';
	
	if($reid != 0)
	{
		$row = $dsql->GetOne("Select msg From `#@__guestbook` where id='$reid' ");
		$msg = "<div class=\\'rebox\\'>".addslashes($row['msg'])."</div>\n".$msg;
	}

	$query = "INSERT INTO `#@__guestbook`(title,tid,mid,uname,email,homepage,qq,face,msg,ip,dtime,ischeck)
                  VALUES ('$title','$tid','{$g_mid}','$uname','$email','$homepage','$qq','$img','$msg','$ip','$dtime','$needCheck'); ";
	$dsql->ExecuteNoneQuery($query);
	$gid = $dsql->GetLastID();
	if($needCheck==1)
	{
		require_once(DEDEINC."/oxwindow.class.php");
		$msg = "
		<font color='red'><b>成功发送或回复留言！</b></font> &nbsp; <a href='guestbook.php' style='font-size:14px;font-weight:bold'><u>我已经知道了，点击此返回&gt;&gt;</u></a>";
  	$wintitle = "留言发布成功提示";
		$wecome_info = "留言发布成功：";
		$win = new OxWindow();
		$win->Init("","js/blank.js","post");
		$win->AddTitle("提示：");
		$win->AddMsgItem("<div style='padding:20px;line-height:300%;font-size:14px'>$msg</div>");
		$winform = $win->GetWindow("hand");
		$win->Display();
	}
	else {
		ShowMsg('成功发送一则留言，但需审核后才能显示！','guestbook.php',0,3000);
	}
	exit();
}
//显示所有留言
else
{
	setcookie('GUEST_BOOK_POS',GetCurUrl(),time()+3600,'/');

	if($g_isadmin) $sql = 'select * from `#@__guestbook` order by id desc';
	else $sql = 'select * from `#@__guestbook` where ischeck=1 order by id desc';

	$dlist = new DataListCP();
	$dlist->pageSize = 10;
	$dlist->SetParameter('gotopagerank',$gotopagerank);
	$dlist->SetTemplate(DEDETEMPLATE.'/plus/guestbook.htm');
	$dlist->SetSource($sql);
  $dlist->Display();
}
?>