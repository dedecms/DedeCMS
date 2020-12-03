<?php
/*
 * Unicode编码词典的php分词器
 *
 */
define('_SP_', chr(0xFF).chr(0xFE)); 
define('_SP2_', ',');
//解决有些系统内存溢出问题
ini_set('memory_limit', '64M');
class SplitWord
{
    
    //输入和输出的字符编码（只允许 utf-8、gbk/gb2312/gb18030、big5 三种类型）  
    public $sourceCharSet = 'utf-8';
    public $targetCharSet = 'utf-8';
	
    //生成的分词结果数据类型 1 为全部， 2为 词典词汇及单个中日韩简繁字符及英文， 3 为词典词汇及英文
    public $resultType = 2;
    
    //句子长度小于这个数值时不拆分，notSplitLen = n(个汉字) * 2 + 1
    public $notSplitLen = 5;
    
    //把英文单词全部转小写
    public $toLower = false;
    
    //使用最大切分模式对二元词进行消岐
    public $differMax = false;
    
    //尝试合并单字
    public $unitWord = true;
    
    //使用热门词优先模式进行消岐
    public $differFreq = false;
	
    //被转换为unicode的源字符串
    private $sourceString = '';
    
    //附加词典
    public $addonDic = array();
    public $addonDicFile = 'data/words_addons.dic';
    
    //主词典 
    public $dicStr = '';
    public $mainDic = array();
    public $mainDicInfos = array();
    public $mainDicFile = 'data/base_dic_full.dic';
    //是否直接载入词典（选是载入速度较慢，但解析较快；选否载入较快，但解析较慢，需要时才会载入特定的词条）
    private $isLoadAll = false;
    
    //主词典词语最大长度(实际加上词末为12+2)
    private $dicWordMax = 12;
    //粗分后的数组（通常是截取句子等用途）
    private $simpleResult = array();
    //最终结果(用空格分开的词汇列表)
    private $finallyResult = '';
    
    //是否已经载入词典
    public $isLoadDic = false;
    //系统识别或合并的新词
    public $newWords = array();
    public $foundWordStr = '';
    //词库载入时间
    public $loadTime = 0;
    
    //php4构造函数
	  function SplitWord($source_charset='utf-8', $target_charset='utf-8', $load_all=true, $source=''){
	  	$this->__construct($source_charset, $target_charset, $load_all, $source);
	  }	
	
    function __construct($source_charset='utf-8', $target_charset='utf-8', $load_all=true, $source='')
    {
        $this->SetSource( $source, $source_charset, $target_charset );
        $this->isLoadAll = $load_all;
        $this->LoadDict();
    }
    
    function SetSource( $source, $source_charset='utf-8', $target_charset='utf-8' )
    {
        $this->sourceCharSet = strtolower($source_charset);
        $this->targetCharSet = strtolower($target_charset);
        $this->simpleResult = array();
        $this->finallyResult = array();
        $this->finallyIndex = array();
        if( $source != '' )
        {
            $rs = true;
            if( preg_match("/^utf/", $source_charset) ) {
                $this->sourceString = iconv('utf-8//ignore', 'ucs-2', $source);
            }
            else if( preg_match("/^gb/", $source_charset) ) {
                $this->sourceString = iconv('utf-8', 'ucs-2', iconv('gb18030', 'utf-8//ignore', $source));
            }
            else if( preg_match("/^big/", $source_charset) ) {
                $this->sourceString = iconv('utf-8', 'ucs-2', iconv('big5', 'utf-8', $source));
            }
            else {
                $rs = false;
            }
        }
        else
        {
           $rs = false;
        }
        return $rs;
    }
    
    /**
     * 设置结果类型(只在获取finallyResult才有效)
     * @param $rstype 1 为全部， 2去除特殊符号
     *
     * @return void
     */
    function SetResultType( $rstype )
    {
        $this->resultType = $rstype;
    }
    
