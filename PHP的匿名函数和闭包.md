## 匿名函数

```php
// Example1
$func = function( $param ) {
    echo $param;
};
$func( 'some string' );//输出：some string

// Example2
function callFunc( $func ) {
    $func( 'some string' );
}
$printStrFunc = function( $str ) {
    echo $str;
};
callFunc( $printStrFunc );

//可以直接将匿名函数进行传递。如果你了解js，这种写法可能会很熟悉
callFunc( function( $str ) {
    echo $str;
} );
```

## 闭包

PHP在默认情况下，匿名函数内不能调用所在代码块的上下文变量，而需要通过使用use关键字。

```php
//1. 通过闭包获取当前函数环境外的变量值副本。
function getMoney() {
    $rmb = 1;
    $dollar = 6;
    $func = function() use ( $rmb ) {
        echo $rmb; //1
        echo $dollar; //报错，找不到dorllar变量
    };
    $func();
}
getMoney();

//2. 之所以称为副本，是因为通过闭包传值到匿名函数内的变量,值也是不能改变。
function getMoney() {
    $rmb = 1;
    $func = function() use ( $rmb ) {
        $rmb += 2;
        echo $rmb; // 3
    };
    $func();
    echo $rmb; // 还是1没有改变;
}
getMoney();

//3. 如果要改变外部变量的值，还是得通过传址的方法
function getMoney() {
    $rmb = 1;
    $func = function() use ( &$rmb ) {
        $rmb += 2;
        echo $rmb; // 3
    };
    $func();
    echo $rmb; // 3;
}
getMoney();

//4. 
function getMoneyFunc() {
    $rmb = 1;
    $func = function() use(&$rmb){
        echo $rmb;
        //把$rmb的值加1
        $rmb++;
    };
    return $func; // 如果将匿名函数返回给外界，匿名函数会保存use所引用的变量，而外界则不能得到这些变量，这样形成‘闭包’
}

$getMoney = getMoneyFunc();
$getMoney(); // 1
$getMoney(); // 2
$getMoney(); // 3
```

### 闭包的好处

**1. 减少循环**

```php
// 一个基本的购物车，包括一些已经添加的商品和每种商品的数量。
// 其中有一个方法用来计算购物车中所有商品的总价格。该方法使用了一个closure作为回调函数。
class Cart{
    const PRICE_BUTTER = 1.00;
    const PRICE_MILK   = 3.00;
    const PRICE_EGGS   = 6.95;

    protected $products = array();

    public function add($product , $quantity)
    {
        $this->products[$product] = $quantity;
    }

    public function getQuantity($product)
    {
        return isset($this->products[$product]) ? $this->products[$product] : false;
    }

    public function getTotal($tax)
    {
        $total = 0.00;
        // 使用闭包减少循环;
        $callback = function($quantity , $product) use ($tax , &$total){
            $pricePerItem = constant(__CLASS__ . "::PRICE_" . strtoupper($product));
            $total += ($pricePerItem * $quantity) * ($tax + 1.0);
        };
        array_walk($this->products , $callback);
        return round($total , 2);;
    }
}

$my_cart = new Cart;
// 往购物车里添加条目
$my_cart->add('butter' , 1);
$my_cart->add('milk' , 3);
$my_cart->add('eggs' , 6);
// 打出出总价格，其中有 5% 的销售税.
print $my_cart->getTotal(0.05) . "\n";
// The result is 54.29
```


**2. 减少函数的参数**

```php
function html($code , $id = "" , $class = "")
{
    if ($id !== "")
        $id = " id = \"{$id}\"";
    $class = ($class !== "") ? " class =\"$class\"" : "";
    $open = "<$code$id$class>";
    $close = "</$code>";
    return function($inner = "") use ($open , $close){
        return "$open$inner$close";
    };
}
$tag = html('div','','class');

// 可读性和可维护性大大提高;
echo $tag('div1,div1,div1');
echo PHP_EOL;
echo $tag('div2,div2,div2');

```

**3. 解除递归函数**

