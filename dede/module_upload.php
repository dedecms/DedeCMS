<?php
/**
 * 模块上传
 *
 * @version   $Id: module_upload.php 1 14:43 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
CheckPurview('sys_module');
require_once dirname(__FILE__) . "/../include/dedemodule.class.php";
require_once dirname(__FILE__) . "/../include/oxwindow.class.php";
if (empty($action)) {
    $action = '';
}

$mdir = DEDEDATA . '/module';

if ($action == 'upload') {
    if (!is_uploaded_file($upfile)) {
        ShowMsg("貌似你什么都没有上传哦！", "javascript:;");
        exit();
    } else {
        include_once dirname(__FILE__) . "/../include/zip.class.php";
        $tmpfilename = $mdir . '/' . ExecTime() . mt_rand(10000, 50000) . '.tmp';
        move_uploaded_file($upfile, $tmpfilename) or die("把上传的文件移动到{$tmpfilename}时失败，请检查{$mdir}目录是否有写入权限！");

        //ZIP格式的文件
        if ($filetype == 1) {
            $z = new zip();
            $files = $z->get_List($tmpfilename);
            $dedefileindex = -1;
            //为了节省资源，系统仅以.xml作为扩展名识别ZIP包里了dede模块格式文件
            if (is_array($files)) {
                for ($i = 0; $i < count($files); $i++) {
                    if (preg_match("#\.xml#i", $files[$i]['filename'])) {
                        $dedefile = $files[$i]['filename'];
                        $dedefileindex = $i;
                        break;
                    }
                }
            }
            if ($dedefileindex == -1) {
                unlink($tmpfilename);
                ShowMsg("对不起，你上传的压缩包中不存在dede模块文件！<br /><br /><a href='javascript:history.go(-1);'>&gt;&gt;返回重新上传&gt;&gt;</a>", "javascript:;");
                exit();
            }
            $ziptmp = $mdir . '/ziptmp';
            $z->Extract($tmpfilename, $ziptmp, $dedefileindex);
            unlink($tmpfilename);
            $tmpfilename = $mdir . "/ziptmp/" . $dedefile;
        }

        $dm = new DedeModule($mdir);
        $infos = $dm->GetModuleInfo($tmpfilename, 'file');
        if (empty($infos['hash'])) {
            unlink($tmpfilename);
            $dm->Clear();
            ShowMsg("对不起，你上传的文件可能不是织梦模块的标准格式文件！<br /><br /><a href='javascript:history.go(-1);'>&gt;&gt;返回重新上传&gt;&gt;</a>", "javascript:;");
            exit();
        }
        $okfile = $mdir . '/' . $infos['hash'] . '.xml';
        if ($dm->HasModule($infos['hash']) && empty($delhas)) {
            unlink($tmpfilename);
            $dm->Clear();
            ShowMsg("对不起，你上传的模块已经存在，<br />如果要覆盖请先删除原来版本或选择强制删除的选项！<br /><br /><a href='javascript:history.go(-1);'>&gt;&gt;返回重新上传&gt;&gt;</a>", "javascript:;");
            exit();
        }
        @unlink($okfile);
        copy($tmpfilename, $okfile);
        @unlink($tmpfilename);
        $dm->Clear();
        ShowMsg("成功上传一个新的模块！", "module_main.php?action=view&hash={$infos['hash']}");
        exit();
    }
} else {
    $win = new OxWindow();
    $win->Init("module_upload.php", "js/blank.js", "POST' enctype='multipart/form-data");
    $win->mainTitle = "模块管理";
    $wecome_info = "<ul class=\"uk-breadcrumb\"><li><a href=\"module_main.php\">模块管理</a></li><li><span>上传模块</span></li></ul>";
    $win->AddTitle('请选择要上传的文件:');
    $win->AddHidden("action", 'upload');
    $msg = "
    <div class=\"uk-margin\">
    <label class=\"uk-form-label\">文件格式：</label>
    <div class=\"uk-form-controls\">
    <div class=\"uk-margin uk-grid-small uk-child-width-auto uk-grid\">
    <label><input class=\"uk-radio\" type=\"radio\" name=\"filetype\" value='0' checked> 正常的模块包</label>
    <label><input class=\"uk-radio\" type=\"radio\" name=\"filetype\" value='1'> 经过 zip 压缩的模块包</label>
    </div>
    </div>
    </div>
    <div class=\"uk-margin\">
    <label class=\"uk-form-label\">已有模块：</label>
    <div class=\"uk-form-controls\">
    <div class=\"uk-margin uk-grid-small uk-child-width-auto uk-grid\">
    <label> <input name='delhas' type='checkbox' id='delhas' value='1'  class='uk-checkbox'/>  强制删除同名模块(这可能导致已经安装的模块无法卸载)</label>
    </div>
    </div>
    </div>
    <div class=\"uk-margin\">
    <label class=\"uk-form-label\">请选择文件：</label>
    <div class=\"uk-form-controls\">
    <div class=\"uk-margin uk-grid-small uk-child-width-auto uk-grid\">
    <div>
    <div  class='uk-inline'  uk-form-custom=\"target: true\">
    <span class='uk-form-icon uk-icon' uk-icon='icon: upload'></span>
    <input name='upfile' type='file' id='upfile' />
    <input class='uk-input uk-form-small uk-form-width-large' type='text' placeholder='点击选择文件'>
    </div>
    </div>
    </div>
    </div>
    </div>
    ";
    $win->AddMsgItem("$msg");
    $winform = $win->GetWindow('ok');
    $win->Display();
    exit();
}
//ClearAllLink();
