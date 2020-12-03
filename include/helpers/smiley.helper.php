<?php  if(!defined('DEDEINC')) exit('dedecms');
helper('string');
require_once(DEDEDATA.'/smiley.data.php');
		
//邮箱格式检查
if ( ! function_exists('parseSmileys'))
{
	function parseSmileys($str = '', $image_url = '', $ubb=true)
	{
		global $cfg_smileys;
		if ($image_url == '')
		{
			return $str;
		}

		$image_url = preg_replace("/(.+?)\/*$/", "\\1/",  $image_url);

		foreach ($cfg_smileys as $key => $val)
		{
			$str = str_replace($key, "<img src=\"".$image_url.$cfg_smileys[$key][0]."\" width=\"".$cfg_smileys[$key][1]."\" height=\"".$cfg_smileys[$key][2]."\" alt=\"".$cfg_smileys[$key][3]."\"/>", $str);
		}

		return $ubb? ubb($str) : $str;
	}
}