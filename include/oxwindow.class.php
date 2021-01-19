<?php if (!defined('DEDEINC')) {
    exit("DedeCMS Error: Request Error!");
}

/**
 * 提示窗口对话框类
 *
 * @version   $Id: oxwindow.class.php 2 13:53 2010-11-11  $
 * @package   DedeCMS.Libraries
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once DEDEINC . "/dedetag.class.php";

/**
 * 提示窗口对话框类
 *
 * @package    OxWindow
 * @subpackage DedeCMS.Libraries
 * @link       http://www.dedecms.com
 */
class OxWindow
{
    public $myWin = "";
    public $myWinItem = "";
    public $checkCode = "";
    public $formName = "";
    public $tmpCode = "//checkcode";
    public $hasStart = false;

    /**
     *  初始化为含表单的页面
     *
     * @param  string $formaction  表单操作action
     * @param  string $checkScript 检测验证js
     * @param  string $formmethod  表单类型
     * @param  string $formname    表单名称
     * @return void
     */
    public function Init($formaction = "", $checkScript = "js/blank.js", $formmethod = "POST", $formname = "myform")
    {
        $this->myWin .= "<script language='javascript'>\r\n";
        if ($checkScript != "" && file_exists($checkScript)) {
            $fp = fopen($checkScript, "r");
            $this->myWin .= fread($fp, filesize($checkScript));
            fclose($fp);
        } else {
            $this->myWin .= "function CheckSubmit()\r\n{ return true; }";
        }
        $this->myWin .= "</script>\r\n";
        $this->formName = $formname;
        $this->myWin .= "<form name='$formname' method='$formmethod' onSubmit='return CheckSubmit();' action='$formaction' >\r\n";
    }

    //
    /**
     *  增加隐藏域
     *
     * @param  string $iname  隐藏域名称
     * @param  string $ivalue 隐藏域值
     * @return void
     */
    public function AddHidden($iname, $ivalue)
    {
        $this->myWin .= "<input type='hidden' name='$iname' value='$ivalue'>\r\n";
    }

    /**
     *  开始创建窗口
     *
     * @return void
     */
    public function StartWin()
    {
        $this->myWin .= "<table class=\"uk-table uk-table-middle uk-table-divider uk-table-striped uk-margin-remove\">\r\n";
    }

    /**
     *  增加一个两列的行
     *
     * @access public
     * @param  string $iname  名称
     * @param  string $ivalue 值
     * @return string
     */
    public function AddItem($iname, $ivalue)
    {
        $this->myWinItem .= "
        <div class=\"uk-margin\">
        <label class=\"uk-form-label\">$iname</label>
        <div class=\"uk-form-controls\">
        <div class=\"uk-margin uk-grid-small uk-child-width-auto uk-grid\">
        <label>$ivalue</label>
        </div>
        </div>
        </div>
        ";
    }

    /**
     *  增加一个单列的消息行
     *
     * @access public
     * @param  string $ivalue 短消息值
     * @param  string $height 消息框高度
     * @param  string $col    显示列数
     * @return void
     */
    public function AddMsgItem($ivalue, $height = "100", $col = "2")
    {
        $this->myWinItem .= "$ivalue";
    }

    /**
     *  增加单列的标题行
     *
     * @access public
     * @param  string $title 标题
     * @param  string $col   列
     * @return string
     */
    public function AddTitle($title, $col = "2")
    {
        global $cfg_assets_dir;
        if ($col != "" && $col != "0") {
            $colspan = "colspan='$col'";
        } else {
            $colspan = "";
        }
        $this->myWinItem .= "<tr>\r\n";
        $this->myWinItem .= "<td $colspan><b>$title</b></td>\r\n";
        $this->myWinItem .= "</tr></table>\r\n";
        $this->myWinItem .= "<fieldset class=\"uk-fieldset\">\r\n";
        $this->myWinItem .= "<div id=\"oswindos-container\" class='uk-padding-small'>\r\n";
    }

