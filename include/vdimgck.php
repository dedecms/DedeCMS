<?php
include 'captcha/securimage.php';
require_once (dirname(__FILE__).'/../data/safe/inc_safe_config.php');
//Session保存路径
$sessSavePath = dirname(__FILE__)."/../data/sessions/";
if(is_writeable($sessSavePath) && is_readable($sessSavePath)){ session_save_path($sessSavePath); }

$img = new securimage();
$img->image_width = $safe_wwidth;
$img->image_height = $safe_wheight;
$img->image_type = $safe_gdtype;
$img->font_size = 12;
$img->text_x_start = 1;
$img->text_minimum_distance = 12;
$img->text_maximum_distance = 13;
$img->arc_linethrough = false;
$img->code_length = $safe_codelen;
//生成验证码类型
if($safe_codetype == 1) $img->charset = '0123456789';
else if($safe_codetype == 2) $img->charset = 'ABCDEFGHKLMNPRSTUVWYZ';
else if($safe_codetype == 3) $img->use_wordlist = true;

$img->wordlist_file = dirname(__FILE__).'/data/words/words.txt';
$img->audio_path = dirname(__FILE__).'/data/audio/';
$img->ttf_file = dirname(__FILE__).'/data/fonts/incite.ttf';
$img->draw_lines = false;
if ($handle = @opendir('data/background/'))
{
    while ($bgfile = @readdir($handle))
    {
        if (preg_match('/\.jpg$/i', $bgfile))
        {
            $backgrounds[] = 'data/background/'.$bgfile;
        }
    }
    @closedir($handle);
}
srand ((float) microtime() * 10000000);
$rand_keys = array_rand ($backgrounds);
$background = $backgrounds[$rand_keys];
$bg = '';
if(preg_match('/1/',$safe_gdstyle))
{
	$img->draw_lines = true ;
}
if (preg_match('/2/',$safe_gdstyle))
{
	$bg = $background;
}
if (preg_match('/3/',$safe_gdstyle))
{
	$img->use_multi_text = true;
}
//var_dump($bg);
$img->show($bg);
?>