```php
// ↓↓ 注意,这里的fib一定要用引用哦,因为第一次的时候就会Notice: Undefined variable,然后后面的fib()就会错误;
$fib = function($n) use (&$fib){
    if ($n == 0 || $n == 1)
        return 1;
    return $fib($n - 1) + $fib($n - 2);
};
echo $fib(2) . "\n"; // 2
$lie = $fib;
$fib = function(){
    die('error');
};//rewrite $fib variable

echo $lie(5); // error  达到递归解除;
```

**4. 关于延迟绑定**

```php
$result = 0;
$one = function(){
    var_dump($result);
};
$two = function() use ($result){
    var_dump($result);
};

// 在回调生成的时候进行赋值;
$four = function() use ($result){
    //0 回调生成的时候赋值，也就是$result = 0;
    var_dump($result);
};

// 如果使用引用,就能使use里面的变量完成延迟绑定,也就是在调用的时候再赋值;
$three = function() use (&$result){
    //1 在调用的时候再赋值进去，也就是1，注意对象类型也属于引用；
    var_dump($result);
};

$result += 1;

$one();    // outputs NULL: $result is not in scope
$two();    // outputs int(0): $result was copied
$three();    // outputs int(1)
$four(); // outputs int(0)
```

## 几个配合回调或闭包的函数

`bool array_walk ( array &$array , callable $funcname [, mixed $userdata = NULL ] )`

```php
/**
 * @param array $array
 * @param callable $funcname ()
 * @param mixed|NULL $userdata
 * @return bool
 * bool array_walk ( array &$array , callable $funcname [, mixed $userdata = NULL ] )
 */
$fruits = array(
    "d" => "lemon" ,
    "a" => "orange" ,
    "b" => "banana" ,
    "c" => "apple"
);
$test_print = function(&$item2 , $key, $prefix){
    $item2 .= ' 10';
    echo "{$prefix} : $key => $item2\n";
};
/*
    this result : d => lemon
    this result : a => orange
    this result : b => banana
    this result : c => apple
 */
array_walk($fruits , $test_print, 'this result');

print_r($fruits);
/*
    Array
    (
        [d] => lemon 10
        [a] => orange 10
        [b] => banana 10
        [c] => apple 10
    )
 */
```

---

`bool array_walk_recursive ( array &$input , callable $funcname [, mixed $userdata = NULL ]`

```php
$sweet = array(
    'a' => 'apple' ,
    'b' => 'banana'
);
$fruits = array(
    'sweet' => $sweet ,
    'sour' => 'lemon'
);
$test_print = function($item , $key)
{
    echo "$key holds $item\n";
};

array_walk_recursive($fruits , $test_print);
/*
 * 自动跳过sweet,因为sweet是数组;任何其值为 array 的键都不会被传递到回调函数中去
    a holds apple
    b holds banana
    sour holds lemon
 */
```

---

`array array_filter ( array $array [, callable $callback [, int $flag = 0 ]] )`

```php
$odd = function($var){
    return ($var & 1);
};
$even = function($var){
    return (!($var & 1));
};
$array1 = array( "a" => 1 , "b" => 2 , "c" => 3 , "d" => 4 , "e" => 5 );
$array2 = array( 6 , 7 , 8 , 9 , 10 , 11 , 12 );
echo "Odd :\n";
print_r(array_filter($array1 , "odd"));
/*
Odd :
Array
(
    [a] => 1
    [c] => 3
    [e] => 5
)
 */
echo "Even:\n";
print_r(array_filter($array2 , "even"));
/*
Even:
Array
(
    [0] => 6
    [2] => 8
    [4] => 10
    [6] => 12
)
 */
 
 # 如果不传第二参数的的话
 $entry = array(
    0 => 'foo',
    1 => false,
    2 => -1,
    3 => null,
    4 => ''
);
print_r(array_filter($entry));
/*
 * 当前值为false的话就filter;
Array
(
    [0] => foo
    [2] => -1
)
 */
```

---

`array array_map ( callable $callback , array $arr1 [, array $array ] )`

