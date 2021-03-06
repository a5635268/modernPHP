## 类结构

```php

Closure {
    /* 方法 */
    // 用于禁止实例化的构造函数
    __construct ( void )
    
    // 复制一个闭包，绑定指定的$this对象和类作用域。
    public static Closure bind ( Closure $closure , object $newthis [, mixed $newscope = 'static' ] )
   
    // 复制当前闭包对象，绑定指定的$this对象和类作用域。
    public Closure bindTo ( object $newthis [, mixed $newscope = 'static' ] )
}

// 类作用域，可以是对象，也可以是实例名称

```

##  什么是匿名类？ 

先理解以下三个例子

###  例1.  闭包函数都是继承Closure类

```php
class A {
    public static function testA() {
        return function($i) { //返回匿名函数
            return $i+100;
        };
    }
}

function B(Closure $callback)
{
    return $callback(200);
}
$a = B(A::testA());

// A::testA() 返回匿名函数，也就是闭包函数，所有闭包函数都是继承Closure类
// print_r($a); //输出 300
```

###  例2. 将一个匿名函数绑定到一个类中。返回Closure类

```php
class A {
    public $base = 100;
    public function funca()
    {
        echo 2222;
    }
}

class B {
    private $base = 1000;
}

// bind : 复制一个闭包，绑定指定的$this对象和类作用域。
$f = function () {
    return $this->base + 3;
};

print_r($f);
/**
 * $f其实就是一个closure对象
 Closure Object
    (
    )
 */

$a = Closure::bind($f, new A);
print_r($a());//out: 103
print_r($a);
/*
 * out:
    Closure Object
    (
        [this] => A Object
            (
                [base] => 100
            )

    )
 */

// 第三个参数就声明了这个函数的可调用范围（如果该函数要调用private）, 该参数可以是对象实例，也可以是类名
$b = Closure::bind($f, new B, 'B');

print_r($b);
/**
 * out:
Closure Object
    (
    [this] => B Object
        (
            [base:B:private] => 1000
        )
)
 */
print_r($b());//out: 1003
```
## 3.  第二参数为null，代表静态调用static

```php
class A {
    private static $sfoo = 1;
    private $ifoo = 2;
}

// 要调静态的属性，就必须声明static
$cl1 = static function() {
    return A::$sfoo;
};
$cl2 = function() {
    return $this->ifoo;
};

// 第二参数为null，就代表调用static
$bcl1 = Closure::bind($cl1, null, 'A');
$bcl2 = Closure::bind($cl2, new A(), 'A');

// 以closure对象调用静态属性
$bcl3 = $cl1->bindTo(null,'A');

echo $bcl1(), "\n";//输出 1
echo $bcl2(), "\n";//输出 2
echo $bcl3(); // 输出1
```

## 匿名类有什么用？

###  给类动态添加新方法

```php
trait DynamicTrait {
    /**
     * 自动调用类中存在的方法
     */
    public function __call($name, $args) {
        if(is_callable($this->$name)){
            return call_user_func($this->$name, $args);
        }else{
            throw new \RuntimeException("Method {$name} does not exist");
        }
    }
    /**
     * 添加方法
     */
    public function __set($name, $value) {
        $this->$name = is_callable($value)?
            $value->bindTo($this, $this):
            $value;
    }
}
/**
 * 只带属性不带方法动物类
 *
 * @author fantasy
 */
class Animal {
    use DynamicTrait;
    private $dog = '汪汪队';
}
$animal = new Animal;

// 往动物类实例中添加一个方法获取实例的私有属性$dog
$animal->getdog = function() {
    return $this->dog;
};
echo $animal->getdog();//输出 汪汪队
```

### 模板渲染输出

**Template.php**

```php
class Template{
    /**
     * 渲染方法
     *
     * @access public
     * @param obj 信息类
     * @param string 模板文件名
     */
    public function render($context, $tpl){
        $closure = function($tpl){
            ob_start();
            include $tpl;
            return ob_end_flush();
        };
         
        // PHP7： $closure->call($context, $tpl);
        $closure = $closure->bindTo($context, $context);
        $closure($tpl);
    }
}
```

**Article.php**

```php
/**
 * 文章信息类
 */
class Article
{
    private $title = "这是文章标题";
    private $content = "这是文章内容";
}
```

**tpl.php**

```php
···
···
<body>
<h1><?php echo $this->title;?></h1>
<p><?php echo $this->content;?></p>
</body>
···
···
```

**index.php**

```php
function __autoload($class) {
    require_once "$class.php";
}
$template = new Template;

$template->render(new Article, 'tpl.php');
```

## PHP7 新增的call方法

```php
class Value
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}

$three = new Value(3);
$four = new Value(4);

$closure = function ($delta){
    return $this->getValue() + $delta;
};

/**
 * function call ($newThis, ...$parameters)
 * 把$closure绑定到$three，并调用；第二参数起就是闭包的参数
 */
echo $closure->call($three , 3);
echo PHP_EOL;
echo $closure->call($four , 4);
```

