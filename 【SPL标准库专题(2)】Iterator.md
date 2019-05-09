##  Iterator界面

> 本段内容来自阮一峰老师再加自己的部分注解

SPL规定，所有部署了Iterator界面的class，都可以用在foreach Loop中。Iterator界面中包含5个必须部署的方法：

    * current()
    
    This method returns the current index's value. You are solely
    responsible for tracking what the current index is as the 
    interface does not do this for you. (返回当前索引值)
    
    * key()
    
    This method returns the value of the current index's key. For 
    foreach loops this is extremely important so that the key 
    value can be populated. (返回当前的索引key)
    
    * next()
    
    This method moves the internal index forward one entry. (迭代中的内部指针往前进一步)
    
    * rewind()
    
    This method should reset the internal index to the first element. (重置迭代中的内部指针)
    
    * valid()
    
    This method should return true or false if there is a current 
    element. It is called after rewind() or next(). (验证内部指针是否到最后一行)

**Example**

```
class ArrayReloaded implements Iterator {
  /**
   * 如前一篇文章所说,该类实现了Iterator接口,所以该类对象就是ZEND_ITER_OBJECT,对于ZEND_ITER_OBJECT的类对象,会通过调用对象实现的Iterator接口相关函数来进行foreach。
   */
  private $array = array();
  private $valid = FALSE;

  function __construct($array) {
    $this->array = $array;
  }

  function rewind(){
    /**
     *  reset: 将数组的内部指针指向第一个单元,如果数组为空则返回false;
     *  所以下述语句表示: 数组不为空并且已重置到第一个单元;
     */
    $this->valid = (FALSE !== reset($this->array));
  }

  function current(){
    return current($this->array);
  }

  function key(){
    return key($this->array);
  }

  function next(){
    /**
     * next: 将数组中的内部指针向前移动一位
     * 返回数组内部指针指向的下一个单元的值，或当没有更多单元时返回 FALSE。
     * 所以下述语句表示: 如果还有下一个单元的话,指针移动到下个单元并返回true;
     */
    $this->valid = (FALSE !== next($this->array));
  }

  function valid(){
    return $this->valid;
  }

  #↑↑ 以上5个方法是必须实现的接口方法,也可以再扩展prev和end等方法,后续会介绍一些SPL内置的实现了itertor接口的类,这些类可以拿来直接使用
}

$arr = array(
  'color1' => 'red',
  'color2' => 'blue',
  'color3' => 'green',
  'color4' => 'plack',
  'color5' => 'purple'
);

$colors = new ArrayReloaded($arr);

# 通过foreach来遍历
foreach($colors as $k => $v){
  echo $k.':'.$v.'<br />';
}

# 通过while来遍历
/**
 * 1: foreach的内部实现方式其实也是如此,事实上直接用while来遍历性能更高
 * 2: 在使用迭代器来遍历的时候,一定要记住要rewind和next,而PHP的foreach遍历早已把rewind和next给集成了;
 */
echo '<hr />';
$colors -> rewind();
while($colors -> valid()){
  echo $colors -> key().':'.$colors -> current().'<br />';
  $colors -> next();
}
```

## ArrayIterator 

```
ArrayIterator implements ArrayAccess , SeekableIterator , Countable , Serializable
```
这是一个非常有用的迭代器,里面实现了排序,添加,筛选等foreach不能直接实现的方法(*都是要全部遍历出来再进行判断处理,代码不优雅维护性差*)

**Example**

