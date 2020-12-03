<?php
require_once(dirname(__FILE__)."/config_base.php");
session_start();
$GLOBALS['groupRanks'] = '';
//检验用户是否有权使用某功能
function TestPurview($n)
{
	global $groupRanks;
	$rs = false;
	$purview = $GLOBALS['cuserLogin']->getPurview();
  if(eregi('admin_AllowAll',$purview)) return true;
  if($n=='') return true;
  if(!is_array($groupRanks)){ $groupRanks = explode(' ',$purview); }
	$ns = explode(',',$n);
	foreach($ns as $v){ //只要找到一个匹配的权限，即可认为用户有权访问此页面
	  if($v=='') continue;
	  if(in_array($v,$groupRanks)){ $rs = true; break; }
  }
  return $rs;
}

function CheckPurview($n)
{
  if(!TestPurview($n)){
  	ShowMsg("对不起，你没有权限执行此操作！<br/><br/><a href='javascript:history.go(-1);'>点击此返回上一页&gt;&gt;</a>",'javascript:;');
  	exit();
  }
}

//是否没权限限制(超级管理员)
function TestAdmin(){
	$purview = $GLOBALS['cuserLogin']->getPurview();
  if(eregi('admin_AllowAll',$purview)) return true;
  else return false;
}

$DedeUserCatalogs = Array();
//获得用户授权的所有栏目ID
function GetMyCatalogs($dsql,$cid)
{
	$GLOBALS['DedeUserCatalogs'][] = $cid;
	$dsql->SetQuery("Select ID From #@__arctype where reID='$cid'");
	$dsql->Execute($cid);
	while($row = $dsql->GetObject($cid)){
		GetMyCatalogs($dsql,$row->ID);
	}
}

function MyCatalogs(){
	global $dsql;
	if(count($GLOBALS['DedeUserCatalogs'])==0){
		 if(!is_array($dsql)) $dsql = new DedeSql(true);
		 $ids = $GLOBALS['cuserLogin']->getUserChannel();
		 $ids = ereg_replace('[><]','',str_replace('><',',',$ids));
		 $ids = explode(',',$ids);
		 if(is_array($ids))
		 {
		    foreach($ids as $id)
		    {  GetMyCatalogs($dsql,$id);  }
		 }
	}
	return $GLOBALS['DedeUserCatalogs'];
}

function MyCatalogInArr()
{
	 $r = '';
	 $arr = MyCatalogs();
	 if(is_array($arr))
	 {

		  foreach($arr as $v){
			  if($r=='') $r .= $v;
			  else $r .= ','.$v;
		  }
	 }
	 return $r;
}

//检测用户是否有权限操作某栏目
function CheckCatalog($cid,$msg)
{
	if(!CheckCatalogTest($cid)){
		ShowMsg(" $msg <br/><br/><a href='javascript:history.go(-1);'>点击此返回上一页&gt;&gt;</a>",'javascript:;');
  	exit();
	}
  return true;
}

//检测用户是否有权限操作某栏目
function CheckCatalogTest($cid,$uc=''){
	if(empty($uc)) $uc = $GLOBALS['cuserLogin']->getUserChannel();
	if(empty($uc)||$uc==-1||TestAdmin()) return true;
	if(!in_array($cid,MyCatalogs())) return false;
  else return true;
}


//登录类
class userLogin
{
	var $userName = "";
	var $userPwd = "";
	var $userID = "";
	var $userType = "";
	var $userChannel = "";
	var $userPurview = "";
	var $keepUserIDTag = "dede_admin_id";
	var $keepUserTypeTag = "dede_admin_type";
	var $keepUserChannelTag = "dede_admin_channel";
	var $keepUserNameTag = "dede_admin_name";
	var $keepUserPurviewTag = "dede_admin_purview";
	//php5构造函数
	function __construct()
 	{
 		if(isset($_SESSION[$this->keepUserIDTag])){
			$this->userID=$_SESSION[$this->keepUserIDTag];
			$this->userType=$_SESSION[$this->keepUserTypeTag];
			$this->userChannel=$_SESSION[$this->keepUserChannelTag];
			$this->userName=$_SESSION[$this->keepUserNameTag];
			$this->userPurview=$_SESSION[$this->keepUserPurviewTag];
	  }
  }
	function userLogin(){
		$this->__construct();
	}
	//检验用户是否正确
	function checkUser($username,$userpwd)
	{
		//只允许用户名和密码用0-9,a-z,A-Z,'@','_','.','-'这些字符
		$this->userName = ereg_replace("[^0-9a-zA-Z_@\!\.-]","",$username);
		$this->userPwd = ereg_replace("[^0-9a-zA-Z_@\!\.-]","",$userpwd);
		$pwd = substr(md5($this->userPwd),0,24);
		$dsql = new DedeSql(false);
		$dsql->SetQuery("Select * From #@__admin where userid='".$this->userName."' limit 0,1");
		$dsql->Execute();
		$row = $dsql->GetObject();
		if(!isset($row->pwd)){
			$dsql->Close();
			return -1;
		}
		else if($pwd!=$row->pwd){
			$dsql->Close();
			return -2;
		}
		else{
			$loginip = GetIP();
			$this->userID = $row->ID;
			$this->userType = $row->usertype;
			$this->userChannel = $row->typeid;
			$this->userName = $row->uname;
			$groupSet = $dsql->GetOne("Select * From #@__admintype where rank='".$row->usertype."'");
			$this->userPurview = $groupSet['purviews'];
			$dsql->SetQuery("update #@__admin set loginip='$loginip',logintime='".strftime("%Y-%m-%d %H:%M:%S",time())."' where ID='".$row->ID."'");
			$dsql->ExecuteNoneQuery();
			$dsql->Close();
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

			session_register($this->keepUserPurviewTag);
			$_SESSION[$this->keepUserPurviewTag] = $this->userPurview;

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
		@session_unregister($this->keepUserNameTag);
		@session_unregister($this->keepUserPurviewTag);
		session_destroy();
	}
	//-----------------------------
	//获得用户管理频道的值
	//-----------------------------
	function getUserChannel(){
		if($this->userChannel!="") return $this->userChannel;
		else return -1;
	}
	//获得用户的权限值
	function getUserType(){
		if($this->userType!="") return $this->userType;
		else return -1;
	}
	function getUserRank(){
		return $this->getUserType();
	}
	//获得用户的ID
	function getUserID(){
		if($this->userID!="") return $this->userID;
		else return -1;
	}
	//获得用户的笔名
	function getUserName(){
		if($this->userName!="") return $this->userName;
		else return -1;
	}
	//用户权限表
	function getPurview(){
		return $this->userPurview;
	}
}
?>