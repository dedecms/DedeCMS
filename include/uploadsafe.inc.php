<?php
if(!defined('DEDEINC')) exit('DedeCMS Error: Request Error!');

if(isset($_FILES['GLOBALS'])) exit('Request not allow!');

//为了防止用户通过注入的可能性改动了数据库
//这里强制限定的某些文件类型禁止上传
$cfg_not_allowall = "php|pl|cgi|asp|aspx|jsp|php3|shtm|shtml";
$keyarr = array('name', 'type', 'tmp_name', 'size');
if ($GLOBALS['cfg_html_editor']=='ckeditor' && isset($_FILES['upload']))
{
    $_FILES['imgfile'] = $_FILES['upload'];
    $CKUpload = TRUE;
    unset($_FILES['upload']);
}
foreach($_FILES as $_key=>$_value)
{
    foreach($keyarr as $k)
    {
        if(!isset($_FILES[$_key][$k]))
        {
            exit('DedeCMS Error: Request Error!');
        }
    }
    if( preg_match('#^(cfg_|GLOBALS)#', $_key) )
    {
        exit('Request var not allow for uploadsafe!');
    }
    $$_key = $_FILES[$_key]['tmp_name'];
    ${$_key.'_name'} = $_FILES[$_key]['name'];
    ${$_key.'_type'} = $_FILES[$_key]['type'] = preg_replace('#[^0-9a-z\./]#i', '', $_FILES[$_key]['type']);
    ${$_key.'_size'} = $_FILES[$_key]['size'] = preg_replace('#[^0-9]#','',$_FILES[$_key]['size']);
    if(!empty(${$_key.'_name'}) && (in_array(pathinfo(${$_key.'_name'}, PATHINFO_EXTENSION), explode("|", $cfg_not_allowall), true) === TRUE || !preg_match("#\.#", ${$_key.'_name'})) )
    {
        if(!defined('DEDEADMIN'))
        {
            exit('Not Admin Upload filetype not allow !');
        }
    }
    if(empty(${$_key.'_size'}))
    {
        ${$_key.'_size'} = @filesize($$_key);
    }

    // 如果是图片
    if (preg_match('#image/([a-z]+)#i', ${$_key . '_type'}) || preg_match("#{$cfg_imgtype}#i", pathinfo(${$_key . '_name'}, PATHINFO_EXTENSION))) {
        $imageInfos = getimagesize($$_key);
        if (!is_array($imageInfos) && ${$_key . '_type'} !== 'image/webp') {
            exit('Upload filetype not allow !');
        }

        if (${$_key . '_type'} === 'image/jpeg' || preg_match("#jpg|jpeg#i", pathinfo(${$_key . '_name'}, PATHINFO_EXTENSION))) {
            $image = imagecreatefromjpeg($$_key);
            imagejpeg($image, $$_key);
        } else if (${$_key . '_type'} === 'image/png' || preg_match("#png#i", pathinfo(${$_key . '_name'}, PATHINFO_EXTENSION))) {
            $image = imagecreatefrompng($$_key);
            imagesavealpha($image, true);
            header('Content-type: image/png');
            imagepng($image, $$_key);
        }

        imagedestroy($image);
    }
}
?>