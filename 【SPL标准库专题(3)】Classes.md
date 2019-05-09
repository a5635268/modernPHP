我把SPL分为五个部分：Iterator，Classes，Exceptions，Datastructures，Function；而其中classes是就是做一些类的介绍（Iterator与Datastructures相关的类在各自文章内），在介绍这些类之前，先介绍几个接口：

## ArrayAccess（数组式访问）接口

http://php.net/manual/zh/class.arrayaccess.php#class.arrayaccess

只要实现了这个接口，就可以使得object像array那样操作。ArrayAccess界面包含四个必须部署的方法，这四个方法分别传入的是array的key和value,：

    * offsetExists($offset)
    This method is used to tell php if there is a value for the key specified by offset. It should return true or false.检查一个偏移位置是否存在
    
    * offsetGet($offset)
    This method is used to return the value specified by the key offset.获取一个偏移位置的值
    
    * offsetSet($offset, $value)
    This method is used to set a value within the object, you can throw an exception from this function for a read-only collection. 获取一个偏移位置的值
    
    * offsetUnset($offset)
    This method is used when a value is removed from an array either through unset() or assigning the key a value of null. In the case of numerical arrays, this offset should not be deleted and the array should not be reindexed unless that is specifically the behavior you want. 复位一个偏移位置的值

```php

/**
 * A class that can be used like an array
 */
class Article implements ArrayAccess{
  public $title;
  public $author;
  public $category;

  function __construct($title , $author , $category){
    $this->title = $title;
    $this->author = $author;
    $this->category = $category;
  }

  /**
   * Defined by ArrayAccess interface
   * Set a value given it's key e.g. $A['title'] = 'foo';
   * @param mixed key (string or integer)
   * @param mixed value
   * @return void
   */
  function offsetSet($key , $value){
    if (array_key_exists($key , get_object_vars($this))) {
      $this->{$key} = $value;
    }
  }

  /**
   * Defined by ArrayAccess interface
   * Return a value given it's key e.g. echo $A['title'];
   * @param mixed key (string or integer)
   * @return mixed value
   */
  function offsetGet($key){
    if (array_key_exists($key , get_object_vars($this))) {
      return $this->{$key};
    }
  }

  /**
   * Defined by ArrayAccess interface
   * Unset a value by it's key e.g. unset($A['title']);
   * @param mixed key (string or integer)
   * @return void
   */
  function offsetUnset($key){
    if (array_key_exists($key , get_object_vars($this))) {
      unset($this->{$key});
    }
  }

  /**
   * Defined by ArrayAccess interface
   * Check value exists, given it's key e.g. isset($A['title'])
   * @param mixed key (string or integer)
   * @return boolean
   */
  function offsetExists($offset){
    return array_key_exists($offset , get_object_vars($this));
  }
}

// Create the object
$A = new Article('SPL Rocks','Joe Bloggs', 'PHP');

// Check what it looks like
echo 'Initial State:<div>';
print_r($A);
echo '</div>';

// Change the title using array syntax
$A['title'] = 'SPL _really_ rocks';

// Try setting a non existent property (ignored)
$A['not found'] = 1;

// Unset the author field
unset($A['author']);

// Check what it looks like again
echo 'Final State:<div>';
print_r($A);
echo '</div>';
```

## Serializable接口

**接口摘要**

```php
Serializable {
/* 方法 */
abstract public string serialize ( void )
abstract public mixed unserialize ( string $serialized )
}
```

具体参考： http://php.net/manual/zh/class.serializable.php

简单的说，当实现了Serializable接口的类，被实例化后的对象，在序列化或者反序列化时都会自动调用类中对应的序列化或者反序列化方法；

```php
class obj implements Serializable {
  private $data;
  public function __construct() {
    $this->data = "自动调用了方法:";
  }
  public function serialize() {
    $res = $this->data.__FUNCTION__;
    return serialize($res);
  }

  //然后上面serialize的值作为$data参数传了进来;
  public function unserialize($data) {
    $this->data = unserialize($res);
  }
  public function getData() {
    return $this->data;
  }
}

$obj = new obj;
$ser = serialize($obj);
$newobj = unserialize($ser);

//在调用getData方法之前其实隐式又调用了serialize与unserialize
var_dump($newobj->getData());
```

