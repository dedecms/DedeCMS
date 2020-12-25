<?php if (!defined('DEDEINC')) {
    exit("Request Error!");
}

/**
 * 数据库类
 * 说明:系统底层数据库核心类
 *      调用这个类前,请先设定这些外部变量
 *      $GLOBALS['cfg_dbhost'];
 *      $GLOBALS['cfg_dbuser'];
 *      $GLOBALS['cfg_dbpwd'];
 *      $GLOBALS['cfg_dbname'];
 *      $GLOBALS['cfg_dbprefix'];
 *
 * @version   $Id: dedesqli.class.php 1 15:00 2011-1-21  $
 * @package   DedeCMS.Libraries
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
@set_time_limit(0);
// 在工程所有文件中均不需要单独初始化这个类，可直接用 $dsql 或 $db 进行操作
// 为了防止错误，操作完后不必关闭数据库
$dsql = $dsqlitete = $db = new DedeSqlite(false);
/**
 * Dede MySQLi数据库类
 *
 * @package    DedeSqli
 * @subpackage DedeCMS.Libraries
 * @link       http://www.dedecms.com
 */
if (!defined('MYSQLI_BOTH')) {
    define('MYSQLI_BOTH', MYSQLI_BOTH);
}
class DedeSqlite
{
    public $linkID;
    public $dbHost;
    public $dbUser;
    public $dbPwd;
    public $dbName;
    public $dbPrefix;
    public $result;
    public $queryString;
    public $parameters;
    public $isClose;
    public $safeCheck;
    public $showError = false;
    public $recordLog = false; // 记录日志到data/mysqli_record_log.inc便于进行调试
    public $isInit = false;
    public $pconnect = false;
    public $_fixObject;

    //用外部定义的变量初始类，并连接数据库
    public function __construct($pconnect = false, $nconnect = false)
    {
        $this->isClose = false;
        $this->safeCheck = true;
        $this->pconnect = $pconnect;
        if ($nconnect) {
            $this->Init($pconnect);
        }
    }

    public function DedeSql($pconnect = false, $nconnect = true)
    {
        $this->__construct($pconnect, $nconnect);
    }

    public function Init($pconnect = false)
    {
        $this->linkID = 0;
        //$this->queryString = '';
        //$this->parameters = Array();
        $this->dbHost = $GLOBALS['cfg_dbhost'];
        $this->dbUser = $GLOBALS['cfg_dbuser'];
        $this->dbPwd = $GLOBALS['cfg_dbpwd'];
        $this->dbName = $GLOBALS['cfg_dbname'];
        $this->dbPrefix = $GLOBALS['cfg_dbprefix'];
        $this->result["me"] = 0;
        $this->Open($pconnect);
    }

    //用指定参数初始数据库信息
    public function SetSource($host, $username, $pwd, $dbname, $dbprefix = "dede_")
    {
        $this->dbHost = $host;
        $this->dbUser = $username;
        $this->dbPwd = $pwd;
        $this->dbName = $dbname;
        $this->dbPrefix = $dbprefix;
        $this->result["me"] = 0;
    }

    //设置SQL里的参数
    public function SetParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    //连接数据库
    public function Open($pconnect = false)
    {
        global $dsqlite;
        //连接数据库
        if ($dsqlite && !$dsqlite->isClose && $dsqlite->isInit) {
            $this->linkID = $dsqlite->linkID;
        } else {

            $this->linkID = new SQLite3(DEDEDATA . '/' . $this->dbName . '.db');

            //复制一个对象副本
            CopySQLiPoint($this);
        }

        //处理错误，成功连接则选择数据库
        if (!$this->linkID) {
            $this->DisplayError("DedeCMS错误警告：<font color='red'>连接数据库失败，可能数据库密码不对或数据库服务器出错！</font>");
            exit();
        }
        $this->isInit = true;
        return true;
    }

    //为了防止采集等需要较长运行时间的程序超时，在运行这类程序时设置系统等待和交互时间
    public function SetLongLink()
    {
        @mysqli_query("SET interactive_timeout=3600, wait_timeout=3600 ;", $this->linkID);
    }

    //获得错误描述
    public function GetError()
    {
        $str = mysqli_error($this->linkID);
        return $str;
    }

