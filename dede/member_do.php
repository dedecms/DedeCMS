<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");

if(empty($dopost)) $dopost = "";
if(empty($fmdo)) $fmdo = "";

if(!isset($ENV_GOBACK_URL)) $ENV_GOBACK_URL = '';

/*----------------
function __DelMember()
删除会员
----------------*/
if($dopost=="delmember")
{
	CheckPurview('member_Del');

	if($fmdo=="yes")
	{
		$ID = ereg_replace("[^0-9]","",$ID);
		$dsql = new DedeSql(false);
		$dsql->ExecuteNoneQuery("Delete From #@__member where ID='$ID'");
		$dsql->ExecuteNoneQuery("Delete From #@__memberstow where uid='$ID'");
		$dsql->ExecuteNoneQuery("Delete From #@__member_guestbook where mid='$ID'");
		$dsql->ExecuteNoneQuery("Delete From #@__member_arctype where memberid='$ID'");
		$dsql->ExecuteNoneQuery("Delete From #@__member_flink where mid='$ID'");
		$dsql->Close();
		ShowMsg("成功删除一个会员！",$ENV_GOBACK_URL);
		exit();
	}

	$wintitle = "会员管理-删除会员";
	$wecome_info = "<a href='".$ENV_GOBACK_URL."'>会员管理</a>::删除会员";
	$win = new OxWindow();
	$win->Init("member_do.php","js/blank.js","POST");
	$win->AddHidden("fmdo","yes");
	$win->AddHidden("dopost",$dopost);
	$win->AddHidden("ID",$ID);
	$win->AddTitle("你确实要删除(ID:".$ID.")这个会员?");
	$winform = $win->GetWindow("ok");
	$win->Display();
}
/*-----------------------------
function __UpOperations()
业务状态更改为已付款状态
------------------------------*/
else if($dopost=="upoperations")
{
	CheckPurview('member_Operations');
	if($nid==''){
		ShowMsg("没选定要更改的业务记录！","-1");
		exit();
	}
	$nids = explode('`',$nid);
  $wh = '';
	foreach($nids as $n){
		if($wh=='') $wh = " where aid='$n' ";
		else $wh .= " Or aid='$n' ";
	}
	$dsql = new DedeSql(false);
	$dsql->SetQuery("update #@__member_operation set sta=1 $wh ");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改指定的业务记录！",$ENV_GOBACK_URL);
	exit();
}
/*----------------------------
function __OkOperations()
业务状态更改改完成状态
-----------------------------*/
else if($dopost=="okoperations")
{
	CheckPurview('member_Operations');
	if($nid==''){
		ShowMsg("没选定要更改的业务记录！","-1");
		exit();
	}
	$nids = explode('`',$nid);
  $wh = '';
	foreach($nids as $n){
		if($wh=='') $wh = " where aid='$n' ";
		else $wh .= " Or aid='$n' ";
	}
	$dsql = new DedeSql(false);
	$dsql->SetQuery("update #@__member_operation set sta=2 $wh ");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改指定的业务记录！",$ENV_GOBACK_URL);
	exit();
}
/*----------------
function __UpRank()
会员升级
----------------*/
else if($dopost=="uprank")
{
	CheckPurview('member_Edit');

	if($fmdo=="yes")
	{
		$ID = ereg_replace("[^0-9]","",$ID);
		$membertype = ereg_replace("[^0-9]","",$membertype);
		$dsql = new DedeSql(false);
		$dsql->SetQuery("update #@__member set membertype='$membertype',uptype='0' where ID='$ID'");
		$dsql->ExecuteNoneQuery();
		$dsql->Close();
		ShowMsg("成功升级一个会员！",$ENV_GOBACK_URL);
		exit();
	}

	$dsql = new DedeSql(false);
  $MemberTypes = "";
  $dsql->SetQuery("Select rank,membername From #@__arcrank where rank>0");
  $dsql->Execute();
  $MemberTypes[0] = "普通会员";
  while($row = $dsql->GetObject()){
	  $MemberTypes[$row->rank] = $row->membername;
  }
  $dsql->Close();

  $options = "<select name='membertype' style='width:100'>\r\n";
  foreach($MemberTypes as $k=>$v)
  {
  	if($k!=$uptype) $options .= "<option value='$k'>$v</option>\r\n";
  	else $options .= "<option value='$k' selected>$v</option>\r\n";
  }
  $options .= "</select>\r\n";

	$wintitle = "会员管理-会员升级";
	$wecome_info = "<a href='".$ENV_GOBACK_URL."'>会员管理</a>::会员升级";
	$win = new OxWindow();
	$win->Init("member_do.php","js/blank.js","POST");
	$win->AddHidden("fmdo","yes");
	$win->AddHidden("dopost",$dopost);
	$win->AddHidden("ID",$ID);
	$win->AddTitle("会员升级：");
	$win->AddItem("会员目前的等级：",$MemberTypes[$mtype]);
	$win->AddItem("会员申请的等级：",$MemberTypes[$uptype]);
	$win->AddItem("开通等级：",$options);
	$winform = $win->GetWindow("ok");
	$win->Display();
}
/*----------------
function __Recommend()
推荐会员
----------------*/
else if($dopost=="recommend")
{
	CheckPurview('member_Edit');
	$ID = ereg_replace("[^0-9]","",$ID);
	$dsql = new DedeSql(false);
	if($matt==0){
		$dsql->ExecuteNoneQuery("update #@__member set matt=1 where ID='$ID'");
		$dsql->Close();
		ShowMsg("成功设置一个会员推荐！",$ENV_GOBACK_URL);
	  exit();
	}else{
		$dsql->ExecuteNoneQuery("update #@__member set matt=0 where ID='$ID'");
	  $dsql->Close();
	  ShowMsg("成功取消一个会员推荐！",$ENV_GOBACK_URL);
	  exit();
  }
}
/*----------------
function __AddMoney()
会员充值
----------------*/
else if($dopost=="addmoney")
{
	CheckPurview('member_Edit');

	if($fmdo=="yes")
	{
		$ID = ereg_replace("[^0-9]","",$ID);
		$money = ereg_replace("[^0-9]","",$money);
		$dsql = new DedeSql(false);
		$dsql->SetQuery("update #@__member set money=money+$money where ID='$ID'");
		$dsql->ExecuteNoneQuery();
		$dsql->Close();
		ShowMsg("成功给一个会员充值！",$ENV_GOBACK_URL);
		exit();
	}
	if(empty($upmoney)) $upmoney = 500;
	$wintitle = "会员管理-会员充值";
	$wecome_info = "<a href='".$ENV_GOBACK_URL."'>会员管理</a>::会员充值";
	$win = new OxWindow();
	$win->Init("member_do.php","js/blank.js","POST");
	$win->AddHidden("fmdo","yes");
	$win->AddHidden("dopost",$dopost);
	$win->AddHidden("ID",$ID);
	$win->AddTitle("会员充值：");
	$win->AddMsgItem("请输入充值点数：<input type='text' name='money' size='10' value='$upmoney'>",60);
	$winform = $win->GetWindow("ok");
	$win->Display();
}
/*----------------
function __EditUser()
更改会员
----------------*/
else if($dopost=="edituser")
{
	CheckPurview('member_Edit');
	$dsql = new DedeSql(false);
	$uptime =  GetMkTime($uptime);
	$edpwd = '';
	if($newpwd!=''){
		$newpwd = GetEncodePwd($newpwd);
		$edpwd = "pwd='$newpwd',";
	}
	$query1 = "update #@__member set
 	  {$edpwd}
 	  membertype = '$membertype',
 	  uptime = '$uptime',
 	  exptime = '$exptime',
 	  money = '$money',
 	  scores = '$scores',
 	  email = '$email',
    uname = '$uname',
    sex = '$sex',
    mybb = '$mybb',
    spacename = '$spacename',
    news = '$news'
 	  where ID='$ID'";
 	$query2 = "update #@__member_perinfo set
    uname = '$uname',
    sex = '$sex',
    birthday = '$birthday',
    weight = '$weight',
    height = '$height',
    job = '$job',
    province = '$province',
    city = '$city',
    myinfo = '$myinfo',
    oicq = '$oicq',
    tel = '$tel',
    homepage = '$homepage',
    fullinfo = '$fullinfo',
    address = '$address'
 	  where id='$ID'";
	$dsql->ExecuteNoneQuery($query1);
	$dsql->ExecuteNoneQuery($query2);
  $dsql->Close();
  ShowMsg("成功更改会员资料！",$ENV_GOBACK_URL);
  exit();
}

ClearAllLink();
?>