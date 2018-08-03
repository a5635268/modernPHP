# PHPcomposer使用手札[ing]


> 本文主要是使用composer中的一些记录


## 参考链接

中文文档： http://www.phpcomposer.com/
五个技巧：http://www.jb51.net/article/53881.htm

## composer的原理步骤

1. 分析你的composer.json文件，找到所有需要安装的第三方软件的名称和对应的版本号
2. 从本地缓存目录和Packagist服务器获取上述的第三方软件的信息，包含最新版本，代码存放地址等等
3. 分析依赖关系，根据包依赖、版本是否有更新等条件计算出最终需要安装的第三方软件的清单
4. 根据这份清单下载第三方软件的源代码，根据参数的不同，下载方式会是用Git Clone项目或者是直接下载Zip包
5. 将第三方软件安装到本地，一般是安装在项目下的./vendor目录，同时根据参数生成用于载入第三方软件的autoload文件

## [安装][1]

> 注意安装完要执行composer self-update来检查是否是最新的

## [配置中国镜像][2]

```
composer config -g repo.packagist composer https://packagist.phpcomposer.com
```

## [composer.json](http://docs.phpcomposer.com/01-basic-usage.html#composer.json-Project-Setup)


- [具体键名说明](http://docs.phpcomposer.com/04-schema.html#JSON-schema)



## [composer.lock - 锁文件](http://docs.phpcomposer.com/01-basic-usage.html#composer.lock-The-Lock-File)

第一次 `composer install` 以后就会生成这个锁文件，一旦有这个锁文件以后的update就是根据这个锁文件进行更新，如果composer.json 里面有了改变,哪怕一个小小的空格都会导致改变文件的md5sum。然后Composer就会警告你哈希值和composer.lock中记载的不同。

## [Packagist](http://docs.phpcomposer.com/01-basic-usage.html#Packagist)


一些常用的包：

- [http请求类：vinelab/http](https://packagist.org/packages/vinelab/http)
- [实现依赖注入的容器 topthink/think-container](https://packagist.org/packages/topthink/think-container)
- [think5.0 ORM](https://packagist.org/packages/topthink/think-orm)
- [支持文件及SocketLog的日志：topthink/think-log]( https://packagist.org/packages/topthink/think-log)
- [缓存管理 topthink/think-cache](https://packagist.org/packages/topthink/think-cache)
- [ 模板引擎 topthink/think-template](https://packagist.org/packages/topthink/think-template)
- [微信开发]( https://packagist.org/packages/overtrue/wechat)
- [微信和支付宝支付](https://packagist.org/packages/yansongda/pay)
- [二维码生成](https://packagist.org/packages/bacon/bacon-qr-code)
- [条形码生成]( https://packagist.org/packages/milon/barcode)
- [ 助手类topthink/think-helper](https://packagist.org/packages/topthink/think-helper)
- [文件下载](https://packagist.org/packages/jkuchar/filedownloader)
- [图片处理topthink/think-image](https://packagist.org/packages/topthink/think-image)
- [input验证（laravel）]( https://packagist.org/packages/illuminate/validation)
- [input验证 topthink/think-validate](https://packagist.org/packages/topthink/think-validate)
- [日志记录 monolog](https://packagist.org/packages/monolog/monolog) 


## [自动加载](http://docs.phpcomposer.com/01-basic-usage.html#Autoloading)


autoload提供了一些自动加载的方案，更改完该内容以后就composer update一下；

```
"autoload": {
    "psr-4": {
        "App\\": "app/" 
    },
  "files": [
    "app/Tool/Tool.php"  //files一般用来做函数库加载的。
  ]
},
"autoload-dev": {
    "classmap": [   # 直接 new \ClassName 来使用；
        "tests/",   # 如果此处是目录，那此目录下新增加了类文件，也要update一下，内部是循环加载进vendor/composer/autoload_classmap.php文件中；
        "database/"，
        "SomeClass.php"
    ]
}
```

## 其他技巧

1\. 仅更新单个库

```php
composer update foo/bar
```

2\. 如果composer.json仅仅是增加了点描述，这个时候我们可以执行 `update nothing` 来更新composer.lock。

3\. 不编辑composer.json的情况下安装库

```
composer require "foo/bar:1.0.0"
```

4\. 自动克隆仓库，并检出指定的版本

```
composer create-project doctrine/orm path 2.2.0
```

5\. 考虑缓存，dist包优先


6\. 考虑修改，源代码优先

## 其它参考

https://lvwenhan.com/tag/Composer/page/2


Composer 项目官方：http://getcomposer.org

Composer Github项目：https://github.com/composer/composer



  [1]: https://pkg.phpcomposer.com/#how-to-install-composer
  [2]: https://pkg.phpcomposer.com/