    //关闭数据库
    //mysql能自动管理非持久连接的连接池
    //实际上关闭并无意义并且容易出错，所以取消这函数
    public function Close($isok = false)
    {
        $this->FreeResultAll();
        if ($isok) {
            $this->linkID->close();
            $this->isClose = true;
            $GLOBALS['dsql'] = null;
        }
    }

    //定期清理死连接
    public function ClearErrLink()
    {
    }

    //关闭指定的数据库连接
    public function CloseLink($dblink)
    {
    }

    public function Esc($_str)
    {
        return addslashes($_str);
    }

    //执行一个不返回结果的SQL语句，如update,delete,insert等
    public function ExecuteNoneQuery($sql = '')
    {
        global $dsqlite;
        if (!$dsqlite->isInit) {
            $this->Init($this->pconnect);
        }
        if ($dsqlite->isClose) {
            $this->Open(false);
            $dsqlite->isClose = false;
        }
        if (!empty($sql)) {
            $this->SetQuery($sql);
        } else {
            return false;
        }
        if (is_array($this->parameters)) {
            foreach ($this->parameters as $key => $value) {
                $this->queryString = str_replace("@" . $key, "'$value'", $this->queryString);
            }
        }
        //SQL语句安全检查
        if ($this->safeCheck) {
            CheckSql($this->queryString, 'update');
        }

        $t1 = ExecTime();

        $rs = $this->linkID->exec($this->queryString);

        //查询性能测试
        if ($this->recordLog) {
            $queryTime = ExecTime() - $t1;
            $this->RecordLog($queryTime);
            //echo $this->queryString."--{$queryTime}<hr />\r\n";
        }
        return $rs;
    }

    //执行一个返回影响记录条数的SQL语句，如update,delete,insert等
    public function ExecuteNoneQuery2($sql = '')
    {
        global $dsqlite;
        if (!$dsqlite->isInit) {
            $this->Init($this->pconnect);
        }
        if ($dsqlite->isClose) {
            $this->Open(false);
            $dsqlite->isClose = false;
        }

        if (!empty($sql)) {
            $this->SetQuery($sql);
        }
        if (is_array($this->parameters)) {
            foreach ($this->parameters as $key => $value) {
                $this->queryString = str_replace("@" . $key, "'$value'", $this->queryString);
            }
        }
        $t1 = ExecTime();
        $this->linkID->exec($this->queryString);

        //查询性能测试
        if ($this->recordLog) {
            $queryTime = ExecTime() - $t1;
            $this->RecordLog($queryTime);
            //echo $this->queryString."--{$queryTime}<hr />\r\n";
        }

        return $this->linkID->changes();
    }

    public function ExecNoneQuery($sql = '')
    {
        return $this->ExecuteNoneQuery($sql);
    }

    public function GetFetchRow($id = 'me')
    {
        return $this->result[$id]->numColumns();
    }

    public function GetAffectedRows()
    {
        return $this->linkID->changes();
    }

    //执行一个带返回结果的SQL语句，如SELECT，SHOW等
    public function Execute($id = "me", $sql = '')
    {
        global $dsqlite;
        if (!$dsqlite->isInit) {
            $this->Init($this->pconnect);
        }
        if ($dsqlite->isClose) {
            $this->Open(false);
            $dsqlite->isClose = false;
        }
        if (!empty($sql)) {
            $this->SetQuery($sql);
        }
        //SQL语句安全检查
        if ($this->safeCheck) {
            CheckSql($this->queryString);
        }

        $t1 = ExecTime();
        //var_dump($this->queryString);

        $this->result[$id] = $this->linkID->query($this->queryString);

        //var_dump(mysql_error());

        //查询性能测试
        if ($this->recordLog) {
            $queryTime = ExecTime() - $t1;
            $this->RecordLog($queryTime);
            //echo $this->queryString."--{$queryTime}<hr />\r\n";
        }

        if ($this->result[$id] === false) {
            $this->DisplayError($this->linkID->lastErrorMsg() . " <br />Error sql: <font color='red'>" . $this->queryString . "</font>");
        }
    }

