<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

function ch_stepselect($fvalue,&$arcTag,&$refObj,$fname='')
{
	return GetEnumsValue2($fname,$fvalue);
}

//获取二级枚举的值
function GetEnumsValue2($egroup,$evalue=0)
{
	if( !isset($GLOBALS['em_'.$egroup.'s']) )
	{
		$cachefile = DEDEDATA.'/enums/'.$egroup.'.php';
		if(!file_exists($cachefile))
		{
			require_once(DEDEINC.'/enums.func.php');
			WriteEnumsCache();
		}
		if(!file_exists($cachefile))
		{
			return '';
		}
		else
		{
			require_once($cachefile);
		}
	}
	if($evalue>=500)
	{
		if($evalue % 500 == 0)
		{
			return (isset($GLOBALS['em_'.$egroup.'s'][$evalue]) ? $GLOBALS['em_'.$egroup.'s'][$evalue] : '');
		}
		else
		{
			$elimit = $evalue % 500;
			$erevalue = $evalue - $elimit;
			return $GLOBALS['em_'.$egroup.'s'][$erevalue].' -- '.$GLOBALS['em_'.$egroup.'s'][$evalue];
		}
	}
}

?>