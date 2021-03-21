<?php
/**
 * 数据库操作
 *
 * @version   $Id: sys_data_done.php 1 17:19 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
@ob_start();
@set_time_limit(0);
require_once dirname(__FILE__) . '/config.php';
CheckPurview('sys_Data');
if (empty($dopost)) {
    $dopost = '';
}

$bkdir = DEDEDATA . '/' . $cfg_backup_dir;

//跳转到一下页的JS
$gotojs = "function GotoNextPage(){document.gonext." . "submit();}" . "\r\nset" . "Timeout('GotoNextPage()',500);";
$dojs = "<script language='javascript'>$gotojs</script>";

$isstruct = 1;
$iszip = 1;
if (empty($startpos)) {
    $startpos = 0;
}
$time = time();

$tables = explode(',', $tablearr);


/*--------------------
备份数据
function __bak_data();
--------------------*/
if ($dopost == 'bak') {
    if (empty($tablearr)) {
        ShowMsg('你没选中任何表！', 'javascript:;');
        exit();
    }
    if (!is_dir($bkdir)) {
        MkdirAll($bkdir, $cfg_dir_purview);
        CloseFtp();
    }

    if (empty($nowtable)) {
        $nowtable = '';
    }
    if (empty($fsize)) {
        $fsize = 20480;
    }
    $fsizeb = $fsize * 1024;
    
    //第一页的操作
    if ($nowtable == '') {
        $tmsg = '';
        $dh = dir($bkdir);
        while ($filename = $dh->read()) {
            if (!preg_match("#txt$#", $filename)) {
                continue;
            }
            $filename = $bkdir . "/$filename";
            if (!is_dir($filename)) {
                unlink($filename);
            }
        }
        $dh->close();
        $tmsg .= "清除备份目录旧数据完成...<br />";

        if ($isstruct == 1) {
            $tableStruct = "";
            foreach ($tables as $t) {
                $tableStruct .= "DROP TABLE IF EXISTS `$t`;\r\n";
                $dsql->SetQuery("SHOW CREATE TABLE " . $dsql->dbName . "." . $t);
                $dsql->Execute('me');
                $row = $dsql->GetArray('me', MYSQLI_BOTH);
                $row[1] = preg_replace("#AUTO_INCREMENT=([0-9]{1,})[ \r\n\t]{1,}#i", "", $row[1]);
                $tableStruct .= $row[1].";\r\n";
            }
            $bkfile = $bkdir . "/tables_struct--1--{$time}--".substr(md5($time . mt_rand(1000, 5000) . "6E‌4D‌4O6G‌49‌3Y6D‌45‌546C‌54‌3X6E‌48‌4V6C‌54‌4C6G‌4L‌5B"), 0, 8).".txt";
            $fp = fopen($bkfile, "w");
            fwrite($fp, $tableStruct);
            fclose($fp);
            $tmsg .= "备份数据表结构信息完成...<br />";
        }
        $tmsg .= "<font color='red'>正在进行数据备份的初始化工作，请稍后...</font>";
        $doneForm = "<form name='gonext' method='post' action='sys_data_done.php'>
           <input type='hidden' name='isstruct' value='$isstruct' />
           <input type='hidden' name='dopost' value='bak' />
           <input type='hidden' name='fsize' value='$fsize' />
           <input type='hidden' name='tablearr' value='$tablearr' />
           <input type='hidden' name='nowtable' value='{$tables[0]}' />
           <input type='hidden' name='startpos' value='0' />
           <input type='hidden' name='iszip' value='$iszip' />\r\n</form>\r\n{$dojs}\r\n";
        PutInfo($tmsg, $doneForm);
        exit();
    }
    //执行分页备份
    else {
        $j = 0;
        $fs = array();
        $bakStr = '';

        //分析表里的字段信息
        $dsql->GetTableFields($nowtable);
        $intable = "INSERT INTO `$nowtable` VALUES(";

        while ($r = $dsql->GetFieldObject()) {
            $fs[$j] = trim($r->name);
            $j++;
        }

        $fsd = $j - 1;

        //读取表的内容
        $dsql->SetQuery("SELECT * FROM `$nowtable` ");
        $dsql->Execute();
        $m = 0;
        $bakfilename = "$bkdir/{$nowtable}--{$startpos}--{$time}--".substr(md5($time . mt_rand(1000, 5000) . "6E‌4D‌4O6G‌49‌3Y6D‌45‌546C‌54‌3X6E‌48‌4V6C‌54‌4C6G‌4L‌5B"), 0, 8).".txt";
        while ($row2 = $dsql->GetArray()) {
            if ($m < $startpos) {
                $m++;
                continue;
            }
            //检测数据是否达到规定大小
            if (strlen($bakStr) > $fsizeb) {
                $fp = fopen($bakfilename, "w");
                fwrite($fp, $bakStr);
                fclose($fp);
                $tmsg = "<font color='red'>完成到{$m}条记录的备份，继续备份{$nowtable}...</font>";
                $doneForm = "<form name='gonext' method='post' action='sys_data_done.php'>
                <input type='hidden' name='isstruct' value='$isstruct' />
                <input type='hidden' name='dopost' value='bak' />
                <input type='hidden' name='fsize' value='$fsize' />
                <input type='hidden' name='tablearr' value='$tablearr' />
                <input type='hidden' name='nowtable' value='$nowtable' />
                <input type='hidden' name='startpos' value='$m' />
                <input type='hidden' name='iszip' value='$iszip' />\r\n</form>\r\n{$dojs}\r\n";
                PutInfo($tmsg, $doneForm);
                exit();
            }

            //正常情况
            $line = $intable;

            for ($j = 0; $j <= $fsd; $j++) {
                if ($j < $fsd) {
                    $line .= "'" . RpLine(addslashes($row2[$fs[$j]])) . "',";
                } else {
                    $line .= "'" . RpLine(addslashes($row2[$fs[$j]])) . "');\r\n";
                }
            }
            $m++;
            $bakStr .= $line;
        }

        //如果数据比卷设置值小
        if ($bakStr != '') {
            $fp = fopen($bakfilename, "w");
            fwrite($fp, $bakStr);
            fclose($fp);
        }
        for ($i = 0; $i < count($tables); $i++) {
            if ($tables[$i] == $nowtable) {
                if (isset($tables[$i + 1])) {
                    $nowtable = $tables[$i + 1];
                    $startpos = 0;
                    break;
                } else {
                    ShowMsg("完成所有数据备份！备份文件位置：/data/backupdata", 'sys_data.php');
                    exit();
                }
            }
        }
        $tmsg = "<font color='red'>完成到{$m}条记录的备份，继续备份{$nowtable}...</font>";
        $doneForm = "<form name='gonext' method='post' action='sys_data_done.php?dopost=bak'>
          <input type='hidden' name='isstruct' value='$isstruct' />
          <input type='hidden' name='fsize' value='$fsize' />
          <input type='hidden' name='tablearr' value='$tablearr' />
          <input type='hidden' name='nowtable' value='$nowtable' />
          <input type='hidden' name='startpos' value='$startpos'>\r\n</form>\r\n{$dojs}\r\n";
        PutInfo($tmsg, $doneForm);
        exit();
    }
    //分页备份代码结束
}
/*-------------------------
还原数据
function __re_data();
-------------------------*/
else if ($dopost == 'redat') {
    if ($bakfiles == '') {
        ShowMsg('没指定任何要还原的文件!', 'javascript:;');
        exit();
    }
    $bakfilesTmp = $bakfiles;
    $bakfiles = explode(',', $bakfiles);
    if (empty($countfiles)) {
        $countfiles = count($bakfiles);
    }

    foreach ($bakfiles as $filename) {
        if(preg_match("#tables_struct#", $filename)) {
            $structfile = $filename;
        }
        else if(filesize("$bkdir/$filename") > 0 ) {
            $filelists[] = $filename;
        }
    }

    if (empty($structfile)) {
        $structfile = "";
    }
    if (empty($startgo)) {
        $startgo = 0;
    }
    if ($startgo == 0 && $structfile != '') {
        $tbdata = '';
        $fp = fopen("$bkdir/$structfile", 'r');
        while (!feof($fp)) {
            $tbdata .= fgets($fp, 1024);
        }
        fclose($fp);
        $querys = explode(';', $tbdata);

        foreach ($querys as $q) {
            $dsql->ExecuteNoneQuery(trim($q) . ';');
        }
        $bakfilesTmp = preg_replace("#" . $structfile . "[,]{0,1}#", "", $bakfilesTmp);
        $tmsg = "<font color='red'>完成数据表信息还原，准备还原数据...</font>";
        $doneForm = "<form name='gonext' method='post' action='sys_data_done.php?dopost=redat'>
        <input type='hidden' name='startgo' value='1' />
        <input type='hidden' name='bakfiles' value='$bakfilesTmp' />
        <input type='hidden' name='countfiles' value='$countfiles' />
        </form>\r\n{$dojs}\r\n";
        PutInfo($tmsg, $doneForm, $countfiles - count(explode(',', $bakfilesTmp)), $countfiles);
        exit();
    } else {
        $nowfile = $filelists[0];
        $bakfilesTmp = preg_replace("#" . $nowfile . "[,]{0,1}#", "", $bakfilesTmp);
        $oknum = 0;
        if (filesize("$bkdir/$nowfile") > 0) {
            $fp = fopen("$bkdir/$nowfile", 'r');
            while (!feof($fp)) {
                $line = trim(fgets($fp, 512 * 1024));
                if ($line == "") {
                    continue;
                }

                $rs = $dsql->ExecuteNoneQuery($line);
                if ($rs) {
                    $oknum++;
                }

            }
            fclose($fp);
        }
        if ($bakfilesTmp == "") {
            ShowMsg("成功还原所有的文件的数据!", 'sys_data.php');
            exit();
        }
        $val = $countfiles - count(explode(',', $bakfilesTmp));
        $tmsg = "成功还原<br/>{$nowfile}<br/>{$oknum}条记录<br/><br/><progress class='' uk-progress''  value='{$val}'  max='{$countfiles}' ></progress><br/><br/>正在准备还原其它数据...";
        $doneForm = "<form name='gonext' method='post' action='sys_data_done.php?dopost=redat'>
        <input type='hidden' name='startgo' value='1' />
        <input type='hidden' name='bakfiles' value='$bakfilesTmp' />
        <input type='hidden' name='countfiles' value='$countfiles' />
        </form>\r\n{$dojs}\r\n";
        PutInfo($tmsg, $doneForm, $countfiles - count(explode(',', $bakfilesTmp)), $countfiles);
        exit();
    }
}

