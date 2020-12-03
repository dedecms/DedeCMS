<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: common.inc.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:21 $
 */

//error_reporting(0);

set_magic_quotes_runtime(0);
define('DEDEASK', str_replace("\\","/",substr(dirname(__FILE__), 0, -7)));

require_once DEDEASK.'../include/common.inc.php';

$charset = $cfg_soft_lang;
$timestamp = time();
ob_start();
require_once DEDEINC.'/memberlogin.class.php';
require_once DEDEASK.'./include/functions.inc.php';
require_once DEDEINC.'/dedetemplate.class.php';

if($cfg_ask == 'N')
{
	showmsg('问答系统已关闭，请返回', '-1');
	exit;
}

$dbcharset = $cfg_db_language;

$cfg_ml = new MemberLogin();
$uid = $cfg_ml->M_ID;

$username = $cfg_ml->M_LoginID;
$scores = $cfg_ml->M_Scores;

$tpp = $cfg_ask_tpp;
$tpp = max(1,$tpp);

$dateformat = $cfg_ask_dateformat;
$timeformat = $cfg_ask_timeformat;
$timeoffset = $cfg_ask_timeoffset;


$page = isset($page) ? max(1, intval($page)) : 1;
$tid = isset($tid) && is_numeric($tid) ? $tid : 0;
$tid2 = isset($tid2) && is_numeric($tid2) ? $tid2 : 0;
$lm = isset($lm) && is_numeric($lm) ? $lm : 0;
$id = isset($id) && is_numeric($id) ? $id : 0;
$cfg_ask_expiredtime = isset($cfg_ask_expiredtime) && is_numeric($cfg_ask_expiredtime) ? $cfg_ask_expiredtime : 20;
$cfg_ask_expiredtime = max($cfg_ask_expiredtime, 1);

$sitename = $cfg_ask_sitename;
$indexname = 'index.php';
$symbols = $cfg_ask_symbols;

function myecho()
{
	global $cfg_ask_rewrite;
	$content = ob_get_contents();
	ob_end_clean();
	$search = $replaced = array();
	if($cfg_ask_rewrite == 'Y')
	{
		$search[] = "/\<a href\=\"browser\.php\?(&amp;)?(tid\=(\d+))?(&amp;)?(tid2\=(\d+))?(&amp;)?(lm\=(\d+))?\"/Uie";
		$replaced[] = "rewrite_browser('\\3', '\\6', '\\9')";
		$search[] = "/\<a href\=\"question\.php\?id\=(\d+)\"/";
		$replaced[] = '<a href="question-id-'.'\\1'.'.html" ';
		$content = preg_replace($search, $replaced, $content);
	}
	echo $content;
}

function rewrite_browser($tid='', $tid2='', $lm='')
{
	return '<a href="browser'.($tid ? '-tid-'.$tid : '').($tid2 ? '-tid2-'.$tid2 : '').($lm ? '-lm-'.$lm : '').'.html" ';
}

/*
b.php?id=23
b.php?lm=233
b.php?tid=323
b.php?tid=32&lm=32
*/

?>