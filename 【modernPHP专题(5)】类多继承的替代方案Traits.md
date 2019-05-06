## 概述

traits是PHP5.4新进入的特性，其目的就是解决PHP的类不能多继承的问题。**Traits不是类！不能被实例化。**可以理解为一组能被不同的类都能调用到的方法集合。只需要在类中使用关键词use引入即可，可引入多个Traits，用','隔开。

## 简单使用

```php
trait myTrait{

    public $traitPublic = 'public';
    protected $traitProtected = 'protected';

    function traitMethod1()
    {
        echo __METHOD__,PHP_EOL;
    }

    function traitMethod2()
    {
        echo __METHOD__,PHP_EOL;
    }
}

class myClass{
    use myTrait;
}

$obj = new myClass();
$obj->traitMethod1();
$obj->traitMethod2();

// ↓↓ 只能调用public的属性和方法; protected以及private只供在traits内部自己调用;
echo $obj->traitPublic;
```

## 优先级问题

Trait会覆盖继承的方法，当前类会覆盖Trait方法。即 继承的方法 < Traits方法 < 当前类方法,

```php
trait A{
    public $var1 = 'test';

    public function test()
    {
        echo 'A::test()';
    }

    public function test1()
    {
        echo 'A::test1()';
    }
}

class B{
    public function test()
    {
        echo 'B::test()';
    }

    public function test1()
    {
        echo 'B::test1()';
    }
}

class C extends B{
    use A;

    public function test()
    {
        echo 'c::test()';
    }
}

$c = new C();
$c->test(); //c::test() Traits方法 < 当前类方法
$c->test1(); //A::test1() 继承的方法 < Traits方法
```

## 多个Trait冲突问题

1. 如果两个 trait 都插入了一个同名的方法，如果没有明确解决冲突将会产生一个致命错误。 
2. 为了解决多个 trait 在同一个类中的命名冲突，需要使用 insteadof 操作符来明确指定使用冲突方法中的哪一个。
3. 可用as操作符将其中一个冲突方法另起名；

```php
trait A{
    public function test()
    {
        echo 'A::test()';
    }
}

trait B{
    public function test()
    {
        echo 'B::test()';
    }
}

class C{
    use A , B {
        B::test insteadof A; //明确B替代A
        B::test as t; //或者另起一个名字
    }
}

$c = new C();
$c->test(); //B::test()
$c->t(); //B::test()   可以用as另起名
```

## as可用来修改方法访问控制

```php
trait  HelloWorld{
    public function sayHello()
    {
        echo 'Hello World!';
    }
}

// 修改 sayHello 的访问控制
class  A{
    use  HelloWorld {
        sayHello as protected;
    }
}

// 给方法一个改变了访问控制的别名
// 原版 sayHello 的访问控制则没有发生变化
class  B{
    use  HelloWorld {
        sayHello as private myPrivateHello;
    }
}

$a = new A();
$a->sayHello(); //Fatal error: Call to protected method A::sayHello() from context ''; 改变了sayHello的访问规则;

$b = new B();
$b->sayHello(); //Hello World!
```

## Trait中使用Trait

```php
trait Hello{
    public function sayHello()
    {
        echo 'Hello ';
    }
}

trait World{
    public function sayWorld()
    {
        echo 'World!';
    }
}

trait HelloWorld{
    use Hello , World;
}

class MyHelloWorld{
    use HelloWorld;
}

$o = new MyHelloWorld();
$o->sayHello();
$o->sayWorld(); // Hello World!
```

## Trait中抽象成员

为了对使用的类施加强制要求，trait 支持抽象方法的使用。 

```php
trait Hello{
    public function sayHelloWorld()
    {
        echo 'Hello' . $this->getWorld();
    }

    abstract public function getWorld();
}

class MyHelloWorld{
    private $world;
    use Hello;

    // 必须要实现trait里面的抽象方法,否则Fatal error: Class MyHelloWorld contains 1 abstract method and must therefore be declared abstract or implement the remaining methods
    public function getWorld()
    {
        return $this->world;
    }

    public function setWorld($val)
    {
        $this->world = $val;
    }
}

$obj = new MyHelloWorld();
echo $obj->setWorld();
```

## Trait中静态成员

Traits 可以被静态成员静态方法定义,不可以直接定义静态变量，但静态变量可被trait方法引用.

```php
# 静态属性;
trait Counter {
    public function inc() {
        static $c = 0;
        $c = $c + 1;
        echo "$c\n";
    }
}

class C1 {
    use Counter;
}

class C2 {
    use Counter;
}

$o = new C1();
$o->inc(); // echo 1
$o->inc(); // echo 2;

$p = new C2();
$p->inc(); // echo 1

# 静态方法
trait StaticExample {
    public static function doSomething() {
        echo 'Doing something';
    }
}

class Example {
    use StaticExample;
}

Example::doSomething(); // Doing something
```

## Trait中属性

```php
trait PropertiesTrait{
    public $x = 1;
}

class PropertiesExample{
    use PropertiesTrait;
}

$example = new PropertiesExample;
echo $example->x; // 1
```

如果 trait 定义了一个属性，那类将不能定义同样名称的属性，否则会产生一个错误。如果该属性在类中的定义与在 trait 中的定义兼容（同样的可见性和初始值）则错误的级别是 E_STRICT，否则是一个致命错误。 

```php
trait PropertiesTrait {
    public $same = true;
    public $different = false;
}

class PropertiesExample {
    use PropertiesTrait;
    public $same = true; // Strict Standards
    public $different = true; // 致命错误
}
```

参考链接:

http://www.php.net/manual/zh/language.oop5.traits.php