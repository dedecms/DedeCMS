<?php
$cfg_needFilter = true;
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_memberlogin.php");

//获得当前脚本名称，如果你的系统被禁用了$_SERVER变量，请自行更改这个选项
$dedeNowurl = "";
$s_scriptName="";
$dedeNowurl = GetCurUrl();
$dedeNowurls = explode("?",$dedeNowurl);
$s_scriptName = $dedeNowurls[0];

//检查是否开放会员功能
//-------------------------------

if($cfg_mb_open=='N'){
	ShowMsg("系统关闭了会员功能，因此你无法访问此页面！","javascript:;");
	exit();
}

$cfg_egstr = "[\\\|\"\s\*\?\(\)\$;,'`%]";

$cfg_ml = new MemberLogin();

//$cfg_ml->PutLoginInfo($cfg_ml->M_ID);

//检查用户是否有权限进行某个操作
//------------------------------
function CheckRank($rank=0,$money=0)
{
	global $cfg_ml,$cfg_member_dir,$cfg_pp_isopen,$cfg_pp_loginurl,$cfg_pp_exiturl;
	if(!$cfg_ml->IsLogin()){
		if($cfg_pp_isopen==0 || $cfg_pp_loginurl==''){
			 ShowMsg("你尚未登录或已经超时！",$cfg_member_dir."/login.php?gourl=".urlencode(GetCurUrl()));
		   exit();
		}else{
			$cfg_ml->ExitCookie();
			ShowMsg("你尚未登录或已经超时！",$cfg_pp_exiturl);
			exit();
		}

	}
	else{
		if($cfg_ml->M_Type < $rank)
		{
		  $dsql = new DedeSql(false);
		  $needname = "";
		  if($cfg_ml->M_Type==0){
		  	$row = $dsql->GetOne("Select membername From #@__arcrank where rank='$rank'");
		  	$myname = "未审核会员";
		  	$needname = $row['membername'];
		  }else
		  {
		  	$dsql->SetQuery("Select membername From #@__arcrank where rank='$rank' Or rank='".$cfg_ml->M_Type."' order by rank desc");
		  	$dsql->Execute();
		  	$row = $dsql->GetObject();
		  	$needname = $row->membername;
		  	if($row = $dsql->GetObject()){ $myname = $row->membername; }
		  	else{ $myname = "未审核会员"; }
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