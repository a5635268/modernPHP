# PHP内置的http服务器 [转]

标签（空格分隔）： PHP

---

PHP 5.4起就在CLI SAPI中内置了web服务器,但只是提供开发测试使用，不推荐使用中生产环境中。因为这个服务器接受处理请求时顺序执行的，不能并发处理。

这个内置的web服务器使用起来非常的方便，你只需要执行下面的命令：

## 启动Web服务器

```
$ php -S localhost:8000
```

然后就可以访问了。这样启动后，默认的web服务目录是执行命令的当前目录，如果不想使用当前目录，你需要使用 -t 参数来指定。

## 启动web服务器时指定文档的根目录

```
php -S localhost:8000 -t foo/
```

## 使用路由器脚本

在这个例子中，对图片的请求会返回相应的图片，但对HTML文件的请求会显示“Welcome to PHP”：

```php
// router.php
if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
} else {
    echo "<p>Welcome to PHP</p>";
}
```

```
$ php -S localhost:8000 router.php
```

## 判断是否是在使用内置web服务器

通过程序判断来调整同一个PHP路由器脚本在内置Web服务器中和在生产服务器中的不同行为:

```php
// router.php
if (php_sapi_name() == 'cli-server') {
/* route static assets and return false */
}
/* go on with normal index.php operations */
```

```
$ php -S localhost:8000 router.php
```

这个内置的web服务器能识别一些标准的MIME类型资源，它们的扩展有：.css, .gif, .htm, .html, .jpe, .jpeg, .jpg, .js, .png, .svg, and .txt。对.htm 和 .svg 扩展到支持是在PHP 5.4.4之后才支持的。

## 处理不支持的文件类型

如果你希望这个Web服务器能够正确的处理不被支持的MIME文件类型，这样做：

```php
// router.php
$path = pathinfo($_SERVER["SCRIPT_FILENAME"]);
if ($path["extension"] == "ogg") {
header("Content-Type: video/ogg");
readfile($_SERVER["SCRIPT_FILENAME"]);
}
else {
return FALSE;
}
```

```
$ php -S localhost:8000 router.php
```

## 远程访问这个内置Web服务器

如果你希望能远程的访问这个内置的web服务器，你的启动命令需要改成下面这样:

```
$ php -S 0.0.0.0:8000
```

这样你就可以通过 8000 端口远程的访问这个内置的web服务器了

