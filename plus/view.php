<?php
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_archives_view.php");
require_once(dirname(__FILE__)."/../include/inc_memberlogin.php");
$ml = new MemberLogin();
function ParamError(){
	ShowMsg("对不起，你输入的参数有误！","javascript:;");
	exit();
}

if(!isset($aid)) $aid = 0;

$aid = ereg_replace("[^0-9]","",$aid);

if(empty($okview)) $okview="";
if($aid==0||$aid=="") ParamError();

$arc = new Archives($aid);

if($arc->IsError) {
	$arc->Close();
	ParamError();
}

//未审核文档
if($arc->Fields['arcrank']==-1 && $arc->Fields['memberID']!=$ml->M_ID)
{
	require_once(dirname(__FILE__)."/../include/inc_userlogin.php");
	$ul = new userLogin();
	if(empty($ul->userID))
	{
	   ShowMsg("对不起，你无权访问未审核文档！","javascript:;");
	   $arc->Close();
	   exit();
	}
}


//扣点
function PayMoney($ml,$arc,$money){
	 global $aid;
   $row = $arc->dsql->GetOne("Select aid,money From #@__moneyrecord where aid='$aid' And uid='".$ml->M_ID."'");
   if(!is_array($row)){
		   //金币消费记录
		   $inquery = "INSERT INTO #@__moneyrecord(aid,uid,title,money,dtime)
               VALUES ('$aid','".$ml->M_ID."','{$arc->Fields['title']}','$money','".time()."');";
		   if($arc->dsql->ExecuteNoneQuery($inquery)){
		  	  $inquery = "Update #@__member set money=money-$money where ID='".$ml->M_ID."'";
		      $arc->dsql->ExecuteNoneQuery($inquery);
		      $ml->FushCache();
		   }
		}
}
//检查阅读权限
//--------------------
$needMoney = $arc->Fields['money'];
$needRank = $arc->Fields['arcrank'];
$arcTitle = $arc->Fields['title'];
//设置了权限限制的文章
//会员权限说明:
//1、对于设定了包时的中高级会员，浏览任何权限内的文档都不需要使用金币
//2、对于权限不足，又有金币的用户，可以花1个金币浏览权限外的文档，或花设定的金币浏览某文档
//arctitle msgtitle moremsg
//------------------------------------

if($needMoney > 0 || $needRank > 0)
{
	if($needMoney<1 && $needRank > $ml->M_Type) $needMoney = 1;
	$arctitle = $arc->Fields['title'];
	$arclink = $arc->TypeLink->GetFileUrl($arc->ArcID,
	                $arc->Fields["typeid"],
	                $arc->Fields["senddate"],
	                $arc->Fields["title"],
	                $arc->Fields["ismake"],
	                $arc->Fields["arcrank"]);

	$arc->dsql->SetQuery("Select * From #@__arcrank");
	$arc->dsql->Execute();
	while($nrow = $arc->dsql->GetObject()){
			$memberTypes[$nrow->rank] = $nrow->membername;
	}
	$memberTypes[0] = '未审核会员';
	$memberTypes[-1] = "<a href='{$cfg_memberurl}'>你尚未登陆</a>";

	$description =  $arc->Fields["description"];
	$pubdate = GetDateTimeMk($arc->Fields["pubdate"]);

	//对于设定了包时的中高级会员，浏览任何权限内的文档都不需要使用金币
	//----------------------------------------------------------------
	if( ($ml->M_Type > 10) && ($ml->M_Type >= $needRank ) ){
		 //会员已经过期
		 if($ml->M_HasDay<1){
			  //无足够金币
			  if( $ml->M_Money < $needMoney )
			  {
			     $msgtitle = "阅读：{$arcTitle} 权限不足！";
		       $moremsg = "这篇文档需要 [<font color='red'>".$memberTypes[$needRank]."</font>] ";
		       $moremsg .= "或花费 {$needMoney} 个金币才能访问，你目前的会员身份已经过期，拥有金币 {$ml->M_Money} 个！";
		       include_once($cfg_basedir.$cfg_templets_dir."/plus/view_msg.htm");
		       exit();
		    //有足够金币
		    }else{
		    	 PayMoney($ml,$arc,$needMoney);
		    }
		 }
	//非包时会员或级别不足的会员，使用金币阅读
	//-------------------------------------------------------------------
	}else{
		//无足够金币
		if( $ml->M_Money < $needMoney )
		{
			   $msgtitle = "阅读：{$arcTitle} 权限不足！";
		     $moremsg = "这篇文档需要 [<font color='red'>".$memberTypes[$needRank]."</font>] ";
		     $moremsg .= "或花费 {$needMoney} 个金币才能访问，你目前的会员身份为".$memberTypes[$ml->M_Type]."，拥有金币 {$ml->M_Money} 个！";
		     include_once($cfg_basedir.$cfg_templets_dir."/plus/view_msg.htm");
		     exit();
		 //有足够金币
		 }else{
		    PayMoney($ml,$arc,$needMoney);
		 }
	}
}

$arc->Display();
?>