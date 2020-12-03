<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: functions.inc.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:21 $
 */

defined('DEDEASK') or exit('Request Error!');

function ihtmlspecialchars($string)
{
	if(is_array($string))
	{
		foreach($string as $key => $val)
		{
			$string[$key] = ihtmlspecialchars($val);
		}
	} else
	{
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
		str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}
	return $string;
}

function iheader($string, $replace = true, $http_response_code = 0)
{
	$string = str_replace(array("\r", "\n"), array('', ''), $string);
	if(empty($http_response_code) || PHP_VERSION < '4.3' )
	{
		@header($string, $replace);
	} else
	{
		@header($string, $replace, $http_response_code);
	}
	if(preg_match('/^\s*location:/is', $string))
	{
		exit();
	}
}
function iimplode($array)
{
	if(is_array($array))
	{
		return implode(',', $array);
	} else {
		return $array;
	}
}

function makecookie($var, $value, $life = 0, $prefix = 0)
{
	global $cookiepre, $cookiedomain, $cookiepath, $timestamp, $_SERVER;
	setcookie(($prefix ? $cookiepre : '').$var, $value,
	$life ? $timestamp + $life : 0, $cookiepath,
	$cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

function clearcookies()
{
	global $uid, $username, $pw, $adminid;
	makecookie('auth', '', -86400 * 365);
	$uid = $adminid = 0;
	$username = $pw = '';
}

function random($length, $numeric = 0)
{
	PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
	if($numeric)
	{
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	} else
	{
		$hash = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++)
		{
			$hash .= $chars[mt_rand(0, $max)];
		}
	}
	return $hash;
}

function multi($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10, $autogoto = TRUE, $simple = FALSE)
{
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
	$realpages = 1;
	if($num > $perpage)
	{
		$offset = 2;
		$realpages = @ceil($num / $perpage);
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;
		if($page > $pages)
		{
			$from = 1;
			$to = $pages;
		} else
		{
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1)
			{
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page)
				{
					$to = $page;
				}
			} elseif($to > $pages)
			{
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1" class="first">1 ...</a>' : '').
		($curpage > 1 && !$simple ? '<a href="'.$mpurl.'page='.($curpage - 1).'" class="prev">&lsaquo;&lsaquo;</a>' : '');
		for($i = $from; $i <= $to; $i++)
		{
			$multipage .= $i == $curpage ? '<strong>'.$i.'</strong>' : '<a href="'.$mpurl.'page='.$i.'">'.$i.'</a>';
		}

		$multipage .= ($curpage < $pages && !$simple ? '<a href="'.$mpurl.'page='.($curpage + 1).'" class="next">&rsaquo;&rsaquo;</a>' : '').
		($to < $pages ? '<a href="'.$mpurl.'page='.$pages.'" class="last">... '.$realpages.'</a>' : '').
		(!$simple && $pages > $page && 0>1 ? '<div class="pselect"><input type="text" name="custompage" size="3" onkeydown="if(event.keyCode==13) {window.location=\''.$mpurl.'page=\'+this.value; return false;}" /></div>' : '').'</div>';

		$multipage = $multipage ? '<div class="pages">'.(!$simple ? '<div class="pcount">&nbsp;'.$num.'&nbsp;</div>' : '').'<div class="plist">'.$multipage.'</div>' : '';
	}
	return $multipage;
}

function language($file, $language = 'zh-cn')
{
	$languagepack = DEDEASK.'include/language/'.$language.'.'.$file.'.php';
	if(file_exists($languagepack))
	{
		return $languagepack;
	} else
	{
		return FALSE;
	}
}

function showmsgs($msg, $gotourl = '', $time = 3, $extra = '')
{
	global $dsql;
	$dsql->Close();
	include language('msg');
	$extrahead = $gotourl && $gotourl != '-1' ? '<meta http-equiv="refresh" content="'.$time.' url='.$gotourl.'">' : '';
	if($gotourl == '-1')
	{
		$gotourl = 'javascript:history.go(-1)';
	}
	include DEDEASK.'template/default/showmsg.htm';
	exit();
}

function gethonor($score, &$honors)
{
	foreach($honors as $honor)
	{
		if($honor['integral']<=$score)
		{
			return $honor['titles'];
		}
	}
}
?>