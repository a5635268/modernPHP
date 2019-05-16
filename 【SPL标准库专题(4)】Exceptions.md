## 嵌套异常

了解SPL异常之前，我们先了解一下嵌套异常。嵌套异常顾名思义就是异常里面再嵌套异常，一个异常抛出，在catch到以后再抛出异常，这时可以通过`Exception`基类的`getPrevious`方法可以获得嵌套异常;

```php
<?php

class DBException extends Exception
{

}

class Database
{
    /**
     * @var PDO object setup during construction
     */
    protected $_pdoResource = null;

    public function executeQuery($sql)
    {
        try {
            throw new PDOException('PDO error');
        } catch (PDOException $e) {
            throw new DBException('Query was unexecutable', null, $e);
        }
        return $numRows;
    }
}

try {
    $sql = 'select * from user';
    $db = new Database('PDO', $connectionParams);
    $db->executeQuery($sql);
} catch (DBException $e) {
    echo 'General Error: ' . $e->getMessage() . "\n";
    // 调用被捕获异常的getPrevious()获得嵌套异常
    $pdoException = $e->getPrevious();
    echo 'PDO Specific error: ' . $pdoException->getMessage() . "\n";
}
```

## SPL异常

简要的说一下SPL异常的优点：

1. 可以为异常抛出提供分类，方便后续有选择性的catch异常；
2. 异常语义化更具体，BadFunctionCallException一看就知道是调用错误的未定义方法抛出的错误；

SPL中有总共13个新的异常类型。其中两个可被视为基类：逻辑异常(LogicException )和运行时异常(RuntimeException);两种都继承php异常类。其余的方法在逻辑上可以被拆分为3组：动态调用组，逻辑组和运行时组。

动态调用组包含异常 BadFunctionCallException和BadMethodCallException，BadMethodCallException是BadFunctionCallException（LogicException的子类）的子类。

```php
// OO variant 
class Foo 
{ 
    public function __call($method, $args) 
    { 
        switch ($method) { 
            case 'doBar': /* ... */ break; 
            default: 
                throw new BadMethodCallException('Method ' . $method . ' is not callable by this object'); 
        } 
    } 
  
} 
  
// procedural variant 
function foo($bar, $baz) { 
    $func = 'do' . $baz; 
    if (!is_callable($func)) { 
        throw new BadFunctionCallException('Function ' . $func . ' is not callable'); 
    } 
} 
```

逻辑（logic ）组包含异常: DomainException、InvalidArgumentException、LengthException、OutOfRangeException组成。

运行时（runtime ）组包含异常:
它由OutOfBoundsException、OverflowException、RangeException、UnderflowException、UnexpectedValueExceptio组成。

```php
class Foo 
{ 
    protected $number = 0; 
    protected $bar = null; 
  
    public function __construct($options) 
    { 
        /** 本方法抛出LogicException异常 **/ 
    } 
      
    public function setNumber($number) 
    { 
        /** 本方法抛出LogicException异常 **/ 
    } 
      
    public function setBar(Bar $bar) 
    { 
        /** 本方法抛出LogicException异常 **/ 
    } 
      
    public function doSomething($differentNumber) 
    { 
        if ($differentNumber != $expectedCondition) { 
            /** 在这里，抛出LogicException异常 **/ 
        } 
          
        /** 
         * 在这里，本方法抛出RuntimeException异常 
         */  
    } 
  
} 
```


## 自定义其它异常

```php
class ThrowableError extends \ErrorException
{
    public function __construct(\Throwable $e)
    {
        // 可以通过instanceof来判断异常分类（做一个映射）
        if ($e instanceof \ParseError) {
            $message  = 'Parse error: ' . $e->getMessage();
            $severity = E_PARSE;
        } elseif ($e instanceof \TypeError) {
            $message  = 'Type error: ' . $e->getMessage();
            $severity = E_RECOVERABLE_ERROR;
        } else {
            $message  = 'Fatal error: ' . $e->getMessage();
            $severity = E_ERROR;
        }
    }
}
```

## 链接参考

http://cn.php.net/manual/zh/class.exception.php
http://cn.php.net/manual/zh/spl.exceptions.php
http://www.oschina.net/translate/exception-best-practices-in-php-5-3

