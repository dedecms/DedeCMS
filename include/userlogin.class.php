<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

session_start();

//检验用户是否有权使用某功能
function TestPurview($n)
{
	$rs = false;
	$purview = $GLOBALS['cuserLogin']->getPurview();
	if(eregi('admin_AllowAll',$purview))
	{
		return true;
	}
	if($n=='')
	{
		return true;
	}
	if(!isset($GLOBALS['groupRanks']))
	{
		$GLOBALS['groupRanks'] = explode(' ',$purview);
	}
	$ns = explode(',',$n);
	foreach($ns as $n)
	{
		//只要找到一个匹配的权限，即可认为用户有权访问此页面
		if($n=="")
		{
			continue;
		}
		if(in_array($n,$GLOBALS['groupRanks']))
		{
			$rs = true; break;
		}
	}
	return $rs;
}

function CheckPurview($n)
{
	if(!TestPurview($n))
	{
		ShowMsg("对不起，你没有权限执行此操作！<br/><br/><a href='javascript:history.go(-1);'>点击此返回上一页&gt;&gt;</a>",'javascript:;');
		exit();
	}
}

//是否没权限限制(超级管理员)
function TestAdmin()
{
	$purview = $GLOBALS['cuserLogin']->getPurview();
	if(eregi('admin_AllowAll',$purview))
	{
		return true;
	}
	else
	{
		return false;
	}
}

$DedeUserCatalogs = Array();

//获得用户授权的所有栏目ID
function GetMyCatalogs($dsql,$cid)
{
	$GLOBALS['DedeUserCatalogs'][] = $cid;
	$dsql->SetQuery("Select id From #@__arctype where reid='$cid'");
	$dsql->Execute($cid);
	while($row = $dsql->GetObject($cid))
	{
		GetMyCatalogs($dsql,$row->id);
	}
}

function MyCatalogs()
{
	global $dsql;
	if(count($GLOBALS['DedeUserCatalogs'])==0)
	{
		GetMyCatalogs($dsql,$GLOBALS['cuserLogin']->getUserChannel());
	}
	return $GLOBALS['DedeUserCatalogs'];
}

//检测用户是否有权限操作某栏目
function CheckCatalog($cid,$msg)
{
	if($GLOBALS['cuserLogin']->getUserChannel()=="0"||TestAdmin())
	{
		return true;
	}
	if(!in_array($cid,MyCatalogs()))
	{
		ShowMsg(" $msg <br/><br/><a href='javascript:history.go(-1);'>点击此返回上一页&gt;&gt;</a>",'javascript:;');
		exit();
	}
	return true;
}

$admincachefile = DEDEDATA.'/admin_'.cn_substr(md5($cfg_cookie_encode),24).'.php';
if(!file_exists($admincachefile))
{
	$fp = fopen($admincachefile,'w');
	fwrite($fp,'<'.'?php $admin_path ='." ''; ?".'>');
	fclose($fp);
}
require_once($admincachefile);

//登录类
class userLogin
{
	var $userName = '';
	var $userPwd = '';
	var $userID = '';
	var $adminDir = '';
	var $userType = '';
	var $userChannel = '';
	var $userPurview = '';
	var $keepUserIDTag = "dede_admin_id";
	var $keepUserTypeTag = "dede_admin_type";
	var $keepUserChannelTag = "dede_admin_channel";
	var $keepUserNameTag = "dede_admin_name";
	var $keepUserPurviewTag = "dede_admin_purview";
	var $keepAdminStyleTag = "dede_admin_style";
	var $adminStyle = "dedecms";

	//php5构造函数
	function __construct($admindir='')
	{
		global $admin_path;
		if(isset($_SESSION[$this->keepUserIDTag]))
		{
			$this->userID = $_SESSION[$this->keepUserIDTag];
			$this->userType = $_SESSION[$this->keepUserTypeTag];
			$this->userChannel = $_SESSION[$this->keepUserChannelTag];
			$this->userName = $_SESSION[$this->keepUserNameTag];
			$this->userPurview = $_SESSION[$this->keepUserPurviewTag];
			$this->adminStyle = $_SESSION[$this->keepAdminStyleTag];
		}

		if($admindir!='')
		{
			$this->adminDir = $admindir;
		}
		else
		{
			$this->adminDir = $admin_path;
		}
	}

