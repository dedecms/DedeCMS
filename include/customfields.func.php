<?php if (!defined('DEDEMEMBER')) {exit('Request Error');
}
/**
 * 系统核心函数存放文件
 *
 * @version   $Id: customfields.func.php 2 20:50 2010年7月7日 $
 * @package   DedeCMS.Libraries
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */

/**
 *  获得一个附加表单(发布时用)
 *
 * @access public
 * @param  object $ctag      标签
 * @param  string $admintype 管理员类型
 * @return string
 */
function GetFormItem($ctag, $admintype = 'admin')
{
    global $dsql;
    $fieldname = $ctag->GetName();
    $fieldType = $ctag->GetAtt('type');
    $formitem = $formitem = GetSysTemplets("custom_fields_{$admintype}.htm");
    $innertext = trim($ctag->GetInnerText());
    if ($innertext != '') {
        $formitem = $innertext;
    
    }

    if ($fieldType == 'select') {
        $myformItem = '';
        $items = explode(',', $ctag->GetAtt("default"));
        $myformItem = "<select name='$fieldname' class='uk-select uk-form-width-large uk-form-small'>";
        foreach ($items as $v) {
            $v = trim($v);
            if ($v != '') {
                $myformItem .= "<option value='$v'>$v</option>\r\n";
            
            }
        
        }
        $myformItem .= "</select>\r\n";
        $innertext = $myformItem;
    
    } else if ($fieldType == 'stepselect') {
        global $hasSetEnumJs, $cfg_cmspath;
        $cmspath = ((empty($cfg_cmspath) || !preg_match('/[/$]/', $cfg_cmspath)) ? $cfg_cmspath . '/' : $cfg_cmspath);
        $myformItem = '';
        $myformItem .= "<input type='hidden' id='hidden_{$fieldname}' name='{$fieldname}' value='0' />\r\n";
        $myformItem .= "<span id='span_{$fieldname}'></span>\r\n";
        $myformItem .= "<span id='span_{$fieldname}_son'></span>\r\n";
        $myformItem .= "<span id='span_{$fieldname}_sec'></span>\r\n";
        if ($hasSetEnumJs != 'hasset') {
            $myformItem .= '<script language="javascript" type="text/javascript" src="' . $cmspath . 'assets/pkg/dede/enums.js"></script>' . "\r\n";
            $GLOBALS['hasSetEnumJs'] = 'hasset';
        }
        $myformItem .= "<script language='javascript' type='text/javascript' src='{$cmspath}data/enums/{$fieldname}.js'></script>\r\n";
        $myformItem .= '<script language="javascript" type="text/javascript">MakeTopSelect("' . $fieldname . '", 0);</script>' . "\r\n";
        $formitem = str_replace('~name~', $ctag->GetAtt('itemname'), $formitem);
        $formitem = str_replace('~form~', $myformItem, $formitem);
        return $formitem;
    
    } else if ($fieldType == 'radio') {
        $myformItem = '';
        $items = explode(',', $ctag->GetAtt("default"));
        $i = 0;
        foreach ($items as $v) {
            $v = trim($v);
            if ($v != '') {
                $myformItem .= ($i == 0 ? "<input type='radio' name='$fieldname' class='np' value='$v' checked>$v\r\n" : "<input type='radio' name='$fieldname' class='uk-radio' value='$v'> $v &nbsp&nbsp\r\n");
                $i++;
            
            }
        }
        $innertext = $myformItem;
    
    } else if ($fieldType == 'checkbox') {
        $myformItem = '';
        $items = explode(',', $ctag->GetAtt("default"));
        foreach ($items as $v) {
            $v = trim($v);
            if ($v != '') {
                if ($admintype == 'membermodel') {
                    $myformItem .= "<label><input type='checkbox' name='{$fieldname}[]' class='uk-checkbox' value='$v'> $v</label> &nbsp&nbsp\r\n";
                
                } else {
                    $myformItem .= "<input type='checkbox' name='{$fieldname}[]' class='uk-checkbox' value='$v'> $v &nbsp&nbsp\r\n";
                
                }
            }
        }
        $innertext = $myformItem;
    
    } else if ($fieldType == 'htmltext' || $fieldType == 'textdata') {
        $dfvalue = ($ctag->GetAtt('default') != '' ? $ctag->GetAtt('default') : '');
        $dfvalue = str_replace('{{', '<', $dfvalue);
        $dfvalue = str_replace('}}', '>', $dfvalue);
        if ($admintype == 'admin') {
            $innertext = GetEditor($fieldname, $dfvalue, 350, 'Basic', 'string');
        
        } else if ($admintype == 'diy') {
            $innertext = GetEditor($fieldname, $dfvalue, 350, 'Diy', 'string');
        
        } else {
            $innertext = GetEditor($fieldname, $dfvalue, 350, 'Member', 'string');
        
        }
    
    } else if ($fieldType == "multitext") {
        $innertext = "<textarea name='$fieldname' id='$fieldname' style='height:80' class='uk-textarea uk-form-width-large uk-form-small'></textarea>\r\n";
    
    } else if ($fieldType == "datetime") {
        $nowtime = GetDateTimeMk(time());
        $innertext = "<div class='uk-inline'>";
        $innertext .= "<span class='uk-form-icon uk-icon' uk-icon='icon: calendar4-week'></span>";
        $innertext .= "<input name='$fieldname' value='$nowtime' placeholder='$nowtime' class='uk-input uk-form-width-large uk-form-small' type='datetime-local' >";
        $innertext .= "</div>";

    } else if ($fieldType == 'img') {
        if ($admintype == 'diy') {
            $innertext = "<input type='file' name='$fieldname' id='$fieldname' class='uk-input uk-form-width-large uk-form-small' />\r\n";
        } else {
            $innertext = "
            <div  class='uk-inline'  uk-form-custom=\"target: true\">
            <span class='uk-form-icon uk-icon' uk-icon='icon: card-image'></span>
            <input name='$fieldname' type='file' id='$fieldname' />
            <input class='uk-input uk-form-small uk-form-width-large' type='text' placeholder='点击选择本地图片'>
            </div>\r\n";
        }
    } else if ($fieldType == 'imgfile') {
        if ($admintype == 'diy') {
            $innertext = "<input type='text' name='$fieldname' id='$fieldname' class='uk-input uk-form-width-large uk-form-small' />\r\n";
        } else {
            $innertext = "
            <div  class='uk-inline'  uk-form-custom=\"target: true\">
            <span class='uk-form-icon uk-icon' uk-icon='icon: globe2'></span>
            <input type='text' name='$fieldname' id='$fieldname'   class='uk-input uk-form-width-large uk-form-small'/> &nbsp&nbsp（图片网址）
            </div>\r\n";
        }
    
    }  else if ($fieldType == 'media') {
        if ($admintype == 'diy') {
            $innertext = "<input type='hidden' name='$fieldname' id='$fieldname' class='uk-input uk-form-width-large uk-form-small'/> 不支持的类型\r\n";
        } else {
            $innertext = "
            <div  class='uk-inline'  uk-form-custom=\"target: true\">
            <span class='uk-form-icon uk-icon' uk-icon='icon: play-btn'></span>
            <input name='$fieldname' type='file' id='$fieldname' />
            <input class='uk-input uk-form-small uk-form-width-large' type='text' placeholder='点击选择多媒体文件'>
            </div>\r\n";
        }
    
    } else if ($fieldType == 'addon') {
        if ($admintype == 'diy') {
            $innertext = "<input type='file' name='$fieldname' id='$fieldname' class='uk-input uk-form-width-large uk-form-small' />\r\n";
        
        } else {
            $innertext = "
            <div  class='uk-inline'  uk-form-custom=\"target: true\">
            <span class='uk-form-icon uk-icon' uk-icon='icon: archive'></span>
            <input name='$fieldname' type='file' id='$fieldname' />
            <input class='uk-input uk-form-small uk-form-width-large' type='text' placeholder='点击选择压缩文件'>
            </div>\r\n";
        }
    
    } else if ($fieldType == 'int' || $fieldType == 'float') {
        $dfvalue = ($ctag->GetAtt('default') != '' ? $ctag->GetAtt('default') : '0');
        $innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:100px'  class='uk-input uk-form-width-large uk-form-small' value='$dfvalue' /> &nbsp&nbsp(填写数值)\r\n";
    
    } else {
        $dfvalue = ($ctag->GetAtt('default') != '' ? $ctag->GetAtt('default') : '');
        $innertext = "<input type='text' name='$fieldname' id='$fieldname'   class='uk-input uk-form-width-large uk-form-small' value='$dfvalue' />\r\n";
    
    }
    $formitem = str_replace("~name~", $ctag->GetAtt('itemname'), $formitem);
    $formitem = str_replace("~form~", $innertext, $formitem);
    return $formitem;

}

