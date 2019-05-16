PHP SPL SplObjectStorage是用来存储一组对象的，特别是当你需要唯一标识对象的时候。
PHP SPL SplObjectStorage类实现了Countable,Iterator,Serializable,ArrayAccess四个接口。可实现统计、迭代、序列化、数组式访问等功能。

## 类摘要

```php
SplObjectStorage implements Countable , Iterator , Serializable , ArrayAccess {
    /* 方法 */
    public void addAll ( SplObjectStorage $storage )
    // ↓↓加入对象
    public void attach ( object $object [, mixed $data = NULL ] )
    // ↓↓检查是否包含指定对象
    public bool contains ( object $object )
    // ↓↓移除对象
    public void detach ( object $object )
    // ↓↓返回一串哈希值，每次调用的时候该串哈希值都在改变
    public string getHash ( object $object )
    public mixed getInfo ( void )
    public int count ( void )
    public object current ( void )
    public int key ( void )
    public void next ( void )
    public bool offsetExists ( object $object )
    public mixed offsetGet ( object $object )
    public void offsetSet ( object $object [, mixed $data = NULL ] )
    public void offsetUnset ( object $object )
    public void removeAll ( SplObjectStorage $storage )
    public void removeAllExcept ( SplObjectStorage $storage )
    public void rewind ( void )
    public string serialize ( void )
    public void setInfo ( mixed $data )
    public void unserialize ( string $serialized )
    public bool valid ( void )
}
```


## Example

```php
# Example1：
class A {
  public $i;
  public function __construct($i) {
    $this->i = $i;
  }
}

$a1 = new A(1);
$a2 = new A(2);
$a3 = new A(3);
$a4 = new A(4);

$container = new SplObjectStorage();

//SplObjectStorage::attach 添加对象到Storage中
$container->attach($a1);
$container->attach($a2);
$container->attach($a3);

//SplObjectStorage::detach 将对象从Storage中移除
$container->detach($a2);

//SplObjectStorage::contains用于检查对象是否存在Storage中
var_dump($container->contains($a1)); //true
var_dump($container->contains($a4)); //false

//遍历
$container->rewind();
while($container->valid()) {
  var_dump($container->current());
  var_dump($container->getInfo());
  $container->next();
}
```

## 参考

http://php.net/manual/zh/class.splobjectstorage.php