    /**
     * 载入词典
     *
     * @return void
     */
    function LoadDict( $maindic='' )
    {
        $dicAddon = dirname(__FILE__).'/'.$this->addonDicFile;
        if($maindic=='' || !file_exists(dirname(__FILE__).'/'.$maindic) )
        {
            $dicWords = dirname(__FILE__).'/'.$this->mainDicFile ;
        }
        else
        {
            $dicWords = dirname(__FILE__).'/'.$maindic;
            $this->mainDicFile = $maindic;
        }
        //载入主词典
        $startt = microtime(true);
        $aslen = filesize($dicWords);
        $fp = fopen($dicWords, 'rb');
        $this->dicStr = fread($fp, $aslen);
        fclose($fp);
        $ishead = 1;
        $nc = '';
        $i = 0;
        while( $i < $aslen )
        {
            $nc = substr($this->dicStr, $i, 2);
            $i = $i+2;
            $slen = intval(hexdec(bin2hex( substr($this->dicStr, $i, 2) )));
            $i = $i+2;
            $this->mainDic[$nc][1] = '';
            $this->mainDic[$nc][2][0] = $i;
            $this->mainDic[$nc][2][1] = $slen;
            if( $this->isLoadAll)
            {
                $strs = explode(_SP_, substr($this->dicStr, $i, $slen) );
                $klen = count($strs);
                for($k=0; $k < $klen; $k++)
                {
                    //先不对词频和词性进行解释，以提升载入速度，可以用($this->GetWordInfos($word)获得词的附加信息)
                    $this->mainDic[$nc][1][$strs[$k]] = $strs[$k+1];
                    //$this->mainDic[$nc][1][$strs[$k]] = explode(',', $strs[$k+1]); //直接解析（需多花费0.1秒时间）
                    $k = $k+1;
                }
            }
            $i = $i + $slen;
        }
        //不必保留词典文件字符串
        if( $this->isLoadAll)
        {
            $this->dicStr = '';
        }
        //载入副词典
        $ds = file($dicAddon);
        foreach($ds as $d)
        {
            $d = trim($d);
            if($d=='') continue;
            $estr = substr($d, 1, strlen($d)-1);
            $estr = iconv('utf-8', 'ucs-2', $estr);
            $this->addonDic[substr($d, 0, 1)][$estr] = strlen($estr);
        }
        $this->loadTime = microtime(true) - $startt;
        $this->isLoadDic = true;
    }
    
   /**
    * 检测某个尾词是否存在
    */
    function IsWordEnd($nc)
    {
         if( !isset( $this->mainDic[$nc] ) )
         {
            return false;
         }
         if( !is_array($this->mainDic[$nc][1]) )
         {       
              $strs = explode(_SP_, substr($this->dicStr, $this->mainDic[$nc][2][0], $this->mainDic[$nc][2][1]) );
              $klen = count($strs);
              for($k=0; $k < $klen; $k++)
              {
                  $this->mainDic[$nc][1][$strs[$k]] = $strs[$k+1];
                  //$this->mainDic[$nc][1][$strs[$k]] = explode(',', $strs[$k+1]);
                  $k = $k+1;
              }
         }
         return true;
    }
    
    /**
     * 获得某个词的词性及词频信息
     * @parem $word unicode编码的词
     * @return void
     */
     function GetWordProperty($word)
     {
        if( strlen($word)<4 )
        {
            return '/s';
        }
        $infos = $this->GetWordInfos($word);
        return isset($infos['m']) ? "/{$infos['m']}{$infos['c']}" : "/s";
     }
    
    /**
     * 指定某词的词性信息（通常是新词）
     * @parem $word unicode编码的词
     * @parem $infos array('c' => 词频, 'm' => 词性);
     * @return void;
     */
    function SetWordInfos($word, $infos)
    {
        if( strlen($word)<4 )
        {
            return ;
        }
        if( isset($this->mainDicInfos[$word]) )
        {
            $this->newWords[$word]++;
            $this->mainDicInfos[$word]['c']++;
        }
        else
        {
            $this->newWords[$word] = 1;
            $this->mainDicInfos[$word] = $infos;
        }
    }
    
    /**
     * 从词典文件找指定词的信息
     * @parem $word unicode编码的词
     * @return array('c' => 词频, 'm' => 词性);
     */
    function GetWordInfos($word)
    {
        $rearr = '';
        if( strlen($word) < 4 )
        {
            return $rearr;
        }
        //检查缓存数组
        if( isset($this->mainDicInfos[$word]) )
        {
            return $this->mainDicInfos[$word];
        }
        //分析
        $wfoot = $this->GetWord($word);
        $whead = substr($word, strlen($word)-2, 2);
        if( !$this->IsWordEnd($whead) || !isset($this->mainDic[$whead][1][$wfoot]) )
        {
            return $rearr;
        }
        if( is_array($this->mainDic[$whead][1][$wfoot]) )
        {
            $rearr['c'] = $this->mainDic[$whead][1][$wfoot][0];
            $rearr['m'] = $this->mainDic[$whead][1][$wfoot][1];
        }
        else
        {
            $strs = explode(_SP2_, $this->mainDic[$whead][1][$wfoot]);
            $rearr['c'] = $strs[0];
            $rearr['m'] = $strs[1];
        }
        //保存到缓存数组
        $this->mainDicInfos[$word] = $rearr;
        return $rearr;
    }
    
