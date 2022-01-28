<?php   if(!defined('DEDEINC')) exit('DedeCMS Error: Request Error!');
/**
 * 图像处理类
 *
 * @version        $Id: image.class.php 1 18:10 2010年7月5日 $
 * @package        DedeCMS.Libraries
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
class image
{
    var $attachinfo;
    var $targetfile;    //图片路径
    var $imagecreatefromfunc;
    var $imagefunc;
    var $attach;
    var $animatedgif;
    var $watermarkquality;
    var $watermarktext;
    var $thumbstatus;
    var $watermarkstatus;
    
    // 析构函数,兼容PHP4
    function image($targetfile, $cfg_thumb, $cfg_watermarktext, $photo_waterpos, $photo_diaphaneity, $photo_wheight, $photo_wwidth, $cfg_watermarktype, $photo_marktrans,$trueMarkimg, $attach = array())
    {
        $this->__construct($targetfile, $cfg_thumb, $cfg_watermarktext, $photo_waterpos, $photo_diaphaneity, $photo_wheight, $photo_wwidth, $cfg_watermarktype, $photo_marktrans,$trueMarkimg, $attach);
    }

    // 析构函数
    function __construct($targetfile, $cfg_thumb, $cfg_watermarktext, $photo_waterpos, $photo_diaphaneity, $photo_wheight, $photo_wwidth, $cfg_watermarktype, $photo_marktrans,$trueMarkimg, $attach = array())
    {
        $this->thumbstatus = $cfg_thumb;
        $this->watermarktext = $cfg_watermarktext;
        $this->watermarkstatus = $photo_waterpos;
        $this->watermarkquality = $photo_marktrans;
        $this->watermarkminwidth = $photo_wwidth;
        $this->watermarkminheight = $photo_wheight;
        $this->watermarktype = $cfg_watermarktype;
        $this->watermarktrans = $photo_diaphaneity;
        $this->animatedgif = 0;
        $this->targetfile = $targetfile;
        $this->attachinfo = @getimagesize($targetfile);
        $this->attach = $attach;


        switch($this->attachinfo['mime'])
        {
            case 'image/jpeg':
                $this->imagecreatefromfunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
                $this->imagefunc = function_exists('imagejpeg') ? 'imagejpeg' : '';
                break;
            case 'image/gif':
                $this->imagecreatefromfunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
                $this->imagefunc = function_exists('imagegif') ? 'imagegif' : '';
                break;
            case 'image/png':
                $this->imagecreatefromfunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
                $this->imagefunc = function_exists('imagepng') ? 'imagepng' : '';
                break;
        }//为空则匹配类型的函数不存在

        $this->attach['size'] = empty($this->attach['size']) ? @filesize($targetfile) : $this->attach['size'];
        if($this->attachinfo['mime'] == 'image/gif')
        {
            $fp = fopen($targetfile, 'rb');
            $targetfilecontent = fread($fp, $this->attach['size']);
            fclose($fp);
            $this->animatedgif = strpos($targetfilecontent, 'NETSCAPE2.0') === false ? 0 : 1;
        }
    }

    /**
     *  生成缩略图
     *
     * @access    public
     * @param     int  $thumbwidth  图片宽度
     * @param     int  $thumbheight  图片高度
     * @param     int  $preview  是否预览
     * @return    void
     */
    function thumb($thumbwidth, $thumbheight, $preview = 0)
    {
        $this->thumb_gd($thumbwidth, $thumbheight, $preview);

        if($this->thumbstatus == 2 && $this->watermarkstatus)
        {
            $this->image($this->targetfile, $this->attach);
            $this->attach['size'] = filesize($this->targetfile);
        }
    }

    /**
     *  图片水印
     *
     * @access    public
     * @param     int   $preview  是否预览
     * @return    void
     */
    function watermark($preview = 0)
    {
        if($this->watermarkminwidth && $this->attachinfo[0] <= $this->watermarkminwidth && $this->watermarkminheight && $this->attachinfo[1] <= $this->watermarkminheight)
        {
            return ;
        }
        $this->watermark_gd($preview);
    }

    /**
     *  使用gd生成缩略图
     *
     * @access    public
     * @param     int  $thumbwidth  图片宽度
     * @param     int  $thumbheight  图片高度
     * @param     int  $preview  是否预览
     * @return    void
     */
    function thumb_gd($thumbwidth, $thumbheight, $preview = 0)
    {

        if($this->thumbstatus && function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled') && function_exists('imagejpeg'))
        {
            $imagecreatefromfunc = $this->imagecreatefromfunc;
            $imagefunc = $this->thumbstatus == 1 ? 'imagejpeg' : $this->imagefunc;
            list($imagewidth, $imageheight) = $this->attachinfo;
            if(!$this->animatedgif && ($imagewidth >= $thumbwidth || $imageheight >= $thumbheight))
            {
                $attach_photo = $imagecreatefromfunc($this->targetfile);
                $x_ratio = $thumbwidth / $imagewidth;
                $y_ratio = $thumbheight / $imageheight;
                if(($x_ratio * $imageheight) < $thumbheight)
                {
                    $thumb['height'] = ceil($x_ratio * $imageheight);
                    $thumb['width'] = $thumbwidth;
                }
                else
                {
                    $thumb['width'] = ceil($y_ratio * $imagewidth);
                    $thumb['height'] = $thumbheight;
                }
                $targetfile = !$preview ? ($this->thumbstatus == 1 ? $this->targetfile.'.thumb.jpg' : $this->targetfile) : './watermark_tmp.jpg';
                $thumb_photo = imagecreatetruecolor($thumb['width'], $thumb['height']);
                imagecopyresampled($thumb_photo, $attach_photo, 0, 0, 0, 0, $thumb['width'], $thumb['height'], $imagewidth, $imageheight);
                if($this->attachinfo['mime'] == 'image/jpeg')
                {
                    $imagefunc($thumb_photo, $targetfile, 100);
                }
                else
                {
                    $imagefunc($thumb_photo, $targetfile);
                }
                $this->attach['thumb'] = $this->thumbstatus == 1 ? 1 : 0;
            }
        }
    }

    /**
     *  使用gd进行水印
     *
     * @access    public
     * @param     int   $preview  是否预览
     * @return    void
     */
    function watermark_gd($preview = 0)
    {
        if($this->watermarkstatus && function_exists('imagecopy') && function_exists('imagealphablending') && function_exists('imagecopymerge'))
        {
            $imagecreatefunc = $this->imagecreatefromfunc;
            $imagefunc = $this->imagefunc;
            list($imagewidth, $imageheight) = $this->attachinfo;
            if($this->watermarktype < 2)
            {
                $watermark_file = $this->watermarktype == 1 ? DEDEDATA.'/mark/mark.png' : DEDEDATA.'/mark/mark.gif';
                $watermarkinfo = @getimagesize($watermark_file);
                $watermark_logo = $this->watermarktype == 1 ? @imagecreatefrompng($watermark_file) : @imagecreatefromgif($watermark_file);
                if(!$watermark_logo)
                {
                    return ;
                }
                list($logowidth, $logoheight) = $watermarkinfo;
            }
            else
            {
                $this->watermarktext['text'] = $this->strAutoSplit($this->watermarktext['text'], 'UTF-8', $this->watermarktext['fontpath'], $this->watermarktext['size'], $imagewidth - 12);

                $box = @imagettfbbox($this->watermarktext['size'], $this->watermarktext['angle'], $this->watermarktext['fontpath'],$this->watermarktext['text']);
                $logowidth = max($box[2], $box[4]) - min($box[0], $box[6]);
                $logoheight = max($box[1], $box[3]) - min($box[5], $box[7]);
                $ax = min($box[0], $box[6]) * -1;
                $ay = min($box[5], $box[7]) * -1;
            }
            $wmwidth = $imagewidth - $logowidth;
            $wmheight = $imageheight - $logoheight;
            if(($this->watermarktype < 2 && is_readable($watermark_file) || $this->watermarktype == 2) && $wmwidth > 10 && $wmheight > 10 && !$this->animatedgif)
            {
                switch($this->watermarkstatus)
                {
                    case 1:

                        $x = +5;
                        $y = +5;
                        break;
                    case 2:
                        $x = ($imagewidth - $logowidth) / 2;
                        $y = +5;
                        break;
                    case 3:
                        $x = $imagewidth - $logowidth - 5;
                        $y = +5;
                        break;
                    case 4:
                        $x = +5;
                        $y = ($imageheight - $logoheight) / 2;
                        break;
                    case 5:
                        $x = ($imagewidth - $logowidth) / 2;
                        $y = ($imageheight - $logoheight) / 2;
                        break;
                    case 6:
                        $x = $imagewidth - $logowidth - 5;
                        $y = ($imageheight - $logoheight) / 2;
                        break;
                    case 7:
                        $x = +5;
                        $y = $imageheight - $logoheight - 5;
                        break;
                    case 8:
                        $x = ($imagewidth - $logowidth) / 2;
                        $y = $imageheight - $logoheight - 5;
                        break;
                    case 9:
                        $x = $imagewidth - $logowidth - 5;
                        $y = $imageheight - $logoheight -5;
                        break;
                }
                $dst_photo = @imagecreatetruecolor($imagewidth, $imageheight);
                imagealphablending($dst_photo, true);
                imagesavealpha($dst_photo, true);
                $bg =imagecolorallocatealpha($dst_photo, 255, 255, 255, 127);
                imagefill($dst_photo, 0, 0, $bg);
                $target_photo = $imagecreatefunc($this->targetfile);
                imagecopy($dst_photo, $target_photo, 0, 0, 0, 0, $imagewidth, $imageheight);
                if($this->watermarktype == 1)
                {
                    imagecopy($dst_photo, $watermark_logo, $x, $y, 0, 0, $logowidth, $logoheight);
                }
                elseif($this->watermarktype == 2)
                {
                    $textBackground = imagecreatetruecolor($imagewidth, $imageheight);
                    $colorrgba = explode(',', $this->watermarktext['background']);
                    $textBackgroundColor = imagecolorallocatealpha($textBackground, $colorrgba[0], $colorrgba[1], $colorrgba[2], $colorrgba[3]);
                    imagefill($textBackground, 0, 0, $textBackgroundColor);
                    if ($this->watermarktext['padding'] < 0) {
                        $this->watermarktext['padding'] = 0;
                    }
                    $textBackgroundHeight = $logoheight + $this->watermarktext['padding'] + $this->watermarktext['padding'];
                    if ($textBackgroundHeight >= $imageheight) {
                        $textBackgroundHeight = $imageheight - 10;
                    }
                    $padding = ($textBackgroundHeight - $logoheight) / 2;
                    $textBackgroundPadding = $padding;
                    if ($this->watermarkstatus == 1 || $this->watermarkstatus == 2 || $this->watermarkstatus == 3) {
                        $textBackgroundPadding = 0;
                    }
                    if ($this->watermarkstatus == 7 || $this->watermarkstatus == 8 || $this->watermarkstatus == 9) {
                        $textBackgroundPadding = $textBackgroundPadding * 2;
                    }
                    $textPadding = 0;
                    if ($this->watermarkstatus == 1 || $this->watermarkstatus == 2 || $this->watermarkstatus == 3) {
                        $textPadding = $padding * -1;
                    }
                    if ($this->watermarkstatus == 7 || $this->watermarkstatus == 8 || $this->watermarkstatus == 9) {
                        $textPadding = $padding;
                    }

                    imagecopy($dst_photo, $textBackground, 0, $y - $textBackgroundPadding, 0, 0, $imagewidth, $textBackgroundHeight);

                    if(($this->watermarktext['shadowx'] || $this->watermarktext['shadowy']) && $this->watermarktext['shadowcolor'])
                    {
                        $shadowcolorrgb = explode(',', $this->watermarktext['shadowcolor']);
                        $shadowcolor = imagecolorallocate($dst_photo, $shadowcolorrgb[0], $shadowcolorrgb[1], $shadowcolorrgb[2]);
                        imagettftext($dst_photo, $this->watermarktext['size'], $this->watermarktext['angle'],
                        $x + $ax + $this->watermarktext['shadowx'], $y + $ay - $textPadding + $this->watermarktext['shadowy'], $shadowcolor,
                        $this->watermarktext['fontpath'], $this->watermarktext['text']);
                    }
                    $colorrgb = explode(',', $this->watermarktext['color']);
                    $color = imagecolorallocate($dst_photo, $colorrgb[0], $colorrgb[1], $colorrgb[2]);
                    imagettftext($dst_photo, $this->watermarktext['size'], $this->watermarktext['angle'],
                    $x + $ax, $y + $ay - $textPadding, $color, $this->watermarktext['fontpath'], $this->watermarktext['text']);
                }
                else
                {
                    imagealphablending($watermark_logo, true);
                    imagecopymerge($dst_photo, $watermark_logo, $x, $y, 0, 0, $logowidth, $logoheight, $this->watermarktrans);
                }
                $targetfile = !$preview ? $this->targetfile : './watermark_tmp.jpg';
                if($this->attachinfo['mime'] == 'image/jpeg')
                {
                    $imagefunc($dst_photo, $targetfile, $this->watermarkquality);
                }
                else
                {
                    $imagefunc($dst_photo, $targetfile);
                }
                $this->attach['size'] = filesize($this->targetfile);
            }
        }
    }

    function getTextFromWidth($str, $charset, $fontFamily, $fontSize, $width) {
        for ($i = 0; $i <= mb_strlen($str, $charset); $i++) {
            $text = mb_substr($str, 0, $i, $charset);
            $boxs = imagettfbbox($fontSize, 0, $fontFamily, $text);
            $textWidth = abs(max($boxs[2], $boxs[4]) - min($boxs[0], $boxs[6]));
            if ($textWidth > $width) {
                return mb_substr($str, 0, $i - 1, $charset);
                break;
            }
        }
        return $text;
    }

    function strAutoSplit($str, $charset, $fontFamily, $fontSize, $width) {
        $result = "";
        for (;;) {
            $text = $this->getTextFromWidth($str, $charset, $fontFamily, $fontSize, $width);
            $result = "{$result}\r\n{$text}";
            $len = mb_strlen($text, $charset);
            $str = mb_substr($str, $len, NULL, $charset);
            if ($str == mb_substr($str, $len, NULL, $charset)) {
                break;
            }
        }
        return $result;
    }
}//End Class