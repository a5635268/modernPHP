# PHP 生成器语法

标签（空格分隔）： PHP

---

一般你在迭代一组数据的时候，需要创建一个数据，假设数组很大，则会消耗很大性能，甚至造成内存不足。

```php
//Fatal error: Allowed memory size of 1073741824 bytes exhausted (tried to allocate 32 bytes) 
range(1, 100000000);
```

PHP5.5实现了生成器，每当产生一个数组元素则用`yield`关键词返回，并且执行函数暂停，当执行函数next方法时，则会从上一次被yield的位置开始继续执行，如下例子，只会产生中间变量$i，并只在每次循环的赋值；


```php
function xrange($start, $limit, $step = 1) {
    for ($i = $start; $i <= $limit; $i += $step) {
     //注意变量$i的值在不同的yield之间是保持传递的。
        yield $i;
    }
}

$generator = xrange(1, 100000000, 1);

// 可以通过foreach获得;
foreach ($generator as $number) {
   echo  "$number";
   echo PHP_EOL;
}

// 由于生成器其实是一个实现了iterator接口的类，所以也可以通过相关的iterator方法来迭代
// var_dump($generator)  class Generator#1 (0) {}
// Generator implements Iterator {}

$generator->rewind();
while ($generator->valid()){
    echo $generator->current();
    echo PHP_EOL;
    $generator->next();
}
```

生成器函数的核心是yield关键字。它最简单的调用形式看起来像一个return申明，不同之处在于普通return会返回值并终止函数的执行，而yield会返回一个值给循环调用此生成器的代码并且只是暂停执行生成器函数。

## 通过生成器来生成关联数组

```php
/*
 * 下面每一行是用分号分割的字段组合，第一个字段将被用作键名。
 */
$input = <<<'EOF'
1;PHP;Likes dollar signs
2;Python;Likes whitespace
3;Ruby;Likes blocks
EOF;

function input_parser($input) {
    foreach (explode("\n", $input) as $line) {
        $fields = explode(';', $line);
        $id = array_shift($fields);
        yield $id => $fields;
    }
}

foreach (input_parser($input) as $id => $fields) {
    echo "$id:\n";
    echo "    $fields[0]\n";
    echo "    $fields[1]\n";
}
/*
    1:
        PHP
        Likes dollar signs
    2:
        Python
        Likes whitespace
    3:
        Ruby
        Likes blocks
 */
```

## 生成NULL值

略; 

## 使用引用来生成值 

先了解一下从函数返回一个引用的概念

手册解释: 引用返回用在当想用函数找到引用应该被绑定在哪一个变量上面时。不要用返回引用来增加性能，引擎足够聪明来自己进行优化。仅在有合理的技术原因时才返回引用！要返回引用，使用此语法：

**Example: 使用返回引用**

```php
class foo{
    public $value = 42;

    public function &getValue()
    {
        return $this->value;
    }
}

$obj = new foo;
// $myValue is a reference to $obj->value, which is 42.
// $myValue 是 $obj->value 的引用，它们的值都是 42
$myValue = &$obj->getValue();
// 对 $obj->value 重新赋值，会影响到 $myValue 的值
$obj->value = 2;
// prints the new value of $obj->value, i.e. 2.
echo $myValue;    // 程序输出 2
```

**Example: 没有使用返回引用**

```php
class foo {
    public $value = 42;

    public function getValue() {
        return $this->value;
    }
}

$obj = new foo;
$myValue = $obj->getValue();
$obj->value = 2;

echo $obj->value;  // 输出 2
echo $myValue; 	//  输出42, 因为返回的是当时值的一个副本;
```

函数 &getValue() 把引用绑定在成员变量 $value 上了。正常来说，$obj = new foo; 产生的 $obj 是一个copy，它的成员变量 $value 与函数 getValue() 不存在“别名”（引用）关系。

**Example: 通过引用来生成值**

```php
function &gen_reference() {
    $value = 3;
    while ($value > 0) {
        yield $value;
    }
}

/*
 * 我们可以在循环中修改$number的值，而生成器是使用的引用值来生成，所以gen_reference()内部的$value值也会跟着变化。
 */
foreach (gen_reference() as &$number) {
    echo (--$number).'...';//改变的是gen_reference()里面的$value值;这样里面的循环就不是死循环了。
}
```

##  yield from关键字

在PHP7的版本，生成器允许从其他生成器，可迭代对象或数组通过`yield from`关键字来生成对应的值;


```php
function count_to_ten() {
    yield 1;
    yield 2;
    yield from [3, 4];
    yield from new ArrayIterator([5, 6]);
    yield from seven_eight();
    yield 9;
    yield 10;
}

function seven_eight() {
    yield 7;
    yield from eight();
}

function eight() {
    yield 8;
}

foreach (count_to_ten() as $num) {
    echo "$num ";
}

// 输出1 2 3 4 5 6 7 8 9 10 
```