    public function Query($id = "me", $sql = '')
    {
        $this->Execute($id, $sql);
    }

    //执行一个SQL语句,返回前一条记录或仅返回一条记录
    public function GetOne($sql = '', $acctype = MYSQLI_ASSOC)
    {
        global $dsqlite;
        if (!$dsqlite->isInit) {
            $this->Init($this->pconnect);
        }
        if ($dsqlite->isClose) {
            $this->Open(false);
            $dsqlite->isClose = false;
        }
        if (!empty($sql)) {
            if (!preg_match("/LIMIT/i", $sql)) {
                $this->SetQuery(preg_replace("/[,;]$/i", '', trim($sql)) . " LIMIT 0,1;");
            } else {
                $this->SetQuery($sql);
            }

        }
        $this->Execute("one");
        $arr = $this->GetArray("one", $acctype);
        if (!is_array($arr)) {
            return '';
        } else {
            $this->result["one"]->reset();return ($arr);
        }
    }

    //执行一个不与任何表名有关的SQL语句,Create等
    public function ExecuteSafeQuery($sql, $id = "me")
    {
        global $dsqlite;
        if (!$dsqlite->isInit) {
            $this->Init($this->pconnect);
        }
        if ($dsqlite->isClose) {
            $this->Open(false);
            $dsqlite->isClose = false;
        }
        $this->result[$id] = $this->linkID->query($sql);
    }

    //返回当前的一条记录并把游标移向下一记录
    // SQLITE3_ASSOC、SQLITE3_NUM、SQLITE3_BOTH
    public function GetArray($id = "me", $acctype = SQLITE3_ASSOC)
    {
        switch ($acctype) {
        case MYSQLI_ASSOC:
            $acctype = SQLITE3_ASSOC;
            break;
        case MYSQLI_NUM:
            $acctype = SQLITE3_NUM;
            break;
        default:
            $acctype = SQLITE3_BOTH;
            break;
        }

        if ($this->result[$id] === 0) {
            return false;
        } else {
            $rs = $this->result[$id]->fetchArray($acctype);
            if (!$rs) {
                $this->result[$id] = 0;
                return false;
            }
            return $rs;
        }
    }

    public function GetObject($id = "me")
    {
        if (!isset($this->_fixObject[$id])) {
            $this->_fixObject[$id] = array();
            while ($row = $this->result[$id]->fetchArray(SQLITE3_ASSOC)) {
                $this->_fixObject[$id][] = (object) $row;
            }
            $this->result[$id]->reset();
        }
        return array_shift($this->_fixObject[$id]);
    }

    // 检测是否存在某数据表
    public function IsTable($tbname)
    {
        global $dsqlite;
        if (!$dsqlite->isInit) {
            $this->Init($this->pconnect);
        }
        $prefix = "#@__";
        $tbname = str_replace($prefix, $GLOBALS['cfg_dbprefix'], $tbname);

        $row = $this->linkID->querySingle("PRAGMA table_info({$tbname});");

        if ($row !== null) {
            return true;
        }
        return false;
    }

    //获得MySql的版本号
    public function GetVersion($isformat = true)
    {
        global $dsqlite;
        if (!$dsqlite->isInit) {
            $this->Init($this->pconnect);
        }
        if ($dsqlite->isClose) {
            $this->Open(false);
            $dsqlite->isClose = false;
        }
        $rs = $this->linkID->querySingle("select sqlite_version();");
        $sqlite_version = $rs;
        if ($isformat) {
            $sqlite_versions = explode(".", trim($sqlite_version));
            $sqlite_version = number_format($sqlite_versions[0] . "." . $sqlite_versions[1], 2);
        }
        return $sqlite_version;
    }

    //获取特定表的信息
    public function GetTableFields($tbname, $id = "me")
    {
        global $dsqlite;
        if (!$dsqlite->isInit) {
            $this->Init($this->pconnect);
        }
        $prefix = "#@__";
        $tbname = str_replace($prefix, $GLOBALS['cfg_dbprefix'], $tbname);
        $query = "SELECT * FROM {$tbname} LIMIT 0,1";
        $this->result[$id] = $this->linkID->query($query);
    }

