<?php if (!defined('DEDEINC')) {
    exit("Request Error!");
}

/**
 * 织梦控制器基类
 *
 * @version   $Id: control.class.php 1 10:33 2010年7月6日 $
 * @package   DedeCMS.Libraries
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once DEDEINC . "/dedetemplate.class.php";

class Control
{
    public $tpl;
    public $dsql;
    public $style = 'default';
    public $_helpers = array();

    public $apptpl = '../templates/';

    public function __construct()
    {
        $this->Control();
    }

    // 析构函数
    public function Control()
    {
        global $dsql;
        $this->tpl = isset($this->tpl) ? $this->tpl : new DedeTemplate();
        $this->dsql = isset($dsql) ? $dsql : new DedeSqli(false);
    }

    //设置模板
    //如果想要使用模板中指定的pagesize，必须在调用模板后才调用 SetSource($sql)
    public function SetTemplate($tplfile)
    {
        $tplfile = DEDEAPPTPL . '/' . $this->style . '/' . $tplfile;
        $this->tpl->LoadTemplate($tplfile);
    }
    public function SetTemplet($tplfile)
    {
        $tplfile = DEDEAPPTPL . '/' . $this->style . '/' . $tplfile;
        $this->tpl->LoadTemplate($tplfile);
    }

    //设置/获取文档相关的各种变量
    public function SetVar($k, $v)
    {
        $this->tpl->Assign($k, $v);
    }

    public function GetVar($k)
    {
        global $_vars;
        return isset($_vars[$k]) ? $_vars[$k] : '';
    }

    public function Model($name = '')
    {
        $name = preg_replace("#[^\w]#", "", $name);
        $modelfile = DEDEMODEL . '/' . $name . '.php';
        if (file_exists($modelfile)) {
            include_once $modelfile;
        }
        if (!empty($name) && class_exists($name)) {
            return new $name;
        }
        return false;
    }

    public function Libraries($name = '', $data = '')
    {
        if (defined('APPNAME')) {
            $classfile = 'MY_' . $name . '.class.php';
            if (file_exists('../' . APPNAME . '/libraries/' . $classfile)) {
                include '../' . APPNAME . '/libraries/' . $classfile;
                return new $name($data);
            } else {
                if (!empty($name) && class_exists($name)) {
                    return new $name($data);
                }
            }
            return false;
        } else {
            if (!empty($name) && class_exists($name)) {
                return new $name($data);
            }
            return false;
        }
    }

    //载入helper
    public function helper($helper, $path)
    {
        $help_path = $path . '/data/helper/' . $helper . ".helper.php";
        if (file_exists($help_path)) {
            include_once $help_path;
        } else {
            exit('Unable to load the requested file: ' . $helper . ".helper.php");
        }
    }

    //显示数据
    public function Display()
    {
        $this->tpl->SetObject($this);
        $this->tpl->Display();
    }

    //保存为HTML
    public function SaveTo($filename)
    {
        $this->tpl->SetObject($this);
        $this->tpl->SaveTo($filename);
    }

    // 释放资源
    public function __destruct()
    {
        unset($this->tpl);
        $this->dsql->Close(true);
    }
}
