<?php
if(!defined('DEDEINC')) exit('Request Error!');
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
		if($n=='')
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

//检测用户是否有权限操作某栏目
function CheckCatalog($cid, $msg)
{
	global $cfg_admin_channel, $admin_catalogs;
	if($cfg_admin_channel=='all' || TestAdmin())
	{
		return true;
	}
	if( !in_array($cid, $admin_catalogs) )
	{
		ShowMsg(" $msg <br/><br/><a href='javascript:history.go(-1);'>点击此返回上一页&gt;&gt;</a>",'javascript:;');
		exit();
	}
	return true;
}

/*****************************************
发布文档临时附件信息缓存、发文档前先清空附件信息
发布文档时涉及的附件保存到缓存里，完成后把它与文档关连
******************************************/
function AddMyAddon($fid, $filename)
{
	$cacheFile = DEDEDATA.'/cache/addon-'.session_id().'.inc';
	if(!file_exists($cacheFile))
	{
		$fp = fopen($cacheFile, 'w');
		fwrite($fp, '<'.'?php'."\r\n");
		fwrite($fp, "\$myaddons = array();\r\n");
		fwrite($fp, "\$maNum = 0;\r\n");
		fclose($fp);
	}
	include($cacheFile);
	$fp = fopen($cacheFile, 'a');
	$arrPos = $maNum;
	$maNum++;
	fwrite($fp, "\$myaddons[\$maNum] = array('$fid', '$filename');\r\n");
	fwrite($fp, "\$maNum = $maNum;\r\n");
	fclose($fp);
}
//清理附件，如果关连的文档ID，先把上一批附件传给这个文档ID
function ClearMyAddon($aid=0, $title='')
{
	global $dsql;
	$cacheFile = DEDEDATA.'/cache/addon-'.session_id().'.inc';
	$_SESSION['bigfile_info'] = array();
	$_SESSION['file_info'] = array();
	if(!file_exists($cacheFile))
	{
		return ;
	}
	//把附件与文档关连
	if(!empty($aid))
	{
		include($cacheFile);
		foreach($myaddons as $addons)
		{
			if(!empty($title)) {
				$dsql->ExecuteNoneQuery("Update `#@__uploads` set arcid='$aid',title='$title' where aid='{$addons[0]}'");
			}
			else {
				$dsql->ExecuteNoneQuery("Update `#@__uploads` set arcid='$aid' where aid='{$addons[0]}' ");
			}
		}
	}
	@unlink($cacheFile);
}

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
	var $keepUserIDTag = 'dede_admin_id';
	var $keepUserTypeTag = 'dede_admin_type';
	var $keepUserChannelTag = 'dede_admin_channel';
	var $keepUserNameTag = 'dede_admin_name';
	var $keepUserPurviewTag = 'dede_admin_purview';
	var $keepAdminStyleTag = 'dede_admin_style';
	var $adminStyle = 'dedecms';

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
		if($this->userID != '' && $this->userType != '')
		{
			global $admincachefile,$adminstyle;
			if(empty($adminstyle)) $adminstyle = 'dedecms';

			@session_register($this->keepUserIDTag);
			$_SESSION[$this->keepUserIDTag] = $this->userID;

			@session_register($this->keepUserTypeTag);
			$_SESSION[$this->keepUserTypeTag] = $this->userType;

			@session_register($this->keepUserChannelTag);
			$_SESSION[$this->keepUserChannelTag] = $this->userChannel;

			@session_register($this->keepUserNameTag);
			$_SESSION[$this->keepUserNameTag] = $this->userName;

			@session_register($this->keepUserPurviewTag);
			$_SESSION[$this->keepUserPurviewTag] = $this->userPurview;

			@session_register($this->keepAdminStyleTag);
			$_SESSION[$this->keepAdminStyleTag] = $adminstyle;

			PutCookie('DedeUserID', $this->userID, 3600 * 24, '/');
			PutCookie('DedeLoginTime', time(), 3600 * 24, '/');
			
			$this->ReWriteAdminChannel();
			
			return 1;
		}
		else
		{
			return -1;
		}
	}
	
	//重写用户权限频道
	function ReWriteAdminChannel()
	{
		//$this->userChannel
		$cacheFile = DEDEDATA.'/cache/admincat_'.$this->userID.'.inc';
		//管理员管理的频道列表
		$typeid = trim($this->userChannel);
		if( empty($typeid) || $this->getUserType() >= 10 ) {
				$firstConfig = "\$cfg_admin_channel = 'all';\r\n\$admin_catalogs = array();\r\n";
		}
		else {
				$firstConfig = "\$cfg_admin_channel = 'array';\r\n";
		}
		$fp = fopen($cacheFile, 'w');
		fwrite($fp, '<'.'?php'."\r\n");
		fwrite($fp, $firstConfig);
		if( !empty($typeid) )
		{
			 $typeids = explode(',', $typeid);
			 $typeid = '';
			 foreach($typeids as $tid)
			 {
			 		$typeid .= ( $typeid=='' ? GetSonIdsUL($tid) : ','.GetSonIdsUL($tid) );
			 }
			 $typeids = explode(',', $typeid);
			 $typeidsnew = array_unique($typeids);
			 $typeid = join(',', $typeidsnew);
			 fwrite($fp, "\$admin_catalogs = array($typeid);\r\n");
		}
		fwrite($fp, '?'.'>');
		fclose($fp);
	}

	//结束用户的会话状态
	function exitUser()
	{
		ClearMyAddon();
		@session_unregister($this->keepUserIDTag);
		@session_unregister($this->keepUserTypeTag);
		@session_unregister($this->keepUserChannelTag);
		@session_unregister($this->keepUserNameTag);
		@session_unregister($this->keepUserPurviewTag);
		DropCookie('dedeAdmindir');
		DropCookie('DedeUserID');
		DropCookie('DedeLoginTime');
		$_SESSION = array();
	}

	//获得用户管理频道的值
	function getUserChannel()
	{
		if($this->userChannel != '')
		{
			return $this->userChannel;
		}
		else
		{
			return '';
		}
	}

	//获得用户的权限值
	function getUserType()
	{
		if($this->userType != '')
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
		if($this->userID != '')
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
		if($this->userName != '')
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

//获得某id的所有下级id
function GetSonIdsUL($id, $channel=0, $addthis=true)
{
	global $_Cs;
	$GLOBALS['idArray'] = array();
	if( !is_array($_Cs) )
	{
		require_once(DEDEROOT."/data/cache/inc_catalog_base.inc");
	}
	GetSonIdsLogicUL($id,$_Cs,$channel,$addthis);
	$rquery = join(',', $GLOBALS['idArray']);
	return $rquery;
}

//递归逻辑
function GetSonIdsLogicUL($id,$sArr,$channel=0,$addthis=false)
{
	if($id!=0 && $addthis)
	{
		$GLOBALS['idArray'][$id] = $id;
	}
	foreach($sArr as $k=>$v)
	{
		if( $v[0]==$id && ($channel==0 || $v[1]==$channel ))
		{
			GetSonIdsLogicUL($k,$sArr,$channel,true);
		}
	}
}

?>