```
<?php
$arr = array(
  'color1' => 'red',
  'color3' => 'green',
  'color4' => 'plack',
  'color2' => 'blue',
  'color5' => 'purple'
);

// $colors = new ArrayIterator($arr); //可以直接通过实例一个数组迭代器对象,然后while这个迭代器;

//但以下的方式要更易于扩展
//先实例一个array对象
$colorsObj = new ArrayObject($arr);
$it = $colorsObj -> getIterator(); //获得当前的ArrayIterator

# 通过while来遍历
$it -> rewind();
while($it -> valid()){
  echo $it -> key().':'.$it -> current().'<br />';
  $it -> next();
}

#通过iterator迭代器来遍历就变得很灵活
echo $colorsObj -> count(); //元素数量统计

//从第三个开始遍历
$it -> rewind(); //凡是要使用迭代器之前先重置;
if($it -> valid()){
  $it -> seek(2); //从0开始的,第二个位置
  while($it -> valid()){
    echo $it -> key().':'.$it -> current().'<br />';
    $it -> next();
  }
}

//对索引名进行升序排列
$it -> ksort();
foreach($it as $k => $v){
  echo $k .'-->'. $v.'<br />';
}

//对索引值进行排序
$it -> asort();
foreach($it as $k => $v){
  echo $k .'-->'. $v.'<br />';
}

//这些对象方法是否很熟悉? 这就是上一篇文章中说到的 "SPL是一种使object（物体）模仿array（数组）行为的interfaces和classes"
```

## AppendIterator

按顺序迭代访问几个不同的迭代器。例如，希望在一次循环中迭代访问两个或者更多的组合。这个迭代器的append方法类似于array_merge()函数来合并数组。

**Example**

```
$arr1 = array(
  'color1' => 'red',
  'color3' => 'green',
  'color4' => 'plack',
  'color2' => 'blue',
  'color5' => 'purple'
);

$arr2 = array(
  'fruit1' => 'apple',
  'fruit2' => 'orange',
  'fruit3' => 'banana',
  'fruit4' => 'grape',
  'fruit5' => 'strawberry',
);

$ao1 = new ArrayIterator($arr1);
$ao2 = new ArrayIterator($arr2);
$iterator = new AppendIterator();
$iterator -> append($ao1);
$iterator -> append($ao2);
foreach($iterator as $k => $v){
  echo $k.':'.$v,'<br>';
}
```

## MultipleIterator

迭代器的链接器，更多参考连接 http://php.net/manual/en/class.multipleiterator.php

```
$person_id = new ArrayIterator(array('001', '002', '003')); 
$person_name = new ArrayIterator(array('张三', '李四', '王五')); 
$person_age = new ArrayIterator(array(22, 23, 11)); 
$mit = new MultipleIterator(MultipleIterator::MIT_KEYS_ASSOC); 
$mit->attachIterator($person_id, "ID"); 
$mit->attachIterator($person_name, "NAME"); 
$mit->attachIterator($person_age, "AGE"); 
echo"连接的迭代器个数:".$mit->countIterators() . "\n"; //3 
foreach ($mit as $person) { 
    print_r($person); 
} 
/**output
Array
(
    [ID] => 001
    [NAME] => 张三
    [AGE] => 22
)
Array
(
    [ID] => 002
    [NAME] => 李四
    [AGE] => 23
)
Array
(
    [ID] => 003
    [NAME] => 王五
    [AGE] => 11
)
**/ 
```

## LimitIterator

返回给定数量的结果以及从集合中取出结果的起始索引点

```
<?php
//相当于sql中的limit
$fruitArr = array(
  'apple',
  'banana',
  'cherry',
  'damson',
  'elderberry'
);
$fruits = new ArrayIterator($fruitArr);
//从第一个开始取三个
foreach (new LimitIterator($fruits, 0, 3) as $fruit) {
  var_dump($fruit);
}

//从第二个开始取到结束
foreach (new LimitIterator($fruits, 2) as $fruit) {
  print_r($fruit);
}
/**output
string(5) "apple"
string(6) "banana"
string(6) "cherry"
cherrydamsonelderberry
 */
```

## FilterIterator

基于OuterIterator接口，用于过滤数据，返回符合条件的元素。必须实现一个抽象方法accept()，此方法必须为迭代器的当前项返回true或false