    /**
     * 开始执行分析
     * @parem bool optimize 是否对结果进行优化
     * @return bool
     */
    function StartAnalysis($optimize=true)
    {
        if( !$this->isLoadDic )
        {
            $this->LoadDict();
        }
        $this->simpleResult = $this->finallyResult = array();
        $this->sourceString .= chr(0).chr(32);
        $slen = strlen($this->sourceString);
        $sbcArr = array();
        $j = 0;
        //全角与半角字符对照表
        for($i=0xFF00; $i < 0xFF5F; $i++)
        {
            $scb = 0x20 + $j;
            $j++;
            $sbcArr[$i] = $scb;
        }
        //对字符串进行粗分
        $onstr = '';
        $lastc = 1; //1 中/韩/日文, 2 英文/数字/符号('.', '@', '#', '+'), 3 ANSI符号 4 纯数字 5 非ANSI符号或不支持字符
        $s = 0;
        $ansiWordMatch = "[0-9a-z@#%\+\.-]";
        $notNumberMatch = "[a-z@#%\+]";
        $nameLink = 0xB7;
        for($i=0; $i < $slen; $i++)
        {
            $c = $this->sourceString[$i].$this->sourceString[++$i];
            $cn = hexdec(bin2hex($c));
            $cn = isset($sbcArr[$cn]) ? $sbcArr[$cn] : $cn;
            //ANSI字符
            if($cn < 0x80)
            {
                if( preg_match('/'.$ansiWordMatch.'/i', chr($cn)) )
                {
                    if( $lastc != 2 && $onstr != '') {
                        $this->simpleResult[$s]['w'] = $onstr;
                        $this->simpleResult[$s]['t'] = $lastc;
                        $this->DeepAnalysis($onstr, $lastc, $s, $optimize);
                        $s++;
                        $onstr = '';
                    }
                    $lastc = 2;
                    $onstr .= chr(0).chr($cn);
                }
                else
                {
                    if( $onstr != '' )
                    {
                        $this->simpleResult[$s]['w'] = $onstr;
                        if( $lastc==2 )
                        {
                            if( !preg_match('/'.$notNumberMatch.'/i', iconv('ucs-2', 'utf-8', $onstr)) ) $lastc = 4;
                        }
                        $this->simpleResult[$s]['t'] = $lastc;
                        if( $lastc != 4 ) $this->DeepAnalysis($onstr, $lastc, $s, $optimize);
                        $s++;
                    }
                    $onstr = '';
                    $lastc = 3;
                    if($cn < 31)
                    {
                        continue;
                    }
                    else
                    {
                        $this->simpleResult[$s]['w'] = chr(0).chr($cn);
                        $this->simpleResult[$s]['t'] = 3;
                        $s++;
                    }
                }
            }
            //普通字符
            else
            {
                //正常文字 $cn==$nameLink || 
                if( ($cn>0x3FFF && $cn < 0x9FA6) || ($cn>0xF8FF && $cn < 0xFA2D)
                    || ($cn>0xABFF && $cn < 0xD7A4) || ($cn>0x3040 && $cn < 0x312B) )
                {
                    if( $lastc != 1 && $onstr != '')
                    {
                        $this->simpleResult[$s]['w'] = $onstr;
                        if( $lastc==2 )
                        {
                            if( !preg_match('/'.$notNumberMatch.'/i', iconv('ucs-2', 'utf-8', $onstr)) ) $lastc = 4;
                        }
                        $this->simpleResult[$s]['t'] = $lastc;
                        if( $lastc != 4 ) $this->DeepAnalysis($onstr, $lastc, $s, $optimize);
                        $s++;
                        $onstr = '';
                    }
                    $lastc = 1;
                    $onstr .= $c;
                }
                //特殊符号
                else
                {
                    if( $onstr != '' )
                    {
                        $this->simpleResult[$s]['w'] = $onstr;
                        if( $lastc==2 )
                        {
                            if( !preg_match('/'.$notNumberMatch.'/i', iconv('ucs-2', 'utf-8', $onstr)) ) $lastc = 4;
                        }
                        $this->simpleResult[$s]['t'] = $lastc;
                        if( $lastc != 4 ) $this->DeepAnalysis($onstr, $lastc, $s, $optimize);
                        $s++;
                    }
                    
                    //检测书名
                    if( $cn == 0x300A )
                    {
                        $tmpw = '';
                        $n = 1;
                        $isok = false;
                        $ew = chr(0x30).chr(0x0B);
                        while(true)
                        {
                            $w = $this->sourceString[$i+$n].$this->sourceString[$i+$n+1];
                            if( $w == $ew )
                            {
                                $this->simpleResult[$s]['w'] = $c;
                                $this->simpleResult[$s]['t'] = 5;
                                $s++;
                        
                                $this->simpleResult[$s]['w'] = $tmpw;
                                $this->newWords[$tmpw] = 1;
                                if( !isset($this->newWords[$tmpw]) )
                                {
                                    $this->foundWordStr .= $this->OutStringEncoding($tmpw).'/nb, ';
                                    $this->SetWordInfos($tmpw, array('c'=>1, 'm'=>'nb'));
                                }
                                $this->simpleResult[$s]['t'] = 13;
                                
                                $s++;

                                //最大切分模式对书名继续分词
                                if( $this->differMax )
                                {
                                    $this->simpleResult[$s]['w'] = $tmpw;
                                    $this->simpleResult[$s]['t'] = 21;
                                    $this->DeepAnalysis($tmpw, $lastc, $s, $optimize);
                                    $s++;
                                }
                                
                                $this->simpleResult[$s]['w'] = $ew;
                                $this->simpleResult[$s]['t'] =  5;
                                $s++;
                        
                                $i = $i + $n + 1;
                                $isok = true;
                                $onstr = '';
                                $lastc = 5;
                                break;
                            }
                            else
                            {
                                $n = $n+2;
                                $tmpw .= $w;
                                if( strlen($tmpw) > 60 )
                                {
                                    break;
                                }
                            }
                        }//while
                        if( !$isok )
                        {
                            $this->simpleResult[$s]['w'] = $c;
              	            $this->simpleResult[$s]['t'] = 5;
              	            $s++;
              	            $onstr = '';
                            $lastc = 5;
                        }
                        continue;
                    }
                    
                    $onstr = '';
                    $lastc = 5;
                    if( $cn==0x3000 )
                    {
                        continue;
                    }
                    else
                    {
                        $this->simpleResult[$s]['w'] = $c;
                        $this->simpleResult[$s]['t'] = 5;
                        $s++;
                    }
                }//2byte symbol
                
            }//end 2byte char
        
        }//end for
        
        //处理分词后的结果
        $this->SortFinallyResult();
    }
    
