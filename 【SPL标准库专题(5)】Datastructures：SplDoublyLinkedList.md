## 简述

双链表是一种重要的线性存储结构，对于双链表中的每个节点，不仅仅存储自己的信息，还要保存前驱和后继节点的地址。

![2516436194.png-28.6kB][1]

## 类摘要

```php
SplDoublyLinkedList  implements Iterator   , ArrayAccess   , Countable   {
 
    public __construct  ( void )
    public void add  ( mixed  $index  , mixed  $newval  )
    //双链表的头部节点
    public mixed top  ( void )
    //双链表的尾部节点
    public mixed bottom  ( void )
    //双联表元素的个数
    public int count  ( void )
    //检测双链表是否为空
    public bool isEmpty  ( void )
 
    //当前节点索引
    public mixed key  ( void )
    //移到上条记录
    public void prev  ( void )
    //移到下条记录
    public void next  ( void )
    //当前记录
    public mixed current  ( void )
    //将指针指向迭代开始处
    public void rewind  ( void )
    //检查双链表是否还有节点
    public bool valid  ( void )
 
    //指定index处节点是否存在
    public bool offsetExists  ( mixed  $index  )
    //获取指定index处节点值
    public mixed offsetGet  ( mixed  $index  )
    //设置指定index处值
    public void offsetSet  ( mixed  $index  , mixed  $newval  )
    //删除指定index处节点
    public void offsetUnset  ( mixed  $index  )
 
    //从双链表的尾部弹出元素
    public mixed pop  ( void )
    //添加元素到双链表的尾部
    public void push  ( mixed  $value  )
 
    //序列化存储
    public string serialize  ( void )
    //反序列化
    public void unserialize  ( string $serialized  )
 
    //设置迭代模式
    public void setIteratorMode  ( int $mode  )
    //获取迭代模式SplDoublyLinkedList::IT_MODE_LIFO  (Stack style) SplDoublyLinkedList::IT_MODE_FIFO  (Queue style)
    public int getIteratorMode  ( void )
 
    //双链表的头部移除元素
    public mixed shift  ( void )
    //双链表的头部添加元素
    public void unshift  ( mixed  $value  )
 
}
```

- 实现了Iterator接口，可以快速实现迭代；
- 实现了 ArrayAccess 接口， 可以如数组般访问链表数据；

```php
$list = new SplDoublyLinkedList();
$list->push('a');
$list->push('b');
$list->push('c');
$list->push('d');

# 方法看看名称就能理解了,主要介绍以下几个地方；
/*
  # 此时的链表结构
  [0] => a
  [1] => b
  [2] => c
  [3] => d
*/

$list->add(1,'z');

// 由于实现了接口ArrayAccess所以可以像操作数组那样操作数据；
echo $list[2];

/*
  # 此时的链表结构
  [0] => a
  [1] => z
  [2] => b
  [3] => c
  [4] => d
*/

//设置一个迭代模式进行迭代↓↓；
$list->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
$iteratorMode = $list->getIteratorMode(); //获取当前的迭代模式

/*
  # 关于模式
  IT_MODE_LIFO: Stack style, 后入先出，堆结构
  IT_MODE_FIFO: Queue style, 先入先出，队列结构(默认)
  IT_MODE_DELETE: Elements are deleted by the iterator 一边迭代，一边删除
  IT_MODE_KEEP: Elements are traversed by the iterator 普通迭代，不删除(默认)
 */

// ↓↓设置是否在迭代的时候删除元素
$list->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);
for ($list->rewind(); $list->valid(); $list->next()) {
  echo $list->current()."\n";
}

for ($list->rewind(); $list->valid(); $list->next()) {
  echo $list->current()."\n";
}
```

其他的方法手册看看名称都能理解，就不说明了：
http://php.net/manual/zh/class.spldoublylinkedlist.php


  [1]: http://static.zybuluo.com/a5635268/1s8vqnhjrafpqqhxxlkzvoi5/2516436194.png