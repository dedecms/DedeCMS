<?php

if(!defined('DEDEINC')) exit('Request Error!');
require_once(dirname(__FILE__).'/likesgpage.lib.php');

function lib_likepage(&$ctag,&$refObj)
{
	return lib_likesgpage($ctag, $refObj);
}

?>