    /**
     * 深入分词
     * @parem $str
     * @parem $ctype (2 英文类， 3 中/韩/日文类)
     * @parem $spos   当前粗分结果游标
     * @return bool
     */
    function DeepAnalysis( &$str, $ctype, $spos, $optimize=true )
    {

        //中文句子
        if( $ctype==1 )
        {
            $slen = strlen($str);
            //小于系统配置分词要求长度的句子
            if( $slen < $this->notSplitLen )
            {
                $tmpstr = '';
                $lastType = 0;
                if( $spos > 0 ) $lastType = $this->simpleResult[$spos-1]['t'];
                if($slen < 5)
                {
                	  //echo iconv('ucs-2', 'utf-8', $str).'<br/>';
                	  if( $lastType==4 && ( isset($this->addonDic['u'][$str]) || isset($this->addonDic['u'][substr($str, 0, 2)]) ) )
        						{
             					 $str2 = '';
             					 if( !isset($this->addonDic['u'][$str]) && isset($this->addonDic['s'][substr($str, 2, 2)]) )
             					 {
             					    $str2 = substr($str, 2, 2);
             					    $str  = substr($str, 0, 2);
             					 }
             					 $ww = $this->simpleResult[$spos - 1]['w'].$str;
             					 $this->simpleResult[$spos - 1]['w'] = $ww;
             					 $this->simpleResult[$spos - 1]['t'] = 4;
             					 if( !isset($this->newWords[$this->simpleResult[$spos - 1]['w']]) )
                       {
             					    $this->foundWordStr .= $this->OutStringEncoding( $ww ).'/mu, ';
             					    $this->SetWordInfos($ww, array('c'=>1, 'm'=>'mu'));
             					 }
             					 $this->simpleResult[$spos]['w'] = '';
             					 if( $str2 != '' )
             					 {
             					    $this->finallyResult[$spos-1][] = $ww;
             					    $this->finallyResult[$spos-1][] = $str2;
             					 }
       							}
       							else {
       								 $this->finallyResult[$spos][] = $str;
       							}
                }
                else
                {
                	  $this->DeepAnalysisChinese( $str, $ctype, $spos, $slen, $optimize );
                }
            }
            //正常长度的句子，循环进行分词处理
            else
            {
                $this->DeepAnalysisChinese( $str, $ctype, $spos, $slen, $optimize );
            }
        }
        //英文句子，转为小写
        else
        {
            if( $this->toLower ) {
                $this->finallyResult[$spos][] = strtolower($str);
            }
            else {
                $this->finallyResult[$spos][] = $str;
            }
        }
    }
    
