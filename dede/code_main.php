<?php
error_reporting(0);
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typeunit_admin.php");
$userChannel = $cuserLogin->getUserChannel();
if(!isset($action)) $action = false;
if(!$action || $action=='info' || $action=='prompt'){
	if(!isset($_POST['step'])){
		include('./templets/code_main.htm');exit;
	}else{
		$lgpwd=md5($lgpwd);
		$verify=md5("actionloginlguser{$lguser}lgpwd{$lgpwd}{$_SERVER[HTTP_USER_AGENT]}");
		ObHeader("http://union.phpwind.com/index.php?action=login&lguser=$lguser&lgpwd=$lgpwd&verify=$verify");
	}
}elseif($action=='key'){
	if(!isset($cfg_siteid)||!$cfg_siteid){
		$dsql = new DedeSql(false);
		$rt = $dsql->GetOne("Select value From #@__sysconfig where varname='cfg_siteid'");
		if(!$rt['value']){
			$cfg_siteid = generatestr(16);
			$dsql->ExecuteNoneQuery("Insert Into #@__sysconfig(info,varname,value) Values('PW营销系统密钥一','cfg_siteid','$cfg_siteid')");

			$cfg_siteownerid = generatestr(18);
			$dsql->ExecuteNoneQuery("Insert Into #@__sysconfig(info,varname,value) Values('PW营销系统密钥二','cfg_siteownerid','$cfg_siteownerid')");

			$cfg_sitehash = '12'.SitStrCode(md5($cfg_siteid.$cfg_siteownerid),md5($cfg_siteownerid.$cfg_siteid));
			$dsql->ExecuteNoneQuery("Insert Into #@__sysconfig(info,varname,value) Values('PW营销唯一识别码','cfg_sitehash','$cfg_sitehash')");
		}
		$dsql->SetQuery("Select varname,value From #@__sysconfig order by aid asc");
		$dsql->Execute();
		$configfile = dirname(__FILE__)."/../include/config_hand.php";
		$configfile_bak = dirname(__FILE__)."/../include/config_hand_bak.php";
		@copy($configfile,$configfile_bak) or die('读取文件权限出错,目录文件'.$configfile.'不可写!<a href="code_main.php">返回</a>');
		$fp = @fopen($configfile,'w');
		@flock($fp,3);
		fwrite($fp,"<"."?php\r\n") or die('读取文件权限出错,目录文件'.$configfile.'不可写!<a href="code_main.php">返回</a>');
		while($row = $dsql->GetArray()){
			fwrite($fp,"\${$row['varname']} = '".str_replace("'","\\'",$row['value'])."';\r\n");
		}
		fwrite($fp,"?".">");
		fclose($fp);
	}
	include('./templets/code_main.htm');exit;
}

function generatestr($len) {
	mt_srand((double)microtime() * 1000000);
    $keychars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWYXZ";
	$maxlen = strlen($keychars)-1;
	$str = '';
	for ($i=0;$i<$len;$i++){
		$str .= $keychars[mt_rand(0,$maxlen)];
	}
	return substr(md5($str.time().$_SERVER["HTTP_USER_AGENT"]),0,$len);
}
function SitStrCode($string,$key,$action='ENCODE'){
	$string	= $action == 'ENCODE' ? $string : base64_decode($string);
	$len	= strlen($key);
	$code	= '';
	for($i=0; $i<strlen($string); $i++){
		$k		= $i % $len;
		$code  .= $string[$i] ^ $key[$k];
	}
	$code = $action == 'DECODE' ? $code : str_replace('=','',base64_encode($code));
	return $code;
}
ClearAllLink();
?>