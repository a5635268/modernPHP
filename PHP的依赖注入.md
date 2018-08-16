# 依赖倒置，控制反转，服务容器，依赖注入

> 依赖倒置和控制反转是一种编程思想，而依赖注入就是通过服务容器实现这种面向接口或者是面向抽象编程的思想


## 概念理解

### 依赖倒置原则

依赖倒置是一种软件设计思想，在传统软件中，上层代码依赖于下层代码，当下层代码有所改动时，上层代码也要相应进行改动，因此维护成本较高。而依赖倒置原则的思想是，**上层不应该依赖下层，应依赖接口**。意为上层代码定义接口，下层代码实现该接口，从而使得下层依赖于上层接口，降低耦合度，提高系统弹性


### 控制反转

当调用者需要被调用者的协助时，在传统的程序设计过程中，通常由调用者来创建被调用者的实例，但在这里，创建被调用者的工作不再由调用者来完成，而是将被调用者的创建移到调用者的外部，从而反转被调用者的创建，消除了调用者对被调用者创建的控制，因此称为控制反转。

要实现控制反转，通常的解决方案是将创建被调用者实例的工作交由 IoC 容器来完成，然后在调用者中注入被调用者（通过构造器/方法注入实现），这样我们就实现了调用者与被调用者的解耦，该过程被称为依赖注入。

依赖注入不是目的，它是一系列工具和手段，最终的目的是帮助我们开发出松散耦合（loose coupled）、可维护、可测试的代码和程序。这条原则的做法是大家熟知的**面向接口，或者说是面向抽象编程**。

> 通俗的说，在调用一个对象的方法，首先要实例化对象之后。 所谓的注入，就是一种工厂模式的升华。由一个更高级的工厂（容器），来完成对象实例化，实现调用者与被调用者的解耦


## 解决什么问题

>[info] 所谓的上层代码依赖于**接口**，就是业务逻辑的实现是跳过了具体对象的抽象行为。比如把数据存入缓存，我们要实现的是 `set` 这个抽象接口，而不是 `redisSet`或者是`memcacheSet`; 


```PHP
<?php
class C
{
    public function doSomething()
    {
        echo __METHOD__, '我是C类|' , PHP_EOL;
    }
}

class B
{
    public function doSomething()
    {
        // 依赖于C类
        $bim = new C();
        $bim->doSomething();
        echo __METHOD__, '我是B类|' , PHP_EOL;
    }
}

class A
{
    public function doSomething()
    {
        // 依赖于B类
        $bar = new B();
        $bar->doSomething();
        echo __METHOD__, '我是A类|' , PHP_EOL;
    }
}

$class = new A();
$class->doSomething(); //C::doSomething我是C类|B::doSomething我是B类|A::doSomething我是A类|
```

A依赖于B，B依赖于C， 如果有一天更改C，势必影响B和A；而且`$class->doSomething(); `已把所有依赖的业务逻辑耦合在一起了。


## 如何解决


dependency injection container会提供更多的特性，如

>[info] 1. 自动绑定（Autowiring）或 自动解析（Automatic Resolution）
> 2. 注释解析器（Annotations）
> 3. 延迟注入（Lazy injection）
> 


```php
<?php
class C
{
    public function doSomething()
    {
        echo __METHOD__, '我是周伯通C|';
    }
}

class B
{
    private $c;

    public function __construct(C $c)
    {
        $this->c = $c;
    }

    public function doSomething()
    {
        $this->c->doSomething();
        echo __METHOD__, '我是周伯通B|';
    }
}

class A
{
    private $b;

    public function __construct(B $b)
    {
        $this->b = $b;
    }

    public function doSomething()
    {
        $this->b->doSomething();
        echo __METHOD__, '我是周伯通A|';;
    }
}

// 服务容器
class Container
{
    private $s = array();

    public function __set($k, $c)
    {
        $this->s[$k] = $c;
    }

    public function __get($k)
    {
        // return $this->s[$k]($this);
        return $this->build($this->s[$k]);
    }

    /**
     * 自动绑定（Autowiring）自动解析（Automatic Resolution）
     *
     * @param string $className
     * @return object
     * @throws Exception
     */
    public function build($className)
    {
        // 如果是匿名函数（Anonymous functions），也叫闭包函数（closures）
        if ($className instanceof Closure) {
            // 执行闭包函数，并将结果
            return $className($this);
        }

        /** @var ReflectionClass $reflector */
        $reflector = new ReflectionClass($className);

        // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if (!$reflector->isInstantiable()) {
            throw new Exception("Can't instantiate this.");
        }

        /** @var ReflectionMethod $constructor 获取类的构造函数 */
        $constructor = $reflector->getConstructor();

        // 若无构造函数，直接实例化并返回
        if (is_null($constructor)) {
            return new $className;
        }

        // 取构造函数参数,通过 ReflectionParameter 数组返回参数列表
        $parameters = $constructor->getParameters();

        // 递归解析构造函数的参数
        $dependencies = $this->getDependencies($parameters);

        // 创建一个类的新实例，给出的参数将传递到类的构造函数。
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    public function getDependencies($parameters)
    {
        $dependencies = [];

        /** @var ReflectionParameter $parameter */
        foreach ($parameters as $parameter) {
            /** @var ReflectionClass $dependency */
            $dependency = $parameter->getClass();

            if (is_null($dependency)) {
                // 是变量,有默认值则设置默认值
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                // 是一个类，递归解析
                $dependencies[] = $this->build($dependency->name);
            }
        }

        return $dependencies;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws Exception
     */
    public function resolveNonClass($parameter)
    {
        // 有默认值则返回默认值
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new Exception('I have no idea what to do here.');
    }
}

// ----
$class = new Container();
$class->b = 'B';
$class->a = function ($class) {
    return new A($class->b);
};

//// 从容器中取得A
$model = $class->a;
$model->doSomething();

//$di = new Container();
//$di->php7 = 'A';
///** @var A $php7 */
//$foo = $di->php7;
//var_dump($foo);
//
//$foo->doSomething(); //C::doSomething我是周伯通C|B::doSomething我是周伯通B|A::doSomething我是周伯通A|object(A)#10 (1) { ["b":"A":private]=> object(B)#14 (1) { ["c":"B":private]=> object(C)#16 (0) { } } } C::doSomething我是周伯通C|B::doSomething我是周伯通B|A::doSomething我是周伯通A|
```