```
class UserFilter extends FilterIterator{
  private $userFilter;

  public function  __construct(Iterator $iterator , $filter){
    parent::__construct($iterator);
    //要过滤的参数
    $this->userFilter = $filter;
  }

  public function accept(){
    /*
     * getInnerIterator(): 获得内部的迭代器
     * current(): 然后获取当前的元素
     * in strcmp(string str1,string str2) 区分字符串中字母大小写地比较,返回0就相同
     * int strcasecmp(string str1,string str2) 忽略字符串中字母大小写地比较，返回0就相同
     * 如果accept返回false的话就过滤掉
     */
    $user = $this->getInnerIterator()->current();
    if (strcasecmp($user['name'] , $this->userFilter) == 0) {
      return false;
    }
    return true;
  }
}

$array = array (array ('name' => 'Jonathan' , 'id' => '5') , array ('name' => 'Abdul' , 'id' => '22'),array ('name' => 'zhouzhou' , 'id' => '9'));
$object = new ArrayObject($array);
//去除掉名为abdul的人员
$iterator = new UserFilter($object->getIterator() , 'abdul');
foreach ($iterator as $result) {
  echo $result['name'];
}

/**output
 * Jonathan
 **/
```

## RegexIterator
继承FilterIterator，支持使用正则表达式模式匹配和修改迭代器中的元素。经常用于将字符串匹配。
更多参考： http://cn2.php.net/manual/zh/class.regexiterator.php

```
//可以实现:  preg_match_all(), preg_match(), preg_replace(),preg_split()等函数的功能

$a = new ArrayIterator(array('test1', 'test2', 'test3'));
$i = new RegexIterator($a, '/^(test)(\d+)/', RegexIterator::REPLACE);
$i->replacement = '$2:$1';
print_r(iterator_to_array($i));

/**output
Array
(
[0] => 1:test
[1] => 2:test
[2] => 3:test
)
 **/
```

## IteratorIterator

一种通用类型的迭代器，所有实现了Traversable接口的类都可以被它迭代访问。

## CachingIterator

用来执行提前读取一个元素的迭代操作，例如可以用于确定当前元素是否为最后一个元素。

```
$array = array ('koala' , 'kangaroo' , 'wombat' , 'wallaby' , 'emu' , 'kiwi' , 'kookaburra' , 'platypus');
$object = new CachingIterator(new ArrayIterator($array));
foreach ($object as $value) {
  echo $value;
  if ($object->hasNext()) {
    echo ','; //如果有下一项的话才输出 突出不了该迭代器的作用啊，其他迭代器也可以搞定的
  }
}
/**output
 * koala,kangaroo,wombat,wallaby,emu,kiwi,kookaburra,platypus
 **/
```

## SeekableIterator
用于创建非顺序访问的迭代器，允许跳转到迭代器中的任何一点上。

```
$array = array("apple", "banana", "cherry", "damson", "elderberry");
$iterator = new ArrayIterator($array);
$iterator->seek(3); //起始0从第3个开始取；
echo $iterator->current().'<br />';
/**output
damson
 **/
```

## NoRewindIterator
用于不能多次迭代的集合，适用于在迭代过程中执行一次性操作。

```
$fruit = array('apple', 'banana', 'cranberry');
$arr = new ArrayObject($fruit);
$it = new NoRewindIterator($arr->getIterator());
echo "Fruit A:\n";
foreach ($it as $item) {
  echo $item . "\n";
}

echo "Fruit B:\n"; 

// ↓↓ 由于NoRewindIterator没有rewind方法，所以foreach就不能用rewind重置游标，这个时候$it已经到最后了，所以为空；
foreach ($it as $item) {
  echo $item . "\n";
}

/**output
Fruit A:
apple
banana
cranberry
Fruit B:
 **/
```

## EmptyIterator

一种占位符形式的迭代器，不执行任何操作。当要实现某个抽象类的方法并且这个方法需要返回一个迭代器时，可以使用这种迭代器。

## InfiniteIterator

用于持续地访问数据，当迭代到最后一个元素时，会再次从第一个元素开始迭代访问。

```
$arrayit = new ArrayIterator(array('cat', 'dog'));
$infinite = new InfiniteIterator($arrayit);

//必须限制否则就是死循环
$limit = new LimitIterator($infinite, 0, 7);
foreach ($limit as $value) {
  echo "$value\n";
}
```

## RecursiveArrayIterator
创建一个用于递归形式数组结构的迭代器，类似于多维数组.它为许多更复杂的迭代器提供了所需的操作，如RecursiveTreeIterator和RecursiveIteratorIterator迭代器。   