    /**
     *  结束Window
     *
     * @param  bool $isform
     * @return void
     */
    public function CloseWin($isform = true)
    {
        if (!$isform) {
            $this->myWin .= "</div></fieldset>\r\n";
        } else {
            $this->myWin .= "</fieldset></form>\r\n";
        }
    }

    /**
     *  增加自定义JS脚本
     *
     * @param  string $scripts
     * @return void
     */
    public function SetCheckScript($scripts)
    {
        $pos = strpos($this->myWin, $this->tmpCode);
        if ($pos > 0) {
            $this->myWin = substr_replace($this->myWin, $scripts, $pos, strlen($this->tmpCode));
        }
    }

    /**
     *  获取窗口
     *
     * @param  string $wintype 菜单类型
     * @param  string $msg     短消息
     * @param  bool   $isform  是否是表单
     * @return string
     */
    public function GetWindow($wintype = "save", $msg = "", $isform = true)
    {
        global $cfg_assets_dir;
        $this->StartWin();
        $this->myWin .= $this->myWinItem;
        if ($wintype != "") {
            if ($wintype != "hand") {
                $this->myWin .= "
</div>
<div id='dede-oswindow-toolbar' class=\"uk-card-footer\">
<div class=\"uk-grid uk-flex uk-flex-middle\">
<div class=\"uk-button-group\">
<a href=\"#\"  onClick='history.go(-1);' class=\"uk-button uk-button-default  uk-button-small uk-flex-inline\">
    <span class=\"dede-toolbar-icon\" uk-icon=\"icon:arrow-left-short;ratio:0.8\"></span> 返回
</a>
<button type=\"reset\" class=\"uk-button uk-button-default  uk-button-small uk-flex-inline\">
    <span class=\"dede-toolbar-icon\" uk-icon=\"icon: arrow-counterclockwise;ratio:0.8\"></span> 重置
</button>
<button type='submit' class=\"uk-button uk-button-default  uk-button-small uk-flex-inline\">
    <span class=\"dede-toolbar-icon\" uk-icon=\"icon: record2;ratio:0.8\"></span> 提交
</button>
</div>
</div>
</div>               
";
            } else {
                if ($msg != '') {
                    $this->myWin .= "<tr><td bgcolor='#F5F5F5'>$msg</td></tr>";
                } else {
                    $this->myWin .= '';
                }
            }
        }
        $this->CloseWin($isform);
        return $this->myWin;
    }
 
    /**
     *  显示页面
     *
     * @access public
     * @param  string $modfile 模型模板
     * @return string
     */
    public function Display($modfile = "")
    {
        global $cfg_templets_dir, $wecome_info, $cfg_basedir;
        if (empty($wecome_info)) {
            $wecome_info = "DedeCMS OX 通用对话框：";
        }
        $ctp = new DedeTagParse();
        if ($modfile == '') {
            $ctp->LoadTemplate($cfg_basedir . $cfg_templets_dir . '/plus/win_templet.htm');
        } else {
            $ctp->LoadTemplate($modfile);
        }
        $emnum = $ctp->Count;
        for ($i = 0; $i <= $emnum; $i++) {
            if (isset($GLOBALS[$ctp->CTags[$i]->GetTagName()])) {
                $ctp->Assign($i, $GLOBALS[$ctp->CTags[$i]->GetTagName()]);
            }
        }
        $ctp->Display();
        $ctp->Clear();
    }
} //End Class

/**
 *  显示一个不带表单的普通提示
 *
 * @access public
 * @param  string $msg   消息提示信息
 * @param  string $title 提示标题
 * @return string
 */
function ShowMsgWin($msg, $title)
{
    $win = new OxWindow();
    $win->Init();
    $win->mainTitle = "DeDeCMS系统提示：";
    $win->AddTitle($title);
    $win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
    $winform = $win->GetWindow("hand");
    $win->Display();
}
