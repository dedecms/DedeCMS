<?
session_start();
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
	var $keepUserIDTag="dede_admin_id";
	var $keepUserTypeTag="dede_admin_type";
	var $keepUserChannelTag="dede_admin_channel";
	var $keepUserNameTag="dede_admin_name";
	function userLogin()
	{
		if
		(isset($_SESSION[$this->keepUserIDTag])&&isset($_SESSION[$this->keepUserTypeTag])&&isset($_SESSION[$this->keepUserChannelTag])&&isset($_SESSION[$this->keepUserNameTag]))
		{
			$this->userID=$_SESSION[$this->keepUserIDTag];
			$this->userType=$_SESSION[$this->keepUserTypeTag];
			$this->userChannel=$_SESSION[$this->keepUserChannelTag];
			$this->userName=$_SESSION[$this->keepUserNameTag];
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
			session_register($this->keepUserIDTag);
			$_SESSION[$this->keepUserIDTag] = $this->userID;
			
			session_register($this->keepUserTypeTag);
			$_SESSION[$this->keepUserTypeTag] = $this->userType;
			
			session_register($this->keepUserChannelTag);
			$_SESSION[$this->keepUserChannelTag] = $this->userChannel;
			
			session_register($this->keepUserNameTag);
			$_SESSION[$this->keepUserNameTag] = $this->userName;
			
			return 1;
		}
		else
			return -1;
	}
	//结束用户的会话状态
	function exitUser()
	{
		@session_unregister($this->keepUserIDTag);
		@session_unregister($this->keepUserTypeTag);
		@session_unregister($this->keepUserChannelTag);
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