```
$fruits = array("a" => "lemon", "b" => "orange", array("a" => "apple", "p" => "pear"));
$iterator = new RecursiveArrayIterator($fruits);
while ($iterator->valid()) {
  //检查是否含有子节点
  if ($iterator->hasChildren()) {
    //输出所有字节点
    foreach ($iterator->getChildren() as $key => $value) {
      echo $key . ' : ' . $value . "\n";
    }
  } else {
    echo "No children.\n";
  }
  $iterator->next();
}

/**output
No children.
No children.
a : apple
p : pear
 **/
```

## RecursiveIteratorIterator

将一个树形结构的迭代器展开为一维结构。

```
$fruits = array("a" => "lemon", "b" => "orange", array("a" => "apple", "p" => "pear",'c' => ['a','b']));
$arrayiter = new RecursiveArrayIterator($fruits);
$iteriter = new RecursiveIteratorIterator($arrayiter);
foreach ($iteriter as $key => $value) {
  $d = $iteriter->getDepth();
  echo "depth=$d k=$key v=$value\n";
}
/**output
depth=0 k=a v=lemon
depth=0 k=b v=orange
depth=1 k=a v=apple
depth=1 k=p v=pear
depth=2 k=0 v=a
depth=2 k=1 v=b
 **/
```

## RecursiveTreeIterator

以可视在方式显示一个树形结构。

```
$hey = array("a" => "lemon", "b" => "orange", array("a" => "apple", "p" => "pear"));
$awesome = new RecursiveTreeIterator(
  new RecursiveArrayIterator($hey),
  null, null, RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($awesome as $line)
  echo $line . PHP_EOL;

/**output
|-lemon
|-orange
  |-apple
  \-pear
 **/
```

## ParentIterator

是一个扩展的FilterIterator迭代器，它可以过滤掉来自于RecursiveIterator迭代器的非父元素，只找出子节点的键值。通俗来说，就是去枝留叶。

```php
$hey = array ("a" => "lemon" , "b" => "orange" , array ("a" => "apple" , "p" => "pear"));
$arrayIterator = new RecursiveArrayIterator($hey);
$it = new ParentIterator($arrayIterator);
print_r(iterator_to_array($it));
/**output
 * Array
 * (
 * [0] => Array
 * (
 * [a] => apple
 * [p] => pear
 * )
 * )
 **/
```

## RecursiveFilterIterator

是FilterIterator迭代器的递归形式，也要求实现抽象的accept()方法，但在这个方法中应该使用$this->getInnerIterator()方法访问当前正在迭代的迭代器。

```
class TestsOnlyFilter extends RecursiveFilterIterator{
  public function accept(){
    // 找出含有“叶”的元素
    return $this->hasChildren() || (mb_strpos($this->current() , "叶") !== false);
  }
}

$array = array ("叶1" , array ("力2" , "叶3" , "叶4") , "叶5");
$iterator = new RecursiveArrayIterator($array);
$filter = new TestsOnlyFilter($iterator);
$filter = new RecursiveIteratorIterator($filter);
print_r(iterator_to_array($filter));
/**output
 * Array
 * (
 * [0] => 叶1
 * [1] => 叶3 //只会找出含叶的元素，不会把元素成员全部显示出来
 * [2] => 叶5
 * )
 **/
```

## RecursiveRegexIterator

是RegexIterator迭代器的递归形式，只接受RecursiveIterator迭代器作为迭代对象。

```php
$rArrayIterator = new RecursiveArrayIterator(array ('叶1' , array ('tet3' , '叶4' , '叶5')));
$rRegexIterator = new RecursiveRegexIterator(
  $rArrayIterator , '/^叶/' , RecursiveRegexIterator::ALL_MATCHES
);
foreach ($rRegexIterator as $key1 => $value1) {
  if ($rRegexIterator->hasChildren()) {
    // print all children
    echo "Children: ";
    foreach ($rRegexIterator->getChildren() as $key => $value) {
      echo $value . " ";
    }
    echo "\n";
  } else {
    echo "No children\n";
  }
}
/**output
 * No children
 * Children: 叶4 叶5
 **/
```

## RecursiveCachingIterator

在RecursiveIterator迭代器上执行提前读取一个元素的递归操作。

## CallbackFilterIterator(PHP5.4)

