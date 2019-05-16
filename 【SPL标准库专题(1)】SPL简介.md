## 什么是SPL

SPL是Standard PHP Library（PHP标准库）的缩写。

根据官方定义，它是"a collection of interfaces and classes that are meant to solve standard problems" **SPL是用于解决典型问题(standard problems)的一组接口与类的集合。** 但是，目前在使用中，SPL更多地被看作是一种使object（物体）模仿array（数组）行为的interfaces和classes。SPL的核心概念就是Iterator。

在我的理解中,SPL以及后面要说的设计模式专题都是用于同一个目的: **构建优雅简洁易于扩展和维护的代码**,有时候我们看上去写了更多的代码,但事实上却让代码的扩展性和维护性变得更强。

> 另外本专题属于PHP进阶课程。在本专题中给出的一些Example，看上去是有更简单的替代方案，但在实际上在更复杂的开发中，看似更多的代码却使得程序的可插拔性，可维护性变得更强，SPL以及设计模式都算是面向对象中的精髓了，所以面向对象的基础一定要掌握得非常牢才更能理解；


## Iterator

迭代器有时又称光标（cursor）是程式设计的软件设计模式，可在容器物件（container，例如list或vector）上遍访的接口，设计人员无需关心容器物件的内容。
PHP5开始支持了接口， 并且内置了Iterator接口， 所以如果你定义了一个类，并实现了Iterator接口，那么你的这个类对象就是ZEND_ITER_OBJECT,否则就是ZEND_ITER_PLAIN_OBJECT.对于ZEND_ITER_PLAIN_OBJECT的类，foreach会通过HASH_OF获取该对象的默认属性数组，然后对该数组进行foreach。
而对于ZEND_ITER_OBJECT的类对象，则会通过调用对象实现的Iterator接口相关函数来进行foreach。
通俗地说，Iterator能够使许多不同的数据结构，都能有统一的操作界面，比如一个数据库的结果集、同一个目录中的文件集、或者一个文本中每一行构成的集合。

如果按照普通情况，遍历一个MySQL的结果集，程序需要这样写：

```
// Fetch the "aggregate structure"
$result = mysql_query("SELECT * FROM users");

// Iterate over the structure
while ( $row = mysql_fetch_array($result) ) {
   // do stuff with the row here
}
```

读出一个目录中的内容，需要这样写：

```
// Fetch the "aggregate structure"
$dh = opendir('/home/harryf/files');

// Iterate over the structure
while ( $file = readdir($dh) ) {
   // do stuff with the file here
}
```

读出一个文本文件的内容，需要这样写：

```
// Fetch the "aggregate structure"
$fh = fopen("/home/hfuecks/files/results.txt", "r");

// Iterate over the structure
while (!feof($fh)) {

   $line = fgets($fh);
   // do stuff with the line here

}
```

上面三段代码，虽然处理的是不同的resource（资源），但是功能都是遍历结果集（loop over contents），因此Iterator的基本思想，就是将这三种不同的操作统一起来，用同样的命令界面，处理不同的资源。

SPL提供了6个迭代器接口(具体的会在本专题后续的文章里面说明)：

|      名称   | 功能    |
| :--------   | :-----  |
| Traversable  |  遍历接口（检测一个类是否可以使用 foreach 进行遍历的接口） |
| Iterator  |  迭代器接口（可在内部迭代自己的外部迭代器或类的接口） |
| IteratorAggregate |  聚合式迭代器接口（创建外部迭代器的接口） |
| OuterIterator|  迭代器嵌套接口（将一个或多个迭代器包裹在另一个迭代器中） |
| RecursiveIterator | 递归迭代访问接口（提供递归访问功能） |
| SeekableIterator  | 可索引迭代访问接口（实现查找功能） |

## Classes

SPL除了定义一系列Interfaces以外，还提供一系列的内置类，它们对应不同的任务，大大简化了编程。
查看所有的内置类，可以使用下面的代码：

```
// a simple foreach() to traverse the SPL class names
foreach(spl_classes() as $key=>$value){
  echo $key.' -&gt; '.$value.'<br />';
}
```

## Datastructures

同时 SPL 还提供了些数据结构基本类型的实现 。虽然我们可以使用传统的变量类型来描述数据结构，例如用数组来描述堆栈（Strack）-- 然后使用对应的方式 pop 和 push（arraypop()、arraypush()），但你得时刻小心，·因为毕竟它们不是专门用于描述数据结构的 -- 一次误操作就有可能破坏该堆栈。

而 SPL 的 SplStack 对象则严格以堆栈的形式描述数据，并提供对应的方法。同时，这样的代码应该也能理解它在操作堆栈而非某个数组，从而能让你的同伴更好的理解相应的代码，并且它更快。

SPL拥有的以下数据结构：
Doubly Linked Lists（双向链表），Heaps（堆），Arrays（阵列），Map（映射）

## Function

同时SPL还提供了很多方便的函数,比如我们在框架中经常用到的`spl_autoload_register`(注册给定的函数作为 __autoload 的实现)

- class_implements — 返回指定的类实现的所有接口。
- class_parents — 返回指定类的父类。
- class_uses — Return the traits used by the given class
- iterator_apply — 为迭代器中每个元素调用一个用户自定义函数
- iterator_count — 计算迭代器中元素的个数
- iterator_to_array — 将迭代器中的元素拷贝到数组
- spl_autoload_call — 尝试调用所有已注册的__autoload()函数来装载请求类
- spl_autoload_extensions — 注册并返回spl_autoload函数使用的默认文件扩展名。
- spl_autoload_functions — 返回所有已注册的__autoload()函数。
- spl_autoload_register — 注册给定的函数作为 __autoload 的实现
- spl_autoload_unregister — 注销已注册的__autoload()函数
- spl_autoload — __autoload()函数的默认实现
- spl_classes — 返回所有可用的SPL类
- spl_object_hash — 返回指定对象的hash id