	function userLogin($admindir='')
	{
		$this->__construct($admindir);
	}

	//检验用户是否正确
	function checkUser($username,$userpwd)
	{
		global $dsql;

		//只允许用户名和密码用0-9,a-z,A-Z,'@','_','.','-'这些字符
		$this->userName = ereg_replace("[^0-9a-zA-Z_@!\.-]",'',$username);
		$this->userPwd = ereg_replace("[^0-9a-zA-Z_@!\.-]",'',$userpwd);
		$pwd = substr(md5($this->userPwd),5,20);
		$dsql->SetQuery("Select admin.*,atype.purviews From `#@__admin` admin left join `#@__admintype` atype on atype.rank=admin.usertype where admin.userid like '".$this->userName."' limit 0,1");
		$dsql->Execute();
		$row = $dsql->GetObject();
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
			$loginip = GetIP();
			$this->userID = $row->id;
			$this->userType = $row->usertype;
			$this->userChannel = $row->typeid;
			$this->userName = $row->uname;
			$this->userPurview = $row->purviews;
			$inquery = "update `#@__admin` set loginip='$loginip',logintime='".time()."' where id='".$row->id."'";
			$dsql->ExecuteNoneQuery($inquery);
			$sql = "update #@__member set logintime=".time().", loginip='$loginip' where mid=".$row->id;
			$dsql->ExecuteNoneQuery($sql);
			return 1;
		}
	}

	//保持用户的会话状态
	//成功返回 1 ，失败返回 -1
	function keepUser()
	{
		if($this->userID!=""&&$this->userType!="")
		{
			global $admincachefile,$adminstyle;
			if(empty($adminstyle))
			{
				$adminstyle = 'dedecms';
			}

			//session_register($this->keepUserIDTag);
			$_SESSION[$this->keepUserIDTag] = $this->userID;

			//session_register($this->keepUserTypeTag);
			$_SESSION[$this->keepUserTypeTag] = $this->userType;

			//session_register($this->keepUserChannelTag);
			$_SESSION[$this->keepUserChannelTag] = $this->userChannel;

			//session_register($this->keepUserNameTag);
			$_SESSION[$this->keepUserNameTag] = $this->userName;

			//session_register($this->keepUserPurviewTag);
			$_SESSION[$this->keepUserPurviewTag] = $this->userPurview;

			//session_register($this->keepAdminStyleTag);
			$_SESSION[$this->keepAdminStyleTag] = $adminstyle;

			//PutCookie('dedeAdmindir',$this->adminDir,3600 * 24,'/');
			PutCookie('DedeUserID',$this->userID,3600 * 24,'/');
			PutCookie('DedeLoginTime',time(),3600 * 24,'/');
			$fp = fopen($admincachefile,'w');
			fwrite($fp,'<'.'?php $admin_path ='." '{$this->adminDir}'; ?".'>');
			fclose($fp);
			return 1;
		}
		else
		{
			return -1;
		}
	}

	//结束用户的会话状态
	function exitUser()
	{
		/*
		@session_unregister($this->keepUserIDTag);
		@session_unregister($this->keepUserTypeTag);
		@session_unregister($this->keepUserChannelTag);
		@session_unregister($this->keepUserNameTag);
		@session_unregister($this->keepUserPurviewTag);
		*/
		DropCookie('dedeAdmindir');
		DropCookie('DedeUserID');
		DropCookie('DedeLoginTime');
		$_SESSION = array();
	}

	//获得用户管理频道的值
	function getUserChannel()
	{
		if($this->userChannel!='')
		{
			return $this->userChannel;
		}
		else
		{
			return -1;
		}
	}

	//获得用户的权限值
	function getUserType()
	{
		if($this->userType!='')
		{
			return $this->userType;
		}
		else
		{
			return -1;
		}
	}

	function getUserRank()
	{
		return $this->getUserType();
	}

	//获得用户的ID
	function getUserID()
	{
		if($this->userID!='')
		{
			return $this->userID;
		}
		else
		{
			return -1;
		}
	}

	//获得用户的笔名
	function getUserName()
	{
		if($this->userName!='')
		{
			return $this->userName;
		}
		else
		{
			return -1;
		}
	}

	//用户权限表
	function getPurview()
	{
		return $this->userPurview;
	}
}

?>