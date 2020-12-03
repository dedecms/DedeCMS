<?
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_archives_view.php");
$aid = ereg_replace("[^0-9]","",$aid);
if(empty($okview)) $okview="";
if($aid==0||$aid=="")
{
	ShowMsg("对不起，你输入的参数有误！","-1");
	exit();
}
$arc = new Archives($aid);
//检查阅读权限
//--------------------
$needMoney = $arc->ArcInfos['money'];
$needRank = $arc->ArcInfos['arcrank'];
//设置了权限限制的文章
//arctitle msgtitle moremsg
//------------------------------------
if($needMoney>0 || $needRank>1)
{
	require_once(dirname(__FILE__)."/../include/inc_memberlogin.php");
	$ml = new MemberLogin();
	$arctitle = $arc->ArcInfos['title'];
	$arclink = $arc->TypeLink->GetFileUrl($arc->ArcID,
	                $arc->ArcInfos["typeid"],
	                $arc->ArcInfos["senddate"],
	                $arc->ArcInfos["title"],
	                $arc->ArcInfos["ismake"],
	                $arc->ArcInfos["arcrank"]
	           );
	$description =  $arc->ArcInfos["description"]; 
	$pubdate = GetDateTimeMk($arc->ArcInfos["pubdate"]);
	if($ml->M_ID==0 || 
	($ml->M_Type < $needRank && $arc->ArcInfos['memberID']!=$ml->M_ID) )  //会员级别不足
	{
		$dsql = new DedeSql(false);
		$dsql->SetQuery("Select * From #@__arcrank");
		$dsql->Execute();
		while($row = $dsql->GetObject()){
			$memberTypes[$row->rank] = $row->membername;
		}
		$memberTypes[0] = "普通会员";
		$dsql->Close();
		$msgtitle = "没有权限！";
		$moremsg = "这篇文档需要<font color='red'>".$memberTypes[$needRank]."</font>才能访问，你目前是：<font color='red'>".$memberTypes[$ml->M_Type]."</font>";
		$dtp = new DedeTagParse();
		$dtp->LoadTemplate($cfg_basedir.$cfg_templets_dir."/plus/view_msg.htm");
		$dtp->Display();
		$arc->Close();
		exit();
	}
	if($needMoney > 0 && $needMoney > $ml->M_Money  && $arc->ArcInfos['memberID']!=$ml->M_ID) //没有足够的金币
	{
		$msgtitle = "没有权限！";
		$moremsg = "这篇文档需要 <font color='red'>".$needMoney." 金币</font> 才能访问，你目前拥有金币：<font color='red'>".$ml->M_Money." 个</font>";
		$dtp = new DedeTagParse();
		$dtp->LoadTemplate($cfg_basedir.$cfg_templets_dir."/plus/view_msg.htm");
		$dtp->Display();
		$arc->Close();
		exit();
	}
	else if($needMoney > 0  && $arc->ArcInfos['memberID']!=$ml->M_ID) //如果文章需要金币，检查用户是否浏览过本文档
	{
		$dsql = new DedeSql(false);
		$row = $dsql->GetOne("Select aid,money From #@__moneyrecord where aid='$aid' And uid='".$ml->M_ID."'");
		if(!is_array($row))
		{
		  $inquery = "
		  INSERT INTO #@__moneyrecord(aid,uid,title,money,dtime) 
      VALUES ('$aid','".$ml->M_ID."','$arctitle','$needMoney','".time()."');
		  ";
		  $dsql->SetQuery($inquery);
		  if($dsql->ExecuteNoneQuery())
		  {
		  	$inquery = "Update #@__member set money=money-$needMoney where ID='".$ml->M_ID."'";
		    $dsql->SetQuery($inquery);
		    $dsql->ExecuteNoneQuery();
		  }
		}
		$dsql->Close();
	}
}
$arc->Display();
$arc->Close();
?>