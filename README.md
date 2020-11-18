## 介绍

> 基于 [lustre/php-dfa-sensitive](#https://github.com/FireLustre/php-dfa-sensitive) 添加了默认敏感词库

## 安装

```shell
composer require jmhc/sensitive
```

## 使用

```php
use Jmhc\Sensitive\SensitiveService;

// 实例化服务
$sensitive = new SensitiveService();
// 设置敏感字符串数组
$sensitive->setSensitiveWords([]);
// 设置排除字符串数组
$sensitive->setExceptWords([]);
// 使用敏感词库,默认使用
$sensitive->withSensitiveWordLibrary(true);
// 验证,有敏感词会抛出 Jmhc\Sensitive\SensitiveException 异常
$sensitive->validate('这是需要验证的字符串', '错误消息前缀');
```