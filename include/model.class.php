<?php if (!defined('DEDEINC')) {
    exit("Request Error!");
}

/**
 * 模型基类
 *
 * @version        $Id: model.class.php 1 13:46 2010-12-1  $
 * @package        DedeCMS.Libraries
 * @founder        IT柏拉图, https: //weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */

class Model
{
    public $dsql;
    public $db;

    public function __construct()
    {
        $this->Model();
    }

    // 析构函数
    public function Model()
    {
        global $dsql;
        if ($GLOBALS['cfg_mysql_type'] == 'mysqli') {
            $this->dsql = $this->db = isset($dsql) ? $dsql : new DedeSqli(false);
        } else {
            $this->dsql = $this->db = isset($dsql) ? $dsql : new DedeSql(false);
        }

    }

    // 释放资源
    public function __destruct()
    {
        $this->dsql->Close(true);
    }
}
