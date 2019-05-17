这两个类都是继承自`SplDoublyLinkedList`，分别派生自`SplDoublyLinkedList`的堆栈模式和队列模式；所以放在一起来介绍；

## 堆栈SplStack 

![3846779171.jpg-31.4kB][1]

```php
# 类摘要
SplStack extends SplDoublyLinkedList implements Iterator , ArrayAccess , Countable {
  /* 方法 */
  __construct(void)
  
  // 重写了父类SplDoublyLinkedList，固定为堆栈模式，然后此处只需要传IT_MODE_DELETE或者IT_MODE_KEEP。
  void setIteratorMode(int $mode )
 
  /* 继承自SplDoublyLinkedList的方法 */
  ...
 }
```

```php
//把栈想象成一个颠倒的数组
$stack = new SplStack();
/**
 * 可见栈和双链表的区别就是IteratorMode改变了而已，栈的IteratorMode只能为：
 * （1）SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_KEEP  （默认值,迭代后数据保存）
 * （2）SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_DELETE （迭代后数据删除）
 */
$stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_DELETE);
$stack->push('a');
$stack->push('b');
$stack->push('c');
$stack->offsetSet(0, 'first');//index 为0的是最后一个元素,后入后出
$stack->pop(); //出栈
foreach($stack as $item) {
  echo $item . PHP_EOL; // first a
}
print_R($stack); //测试IteratorMode
```

## 队列SplQueue

![804515552.png-10.1kB][2]

```php
 # 类摘要
 SplQueue extends SplDoublyLinkedList implements Iterator , ArrayAccess , Countable {
    /* 方法 */
    __construct ( void )
    
    // 出队
    mixed dequeue ( void )
    
    // 入队
    void enqueue ( mixed $value )
    
    // 重写了父类SplDoublyLinkedList，固定为堆栈模式，然后此处只需要传IT_MODE_DELETE或者IT_MODE_KEEP。
    void setIteratorMode ( int $mode )
    
    //其他继承的方法
 }
```


```php
$q = new SplQueue();

$q->setIteratorMode(SplQueue::IT_MODE_DELETE);

//可以放任何数据类型到队列里面
$q->enqueue('item1');
//每次放入都是只占一个队列的位置
$q->enqueue(array("FooBar", "foo"));
$q->enqueue(new stdClass());


$q->rewind();
while($q->valid()){
  print_r($q->current());
  echo "\n";
  $q->next();
}

// 出队，先入先出,因为队列为空，所以此处报错;
$q->dequeue();
```


  [1]: http://static.zybuluo.com/a5635268/eu2xtc2vowfjztlqkvkjlh48/3846779171.jpg
  [2]: http://static.zybuluo.com/a5635268/f3l026zxxn2u55u546ewu238/804515552.png