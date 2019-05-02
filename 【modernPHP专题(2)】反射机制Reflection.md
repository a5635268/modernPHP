## 简介

PHP Reflection API是PHP5才有的新功能，它是用来导出或提取出关于类、方法、属性、参数等的详细信息，包括注释。

```php
class Reflection { } 
interface Reflector { }
class ReflectionException extends Exception { }
class ReflectionFunction implements Reflector { }
class ReflectionParameter implements Reflector { }
class ReflectionMethod extends ReflectionFunction { }
class ReflectionClass implements Reflector { }
class ReflectionObject extends ReflectionClass { }
class ReflectionProperty implements Reflector { }
class ReflectionExtension implements Reflector { } 
```

用得比较多的就只有两个`ReflectionClass`与`ReflectionObject`，两个的用法都一样，只是前者针对类，后者针对对象，后者是继承前者的类；然后其中又有一些属性或方法能返回对应的`Reflection`对象（ReflectionProperty以及ReflectionMethod）

### ReflectionClass

具体参考手册：http://php.net/manual/zh/class.reflectionclass.php

通过ReflectionClass，我们可以得到Person类的以下信息：

- 常量 Contants
- 属性 Property Names
- 方法 Method Names
- 静态属性 Static Properties
- 命名空间 Namespace
- Person类是否为final或者abstract

```
<?php
namespace app;

class Person{
  /**
   * For the sake of demonstration, we"re setting this private
   */
  private $_allowDynamicAttributes = false;

  /** type=primary_autoincrement */
  protected $id = 0;

  /** type=varchar length=255 null */
  protected $name;

  /** type=text null */
  protected $biography;

  public function getId(){
    return $this->id;
  }

  public function setId($v){
    $this->id = $v;
  }

  public function getName(){
    return $this->name;
  }

  public function setName($v){
    $this->name = $v;
  }

  public function getBiography(){
    return $this->biography;
  }

  public function setBiography($v){
    $this->biography = $v;
  }
}

//传递类名或对象进来
$class = new \ReflectionClass('app\Person');

//获取属性，不管该属性是否public
$properties = $class->getProperties();
foreach($properties as $property) {
  echo $property->getName()."\n";
}
// 输出:
// _allowDynamicAttributes
// id
// name
// biography

//默认情况下，ReflectionClass会获取到所有的属性，private 和 protected的也可以。如果只想获取到private属性，就要额外传个参数：
/*
 * ReflectionProperty::IS_STATIC
 * ReflectionProperty::IS_PUBLIC
 * ReflectionProperty::IS_PROTECTED
 * ReflectionProperty::IS_PRIVATE
 */
 
 //↓↓ 注意一个|组合： 获得IS_PRIVATE或者IS_PROTECTED的属性
$private_properties = $class->getProperties(\ReflectionProperty::IS_PRIVATE|\ReflectionProperty::IS_PROTECTED);

foreach($private_properties as $property) {
  //↓↓如果该属性是受保护的属性；
  if($property->isProtected()) {

    // ↓↓ 获取注释
    $docblock = $property->getDocComment();
    preg_match('/ type\=([a-z_]*) /', $property->getDocComment(), $matches);
    echo $matches[1]."\n";
  }
}
// Output:
// primary_autoincrement
// varchar
// text

$data = array("id" => 1, "name" => "Chris", "biography" => "I am am a PHP developer");
foreach($data as $key => $value) {
  if(!$class->hasProperty($key)) {
    throw new \Exception($key." is not a valid property");
  }

  if(!$class->hasMethod("get".ucfirst($key))) {
    throw new \Exception($key." is missing a getter");
  }

  if(!$class->hasMethod("set".ucfirst($key))) {
    throw new \Exception($key." is missing a setter");
  }

  $object = new Person();

  // http://php.net/manual/zh/class.reflectionmethod.php
  // getMethod 获得一个该方法的reflectionmethod对象，然后使用里面的invoke方法；
  $setter = $class->getMethod("set".ucfirst($key));
  $ok = $setter->invoke($object, $value);

  // Get the setter method and invoke it
  $getter = $class->getMethod("get".ucfirst($key));
  $objValue = $getter->invoke($object);

  // Now compare
  if($value == $objValue) {
    echo "Getter or Setter has modified the data.\n";
  } else {
    echo "Getter and Setter does not modify the data.\n";
  }
}
```


#### getMethod and invoke

`ReflectionClass::getMethod` — 获取一个类方法的 **ReflectionMethod**(可以理解为获得这个类方法的控制权，不管这个类方法是否是public)。

具体的参考： 

- http://php.net/manual/zh/class.reflectionmethod.php
- http://php.net/manual/zh/reflectionmethod.invoke.php

```php
<?php
class HelloWorld {
  private function sayHelloTo($name,$arg1,$arg2) {
    return 'Hello ' . $name.' '.$arg1.' '.$arg2;
  }
}

$obj = new HelloWorld();
// 第一个参数可以是对象,也可以是类
$reflectionMethod = new ReflectionMethod($obj , 'sayHelloTo');
if(!$reflectionMethod -> isPublic()){
  $reflectionMethod -> setAccessible(true);
}
/*
 * public mixed ReflectionMethod::invoke ( object $object [, mixed $parameter [, mixed $... ]] )
 * 1. 获得某个类方法的ReflectionMethod
 * 2. $object 该方法所在的类实例的对象，然后第二参数起对号入座到该方法的每个参数；
 * 3. 通过invoke就可以执行这个方法了
 */
echo $reflectionMethod->invoke($obj, 'GangGe','How','are you');

//也可以把参数作为数组传进来
echo $reflectionMethod -> invokeArgs($obj,array('GangGe','How','are you'));
```

