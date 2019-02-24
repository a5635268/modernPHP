# php7概述


[TOC]



* * * * *


## PHP7性能

7最大的亮点，应该就是性能提高了两倍，某些测试环境下甚至提高到三到五倍，具体可以了解以下链接：

[PHP7 VS HHVM (WordPress)](http://www.laruence.com/2014/12/18/2976.html)

[HHVM vs PHP 7 – The Competition Gets Closer! ](https://kinsta.com/blog/hhvm-vs-php-7/)

[PHP 7.0 Is Showing Very Promising Performance Over PHP 5, Closing Gap With HHVM](https://www.phoronix.com/scan.php?page=article&item=php-70-rc2&num=1)

[PHP7革新与性能优化](https://www.csdn.net/article/2015-09-16/2825720)


## 特性简述

### 标量类型声明

PHP 7 中的函数的形参类型声明可以是标量了。在 PHP 5 中只能是类名、接口、array 或者 callable (PHP 5.4，即可以是函数，包括匿名函数)，**现在也可以使用 string、int、float和 bool 了**。

```php
<?php

// 强制模式，强制把参数int化
function sumOfInts(int ...$ints)
{
    return array_sum($ints);
}

var_dump(sumOfInts(2, '3', 4.1));
```
> 强制模式（默认，既强制类型转换）下会对不符合预期的参数进行强制类型转换，但严格模式下则触发 **TypeError 的致命错误。**
> 严格模式：申明 declare(strict_types=1)即可; 


### 返回值类型声明

PHP 7 增加了对返回类型声明的支持。 类似于参数类型声明，返回类型声明指明了函数返回值的类型。可用的类型与参数声明中可用的类型相同。

```php

<?php

function arraysSum(array ...$arrays): array
{
   # 把返回值强制转换为string
    return array_map(function(array $array): string {
        return array_sum($array);
    }, $arrays);
}

var_dump(arraysSum([1,2,3], [4,5,6], [7,8,9]));

# output
array(3) {
  [0]=>
  string(1) "6"
  [1]=>
  string(2) "15"
  [2]=>
  string(2) "24"
}
```

> 同样有严格模式和强制模式


### NULL 合并运算符

```php

// 如果 $_GET['user'] 不存在返回 'nobody'，否则返回 $_GET['user'] 的值
$username = $_GET['user'] ?? 'nobody';

// 相当于
isset($_GET['user']) ?  $_GET['user'] : 'nobody';

// 类似于屏蔽notice错误后的：
$username = $_GET['user'] ?: 'nobody';
```

### 太空船操作符（组合比较符）


用于比较两个表达式。当$a大于、等于或小于$b时它分别返回-1、0或1。


```php

<?php
// $a 是否大于 $b , 大于就返回1
// 整型
echo 1 <=> 1; // 0
echo 1 <=> 2; // -1
echo 2 <=> 1; // 1

// 浮点型
echo 1.5 <=> 1.5; // 0
echo 1.5 <=> 2.5; // -1
echo 2.5 <=> 1.5; // 1

// 字符串
echo "a" <=> "a"; // 0
echo "a" <=> "b"; // -1
echo "b" <=> "a"; // 1
```


### 通过 define() 定义常量数组

```php

<?php

define('ANIMALS', [
    'dog',
    'cat',
    'bird'
]);

// ANIMALS[1] = mouse; 常量数组里面的值，是不可以更改的

echo ANIMALS[1]; // 输出 "cat"
```

### 匿名类

现在支持通过new class 来实例化一个匿名类

```php
interface Logger {
    public function log(string $msg);
}

class Application {
    private $logger;

    public function getLogger(): Logger {
        return $this->logger;
    }

    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }
}

$app = new Application;
$app->setLogger(new class implements Logger {
    public function log(string $msg) {
        echo $msg;
    }
});

var_dump($app->getLogger());

# output:
object(class@anonymous)#2 (0) {
}
```

### 为unserialize()提供过滤

这个特性旨在提供更安全的方式解包不可靠的数据。它通过白名单的方式来防止潜在的代码注入。

```php
// 转换对象为 __PHP_Incomplete_Class 对象
$data = unserialize($foo, ["allowed_classes" => false]);

// 转换对象为 __PHP_Incomplete_Class 对象，除了 MyClass 和 MyClass2
$data = unserialize($foo, ["allowed_classes" => ["MyClass", "MyClass2"]]);

// 默认接受所有类
$data = unserialize($foo, ["allowed_classes" => true]);
```

### 断言assert

向后兼用并增强之前的 assert() 的方法。 它使得在生产环境中启用断言为零成本，并且提供当断言失败时抛出特定异常的能力。

```php
ini_set('assert.exception', 1);

class CustomError extends AssertionError {}
assert(2 == 1, new CustomError('Some error message'));
```

### use 加强

从同一 namespace 导入的类、函数和常量现在可以通过单个 use 语句 一次性导入了。


```php
//  PHP 7 之前版本用法
use some\namespace\ClassA;
use some\namespace\ClassB;
use some\namespace\ClassC as C;

use function some\namespace\fn_a;
use function some\namespace\fn_b;
use function some\namespace\fn_c;

use const some\namespace\ConstA;
use const some\namespace\ConstB;
use const some\namespace\ConstC;

// PHP 7+ 用法
use some\namespace\{ClassA, ClassB, ClassC as C};
use function some\namespace\{fn_a, fn_b, fn_c};
use const some\namespace\{ConstA, ConstB, ConstC};
```

### Generator 加强 ： yield from

增强了Generator的功能，这个可以实现很多先进的特性

```php
function gen()
{
    yield 1;
    yield 2;

    yield from gen2();
}

function gen2()
{
    yield 3;
    yield 4;
}

foreach (gen() as $val)
{
    echo $val, PHP_EOL;
}
```

### 整除

新增了整除函数 intdiv()

```php
var_dump(intdiv(10, 3)); # 3
```

### array_column() 和 list()

新增支持对象数组

```php
list($a, $b) = (object) new ArrayObject([0, 1]);
```

> PHP7结果：$a == 0 and $b == 1.
> PHP5结果：$a == null and $b == null.

### dirname()

增加了可选项$levels，可以用来指定目录的层级。dirname(dirname($foo)) => dirname($foo, 2);

### Closure::call()

Closure::call() 现在有着更好的性能，简短干练的**暂时绑定一个方法到对象上闭包并调用它。**

```php
class A {private $x = 1;}

// Pre PHP 7 代码
$getXCB = function() {return $this->x;};
$getX = $getXCB->bindTo(new A, 'A'); 
echo $getX();

//// PHP 7+ 代码
$getX = function() {return $this->x;};
echo $getX->call(new A);
```

### Unicode codepoint 转译语法

```php
echo "\u{aa}";
echo "\u{0000aa}";
echo "\u{9999}";
```

更多请参考：

[关于PHP7新特性](http://www.php7.site/book/php7/about-30.html)