    //获取字段详细信息
    public function GetFieldObject($id = "me")
    {
        $cols = $this->result[$id]->numColumns();
        $fields = array();
        while ($row = $this->result[$id]->fetchArray()) {
            for ($i = 1; $i < $cols; $i++) {
                $fields[] = $this->result[$id]->columnName($i);
            }
        }

        return (object) $fields;
    }

    //获得查询的总记录数
    public function GetTotalRow($id = "me")
    {
        $queryString = preg_replace("/SELECT(.*)FROM/isU", 'SELECT count(*) as dd FROM', $this->queryString);
        $rs = $this->linkID->query($queryString);
        $row = $rs->fetchArray();
        return $row['dd'];
    }

    //获取上一步INSERT操作产生的ID
    public function GetLastID()
    {
        //如果 AUTO_INCREMENT 的列的类型是 BIGINT，则 mysqli_insert_id() 返回的值将不正确。
        //可以在 SQL 查询中用 MySQL 内部的 SQL 函数 LAST_INSERT_ID() 来替代。
        //$rs = mysqli_query($this->linkID, "Select LAST_INSERT_ID() as lid");
        //$row = mysqli_fetch_array($rs);
        //return $row["lid"];
        return $this->linkID->lastInsertRowID();
    }

    //释放记录集占用的资源
    public function FreeResult($id = "me")
    {
        if ($this->result[$id]) {
            @$this->result[$id]->reset();
        }

    }
    public function FreeResultAll()
    {
        if (!is_array($this->result)) {
            return '';
        }
        foreach ($this->result as $kk => $vv) {
            if ($vv) {
                @$vv->reset();
            }
        }
    }

    //设置SQL语句，会自动把SQL语句里的#@__替换为$this->dbPrefix(在配置文件中为$cfg_dbprefix)
    public function SetQuery($sql)
    {
        $prefix = "#@__";
        $sql = str_replace($prefix, $GLOBALS['cfg_dbprefix'], $sql);
        $this->queryString = $sql;
        //$this->queryString = preg_replace("/CONCAT\(',', arc.typeid2, ','\)/i","printf(',%s,', arc.typeid2)",$this->queryString);
        if (preg_match("/CONCAT\(([^\)]*?)\)/i", $this->queryString, $matches)) {
            $this->queryString = preg_replace("/CONCAT\(([^\)]*?)\)/i", str_replace(",", "||", $matches[1]), $this->queryString);
            $this->queryString = str_replace("'||'", "','", $this->queryString);
        }

        $this->queryString = preg_replace("/FIND_IN_SET\('([\w]+)', arc.flag\)>0/i", "(',' || arc.flag || ',') LIKE '%,\\1,%'", $this->queryString);
        $this->queryString = preg_replace("/FIND_IN_SET\('([\w]+)', arc.flag\)<1/i", "(',' || arc.flag || ',') NOT LIKE '%,\\1,%'", $this->queryString);
        if (preg_match("/CREATE TABLE/i", $this->queryString)) {
            $this->queryString = preg_replace("/[\r\n]/", '', $this->queryString);
            $this->queryString = preg_replace('/character set (.*?) /i', '', $this->queryString);
            $this->queryString = preg_replace('/unsigned/i', '', $this->queryString);
            $this->queryString = str_replace('TYPE=MyISAM', '', $this->queryString);

            $this->queryString = preg_replace('/TINYINT\(([\d]+)\)/i', 'INTEGER', $this->queryString);
            $this->queryString = preg_replace('/mediumint\(([\d]+)\)/i', 'INTEGER', $this->queryString);
            $this->queryString = preg_replace('/smallint\(([\d]+)\)/i', 'INTEGER', $this->queryString);
            $this->queryString = preg_replace('/int\(([\d]+)\)/i', 'INTEGER', $this->queryString);
            $this->queryString = preg_replace('/auto_increment/i', 'PRIMARY KEY AUTOINCREMENT', $this->queryString);
            $this->queryString = preg_replace('/, KEY(.*?)MyISAM;/i', '', $this->queryString);
            $this->queryString = preg_replace('/, KEY(.*?);/i', ');', $this->queryString);
            $this->queryString = preg_replace('/, UNIQUE KEY(.*?);/i', ');', $this->queryString);
            $this->queryString = preg_replace('/set\(([^\)]*?)\)/', 'varchar', $this->queryString);
            $this->queryString = preg_replace('/enum\(([^\)]*?)\)/', 'varchar', $this->queryString);
            if (preg_match("/PRIMARY KEY AUTOINCREMENT/", $this->queryString)) {
                $this->queryString = preg_replace('/,([\t\s ]+)PRIMARY KEY  \(`([0-9a-zA-Z]+)`\)/i', '', $this->queryString);
                $this->queryString = str_replace(',	PRIMARY KEY (`id`)', '', $this->queryString);
            }
        }
        $this->queryString = preg_replace("/SHOW fields FROM `([\w]+)`/i", "PRAGMA table_info('\\1') ", $this->queryString);
        $this->queryString = preg_replace("/SHOW CREATE TABLE .([\w]+)/i", "SELECT 0,sql FROM sqlite_master WHERE name='\\1'; ", $this->queryString);
        //var_dump($this->queryString);
        $this->queryString = preg_replace("/Show Tables/i", "SELECT name FROM sqlite_master WHERE type = \"table\"", $this->queryString);
        $this->queryString = str_replace("\'", "\"", $this->queryString);
        //var_dump($this->queryString);
    }