    /**
     * 中文的深入分词
     * @parem $str
     * @return void
     */
    function DeepAnalysisChinese( &$str, $lastec, $spos, $slen, $optimize=true )
    {
        $quote1 = chr(0x20).chr(0x1C);
        $tmparr = array();
        $hasw = 0;
        //如果前一个词为 “ ， 并且字符串小于3个字符当成一个词处理。
        if( $spos > 0 && $slen < 11 && $this->simpleResult[$spos-1]['w']==$quote1 )
        {
            $tmparr[] = $str;
            if( !isset($this->newWords[$str]) )
            {
                $this->foundWordStr .= $this->OutStringEncoding($str).'/nq, ';
                $this->SetWordInfos($str, array('c'=>1, 'm'=>'nq'));
            }
            if( !$this->differMax )
            {
                $this->finallyResult[$spos][] = $str;
                return ;
            }
        }
        //进行切分
        for($i=$slen-1; $i>0; $i--)
        {
            $nc = $str[$i-1].$str[$i];
            if($i<2)
            {
                $tmparr[] = $nc;
                $i = 0;
                break;
            }
            if( $this->IsWordEnd($nc) )
            {
                $i = $i - 1;
                $isok = false;
                for($k=12; $k>1; $k=$k-2)
                {
                    //if($i < $k || $this->mainDic[$nc][0][$k]==0) continue;
                    if($i < $k) continue;
                    $w = substr($str, $i-$k, $k);
                    if( isset($this->mainDic[$nc][1][$w]) )
                    {
                        $tmparr[] = $w.$nc;
                        $i = $i - $k;
                        $isok = true;
                        break;
                    }
                }
                if(!$isok)
                {
                   $tmparr[] = $nc;
                }
            }
            else
            {
               $tmparr[] = $nc;
               $i = $i - 1;
            }
        }
        if(count($tmparr)==0) return ;
        for($i=count($tmparr)-1; $i>=0; $i--)
        {
            $this->finallyResult[$spos][] = $tmparr[$i];
        }
        //优化结果(岐义处理、新词、数词、人名识别等)
        if( $optimize )
        {
            $this->OptimizeResult( $this->finallyResult[$spos], $spos );
        }
    }
    
