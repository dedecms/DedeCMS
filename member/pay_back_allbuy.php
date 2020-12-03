<?php 
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_memberlogin.php");
require_once(dirname(__FILE__)."/../include/pub_httpdown.php");
require_once(dirname(__FILE__)."/config_pay_allbuy.php");

$cfg_ml = new MemberLogin(); 
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0) $burl = "control.php";
else $burl = "javascript:;";

if(empty($billno)){
	echo "非法访问！";
	exit();
}

$mySign = md5($cfg_merchant.$billno.$amount.$success.$cfg_merpassword);
$buyid = $billno;


//签名正确
if($sign == $mySign && $success=="Y"){
     $dsql = new DedeSql(false);
     //获取订单信息，检查订单的有效性
     $row = $dsql->GetOne("Select * From #@__member_operation where buyid='$buyid' ");
     if(!is_array($row)||$row['sta']==2){
		   $oldinfo = $row['oldinfo'];
		   $msg = "本交易已经完成！，系统返回信息( $oldinfo ) <br><br> <a href='control.php'>返回主页</a> ";
		   ShowMsg($msg,"javascript:;");
		   $dsql->Close();
		   exit();
	   }
	   $mid = $row['mid'];
	   $pid = $row['pid'];
     //更新交易状态为已付款
	   $dsql->ExecuteNoneQuery("Update #@__member_operation set sta=1 where buyid='$buyid' ");
	   //-------------------------------------------
	   //会员产品
	   //-------------------------------------------
	   if($row['product']=='member')
	   {
		    $row = $dsql->GetOne(" Select rank,exptime From #@__member_type where aid='{$row['pid']}' ");
		    $rank = $row['rank'];
		    $exptime = $row['exptime'];
		    $equery =  " Update #@__member set 
		                membertype='$rank',exptime='$exptime',uptime='".time()."' where ID='$mid' ";
		    $dsql->ExecuteNoneQuery($equery);
			  //更新交易状态为已关闭
			  $dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='会员升级成功！' where buyid='$buyid' ");
		    $dsql->Close();
		    
		    //向支付接口返回信息
		    $dhd = new DedeHttpDown();
   	    @$dhd->OpenUrl("http://www.allbuy.cn/merchant/checkfeedback.asp?".$_SERVER["QUERY_STRING"]);
   	    @$staCode = $dhd->GetHtml();
   	    $dhd->Close();
   	    
        ShowMsg("成功完成交易！",$burl);
	      exit();
	   }
	   //点卡产品
	   else if($row['product']=='card')
	   {
		    $row = $dsql->GetOne("Select cardid From #@__moneycard_record where ctid='$pid' And isexp='0' ");
		    //如果找不到某种类型的卡，直接为用户增加金币
		    if(!is_array($row)){
		    	  $nrow = $dsql->GetOne("Select num From  #@__moneycard_type where tid='$pid' ");
		    	  $dnum = $nrow['num'];
		    	  $equery =  " Update #@__member set money=money+".$dnum." where ID='$mid' ";
		        $dsql->ExecuteNoneQuery($equery);
		        //更新交易状态为已关闭
			      $dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='直接充值了 {$dnum} 金币到帐号！' where buyid='$buyid' ");
		        
		        //向支付接口返回信息
		        $dhd = new DedeHttpDown();
   	        @$dhd->OpenUrl("http://www.allbuy.cn/merchant/checkfeedback.asp?".$_SERVER["QUERY_STRING"]);
   	        @$staCode = $dhd->GetHtml();
   	        $dhd->Close();
		        
		        ShowMsg("由于此点卡已经卖完，系统直接为你的帐号增加了：{$dnum} 个金币！",$burl);
		        $dsql->Close();
		        exit();
		    }else{
		    	 $cardid = $row['cardid'];
		    	 $dsql->ExecuteNoneQuery(" Update #@__moneycard_record set uid='$mid',isexp='1',utime='".time()."' where cardid='$cardid' ");
		    	 //更新交易状态为已关闭
			     $dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='充值密码：{$cardid}' where buyid='$buyid' ");
		    	 
		    	 //向支付接口返回信息
		        $dhd = new DedeHttpDown();
   	        @$dhd->OpenUrl("http://www.allbuy.cn/merchant/checkfeedback.asp?".$_SERVER["QUERY_STRING"]);
   	        @$staCode = $dhd->GetHtml();
   	        $dhd->Close();
		    	 
		    	 ShowMsg("交易成功！<a href='control.php'><u>[返回]</u></a><br> 充值密码为：{$cardid}","javascript:;");
		    	 $dsql->Close();
		       exit();
		    }
	  }
}else{
	ShowMsg("交易错误，请与管理员联系！",$burl);
	exit();
}
?>