    public function SetSql($sql)
    {
        $this->SetQuery($sql);
    }

    public function RecordLog($runtime = 0)
    {
        $RecordLogFile = dirname(__FILE__) . '/../data/mysqli_record_log.inc';
        $url = $this->GetCurUrl();
        $savemsg = <<<EOT

------------------------------------------
SQL:{$this->queryString}
Page:$url
Runtime:$runtime
EOT;
        $fp = @fopen($RecordLogFile, 'a');
        @fwrite($fp, $savemsg);
        @fclose($fp);
    }

    //显示数据链接错误信息
    public function DisplayError($msg)
    {
        $errorTrackFile = dirname(__FILE__) . '/../data/mysqli_error_trace.inc';
        if (file_exists(dirname(__FILE__) . '/../data/mysqli_error_trace.php')) {
            @unlink(dirname(__FILE__) . '/../data/mysqli_error_trace.php');
        }
        if ($this->showError) {
            $emsg = '';
            $emsg .= "<div><h3>DedeCMS Error Warning!</h3>\r\n";
            $emsg .= "<div><a href='http://bbs.dedecms.com' target='_blank' style='color:red'>Technical Support: http://bbs.dedecms.com</a></div>";
            $emsg .= "<div style='line-helght:160%;font-size:14px;color:green'>\r\n";
            $emsg .= "<div style='color:blue'><br />Error page: <font color='red'>" . $this->GetCurUrl() . "</font></div>\r\n";
            $emsg .= "<div>Error infos: {$msg}</div>\r\n";
            $emsg .= "<br /></div></div>\r\n";

            echo $emsg;
        }

        $savemsg = 'Page: ' . $this->GetCurUrl() . "\r\nError: " . $msg . "\r\nTime" . date('Y-m-d H:i:s');
        //保存MySql错误日志
        $fp = @fopen($errorTrackFile, 'a');
        @fwrite($fp, '<' . '?php  exit();' . "\r\n/*\r\n{$savemsg}\r\n*/\r\n?" . ">\r\n");
        @fclose($fp);
    }

    //获得当前的脚本网址
    public function GetCurUrl()
    {
        if (!empty($_SERVER["REQUEST_URI"])) {
            $scriptName = $_SERVER["REQUEST_URI"];
            $nowurl = $scriptName;
        } else {
            $scriptName = $_SERVER["PHP_SELF"];
            if (empty($_SERVER["QUERY_STRING"])) {
                $nowurl = $scriptName;
            } else {
                $nowurl = $scriptName . "?" . $_SERVER["QUERY_STRING"];
            }
        }
        return $nowurl;
    }

}

//复制一个对象副本
function CopySQLiPoint(&$ndsql)
{
    $GLOBALS['dsqlite'] = $ndsql;
}

