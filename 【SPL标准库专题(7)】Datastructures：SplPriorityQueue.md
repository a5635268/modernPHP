普通的队列是一种先进先出的数据结构，元素在队列尾追加，而从队列头取出。在优先队列中，元素被赋予优先级。当访问元素时，具有最高优先级的元素最先取出。优先队列具有最高级先出 （largest-in，first-out）的行为特征。
总结下来就是普通队列有**先进先出**原则，优先级队列有**优先级高先出**原则，这个优先级可以设置；


## 类摘要

```php
// 1. 没有实现ArrayAccess接口，所以不能像数组那样操作；
SplPriorityQueue implements Iterator , Countable {
  /* 方法 */
  public __construct ( void )

  // 比较方法，内部应该用到了冒泡排序，对于权重值来说，返回0代表相等，返回正整数就代表大于，返回负整数就代表小于;
  // 默认是权重值越优先，也可以让其被子类覆盖改为权重值越小越优先
  public int compare ( mixed $priority1 , mixed $priority2 )
  public mixed extract ( void )

  //恢复到上一个被破坏的节点？ 测试无用；
  public void recoverFromCorruption ( void )
  public void setExtractFlags ( int $flags )
  public void insert ( mixed $value , mixed $priority )

  public int count ( void )
  public mixed current ( void )
  public bool isEmpty ( void )
  public mixed key ( void )
  public void next ( void )
  public void rewind ( void )
  public mixed top ( void )
  public bool valid ( void )
}
```

## Example

```php
class PQtest extends SplPriorityQueue
{
  //覆盖父类，更改其优先规则为权重值越小越优先；
  public function compare($priority1, $priority2)
  {
    if ($priority1 === $priority2) return 0;
    return $priority1 > $priority2 ? -1 : 1;
  }
}

$pq = new PQtest();

// 设置值与优先值
$pq->insert('a', 10);
$pq->insert('b', 1);
$pq->insert('c', 8);

/**
 * 设置元素出队模式
 * SplPriorityQueue::EXTR_DATA 仅提取值
 * SplPriorityQueue::EXTR_PRIORITY 仅提取优先级
 * SplPriorityQueue::EXTR_BOTH 提取数组包含值和优先级
 */
$pq->setExtractFlags(PQtest::EXTR_BOTH);

//从顶部取出一个节点，该节点下面的节点移上为顶部节点;
print_r(
  $pq->extract()
);
/*
  [data] => b
  [priority] => 1
 */

$pq->recoverFromCorruption();

//拿出顶部节点
print_r(
  $pq->extract()
);

/*
  [data] => c
  [priority] => 8
 */

// 还原自上一个节点？ 没什么效果？
$pq->recoverFromCorruption();

print_r(
  $pq->current()
);

$pq->rewind();
while($pq->valid()){
  print_r($pq->current());
  echo PHP_EOL;
  $pq -> next();
}
```