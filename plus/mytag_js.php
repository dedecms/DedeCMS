<?php
/**
 *
 * 自定义标签js调用方式
 *
 * @version        $Id: mytag_js.php 1 20:55 2010年7月8日 $
 * @package        DedeCMS.Site
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(DEDEINC.'/arc.partview.class.php');

if(isset($arcID)) $aid = $arcID;
$arcID = $aid = (isset($aid) && is_numeric($aid)) ? $aid : 0;
if($aid==0) die(" document.write('Request Error!'); ");

$cacheFile = DEDEDATA.'/cache/mytag-'.$aid.'.htm';
if( isset($nocache) || !file_exists($cacheFile) || time() - filemtime($cacheFile) > $cfg_puccache_time )
{
    $pv = new PartView();
    $row = $pv->dsql->GetOne(" SELECT * FROM `#@__mytag` WHERE aid='$aid' ");
    if(!is_array($row))
    {
        $myvalues = "<!--\r\ndocument.write('Not found input!');\r\n-->";
    }
    else
    {
        $tagbody = '';
        if($row['timeset']==0)
        {
            $tagbody = $row['normbody'];
        }
        else
        {
            $ntime = time();
            if($ntime>$row['endtime'] || $ntime < $row['starttime']) {
                $tagbody = $row['expbody'];
            }
            else {
                $tagbody = $row['normbody'];
            }
        }
        $pv->SetTemplet($tagbody, 'string');
        $myvalues  = $pv->GetResult();

        global $cfg_disable_funs;
        $cfg_disable_funs = isset($cfg_disable_funs) ? $cfg_disable_funs : 'phpinfo,eval,assert,exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source,file_put_contents,fsockopen,fopen,fwrite';
        foreach (explode(",", $cfg_disable_funs) as $value) {
            $value = str_replace(" ", "", $value);
            if(!empty($value) && preg_match("#[^a-z]+['\"]*{$value}['\"]*[\s]*[(]#i", " {$myvalues}") == TRUE) {
                $myvalues = dede_htmlspecialchars($myvalues);
                die("DedeCMS提示：当前页面中存在恶意代码！<pre>{$myvalues}</pre>");
            }
            if(!empty($value) && preg_match("#[^<]+<\?(php|=)#i", " {$myvalues}") == TRUE) {
                $myvalues = dede_htmlspecialchars($myvalues);
                die("DedeCMS提示：当前页面中存在恶意代码！<pre>{$myvalues}</pre>");
            }
        }

        $myvalues = str_replace('"','\"',$myvalues);
        $myvalues = str_replace("\r","\\r",$myvalues);
        $myvalues = str_replace("\n","\\n",$myvalues);
        $myvalues =  "<!--\r\ndocument.write(\"{$myvalues}\");\r\n-->\r\n";
        file_put_contents($cacheFile, $myvalues);
        /* 使用 file_put_contents替换下列代码提高执行效率
        $fp = fopen($cacheFile, 'w');
        fwrite($fp, $myvalues);
        fclose($fp);
        */
    }
}
include $cacheFile;