//SQL语句过滤程序，由80sec提供，这里作了适当的修改
if (!function_exists('CheckSql')) {
    function CheckSql($db_string, $querytype = 'select')
    {
        global $cfg_cookie_encode;
        $clean = '';
        $error = '';
        $old_pos = 0;
        $pos = -1;
        $log_file = DEDEINC . '/../data/' . md5($cfg_cookie_encode) . '_safe.txt';
        $userIP = GetIP();
        $getUrl = GetCurUrl();

        //如果是普通查询语句，直接过滤一些特殊语法
        if ($querytype == 'select') {
            $notallow1 = "[^0-9a-z@\._-]{1,}(union|sleep|benchmark|load_file|outfile)[^0-9a-z@\.-]{1,}";

            //$notallow2 = "--|/\*";
            if (preg_match("/" . $notallow1 . "/i", $db_string)) {
                fputs(fopen($log_file, 'a+'), "$userIP||$getUrl||$db_string||SelectBreak\r\n");
                exit("<font size='5' color='red'>Safe Alert: Request Error step 1 !</font>");
            }
        }

        //完整的SQL检查
        while (true) {
            $pos = strpos($db_string, '\'', $pos + 1);
            if ($pos === false) {
                break;
            }
            $clean .= substr($db_string, $old_pos, $pos - $old_pos);
            while (true) {
                $pos1 = strpos($db_string, '\'', $pos + 1);
                $pos2 = strpos($db_string, '\\', $pos + 1);
                if ($pos1 === false) {
                    break;
                } elseif ($pos2 == false || $pos2 > $pos1) {
                    $pos = $pos1;
                    break;
                }
                $pos = $pos2 + 1;
            }
            $clean .= '$s$';
            $old_pos = $pos + 1;
        }
        $clean .= substr($db_string, $old_pos);
        $clean = trim(strtolower(preg_replace(array('~\s+~s'), array(' '), $clean)));

        if (strpos($clean, '@') !== false or strpos($clean, 'char(') !== false or strpos($clean, '"') !== false
            or strpos($clean, '$s$$s$') !== false
        ) {
            $fail = true;
            if (preg_match("#^create table#i", $clean)) {
                $fail = false;
            }

            $error = "unusual character";
        }

        //老版本的Mysql并不支持union，常用的程序里也不使用union，但是一些黑客使用它，所以检查它
        if (strpos($clean, 'union') !== false && preg_match('~(^|[^a-z])union($|[^[a-z])~s', $clean) != 0) {
            $fail = true;
            $error = "union detect";
        }

        //发布版本的程序可能比较少包括--,#这样的注释，但是黑客经常使用它们
        elseif (strpos($clean, '/*') > 2 || strpos($clean, '--') !== false || strpos($clean, '#') !== false) {
            $fail = true;
            $error = "comment detect";
        }

        //这些函数不会被使用，但是黑客会用它来操作文件，down掉数据库
        elseif (strpos($clean, 'sleep') !== false && preg_match('~(^|[^a-z])sleep($|[^[a-z])~s', $clean) != 0) {
            $fail = true;
            $error = "slown down detect";
        } elseif (strpos($clean, 'benchmark') !== false && preg_match('~(^|[^a-z])benchmark($|[^[a-z])~s', $clean) != 0) {
            $fail = true;
            $error = "slown down detect";
        } elseif (strpos($clean, 'load_file') !== false && preg_match('~(^|[^a-z])load_file($|[^[a-z])~s', $clean) != 0) {
            $fail = true;
            $error = "file fun detect";
        } elseif (strpos($clean, 'into outfile') !== false && preg_match('~(^|[^a-z])into\s+outfile($|[^[a-z])~s', $clean) != 0) {
            $fail = true;
            $error = "file fun detect";
        }

        //老版本的MYSQL不支持子查询，我们的程序里可能也用得少，但是黑客可以使用它来查询数据库敏感信息
        elseif (preg_match('~\([^)]*?select~s', $clean) != 0) {
            $fail = true;
            $error = "sub select detect";
        }
        if (!empty($fail)) {
            fputs(fopen($log_file, 'a+'), "$userIP||$getUrl||$db_string||$error\r\n");
            exit("<font size='5' color='red'>Safe Alert: Request Error step 2!</font>");
        } else {
            return $db_string;
        }
    }
}
