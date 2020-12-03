<?
require_once(dirname(__FILE__)."/config_base.php");

//检测用户上传空间
function GetUserSpace($uid,$dsql){
	$row = $dsql->GetOne("select sum(filesize) as fs From #@__uploads where memberID='$uid'; ");
	return $row['fs'];
}

function CheckUserSpace($uid){
	global $cfg_mb_max;
	$dsql = new DedeSql(false);
	$hasuse = GetUserSpace($uid,$dsql);
	$maxSize = $cfg_mb_max * 1024 * 1024;
	if($hasuse >= $maxSize){
		 $dsql->Close();
		 ShowMsg('你的空间已满，不允许上传新文件！','-1');
		 exit();
	}
}

//检测用户的附件类型
function CheckAddonType($aname){
	global $cfg_mb_mediatype;
	if(empty($cfg_mb_mediatype)){
		$cfg_mb_mediatype = "jpg|gif|png|swf|mpg|mp3|rm|rmvb|wmv|asf|wma|zip|rar|doc|xsl|ppt|wps";
	}
	$anames = explode('.',$aname);
	$atype = $anames[count($anames)-1];
	if(count($anames)==1) return false;
	else{
		$atype = strtolower($atype);
		$cfg_mb_mediatypes = explode('|',trim($cfg_mb_mediatype));
		if(in_array($atype,$cfg_mb_mediatypes)) return true;
		else return false;
	}
}

//获取省份信息
function GetProvince($pid,$dsql){
	global $dsql;
	if($pid<=0) return "未知";
	else{
		$row = $dsql->GetOne("Select name From #@__area where eid='$pid';");
		return $row['name'];
	}
}

//------------------------
//网站会员登录类
//------------------------
class MemberLogin
{
	var $M_ID;
	var $M_LoginID;
	var $M_Type;
	var $M_UType;
	var $M_Money;
	var $M_UserName;
	var $M_MySafeID;
	var $M_LoginTime;
	var $M_KeepTime;
	var $M_UserPwd;
	var $M_Upmoney;
	//php5构造函数
	function __construct($kptime = 0)
 	{
 		if(empty($kptime)) $this->M_KeepTime = 3600 * 24 * 15;
 		else $this->M_KeepTime = $kptime;
 		$this->M_ID = $this->GetNum(GetCookie("DedeUserID"));
 		$this->M_LoginTime = GetCookie("DedeLoginTime");
 		if(empty($this->M_ID)){
 			$this->ResetUser();
 		}else{
 		  $this->M_ID = ereg_replace("[^0-9]","",$this->M_ID);
 		  $dsql = new DedeSql(false);
 		  $row = $dsql->GetOne("Select ID,userid,pwd,uname,membertype,uptype,money,upmoney From #@__member where ID='{$this->M_ID}' ");
 		  if(is_array($row)) $dsql->ExecuteNoneQuery("update #@__member set logintime='".mytime()."' where ID='".$row['ID']."';");
 		  $dsql->Close();
 		  if(is_array($row)){
 		    $this->M_LoginID = $row['userid'];
 		    $this->M_UserPwd = $row['pwd'];
 		    $this->M_Type = $row['membertype'];
 		    $this->M_UType = $row['uptype'];
 		    $this->M_Money = $row['money'];
 		    $this->M_UserName = $row['uname'];
 		    $this->M_Upmoney = $row['upmoney'];
 		    
 		  }else{
 		  	$this->ResetUser();
 		  }
 	  }
  }
  function MemberLogin($kptime = 0){
  	$this->__construct($kptime);
  }
  //退出cookie的会话
  function ExitCookie(){
  	$this->ResetUser();
  }
  //验证用户是否已经登录
  function IsLogin(){
  	if($this->M_ID > 0) return true;
  	else return false;
  }
  //重置用户信息
  function ResetUser(){
  	$this->M_ID = 0;
 		$this->M_LoginID = "";
 		$this->M_Type = 0;
 		$this->M_UType = 0;
 		$this->M_Money = 0;
 		$this->M_UserName = "";
 		$this->M_LoginTime = 0;
 		DropCookie("DedeUserID");
 		DropCookie("DedeLoginTime");
  }
  //获取整数值
  function GetNum($fnum){
	  $fnum = ereg_replace("[^0-9\.]","",$fnum);
	  return $fnum;
  }
  //用户登录
  function CheckUser($loginuser,$loginpwd){
 		$loginuser = ereg_replace("[;%' \\\?\*\$\r\n\t]","",$loginuser);
 		$dsql = new DedeSql(false);
 		$row = $dsql->GetOne("Select ID,pwd From #@__member where userid='$loginuser' ");
 		$dsql->Close();
 		if(is_array($row)) //用户存在
 		{
 		    //密码错误
 		   if($row['pwd'] != $loginpwd){ return -1; }
 		   else{ //成功登录
 		   	 $this->PutLoginInfo($row['ID']);
 		     return 1;
 		   }
 	  }else{ //用户不存在
 	  	return 0;
 	  }
  }
  //保存用户cookie
  function PutLoginInfo($uid){
  	$this->M_ID = $uid;
 		$this->M_LoginTime = mytime();
 		PutCookie("DedeUserID",$uid,$this->M_KeepTime);
 		PutCookie("DedeLoginTime",$this->M_LoginTime,$this->M_KeepTime);
  }
  //获得会员目前的状态
  function GetSta($dsql)
  {
  	$sta = "";
  	if($this->M_Type==0) $sta .= "你目前的身份是：普通会员";
  	else{
  		$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$this->M_Type."'");
  		$sta .= "你目前的身份是：".$row['membername'];
  	}
  	if($this->M_UType>0){
  		$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$this->M_UType."'");
  	  $mname = $row['membername'];
  	  $sta .= " 正在申请升级为：$mname ";
  	}
  	$sta .= " 你目前拥有金币：".$this->M_Money." 个";
  	if($this->M_Upmoney>0) $sta .= "，正在申请 ".$this->M_Upmoney." 个金币";
  	$sta .= "。";
  	return $sta;
  }
}
?>