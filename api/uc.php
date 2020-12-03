<?php
include_once (dirname(__FILE__)."/../include/common.inc.php");
define('IN_DISCUZ', FALSE);

define('UC_CLIENT_VERSION', '1.5.0');	//note UCenter 版本标识
define('UC_CLIENT_RELEASE', '20081031');

define('API_DELETEUSER', 1);		//note 用户删除 API 接口开关
define('API_RENAMEUSER', 1);		//note 用户改名 API 接口开关
define('API_GETTAG', 1);		//note 获取标签 API 接口开关
define('API_SYNLOGIN', 1);		//note 同步登录 API 接口开关
define('API_SYNLOGOUT', 1);		//note 同步登出 API 接口开关
define('API_UPDATEPW', 1);		//note 更改用户密码 开关
define('API_UPDATEBADWORDS', 1);	//note 更新关键字列表 开关
define('API_UPDATEHOSTS', 1);		//note 更新域名解析缓存 开关
define('API_UPDATEAPPS', 1);		//note 更新应用列表 开关
define('API_UPDATECLIENT', 1);		//note 更新客户端缓存 开关
define('API_UPDATECREDIT', 1);		//note 更新用户积分 开关
define('API_GETCREDITSETTINGS', 1);	//note 向 UCenter 提供积分设置 开关
define('API_GETCREDIT', 1);		//note 获取用户的某项积分 开关
define('API_UPDATECREDITSETTINGS', 1);	//note 更新应用积分设置 开关

define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');

define('UC_CLIENT_ROOT', DEDEROOT.'/uc_client');