/**
 *  处理不同类型的数据
 *
 * @access public
 * @param  string $dvalue    默认值
 * @param  string $dtype     默认类型
 * @param  int    $aid       文档ID
 * @param  string $job       操作类型
 * @param  string $addvar    值
 * @param  string $admintype 管理类型
 * @param  string $fieldname 变量类型
 * @return string
 */
function GetFieldValue($dvalue, $dtype, $aid = 0, $job = 'add', $addvar = '', $admintype = 'admin', $fieldname = '')
{
    global $cfg_basedir, $cfg_cmspath, $adminid, $cfg_ml, $cfg_cookie_encode;
    if (!empty($adminid)) {
        $adminid = $adminid;
    
    } else {
        $adminid = isset($cfg_ml) ? $cfg_ml->M_ID : 1;
    
    }
    if ($dtype == 'int') {
        if ($dvalue == '') {
            return 0;
        
        }
        return GetAlabNum($dvalue);
    
    } else if ($dtype == 'stepselect') {
        $dvalue = trim(preg_replace("#[^0-9\.]#", "", $dvalue));
        return $dvalue;
    
    } else if ($dtype == 'float') {
        if ($dvalue == '') {
            return 0;
        
        }
        return GetAlabNum($dvalue);
    
    } else if ($dtype == 'datetime') {
        if ($dvalue == '') {
            return 0;
        
        }
        return strtotime($dvalue);
    
    } else if ($dtype == 'checkbox') {
        $okvalue = '';
        if (is_array($dvalue)) {
            $okvalue = join(',', $dvalue);
        
        }
        return $okvalue;
    
    } else if ($dtype == "htmltext") {
        if ($admintype == 'member' || $admintype == 'diy') {
            $dvalue = HtmlReplace($dvalue, -1);
        
        }
        return $dvalue;
    
    } else if ($dtype == "multitext") {
        if ($admintype == 'member' || $admintype == 'diy') {
            $dvalue = HtmlReplace($dvalue, 0);
        
        }
        return $dvalue;
    
    } else if ($dtype == "textdata") {
        $ipath = $cfg_cmspath . "/data/textdata";
        $tpath = ceil($aid / 5000);
        if (!is_dir($cfg_basedir . $ipath)) {
            MkdirAll($cfg_basedir . $ipath, $GLOBALS['cfg_dir_purview']);
        
        }
        if (!is_dir($cfg_basedir . $ipath . '/' . $tpath)) {
            MkdirAll($cfg_basedir . $ipath . '/' . $tpath, $GLOBALS['cfg_dir_purview']);
        
        }
        $ipath = $ipath . '/' . $tpath;
        $filename = "{$ipath}/{$aid}-" . cn_substr(md5($cfg_cookie_encode), 0, 16) . ".txt";

        //用户投稿内容安全处理
        if ($admintype == 'member' || $admintype == 'diy') {
            $dvalue = HtmlReplace($dvalue, -1);
        
        }
        $fp = fopen($cfg_basedir . $filename, "w");
        fwrite($fp, stripslashes($dvalue));
        fclose($fp);
        CloseFtp();
        return $filename;
    
    } else if ($dtype == 'img' || $dtype == 'imgfile') {
       
        if (preg_match("#[\\|/]uploads[\\|/]userup#", $dvalue)) {
            return $dvalue;
        
        }

        if ($admintype == 'diy') {
            $iurl = MemberUploads($fieldname, '', 0, 'image', '', -1, -1, false);
            return $iurl;
        
        }
        $iurl = stripslashes($dvalue);
        if (trim($iurl) == '') {
            return '';
        
        }
        $iurl = trim(str_replace($GLOBALS['cfg_basehost'], "", $iurl));
        $imgurl = "{dede:img text='' width='' height=''} " . $iurl . " {/dede:img}";
        if (preg_match("/^http:\/\//i", $iurl) && $GLOBALS['cfg_isUrlOpen']) {
            
            //远程图片
            $reimgs = '';
            if ($GLOBALS['cfg_isUrlOpen'] && $GLOBALS['cfg_rm_remote'] == 'Y') {
                $reimgs = GetRemoteImage($iurl, $adminid);
                if (is_array($reimgs)) {
                    $imgurl = "{dede:img text='' width='" . $reimgs[1] . "' height='" . $reimgs[2] . "'} " . $reimgs[0] . " {/dede:img}";
                }
            } else {
                    $imgurl = "{dede:img text='' width='' height=''} " . $iurl . " {/dede:img}";
            }
        
        } else if ($iurl != '') {
            //站内图片
            $imgfile = $cfg_basedir . $iurl;
            if (is_file($imgfile)) {
                $info = array();
                $imginfos = GetImageSize($imgfile, $info);

                    $imgurl = "{dede:img text='' width='" . $imginfos[0] . "' height='" . $imginfos[1] . "'} $iurl {/dede:img}";
                
            
            }
        
        }
        return addslashes($imgurl);
    
    } else if ($dtype == 'addon' && $admintype == 'diy') {
        if (preg_match("#[\\|/]uploads[\\|/]userup#", $dvalue)) {
            return $dvalue;
        
        }

        $dvalue = MemberUploads($fieldname, '', 0, 'addon', '', -1, -1, false);
        return $dvalue;
    
    } else {
        if ($admintype == 'member' || $admintype == 'diy') {
            $dvalue = HtmlReplace($dvalue, 1);
        
        }
        return $dvalue;
    
    }

}

