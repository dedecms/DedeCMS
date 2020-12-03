<?php 
//本文件用于扩展采集结果
//下载居于http 1.1协议的防盗链图片
//------------------------------------
function DownImageKeep($gurl,$rfurl,$filename,$gcookie="",$JumpCount=0,$maxtime=30){
   $urlinfos = GetHostInfo($gurl);
   $ghost = trim($urlinfos['host']);
   if($ghost=='') return false;
   $gquery = $urlinfos['query'];
   if($gcookie=="" && !empty($rfurl)) $gcookie = RefurlCookie($rfurl);
   $sessionQuery = "GET $gquery HTTP/1.1\r\n";
   $sessionQuery .= "Host: $ghost\r\n";
   $sessionQuery .= "Referer: $rfurl\r\n";
   $sessionQuery .= "Accept: */*\r\n";
   $sessionQuery .= "User-Agent: Mozilla/4.0 (compatible; MSIE 5.00; Windows 98)\r\n";
   if($gcookie!=""&&!ereg("[\r\n]",$gcookie)) $sessionQuery .= $gcookie."\r\n";
   $sessionQuery .= "Connection: Keep-Alive\r\n\r\n";
   $errno = "";
   $errstr = "";
   $m_fp = fsockopen($ghost, 80, $errno, $errstr,10);
   fwrite($m_fp,$sessionQuery);
   $lnum = 0;
   //获取详细应答头
   $m_httphead = Array();
	 $httpstas = explode(" ",fgets($m_fp,256));
	 $m_httphead["http-edition"] = trim($httpstas[0]);
   $m_httphead["http-state"] = trim($httpstas[1]);
	 while(!feof($m_fp)){
			$line = trim(fgets($m_fp,256));
			if($line == "" || $lnum>100) break;
			$hkey = "";
			$hvalue = "";
			$v = 0;
			for($i=0;$i<strlen($line);$i++){
				if($v==1) $hvalue .= $line[$i];
				if($line[$i]==":") $v = 1;
				if($v==0) $hkey .= $line[$i];
			}
			$hkey = trim($hkey);
			if($hkey!="") $m_httphead[strtolower($hkey)] = trim($hvalue);
	 }
	 //分析返回记录
	 if(ereg("^3",$m_httphead["http-state"])){
	 	  if(isset($m_httphead["location"]) && $JumpCount<3){
	 	  	$JumpCount++;
	 	  	DownImageKeep($gurl,$rfurl,$filename,$gcookie,$JumpCount);
	 	  }
	 	  else{ return false; }
	 }
	 if(!ereg("^2",$m_httphead["http-state"])){
	 	  return false;
	 }
	 if(!isset($m_httphead)) return false;
	 $contentLength = $m_httphead['content-length'];
	 //保存文件
	 $fp = fopen($filename,"w") or die("写入文件：{$filename} 失败！");
	 $i=0;
	 $okdata = "";
	 $starttime = time();
	 while(!feof($m_fp)){
			$okdata .= fgetc($m_fp);
			$i++;
			//超时结束
			if(time()-$starttime>$maxtime) break;
			//到达指定大小结束
			if($i >= $contentLength) break;
	 }
	 if($okdata!="") fwrite($fp,$okdata);
	 fclose($fp);
	 if($okdata==""){
	 	  @unlink($filename);
	 	  fclose($m_fp);
	    return false;
	 }
	 fclose($m_fp);
	 return true;
}
//获得某页面返回的Cookie信息
//----------------------------
function RefurlCookie($gurl){
	global $gcookie,$lastRfurl;
	$gurl = trim($gurl);
	if(!empty($gcookie) && $lastRfurl==$gurl) return $gcookie;
	else $lastRfurl=$gurl;
	if(trim($gurl)=='') return '';
	$urlinfos = GetHostInfo($gurl);
  $ghost = $urlinfos['host'];
  $gquery = $urlinfos['query'];
  $sessionQuery = "GET $gquery HTTP/1.1\r\n";
  $sessionQuery .= "Host: $ghost\r\n";
  $sessionQuery .= "Accept: */*\r\n";
  $sessionQuery .= "User-Agent: Mozilla/4.0 (compatible; MSIE 5.00; Windows 98)\r\n";
  $sessionQuery .= "Connection: Close\r\n\r\n";
  $errno = "";
  $errstr = "";
  $m_fp = fsockopen($ghost, 80, $errno, $errstr,10) or die($ghost.'<br />');
  fwrite($m_fp,$sessionQuery);
  $lnum = 0;
  //获取详细应答头
  $gcookie = "";
	while(!feof($m_fp)){
			$line = trim(fgets($m_fp,256));
			if($line == "" || $lnum>100) break;
			else{
				if(eregi("^cookie",$line)){
					$gcookie = $line;
					break;
				}
			}
	 }
   fclose($m_fp);
   return $gcookie;
}

//获得网址的host和query部份
//-------------------------------------
function GetHostInfo($gurl){
	$gurl = eregi_replace("^http://","",trim($gurl));
	$garr['host'] = eregi_replace("/(.*)$","",$gurl);
	$garr['query'] = "/".eregi_replace("^([^/]*)/","",$gurl);
	return $garr;
}

//HTML里的图片转DEDE格式
//-----------------------------------
function TurnImageTag(&$body){
   global $cfg_album_width,$cfg_ddimg_width;
   if(empty($cfg_album_width)) $cfg_album_width = 800;
   if(empty($cfg_ddimg_width)) $cfg_ddimg_width = 150;
   preg_match_all('/src=[\'"](.+?)[\'"]/is',$body,$match);
   $ttx = '';
   if(is_array($match[1]) && count($match[1])>0){
     for($i=0;isset($match[1][$i]);$i++){
       $ttx .= "{dede:img text='' }".$match[1][$i]." {/dede:img}"."\r\n";
     }
   }
   $ttx = "{dede:pagestyle maxwidth='{$cfg_album_width}' ddmaxwidth='{$cfg_ddimg_width}' row='3' col='3' value='2'/}\r\n".$ttx;
   return $ttx;
}

//HTML里的网址格式转换
//-----------------------------------
function TurnLinkTag(&$body){
   $ttx = '';
   $handid = '服务器';
   preg_match_all("/<a href=['\"](.+?)['\"]([^>]+?)>(.+?)<\/a>/is",$body,$match);
   if(is_array($match[1]) && count($match[1])>0)
   {
     for($i=0;isset($match[1][$i]);$i++)
     {
       $servername = (isset($match[3][$i]) ? str_replace("'","`",$match[3][$i]) : $handid.($i+1));
       if(ereg("[<>]",$servername) || strlen($servername)>40) $servername = $handid.($i+1);
       $ttx .= "{dede:link text='$servername'} {$match[1][$i]} {/dede:link}\r\n";
     }
   }
   return $ttx;
}

?>