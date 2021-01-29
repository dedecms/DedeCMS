![dedecms](/assets/img/dedecms_logo.png)

​ DedeCMS 是织梦团队开发 PHP 网站管理系统，它以简单、易用、高效为特色，组建出各种各样各具特色的网站，如地方门户、行业门户、政府及企事业站点等。

## DedeCMS Git 说明

DedeCMS代码托管在Github，织梦项目组集中发布的公开项目为 : https://github.com/dedecms/DedeCMS 

开发过程中以night beta（夜间测试版）的方式更新，测试版不能用于生产。

获取可用于生产的正式版请访问：http://www.dedecms.com 或 https://github.com/dedecms/DedeCMS/releases

### Windows 环境：

IIS/Apache/Nginx + PHP7+ + MySQL/MariaDB

如果在 windows 环境中使用，建议用 DedeCMS 提供的 DedeAMPZ 套件以达到最佳使用性能。

### Linux/Unix 环境：

Apache/Nginx + PHP7+ + MySQL/MariaDB (PHP 必须在非安全模式下运行)

### 建议使用环境:

建议使用平台：OpenBSD + Nginx + PHP7 + MariaDB

推荐理由：

1. OpenBSD以安全著称，其会对pkg源内的软件进行安全优化，在许多软件开发商未发现漏洞前进行安全补丁；
2. OpenBSD的PF防火墙非常强大，许多硬件防火墙均使用OpenBSD的PF防火墙进行二次开发；
3. 运行在OpenBSD的chroot模式下的Nginx、PHP即使因漏洞被攻破，也不会影响主系统的安全。

注意事项：

1. 请使用pkg_add来安装Nginx、PHP、MariaDB，确保系统安全；
2. 赋予网站一个低权限系统用户，切勿使用root、www、php等用户；
3. 分配给MariaDB库一个低权限sql用户，切勿使用root等MariaDB系统用户；
4. 上传数据可使用SFTP进行，OpenSSH也是OpenBSD团队开源的优秀系统，在安装OpenBSD时确保SSH开启即可使用SFTP；
5. SFTP用户为系统用户，对于多用户服务器可在OpenBSD内对低权限系统用户进行目录访问限制；
6. MariaDB远程控制，同样可以使用SSH方式链接服务器进行GUI化管理，目前大多数的MYSQL GUI管理工具均支持SSH方式远程访问数据库。

### PHP 函数库依赖：

allow_url_fopen

GD 扩展库

MySQL 扩展库

系统函数 —— phpinfo、dir

## 基本目录结构

```
/
../a           默认HTML文件存放目录(必须可写入)
../install     安装程序目录，安装完后可删除[安装时必须有可写入权限]
../dede        默认后台管理目录（可任意改名）
../include     类库文件目录
../plus        附助程序目录
../assets      系统默认静态资源目录
../uploads     默认上传目录(必须可写入)
../templets    系统默认内核模板目录
../data        系统缓存或其它可写入数据存放目录(必须可写入)
../special     专题目录(生成一次专题后可以删除special/index.php，必须可写入)
```

## 兼容性问题

1. data 目录没写入权限，导致系统 session 无法使用，这将导致无法登录管理后台（直接表现为验证码不能正常显示）；
2. php 的上传的临时文件夹没设置好或没写入权限，这会导致文件上传的功能无法使用；
3. 出现莫名的错误，如安装时显示空白，这样能是由于系统没装载 mysql 扩展导致的，对于初级用户，可以下载 dede 的 php 套件包，以方便简单的使用。

## 常见问题

1. 上传文件时出现：413 Request Entity Too Large错误，请检查php.ini中upload_max_filesize、post_max_size及Nginx的client_max_body_size设置;

## 安装

1. 下载程序解压到本地目录;
2. 上传程序目录中的/uploads 到网站根目录
3. 运行 http://你的域名或 IP/install/index.php, 按照安装提速说明进行程序安装。

## License

请参阅[许可协议](/license.txt)。

## Resources

- 请参阅[awesome-dedecms](https://github.com/dedecms/awesome-dedecms)。