/**
 *  获得带值的表单(编辑时用)
 *
 * @access public
 * @param  object $ctag      标签
 * @param  mixed  $fvalue    变量值
 * @param  string $admintype 用户类型
 * @param  string $fieldname 变量名称
 * @return string
 */
function GetFormItemValue($ctag, $fvalue, $admintype = 'admin', $fieldname = '')
{
    global $cfg_basedir, $dsql;
    $fieldname = $ctag->GetName();
    $formitem = $formitem = GetSysTemplets("custom_fields_{$admintype}.htm");
    $innertext = trim($ctag->GetInnerText());
    if ($innertext != '') {
        $formitem = $innertext;
    
    }
    $ftype = $ctag->GetAtt('type');
    $myformItem = '';
    if (preg_match("/select|radio|checkbox/i", $ftype)) {
        $items = explode(',', $ctag->GetAtt('default'));
    
    }
    if ($ftype == 'select') {
        $myformItem = "<select name='$fieldname' class='uk-select uk-form-width-large uk-form-small'>";
        if (is_array($items)) {
            foreach ($items as $v) {
                $v = trim($v);
                if ($v == '') {
                    continue;
                
                }
                $myformItem .= ($fvalue == $v ? "<option value='$v' selected>$v</option>\r\n" : "<option value='$v'>$v</option>\r\n");
            
            }
        
        }
        $myformItem .= "</select>\r\n";
        $innertext = $myformItem;
    
    } else if ($ctag->GetAtt("type") == 'stepselect') {
        global $hasSetEnumJs, $cfg_cmspath;
        $cmspath = ((empty($cfg_cmspath) || preg_match('/[/$]/', $cfg_cmspath)) ? $cfg_cmspath . '/' : $cfg_cmspath);
        $myformItem = '';
        $myformItem .= "<input type='hidden' id='hidden_{$fieldname}' name='{$fieldname}' value='{$fvalue}' />\r\n";
        $myformItem .= "<span id='span_{$fieldname}'></span>\r\n";
        $myformItem .= "<span id='span_{$fieldname}_son'></span>\r\n";
        $myformItem .= "<span id='span_{$fieldname}_sec'></span>\r\n";
        if ($hasSetEnumJs != 'hasset') {
            $myformItem .= '<script language="javascript" type="text/javascript" src="' . $cmspath . 'assets/pkg/dede/enums.js"></script>' . "\r\n";
            $GLOBALS['hasSetEnumJs'] = 'hasset';
        
        }
        $myformItem .= "<script language='javascript' type='text/javascript' src='{$cmspath}data/enums/{$fieldname}.js'></script>\r\n";
        $myformItem .= "<script language='javascript' type='text/javascript'>MakeTopSelect('$fieldname', $fvalue);</script>\r\n";
        $formitem = str_replace('~name~', $ctag->GetAtt('itemname'), $formitem);
        $formitem = str_replace('~form~', $myformItem, $formitem);
        return $formitem;
    
    } else if ($ftype == 'radio') {
        if (is_array($items)) {
            foreach ($items as $v) {
                $v = trim($v);
                if ($v == '') {
                    continue;
                
                }
                $myformItem .= ($fvalue == $v ? "<input type='radio' name='$fieldname' class='uk-radio' value='$v' checked='checked' />$v\r\n" : "<input type='radio' name='$fieldname' class='uk-radio' value='$v' /> $v \r\n");
            }
        
        }
        $innertext = $myformItem;
    
    }

    //checkbox
    else if ($ftype == 'checkbox') {
        $myformItem = '';
        $fvalues = explode(',', $fvalue);
        if (is_array($items)) {
            foreach ($items as $v) {
                $v = trim($v);
                if ($v == '') {
                    continue;
                
                }
                if (in_array($v, $fvalues)) {
                    $myformItem .= "<input type='checkbox' name='{$fieldname}[]' class='uk-checkbox' value='$v' checked='checked' /> $v &nbsp&nbsp\r\n";
                
                } else {
                    $myformItem .= "<input type='checkbox' name='{$fieldname}[]' class='uk-checkbox' value='$v' /> $v &nbsp&nbsp\r\n";
                
                }
            
            }
        
        }
        $innertext = $myformItem;
    
    }

    //文本数据的特殊处理
    else if ($ftype == "textdata") {
        if (is_file($cfg_basedir . $fvalue)) {
            $fp = fopen($cfg_basedir . $fvalue, 'r');
            $okfvalue = '';
            while (!feof($fp)) {$okfvalue .= fgets($fp, 1024);
            }
            fclose($fp);
        
        } else {
            $okfvalue = '';
        
        }
        if ($admintype == 'admin') {
            $myformItem = GetEditor($fieldname, $okfvalue, 350, 'Basic', 'string') . "\r\n <input type='hidden' name='{$fieldname}_file' value='{$fvalue}' />\r\n ";
        
        } else {
            $myformItem = GetEditor($fieldname, $okfvalue, 350, 'Member', 'string') . "\r\n <input type='hidden' name='{$fieldname}_file' value='{$fvalue}' />\r\n ";
        
        }
        $innertext = $myformItem;
    
    } else if ($ftype == "htmltext") {
        if ($admintype == 'admin') {
            $myformItem = GetEditor($fieldname, $fvalue, 350, 'Basic', 'string') . "\r\n ";
        
        } else {
            $myformItem = GetEditor($fieldname, $fvalue, 350, 'Member', 'string') . "\r\n ";
        
        }
        $innertext = $myformItem;
    
    } else if ($ftype == "multitext") {
        $innertext = "<textarea name='$fieldname' id='$fieldname' style='width:90%;height:80px' class='uk-textarea uk-form-width-large uk-form-small'>$fvalue</textarea>\r\n";
    
    } else if ($ftype == "datetime") {
        $nowtime = GetDateTimeMk($fvalue);
        $innertext = "<div class='uk-inline'>";
        $innertext .= "<span class='uk-form-icon uk-icon' uk-icon='icon: calendar4-week'></span>";
        $innertext .= "<input name='$fieldname' value='$nowtime' placeholder='1970-01-01T00:00:00Z' class='uk-input uk-form-width-large uk-form-small' type='datetime-local' >";
        $innertext .= "</div>";
    
    } else if ($ftype == "img") {
        $ndtp = new DedeTagParse();
        $ndtp->LoadSource($fvalue);
        if (!is_array($ndtp->CTags)) {
            $ndtp->Clear();
            $fvalue = (object) null;
        
        } else {
            $fvalue = $ndtp->GetTag("img");
        }
        $val = trim($fvalue->InnerText);

        $innertext = "
        <input name='$fieldname"."_url' type='hidden' id='$fieldname"."_url' value='$val'>
        <div  class='uk-inline'  uk-form-custom=\"target: true\">
        <span class='uk-form-icon uk-icon' uk-icon='icon: card-image'></span>
        <input name='$fieldname' type='file' id='$fieldname' />
        <input class='uk-input uk-form-width-large' name='$fieldname' type='text' id='$fieldname' placeholder='$val'> &nbsp&nbsp (点击选择本地图片)
        </div>\r\n";
    
    } else if ($ftype == "imgfile") {
        $ndtp = new DedeTagParse();
        $ndtp->LoadSource($fvalue);
        if (!is_array($ndtp->CTags)) {
            $ndtp->Clear();
            $fvalue = (object) null;
        } else {
            $fvalue = $ndtp->GetTag("img");
        }
        $val = trim($fvalue->InnerText);

        $innertext = "
        <input name='$fieldname"."_url' type='hidden' id='$fieldname"."_url' value='$val'>
        <div  class='uk-inline'  uk-form-custom=\"target: true\">
        <span class='uk-form-icon uk-icon' uk-icon='icon: globe2'></span>
        <input type='text' name='$fieldname' id='$fieldname'  value='$val' class='uk-input uk-form-width-large uk-form-small' placeholder='$val'/> &nbsp&nbsp（图片网址）
        </div>\r\n";
    
    } else if ($ftype == "media") {
        $val = trim($fvalue);
        $innertext = "
        <input name='$fieldname"."_url' type='hidden' id='$fieldname"."_url' value='$val'>
        <div  class='uk-inline'  uk-form-custom=\"target: true\">
        <span class='uk-form-icon uk-icon' uk-icon='icon: play-btn'></span>
        <input name='$fieldname' type='file' id='$fieldname' />
        <input class='uk-input uk-form-width-large' name='$fieldname' type='text' id='$fieldname' placeholder='$val'> &nbsp&nbsp (点击选择多媒体文件)
        </div>\r\n";

    } else if ($ftype == "addon") {
        $fvalue = trim($fvalue);
        $val = trim($fvalue);
        $innertext = "
        <input name='$fieldname"."_url' type='hidden' id='$fieldname"."_url' value='$val'>
        <div  class='uk-inline'  uk-form-custom=\"target: true\">
        <span class='uk-form-icon uk-icon' uk-icon='icon: parchive'></span>
        <input name='$fieldname' type='file' id='$fieldname' />
        <input class='uk-input uk-form-width-large' name='$fieldname' type='text' id='$fieldname' placeholder='$val'> &nbsp&nbsp (点击选择压缩包)
        </div>\r\n";
    } else if ($ftype == "int" || $ftype == "float") {
        $innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:100px'  class='uk-input uk-form-width-large uk-form-small' value='$fvalue' /> &nbsp&nbsp(填写数值)\r\n";
    
    } else {
        $innertext = "<input type='text' name='$fieldname' id='$fieldname' class='uk-input uk-form-width-large uk-form-small' value='$fvalue' />\r\n";
    
    }
    $formitem = str_replace('~name~', $ctag->GetAtt('itemname'), $formitem);
    $formitem = str_replace('~form~', $innertext, $formitem);
    return $formitem;

}

