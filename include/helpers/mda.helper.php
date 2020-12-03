<?php if(!defined('DEDEINC')) exit('dedecms');

define('MDA_APIHOST', 'http://ssp.desdev.cn');

define('MDA_JQUERY', MDA_APIHOST.'/assets/js/jquery.min.js');
define('MDA_REG_URL', MDA_APIHOST.'/home/register');
define('MDA_FORGOT_PASSWORD_URL', MDA_APIHOST.'/home/forgot_password');
define('MDA_UPDATE_URL', MDA_APIHOST.'/home/update');

define('MDA_API_BIND_USER', MDA_APIHOST.'/api_v1/dedecms/bind_user');
define('MDA_API_LOGIN', MDA_APIHOST.'/api_v1/dedecms/login');
define('MDA_API_CHECK_LOGIN', MDA_APIHOST.'/api_v1/dedecms/check_login');
define('MDA_API_GET_PLACE', MDA_APIHOST.'/api_v1/dedecms/get_place');

define('MDA_VER', '0.0.1');

function mda_http_send($url, $limit=0, $post='', $cookie='', $timeout=15)
{
    $return = '';
    $matches = parse_url($url);
    $scheme = $matches['scheme'];
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'].(@$matches['query'] ? '?'.$matches['query'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : 80;

    if (function_exists('curl_init') && function_exists('curl_exec')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $scheme.'://'.$host.':'.$port.$path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            $content = is_array($post) ? http_build_query($post) : $post;
            curl_setopt($ch, CURLOPT_POSTFIELDS, urldecode($content));
        }
        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, 900);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        
        if ($errno ) {
            return;
        } else {
            return !$limit ? $data : substr($data, 0, $limit);
        }
    }

    if ($post) {
        $content = is_array($post) ? urldecode(http_build_query($post)) : $post;
        $out = "POST $path HTTP/1.0\r\n";
        $header = "Accept: */*\r\n";
        $header .= "Accept-Language: zh-cn\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "User-Agent: ".@$_SERVER['HTTP_USER_AGENT']."\r\n";
        $header .= "Host: $host:$port\r\n";
        $header .= 'Content-Length: '.strlen($content)."\r\n";
        $header .= "Connection: Close\r\n";
        $header .= "Cache-Control: no-cache\r\n";
        $header .= "Cookie: $cookie\r\n\r\n";
        $out .= $header.$content;
    } else {
        $out = "GET $path HTTP/1.0\r\n";
        $header = "Accept: */*\r\n";
        $header .= "Accept-Language: zh-cn\r\n";
        $header .= "User-Agent: ".@$_SERVER['HTTP_USER_AGENT']."\r\n";
        $header .= "Host: $host:$port\r\n";
        $header .= "Connection: Close\r\n";
        $header .= "Cookie: $cookie\r\n\r\n";
        $out .= $header;
    }

    $fpflag = 0;
    $fp = false;
    if (function_exists('fsocketopen')) {
        $fp = fsocketopen($host, $port, $errno, $errstr, $timeout);
    }
    if (!$fp) {
        $context = stream_context_create(array(
            'http' => array(
                'method' => $post ? 'POST' : 'GET',
                'header' => $header,
                'content' => $content,
                'timeout' => $timeout,
            ),
        ));
        $fp = @fopen($scheme.'://'.$host.':'.$port.$path, 'b', false, $context);
        $fpflag = 1;
    }

    if (!$fp) {
        return '';
    } else {
        stream_set_blocking($fp, true);
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        $status = stream_get_meta_data($fp);
        if (!$status['timed_out']) {
            while (!feof($fp) && !$fpflag) {
                if (($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                    break;
                }
            }
            if ($limit) {
                $return = stream_get_contents($fp, $limit);
            } else {
                $return = stream_get_contents($fp);
            }
        }
        @fclose($fp);
        return $return;
    }
}

function mda_get_setting($skey, $time=false, $real=false)
{
    global $dsql;
    static $setting = array();
    $skey=addslashes($skey);
    if (empty($setting[$skey]) || $real) {
        $row = $dsql->GetOne("SELECT * FROM `#@__plus_mda_setting` WHERE skey='{$skey}'");
        $setting[$skey]['svalue']=$row['svalue'];
        $setting[$skey]['stime']=$row['stime'];
    }
    if (!isset($setting[$skey])) return $time ? array() : null;
    if ( $skey == 'channel_uuid' AND empty($setting[$skey]['svalue']) ) return '58b78319a0efe';
    if ( $skey == 'channel_secret' AND empty($setting[$skey]['svalue']) ) return 'lDQ97LIb4NXwCV2z';
    return $time ? $setting[$skey] : $setting[$skey]['svalue'];
}

function mda_set_setting($skey, $svalue)
{
    global $dsql;
    $stime=time();
    $skey=addslashes($skey);
    $svalue=addslashes($svalue);
    $sql="UPDATE `#@__plus_mda_setting` SET svalue='{$svalue}',stime='{$stime}' WHERE skey='{$skey}' ";
    $dsql->ExecuteNoneQuery($sql);
}

function mda_check_islogin()
{
    global $dopost;
    $jquery_url = MDA_JQUERY;
    $mda_login=MDA_API_CHECK_LOGIN;
    echo <<<EOT
<script type="text/javascript" src="{$jquery_url}"></script>
<script type="text/javascript">
(function($){
    $.ajax({
        type: "GET",
        url: "{$mda_login}",
        dataType : 'jsonp',
        jsonpCallback:"callfunc",
        success: function(msg){
            if(msg.code != 0){
                window.location.href='?dopost=login&nomsg=yes&forward={$dopost}';
                //console.log( msg );
            }
        }
    });
})(jQuery)
</script>    
EOT;
    //exit;
}

function mda_islogin()
{
    if(empty($_SESSION['mda_email'])) {
        return FALSE;
    }
    $email = mda_get_setting('email');
    $channel_uuid = mda_get_setting('channel_uuid');
    $channel_secret = mda_get_setting('channel_secret');
    if(empty($email) OR empty($channel_uuid) OR empty($channel_uuid)) {
        return FALSE;
    }
    return TRUE;
}
?>
