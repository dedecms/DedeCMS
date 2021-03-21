<?php if (!defined('DEDEINC')) {exit("DedeCMS Error: Request Error!");
}
/**
 * 管理员后台基本函数
 *
 * @version   $Id:inc_fun_funAdmin.php 1 13:58 2010年7月5日 $
 * @package   DedeCMS.Libraries
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */

/**
 *  获取拼音信息
 *
 * @access public
 * @param  string $str     字符串
 * @param  int    $ishead  是否为首字母
 * @param  int    $isclose 解析后是否释放资源
 * @return string
 */
function SpGetPinyin($str, $ishead = 0, $isclose = 1)
{
    global $pinyins;
    $restr = '';
    $str = trim($str);
    $slen = strlen($str);
    if ($slen < 2) {
        return $str;
    
    }
    if (@count($pinyins) == 0) {
        $fp = fopen(DEDEINC . '/data/pinyin.dat', 'r');
        while (!feof($fp)) {
            $line = trim(fgets($fp));
            $pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
        
        }
        fclose($fp);
    
    }
    for ($i = 0; $i < $slen; $i++) {
        if (ord($str[$i]) > 0x80) {
            $c = $str[$i] . $str[$i + 1];
            $i++;
            if (isset($pinyins[$c])) {
                if ($ishead == 0) {
                    $restr .= $pinyins[$c];
                
                } else {
                    $restr .= $pinyins[$c][0];
                
                }
            
            } else {
                $restr .= "_";
            
            }
        
        } else if (preg_match("/[a-z0-9]/i", $str[$i])) {
            $restr .= $str[$i];
        
        } else {
            $restr .= "_";
        
        }
    
    }
    if ($isclose == 0) {
        unset($pinyins);
    
    }
    return $restr;

}

/**
 *  创建目录
 *
 * @access public
 * @param  string $spath 目录名称
 * @return string
 */
function SpCreateDir($spath)
{
    global $cfg_dir_purview, $cfg_basedir, $cfg_ftp_mkdir, $isSafeMode;
    if ($spath == '') {
        return true;
    
    }
    $flink = false;
    $truepath = $cfg_basedir;
    $truepath = str_replace("\\", "/", $truepath);
    $spaths = explode("/", $spath);
    $spath = "";
    foreach ($spaths as $spath) {
        if ($spath == "") {
            continue;
        
        }
        $spath = trim($spath);
        $truepath .= "/" . $spath;
        if (!is_dir($truepath) || !is_writeable($truepath)) {
            if (!is_dir($truepath)) {
                $isok = MkdirAll($truepath, $cfg_dir_purview);
            
            } else {
                $isok = ChmodAll($truepath, $cfg_dir_purview);
            
            }
            if (!$isok) {
                echo "创建或修改目录：" . $truepath . " 失败！<br>";
                CloseFtp();
                return false;
            
            }
        
        }
    
    }
    CloseFtp();
    return true;

}

function jsScript($js)
{
    $out = "<script type=\"text/javascript\">";
    $out .= "//<![CDATA[\n";
    $out .= $js;
    $out .= "\n//]]>";
    $out .= "</script>\n";

    return $out;

}

/**
 *  获取编辑器
 *
 * @access public
 * @param  string $fname      表单名称
 * @param  string $fvalue     表单值
 * @param  string $nheight    内容高度
 * @param  string $etype      编辑器类型
 * @param  string $gtype      获取值类型
 * @param  string $isfullpage 是否全屏
 * @return string
 */
function SpGetEditor($fname, $fvalue, $nheight = "350", $etype = "Basic", $gtype = "print", $isfullpage = "false", $bbcode = false)
{
    global $cfg_ckeditor_initialized;
    if (!isset($GLOBALS['cfg_html_editor'])) {
        $GLOBALS['cfg_html_editor'] = 'fck';
    
    }
    if ($gtype == "") {
        $gtype = "print";
    
    }
    if ($GLOBALS['cfg_html_editor'] == 'fck') {
        include_once DEDEINC . '/FCKeditor/fckeditor.php';
        $fck = new FCKeditor($fname);
        $fck->BasePath = $GLOBALS['cfg_cmspath'] . '/include/FCKeditor/';
        $fck->Width = '100%';
        $fck->Height = $nheight;
        $fck->ToolbarSet = $etype;
        $fck->Config['FullPage'] = $isfullpage;
        if ($GLOBALS['cfg_fck_xhtml'] == 'Y') {
            $fck->Config['EnableXHTML'] = 'true';
            $fck->Config['EnableSourceXHTML'] = 'true';
        
        }
        $fck->Value = $fvalue;
        if ($gtype == "print") {
            $fck->Create();
        
        } else {
            return $fck->CreateHtml();
        
        }
    
    } else if ($GLOBALS['cfg_html_editor'] == 'ckeditor') {
        $addConfig = "";
        if (defined("DEDEADMIN")) {
            $addConfig = ",{filebrowserImageUploadUrl:'./dialog/select_images_post.php?'}";
        
        }
        $code = <<<EOT
<script src="{$GLOBALS['cfg_assets_dir']}/pkg/ckeditor/ckeditor.js"></script>
<textarea id="{$fname}" name="{$fname}" rows="8" cols="60">{$fvalue}</textarea>
<script>
var editor = CKEDITOR.replace('{$fname}'{$addConfig});
</script>
EOT;
        if ($gtype == "print") {
            echo $code;
        
        } else {
            return $code;
        
        }
    
    } 

}

/**
 *  获取更新信息
 *
 * @return void
 */
function SpGetNewInfo()
{
    global $cfg_version, $dsql;
    $nurl = $_SERVER['HTTP_HOST'];
    if (preg_match("#[a-z\-]{1,}\.[a-z]{2,}#i", $nurl)) {
        $nurl = urlencode($nurl);
    
    } else {
        $nurl = "test";
    
    }
    $phpv = phpversion();
    $sp_os = PHP_OS;
    $mysql_ver = $dsql->GetVersion();

    $add_query = '';

    $query = " SELECT COUNT(*) AS dd FROM `#@__arctiny` ";
    $row2 = $dsql->GetOne($query);
    if ($row2) {
        $add_query .= "&acount={$row2['dd']}";
    }

    $offUrl = "http://n" . "ew" . "ver.a" . "pi.de" . "decm" . "s.com" . "/in" . "dex.php?c=i" . "nfo58&ve" . "rsi" . "on={$cfg_version}&form" . "url={$nurl}&ph" . "pver={$phpv}&o" . "s={$sp_os}&mysql" . "ver={$mysql_ver}{$add_query}";
    return $offUrl;

}