function UploadImage($fname)
{
    $pathinfo = pathinfo($_FILES[$fname]['name']);
    $ext = strtolower($pathinfo['extension']);
    global $cfg_imgtype;
    if (in_array(strtolower($ext), explode("|", $cfg_imgtype)) === false) {
        ShowMsg("系统不支持上传".$pathinfo['extension']."类型，".$_FILES[$fname]['name']."上传失败。", "-1");
        exit();
    }
    global $cfg_image_dir;
    return _upload($fname, $cfg_image_dir, 'png');
   
}

function UploadMedia($fname)
{
    global $cfg_mediatype;
    $pathinfo = pathinfo($_FILES[$fname]['name']);
    $ext = strtolower($pathinfo['extension']);
    if (in_array(strtolower($ext), explode("|", $cfg_mediatype)) === false) {
        ShowMsg("系统不支持上传".$pathinfo['extension']."类型，".$_FILES[$fname]['name']."上传失败。", "-1");
        exit();
    }
    global $cfg_other_medias;
    return _upload($fname, $cfg_other_medias, $ext);
}

function UploadAddon($fname)
{
    global $cfg_softtype;
    $pathinfo = pathinfo($_FILES[$fname]['name']);
    $ext = strtolower($pathinfo['extension']);
    if (in_array(strtolower($ext), explode("|", $cfg_softtype)) === false) {
        ShowMsg("系统不支持上传".$pathinfo['extension']."类型，".$_FILES[$fname]['name']."上传失败。", "-1");
        exit();
    }
    global $cfg_soft_dir;
    return _upload($fname, $cfg_soft_dir, $ext);
}