function PutInfo($msg1, $msg2)
{
    global $cfg_assets_dir, $cfg_soft_lang;
    $msginfo = "<html>\n<head>
        <meta http-equiv='Content-Type' content='text/html; charset={$cfg_soft_lang}' />
        <title>DEDECMS 提示信息</title>
        <meta name='copyright' content='2007-2021 DedeCMS, 上海卓卓网络科技有限公司 (DesDev, Inc.)' />
        <link rel='icon' href='/favicon.ico' />
        <!-- CSS FILES -->
        <link rel='stylesheet' type='text/css' href='{$cfg_assets_dir}/pkg/uikit/css/uikit.min.css' />
        <link rel='stylesheet' type='text/css' href='{$cfg_assets_dir}/css/manage.dede.css'>
        <base target='_self'/>\n</head>
        <body leftmargin='0' topmargin='0'>\n<br/>
        <center style='width:450px' class='uk-container'>
        <div class='uk-card uk-card-small uk-card-default' style='margin-top: 50px;'>
        <div class='uk-card-header'  style='height:20px'>DedeCMS 提示信息！</div>
        <br/><span style='line-height:160%'>{$msg1}</span>
        <br/><br/></div>\r\n{$msg2} <br/>";
    echo $msginfo . "</center>\n</body>\n</html>";
}

function RpLine($str)
{
    $str = str_replace("\r", "\\r", $str);
    $str = str_replace("\n", "\\n", $str);
    return $str;
}