    /**
    * 对最终分词结果进行优化（把simpleresult结果合并，并尝试新词识别、数词合并等）
    * @parem $optimize 是否优化合并的结果
    * @return bool
    */
    //t = 1 中/韩/日文, 2 英文/数字/符号('.', '@', '#', '+'), 3 ANSI符号 4 纯数字 5 非ANSI符号或不支持字符
    function OptimizeResult( &$smarr, $spos )
    {
        $newarr = array();
        $prePos = $spos - 1;
        $arlen = count($smarr);
        $i = $j = 0;
        //检测数量词
        if( $prePos > -1 && !isset($this->finallyResult[$prePos]) )
        {
            $lastw = $this->simpleResult[$prePos]['w'];
            $lastt = $this->simpleResult[$prePos]['t'];
        	  if( ($lastt==4 || isset( $this->addonDic['c'][$lastw] )) && isset( $this->addonDic['u'][$smarr[0]] ) )
        	  {
               $this->simpleResult[$prePos]['w'] = $lastw.$smarr[0];
               $this->simpleResult[$prePos]['t'] = 4;
               if( !isset($this->newWords[ $this->simpleResult[$prePos]['w'] ]) )
               {
                    $this->foundWordStr .= $this->OutStringEncoding( $this->simpleResult[$prePos]['w'] ).'/mu, ';
                    $this->SetWordInfos($this->simpleResult[$prePos]['w'], array('c'=>1, 'm'=>'mu'));
               }
               $smarr[0] = '';
               $i++;
       		  }
       }
       for(; $i < $arlen; $i++)
       {
            
            if( !isset( $smarr[$i+1] ) )
            {
                $newarr[$j] = $smarr[$i];
                break;
            }
            $cw = $smarr[$i];
            $nw = $smarr[$i+1];
            $ischeck = false;
            //检测数量词
            if( isset( $this->addonDic['c'][$cw] ) && isset( $this->addonDic['u'][$nw] ) )
            {
                //最大切分时保留合并前的词
                if($this->differMax)
                {
                        $newarr[$j] = chr(0).chr(0x28);
                        $j++;
                        $newarr[$j] = $cw;
                        $j++;
                        $newarr[$j] = $nw;
                        $j++;
                        $newarr[$j] = chr(0).chr(0x29);
                        $j++;
                }
                $newarr[$j] = $cw.$nw;
                if( !isset($this->newWords[$newarr[$j]]) )
                {
                    $this->foundWordStr .= $this->OutStringEncoding( $newarr[$j] ).'/mu, ';
                    $this->SetWordInfos($newarr[$j], array('c'=>1, 'm'=>'mu'));
                }
                $j++; $i++; $ischeck = true;
            }
            //检测前导词(通常是姓)
            else if( isset( $this->addonDic['n'][ $smarr[$i] ] ) )
            {
                $is_rs = false;
                //词语是副词或介词或频率很高的词不作为人名
                if( strlen($nw)==4 )
                {
                    $winfos = $this->GetWordInfos($nw);
                    if(isset($winfos['m']) && ($winfos['m']=='r' || $winfos['m']=='c' || $winfos['c']>500) )
                    {
                    	 $is_rs = true;
                    }
                }
                if( !isset($this->addonDic['s'][$nw]) && strlen($nw)<5 && !$is_rs )
                {
                    //最大切分时保留合并前的姓名
                    if($this->differMax)
                    {
                            $newarr[$j] = chr(0).chr(0x28);
                            $j++;
                            $newarr[$j] = $cw;
                            $j++;
                            $nj = $j;
                            $newarr[$j] = $nw;
                            $j++;
                            $newarr[$j] = chr(0).chr(0x29);
                            $j++;
                    }
                    $newarr[$j] = $cw.$nw;
                    //echo iconv('ucs-2', 'utf-8', $newarr[$j])."<br />";
                    //尝试检测第三个词
                    if( strlen($nw)==2 && isset($smarr[$i+2]) && strlen($smarr[$i+2])==2 && !isset( $this->addonDic['s'][$smarr[$i+2]] ) )
                    {
                        $newarr[$j] .= $smarr[$i+2];
                        if($this->differMax)
                        {
                            $newarr[$nj] .= $smarr[$i+2];
                        }
                        $i++;
                    }
                    if( !isset($this->newWords[$newarr[$j]]) )
                    {
                        $this->SetWordInfos($newarr[$j], array('c'=>1, 'm'=>'nr'));
                        $this->foundWordStr .= $this->OutStringEncoding($newarr[$j]).'/nr, ';
                    }
                    $j++; $i++; $ischeck = true;
                }
            }
            //检测后缀词(地名等)
            else if( isset($this->addonDic['a'][$nw]) )
            {
                $is_rs = false;
                //词语是副词或介词不作为前缀
                if( strlen($cw)>2 )
                {
                    $winfos = $this->GetWordInfos($cw);
                    if(isset($winfos['m']) && ($winfos['m']=='a' || $winfos['m']=='r' || $winfos['m']=='c' || $winfos['c']>500) )
                    {
                    	 $is_rs = true;
                    }
                }
                if( !isset($this->addonDic['s'][$cw]) && !$is_rs )
                {
                    //最大切分时保留合并前的词
                    if($this->differMax)
                    {
                        $newarr[$j] = chr(0).chr(0x28);
                        $j++;
                        $newarr[$j] = $cw;
                        $j++;
                        $newarr[$j] = $nw;
                        $j++;
                        $newarr[$j] = chr(0).chr(0x29);
                        $j++;
                    }
                    $newarr[$j] = $cw.$nw;
                    if( !isset($this->newWords[$newarr[$j]]) )
                    {
                        $this->foundWordStr .= $this->OutStringEncoding($newarr[$j]).'/na, ';
                        $this->SetWordInfos($newarr[$j], array('c'=>1, 'm'=>'na'));
                    }
                    $i++; $j++; $ischeck = true;
                }
            }
            //新词识别（暂无规则）
            else if($this->unitWord)
            {
                if(strlen($cw)==2 && strlen($nw)==2 
                && !isset($this->addonDic['s'][$cw]) && !isset($this->addonDic['t'][$cw]) && !isset($this->addonDic['a'][$cw]) 
                && !isset($this->addonDic['s'][$nw]) && !isset($this->addonDic['c'][$nw]))
                {
                    //最大切分时保留合并前的词
                    if($this->differMax)
                    {
                        $newarr[$j] = chr(0).chr(0x28);
                        $j++;
                        $newarr[$j] = $cw;
                        $j++;
                        $nj = $j;
                        $newarr[$j] = $nw;
                        $j++;
                        $newarr[$j] = chr(0).chr(0x29);
                        $j++;
                    }
                    $newarr[$j] = $cw.$nw;
                    $wf = $nw;
                    //尝试检测第三个词
                    if( isset($smarr[$i+2]) && strlen($smarr[$i+2])==2 && (isset( $this->addonDic['a'][$smarr[$i+2]] ) || isset( $this->addonDic['u'][$smarr[$i+2]] )) )
                    {
                        $newarr[$j] .= $smarr[$i+2];
                        $newarr[$nj] .= $smarr[$i+2];
                        $i++;
                    }
                    if( !isset($this->newWords[$newarr[$j]]) )
                    {
                        $this->foundWordStr .= $this->OutStringEncoding($newarr[$j]).'/ms, ';
                        $this->SetWordInfos($newarr[$j], array('c'=>1, 'm'=>'ms'));
                    }
                    $i++; $j++; $ischeck = true;
                }
            }
            
            //不符合规则
            if( !$ischeck )
            {
                $newarr[$j] = $cw;
              	//二元消岐处理——最大切分模式
                if( $this->differMax && !isset($this->addonDic['s'][$cw]) && strlen($cw) < 5 && strlen($nw) < 7)
                {
                    $slen = strlen($nw);
                    $hasDiff = false;
                    for($y=2; $y <= $slen-2; $y=$y+2)
                    {
                        $nhead = substr($nw, $y-2, 2);
                        $nfont = $cw.substr($nw, 0, $y-2);
                        if( $this->IsWordEnd($nhead) && isset( $this->mainDic[$nhead][1][$nfont] ) )
                        {
                            if( strlen($cw) > 2 ) $j++;
                            $hasDiff = true;
                            $newarr[$j] = $nfont.$nhead;
                        }
                    }
                }
                $j++;
            }
            
       }//end for
       $smarr =  $newarr;
    }
    
