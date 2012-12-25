gnudip2ddns
===========

把GnuDip请求转发给若干DDNS提供商。

由于电信提供的华为HG526 modem只支持GnuDip动态域名协议，不支持其它动态域名协议，因此自己实现了GnuDip协议，以便更新各动态域名。

本应用原来是为新浪AppEngine开发的，部署在新浪AppEngine上。

使用时需要修改update.php：

. 设定更新使用的密码

. 设定要更新的若干DDNS的更新URL
