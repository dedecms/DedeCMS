<?
//对于安全模式的PHP,本文件用于注册外部变量
//这个文件不应该在〈? ?〉外面的任何地方出现任何字符,否则部分程序可能性出错
//把GET变量分解为外部变量
if (!empty($_GET))
{
    foreach($_GET AS $key => $value)
    {
    	$$key = $value;
    }
}
//把POST变量分解为外部变量
if (!empty($_POST))
{
    foreach($_POST AS $key => $value)
    {
    	$$key = $value;
    }
}
//把cookie变量分解为外部变量
if (!empty($_COOKIE))
{
    foreach($_COOKIE AS $key => $value)
    {
    	$$key = $value;
    }
}
//把FILES变量分解为外部变量
//FILES变量分解出的外部变量为(假设客户端的上传框名称为file1)
//$file1 临时文件名
//$file1_name 原始文件名
//$file1_type 文件类型
//$file1_tmp_name 临时文件名与 $file1 相同
//$file1_error 文件错误
//$file1_size 文件大小
if (!empty($_FILES)) {
    foreach($_FILES AS $name => $value)
    {
        $$name = $value['tmp_name'];
        foreach($value AS $namen => $valuen)
        {
        	${$name.'_'.$namen} = $value[$namen];
        }
    }
}
?>