    /**
    * 转换最终分词结果到 finallyResult 数组
    * @return void
    */
    function SortFinallyResult()
    {
    	  $newarr = array();
        $i = 0;
        foreach($this->simpleResult as $k=>$v)
        {
            if( empty($v['w']) ) continue;
            if( isset($this->finallyResult[$k]) && count($this->finallyResult[$k]) > 0 )
            {
                foreach($this->finallyResult[$k] as $w)
                {
                    if(!empty($w))
                    {
                    	$newarr[$i]['w'] = $w;
                    	$newarr[$i]['t'] = 20;
                    	$i++;
                    }
                }
            }
            else if($v['t'] != 21)
            {
                $newarr[$i]['w'] = $v['w'];
                $newarr[$i]['t'] = $v['t'];
                $i++;
            }
        }
        $this->finallyResult = $newarr;
        $newarr = '';
  	}
    
    /**
     * 把uncode字符串转换为输出字符串
     * @parem str
     * return string
     */
     function OutStringEncoding( &$str )
     {
        $rsc = $this->SourceResultCharset();
        if( $rsc==1 ) {
            $rsstr = iconv('ucs-2', 'utf-8', $str);
        }
        else if( $rsc==2 ) {
            $rsstr = iconv('utf-8', 'gb18030', iconv('ucs-2', 'utf-8', $str) );
        }
        else{
            $rsstr = iconv('utf-8', 'big5', iconv('ucs-2', 'utf-8', $str) );
        }
        return $rsstr;
     }
    
    /**
     * 获取最终结果字符串（用空格分开后的分词结果）
     * @return string
     */
     function GetFinallyResult($spword=' ', $word_meanings=false)
     {
        $rsstr = '';
        foreach($this->finallyResult as $v)
        {
            if( $this->resultType==2 && ($v['t']==3 || $v['t']==5) )
            {
            	continue;
            }
            $m = '';
            if( $word_meanings )
            {
                $m = $this->GetWordProperty($v['w']);
            }
            $w = $this->OutStringEncoding($v['w']);
            if( $w != ' ' )
            {
                if($word_meanings) {
                    $rsstr .= $spword.$w.$m;
                }
                else {
                    $rsstr .= $spword.$w;
                }
            }
        }
        return $rsstr;
     }
     
    /**
     * 获取粗分结果，不包含粗分属性
     * @return array()
     */
     function GetSimpleResult()
     {
        $rearr = array();
        foreach($this->simpleResult as $k=>$v)
        {
            if( empty($v['w']) ) continue;
            $w = $this->OutStringEncoding($v['w']);
            if( $w != ' ' ) $rearr[] = $w;
        }
        return $rearr;
     }
     
    /**
     * 获取粗分结果，包含粗分属性（1中文词句、2 ANSI词汇（包括全角），3 ANSI标点符号（包括全角），4数字（包括全角），5 中文标点或无法识别字符）
     * @return array()
     */
     function GetSimpleResultAll()
     {
        $rearr = array();
        foreach($this->simpleResult as $k=>$v)
        {
            $w = $this->OutStringEncoding($v['w']);
            if( $w != ' ' )
            {
                $rearr[$k]['w'] = $w;
                $rearr[$k]['t'] = $v['t'];
            }
        }
        return $rearr;
     }
     
    /**
     * 获取索引hash数组
     * @return array('word'=>count,...)
     */
     function GetFinallyIndex()
     {
        $rearr = array();
        foreach($this->finallyResult as $v)
        {
            if( $this->resultType==2 && ($v['t']==3 || $v['t']==5 || isset($this->addonDic['s'][$v['w']]) ) )
            {
            	continue;
            }
            $w = $this->OutStringEncoding($v['w']);
            if( $w == ' ' || $w == '(' || $w == ')' )
            {
                continue;
            }
            if( isset($rearr[$w]) )
            {
            	 $rearr[$w]++;
            }
            else
            {
            	 $rearr[$w] = 1;
            }
        }
        arsort($rearr);
        return $rearr;
     }
     
    /**
     * 获得保存目标编码
     * @return int
     */
     function SourceResultCharset()
     {
        if( preg_match("/^utf/", $this->targetCharSet) ) {
           $rs = 1;
        }
        else if( preg_match("/^gb/", $this->targetCharSet) ) {
           $rs = 2;
        }
        else if( preg_match("/^big/", $this->targetCharSet) ) {
           $rs = 3;
        }
        else {
            $rs = 4;
        }
        return $rs;
     }
     
