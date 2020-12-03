<?
/*-------------------------------------------------------
使用方法
1、把本文件放到 include 文件夹
2、在 inc_archives_view.php 中引入本文件
require_once(dirname(__FILE__)."/inc_downclass.php"); 
3、然后文章模板的{dede:field name='body'/}标记中加入 function='RndString(@me)'
即是改为 {dede:field name='body' function='RndString(@me)'/}
-----------------------------------------------------------*/
function RndString($body)
{
  //最大间隔距离(如果在检测不到p标记的情况下，加入混淆字串的最大间隔距离)
  $maxpos = 1024;
  //font 的字体颜色
  $fontColor = "#FFFFFF";
  //div span p 标记的随机样式
  $st1 = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(100,999);
  $st2 = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(100,999);
  $st3 = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(100,999);
  $st4 = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(100,999);
  $rndstyle[1]['value'] = ".{$st1} { display:none; }";
  $rndstyle[1]['name'] = $st1;
  $rndstyle[2]['value'] = ".{$st2} { display:none; }";
  $rndstyle[2]['name'] = $st2;
  $rndstyle[3]['value'] = ".{$st3} { display:none; }";
  $rndstyle[3]['name'] = $st3;
  $rndstyle[4]['value'] = ".{$st4} { display:none; }";
  $rndstyle[4]['name'] = $st4;
  $mdd = mt_rand(1,4);
  //以后内容如果你不懂其含义，请不要改动
  //---------------------------------------------------
  $rndstyleValue = $rndstyle[$mdd]['value'];
  $rndstyleName = $rndstyle[$mdd]['name'];
  $reString = "<style> $rndstyleValue </style>\r\n";
  //附机标记
  $rndem[1] = 'font';
  $rndem[2] = 'div';
  $rndem[3] = 'span';
  $rndem[4] = 'p';
  //读取字符串数据
  $fp = fopen(dirname(__FILE__).'/data/downmix.php','r');
  $start = 0;
  $totalitem = 0;
  while(!feof($fp)){
	   $v = trim(fgets($fp,128));
	   if($start==1){
		    if(ereg("#end#",$v)) break;
		    if($v!=""){ $totalitem++; $rndstring[$totalitem] = ereg_replace("#,","",$v); }
	   }
	   if(ereg("#start#",$v)){ $start = 1; }
  }
  fclose($fp);
  //处理要防采集的字段
  $bodylen = strlen($body) - 1;
  $prepos = 0;
  for($i=0;$i<=$bodylen;$i++){
  	if($i+2 >= $bodylen || $i<50) $reString .= $body[$i];
  	else{
  	  @$ntag = strtolower($body[$i].$body[$i+1].$body[$i+2]);
  	  if($ntag=='</p' || ($ntag=='<br' && $i-$prepos>$maxpos) ){
  	  	 $dd = mt_rand(1,4);
  	  	 $emname = $rndem[$dd];
  	  	 $dd = mt_rand(1,$totalitem);
  	  	 $rnstr = $rndstring[$dd];
  	  	 if($emname!='font') $rnstr = " <$emname class='$rndstyleName'>$rnstr</$emname> ";
  	  	 else  $rnstr = " <font color='$fontColor'>$rnstr</font> ";
  	  	 $reString .= $rnstr.$body[$i];
  	  	 $prepos = $i;
  	  }
  	  else $reString .= $body[$i];
    }
  }
  unset($body);
  return $reString;
}//函数结束
?>