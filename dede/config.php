<?php
define('DEDEADMIN', ereg_replace("[/\\]{1,}", '/', dirname(__FILE__) ) );
require_once(DEDEADMIN.'/../include/common.inc.php');
require_once(DEDEINC.'/userlogin.class.php');
header('Cache-Control:private');
$dsql->safeCheck = false;
$dsql->SetLongLink();

//获得当前脚本名称，如果你的系统被禁用了$_SERVER变量，请自行更改这个选项
$dedeNowurl = $s_scriptName = '';
$isUrlOpen = @ini_get('allow_url_fopen');
$dedeNowurl = GetCurUrl();
$dedeNowurls = explode('?', $dedeNowurl);
$s_scriptName = $dedeNowurls[0];
$cfg_remote_site = empty($cfg_remote_site)? 'N' : $cfg_remote_site;

//检验用户登录状态
$cuserLogin = new userLogin();
if($cuserLogin->getUserID()==-1)
{
	header("location:login.php?gotopage=".urlencode($dedeNowurl));
	exit();
}
if($cfg_dede_log=='Y')
{
	$s_nologfile = '_main|_list';
	$s_needlogfile = 'sys_|file_';
	$s_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
	$s_query = isset($dedeNowurls[1]) ? $dedeNowurls[1] : '';
	$s_scriptNames = explode('/',$s_scriptName);
	$s_scriptNames = $s_scriptNames[count($s_scriptNames)-1];
	$s_userip = GetIP();
	if( $s_method=='POST' || (!eregi($s_nologfile,$s_scriptNames) && $s_query!='') || eregi($s_needlogfile,$s_scriptNames) )
	{
		$inquery = "INSERT INTO `#@__log`(adminid,filename,method,query,cip,dtime)
             VALUES ('".$cuserLogin->getUserID()."','{$s_scriptNames}','{$s_method}','".addslashes($s_query)."','{$s_userip}','".time()."');";
		$dsql->ExecuteNoneQuery($inquery);
	}
}

//启用远程站点则创建FTP类
if($cfg_remote_site=='Y')
{
	require_once(DEDEINC.'/ftp.class.php');
	if(file_exists(DEDEDATA."/cache/inc_remote_config.php"))
	{
		require_once DEDEDATA."/cache/inc_remote_config.php";
	}
	if(empty($remoteuploads)) $remoteuploads = 0;
	if(empty($remoteupUrl)) $remoteupUrl = '';
	$config = array(
	  'hostname' => $GLOBALS['cfg_ftp_host'],
	  'username' => $GLOBALS['cfg_ftp_user'],
	  'password' => $GLOBALS['cfg_ftp_pwd'],
	  'debug' => 'TRUE'
	);
	$ftp = new FTP($config); 

	//初始化FTP配置
	if($remoteuploads==1){
		$ftpconfig = array(
			'hostname'=>$rmhost, 
			'port'=>$rmport,
			'username'=>$rmname,
			'password'=>$rmpwd
		);
	}
}

//管理缓存、管理员频道缓存
$cache1 = DEDEDATA.'/cache/inc_catalog_base.inc';
if(!file_exists($cache1)) UpDateCatCache();
$cacheFile = DEDEDATA.'/cache/admincat_'.$cuserLogin->userID.'.inc';
if(file_exists($cacheFile)) require_once($cacheFile);

//更新栏目缓存
function UpDateCatCache()
{
	global $dsql, $cfg_multi_site, $cache1, $cacheFile, $cuserLogin;
	$cache2 = DEDEDATA.'/cache/channelsonlist.inc';
	$cache3 = DEDEDATA.'/cache/channeltoplist.inc';
	$dsql->SetQuery("Select id,reid,channeltype,issend From `#@__arctype`");
	$dsql->Execute();
	$fp1 = fopen($cache1,'w');
	$phph = '?';
	$fp1Header = "<{$phph}php\r\nglobal \$cfg_Cs;\r\n\$cfg_Cs=array();\r\n";
	fwrite($fp1,$fp1Header);
	while($row=$dsql->GetObject())
	{
		fwrite($fp1,"\$cfg_Cs[{$row->id}]=array({$row->reid},{$row->channeltype},{$row->issend});\r\n");
	}
	fwrite($fp1,"{$phph}>");
	fclose($fp1);
	$cuserLogin->ReWriteAdminChannel();
	@unlink($cache2);
	@unlink($cache3);
}


function UpDateMemberModCache()
{
	global $dsql;
	$cachefile = DEDEDATA.'/cache/member_model.inc';

	$dsql->SetQuery("SELECT * FROM `#@__member_model` WHERE state='1'");
	$dsql->Execute();
	$fp1 = fopen($cachefile,'w');
	$phph = '?';
	$fp1Header = "<{$phph}php\r\nglobal \$_MemberMod;\r\n\$_MemberMod=array();\r\n";
	fwrite($fp1,$fp1Header);
	while($row=$dsql->GetObject())
	{
		fwrite($fp1,"\$_MemberMod[{$row->id}]=array('{$row->name}','{$row->table}');\r\n");
	}
	fwrite($fp1,"{$phph}>");
	fclose($fp1);
}


function DedeInclude($filename,$isabs=false)
{
	return $isabs ? $filename : DEDEADMIN.'/'.$filename;
}

//获取当前用户的ftp站点
function GetFtp($current='', $formname='')
{
	global $dsql;
	$formname = empty($formname)? 'serviterm' : $formname;
	$cuserLogin = new userLogin();
	$row=$dsql->GetOne("Select servinfo From `#@__multiserv_config`");
	$row['servinfo']=trim($row['servinfo']);
	if(!empty($row['servinfo'])){
		$servinfos = explode("\n", $row['servinfo']);
		$select="";
		echo '<select name="'.$formname.'" size="1" id="serviterm">';
		$i=0;
		foreach($servinfos as $servinfo){
			$servinfo = trim($servinfo);
			list($servname,$servurl,$servport,$servuser,$servpwd,$userlist) = explode('|',$servinfo);
			$servname = trim($servname);
			$servurl = trim($servurl);
			$servport = trim($servport);
			$servuser = trim($servuser);
			$servpwd = trim($servpwd);
			$userlist = trim($userlist);   
			$checked = ($current == $i)? '  selected="selected"' : '';
			if(strstr($userlist,$cuserLogin->getUserName())){
	       $select.="<option value='".$servurl.",".$servuser.",".$servpwd."'{$checked}>".$servname."</option>";  
			}
			$i++;
		} 
		echo  $select."</select>";
	}
}
?>