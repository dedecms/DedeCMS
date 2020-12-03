<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

function DedeGetHtml($url="")
{
	if(empty($url)) return FALSE;
	if(function_exists('fsockopen'))
	{
		if (!class_exists('DedeHttpDown', false)) {
			require_once(DEDEINC.'/dedehttpdown.class.php');
		}
		$del = new DedeHttpDown();
		$del->OpenUrl($url);
		return $del->GetHtml();
	} elseif (function_exists('curl_exec'))
	{
        $ch = curl_init();  
        $timeout = 10;   
        curl_setopt($ch,CURLOPT_URL,$url);      
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_MAXREDIRS,5);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        curl_setopt($ch,CURLOPT_ENCODING , "gzip");
        curl_setopt($ch,CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 5.00; Windows 98)\r\n");
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
	}
}

function lib_flink(&$ctag,&$refObj)
{
	global $dsql,$cfg_soft_lang,$cfg_version;
	$attlist="type|textall,row|24,titlelen|24,linktype|1,typeid|0";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	$totalrow = $row;
	$revalue = '';

	$wsql = " where ischeck >= '$linktype' ";
	if($typeid == 0)
	{
		$wsql .= '';
	}
	else
	{
		$wsql .= "And typeid = '$typeid'";
	}
	if($type=='image')
	{
		$wsql .= " And logo<>'' ";
	}
	else if($type=='text')
	{
		$wsql .= " And logo='' ";
	}
	
	$equery = "Select * from #@__flink $wsql order by sortrank asc limit 0,$totalrow";

	if(trim($ctag->GetInnerText())=='') $innertext = "<li>[field:link /]</li>";
	else $innertext = $ctag->GetInnerText();
	
	$dsql->SetQuery($equery);
	$dsql->Execute();
	$dlinks = array();
	while($dbrow=$dsql->GetArray())
	{
		$dlinks[] = array(
			'url'	=>	$dbrow['url'],
			'webname'	=>	$dbrow['webname'],
			'logo'	=>	$dbrow['logo']
		);
	}
	
	// 获取织梦链
	$cache_file = DEDEDATA.'/cache/dedelink.txt';
	if(file_exists($cache_file))
	{
		$result = unserialize(file_get_contents($cache_file));
	}
	if(!isset($result['result']) OR $result['timeout'] < time())
	{
		$linkUrl = DedeGetHtml("http://flink.dedecms.com/server_url.php")."flink_v56.php?lang={$cfg_soft_lang}&site={$_SERVER['SERVER_NAME']}&version=".$cfg_version;
		$linkInfo = DedeGetHtml($linkUrl);
		
		$result = array();
		$result['result'] = $linkInfo;
		$result['timeout'] = time() + 60 * 60 * 3; // 缓存3个小时
		file_put_contents($cache_file, serialize($result));
	} else {
		$linkInfo = $result['result'];
	}
	
	if(!empty($linkInfo)){
		$dedelinks = explode("\r\n", $linkInfo);
		//var_dump($dedelinks);
		foreach ($dedelinks as $dedelink)
		{
			$linkrow = explode("\t", $dedelink);
			if(count($linkrow)  < 4)
			{
				continue;
			}
			$row = array();
			$row['url'] = 'http://'.$linkrow[1];
			$row['webname'] = $linkrow[0];
			$row['logo'] = $linkrow[3];
			$dlinks[] = $row;
		}
	}
	
	//var_dump($dlinks);exit;
	foreach ($dlinks as $dbrow)
	{
		if($type=='text'||$type=='textall')
		{
			$link = "<a href='".$dbrow['url']."' target='_blank'>".cn_substr($dbrow['webname'],$titlelen)."</a> ";
		}
		else if($type=='image')
		{
			$link = "<a href='".$dbrow['url']."' target='_blank'><img src='".$dbrow['logo']."' width='88' height='31' border='0'></a> ";
		}
		else
		{
			if($dbrow['logo']=='')
			{
				$link = "<a href='".$dbrow['url']."' target='_blank'>".cn_substr($dbrow['webname'],$titlelen)."</a> ";
			}
			else
			{
				$link = "<a href='".$dbrow['url']."' target='_blank'><img src='".$dbrow['logo']."' width='88' height='31' border='0'></a> ";
			}
		}
		$rbtext = preg_replace("/\[field:url([\/\s]{0,})\]/isU", $row['url'], $innertext);
 		$rbtext = preg_replace("/\[field:webname([\/\s]{0,})\]/isU", $row['webname'], $rbtext);
 		$rbtext = preg_replace("/\[field:logo([\/\s]{0,})\]/isU", $row['logo'], $rbtext);
 		$rbtext = preg_replace("/\[field:link([\/\s]{0,})\]/isU", $link, $rbtext);
 		$revalue .= $rbtext;
	}
	return $revalue;
}
?>