## IteratorAggregate（聚合式迭代器）接口

**类摘要**

```php
IteratorAggregate extends Traversable {
    /* 方法 */
    abstract public Traversable getIterator ( void )
}
```

> Traversable作用为检测一个类是否可以使用 foreach 进行遍历的接口，在php代码中不能用。只有内部的PHP类（用C写的类）才可以直接实现Traversable接口；php代码中使用Iterator或IteratorAggregate接口来实现遍历。

实现了此接口的类，内部都有一个getIterator方法来获取迭代器实例；

```php
class myData implements IteratorAggregate {

  private $array = [];
  const TYPE_INDEXED = 1;
  const TYPE_ASSOCIATIVE = 2;

  public function __construct( array $data, $type = self::TYPE_INDEXED ) {
    reset($data);
    while( list($k, $v) = each($data) ) {
      $type == self::TYPE_INDEXED ?
        $this->array[] = $v :
        $this->array[$k] = $v;
    }
  }

  public function getIterator() {
    return new ArrayIterator($this->array);
  }

}

$obj = new myData(['one'=>'php','javascript','three'=>'c#','java',]/*,TYPE 1 or 2*/ );

//↓↓ 遍历的时候其实就是遍历getIterator中实例的迭代器对象，要迭代的数据为这里面传进去的数据
foreach($obj as $key => $value) {
  var_dump($key, $value);
  echo PHP_EOL;
}
```

## Countable 接口

类实现 Countable接口后，在count时以接口中返回的值为准. 

```php
//Example One, BAD :(
class CountMe{
  protected $_myCount = 3;

  public function count(){
    return $this->_myCount;
  }
}

$countable = new CountMe();
echo count($countable); //result is "1", not as expected

//Example Two, GOOD :)
class CountMe implements Countable{
  protected $_myCount = 3;

  public function count(){
    return $this->_myCount;
  }
}

$countable = new CountMe();
echo count($countable); //result is "3" as expected 
```

## ArrayObject类

简单的说该类可以使得像操作Object那样操作Array；

> 这是一个很有用的类；

```php
/*** a simple array ***/
$array = array('koala', 'kangaroo', 'wombat', 'wallaby', 'emu', 'kiwi', 'kookaburra', 'platypus');

/*** create the array object ***/
$arrayObj = new ArrayObject($array);

//增加一个元素
$arrayObj->append('dingo');

//显示元素的数量
//echo $arrayObj->count();

//对元素排序: 大小写不敏感的自然排序法，其他排序法可以参考手册
$arrayObj->natcasesort();

//传入其元素索引，从而删除一个元素
$arrayObj->offsetUnset(5);

//传入某一元素索引，检测某一个元素是否存在
if ($arrayObj->offsetExists(5))
{
  echo 'Offset Exists<br />';
}

//更改某个元素的值
$arrayObj->offsetSet(3, "pater");

//显示某一元素的值
//echo $arrayObj->offsetGet(4);

//更换数组，更换后就以此数组为对象
$fruits = array("lemons" => 1, "oranges" => 4, "bananas" => 5, "apples" => 10);
$arrayObj->exchangeArray($fruits);

// Creates a copy of the ArrayObject.
$copy = $fruitsArrayObject->getArrayCopy();

/*** iterate over the array ***/
for($iterator = $arrayObj->getIterator();
  /*** check if valid ***/
    $iterator->valid();
  /*** move to the next array member ***/
    $iterator->next())
{
  /*** output the key and current array value ***/
  echo $iterator->key() . ' => ' . $iterator->current() . '<br />';
}
```

## SplObserver, SplSubject 

这是两个专用于设计模式中观察者模式的类，会在后面的设计模式专题中详细介绍；

## SplFileInfo 

简单的说，该对象就是把一些常用的文件信息函数进行了封装，比如获取文件所属，权限，时间等等信息，具体参考：
http://php.net/manual/zh/class.splfileinfo.php

## SplFileObject 
SplFileObject类为操作文件提供了一个面向对象接口. 具体参考：http://php.net/manual/zh/class.splfileobject.php

```php
SplFileObject extends SplFileInfo implements RecursiveIterator , SeekableIterator {}
```