同时执行过滤和回调操作，在找到一个匹配的元素之后会调用回调函数。

```php
$hey = array ("李1" , "叶2" , "叶3" , "叶4" , "叶5" , "叶6" ,);
$arrayIterator = new RecursiveArrayIterator($hey);

$isYe = function($current){
  return mb_strpos($current , '叶') !== false;
};

$rs = new CallbackFilterIterator($arrayIterator , $isYe);
print_r(iterator_to_array($rs));

/**output
 * Array
 * (
 * [0] => 叶2
 * [1] => 叶3
 * [2] => 叶4
 * [3] => 叶5
 * [4] => 叶6
 * )
 **/
```

##  RecursiveCallbackFilterIterator(PHP5.4)

在RecursiveIterator迭代器上进行递归操作，同时执行过滤和回调操作，在找到一个匹配的元素之后会调用回调函数。

```php
function doesntStartWithLetterT($current){
  $rs = $current->getFileName();
  return $rs[0] !== 'T';
}

$rdi = new RecursiveDirectoryIterator(__DIR__);
$files = new RecursiveCallbackFilterIterator($rdi , 'doesntStartWithLetterT');
foreach (new RecursiveIteratorIterator($files) as $file) {
  echo $file->getPathname() . PHP_EOL;
}

```

## DirectoryIterator

目录文件遍历器，提供了查询当前文件的所有信息的方法（是否可读可写，所属，权限等等），具体参考 http://cn2.php.net/manual/zh/class.directoryiterator.php

```php
$it = new DirectoryIterator("../");
foreach ($it as $file) {
  //用isDot ()方法分别过滤掉“.”和“..”目录
  if (!$it->isDot()) {
    echo $file . "\n";
  }
}

```

## RecursiveDirectoryIterator

递归目录文件遍历器，可实现列出所有目录层次结构，而不是只操作一个目录。具体看：http://cn2.php.net/manual/zh/class.recursivedirectoryiterator.php

```php
//列出指定目录中所有文件
$path = realpath('../');
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path) , RecursiveIteratorIterator::SELF_FIRST);
foreach ($objects as $name => $object) {
  echo "$name\n";
}
```

## FilesystemIterator

是DirectoryIterator的遍历器

```php
$it = new FilesystemIterator('../');
foreach ($it as $fileinfo) {
  echo $fileinfo->getFilename() . "\n";
}

```

## GlobIterator

带匹配模式的文件遍历器

```php
$iterator = new GlobIterator('*.php');
if (!$iterator->count()) {
  echo '无php文件';
} else {
  $n = 0;
  printf("总计 %d 个php文件\r\n" , $iterator->count());
  foreach ($iterator as $item) {
    printf("[%d] %s\r\n" , ++ $n , $iterator->key());
  }
}
```

## SimpleXMLIterator

XMl文档访问迭代器，可实现访问xml中所有节点

```php
$xml = <<<XML
<books>
        <book>
            <title>PHP Basics</title>
            <author>Jim Smith</author>
        </book>
        <book>XML basics</book>
</books>
XML;
// SimpleXML转换为数组
function sxiToArray($sxi){
  $a = array ();
  for ($sxi->rewind();$sxi->valid();$sxi->next()) {
    if (!array_key_exists($sxi->key() , $a)) {
      $a[$sxi->key()] = array ();
    }
    if ($sxi->hasChildren()) {
      $a[$sxi->key()][] = sxiToArray($sxi->current());
    } else {
      $a[$sxi->key()][] = strval($sxi->current());
    }
  }
  return $a;
}

$xmlIterator = new SimpleXMLIterator($xml);
$rs = sxiToArray($xmlIterator);
print_r($rs);
/**output
 * Array
 * (
 * [book] => Array
 * (
 * [0] => Array
 * (
 * [title] => Array
 * (
 * [0] => PHP Basics
 * )
 * [author] => Array
 * (
 * [0] => Jim Smith
 * )
 * )
 * [1] => XML basics
 * )
 * )
 **/
```

## 参考链接：

http://www.ruanyifeng.com/blog/2008/07/php_spl_notes.html
http://www.cnblogs.com/ScriptZhang/archive/2010/05/25/1743875.html