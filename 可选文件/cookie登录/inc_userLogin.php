<?
require_once("config_base.php");
//本类用于检测管理员登录状况
//由于本类开启session和cookie支持，所以请引入时把这个类置在文件未有任何输出之前。
//使用cookie管理会话存在一定的风险，如果你知道这意味着什么，请在keepUser()中关闭cookie登录的选项。
//-----------本类使用说明-------------------------------
//检验用户登录：
//checkUser(username,userpwd);
//返回 1 表示成功，返回 -1 表示用户名不正确，-2 表示密码错误

//保持用户会话状态，
//keepUser(keeptype,keeptime);
//keeptype为 session或cookie，keeptime单位为分钟，仅对cookie有效
//正确返回 1，失败返回 -1

//其它方法：

//getUserType(); 获取用户级别
//本系统只支持超级管理员 10，频道总编 5 ，信息采编 2 三种权限
//失败返回 -1

//getUserID(); 获取用户ID
//正确则返回用户ID，失败返回 -1

//exitUser(); 注销会话
//------------------------------------------------------
class userLogin
{
	var $userName="";
	var $userPwd="";
	var $userID="";
	var $userType="";
	var $userChannel="";
	var $userMd5="";
	var $keepUserIDTag="dede_admin_id";
	var $keepUserTypeTag="dede_admin_type";
	var $keepUserChannelTag="dede_admin_channel";
	var $keepUserNameTag="dede_admin_name";
	var $safeMd5 = "d877f7sa8f7rbg8b7n";
	var $keepSafeMD5 = "dede_md5";
	function userLogin()
	{
		if
		(isset($_COOKIE[$this->keepSafeMD5])&&isset($_COOKIE[$this->keepUserIDTag])&&isset($_COOKIE[$this->keepUserTypeTag])&&isset($_COOKIE[$this->keepUserChannelTag])&&isset($_COOKIE[$this->keepUserNameTag]))
		{
			$this->userID=$_COOKIE[$this->keepUserIDTag];
			$this->userType=$_COOKIE[$this->keepUserTypeTag];
			$this->userChannel=$_COOKIE[$this->keepUserChannelTag];
			$this->userName=$_COOKIE[$this->keepUserNameTag];
			if($_COOKIE[$this->keepSafeMD5]!=md5($this->safeMd5.$this->userID.$this->userName))
			{echo "MD5 身份安全验证码错误！<a href='login.php'>请重新登录</a>";exit();}
	    }
	}
	//检验用户是否正确
	function checkUser($username,$userpwd)
	{
		//只允许用户名和密码用0-9,a-z,A-Z,'@','_','.','-'这些字符
		$this->userName = ereg_replace("[^0-9a-zA-Z_@\!\.-]","",$username);
		$this->userPwd = ereg_replace("[^0-9a-zA-Z_@\!\.-]","",$userpwd);
		$conn = connectMySql();
		$pwd = md5($this->userPwd);
		$rs = mysql_query("Select * From dede_admin where userid like '".$this->userName."' limit 0,1",$conn);
		$row = mysql_fetch_object($rs);
		if(!isset($row->pwd))
		{
			return -1;
		}
		else if($pwd!=$row->pwd)
		{
			return -2;
		}
		else
		{
			if(isset($_SERVER["REMOTE_ADDR"]))	$loginip = $_SERVER["REMOTE_ADDR"];
			else $loginip="PHP配置错误";
			$this->userID = $row->ID;
			$this->userType = $row->usertype;
			$this->userChannel = $row->typeid;
			$this->userName = $row->uname;
			$this->userMd5 = md5($this->safeMd5.$this->userID.$this->userName);
			$squery = "update dede_admin set loginip='$loginip',logintime='".strftime("%Y-%m-%d %H:%M:%S",time())."' where ID=".$row->ID;
			mysql_query($squery,$conn);
			return 1;
		}
	}
	//保持用户的会话状态
	//成功返回 1 ，失败返回 -1
	function keepUser()
	{
		if($this->userID!=""&&$this->userType!="")
		{
			setcookie($this->keepUserIDTag,$this->userID,time()+72000,"/");
			setcookie($this->keepUserTypeTag,$this->userType,time()+72000,"/");
			setcookie($this->keepUserChannelTag,$this->userChannel,time()+72000,"/");
			setcookie($this->keepUserNameTag,$this->userName,time()+72000,"/");
			setcookie($this->keepSafeMD5,$this->userMd5,time()+72000,"/");
			return 1;
		}
		else
			return -1;
	}
	//结束用户的会话状态
	function exitUser()
	{
		setcookie($this->keepUserIDTag,"",time()-72000,"/");
		setcookie($this->keepUserTypeTag,"",time()-72000,"/");
		setcookie($this->keepUserChannelTag,"",time()-72000,"/");
		setcookie($this->keepUserNameTag,"",time()-72000,"/");
		setcookie($this->keepSafeMD5,"",time()-72000,"/");
		$this->userType = "";
		$this->userID = "";
		$this->userChannel = "";
	}
	//获得用户的权限值
	function getUserChannel()
	{
		if($this->userChannel!="") return $this->userChannel;
		else return -1;
	}
	//获得用户的权限值
	function getUserType()
	{
		if($this->userType!="") return $this->userType;
		else return -1;
	}
	function getUserRank()
	{
		return $this->getUserType();
	}
	//获得用户的ID
	function getUserID()
	{
		if($this->userID!="") return $this->userID;
		else return -1;
	}
	//获得用户的笔名
	function getUserName()
	{
		if($this->userName!="") return $this->userName;
		else return -1;
	}
}
?>