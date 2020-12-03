<?
if (!empty($_GET))   { foreach($_GET AS $key => $value) $$key = $value; }
if (!empty($_POST))  { foreach($_POST AS $key => $value) $$key = $value; }
if (!empty($_COOKIE)){ foreach($_COOKIE AS $key => $value) $$key = $value; }
if (!empty($_FILES)) {
  foreach($_FILES AS $name => $value)
  {
     $$name = $value['tmp_name'];
     foreach($value AS $namen => $valuen){
       ${$name.'_'.$namen} = $value[$namen];
     }
  }
}
//$file1 临时文件名
//$file1_name 原始文件名
//$file1_type 文件类型
//$file1_tmp_name 临时文件名与 $file1 相同
//$file1_error 文件错误
//$file1_size 文件大小
?>