<?php
include_once (dirname(__FILE__)."/../include/common.inc.php");
if(!defined('UC_APPID')) exit('Invalid Request');
include_once DEDEROOT.'/uc_client/client.php';

function uc_credit_note($username,$amount=0,$credits='scores')
{
	list($uid) = uc_get_user($username);
	if($uid < 1 || !$amount) return 0;
	include DEDEDATA.'/credits.inc.php';
	$arr = array('scores' => 1, 'money' => 2);
	$credit = isset($arr[$credits]) ? $arr[$credits] : 1;
	
	if(isset($_CACHE['credit']) && is_array($_CACHE['credit']))
	{
		foreach($_CACHE['credit'] as $appid => $creditsItems)
		{
			if($creditsItems['creditdesc']!=$credit) continue;
			$amount = $amount*$creditsItems['ratio'];
			uc_credit_exchange_request($uid,$creditsItems['creditdesc'],$creditsItems['creditsrc'],$appid,$amount);
		}
	}
}

function uc_feed_note($username,$feed)
{
	$data = uc_get_user($username);
	if(!$data) return '';
	$uid = $data[0];
	return uc_feed_add($feed['icon'], $uid, $username, $feed['title_template'], $feed['title_data'], $feed['body_template'], $feed['body_data'], '', '', $feed['images']);
}
?>