<?
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
	var $SafeCode;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct()
 	{
 		$this->SafeCode = $GLOBALS['cfg_cookie_encode'];
 		$this->M_ID = $this->GetNum($this->GetCookie("Dede_UserID"));
 		$this->M_LoginID = $this->GetCookie("Dede_UserLoginID");
 		$this->M_Type = $this->GetNum($this->GetCookie("Dede_UserType"));
 		$this->M_UType = $this->GetCookie("Dede_UserUType");
 		$this->M_Money = $this->GetNum($this->GetCookie("Dede_UserMoney"));
 		$this->M_UserName = $this->GetCookie("Dede_UserName");
 		$this->M_LoginTime = $this->GetCookie("Dede_LoginTime");
 		$this->M_MySafeID = $this->GetCookie("Dede_SafeID");
 		if($this->M_MySafeID != $this->GetSafeID() 
 		         || $this->M_ID=="" || $this->M_ID==0)
 		{
 			$this->ResetUser();
 		}
  }
  function MemberLogin()
  {
  	$this->__construct();
  }
  //---------------------
  //退出cookie的会话
  //---------------------
  function ExitCookie()
  {
  	setcookie("Dede_UserID","",time()-36000,"/");
  	setcookie("Dede_SafeID","",time()-36000,"/");
  }
  //--------------------
  //验证用户是否已经登录
  //--------------------
  function IsLogin()
  {
  	if($this->M_ID > 0) return true;
  	else return false;
  }
  //--------------------
  //获得用户安全验证ID
  //--------------------
  function GetSafeID()
  {
  	$safecode = md5($this->SafeCode.$this->M_ID.$this->M_Type.$this->M_Money.$this->M_LoginTime);
  	return $safecode;
  }
  //--------------------
  //设置用户安全验证ID
  //--------------------
  function SetSafeID()
  {
  	$safecode = $this->GetSafeID();
  	$this->M_MySafeID = $safecode;
  	$this->PutCookie("Dede_SafeID",$safecode);
  }
  //获得一个cookie值
  function GetCookie($key)
  {
	  if(!isset($_COOKIE[$key])) return "";
	  else return $_COOKIE[$key];
  }
  //---------------------
  //重置用户信息
  //---------------------
  function ResetUser()
  {
  	$this->M_ID = 0;
 		$this->M_LoginID = "";
 		$this->M_Type = 0;
 		$this->M_UType = 0;
 		$this->M_Money = 0;
 		$this->M_UserName = "";
 		$this->M_LoginTime = 0;
  }
  //---------------------
  //获取整数值
  //---------------------
  function GetNum($fnum)
  {
	  $fnum = ereg_replace("[^0-9\.]","",$fnum);
	  return $fnum;
  }
  //------------------------------
  //用户登录
  //------------------------------
  function CheckUser($loginuser,$loginpwd)
  {
 		$dsql = new DedeSql(false);
 		$dsql->SetQuery("Select ID,pwd,uname,membertype,uptype,money From #@__member where userid='$loginuser'");
 		$dsql->Execute();
 		if($dsql->GetTotalRow()>0) //用户存在
 		{
 		   $row = $dsql->GetObject();
 		   if($row->pwd != $loginpwd){ //密码错误
 		     $dsql->Close();
 		     return -1;
 		   }
 		   else  //成功登录
 		   {
 		   	 $this->PutLoginInfo($row->ID,$loginuser,$row->membertype,$row->money,$row->uname,$row->uptype);
 		   	 $dsql->Close();
 		     return 1;
 		   }
 	  }
 	  else{ //用户不存在
 	  	$dsql->Close();
 	  	return 0;
 	  }
  }
  //--------------------
  //保存用户cookie
  //--------------------
  function PutLoginInfo($uid,$lid,$mtype,$money,$uname,$utype)
  {
  	$this->M_ID = $uid;
 		$this->M_LoginID = $lid;
 		$this->M_Type = $mtype;
 		$this->M_UType = $utype;
 		$this->M_Money = $money;
 		$this->M_UserName = $uname;
 		$this->M_LoginTime = time();
 		$this->PutCookie("Dede_UserID",$uid);
 		$this->PutCookie("Dede_UserLoginID",$lid);
 		$this->PutCookie("Dede_UserType",$mtype);
 		$this->PutCookie("Dede_UserUType",$utype);
 		$this->PutCookie("Dede_UserMoney",$money);
 		$this->PutCookie("Dede_UserName",$uname);
 		$this->PutCookie("Dede_LoginTime",$this->M_LoginTime);
 		$this->SetSafeID();
  }
  //按默认参数设置一个Cookie
  function PutCookie($key,$value)
  {
	  setcookie($key,$value,time()+3600,"/");
  }
  //---------------
  //获得会员目前的状态
  //----------------
  function GetSta()
  {
  	$sta = "";
  	$dsql = new DedeSql(false);
  	if($this->M_Type==0) $sta .= "你目前的身份是：普通会员";
  	else
  	{
  		$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$this->M_Type."'");
  		$sta .= "你目前的身份是：".$row['membername'];
  	}
  	if($this->M_UType>0)
  	{
  		$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$this->M_UType."'");
  	  $mname = $row['membername'];
  	  $sta .= " 正在申请升级为：$mname ";
  	}
  	$dsql->Close();
  	$sta .= " 你目前拥有金币：".$this->M_Money." 个。";
  	return $sta;
  }
}
?>