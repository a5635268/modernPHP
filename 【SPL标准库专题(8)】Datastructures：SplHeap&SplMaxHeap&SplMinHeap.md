堆(Heap)就是为了实现优先队列而设计的一种数据结构，它是通过构造二叉堆(二叉树的一种)实现。根节点最大的堆叫做最大堆或大根堆，根节点最小的堆叫做最小堆或小根堆。二叉堆还常用于排序(堆排序)。

![1352265671_1438.jpg-27.3kB][1]

## 类摘要

```php
abstract SplHeap implements Iterator , Countable {
  /* 方法 */
public __construct ( void )
abstract protected int compare ( mixed $value1 , mixed $value2 )
public int count ( void )
public mixed current ( void )
public mixed extract ( void )
public void insert ( mixed $value )
public bool isEmpty ( void )
public mixed key ( void )
public void next ( void )
public void recoverFromCorruption ( void )
public void rewind ( void )
public mixed top ( void )
public bool valid ( void )
}
```

从上面可以看到由于类中包含一个`compare`的抽象方法，所以该类必须为抽象类（不可实例化，只能被继承使用）；

**最小堆和最大堆其实就是对`compare`该抽象方法的一个算法的两种呈现；** 也可以自己写一个类继承SplHeap按自己的方式来做排序；

## Example

### 自定义排序堆

```php
class MySimpleHeap extends SplHeap
{
  //compare()方法用来比较两个元素的大小，决定他们在堆中的位置
  public function  compare( $value1, $value2 ) {
    return ($value2 - $value1);
  }
}
$obj = new MySimpleHeap();

$obj->insert( 4 );
$obj->insert( 8 );
$obj->insert( 1 );
$obj->insert( 0 );

echo $obj->top();  //8

foreach( $obj as $number ) {
  echo $number;
  echo PHP_EOL;
}
```

### 最大堆与最小堆

```php
$heap = new SplMaxHeap();
$heap->insert(100);
$heap->insert(80);
$heap->insert(88);
$heap->insert(70);
$heap->insert(810);
$heap->insert(800);

//最大堆，从大到小排序
$heap->rewind();
while($heap->valid()){
  echo $heap->key(),'=>',$heap->current(),PHP_EOL;
  $heap->next();
}
```

  [1]: http://static.zybuluo.com/a5635268/oupyds2yiv6mdhw8nmikvdty/1352265671_1438.jpg