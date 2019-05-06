1. 以static::来调用，是在运行的那个时刻才执行绑定操作；
2. 父类中有个方法是延迟绑定的,在子类::调用这个方法的时候它**又回到调用的子类开始向上找**;


**example1：**

```php
class Human {
    public static function whoami() {
        echo '来自父类的whoami在执行';
    }
    public static function say() {
        self::whoami(); // 子类内没有say方法,找到了父类这里
        // 在这里的self 指的是 父类
    }
    public static function say2() {
        static::whoami();    //  子类也没有say2方法,又找到父类这里
        // 但是父类用static::whoami,
        // 指调用你子类自己的whoami方法
    }
}

class Stu extends Human{
    public static function whoami () {
        echo '来自子类的whoami在执行';
    }
}

// 来自父类的whoami在执行
Stu::say();//调用Stu类的say方法,但Stu类没有say方法,就向其父类寻找,找到父类以后,发现父类的say方法里面又调用self::whoami();此时self里面其实是有两个whoami的方法,但由于本次调用发生的环境是在父类的say方法里面,所以它调用的是父类的whoami方法,不调用子类的whoami方法;

echo PHP_EOL;

// 来自子类的whoami在执行
Stu::say2(); //调用Stu类的say2方法,但Stu类没有say2方法,就向其父类寻找,找到父类say2以后,发现父类的say2方法里面用了static延迟绑定了whoami方法,而此时发生调用的子类里面有whoami方法(如果没有就向父类寻找),所以在此时是绑定在子类的whoami上,所以这里调用的是子类的whoami方法;
```

**example2：**


```php
class Animal { 
    const age = 1; 
    public static $leg = 4; 
    public static function cry() { 
        echo '呜呜<br />'; 
    } 
    public static function t1() { 
        self::cry(); 
        echo self::age,'<br />'; 
        echo self::$leg,'<br />'; 
    } 
    public static function t2() { 
        static::cry(); 
        echo static::age,'<br />'; 
        echo static::$leg,'<br />'; 
    } 
} 
class Human extends Animal { 
    public static $leg = 2; 
    public static function cry() { 
        echo '哇哇<br />'; 
    } 
} 
class Stu extends Human { 
    const age = 16; 
    public static function cry() { 
        echo '嘤嘤<br />'; 
    } 
} 
Stu::t1(); //呜呜,1,4 
/*
↑↑:一直找到Animal类,Animal类的t1方法是普通绑定,所以是呜呜,1,4
*/

Stu::t2(); // 嘤嘤,16,2 
/*
↑↑:一直找到Animal类,Animal类的t2方法是延迟绑定,又回到Stu类开始找,Stu类有cry方法,所以是嘤嘤,有age属性所以是16,没有leg属性,然后向上找,一直找到既可,所以是2
*/
```

## new static()与new self()


1. self - 就是这个类，是代码段里面的这个类。new self就是实例化本类;
2. static - PHP 5.3加进来的只得是当前这个类，有点像$this的意思，从堆内存中提取出来，访问的是当前实例化的那个类，那么 static 代表的就是那个类。


~~~php
<?php
class A {
static public function get_self() {
        return new self();
    }

static public function get_static() {
        return new static();
    }
}

class B extends A { }

echo get_class(A::get_self());//A
echo get_class(A::get_static()); // A

echo get_class(B::get_self()); // A: 实例化B::get_self()对象里面的self()所在哪个类,就返回哪个类。
echo get_class(B::get_static()); // B: 访问的是当前类,有点像this; 因为其是B继承了的get_static方法，而调用的。
~~~


>[info]  由此可见，他们的区别只有在继承中才能体现出来，如果没有任何继承，那么这两者是没有区别的。但如果是在静态方法内new本类的话，最好还是用new static();