```php
/**
 * @param callable $callback
 * @param array $arr1
 * @param array $array
 */

$func = function($value) {
    return $value * 2;
};

print_r(array_map($func, range(1, 5)));

/*
Array
(
    [0] => 2
    [1] => 4
    [2] => 6
    [3] => 8
    [4] => 10
)
 */
 
 $show_Spanish = function($n , $m){
    return ("The number $n is called $m in Spanish");
};
$a = array( 1 , 2 , 3 , 4 , 5 );
$b = array( "uno" , "dos" , "tres" , "cuatro" , "cinco" );
$c = array_map($show_Spanish , $a , $b);
/**
print_r($c);
Array
(
[0] => The number 1 is called uno in Spanish
[1] => The number 2 is called dos in Spanish
[2] => The number 3 is called tres in Spanish
[3] => The number 4 is called cuatro in Spanish
[4] => The number 5 is called cinco in Spanish
)
 */

$map_Spanish = function($n , $m){
    return (array($n => $m));
};
$d = array_map($map_Spanish , $a , $b);
print_r($d);
/**
    Array (
    [0] => Array ( [1] => uno )
    [1] => Array ( [2] => dos )
    [2] => Array ( [3] => tres )
    [3] => Array ( [4] => cuatro )
    [4] => Array ( [5] => cinco )
    )
 */
```

---

`mixed array_reduce ( array $input , callable $function [, mixed $initial = NULL ] )`

```php

/**
 * 用回调函数迭代地将数组简化为单一的结果值,解释不清楚的一看代码就明白了;
 * @param array $input
 * @param callable $function
 * @param mixed|NULL $initial 如果指定了可选参数 initial，该参数将被当成是数组中的第一个值来处理，或者如果数组为空的话就作为最终返回值。
 */

$rsum = function($result , $value){
    // $result 初始值为NULL, 如果有第三参数的话,第三参数为初始值;
    $result += $value;
    return $result;
};

$rmul = function($result , $value){
    $result *= $value;
    return $result;
};

$a = array(1, 2, 3, 4, 5);
$x = array();
$b = array_reduce($a, $rsum); // (NULL)0+1+2+3+4+5 = 15;
$c = array_reduce($a, $rmul, 10); // 10*1*2*3*4*5 = 1200;
$d = array_reduce($x, $rsum, "No data to reduce"); // No data to reduce
```

---

`mixed preg_replace_callback ( mixed $pattern , callable $callback , mixed $subject [, int $limit = -1 [, int &$count ]] )`

```php
/**
 * @param mixed $pattern 正则模式;
 * @param callable $callback
 * @param mixed $subject
 * @param int $limit 对于每个模式用于每个 subject 字符串的最大可替换次数。 默认是-1（无限制）。
 * @param int $count 如果指定，这个变量将被填充为替换执行的次数。
 * mixed preg_replace_callback ( mixed $pattern , callable $callback , mixed $subject [, int $limit = -1 [, int &$count ]] )
 */
// 将文本中的年份增加一年.
$text = "April fools day is 04/01/2002\n";
$text .= "Last christmas was 12/24/2001\n";

// 回调函数
$next_year = function($matches){
    // 通常: $matches[0]是完成的匹配
    // $matches[1]是第一个捕获子组的匹配
    // 以此类推
    return $matches[1] . ($matches[2] + 1);
};

echo preg_replace_callback("|(\d{2}/\d{2}/)(\d{4})|" , $next_year , $text);
```

---

`mixed call_user_func ( callable $callback [, mixed $parameter [, mixed $... ]] )`

`mixed call_user_func_array ( callable $callback , array $param_arr )`

```php
/**
 * @param callable $callback 第一个参数为需要调用的函数; 如果是数组array($classname, $methodname)
 * @param mixed $parameter 第二个参数开始就是队列进该函数的参数;
 * @param mixed $parameter2
 * @param mixed $parameter3
 * ..
 * @return 返回值：返回调用函数的结果，或FALSE
 */
$eat = function($fruit , $num){ //参数可以为多个
    echo "You want to eat $fruit $num pcs, no problem\n";
};
call_user_func($eat , "apple" , 10); //print: You want to eat apple 10 pcs, no problem;
call_user_func($eat , "orange" , 5); //print: You want to eat orange 5 pcs,no problem;

// 调用类方法
class myclass {
    public static function say_hello($name,$message)
    {
        echo "Hello! $name $message";
    }
}

//array(类名，静态方法名),参数
call_user_func(array('myclass', 'say_hello'), 'dain_sun', 'good person');

call_user_func_array(array('myclass', 'say_hello'), array('dain_sun', 'good person'));

// Hello! dain_sun good person
```