#### getProperty 

获得一个 `ReflectionProperty` 类实例 (同上，获得该属性的控制权) http://cn2.php.net/manual/zh/class.reflectionproperty.php

##### getValue获取属性值

```
public mixed ReflectionProperty::getValue ([ object $object ] )
```
如果该获得该实例的类属性**不是一个static的属性，就必须传该类的实例**

```php
<?php
class Foo {
  public static $staticProperty = 'foobar';
  public $property = 'barfoo';
  protected $privateProperty = 'foofoo';
}
$reflectionClass = new ReflectionClass('Foo');
var_dump($reflectionClass->getProperty('staticProperty')->getValue()); //静态属性可以不加参数
var_dump($reflectionClass->getProperty('property')->getValue(new Foo)); //非静态属性必须加传一个类实例
$reflectionProperty = $reflectionClass->getProperty('privateProperty'); //受保护的属性就要通过setAccessible获得其权限
$reflectionProperty->setAccessible(true);
var_dump($reflectionProperty->getValue(new Foo));
```

### Example

#### 模拟YII框架中控制器调用方法的实现

```php
<?php

if (PHP_SAPI != 'cli') {
    exit('Please run it in terminal!');
}
if ($argc < 3) {
    exit('At least 2 arguments needed!');
}
 
$controller = ucfirst($argv[1]) . 'Controller';
$action = 'action' . ucfirst($argv[2]);
 
// 检查类是否存在
if (!class_exists($controller)) {
    exit("Class $controller does not existed!");
}
 
// 获取类的反射
$reflector = new ReflectionClass($controller);
// 检查方法是否存在
if (!$reflector->hasMethod($action)) {
    exit("Method $action does not existed!");
}
 
// 取类的构造函数，返回的是ReflectionMethod对象
$constructor = $reflector->getConstructor();

// 取构造函数的参数，这是一个对象数组
$parameters = $constructor->getParameters();

// 遍历参数
foreach ($parameters as $key => $parameter) {
    // 获取参数声明的类
    $injector = new ReflectionClass($parameter->getClass()->name);
    // 实例化参数声明类并填入参数列表
    $parameters[$key] = $injector->newInstance(); //实例化$parameter->getClass()->name类
}
 
// 使用参数列表实例 controller 类
$instance = $reflector->newInstanceArgs($parameters);
// 执行
$instance->$action();
 
class HelloController
{
    private $model;
 
    public function __construct(TestModel $model)
    {
        $this->model = $model;
    }
 
    public function actionWorld()
    {
        echo $this->model->property, PHP_EOL;
    }
}
 
class TestModel
{
    public $property = 'property';
}
```

#### TP框架中实现前后控制器

```php
<?php
class BlogAction {

	public function detail() {
		echo 'detail' . "\r\n";
	}

	public function test($year = 2014, $month = 4, $day = 21) {
		echo $year . '--' . $month . '--' . $day . "\r\n";
	}

	public function _before_detail() {
		echo __FUNCTION__ . "\r\n";
	}

	public function _after_detail() {
		echo __FUNCTION__ . "\r\n";
	}
}

// 执行detail方法
$method = new ReflectionMethod('BlogAction', 'detail');
$instance = new BlogAction();

// 进行权限判断
if ($method->isPublic()) {

	$class = new ReflectionClass('BlogAction');

	// 执行前置方法
	if ($class->hasMethod('_before_detail')) {
		$beforeMethod = $class->getMethod('_before_detail');
		if ($beforeMethod->isPublic()) {
			$beforeMethod->invoke($instance);
		}
	}

	$method->invoke(new BlogAction);

	// 执行后置方法
	if ($class->hasMethod('_after_detail')) {
		$beforeMethod = $class->getMethod('_after_detail');
		if ($beforeMethod->isPublic()) {
			$beforeMethod->invoke($instance);
		}
	}
}

// 执行带参数的方法
$method = new ReflectionMethod('BlogAction', 'test');
$params = $method->getParameters();
foreach ($params as $param) {
	$paramName = $param->getName();
	if (isset($_REQUEST[$paramName])) {
		$args[] = $_REQUEST[$paramName];
	} elseif ($param->isDefaultValueAvailable()) {
		$args[] = $param->getDefaultValue();
	}
}

if (count($args) == $method->getNumberOfParameters()) {
	$method->invokeArgs($instance, $args);
} else {
	echo 'parameters is wrong!';
}
```

#### 其他参考

```php
/**
 * 执行App控制器
 */
public function execApp() {

	// 创建action控制器实例
	$className = MODULE_NAME . 'Controller';
	$namespaceClassName = '\\apps\\' . APP_NAME . '\\controller\\' . $className;
	load_class($namespaceClassName, false);

	if (!class_exists($namespaceClassName)) {
		throw new \Exception('Oops! Module not found : ' . $namespaceClassName);
	}

	$controller = new $namespaceClassName();

	// 获取当前操作名
	$action = ACTION_NAME;

	// 执行当前操作
	//call_user_func(array(&$controller, $action)); // 其实吧，用这个函数足够啦！！！
	try {
		$methodInfo = new \ReflectionMethod($namespaceClassName, $action);
		if ($methodInfo->isPublic() && !$methodInfo->isStatic()) {
			$methodInfo->invoke($controller);
		} else { // 操作方法不是public类型，抛出异常
			throw new \ReflectionException();
		}
	} catch (\ReflectionException $e) {
		// 方法调用发生异常后，引导到__call方法处理
		$methodInfo = new \ReflectionMethod($namespaceClassName, '__call');
		$methodInfo->invokeArgs($controller, array($action, ''));
	}
	return;
}
```