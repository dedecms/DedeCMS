<?
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_memberlogin.php");

$cfg_ml = new MemberLogin(); 

$cfg_member_menu = "
<a href=\"index.php\"><u>会员主页</u></a>&nbsp;
<a href=\"mystow.php\"><u>我的收藏夹</u></a>&nbsp;
<a href=\"mypay.php\"><u>消费记录</u></a>&nbsp;
<a href=\"artsend.php\"><u>投稿</u></a>&nbsp;
<a href=\"artlist.php\"><u>管理稿件</u></a>&nbsp;
<a href=\"edit_info.php\"><u>更改个人资料</u></a>&nbsp;
<a href=\"index_do.php?fmdo=login&dopost=exit\"><u>退出登录</u></a>
";

//------------------------------
//检查用户是否有权限进行某个操作
//------------------------------
function CheckRank($rank=0,$money=0)
{
	global $cfg_ml,$cfg_member_dir;
	if(!$cfg_ml->IsLogin()){
		ShowMsg("你尚未登录或已经超时！",$cfg_member_dir."/login.php?gourl=".urlencode(GetCurUrl()));
		exit();
	}
	else{
		if($cfg_ml->M_Type < $rank)
		{
		  $dsql = new DedeSql(false);
		  $needname = "";
		  if($cfg_ml->M_Type==0){
		  	$row = $dsql->GetOne("Select membername From #@__arcrank where rank='$rank'");
		  	$myname = "普通会员";
		  	$needname = $row['membername'];
		  }else
		  {
		  	$dsql->SetQuery("Select membername From #@__arcrank where rank='$rank' Or rank='".$cfg_ml->M_Type."' order by rank desc");
		  	$dsql->Execute();
		  	$row = $dsql->GetObject();
		  	$needname = $row->membername;
		  	if($row = $dsql->GetObject()){ $myname = $row->membername; }
		  	else{ $myname = "普通会员"; }
		  }
		  $dsql->Close();
		  ShowMsg("对不起，需要：<span style='font-size:11pt;color:red'>$needname</span> 才能访问本页面。<br>你目前的等级是：<span style='font-size:11pt;color:red'>$myname</span> 。","-1",0,5000);
		  exit();
		}
		else if($cfg_ml->M_Money < $money)
		{
			ShowMsg("对不起，需要花费金币：<span style='font-size:11pt;color:red'>$money</span> 才能访问本页面。<br>你目前拥有的金币是：<span style='font-size:11pt;color:red'>".$cfg_ml->M_Money."</span>  。","-1",0,5000);
		  exit();
		}
	}
}
?>