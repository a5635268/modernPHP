# PHP魔术方法小结

标签（空格分隔）： PHP

---

## 说明

魔术方法就是在特定场景下不需要调用而自动执行的方法。因为有魔术方法，所以我们的类可以写得很灵活~

```
__construct       #构造方法,在类被实例化时自动调用,一般用于初始化操作;

__destruct        #析构方法,对象被销毁时自动调用;

__clone()         #克隆方法,当对象被克隆时,将会自动调用

__get($key)       #属性获取方法,当对象要调用一个被保护或不存在的属性时,__get方法就会自动被调用,并传入调用的属性名称;

__set($key,$val)  # 属性设置方法,当对象设置一个被保护或不存在的属性时,__set会被执行,并传入要设置的属性名称和属性值,注意这里的设置令对象本身没有发生改变,除非更改操作是发生在__set方法内;但无论如何,都不可以为对象增加本来就没有的属性,只有对已有的属性进行操作;

__isset($key)     # 当用isset判断一个对象是否有这个属性,并且这个属性是被保护或者不存在时被自动执行,并传入判断的属性名称;

__unset($key)     # 同上,当用unset删除一个对象的保护属性或未存在的属性时,自动被执行;

__isset($key) #__isset方法如果返回一个为true的值时,isset的判断就会失效,不管本类有没有其判断的属性,isset都会返回真;

__call($method,$arguments)    # 当对象在调用一个被保护或不存在的方法时,会自动执行,并传入两个参数$method为方法吗,$arguments为该方法的参数数组;

__callStatic($method,$arguments)    # 当调用了类当中某个被保护或不存在的静态方法时,会自动执行,并传入两个参数$method为方法,$arguments为该方法的参数数组;注意,是静态方法,并且是php5.3新增的魔术方法;

__toString() # 输出对象引用时自动调用;

__invoke() # $obj = class();$obj()时执行该函数

__sleep() # 在类序列化时调用

__wakeup() # 在类反序列化时调用
```

```php
<?php
#魔术方法概览:

class magic{
  private $privateProperty = "被保护的属性";

  public function __construct(){
    echo "我是魔术构造方法,本类被实例化的时候我就会自动执行" . "<hr />";
  }
  
  public function __toString(){
    echo 'hahaha,我是toString';
    return $this -> privateProperty;
  }
  
  public function __invoke(){
    echo '类被实例为对象后,可以直接当做方法调用,调用的就是我~';
  }

  public function func(){
    echo "这是一个普通的方法 <br />";
  }

  private function privateFunc(){
    echo "这是一个被保护的方法 <br />";
  }

  public function __destruct(){
    echo "我是析构方法,在对象被销毁(代码执行完、\$obj被赋值为NULL或被unset)时,我会被自动调用 <br />";
    echo "<strong>貌似对象被克隆的时候,我也会自动执行</strong><hr />";
  }

  public function __clone(){
    echo "我是克隆魔术方法,当对象被clone时,我会被自动执行. <hr />";
  }

  public function __get($key){
    echo "我是__get方法,当对象调用一个被保护或不存在的属性时,我会被执行,并传入调用的属性名称 -> {$key} <hr />";
  }

  public function __set($key , $val){
    echo "我是__set方法,当对象设置一个被保护或不存在的属性时,我会被执行,并传入要设置的属性名称->\"{$key}\"和属性值->\"{$val}\";<br /><strong>注意,这里的设置并没有令对象本身的属性值发生改变或增加</strong> <hr />";
    /*
      __set方法的使用:
      1.利用本方法预留一个接口,有条件的限制类外部操作类内部被保护的属性;
      2.框架中一般private一个$data的数组,用set方法对其增加数组元素,然后来操作这个数组;		
    */
  }

  public function __isset($key){
    echo "我是__isset方法,当isset判断对象的被保护或不存在属性时,我会被执行,并传入被判断的属性名称 ->\"{$key}\" <br />
		  <storng>注意,本方法如果返回一个为true的值时,isset的判断就会失效,不管本类有没有其判断的属性 ->\"{$key}\",都会返回真</storng>
		<hr />";
    return true;
  }

  public function __unset($key){
    echo "我是__unset方法,当unset对象的被保护或不存在属性时,我会被执行,并传入要被unset的属性名称 ->\"{$key}\" <hr />";
  }

  public function __call($method , $arguments){
    echo "我是__call方法,当对象在调用一个被保护或不存在的方法时,我会被调用,并传入两个参数,\$method ----> \"{$method}\"(方法名); \$arguments  ---->(参数数组)";
    print_r($arguments);
    echo "<hr />";
  }

  public static function __callStatic($method , $arguments){
    echo "我是__callStatic方法,当调用了类当中某个被保护或不存在的静态方法时,我会被调用,并传入两个参数,\$method ----> \"{$method}\"(方法名); \$arguments  ---->(参数数组)";
    print_r($arguments);
    echo "<strong>注意:只能是类::staticMethod,并且本方法是php5.3版本才更新的</strong>";
    echo "<hr />";
  }
}

$obj = new magic();

# __invoke

$obj(); //5.3以后这样搞就等于是直接调用类里面的__invoke()方法

# __toString
//如果没有__toString就会报错;Object of class magic could not be converted to string
//但是有__toString方法的话,不仅toString方法会被调用,并且还会返回toString中返回的值;
echo $obj;

# __clone;
//  $obj1 = clone $obj;

#__get($key)获取保护和不存在的属性↓↓;
$obj->privateProperty; //调用被保护的属性;
$obj->zxg; //调用不存在的属性;

#__set($key,$val)设置保护和不存在的属性↓↓:
print_r($obj);
echo " <hr />";
$obj->privateProperty = "通过__set方法改变了属性值;";
$obj->zxg = "xgg"; //未存在的属性;
print_r($obj);//$obj本身没有发生改变,除非更改操作是发生在__set方法内;但无论如何,都不可以为对象增加属性,只有对已有的属性进行操作;
echo " <hr />";

#__isset($key)方法的使用
echo isset($obj->jyh) ? "有jyh属性(事实上是没有这个属性的,但__isset方法返回真,其就为真)" : "没有jyh属性";
echo " <hr />";

#__unset($key)方法的使用
unset($obj->zxg);

#__call($method,$arguments)
$obj->privateFunc('arg1' , 'arg2' , 'arg3');

#__callStatic($method,$arguments)
magic::zhouzhou(27 , 'pig');
?>
```




