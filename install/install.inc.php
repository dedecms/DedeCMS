<?php
/**
 * @version   $Id: install.inc.php 1 13:41 2010年7月26日 $
 * @package   DedeCMS.Install
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
function RunMagicQuotes(&$str)
{
    if (!get_magic_quotes_gpc()) {
        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $str[$key] = RunMagicQuotes($val);
            }
        } else {
            $str = addslashes($str);
        }

    }
    return $str;
}

function GetBackAlert($msg, $isstop = 0)
{
    global $s_lang;
    $msg = str_replace('"', '`', $msg);
    if ($isstop == 1) {
        $msg = "<script>\r\n<!--\r\n alert(\"{$msg}\");\r\n-->\r\n</script>\r\n";
    } else {
        $msg = "<script>\r\n<!--\r\n alert(\"{$msg}\");history.go(-1);\r\n-->\r\n</script>\r\n";
    }

    $msg = "<meta http-equiv=content-type content='text/html; charset={$s_lang}'>\r\n" . $msg;
    return $msg;
}

function TestWrite($d)
{
    $tfile = '_dedet.txt';
    $d = preg_replace("#\/$#", '', $d);
    $fp = @fopen($d . '/' . $tfile, 'w');
    if (!$fp) {
        return false;
    } else {
        fclose($fp);
        $rs = @unlink($d . '/' . $tfile);
        if ($rs) {
            return true;
        } else {
            return false;
        }

    }
}

function ReWriteConfigAuto()
{
    global $dsql;
    $configfile = DEDEDATA . '/config.cache.inc.php';
    if (!is_writeable($configfile)) {
        echo "配置文件'{$configfile}'不支持写入，无法修改系统配置参数！";
        //ClearAllLink();
        exit();
    }
    $fp = fopen($configfile, 'w');
    flock($fp, 3);
    fwrite($fp, "<" . "?php\r\n");
    $dsql->SetQuery("Select `varname`,`type`,`value`,`groupid` From `#@__sysconfig` order by aid asc ");
    $dsql->Execute();
    while ($row = $dsql->GetArray()) {
        if ($row['type'] == 'number') {
            fwrite($fp, "\${$row['varname']} = " . $row['value'] . ";\r\n");
        } else {
            fwrite($fp, "\${$row['varname']} = '" . str_replace("'", '', $row['value']) . "';\r\n");
        }

    }
    fwrite($fp, "?" . ">");
    fclose($fp);
}

//更新栏目缓存
function UpDateCatCache()
{
    global $conn, $cfg_multi_site, $dbprefix;
    $cache1 = DEDEDATA . "/cache/inc_catalog_base.inc";
    $rs = mysqli_query($conn, "Select id,reid,channeltype,issend,typename From `" . $dbprefix . "arctype`");

    $fp1 = fopen($cache1, 'w');
    $phph = '?';
    $fp1Header = "<{$phph}php\r\nglobal \$cfg_Cs;\r\n\$cfg_Cs=array();\r\n";
    fwrite($fp1, $fp1Header);
    while ($row = mysqli_fetch_array($rs)) {
        $row['typename'] = base64_encode($row['typename']);
        fwrite($fp1, "\$cfg_Cs[{$row['id']}]=array({$row['reid']},{$row['channeltype']},{$row['issend']},'{$row['typename']}');\r\n");
    }
    fwrite($fp1, "{$phph}>");
    fclose($fp1);
}

function IsDownLoad($url)
{
    if (file_exists($url . '.xml')) {
        return true;
    } else {
        return false;
    }
}