//note 普通的 http 通知方式
if(!defined('IN_UC'))
{

	error_reporting(0);
	set_magic_quotes_runtime(0);
	defined('MAGIC_QUOTES_GPC') || define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

	$_DCACHE = $get = $post = array();

	$code = @$_GET['code'];

	parse_str(_authcode($code, 'DECODE', UC_KEY), $get);
	
	if(MAGIC_QUOTES_GPC)
	{
		$get = _stripslashes($get);
	}

	$timestamp = time();
	if($timestamp - $get['time'] > 3600) {
		exit('Authracation has expiried');
	}
	if(empty($get)) {
		exit('Invalid Request');
	}
	$action = $get['action'];

	require_once UC_CLIENT_ROOT.'/lib/xml.class.php';
	$post = xml_unserialize(file_get_contents('php://input'));

	if(in_array($get['action'], array('test', 'DELETE user', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcreditsettings', 'updatecreditsettings')))
	{
		$uc_note = new uc_note();
		exit($uc_note->$get['action']($get, $post));
	}else{
		exit(API_RETURN_FAILED);
	}

//note include 通知方式
} else {

	exit('Invalid Request');
}

class uc_note
{

	var $dbconfig = '';
	var $db = '';
	var $appdir = '';
	var $tablepre = 'dede_';
	
	function _serialize($arr, $htmlon = 0)
	{
		if(!function_exists('xml_serialize'))
		{
			include_once UC_CLIENT_ROOT.'/lib/xml.class.php';
		}
		return xml_serialize($arr, $htmlon);
	}

	function uc_note()
	{
		$this->appdir = DEDEROOT;
		$this->dbconfig = DEDEINC.'/common.inc.php';
		$this->db = $GLOBALS['dsql'];
		$this->tablepre = $GLOBALS['cfg_dbprefix'];
	}
	
	function get_uids($uids)
	{
		include UC_CLIENT_ROOT.'/client.php';
		
		$members = explode(",", $uids);
		empty($members) && exit(API_RETURN_FORBIDDEN);
		
		$members_username = array();
		
		foreach($members as $id)
		{
			$row = uc_get_user($id,1);
			$members_username[] =  $row[1];		
		}
		
		$comma_temps = implode(",", $members_username);
		
		empty($comma_temps) && exit(API_RETURN_FORBIDDEN);
		
		$comma_uids = array();
		
		$row = $this->db->SetQuery("SELECT mid FROM `#@__member` WHERE userid IN ($comma_temps)");
		$this->db->Execute();
		while($row = $this->db->GetArray())
		{
			$comma_uids[] = $row['mid'];
		}
		
		empty($comma_uids) && exit(API_RETURN_FORBIDDEN);
		
		return implode(",", $comma_uids);
	}

	function test($get, $post)
	{
		return API_RETURN_SUCCEED;
	}

	function deleteuser($get, $post)
	{
		$uids = $this->get_uids($get['ids']);
		!API_DELETEUSER && exit(API_RETURN_FORBIDDEN);

		//note 用户删除 API 接口
		$rs = $this->db->ExecuteNoneQuery2("DELETE FROM `#@__member` WHERE mid IN ($uids) AND matt<>10 limit 1");
		if($rs > 0)
		{
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_tj` WHERE mid IN ($uids) limit 1");
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_space` WHERE mid IN ($uids) limit 1");
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_company` WHERE mid IN ($uids) limit 1");
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_person` WHERE mid IN ($uids) limit 1");
		
			//删除用户相关数据
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_stow` WHERE mid IN ($uids) ");
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_flink` WHERE mid IN ($uids) ");
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_guestbook` WHERE mid IN ($uids) ");
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_operation` WHERE mid IN ($uids) ");
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_pms` WHERE toid IN ($uids) OR fromid IN ($uids) ");
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_friends` WHERE mid IN ($uids) OR fid IN ($uids) ");
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__member_vhistory` WHERE mid IN ($uids) OR vid IN ($uids) ");
			$this->db->ExecuteNoneQuery("DELETE FROM `#@__feedback` WHERE mid IN ($uids) ");
			$this->db->ExecuteNoneQuery("UPDATE `#@__archives` SET mid='0' WHERE mid IN ($uids)");
		}
		else
		{
			exit(API_RETURN_FORBIDDEN);
		}

		return API_RETURN_SUCCEED;
	}

	function renameuser($get, $post)
	{
		$uids = $this->get_uids($get['ids']);
		
		
		$usernameold = $get['oldusername'];
		$usernamenew = $get['newusername'];
		if(!API_RENAMEUSER)
		{
			return API_RETURN_FORBIDDEN;
		}

		//note 获取标签 API 接口
		$rs = $this->db->ExecuteNoneQuery2("UPDATE `#@__member` SET userid='$usernamenew' WHERE userid='$usernamenew' AND matt<>10 limit 1");
		if($rs > 0)
		{
			$this->db->ExecuteNoneQuery("UPDATE `#@__archives` SET writer='$usernamenew' WHERE writer='$usernamenew'");
			$this->db->ExecuteNoneQuery("UPDATE `#@__member_pms` SET floginid=REPLACE(floginid, '\t$usernameold', '\t$usernamenew'),tologinid=REPLACE(tologinid, '\t$usernameold', '\t$usernamenew')");
			
			
			$row = $this->db->GetOne("SHOW TABLE STATUS");
			$db_tables = $row['Name']; unset($row);
			
			if(in_array($this->tablepre.'guestbook',$db_tables))
			{
				$this->db->ExecuteNoneQuery("UPDATE `#@__guestbook` SET uname='$usernamenew' WHERE uname='$usernamenew'");
			}
			
			if(in_array($this->tablepre.'story_books',$db_tables))
			{
				$this->db->ExecuteNoneQuery("UPDATE `#@__story_books` SET author='$usernamenew' WHERE author='$usernamenew'");
			}
			
			if(in_array($this->tablepre.'groups',$db_tables))
			{
				$this->db->ExecuteNoneQuery("UPDATE `#@__groups` SET creater='$usernamenew' WHERE creater='$usernamenew'");
				$this->db->ExecuteNoneQuery("UPDATE `#@__group_threads` SET author='$usernamenew' WHERE author='$usernamenew'");
				$this->db->ExecuteNoneQuery("UPDATE `#@__group_user` SET username='$usernamenew' WHERE username='$usernamenew'");
				$this->db->ExecuteNoneQuery("UPDATE `#@__group_posts` SET author='$usernamenew' WHERE author='$usernamenew'");
				$this->db->ExecuteNoneQuery("UPDATE `#@__group_guestbook` SET uname='$usernamenew' WHERE uname='$usernamenew'");
				$this->db->ExecuteNoneQuery("UPDATE `#@__groups` SET ismaster=REPLACE(ismaster, '\t$usernameold', '\t$usernamenew')");
			}
			
			return API_RETURN_SUCCEED;
		}
		else
		{
			return API_RETURN_FORBIDDEN;
		}
	}

	function gettag($get, $post)
	{
		$name = $get['id'];
		if(!API_GETTAG)
		{
			return API_RETURN_FORBIDDEN;
		}

		//note 获取标签 API 接口

		$name = trim($name);
		if(empty($name) || !preg_match('/^([\x7f-\xff_-]|\w|\s)+$/', $name) || strlen($name) > 20)
		{
			return API_RETURN_FAILED;
		}

		$row = $this->db->GetOne("SELECT `total`,`id` FROM `#@__tagindex` WHERE `tag`='$name'");
		if(!is_array($row))
		{
			return API_RETURN_FAILED;
		}
		
		$tpp = $row['total'] > 10 ? 10 : $row['total'];		
		
		$ids = array();
		
		$this->db->SetQuery("SELECT aid FROM `#@__taglist` WHERE `tid`='$row[id]' AND arcrank>-1");
		$this->db->Execute();
		while($row = $this->db->GetArray())
		{
			$ids[] = $row['aid'];			
		}
		
		if(empty($ids))
		{
			return API_RETURN_FAILED;
		}
		
		$aids = implode(",", $ids);
		
		include_once DEDEINC.'/channelunit.func.php';
		
		$archives_list = array();		
		$this->db->SetQuery("SELECT arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath 
	FROM `#@__archives` arc LEFT JOIN `#@__arctype` tp ON arc.typeid=tp.id WHERE arc.id IN($aids) ORDER BY id DESC LIMIT $tpp");
		$this->db->Execute();
		while($row = $this->db->GetArray())
		{
			$row['url'] = GetFileUrl($row['id'],$row['typeid'],$row['senddate'],$row['title'],$row['ismake'],$row['arcrank'],$row['namerule'],$row['typedir'],$row['money'],$row['filename'],$row['moresite'],$row['siteurl'],$row['sitepath']);

			$row['url'] = !ereg('http:',$row['url']) ? $GLOBALS['cfg_basehost'].$row['url'] : $row['url'];
			
			if(!empty($row['url']))
			{
				$archives_list[] = array('title' => $row['title'],'writer' => $row['writer'],'pubdate' => $row['pubdate'],'url' => $row['url']);
			}
		}

		$return = array($name, $archives_list);
		return $this->_serialize($return, 1);
	}

	function synlogin($get, $post)
	{
		$uid = $get['uid'];
		$username = $get['username'];
		if(!API_SYNLOGIN)
		{
			return API_RETURN_FORBIDDEN;
		}

		//note 同步登录 API 接口
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$result = $this->db->GetOne("SELECT mid,pwd FROM `#@__member` WHERE `userid` like '$username' AND matt<>10");
		if(is_array($result))
		{
			include_once DEDEINC.'/memberlogin.class.php';
			$cfg_ml = new MemberLogin(86400);
			$cfg_ml->PutLoginInfo($result['mid']);
		}
	}

	function synlogout($get, $post)
	{
		if(!API_SYNLOGOUT)
		{
			return API_RETURN_FORBIDDEN;
		}

		//note 同步登出 API 接口
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		include_once DEDEINC.'/memberlogin.class.php';
		$cfg_ml = new MemberLogin();
		$cfg_ml->ExitCookie();
	}

	function updatepw($get, $post)
	{
		if(!API_UPDATEPW)
		{
			return API_RETURN_FORBIDDEN;
		}
		$username = $get['username'];
		$password = $get['password'];
		
		//note 修改密码 API 接口
		$newpw = md5($password);
		$this->db->ExecuteNoneQuery("UPDATE `#@__member` SET `pwd`='$newpw' WHERE `userid`='$username'");
		return API_RETURN_SUCCEED;
	}

	function updatebadwords($get, $post)
	{
		if(!API_UPDATEBADWORDS)
		{
			return API_RETURN_FORBIDDEN;
		}

		$row = $this->db->GetOne("SELECT `value` FROM `#@__sysconfig` WHERE `varname`='cfg_replacestr'");
		
		$badwords = isset($row['value']) ? explode(",", $row['value']) : array();
		
		if(is_array($post))
		{
			foreach($post as $k => $v)
			{
				if(in_array($v['find'],$badwords)) continue;
				$badwords[] = $v['find'];
			}
		}

		$badwords_comma = !empty($badwords) ? implode(",", $badwords) : '';
		

		$this->db->ExecuteNoneQuery("UPDATE `#@__sysconfig` SET `value`='$badwords_comma' WHERE `varname`='cfg_replacestr'");
		
		$cachefile = DEDEDATA.'/config.cache.inc.php';
		
		if(!is_writeable($cachefile))
		{
			return API_RETURN_FORBIDDEN;
		}

		$fp = fopen($cachefile, 'w');
		$this->db->SetQuery("SELECT `varname`,`type`,`value`,`groupid` From `#@__sysconfig` order by aid asc ");
		$this->db->Execute();
		$s = '<?php'."\r\n";
		while($row = $this->db->GetArray())
		{
			$s .= '$'.$row['varname'].' = '.($row['type']=='number' ? $row['value'] : "'".str_replace("'",'',$row['value'])."'").";\r\n";
		}
		$s .= '?>';
		fwrite($fp, $s);
		fclose($fp);
		return API_RETURN_SUCCEED;
	}

	function updatehosts($get, $post)
	{
		if(!API_UPDATEHOSTS)
		{
			return API_RETURN_FORBIDDEN;
		}
		//note 理新HOST缓存 API 接口
		$cachefile = UC_CLIENT_ROOT.'/data/cache/hosts.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'hosts\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);
		return API_RETURN_SUCCEED;
	}

	function updateapps($get, $post)
	{
		if(!API_UPDATEAPPS)
		{
			return API_RETURN_FORBIDDEN;
		}
		$UC_API = $post['UC_API'];

		//note 写 app 缓存文件
		$cachefile = UC_CLIENT_ROOT.'/data/cache/apps.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'apps\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

		return API_RETURN_SUCCEED;
	}

	function updateclient($get, $post)
	{
		if(!API_UPDATECLIENT)
		{
			return API_RETURN_FORBIDDEN;
		}
		$cachefile = UC_CLIENT_ROOT.'/data/cache/settings.php';
		$fp = fopen($cachefile, 'w');
		$s = '<?php'."\r\n";
		$s .= '$_CACHE[\'settings\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);
		
		return API_RETURN_SUCCEED;
	}

	function updatecredit($get, $post)
	{
		if(!API_UPDATECREDIT)
		{
			return API_RETURN_FORBIDDEN;
		}
		/*
		note 更新积分
		discuz 默认8个积分表达,而DedeCMS只有一个积分字段,scores.注意money不能做积分来用.
		extcredits1  extcredits2  extcredits3  extcredits4  extcredits5  extcredits6  extcredits7  extcredits8
		*/
				
		$credit = intval($get['credit']);
		$fileds = $credit > 1 ? 'money' : 'scores';
		$amount = $get['amount'];
		$uid = $get['uid'];
		include UC_CLIENT_ROOT.'/client.php';
		$data = uc_get_user($uid,1);
		$username = $data[1];
		
		$result = $this->db->GetOne("SELECT mid FROM `#@__member` WHERE userid='$username'");
		if(is_array($result))
		{
			$this->db->ExecuteNoneQuery("UPDATE `#@__member` SET `$fileds`=`$fileds`+'$amount' WHERE mid='$result[mid]'");
		}
		
		return API_RETURN_SUCCEED;
	}

	function getcredit($get, $post)
	{
		if(!API_GETCREDIT)
		{
			return API_RETURN_FORBIDDEN;
		}
		
		include UC_CLIENT_ROOT.'/client.php';
		$data = uc_get_user($uid,1);
		$username = $data[1];
		$credit = intval($get['credit']);
		$fileds = $credit > 1 ? 'money' : 'scores';
		$result = $this->db->GetOne("SELECT `$fileds` AS credit FROM `#@__member` WHERE userid='$username'");
		
		echo is_array($result) ? $result['credit'] : 0;		
	}

	function getcreditsettings($get, $post)
	{
		if(!API_GETCREDITSETTINGS)
		{
			return API_RETURN_FORBIDDEN;
		}
		
		//这里支持DedeCMS积分,金币设置
		$credits[1] = array(strip_tags('积分'), '分');
		$credits[2] = array(strip_tags('金币'), '枚');
		return $this->_serialize($credits);
	}

	function updatecreditsettings($get, $post)
	{
		if(!API_UPDATECREDITSETTINGS)
		{
			return API_RETURN_FORBIDDEN;
		}
		$credit = $get['credit'];
		$outextcredits = array();
		if($credit && is_array($credit)) {
			foreach($credit as $appid => $credititems) {
				foreach($credititems as $value) {
					if($value['appiddesc']!=UC_APPID) continue;
					$outextcredits[$appid][] = array(
						'appiddesc' => $value['appiddesc'],
						'creditdesc' => $value['creditdesc'],
						'creditsrc' => $value['creditsrc'],
						'title' => $value['title'],
						'unit' => $value['unit'],
						'ratiosrc' => $value['ratiosrc'],
						'ratiodesc' => $value['ratiodesc'],
						'ratio' => $value['ratio']
					);
				}
			}
		}
		$_CACHE = "<?php !defined('UC_API') && exit(\"403 Forbidden!\");\n".'$_CACHE[\'credit\'] = unserialize("'.addslashes(serialize($outextcredits)).'");'."\r\n".'?>';
		$fp = @fopen(DEDEDATA.'/credits.inc.php', 'w');
		@fwrite($fp, $_CACHE);
		@fclose($fp);
		return API_RETURN_SUCCEED;
	}
}


function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
				return '';
			}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

function _stripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = _stripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

?>