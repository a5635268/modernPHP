> 依赖倒置和控制反转是一种编程思想，而依赖注入就是通过服务容器实现这种面向接口或者是面向抽象编程的思想


## 概念理解

### 依赖倒置原则

依赖倒置是一种软件设计思想，在传统软件中，上层代码依赖于下层代码，当下层代码有所改动时，上层代码也要相应进行改动，因此维护成本较高。而依赖倒置原则的思想是，**上层不应该依赖下层，应依赖接口**。意为上层代码定义接口，下层代码实现该接口，从而使得下层依赖于上层接口，降低耦合度，提高系统弹性


### 控制反转

当调用者需要被调用者的协助时，在传统的程序设计过程中，通常由调用者来创建被调用者的实例，但在这里，创建被调用者实例的工作不再由调用者来完成，而是将被调用者的创建移到调用者的外部，从而反转被调用者的创建，消除了调用者对被调用者创建的控制，因此称为控制反转。

要实现控制反转，通常的解决方案是将创建被调用者实例的工作交由 IoC 容器来完成，然后在调用者中注入被调用者（通过构造器/方法注入实现），这样我们就实现了调用者与被调用者的解耦，该过程被称为依赖注入。

依赖注入不是目的，它是一系列工具和手段，最终的目的是帮助我们开发出松散耦合（loose coupled）、可维护、可测试的代码和程序。这条原则的做法是大家熟知的**面向接口，或者说是面向抽象编程**。

> 通俗的说，在调用一个对象的方法，首先要实例化对象之后。 而所谓的注入，就是一种工厂模式的升华。由一个更高级的工厂（容器），来完成对象实例化，实现调用者与被调用者的解耦


## 解决什么问题

### 实现调用者与被调用者的解耦

>[info] 所谓的上层代码依赖于**接口**，就是业务逻辑的实现是跳过了具体对象的抽象行为。比如我们要对用户发消息，可以通过邮件发送，也可以通过短信发送。上层代码不用关注其用什么发送，只发送即可（适配器模式）


```PHP
interface Mail
{
    public function send();
}

class Email implements Mail
{
    public function send()
    {
        echo '发送邮件' . PHP_EOL;
    }
}

class SmsMail implements Mail
{
    public function send()
    {
        echo '发送短信' . PHP_EOL;
    }
}

// 注册容器
class Register
{
    private $_mailObj;

    // 构造函数里面已经约束了必须是实现了Mail接口的类的实例
    public function __construct(Mail $mailObj)
    {
        $this->_mailObj = $mailObj;
    }

    public function doRegister()
    {
        // 一定会有send方法
        $this->_mailObj->send();//发送信息
    }
}


$emailObj = new Email();
$smsObj = new SmsMail();

$reg = new Register($emailObj);
$reg->doRegister();//使用email发送

$reg = new Register($smsObj);
$reg->doRegister($smsObj);//使用短信发送
```

使用构造函数注入的方法，使得它只依赖于发送短信的接口，只要实现其接口中的'send'方法，不管你什么方式发送都可以。上面通过构造函数注入对象的方式，就是最简单的依赖注入；当然"注入"不仅可以通过构造函数注入，也可以通过属性注入，上面你可以通过一个"setter"来动态为"mailObj"这个属性赋值。


### 通过php反射机制实现自动注入

真实的dependency injection container会提供更多的特性，如

>[info] 1. 自动绑定（Autowiring）或 自动解析（Automatic Resolution）
> 2. 注释解析器（Annotations）
> 3. 延迟注入（Lazy injection）

```php
<?php

class C
{
    public function doSomething()
    {
        echo __METHOD__ , '我是周伯通C|';
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
        echo __METHOD__ , '我是周伯通B|';
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
        echo __METHOD__ , '我是周伯通A|';;
    }
}

class Container
{
    private $s = [];

    public function __set($k , $c)
    {
        $this->s[$k] = $c;
    }

    public function __get($k)
    {
        return $this->build($this->s[$k]);
    }

    /**
     * 自动绑定（Autowiring）自动解析（Automatic Resolution）
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

        if(!class_exists($className)){
            throw new Exception("{$className} class is not exists");
        }

        /** @var ReflectionClass $reflector */
        $reflector = new ReflectionClass($className);

        // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if (!$reflector->isInstantiable()) {
            throw new Exception("Can't instantiate this.");
        }

        /** @var ReflectionMethod $constructor 获取类的构造函数 */
        $constructor = $reflector->getConstructor();

        // 若无构造函数，直接实例化并返回, （注意！ 此处退出递归1）
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
                // 是变量,有默认值则设置默认值 （注意，此处退出递归2）
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


/*// example1
$container = new Container();
$container->b = 'B';
$container->a = function ($container){
    return new A($container->b);
};

// 从容器中取得A
$model = $container->a;
// output: C::doSomething我是周伯通C|B::doSomething我是周伯通B|A::doSomething我是周伯通A|
// 实现依赖自动注入
$model->doSomething();*/


// example2
$di = new Container();
$di->php7 = 'A'; // 自动注入classA
/** @var A $php7 */
$foo = $di->php7;

$foo->doSomething(); //C::doSomething我是周伯通C|B::doSomething我是周伯通B|A::doSomething我是周伯通A|
```

参考：

https://www.cnblogs.com/painsOnline/p/5138806.html
https://www.cnblogs.com/phpper/p/7781810.html

