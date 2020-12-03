<?php
/******************************
//UTF-8 - GB
*******************************/
function utf82gb($utfstr)
{
	if(function_exists('iconv')){ return iconv('utf-8','gbk//ignore',$utfstr); }
	global $UC2GBTABLE;
	$okstr = "";
	if(trim($utfstr)=="") return $utfstr;
	if(empty($UC2GBTABLE)){
		$filename = dirname(__FILE__)."/data/gb-utf8.table";
		$fp = fopen($filename,"r");
		while($l = fgets($fp,15))
		{	$UC2GBTABLE[hexdec(substr($l, 5, 4))] = hexdec(substr($l, 0, 4));}
		fclose($fp);
	}
	$okstr = "";
	$ulen = strlen($utfstr);
	for($i=0;$i<$ulen;$i++)
	{
		$c = $utfstr[$i];
		$cb = decbin(ord($utfstr[$i]));
		if(strlen($cb)==8){
			$csize = strpos(decbin(ord($cb)),"0");
			for($j=0;$j < $csize;$j++){
				$i++; $c .= $utfstr[$i];
			}
			$c = utf82u($c);
			if(isset($UC2GBTABLE[$c])){
				$c = dechex($UC2GBTABLE[$c]+0x8080);
				$okstr .= chr(hexdec($c[0].$c[1])).chr(hexdec($c[2].$c[3]));
			}
			else
			{ $okstr .= "&#".$c.";";}
		}
		else $okstr .= $c;
	}
	$okstr = trim($okstr);
	return $okstr;
}
/*******************************
//GB 2 UTF-8
*******************************/
function gb2utf8($gbstr) {
	if(function_exists('iconv')){ return iconv('gbk','utf-8',$gbstr); }
	global $CODETABLE;
	if(trim($gbstr)=="") return $gbstr;
	if(empty($CODETABLE)){
		$filename = dirname(__FILE__)."/data/gb-utf8.table";
		$fp = fopen($filename,"r");
		while ($l = fgets($fp,15))
		{ $CODETABLE[hexdec(substr($l, 0, 4))] = substr($l, 5, 4); }
		fclose($fp);
	}
	$ret = "";
	$utf8 = "";
	while ($gbstr!='') {
		if (ord(substr($gbstr, 0, 1)) > 0x80) {
			$thisW = substr($gbstr, 0, 2);
			$gbstr = substr($gbstr, 2, strlen($gbstr));
			$utf8 = "";
			@$utf8 = u2utf8(hexdec($CODETABLE[hexdec(bin2hex($thisW)) - 0x8080]));
			if($utf8!=""){
				for ($i = 0;$i < strlen($utf8);$i += 3)
					$ret .= chr(substr($utf8, $i, 3));
			}
		}
		else
		{
			$ret .= substr($gbstr, 0, 1);
			$gbstr = substr($gbstr, 1, strlen($gbstr));
		}
	}
	return $ret;
}
//Unicode - utf8
function u2utf8($c) {
	//for ($i = 0;$i < count($c);$i++)
	$str = '';
	if ($c < 0x80) {
		$str .= $c;
	} else if ($c < 0x800) {
		$str .= (0xC0 | $c >> 6);
		$str .= (0x80 | $c & 0x3F);
	} else if ($c < 0x10000) {
		$str .= (0xE0 | $c >> 12);
		$str .= (0x80 | $c >> 6 & 0x3F);
		$str .= (0x80 | $c & 0x3F);
	} else if ($c < 0x200000) {
		$str .= (0xF0 | $c >> 18);
		$str .= (0x80 | $c >> 12 & 0x3F);
		$str .= (0x80 | $c >> 6 & 0x3F);
		$str .= (0x80 | $c & 0x3F);
	}
	return $str;
}
//utf8 - Unicode
function utf82u($c)
{
  switch(strlen($c)) {
    case 1:
      return ord($c);
    case 2:
      $n = (ord($c[0]) & 0x3f) << 6;
      $n += ord($c[1]) & 0x3f;
      return $n;
    case 3:
      $n = (ord($c[0]) & 0x1f) << 12;
      $n += (ord($c[1]) & 0x3f) << 6;
      $n += ord($c[2]) & 0x3f;
      return $n;
    case 4:
      $n = (ord($c[0]) & 0x0f) << 18;
      $n += (ord($c[1]) & 0x3f) << 12;
      $n += (ord($c[2]) & 0x3f) << 6;
      $n += ord($c[3]) & 0x3f;
      return $n;
  }
}
/**********************************
//Big5-GB
**********************************/
function big52gb($Text) {
	if(function_exists('iconv')){ return iconv('big5','gbk',$Text); }
	global $BIG5_DATA;
	if(empty($BIG5_DATA)){
		$filename = dirname(__FILE__)."/data/big5-gb.table";
		$fp = fopen($filename, "rb");
		$BIG5_DATA = fread($fp,filesize($filename));
		fclose($fp);
	}
	$max = strlen($Text)-1;
	for($i=0;$i<$max;$i++) {
		$h = ord($Text[$i]);
		if($h>=0x80) {
			$l = ord($Text[$i+1]);
			if($h==161 && $l==64) {
					$gbstr = "~{!!~}";
			}else{
					$p = ($h-160)*510+($l-1)*2;
					$gbstr = $BIG5_DATA[$p].$BIG5_DATA[$p+1];
			}
			$Text[$i] = $gbstr[0];
			$Text[$i+1] = $gbstr[1];
			$i++;
		}
	}
	return $Text;
}
/********************************
//GB-Big5
*********************************/
function gb2big5($Text) {
	if(function_exists('iconv')){ return iconv('gbk','big5',$Text); }
	global $GB_DATA;
	if(empty($GB_DATA)){
		$filename = dirname(__FILE__)."/data/gb-big5.table";
		$fp = fopen($filename, "rb");
		$gb = fread($fp,filesize($filename));
		fclose($fp);
	}
	$max = strlen($Text)-1;
	for($i=0;$i<$max;$i++) {
		$h = ord($Text[$i]);
		if($h>=0x80) {
			$l = ord($Text[$i+1]);
			if($h==161 && $l==64) {
			$big = "~{!!~}";
			}else{
				$p = ($h-160)*510+($l-1)*2;
				$big = $GB_DATA[$p].$GB_DATA[$p+1];
			}
			$Text[$i] = $big[0];
			$Text[$i+1] = $big[1];
			$i++;
		}
	}
	return $Text;
}

/********************************
//unicode url-gbk
//Dic and Code by it prato
*********************************/
function UnicodeUrl2Gbk($str)
{
   if(!isset($GLOBALS['GbkUniDic']))
   {
     $fp = fopen(dirname(__FILE__).'/data/gbk_unicode.dic','rb');
     while(!feof($fp)) $GLOBALS['GbkUniDic'][bin2hex(fread($fp,2))] = fread($fp,2);
     fclose($fp);
  }
  $str = str_replace('$#$','+',$str);
  $glen = strlen($str);
  $okstr = "";
  for($i=0; $i < $glen; $i++)
  {
    if($glen-$i > 4)
    {
       if($str[$i]=='%' && $str[$i+1]=='u')
       {
           $uni = strtolower(substr($str,$i+2,4));
           $i = $i+5;
           if(isset($GLOBALS['GbkUniDic'][$uni])){
           	  $okstr .= $GLOBALS['GbkUniDic'][$uni];
           }
           else $okstr .= "&#".hexdec('0x'.$uni).";";
        }
        else{
          $okstr .= $str[$i];
        }
     }
     else{
       $okstr .= $str[$i];
     }
  }
  return $okstr;
}
?>