     /**
     * 导出词典的词条
     * @parem $targetfile 保存位置
     * @return void
     */
     function ExportDict( $targetfile )
     {
        $fp = fopen($targetfile, 'w');
        foreach($this->mainDic as $k=>$v)
        {
            $ik = iconv('ucs-2', 'utf-8', $k);
            foreach( $v[1] as $wk => $wv)
            {
                $arr = $this->GetWordInfos($wk.$k);
                $wd = iconv('ucs-2', 'utf-8', $wk).$ik;
                if($arr != '')
                {
                    fwrite($fp, $wd.','.$arr['c'].','.$arr['m']."\n");
                }
                else
                {
                    continue;
                }
            }
        }
        fclose($fp);
     }
     
     /**
     * 追加新词到内存里的词典
     * @parem $word unicode编码的词
     * @return void
     */
     function AddNewWord( $word )
     {
        
     }
     
     /**
     * 编译词典
     * @parem $sourcefile utf-8编码的文本词典数据文件<参见范例dict/not-build/base_dic_full.txt>
     * @return void
     */
     function MakeDict( $sourcefile, $maxWordLen=16, $target='' )
     {
        if( $target=='' )
        {
            $dicWords = dirname(__FILE__).'/'.$this->mainDicFile;
        }
        else
        {
            $dicWords = dirname(__FILE__).'/'.$target;
        }
        $narr = $earr = array();
        if( !file_exists($sourcefile) )
        {
            echo 'File: '.$sourcefile.' not found!';
            return ;
        }
        $ds = file($sourcefile);
        $i = 0;
        $maxlen = 0;
        foreach($ds as $d)
        {
            $d = trim($d);
            if($d=='') continue;
            list($d, $hot, $mtype) = explode(',', $d);
            if( empty($hot) ) $hot = 0;
            if( empty($mtype) ) $mtype = 'x';
            if( $mtype=='@' ) continue;
            $d = iconv('utf-8', 'ucs-2', $d);
            /*这里用ANSI混编
            if( strlen($mtype)==1 )
            {
                $mtype = chr(0).$mtype;
            }*/
            $nlength = strlen($d)-2;
            if( $nlength >= $maxWordLen ) continue;
            $maxlen = $nlength > $maxlen ? $nlength : $maxlen;
            $endc = substr($d, $nlength, 2);
            $n = hexdec(bin2hex($endc));
            if( isset($narr[$endc]) )
            {
                $narr[$endc]['w'][$narr[$endc]['c']] = $this->GetWord($d);
                $narr[$endc]['n'][$narr[$endc]['c']] = $hot;
                $narr[$endc]['m'][$narr[$endc]['c']] = $mtype;
                $narr[$endc]['l'] += $nlength;
                $narr[$endc]['c']++;
                $narr[$endc]['h'][$nlength] = isset($narr[$endc]['h'][$nlength]) ? $narr[$endc]['h'][$nlength]+1 : 1;
            }
            else
            {
                $narr[$endc]['w'][0] = $this->GetWord($d);
                $narr[$endc]['n'][0] = $hot;
                $narr[$endc]['m'][0] = $mtype;
                $narr[$endc]['l'] = $nlength;
                $narr[$endc]['c'] = 1;
                $narr[$endc]['h'][$nlength] = 1;
            }
        }
        $alllen = $n = $max = $bigw = $bigc = 0;
        $fp = fopen($dicWords, 'wb');
        foreach($narr as $k=>$v)
        {
            fwrite($fp, $k);
            /* 保存指定长度的词条个数信息（此项对提升分词速度不明显，但会影响载入时间）
            for($i=2; $i <= 12; $i = $i+2)
            {
                if( empty($v['h'][$i]) ) {
                    fwrite($fp, chr(0).chr(0));
                }
                else {
                    fwrite($fp, pack('n', $v['h'][$i]));
                }
            }*/
            $allstr = '';
            foreach($v['w']  as $n=>$w)
            {
                //$hot = pack('n', $narr[$k]['n'][$n]);
                $hot = $narr[$k]['n'][$n];
                $mtype = $narr[$k]['m'][$n];
                $allstr .= ($allstr=='' ? $w._SP_.$hot._SP2_.$mtype : _SP_.$w._SP_.$hot._SP2_.$mtype);
            }
            $alLen = strlen($allstr);
            $max = $alLen > $max ? $alLen : $max;
            fwrite($fp, pack('n', $alLen) );
            fwrite($fp, $allstr);
        }
        fclose($fp);
     }
     
     /**
     * 获得词的前部份
     * @parem $str 单词
     * @return void
     */
     function GetWord($str)
     {
        $newstr = '';
        for($i=0; $i < strlen($str)-3; $i++)
        {
            $newstr .= $str[$i];
            $newstr .= $str[$i+1];
            $i++;
        }
        return $newstr;
     }
    
}

?>