//保存二进制文件数据
//为了安全起见，对二进制数据保存使用base64编码后存入
function GetBinData($fname)
{
    $tmp = DEDEDATA . '/uploadtmp';
    if (!isset($_FILES[$fname]['tmp_name']) || !is_uploaded_file($_FILES[$fname]['tmp_name'])) {
        return '';
    } else {
        $tmpfile = $tmp . '/' . md5(time() . mt_rand(1000, 5000)) . '.tmp';
        $rs = move_uploaded_file($_FILES[$fname]['tmp_name'], $tmpfile);
        if (!$rs) {
            return '';
        }
        $fp = fopen($tmpfile, 'r');
        $data = base64_encode(fread($fp, filesize($tmpfile)));
        fclose($fp);
        @unlink($tmpfile);
        return $data;
    }
}

// 上传文件名，保存路径，保存扩展名
function _upload($fname, $path, $ext)
{
    $src =  GetBinData($fname);
    if (!empty($src)) {
        global  $cfg_addon_savetype, $cfg_basedir, $cuserLogin;
        $ntime = time();
        $savepath =  $path . '/' . MyDate($cfg_addon_savetype, $ntime);
        CreateDir($savepath);
        $fullUrl = $savepath . '/U-' . dd2char(MyDate('mdHis', $ntime) . substr(md5(time() . mt_rand(1000, 5000) . "86‌5H‌6088‌5D‌5285‌59‌6G1C85‌5O‌6186‌6E‌671D87‌5G‌5J84‌6G‌5186‌5C‌6784‌6G‌5G88‌5P‌6N"), 0, 6). $cuserLogin->getUserID() . mt_rand(1000, 9999));
        $fullUrl = $fullUrl . ".".$ext;
        file_put_contents($cfg_basedir . $fullUrl, base64_decode($src));
        return $fullUrl;
    } 
}